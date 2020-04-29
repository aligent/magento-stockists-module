<?php
/**
 * @category    Aligent
 * @package     Aligent_Stockists
 * @copyright   Copyright (c) 2016 Aligent Consulting. (http://www.aligent.com.au)
 *
 * @author      Phirun Son <phirun@aligent.com.au>
 */
namespace Aligent\Stockists\Helper;

use Magento\Framework\DataObject;
use Magento\Framework\HTTP\ZendClient;

class Geocode extends BaseHelper
{
    const GEOCODE_URL = 'stockists/geocode/url';
    const GEOCODE_OUTPUT = 'stockists/geocode/output';
    const GEOCODE_KEY = 'stockists/geocode/key';

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    ) {
        $this->httpClientFactory = $httpClientFactory;
        parent::__construct($context, $storeManager);
    }

    /**
     * @param $request
     * @return bool|DataObject Will return a DataObject with parameter 'location' => 'lat', 'lng' if a location is found
     * in the request. If unable, will return false
     */
    public function getRequestLocation($request)
    {
        //Explicit Latitude and Longitude passed with query
        $latitude = $request->getParam('latitude');
        $longitude = $request->getParam('longitude');

        if ($latitude === null || $longitude === null) {
            //Use address and components for geolocation
            $address = $request->getParam('address');
            $components = $request->getParam('components');

            if (empty($address) && empty($components)) {
                return false;
            }

            return $this->geocode($address, $components);
        } else {
            //'lat' & 'lng' as that is the same as the google result variable names
            return new DataObject([
                'location' => [
                    'lat' => $latitude,
                    'lng' => $longitude
                ]
            ]);
        }
    }

    /**
     * Finds a location based on address and components
     *
     * @param string $address
     * @param array $components
     * @return bool|object @see https://developers.google.com/maps/documentation/geocoding/intro#Results false is returned
     * if any errors occur
     */
    public function geocode($address = '', $components = [])
    {
        $url = $this->getGeocodeUrl();
        $output = $this->getGeocodeOutput();

        if (empty($url) || empty($output)) {
            $this->_logger->warning('Invalid geocoding settings.');
            return false;
        }

        if (is_array($components)) {
            $componentValues = [];
            foreach ($components as $key => $value) {
                $componentValues[] = "$key:$value";
            }
            $components = implode('|', $componentValues);
        }

        $data = [
            'address' => $address,
            'components' => $components
        ];

        $apiKey = $this->getGeocodeKey();
        if (!empty($apiKey)) {
            $data['key'] = $apiKey;
        }

        $fullUrl = $url . (substr($url, -1) !== '/' ? '/' : '') . $output . '?';
        $response = $this->sendRequest($fullUrl, $data);

        if ($response->status !== "OK") {
            $this->_logger->warning('Unable to contact geocode API');
            return false;
        }

        $allResults = $response->results;
        if (count($allResults) < 1) {
            $this->_logger->warning('Unable to find location via geocode API');
            return false;
        }
        $result = $this->getLocation($allResults[0]);

        return $result;
    }

    /**
     * @param $url
     * @param $data
     * @return mixed
     * @throws \Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function sendRequest($url, $data)
    {
        /** @var ZendClient $client */
        $client = $this->httpClientFactory->create();

        $client->setUri($url);
        $client->setConfig(['timeout' => 30]);
        $client->setMethod(\Zend_Http_Client::GET);
        $client->setParameterGet($data);
        $client->setUrlEncodeBody(false);

        try {
            $response = $client->request();
            $responseBody = $response->getBody();
        } catch (\Exception $e) {
            new DataObject([
                'response_code' => -1,
                'response_reason_code' => $e->getCode(),
                'response_reason_text' => $e->getMessage()
            ]);
            throw $e;
        }

        return json_decode($responseBody);
    }

    /**
     * @param $result
     * @return null|object @see https://developers.google.com/maps/documentation/geocoding/intro#Results
     */
    protected function getLocation($result)
    {
        if (property_exists($result, "geometry")) {
            $dataObject = new DataObject(json_decode(json_encode($result->geometry), true));
            $dataObject->addData($this->normalizeAddressComponents($result));
            return $dataObject;
        }
        return null;
    }

    /**
     * @param $result StdClass
     * @return object
     */
    protected function normalizeAddressComponents($result){
        $address = [];
        if(property_exists($result, 'address_components')){
            foreach($result->address_components as $comp){
                foreach($comp->types as $compType){
                    if(!isset($address[$compType])) $address[$compType] = [];
                    $address[$compType][] = array('long_name' => $comp->long_name, 'short_name' => $comp->short_name);
                }
            }
        }
        return json_decode(json_encode(['address_components' => $address]), true);
    }

    public function getGeocodeUrl()
    {
        return $this->getStoreScopeConfigValue(self::GEOCODE_URL);
    }

    public function getGeocodeOutput()
    {
        return $this->getStoreScopeConfigValue(self::GEOCODE_OUTPUT);
    }

    public function getGeocodeKey()
    {
        return $this->getStoreScopeConfigValue(self::GEOCODE_KEY);
    }
}

<?php
/**
 * @category    Aligent
 * @package     Aligent_Stockists
 * @copyright   Copyright (c) 20120 Aligent Consulting. (http://www.aligent.com.au)
 *
 * @author      Torbjorn van Heeswijck <torbjorn.vanheeswijck@aligent.com.au>
 */
namespace Aligent\Stockists\Service;

use Aligent\Stockists\Api\AdapterInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeocodeStockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GeocodeStockist implements GeocodeStockistInterface
{
    const XML_PATH_GEOCODE_KEY = 'stockists/geocode/key';

    protected $_adapter;
    protected $_stockistRepository;
    protected $_scopeConfig;

    /**
     * @param AdapterInterface $adapter
     * @param StockistRepositoryInterface $stockistRepository
     */
    public function __construct(
        AdapterInterface $adapter,
        StockistRepositoryInterface $stockistRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_adapter = $adapter;
        $this->_stockistRepository = $stockistRepository;
        $this->_scopeConfig = $scopeConfig;
    }


    public function execute(StockistInterface $stockist)
    {
        $key = $this->getGeocodeKey();
        $request = $this->_adapter->buildRequest($stockist, $key);
        $response = $this->_adapter->performGeocode($request);
        $result = $this->_adapter->handleResponse($response);
        $stockist->setLat($result->lat);
        $stockist->setLong($result->long);
        $this->_stockistRepository->save($stockist);
    }

    protected function getGeocodeKey()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_GEOCODE_KEY, ScopeInterface::SCOPE_STORE);
    }
}

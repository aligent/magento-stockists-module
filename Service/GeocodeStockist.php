<?php

/**
 * @category    Aligent
 * @package     Aligent_Stockists
 * @copyright   Copyright (c) 2020 Aligent Consulting. (http://www.aligent.com.au)
 *
 * @author      Torbjorn van Heeswijck <torbjorn.vanheeswijck@aligent.com.au>
 */

namespace Aligent\Stockists\Service;

use Aligent\Stockists\Api\AdapterInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeocodeStockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class GeocodeStockist implements GeocodeStockistInterface
{
    const XML_PATH_GEOCODE_KEY = 'stockists/geocode/key';

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param AdapterInterface $adapter
     * @param StockistRepositoryInterface $stockistRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        AdapterInterface $adapter,
        StockistRepositoryInterface $stockistRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->adapter = $adapter;
        $this->stockistRepository = $stockistRepository;
        $this->scopeConfig = $scopeConfig;
    }


    public function execute(StockistInterface $stockist, $forceGeocode = false)
    {
        if ($forceGeocode || $this->adapter->addressHasChangedFor($stockist)) {
            $key = $this->getGeocodeKey();

            // Fail early if no API key is set
            if (!isset($key) || trim($key) === '') {
                return;
            }

            $request = $this->adapter->buildRequest($stockist, $key);
            $response = $this->adapter->performGeocode($request);
            $result = $this->adapter->handleResponse($response);

            if ($result->wasSuccessful()) {
                $stockist->setLat($result->getLat());
                $stockist->setLng($result->getLng());
            }
        }
    }

    protected function getGeocodeKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GEOCODE_KEY, ScopeInterface::SCOPE_STORE);
    }
}

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

    private $adapter;
    private $stockistRepository;
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


    public function execute(StockistInterface $stockist)
    {
        $key = $this->getGeocodeKey();
        $request = $this->adapter->buildRequest($stockist, $key);
        $response = $this->adapter->performGeocode($request);
        $result = $this->adapter->handleResponse($response);
        $stockist->setLat($result["lat"]);
        $stockist->setLong($result["long"]);
    }

    protected function getGeocodeKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GEOCODE_KEY, ScopeInterface::SCOPE_STORE);
    }
}

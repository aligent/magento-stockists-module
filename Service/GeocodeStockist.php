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

/*
 * DEV REQUIREMENTS:
    Take an \Aligent\Stockist\Api\Data\StockistInterface instance as a parameter

    Build a geocoding API request out of the address data on the stockist model

    Perform the request; this should be performed by a DI-overrideable Adapter to give flexibility for other providers

    Save the resulting lat/lng data against the model and save it via the StockistRepository
*/

class GeocodeStockist implements GeocodeStockistInterface
{
    protected $_adapter;
    protected $_stockistRepository;

    /**
     * @param AdapterInterface $adapter
     * @param StockistRepositoryInterface $stockistRepository
     */
    public function __construct(
        AdapterInterface $adapter,
        StockistRepositoryInterface $stockistRepository
    ) {
        $this->_adapter = $adapter;
        $this->_stockistRepository = $stockistRepository;
    }


    public function execute(StockistInterface $stockist)
    {
        // TODO: Implement execute() method.

        /*
            $request = Adapter::buildRequest

            $response = Adapter::performGeocode($request)

            Set relevant values from $response onto $stockist (setLat / setLng)

            StockistRepository::save($stockist)*/
    }
}

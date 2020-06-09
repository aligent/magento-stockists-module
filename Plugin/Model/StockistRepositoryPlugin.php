<?php

namespace Aligent\Stockists\Plugin\Model;

use Aligent\Stockists\Service\GeocodeStockist;

class StockistRepositoryPlugin
{
    /**
     * @var GeocodeStockist
     */
    private $geocodeStockistService;

    /**
     * StockistRepositoryPlugin constructor.
     * @param $geocodeStockistService
     */
    public function __construct(
        $geocodeStockistService
    )
    {
        $this->geocodeStockistService = $geocodeStockistService;
    }

    public function beforeSave(\Aligent\Stockists\Api\StockistRepositoryInterface $subject, \Aligent\Stockists\Api\Data\StockistInterface $stockist)
    {
        $this->geocodeStockistService->execute($stockist);
        return [$stockist];
    }
}
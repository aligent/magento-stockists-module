<?php

namespace Aligent\Stockists\Plugin\Model;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Service\GeocodeStockist;

class StockistRepositoryPlugin
{
    /**
     * @var GeocodeStockist
     */
    private $geocodeStockistService;

    /**
     * StockistRepositoryPlugin constructor.
     * @param GeocodeStockist $geocodeStockistService
     */
    public function __construct(
        GeocodeStockist $geocodeStockistService
    ) {
        $this->geocodeStockistService = $geocodeStockistService;
    }

    public function beforeSave(StockistRepositoryInterface $subject, StockistInterface $stockist): array
    {
        $this->geocodeStockistService->execute($stockist);
        return [$stockist];
    }
}

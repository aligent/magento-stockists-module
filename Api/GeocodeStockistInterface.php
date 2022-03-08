<?php


namespace Aligent\Stockists\Api;

interface GeocodeStockistInterface
{
    /**
     * @param Data\StockistInterface $stockist
     */
    public function execute(Data\StockistInterface $stockist): void;
}

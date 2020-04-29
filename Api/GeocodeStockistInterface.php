<?php


namespace Aligent\Stockists\Api;


interface GeocodeStockistInterface
{
    public function execute(Data\StockistInterface $stockist);
}
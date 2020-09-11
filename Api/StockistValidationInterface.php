<?php

namespace Aligent\Stockists\Api;

use Aligent\Stockists\Api\Data\StockistInterface;

interface StockistValidationInterface
{
    public function validate(StockistInterface $stockist) : bool;
}
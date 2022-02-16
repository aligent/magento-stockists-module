<?php

namespace Aligent\Stockists\Api;

use Aligent\Stockists\Api\Data\StockistInterface;

interface StockistValidationInterface
{
    /**
     * @param StockistInterface $stockist
     * @return array
     */
    public function validate(StockistInterface $stockist) : array;
}

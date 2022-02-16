<?php

namespace Aligent\Stockists\Api;

use Aligent\Stockists\Api\Data\StockistInterface;

interface StockistValidationInterface
{
    /**
     * @param StockistInterface $stockist
     * @return bool
     */
    public function validate(StockistInterface $stockist) : bool;
}

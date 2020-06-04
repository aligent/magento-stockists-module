<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistValidationInterface;
use Magento\Framework\Model\AbstractModel;

class StockistValidation implements StockistValidationInterface
{
    public function validate(StockistInterface $stockist) : bool
    {
        if (!$stockist->getId())
        {
            return false;
        }

        if (!is_subclass_of($stockist,AbstractModel::class))
        {
            return false;
        }

        return true;
    }
}
<?php

namespace Aligent\Stockists\Model\ResourceModel\Stockist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\Stockist as StockistModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'stockist_id';

    protected function _construct()
    {
        $this->_init(StockistModel::class, StockistResource::class);
    }
}
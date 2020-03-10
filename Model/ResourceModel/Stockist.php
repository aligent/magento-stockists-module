<?php

namespace Aligent\Stockists\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Stockist extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('stockist', 'stockist_id');
    }
}
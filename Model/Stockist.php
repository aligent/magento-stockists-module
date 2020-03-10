<?php

namespace Aligent\Stockists\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Api\Data\StockistInterface;

/**
 * @method StockistResource getResource()
 * @method ResourceModel\Stockist\Collection getCollection()
 */
class Stockist extends AbstractModel implements StockistInterface, IdentityInterface
{
    const CACHE_TAG = 'aligent_stockist';
    protected $_cacheTag = 'aligent_stockist';
    protected $_eventPrefix = 'aligent_stockist';

    protected function _construct()
    {
        $this->_init(StockistResource::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
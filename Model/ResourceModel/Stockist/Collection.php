<?php

namespace Aligent\Stockists\Model\ResourceModel\Stockist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\Stockist as StockistModel;
use Aligent\Stockists\Helper\Data as StockistHelper;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'stockist_id';
    /**
     * @var JsonSerializer
     */
    private $json;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        JsonSerializer $json,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->json = $json;
    }

    protected function _construct()
    {
        $this->_init(StockistModel::class, StockistResource::class);
    }

    /**
     * Replaces getSize to also consider any geo-filtering that was applied to the search
     * @param GeoSearchCriteriaInterface $searchCriteria
     * @return int
     */
    public function getSizeFromGeoSearch(GeoSearchCriteriaInterface $searchCriteria) : int
    {
        if ($this->_totalRecords === null) {

            $countSelect = $this->getSelectCountSql();

            $radius = $searchCriteria->getSearchRadius();
            $searchOrigin = $searchCriteria->getSearchOrigin();
            $lat = $searchOrigin['lat'] ?? null;
            $lng = $searchOrigin['lng'] ?? null;

            $countSelect->reset('having');
            $countSelect->where(new \Zend_Db_Expr(
                '(' . StockistHelper::EARTH_MEAN_RADIUS_KM . ' * ACOS('
                . 'COS(RADIANS(' . (float)$lat . ')) * '
                . 'COS(RADIANS(`lat`)) * '
                . 'COS(RADIANS(`lng`) - RADIANS(' . (float)$lng . ')) + '
                . 'SIN(RADIANS(' . (float)$lat . ')) * '
                . 'SIN(RADIANS(`lat`))'
                . '))'
                . ' <= ' . (float)$radius
            ));

            $this->_totalRecords = $this->getConnection()->fetchOne($countSelect);
        }

        return (int)$this->_totalRecords;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        /** @var \Aligent\Stockists\Model\Stockist $item */
        foreach ($this->_items as &$item) {
            if ($item->getData('hours')) {
                $item->setData('hours', $this->json->unserialize($item->getData('hours')));
            }
        }

        return $this;
    }
}
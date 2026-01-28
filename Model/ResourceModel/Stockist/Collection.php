<?php

namespace Aligent\Stockists\Model\ResourceModel\Stockist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\Stockist as StockistModel;
use Aligent\Stockists\Helper\Data as StockistHelper;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'stockist_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(StockistModel::class, StockistResource::class);
    }

    /**
     * Replaces getSize to also consider any geo-filtering that was applied to the search
     *
     * @param GeoSearchCriteriaInterface $searchCriteria
     * @return int
     */
    public function getSizeFromGeoSearch(GeoSearchCriteriaInterface $searchCriteria) : int
    {
        if ($this->_totalRecords === null) {
            $searchOrigin = $searchCriteria->getSearchOrigin();
            $lat = $searchOrigin['lat'] ?? null;
            $lng = $searchOrigin['lng'] ?? null;
            $radius = $searchCriteria->getSearchRadius();

            // If no geo search criteria is set, use standard getSize()
            if ($lat === null || $lng === null || $radius === null) {
                $this->_totalRecords = $this->getSize();
                return (int)$this->_totalRecords;
            }

            $countSelect = $this->getSelectCountSql();
            $countSelect->reset('having');
            $countSelect->where(new \Zend_Db_Expr(
                '(' . StockistHelper::EARTH_MEAN_RADIUS_KM . ' * ACOS('
                . 'COS(RADIANS(' . (float)$lat . ')) * '
                . 'COS(RADIANS(`lat`)) * '
                . 'COS(RADIANS(`lng`) - RADIANS(' . (float)$lng . ')) + '
                . 'SIN(RADIANS(' . (float)$lat . ')) * '
                . 'SIN(RADIANS(`lat`))'
                . '))'
                . ' <= ' . $radius
            ));

            $this->_totalRecords = $this->getConnection()->fetchOne($countSelect);
        }

        return (int)$this->_totalRecords;
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad(): Collection
    {
        parent::_afterLoad();

        /** @var StockistModel $item */
        foreach ($this->_items as $item) {
            $this->getResource()->afterLoad($item);
        }

        return $this;
    }
}

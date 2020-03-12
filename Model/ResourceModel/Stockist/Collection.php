<?php

namespace Aligent\Stockists\Model\ResourceModel\Stockist;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\Stockist as StockistModel;
use Aligent\Stockists\Model\SearchCriteria\DistanceProcessor;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'stockist_id';

    protected function _construct()
    {
        $this->_init(StockistModel::class, StockistResource::class);
    }

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
                '(' . DistanceProcessor::EARTH_MEAN_RADIUS_KM . ' * ACOS('
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
}
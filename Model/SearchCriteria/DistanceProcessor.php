<?php

namespace Aligent\Stockists\Model\SearchCriteria;

use Magento\Framework\Data\Collection\AbstractDb;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Aligent\Stockists\Helper\Data as StockistHelper;

class DistanceProcessor
{
    /**
     * @param GeoSearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     * @return AbstractDb
     */
    public function process(GeoSearchCriteriaInterface $searchCriteria, AbstractDb $collection) : AbstractDb
    {
        $latLng = $searchCriteria->getSearchOrigin();
        $radius = $searchCriteria->getSearchRadius();

        if ($latLng && array_key_exists('lat', $latLng) && array_key_exists('lng', $latLng)) {
            $collection = $this->addDistanceFilter($collection, $latLng['lat'], $latLng['lng'], $radius);
        }

        return $collection;
    }

    /**
     * @param AbstractDb $collection
     * @param float $lat
     * @param float $lng
     * @param float $radius
     * @return AbstractDb
     */
    private function addDistanceFilter(AbstractDb $collection, float $lat, float $lng, float $radius) : AbstractDb
    {
        $collection->getSelect()->columns(['distance' => new \Zend_Db_Expr(
            '(' . StockistHelper::EARTH_MEAN_RADIUS_KM . ' * ACOS('
            . 'COS(RADIANS(' . $lat . ')) * '
            . 'COS(RADIANS(`lat`)) * '
            . 'COS(RADIANS(`lng`) - RADIANS(' . $lng . ')) + '
            . 'SIN(RADIANS(' . $lat . ')) * '
            . 'SIN(RADIANS(`lat`))'
            . '))'
        )]);

        $collection->getSelect()->having('distance <= ?', $radius);
        return $collection;
    }
}

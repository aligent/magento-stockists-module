<?php

namespace Aligent\Stockists\Model\SearchCriteria;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;

class DistanceProcessor
{
    const EARTH_MEAN_RADIUS_KM = 6372.797;

    public function process(GeoSearchCriteriaInterface $searchCriteria, AbstractDb $collection) : AbstractDb {
        $latLng = $searchCriteria->getSearchOrigin();
        $radius = $searchCriteria->getSearchRadius();

        // TODO: Normalize radius (km vs mi) before passing to addDistanceFilter?
        if ($latLng && array_key_exists('lat', $latLng) && array_key_exists('lng', $latLng) && is_numeric($radius)) {
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
    private function addDistanceFilter($collection, $lat, $lng, $radius) : AbstractDb
    {
        $collection->getSelect()->columns(['distance' => new \Zend_Db_Expr(
            '(' . self::EARTH_MEAN_RADIUS_KM . ' * ACOS('
            . 'COS(RADIANS(' . (float)$lat . ')) * '
            . 'COS(RADIANS(`lat`)) * '
            . 'COS(RADIANS(`lng`) - RADIANS(' . (float)$lng . ')) + '
            . 'SIN(RADIANS(' . (float)$lat . ')) * '
            . 'SIN(RADIANS(`lat`))'
            . '))'
        )]);

        $collection->getSelect()->having('distance <= ?', $radius);
        return $collection;
    }
}
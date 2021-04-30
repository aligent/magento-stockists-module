<?php

namespace Aligent\Stockists\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GeoSearchCriteriaInterface extends SearchCriteriaInterface
{
    /**
     * @return array
     */
    public function getSearchOrigin() : array;

    /**
     * @return float
     */
    public function getSearchRadius() : float;

    /**
     * @param array $origin Array having 'lat' and 'lng' keys
     * @return GeoSearchCriteriaInterface
     */
    public function setSearchOrigin(array $origin) : GeoSearchCriteriaInterface;

    /**
     * @param float $radius - in kilometers
     * @return GeoSearchCriteriaInterface
     */
    public function setSearchRadius(float $radius) : GeoSearchCriteriaInterface;
}

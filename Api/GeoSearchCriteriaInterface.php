<?php

namespace Aligent\Stockists\Api;

interface GeoSearchCriteriaInterface extends \Magento\Framework\Api\SearchCriteriaInterface
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
     * @param float $radius In KMs
     * @return GeoSearchCriteriaInterface
     */
    public function setSearchRadius(float $radius) : GeoSearchCriteriaInterface;
}
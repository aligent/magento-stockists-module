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
     */
    public function setSearchOrigin(array $origin) : void;

    /**
     * @param float $radius - in kilometers
     */
    public function setSearchRadius(float $radius) : void;
}

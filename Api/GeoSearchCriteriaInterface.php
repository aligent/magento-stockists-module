<?php

namespace Aligent\Stockists\Api;

interface GeoSearchCriteriaInterface extends \Magento\Framework\Api\SearchCriteriaInterface
{
    public function getSearchOrigin() : array;

    public function getSearchRadius() : float;

    public function setSearchOrigin(array $origin) : GeoSearchCriteriaInterface;

    public function setSearchRadius(float $radius) : GeoSearchCriteriaInterface;
}
<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use \Magento\Framework\Api\Search\SearchCriteria;

class GeoSearchCriteria extends SearchCriteria implements GeoSearchCriteriaInterface
{
    public function getSearchOrigin(): array
    {
        return $this->_get('origin');
    }

    public function getSearchRadius(): float
    {
        return $this->_get('radius');
    }

    public function setSearchOrigin(array $origin): GeoSearchCriteriaInterface
    {
        $this->_data['origin'] = $origin;
        return $this;
    }

    public function setSearchRadius(float $radius): GeoSearchCriteriaInterface
    {
        $this->_data['radius'] = $radius;
        return $this;
    }
}
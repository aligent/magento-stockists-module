<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchCriteria;

class GeoSearchCriteria extends SearchCriteria implements GeoSearchCriteriaInterface
{
    public function getSearchOrigin(): array
    {
        return $this->_get('origin');
    }

    public function getSearchRadius(): float
    {
        return (float)$this->_get('radius');
    }

    public function setSearchOrigin(array $origin): void
    {
        $this->_data['origin'] = $origin;
    }

    public function setSearchRadius(float $radius): void
    {
        $this->_data['radius'] = $radius;
    }
}

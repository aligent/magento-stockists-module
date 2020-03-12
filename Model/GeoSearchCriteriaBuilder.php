<?php

namespace Aligent\Stockists\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;

class GeoSearchCriteriaBuilder extends SearchCriteriaBuilder
{
    public function create()
    {
        /** @var \Aligent\Stockists\Api\GeoSearchCriteriaInterface $searchCriteria */
        $searchCriteria = parent::create();
        $searchCriteria->setSearchOrigin($this->data['origin'] ?? null);
        $searchCriteria->setSearchRadius($this->data['radius'] ?? null);
        $this->data = [];
        return $searchCriteria;
    }

    public function setSearchOrigin(array $origin)
    {
        $this->data['origin'] = $origin;
        return $this;
    }

    public function setSearchRadius(float $radius)
    {
        $this->data['radius'] = $radius;
        return $this;
    }
}
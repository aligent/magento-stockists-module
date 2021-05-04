<?php

namespace Aligent\Stockists\Model;

use Magento\Framework\Api\Search\SearchCriteriaBuilder;

class GeoSearchCriteriaBuilder extends SearchCriteriaBuilder
{
    /**
     * @return \Aligent\Stockists\Api\GeoSearchCriteriaInterface
     */
    public function create()
    {
        // Hold on to these values since the parent create will clear the data array.
        $searchOrigin = $this->data['origin'] ?? [];
        $searchRadius = $this->data['radius'] ?? 0;

        /** @var \Aligent\Stockists\Api\GeoSearchCriteriaInterface $searchCriteria */
        $searchCriteria = parent::create();

        $searchCriteria->setSearchOrigin($searchOrigin);
        $searchCriteria->setSearchRadius($searchRadius);

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

<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;

class GeoSearchCriteriaBuilder extends SearchCriteriaBuilder
{
    /**
     * @return GeoSearchCriteriaInterface
     */
    public function create(): GeoSearchCriteriaInterface
    {
        // Hold on to these values since the parent create will clear the data array.
        $searchOrigin = $this->data['origin'] ?? [];
        $searchRadius = $this->data['radius'] ?? 0;

        /** @var GeoSearchCriteriaInterface $searchCriteria */
        $searchCriteria = parent::create();

        $searchCriteria->setSearchOrigin($searchOrigin);
        $searchCriteria->setSearchRadius($searchRadius);

        return $searchCriteria;
    }

    /**
     * @param array $origin
     * @return void
     */
    public function setSearchOrigin(array $origin): void
    {
        $this->data['origin'] = $origin;
    }

    /**
     * @param float $radius
     * @return void
     */
    public function setSearchRadius(float $radius): void
    {
        $this->data['radius'] = $radius;
    }
}

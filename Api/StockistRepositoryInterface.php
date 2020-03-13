<?php

namespace Aligent\Stockists\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;

interface StockistRepositoryInterface
{
    /**
     * @param $id
     * @return StockistInterface
     */
    public function get($id): StockistInterface;

    /**
     * @param \Aligent\Stockists\Api\GeoSearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(GeoSearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param StockistInterface $formSubmission
     * @return StockistInterface
     */
    public function save(StockistInterface $formSubmission): StockistInterface;
}
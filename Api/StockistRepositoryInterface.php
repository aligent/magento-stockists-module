<?php

namespace Aligent\Stockists\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;

interface StockistRepositoryInterface
{
    public function get($id): StockistInterface;

    public function getList(GeoSearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    public function save(StockistInterface $formSubmission): StockistInterface;
}
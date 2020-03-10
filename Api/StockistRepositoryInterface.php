<?php

namespace Aligent\Stockists\Api;

use Aligent\Stockists\Api\Data\StockistInterface;
use \Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface StockistRepositoryInterface
{
    public function get($id): StockistInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    public function save(StockistInterface $formSubmission): StockistInterface;
}
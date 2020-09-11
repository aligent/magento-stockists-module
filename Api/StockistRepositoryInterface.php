<?php

namespace Aligent\Stockists\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;

interface StockistRepositoryInterface
{
    /**
     * @param string $identifier
     * @return StockistInterface
     */
    public function get(string $identifier): StockistInterface;

    /**
     * @param int $id
     * @return StockistInterface
     */
    public function getById(int $id): StockistInterface;

    /**
     * @param \Aligent\Stockists\Api\GeoSearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(GeoSearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param StockistInterface $stockist
     * @return StockistInterface
     */
    public function save(StockistInterface $stockist): StockistInterface;

    /**
     * @param StockistInterface $stockist
     * @return bool
     */
    public function delete(StockistInterface $stockist): bool;

    /**
     * @param string $identifier
     * @return bool
     */
    public function deleteByIdentifier(string $identifier): bool;

    /**
     * @param string $stockistId
     * @return bool
     */
    public function deleteById(string $stockistId): bool;
}

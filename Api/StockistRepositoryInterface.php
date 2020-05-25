<?php

namespace Aligent\Stockists\Api;

use Magento\Framework\Api\SearchResultsInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;

interface StockistRepositoryInterface
{
    /**
     * @param string $identifier
     * @return \Aligent\Stockists\Api\Data\StockistInterface
     */
    public function get(string $identifier): StockistInterface;

    /**
     * @param int $id
     * @return \Aligent\Stockists\Api\Data\StockistInterface
     */
    public function getById(int $id): StockistInterface;

    /**
     * @param \Aligent\Stockists\Api\GeoSearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(GeoSearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param \Aligent\Stockists\Api\Data\StockistInterface $stockist
     * @return \Aligent\Stockists\Api\Data\StockistInterface
     */
    public function save(StockistInterface $stockist): StockistInterface;

    /**
     * @param \Aligent\Stockists\Api\Data\StockistInterface $stockist
     * @return bool
     */
    public function delete(StockistInterface $stockist): bool;

    /**
     * @param string $identifier
     * @return bool
     */
    public function deleteByIdentifier(string $identifier): bool;
}

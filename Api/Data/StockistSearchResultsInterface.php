<?php

namespace Aligent\Stockists\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface StockistSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return StockistInterface[]
     */
    public function getItems(): array;

    /**
     * @param StockistInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}

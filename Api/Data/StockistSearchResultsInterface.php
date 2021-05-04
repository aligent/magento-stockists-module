<?php

namespace Aligent\Stockists\Api\Data;

use \Magento\Framework\Api\SearchResultsInterface;

interface StockistSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Aligent\Stockists\Api\Data\StockistInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Aligent\Stockists\Api\Data\StockistInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}

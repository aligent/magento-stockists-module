<?php

namespace Aligent\Stockists\Api\Data;

interface StockistDataProcessorInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function execute(array $data): array;
}

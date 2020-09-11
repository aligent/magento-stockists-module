<?php

namespace Aligent\Stockists\Api\Data;

interface StockistDataProcessorInterface
{
    public function execute(array $data): array;
}
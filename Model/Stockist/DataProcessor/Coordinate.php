<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist\DataProcessor;

use Aligent\Stockists\Api\Data\StockistDataProcessorInterface;
use Aligent\Stockists\Api\Data\StockistInterface;

class Coordinate implements StockistDataProcessorInterface
{

    /**
     * @inheritDoc
     */
    public function execute(array $data): array
    {
        if (empty($data[StockistInterface::LNG]) || empty($data[StockistInterface::LAT])) {
            // fetch by address
            $data[StockistInterface::LNG] = 0.0;
            $data[StockistInterface::LAT] = 0.0;
        }
        return $data;
    }
}

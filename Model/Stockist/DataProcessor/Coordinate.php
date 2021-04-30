<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist\DataProcessor;

class Coordinate implements \Aligent\Stockists\Api\Data\StockistDataProcessorInterface
{

    public function __construct()
    {
        // todo: inject class for fetching lng lat
    }

    public function execute(array $data): array
    {
        if (empty($data[\Aligent\Stockists\Api\Data\StockistInterface::LNG]) || empty($data[\Aligent\Stockists\Api\Data\StockistInterface::LAT])) {
            // fetch by address
            $data[\Aligent\Stockists\Api\Data\StockistInterface::LNG] = 0.0;
            $data[\Aligent\Stockists\Api\Data\StockistInterface::LAT] = 0.0;
        }
        return $data;
    }
}

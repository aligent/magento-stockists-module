<?php

namespace Aligent\Stockists\Api;

use Aligent\Stockists\Api\Data\StockistInterface;

interface AdapterInterface
{
    /**
     * @param $stockist
     * @return bool
     */
    public function addressHasChangedFor($stockist) : bool;

    /**
     * @param StockistInterface $stockist
     * @param string $key
     * @return string|null
     */
    public function buildRequest(StockistInterface $stockist, string $key) : ?string;

    /**
     * @param string $request
     * @return array|null
     */
    public function performGeocode(string $request) : ?array;

    /**
     * @param array $response
     * @return GeocodeResultInterface
     */
    public function handleResponse(array $response) : GeocodeResultInterface;
}

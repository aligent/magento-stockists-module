<?php


namespace Aligent\Stockists\Api;


use Aligent\Stockists\Api\Data\StockistInterface;

interface AdapterInterface
{
    public function buildRequest(StockistInterface $stockist, string $key) :? string;

    public function performGeocode(string $request) :? array;

    public function handleResponse(array $response) : \Magento\Framework\Controller\Result\Json;
}
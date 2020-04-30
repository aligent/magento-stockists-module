<?php


namespace Aligent\Stockists\Api;


interface AdapterInterface
{
    public function buildRequest(string $address, string $key) :? string;

    public function performGeocode(string $request) :? array;

    public function handleResponse(array $response) : \Magento\Framework\Controller\Result\Json;
}
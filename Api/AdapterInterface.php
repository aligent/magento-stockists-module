<?php


namespace Aligent\Stockists\Api;


interface AdapterInterface
{
    public function buildRequest();

    public function performGeocode();

    public function handleResponse();
}
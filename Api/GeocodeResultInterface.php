<?php


namespace Aligent\Stockists\Api;


interface GeocodeResultInterface
{
    public function setData(string $status, float $lat, float $long);
    public function getStatus() : string;
    public function wasSuccessful() : bool;
    public function getLat() : float;
    public function getLong() : float;
}
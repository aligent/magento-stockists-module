<?php


namespace Aligent\Stockists\Model;


use Aligent\Stockists\Api\GeocodeResultInterface;

class GoogleMapsGeocodeResult implements GeocodeResultInterface
{
    private $status;
    private $lat;
    private $long;

    public function setData(string $status, float $lat, float $long)
    {
        $this->status = $status;
        $this->lat = $lat;
        $this->long = $long;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function wasSuccessful() : bool
    {
        return $this->getStatus() == "OK";
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLong(): float
    {
        return $this->long;
    }
}
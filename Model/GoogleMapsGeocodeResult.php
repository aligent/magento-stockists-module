<?php


namespace Aligent\Stockists\Model;


use Aligent\Stockists\Api\GeocodeResultInterface;

class GoogleMapsGeocodeResult implements GeocodeResultInterface
{
    private $status;
    private $lat;
    private $lng;

    public function setData(string $status, float $lat, float $lng)
    {
        $this->status = $status;
        $this->lat = $lat;
        $this->lng = $lng;
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

    public function getLng(): float
    {
        return $this->lng;
    }
}
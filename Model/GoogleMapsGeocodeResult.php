<?php


namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\GeocodeResultInterface;

class GoogleMapsGeocodeResult implements GeocodeResultInterface
{
    const STATUS_CODE_OK = "OK";

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
        return (string)$this->status;
    }

    public function wasSuccessful() : bool
    {
        return $this->getStatus() === self::STATUS_CODE_OK;
    }

    public function getLat(): float
    {
        return (float)$this->lat;
    }

    public function getLng(): float
    {
        return (float)$this->lng;
    }
}

<?php


namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\GeocodeResultInterface;

class GoogleMapsGeocodeResult implements GeocodeResultInterface
{
    private $status;
    private $lat;
    private $lng;

    /**
     * @inheritDoc
     */
    public function setData(string $status, float $lat, float $lng)
    {
        $this->status = $status;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function wasSuccessful() : bool
    {
        return $this->getStatus() == "OK";
    }

    /**
     * @inheritDoc
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @inheritDoc
     */
    public function getLng(): float
    {
        return $this->lng;
    }
}

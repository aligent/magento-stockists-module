<?php


namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\GeocodeResultInterface;

class GoogleMapsGeocodeResult implements GeocodeResultInterface
{
    const STATUS_CODE_OK = "OK";

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
        return (string)$this->status;
    }

    /**
     * @inheritDoc
     */
    public function wasSuccessful() : bool
    {
        return $this->getStatus() === self::STATUS_CODE_OK;
    }

    /**
     * @inheritDoc
     */
    public function getLat(): float
    {
        return (float)$this->lat;
    }

    /**
     * @inheritDoc
     */
    public function getLng(): float
    {
        return (float)$this->lng;
    }
}

<?php


namespace Aligent\Stockists\Api;

interface GeocodeResultInterface
{
    /**
     * @param string $status
     * @param float $lat
     * @param float $lng
     * @return mixed
     */
    public function setData(string $status, float $lat, float $lng);

    /**
     * @return string
     */
    public function getStatus() : string;

    /**
     * @return bool
     */
    public function wasSuccessful() : bool;

    /**
     * @return float
     */
    public function getLat() : float;

    /**
     * @return float
     */
    public function getLng() : float;
}

<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\TradingHoursInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

class TradingHours extends DataObject implements TradingHoursInterface
{
    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * TradingHours constructor.
     * @param SerializerInterface $json
     */
    public function __construct(
        SerializerInterface $json
    ) {
        $this->json = $json;
    }
    /**
     * @return string
     */
    public function getMonday(): string
    {
        return $this->getData('monday') ?: '';
    }

    /**
     * @return string
     */
    public function getTuesday(): string
    {
        return $this->getData('tuesday') ?: '';
    }

    /**
     * @return string
     */
    public function getWednesday(): string
    {
        return $this->getData('wednesday') ?: '';
    }

    /**
     * @return string
     */
    public function getThursday(): string
    {
        return $this->getData('thursday') ?: '';
    }

    /**
     * @return string
     */
    public function getFriday(): string
    {
        return $this->getData('friday') ?: '';
    }

    /**
     * @return string
     */
    public function getSaturday(): string
    {
        return $this->getData('saturday') ?: '';
    }

    /**
     * @return string
     */
    public function getSunday(): string
    {
        return $this->getData('sunday') ?: '';
    }

    public function getPublicHolidays(): ?string
    {
        $publicHolidaysData = $this->getData('public_holidays') ?: '';

        if (!$publicHolidaysData) {
            return null;
        }

        return $this->json->serialize($publicHolidaysData);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setMonday(string $hours): TradingHoursInterface
    {
        return $this->setData('monday', $hours);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setTuesday(string $hours): TradingHoursInterface
    {
        return $this->setData('tuesday', $hours);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setWednesday(string $hours): TradingHoursInterface
    {
        return $this->setData('wednesday', $hours);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setThursday(string $hours): TradingHoursInterface
    {
        return $this->setData('thursday', $hours);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setFriday(string $hours): TradingHoursInterface
    {
        return $this->setData('friday', $hours);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setSaturday(string $hours): TradingHoursInterface
    {
        return $this->setData('saturday', $hours);
    }

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setSunday(string $hours): TradingHoursInterface
    {
        return $this->setData('sunday', $hours);
    }

    public function setPublicHolidays(string $publicHolidayHours): TradingHoursInterface
    {
        return $this->setData('public_holidays', $publicHolidayHours);
    }
}

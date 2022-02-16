<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\TradingHoursInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

class TradingHours extends DataObject implements TradingHoursInterface
{
    const PUBLIC_HOLIDAYS = 'public_holidays';

    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * @param SerializerInterface $json
     * @param array $data
     */
    public function __construct(
        SerializerInterface $json,
        array $data = []
    ) {
        parent::__construct($data);
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function getMonday(): string
    {
        return $this->getData('monday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getTuesday(): string
    {
        return $this->getData('tuesday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getWednesday(): string
    {
        return $this->getData('wednesday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getThursday(): string
    {
        return $this->getData('thursday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getFriday(): string
    {
        return $this->getData('friday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getSaturday(): string
    {
        return $this->getData('saturday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getSunday(): string
    {
        return $this->getData('sunday') ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getPublicHolidays(): ?string
    {
        $publicHolidaysData = $this->getData($this::PUBLIC_HOLIDAYS) ?: '';

        if (!$publicHolidaysData) {
            return null;
        }

        return $this->json->serialize($publicHolidaysData);
    }

    /**
     * @inheritDoc
     */
    public function setMonday(string $hours): void
    {
        $this->setData('monday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setTuesday(string $hours): void
    {
        $this->setData('tuesday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setWednesday(string $hours): void
    {
        $this->setData('wednesday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setThursday(string $hours): void
    {
        $this->setData('thursday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setFriday(string $hours): void
    {
        $this->setData('friday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setSaturday(string $hours): void
    {
        $this->setData('saturday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setSunday(string $hours): void
    {
        $this->setData('sunday', $hours);
    }

    /**
     * @inheritDoc
     */
    public function setPublicHolidays(string $publicHolidayHours): void
    {
        $this->setData($this::PUBLIC_HOLIDAYS, $publicHolidayHours);
    }
}

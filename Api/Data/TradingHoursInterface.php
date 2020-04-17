<?php

namespace Aligent\Stockists\Api\Data;

interface TradingHoursInterface
{
    /**
     * @return string
     */
    public function getMonday(): string;

    /**
     * @return string
     */
    public function getTuesday(): string;

    /**
     * @return string
     */
    public function getWednesday(): string;

    /**
     * @return string
     */
    public function getThursday(): string;

    /**
     * @return string
     */
    public function getFriday(): string;

    /**
     * @return string
     */
    public function getSaturday(): string;

    /**
     * @return string
     */
    public function getSunday(): string

    /**
     * @return string
     */
    public function getPublicHolidays(): ?string;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setMonday(string $hours): TradingHoursInterface;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setTuesday(string $hours): TradingHoursInterface;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setWednesday(string $hours): TradingHoursInterface;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setThursday(string $hours): TradingHoursInterface;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setFriday(string $hours): TradingHoursInterface;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setSaturday(string $hours): TradingHoursInterface;

    /**
     * @param string $hours
     * @return TradingHoursInterface
     */
    public function setSunday(string $hours): TradingHoursInterface;

    /**
     * @param string $publicHolidayHours
     * @return TradingHoursInterface
     */
    public function setPublicHolidays(string $publicHolidayHours): TradingHoursInterface;
}
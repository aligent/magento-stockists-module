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
    public function getSunday(): string;

    /**
     * @return string
     */
    public function getPublicHolidays(): ?string;

    /**
     * @param string $hours
     */
    public function setMonday(string $hours): void;

    /**
     * @param string $hours
     */
    public function setTuesday(string $hours): void;

    /**
     * @param string $hours
     */
    public function setWednesday(string $hours): void;

    /**
     * @param string $hours
     */
    public function setThursday(string $hours): void;

    /**
     * @param string $hours
     */
    public function setFriday(string $hours): void;

    /**
     * @param string $hours
     */
    public function setSaturday(string $hours): void;

    /**
     * @param string $hours
     */
    public function setSunday(string $hours): void;

    /**
     * @param string $publicHolidayHours
     */
    public function setPublicHolidays(string $publicHolidayHours): void;
}

<?php

namespace Aligent\Stockists\Api\Data;

interface StockistInterface
{
    /**
     * @return int
     */
    public function getStockistId(): int;

    /**
     * @param int $id
     * @return $this
     */
    public function setStockistId(int $id): StockistInterface;

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier(string $identifier): StockistInterface;

    /**
     * @return float
     */
    public function getLat(): ?float;

    /**
     * @param float $lat
     * @return $this
     */
    public function setLat(float $lat): StockistInterface;

    /**
     * @return float
     */
    public function getLng(): ?float;

    /**
     * @param float $lng
     * @return $this
     */
    public function setLng(float $lng): StockistInterface;

    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): StockistInterface;

    /**
     * @return string
     */
    public function getStreet(): ?string;

    /**
     * @param string $street
     * @return $this
     */
    public function setStreet(string $street): StockistInterface;

    /**
     * @return string
     */
    public function getCity(): ?string;

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): StockistInterface;

    /**
     * @return string
     */
    public function getPostcode(): ?string;

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode(string $postcode): StockistInterface;

    /**
     * @return string
     */
    public function getRegion(): ?string;

    /**
     * @param string $region
     * @return $this
     */
    public function setRegion(string $region): StockistInterface;

    /**
     * @return string
     */
    public function getCountry(): ?string;

    /**
     * @param string $countryCode
     * @return $this
     */
    public function setCountry(string $countryCode): StockistInterface;

    /**
     * @return string
     */
    public function getPhone(): ?string;

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): StockistInterface;

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds): StockistInterface;

    /**
     * @return \Aligent\Stockists\Api\Data\TradingHoursInterface
     */
    public function getHours(): ?\Aligent\Stockists\Api\Data\TradingHoursInterface;

    /**
     * @param \Aligent\Stockists\Api\Data\TradingHoursInterface $hours
     * @return $this
     */
    public function setHours(\Aligent\Stockists\Api\Data\TradingHoursInterface $hours): StockistInterface;
}
<?php

namespace Aligent\Stockists\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\ExtensionAttributesInterface;

interface StockistInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     * Notes: required as some UI Components only take constant as value
     */
    const STOCKIST_ID = 'stockist_id';
    const IDENTIFIER = 'identifier';
    const IS_ACTIVE = 'is_active';
    const DESCRIPTION = 'description';
    const URL_KEY = 'url_key';
    const LAT = 'lat';
    const LNG = 'lng';
    const NAME = 'name';
    const STREET = 'street';
    const CITY = 'city';
    const POSTCODE = 'postcode';
    const REGION = 'region';
    const REGION_ID = 'region_id';
    const COUNTRY = 'country';
    const COUNTRY_ID = 'country_id';
    const PHONE = 'phone';
    const HOURS = 'hours';
    const STORE_IDS = 'store_ids';
    const EXTENSION_ATTRIBUTES = 'extension_attributes';

    /**
     * @return int
     */
    public function getStockistId(): int;

    /**
     * @param int $id
     * @return void
     */
    public function setStockistId(int $id): void;

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * @param string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier): void;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void;

    /**
     * @return string|null
     */
    public function getUrlKey(): ?string;

    /**
     * @param string $urlKey
     * @return void
     */
    public function setUrlKey(string $urlKey): void;

    /**
     * @return float|null
     */
    public function getLat(): ?float;

    /**
     * @param float $lat
     * @return void
     */
    public function setLat(float $lat): void;

    /**
     * @return float|null
     */
    public function getLng(): ?float;

    /**
     * @param float $lng
     * @return void
     */
    public function setLng(float $lng): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @param string $street
     * @return void
     */
    public function setStreet(string $street): void;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void;

    /**
     * @return string|null
     */
    public function getPostcode(): ?string;

    /**
     * @param string $postcode
     * @return void
     */
    public function setPostcode(string $postcode): void;

    /**
     * @return string|null
     */
    public function getRegion(): ?string;

    /**
     * @param string $region
     * @return void
     */
    public function setRegion(string $region): void;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @param string $countryCode
     * @return void
     */
    public function setCountry(string $countryCode): void;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * @param string $phone
     * @return void
     */
    public function setPhone(string $phone): void;

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param int[] $storeIds
     * @return void
     */
    public function setStoreIds(array $storeIds): void;

    /**
     * @return \Aligent\Stockists\Api\Data\TradingHoursInterface|null
     */
    public function getHours(): ?TradingHoursInterface;

    /**
     * @param \Aligent\Stockists\Api\Data\TradingHoursInterface $hours
     * @return void
     */
    public function setHours(TradingHoursInterface $hours): void;

    /**
     * @return \Aligent\Stockists\Api\Data\StockistExtensionInterface
     */
    public function getExtensionAttributes(): StockistExtensionInterface;

    /**
     * @param \Aligent\Stockists\Api\Data\StockistExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(StockistExtensionInterface $extensionAttributes): void;
}

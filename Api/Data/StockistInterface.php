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
    const GALLERY = 'gallery';
    const ALLOW_STORE_DELIVERY = 'allow_store_delivery';
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
     */
    public function setStockistId(int $id): void;

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     */
    public function setDescription(string $description): void;

    /**
     * @return string|null
     */
    public function getGallery(): ?string;

    /**
     * @param string $gallery
     */
    public function setGallery(string $gallery): void;

    /**
     * @return string|null
     */
    public function getUrlKey(): ?string;

    /**
     * @param string $urlKey
     */
    public function setUrlKey(string $urlKey): void;

    /**
     * @return float|null
     */
    public function getLat(): ?float;

    /**
     * @param float $lat
     */
    public function setLat(float $lat): void;

    /**
     * @return float|null
     */
    public function getLng(): ?float;

    /**
     * @param float $lng
     */
    public function setLng(float $lng): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @param string $street
     */
    public function setStreet(string $street): void;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string $city
     */
    public function setCity(string $city): void;

    /**
     * @return string|null
     */
    public function getPostcode(): ?string;

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void;

    /**
     * @return string|null
     */
    public function getRegion(): ?string;

    /**
     * @param string $region
     */
    public function setRegion(string $region): void;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @param string $countryCode
     */
    public function setCountry(string $countryCode): void;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void;

    /**
     * @return bool
     */
    public function getAllowStoreDelivery(): bool;

    /**
     * @param bool $allowStoreDelivery
     */
    public function setAllowStoreDelivery(bool $allowStoreDelivery): void;

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param int[] $storeIds
     */
    public function setStoreIds(array $storeIds): void;

    /**
     * @return TradingHoursInterface|null
     */
    public function getHours(): ?TradingHoursInterface;

    /**
     * @param TradingHoursInterface $hours
     */
    public function setHours(TradingHoursInterface $hours): void;

    /**
     * @return \Aligent\Stockists\Api\Data\StockistExtensionInterface
     */
    public function getExtensionAttributes(): StockistExtensionInterface;

    /**
     * @param \Aligent\Stockists\Api\Data\StockistExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(StockistExtensionInterface $extensionAttributes): void;
}

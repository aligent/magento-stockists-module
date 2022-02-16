<?php

namespace Aligent\Stockists\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

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
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): StockistInterface;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     * @return StockistInterface
     */
    public function setDescription(string $description): StockistInterface;

    /**
     * @return string
     */
    public function getGallery(): ?string;

    /**
     * @param string $gallery
     * @return StockistInterface
     */
    public function setGallery(string $gallery): StockistInterface;

    /**
     * @return string
     */
    public function getUrlKey(): string;

    /**
     * @param string $urlKey
     * @return StockistInterface
     */
    public function setUrlKey(string $urlKey): StockistInterface;

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
     */
    public function setStreet(string $street): void;

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
     * @return bool|null
     */
    public function getAllowStoreDelivery(): ?bool;

    /**
     * @param string $allowStoreDelivery
     * @return StockistInterface
     */
    public function setAllowStoreDelivery(string $allowStoreDelivery): StockistInterface;

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

    /**
     * @return \Aligent\Stockists\Api\Data\StockistExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Aligent\Stockists\Api\Data\StockistExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aligent\Stockists\Api\Data\StockistExtensionInterface $extensionAttributes);
}

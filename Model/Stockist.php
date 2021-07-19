<?php

namespace Aligent\Stockists\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\DataObject\IdentityInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Api\Data\StockistInterface;

/**
 * @method StockistResource getResource()
 * @method ResourceModel\Stockist\Collection getCollection()
 */
class Stockist extends AbstractExtensibleModel implements StockistInterface, IdentityInterface
{
    const CACHE_TAG = 'aligent_stockist';
    protected $_cacheTag = 'aligent_stockist';
    protected $_eventPrefix = 'aligent_stockist';
    protected $_idFieldName = 'stockist_id';

    protected function _construct()
    {
        $this->_init(StockistResource::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return int
     */
    public function getStockistId(): int
    {
        return $this->getId();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setStockistId(int $id): StockistInterface
    {
        $this->setData(StockistInterface::STOCKIST_ID, $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->getData(StockistInterface::IDENTIFIER);
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier(string $identifier): StockistInterface
    {
        $this->setData(StockistInterface::IDENTIFIER, $identifier);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getData(StockistInterface::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return StockistInterface
     */
    public function setDescription(string $description): StockistInterface
    {
        return $this->setData(StockistInterface::DESCRIPTION, $description);
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return !!$this->getData(StockistInterface::IS_ACTIVE);
    }

    /**
     * @param bool $isActive
     * @return StockistInterface
     */
    public function setIsActive(bool $isActive): StockistInterface
    {
        return $this->setData(StockistInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @return string|null
     */
    public function getGallery(): ?string
    {
        return $this->getData(StockistInterface::GALLERY);
    }

    /**
     * @param string $gallery
     * @return StockistInterface
     */
    public function setGallery(string $gallery): StockistInterface
    {
        return $this->setData(StockistInterface::GALLERY, $gallery);
    }

    /**
     * @return string
     */
    public function getUrlKey(): string
    {
        return $this->getData(StockistInterface::URL_KEY);
    }

    /**
     * @param string $urlKey
     * @return StockistInterface
     */
    public function setUrlKey(string $urlKey): StockistInterface
    {
        return $this->setData(StockistInterface::URL_KEY, $urlKey);
    }

    /**
     * @return float
     */
    public function getLat(): ?float
    {
        return $this->getData(StockistInterface::LAT);
    }

    /**
     * @param float $lat
     * @return $this
     */
    public function setLat(float $lat): StockistInterface
    {
        $this->setData(StockistInterface::LAT, $lat);
        return $this;
    }

    /**
     * @return float
     */
    public function getLng(): ?float
    {
        return $this->getData(StockistInterface::LNG);
    }

    /**
     * @param float $lng
     * @return $this
     */
    public function setLng(float $lng): StockistInterface
    {
        $this->setData(StockistInterface::LNG, $lng);
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->getData(StockistInterface::NAME);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): StockistInterface
    {
        $this->setData(StockistInterface::NAME, $name);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->getData(StockistInterface::STREET);
    }

    /**
     * @param string $street
     * @return $this
     */
    public function setStreet(string $street): StockistInterface
    {
        $this->setData(StockistInterface::STREET, $street);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSuburb(): ?string
    {
        return $this->getData(StockistInterface::SUBURB);
    }

    /**
     * @param string $suburb
     * @return StockistInterface
     */
    public function setSuburb(string $suburb): StockistInterface
    {
        return $this->setData(StockistInterface::SUBURB, $suburb);
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->getData(StockistInterface::CITY);
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): StockistInterface
    {
        $this->setData(StockistInterface::CITY, $city);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostcode(): ?string
    {
        return $this->getData(StockistInterface::POSTCODE);
    }

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode(string $postcode): StockistInterface
    {
        $this->setData(StockistInterface::POSTCODE, $postcode);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->getData(StockistInterface::REGION);
    }

    /**
     * @param string $region
     * @return $this
     */
    public function setRegion(string $region): StockistInterface
    {
        $this->setData(StockistInterface::REGION, $region);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->getData(StockistInterface::COUNTRY);
    }

    /**
     * @param string $countryCode
     * @return $this
     */
    public function setCountry(string $countryCode): StockistInterface
    {
        $this->setData(StockistInterface::COUNTRY, $countryCode);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->getData(StockistInterface::PHONE);
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): StockistInterface
    {
        $this->setData(StockistInterface::PHONE, $phone);
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAllowStoreDelivery(): ?bool
    {
        return !!$this->getData(StockistInterface::ALLOW_STORE_DELIVERY);
    }

    /**
     * @param string $allowStoreDelivery
     * @return StockistInterface
     */
    public function setAllowStoreDelivery(string $allowStoreDelivery): StockistInterface
    {
        $this->setData(StockistInterface::ALLOW_STORE_DELIVERY, $allowStoreDelivery);
        return $this;
    }

    /**
     * @return int[]
     */
    public function getStoreIds(): array
    {
        return $this->getData(StockistInterface::STORE_IDS);
    }

    /**
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds): StockistInterface
    {
        $this->setData(StockistInterface::STORE_IDS, $storeIds);
        return $this;
    }

    /**
     * @return \Aligent\Stockists\Api\Data\TradingHoursInterface
     */
    public function getHours(): ?\Aligent\Stockists\Api\Data\TradingHoursInterface
    {
        return $this->getData(StockistInterface::HOURS);
    }

    /**
     * @param \Aligent\Stockists\Api\Data\TradingHoursInterface $hours
     * @return $this
     */
    public function setHours(\Aligent\Stockists\Api\Data\TradingHoursInterface $hours): StockistInterface
    {
        $this->setData(StockistInterface::HOURS, $hours);
        return $this;
    }

    /**
     * @return \Aligent\Stockists\Api\Data\StockistExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \Aligent\Stockists\Api\Data\StockistExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aligent\Stockists\Api\Data\StockistExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

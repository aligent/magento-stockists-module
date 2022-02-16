<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\TradingHoursInterface;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\DataObject\IdentityInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Api\Data\StockistInterface;

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

    /**
     * @inheritDoc
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getStockistId(): int
    {
        return (int)$this->getId();
    }

    /**
     * @inheritDoc
     */
    public function setStockistId(int $id): void
    {
        $this->setData(StockistInterface::STOCKIST_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): ?string
    {
        return $this->getData(StockistInterface::IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier(string $identifier): void
    {
        $this->setData(StockistInterface::IDENTIFIER, $identifier);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->getData(StockistInterface::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): void
    {
        $this->setData(StockistInterface::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData(StockistInterface::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $isActive): void
    {
        $this->setData(StockistInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function getGallery(): ?string
    {
        return $this->getData(StockistInterface::GALLERY);
    }

    /**
     * @inheritDoc
     */
    public function setGallery(string $gallery): void
    {
        $this->setData(StockistInterface::GALLERY, $gallery);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey(): ?string
    {
        return $this->getData(StockistInterface::URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setUrlKey(string $urlKey): void
    {
        $this->setData(StockistInterface::URL_KEY, $urlKey);
    }

    /**
     * @inheritDoc
     */
    public function getLat(): ?float
    {
        $lat = $this->getData(StockistInterface::LAT);
        return ($lat === null) ? null : (float)$lat;
    }

    /**
     * @inheritDoc
     */
    public function setLat(float $lat): void
    {
        $this->setData(StockistInterface::LAT, $lat);
    }

    /**
     * @inheritDoc
     */
    public function getLng(): ?float
    {
        $lng = $this->getData(StockistInterface::LNG);
        return ($lng === null) ? null : (float)$lng;
    }

    /**
     * @inheritDoc
     */
    public function setLng(float $lng): void
    {
        $this->setData(StockistInterface::LNG, $lng);
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->getData(StockistInterface::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): void
    {
        $this->setData(StockistInterface::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getStreet(): ?string
    {
        return $this->getData(StockistInterface::STREET);
    }

    /**
     * @inheritDoc
     */
    public function setStreet(string $street): void
    {
        $this->setData(StockistInterface::STREET, $street);
    }

    /**
     * @inheritDoc
     */
    public function getCity(): ?string
    {
        return $this->getData(StockistInterface::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity(string $city): void
    {
        $this->setData(StockistInterface::CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function getPostcode(): ?string
    {
        return $this->getData(StockistInterface::POSTCODE);
    }

    /**
     * @inheritDoc
     */
    public function setPostcode(string $postcode): void
    {
        $this->setData(StockistInterface::POSTCODE, $postcode);
    }

    /**
     * @inheritDoc
     */
    public function getRegion(): ?string
    {
        return $this->getData(StockistInterface::REGION);
    }

    /**
     * @inheritDoc
     */
    public function setRegion(string $region): void
    {
        $this->setData(StockistInterface::REGION, $region);
    }

    /**
     * @inheritDoc
     */
    public function getCountry(): ?string
    {
        return $this->getData(StockistInterface::COUNTRY);
    }

    /**
     * @inheritDoc
     */
    public function setCountry(string $countryCode): void
    {
        $this->setData(StockistInterface::COUNTRY, $countryCode);
    }

    /**
     * @inheritDoc
     */
    public function getPhone(): ?string
    {
        return $this->getData(StockistInterface::PHONE);
    }

    /**
     * @inheritDoc
     */
    public function setPhone(string $phone): void
    {
        $this->setData(StockistInterface::PHONE, $phone);
    }

    /**
     * @inheritDoc
     */
    public function getAllowStoreDelivery(): bool
    {
        return (bool)$this->getData(StockistInterface::ALLOW_STORE_DELIVERY);
    }

    /**
     * @inheritDoc
     */
    public function setAllowStoreDelivery(bool $allowStoreDelivery): void
    {
        $this->setData(StockistInterface::ALLOW_STORE_DELIVERY, $allowStoreDelivery);
    }

    /**
     * @inheritDoc
     */
    public function getStoreIds(): array
    {
        return $this->getData(StockistInterface::STORE_IDS);
    }

    /**
     * @inheritDoc
     */
    public function setStoreIds(array $storeIds): void
    {
        $this->setData(StockistInterface::STORE_IDS, $storeIds);
    }

    /**
     * @inheritDoc
     */
    public function getHours(): ?TradingHoursInterface
    {
        $res = $this->getData(StockistInterface::HOURS);
        return $res instanceof TradingHoursInterface ? $res : null;
    }

    /**
     * @inheritDoc
     */
    public function setHours(TradingHoursInterface $hours): void
    {
        $this->setData(StockistInterface::HOURS, $hours);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ExtensionAttributesInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(ExtensionAttributesInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}

<?php

namespace Aligent\Stockists\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Api\Data\StockistInterface;

/**
 * @method StockistResource getResource()
 * @method ResourceModel\Stockist\Collection getCollection()
 */
class Stockist extends AbstractModel implements StockistInterface, IdentityInterface
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
        $this->setData('stockist_id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->getData('identifier');
    }

    /**
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier(string $identifier): StockistInterface
    {
        $this->setData('identifier', $identifier);
        return $this;
    }

    /**
     * @return float
     */
    public function getLat(): ?float
    {
        return $this->getData('lat');
    }

    /**
     * @param float $lat
     * @return $this
     */
    public function setLat(float $lat): StockistInterface
    {
        $this->setData('lat', $lat);
        return $this;
    }

    /**
     * @return float
     */
    public function getLng(): ?float
    {
        return $this->getData('lng');
    }

    /**
     * @param float $lng
     * @return $this
     */
    public function setLng(float $lng): StockistInterface
    {
        $this->setData('lng', $lng);
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->getData('name');
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): StockistInterface
    {
        $this->setData('name', $name);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->getData('street');
    }

    /**
     * @param string $street
     * @return $this
     */
    public function setStreet(string $street): StockistInterface
    {
        $this->setData('street', $street);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->getData('city');
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): StockistInterface
    {
        $this->setData('city', $city);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostcode(): ?string
    {
        return $this->getData('postcode');
    }

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode(string $postcode): StockistInterface
    {
        $this->setData('postcode', $postcode);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->getData('region');
    }

    /**
     * @param string $region
     * @return $this
     */
    public function setRegion(string $region): StockistInterface
    {
        $this->setData('region', $region);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->getData('country');
    }

    /**
     * @param string $countryCode
     * @return $this
     */
    public function setCountry(string $countryCode): StockistInterface
    {
        $this->setData('country', $countryCode);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->getData('phone');
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone): StockistInterface
    {
        $this->setData('phone', $phone);
        return $this;
    }

    /**
     * @return int[]
     */
    public function getStoreIds(): array
    {
        return $this->getData('store_ids');
    }

    /**
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds): StockistInterface
    {
        $this->setData('store_ids', $storeIds);
        return $this;
    }

    /**
     * @return \Aligent\Stockists\Api\Data\TradingHoursInterface
     */
    public function getHours(): ?\Aligent\Stockists\Api\Data\TradingHoursInterface
    {
        return $this->getData('hours');
    }

    /**
     * @param \Aligent\Stockists\Api\Data\TradingHoursInterface $hours
     * @return $this
     */
    public function setHours(\Aligent\Stockists\Api\Data\TradingHoursInterface $hours): StockistInterface
    {
        $this->setData('hours', $hours);
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
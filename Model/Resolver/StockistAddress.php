<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class StockistAddress implements ResolverInterface
{
    /**
     * @var array|null
     */
    private ?array $regionCodeCache = null;

    /**
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        private readonly RegionCollectionFactory $regionCollectionFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var StockistInterface $stockist */
        $stockist = $value['model'];
        $regionId = $stockist->getRegionId();

        return [
            'street' => $stockist->getStreet(),
            'city' => $stockist->getCity(),
            'postcode' => $stockist->getPostcode(),
            'region' => $stockist->getRegion(),
            'region_id' => $regionId,
            'region_code' => $this->getRegionCode($regionId),
            'country_code' => $stockist->getCountry(),
            'phone' => $stockist->getPhone(),
            'email' => $stockist->getEmail(),
            'fax' => $stockist->getFax()
        ];
    }

    /**
     * Get region code by region ID
     *
     * @param int|null $regionId
     * @return string|null
     */
    private function getRegionCode(?int $regionId): ?string
    {
        if (!$regionId) {
            return null;
        }

        if ($this->regionCodeCache === null) {
            $this->loadRegionCodes();
        }

        return $this->regionCodeCache[$regionId] ?? null;
    }

    /**
     * Load all region codes into cache
     *
     * @return void
     */
    private function loadRegionCodes(): void
    {
        $this->regionCodeCache = [];
        $regionCollection = $this->regionCollectionFactory->create();

        foreach ($regionCollection as $region) {
            $this->regionCodeCache[(int)$region->getId()] = $region->getCode();
        }
    }
}

<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Model\ResourceModel\Stockist\CollectionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class SearchStockistsByAddress implements ResolverInterface
{
    /**
     * @var array|null
     */
    private ?array $regionCodeCache = null;

    /**
     * @param CollectionFactory $stockistCollectionFactory
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        private readonly CollectionFactory $stockistCollectionFactory,
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
        $this->validateInput($args);

        $countryId = strtoupper(trim($args['country_id']));
        $queryString = trim($args['query_string']);
        $pageSize = (int)($args['pageSize'] ?? 20);
        $currentPage = (int)($args['currentPage'] ?? 1);

        $collection = $this->stockistCollectionFactory->create();

        // Get current store ID from context
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        // Filter by country
        $collection->addFieldToFilter('country', $countryId);

        // Filter by is_active
        $collection->addFieldToFilter('is_active', 1);

        // Filter by store: store_ids contains current store OR store_ids contains 0 (all stores)
        $collection->getSelect()->where(
            'FIND_IN_SET(?, store_ids) OR FIND_IN_SET(0, store_ids)',
            $storeId
        );

        // Search by concatenated address fields using LIKE
        $searchTerm = '%' . $queryString . '%';
        $collection->getSelect()->where(
            "CONCAT(COALESCE(street, ''), ' ', COALESCE(city, ''), ' ', COALESCE(postcode, ''), ' ', COALESCE(region, '')) LIKE ?",
            $searchTerm
        );

        // Get total count before pagination
        $totalCount = $collection->getSize();

        // Apply pagination
        $collection->setPageSize($pageSize);
        $collection->setCurPage($currentPage);

        // Calculate total pages
        $totalPages = $pageSize > 0 ? (int)ceil($totalCount / $pageSize) : 0;

        // Build result items with region_code
        $items = [];
        foreach ($collection as $stockist) {
            $regionCode = $this->getRegionCode((int)$stockist->getRegionId());

            $items[] = [
                'identifier' => $stockist->getIdentifier(),
                'name' => $stockist->getName(),
                'full_address' => (string)($stockist->getStreet() . ', ' . $stockist->getCity() . ', ' . $stockist->getPostcode() . ', '. $regionCode),
                'url_key' => $stockist->getUrlKey(),
                'is_active' => (bool)$stockist->getIsActive(),
                'description' => $stockist->getDescription(),
                'street' => $stockist->getStreet(),
                'city' => $stockist->getCity(),
                'postcode' => $stockist->getPostcode(),
                'region' => $stockist->getRegion(),
                'region_id' => $stockist->getRegionId() ? (int)$stockist->getRegionId() : null,
                'region_code' => $regionCode,
                'country' => $stockist->getCountry(),
                'phone' => $stockist->getPhone(),
                'email' => $stockist->getEmail(),
                'fax' => $stockist->getFax(),
                'lat' => $stockist->getLat() ? (float)$stockist->getLat() : null,
                'lng' => $stockist->getLng() ? (float)$stockist->getLng() : null,
                'store_ids' => is_array($stockist->getStoreIds())
                    ? implode(',', $stockist->getStoreIds())
                    : $stockist->getData('store_ids'),
                'meta_title' => $stockist->getMetaTitle(),
                'meta_keywords' => $stockist->getMetaKeywords(),
                'meta_description' => $stockist->getMetaDescription(),
            ];
        }

        return [
            'items' => $items,
            'total_count' => $totalCount,
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $currentPage,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Validate input arguments
     *
     * @param array|null $args
     * @throws GraphQlInputException
     */
    private function validateInput(?array $args): void
    {
        if (empty($args['country_id'])) {
            throw new GraphQlInputException(__('country_id is required'));
        }

        if (empty($args['query_string'])) {
            throw new GraphQlInputException(__('query is required'));
        }

        if (strlen($args['query_string']) < 3) {
            throw new GraphQlInputException(__('search query must be at least 3 characters'));
        }
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

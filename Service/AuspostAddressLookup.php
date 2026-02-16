<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Service;

use Aligent\Stockists\Model\OptionSource\AddressLookupSource;
use Aligent\Stockists\Model\ResourceModel\AuspostPostcode as AuspostPostcodeResource;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;

class AuspostAddressLookup
{
    private const XML_PATH_ADDRESS_LOOKUP_SOURCE = 'stockists/geocode/address_lookup_source';

    /**
     * @var array|null
     */
    private ?array $regionNameCache = null;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resourceConnection
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ResourceConnection $resourceConnection,
        private readonly RegionCollectionFactory $regionCollectionFactory
    ) {
    }

    /**
     * Check if AusPost address lookup is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADDRESS_LOOKUP_SOURCE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) === AddressLookupSource::AUSPOST;
    }

    /**
     * Search AusPost postcode data by query string
     *
     * @param string $query
     * @param int $pageSize
     * @param int $currentPage
     * @return array{items: array, total_count: int}
     */
    public function searchByQuery(string $query, int $pageSize, int $currentPage): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(AuspostPostcodeResource::TABLE_NAME);
        $searchTerm = '%' . $query . '%';

        // Build base select with LIKE search on concatenated fields
        $select = $connection->select()
            ->from($tableName)
            ->where(
                "CONCAT(pcode, ' ', locality, ' ', state) LIKE ?",
                $searchTerm
            )
            ->where('latitude IS NOT NULL')
            ->where('longitude IS NOT NULL');

        // Get total count
        $countSelect = clone $select;
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(new \Zend_Db_Expr('COUNT(*)'));
        $totalCount = (int)$connection->fetchOne($countSelect);

        // Apply pagination
        $offset = ($currentPage - 1) * $pageSize;
        $select->limit($pageSize, $offset);

        $rows = $connection->fetchAll($select);

        $items = [];
        foreach ($rows as $row) {
            $items[] = $this->mapToSearchItem($row);
        }

        return [
            'items' => $items,
            'total_count' => $totalCount,
        ];
    }

    /**
     * Map database row to StockistSearchItem format
     *
     * @param array $row
     * @return array
     */
    private function mapToSearchItem(array $row): array
    {
        $locality = $row['locality'] ?? '';
        $state = $row['state'] ?? '';
        $pcode = $row['pcode'] ?? '';
        $regionName = $this->getRegionName($state);

        return [
            'name' => $locality,
            'full_address' => "$locality, $state, $pcode",
            'postcode' => $pcode,
            'city' => $locality,
            'region' => $regionName,
            'region_code' => $state,
            'country' => 'AU',
            'lat' => isset($row['latitude']) ? (float)$row['latitude'] : null,
            'lng' => isset($row['longitude']) ? (float)$row['longitude'] : null,
        ];
    }

    /**
     * Get full region name from state code
     *
     * @param string $stateCode
     * @return string
     */
    private function getRegionName(string $stateCode): string
    {
        if ($this->regionNameCache === null) {
            $this->loadRegionNames();
        }

        return $this->regionNameCache[strtoupper($stateCode)] ?? $stateCode;
    }

    /**
     * Load Australian region names into cache
     *
     * @return void
     */
    private function loadRegionNames(): void
    {
        $this->regionNameCache = [];
        $regionCollection = $this->regionCollectionFactory->create();
        $regionCollection->addCountryFilter('AU');

        foreach ($regionCollection as $region) {
            $this->regionNameCache[strtoupper($region->getCode())] = $region->getDefaultName();
        }
    }
}

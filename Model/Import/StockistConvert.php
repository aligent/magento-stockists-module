<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;

class StockistConvert
{
    /**
     * @var array|null
     */
    private ?array $regionByNameCache = null;

    /**
     * @var array|null
     */
    private ?array $regionByCodeCache = null;

    /**
     * @var array|null
     */
    private ?array $regionNameByIdCache = null;

    /**
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        private readonly RegionCollectionFactory $regionCollectionFactory
    ) {
    }

    /**
     * Convert CSV row data to stockist data array
     *
     * @param array $rowData
     * @return array
     */
    public function convert(array $rowData): array
    {
        $data = [];

        // Map CSV columns to stockist fields (region_id is looked up, not directly mapped)
        $columnMapping = [
            Stockists::COL_IDENTIFIER => 'identifier',
            Stockists::COL_NAME => 'name',
            Stockists::COL_URL_KEY => 'url_key',
            Stockists::COL_IS_ACTIVE => 'is_active',
            Stockists::COL_DESCRIPTION => 'description',
            Stockists::COL_STREET => 'street',
            Stockists::COL_CITY => 'city',
            Stockists::COL_POSTCODE => 'postcode',
            Stockists::COL_COUNTRY => 'country',
            Stockists::COL_PHONE => 'phone',
            Stockists::COL_EMAIL => 'email',
            Stockists::COL_FAX => 'fax',
            Stockists::COL_LAT => 'lat',
            Stockists::COL_LNG => 'lng',
            Stockists::COL_HOURS => 'hours',
            Stockists::COL_STORE_IDS => 'store_ids',
            Stockists::COL_META_TITLE => 'meta_title',
            Stockists::COL_META_KEYWORDS => 'meta_keywords',
            Stockists::COL_META_DESCRIPTION => 'meta_description',
        ];

        foreach ($columnMapping as $csvColumn => $fieldName) {
            if (isset($rowData[$csvColumn]) && trim((string)$rowData[$csvColumn]) !== '') {
                $data[$fieldName] = trim((string)$rowData[$csvColumn]);
            }
        }

        // Handle is_active boolean conversion
        if (isset($data['is_active'])) {
            $data['is_active'] = in_array(
                strtolower($data['is_active']),
                ['1', 'true', 'yes'],
                true
            ) ? 'true' : 'false';
        } else {
            $data['is_active'] = 'true';
        }

        // Handle store_ids as comma-separated values
        if (isset($data['store_ids']) && is_string($data['store_ids'])) {
            $storeIds = array_map('trim', explode(',', $data['store_ids']));
            $data['store_ids'] = array_map('intval', array_filter($storeIds, 'is_numeric'));
        }

        if (empty($data['store_ids'])) {
            $data['store_ids'] = [0]; // Default to all store views
        }

        // Map country to country_id for hydrator
        $countryCode = isset($data['country']) ? strtoupper($data['country']) : null;
        if ($countryCode && !isset($data['country_id'])) {
            $data['country_id'] = $countryCode;
        }

        // Look up region_id - priority: region_code column, then region column
        $regionCode = isset($rowData[Stockists::COL_REGION_CODE])
            ? trim((string)$rowData[Stockists::COL_REGION_CODE])
            : null;
        $regionName = isset($rowData[Stockists::COL_REGION])
            ? trim((string)$rowData[Stockists::COL_REGION])
            : null;

        if ($countryCode) {
            if ($regionCode) {
                // Look up by region_code column
                $regionId = $this->getRegionIdByCode($regionCode, $countryCode);
                if ($regionId) {
                    $data['region_id'] = $regionId;
                    // Also set the region name from the looked up region
                    $data['region'] = $this->getRegionNameById($regionId);
                }
            } elseif ($regionName) {
                // Look up by region column (name)
                $regionId = $this->getRegionIdByName($regionName, $countryCode);
                if ($regionId) {
                    $data['region_id'] = $regionId;
                    $data['region'] = $regionName;
                }
            }
        }

        // Handle numeric fields
        $numericFields = ['lat', 'lng'];
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                if (is_numeric($data[$field])) {
                    $data[$field] = (float)$data[$field];
                } else {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    /**
     * Get region_id by region name and country code
     *
     * @param string $regionName
     * @param string $countryCode
     * @return int|null
     */
    public function getRegionIdByName(string $regionName, string $countryCode): ?int
    {
        if ($this->regionByNameCache === null) {
            $this->loadRegionCache();
        }

        $key = strtolower($countryCode . '_' . $regionName);
        return $this->regionByNameCache[$key] ?? null;
    }

    /**
     * Get region_id by region code and country code
     *
     * @param string $regionCode
     * @param string $countryCode
     * @return int|null
     */
    public function getRegionIdByCode(string $regionCode, string $countryCode): ?int
    {
        if ($this->regionByCodeCache === null) {
            $this->loadRegionCache();
        }

        $key = strtolower($countryCode . '_' . $regionCode);
        return $this->regionByCodeCache[$key] ?? null;
    }

    /**
     * Get region name by region ID
     *
     * @param int $regionId
     * @return string|null
     */
    public function getRegionNameById(int $regionId): ?string
    {
        if ($this->regionNameByIdCache === null) {
            $this->loadRegionCache();
        }

        return $this->regionNameByIdCache[$regionId] ?? null;
    }

    /**
     * Load all regions into cache
     *
     * @return void
     */
    private function loadRegionCache(): void
    {
        $this->regionByNameCache = [];
        $this->regionByCodeCache = [];
        $this->regionNameByIdCache = [];

        $regionCollection = $this->regionCollectionFactory->create();

        foreach ($regionCollection as $region) {
            $countryId = $region->getCountryId();
            $regionId = (int)$region->getId();
            $regionName = $region->getDefaultName();

            // Cache by name (e.g., "au_new south wales" => 554)
            $keyByName = strtolower($countryId . '_' . $regionName);
            $this->regionByNameCache[$keyByName] = $regionId;

            // Also cache by locale name if different
            if ($region->getName() !== $regionName) {
                $keyByLocaleName = strtolower($countryId . '_' . $region->getName());
                $this->regionByNameCache[$keyByLocaleName] = $regionId;
            }

            // Cache by code (e.g., "au_nsw" => 554)
            $keyByCode = strtolower($countryId . '_' . $region->getCode());
            $this->regionByCodeCache[$keyByCode] = $regionId;

            // Cache name by ID for reverse lookup
            $this->regionNameByIdCache[$regionId] = $regionName;
        }
    }
}

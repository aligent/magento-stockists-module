<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

use Aligent\Stockists\Model\Import\Stockists;
use Aligent\Stockists\Model\Import\StockistConvert;

class RegionValidator implements ValidatorInterface
{
    /**
     * @var StockistConvert
     */
    private StockistConvert $stockistConvert;

    /**
     * @param StockistConvert $stockistConvert
     */
    public function __construct(
        StockistConvert $stockistConvert
    ) {
        $this->stockistConvert = $stockistConvert;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        $countryCode = isset($rowData[Stockists::COL_COUNTRY])
            ? strtoupper(trim((string)$rowData[Stockists::COL_COUNTRY]))
            : null;

        if (!$countryCode) {
            return $errors; // Country validator will handle this
        }

        $regionCode = isset($rowData[Stockists::COL_REGION_CODE])
            ? trim((string)$rowData[Stockists::COL_REGION_CODE])
            : null;
        $regionName = isset($rowData[Stockists::COL_REGION])
            ? trim((string)$rowData[Stockists::COL_REGION])
            : null;

        // Validate region_code column if provided
        if ($regionCode !== null && $regionCode !== '') {
            $regionId = $this->stockistConvert->getRegionIdByCode($regionCode, $countryCode);
            if ($regionId === null) {
                $errors[] = __(
                    'Invalid region_code "%1" for country "%2"',
                    $regionCode,
                    $countryCode
                );
            }
        } elseif ($regionName !== null && $regionName !== '') {
            // Validate region column (name) if provided and region_code is not
            $regionId = $this->stockistConvert->getRegionIdByName($regionName, $countryCode);
            if ($regionId === null) {
                $errors[] = __(
                    'Invalid region "%1" for country "%2"',
                    $regionName,
                    $countryCode
                );
            }
        }

        return $errors;
    }
}

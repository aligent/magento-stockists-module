<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

use Aligent\Stockists\Model\Import\Stockists;

class CoordinatesValidator implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        // Validate latitude
        if (isset($rowData[Stockists::COL_LAT]) && trim((string)$rowData[Stockists::COL_LAT]) !== '') {
            $lat = $rowData[Stockists::COL_LAT];
            if (!is_numeric($lat) || (float)$lat < -90 || (float)$lat > 90) {
                $errors[] = __('Latitude must be a number between -90 and 90');
            }
        }

        // Validate longitude
        if (isset($rowData[Stockists::COL_LNG]) && trim((string)$rowData[Stockists::COL_LNG]) !== '') {
            $lng = $rowData[Stockists::COL_LNG];
            if (!is_numeric($lng) || (float)$lng < -180 || (float)$lng > 180) {
                $errors[] = __('Longitude must be a number between -180 and 180');
            }
        }

        return $errors;
    }
}

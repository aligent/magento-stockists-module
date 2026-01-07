<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

interface ValidatorInterface
{
    /**
     * Validate row data
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return array Array of error messages
     */
    public function validate(array $rowData, int $rowNumber): array;
}

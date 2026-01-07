<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

use Aligent\Stockists\Model\Import\Stockists;

class EmailValidator implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        if (isset($rowData[Stockists::COL_EMAIL]) && trim((string)$rowData[Stockists::COL_EMAIL]) !== '') {
            $email = trim($rowData[Stockists::COL_EMAIL]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = __('Invalid email format: %1', $email);
            }
        }

        return $errors;
    }
}

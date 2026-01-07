<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

use Aligent\Stockists\Model\Import\Stockists;

class RequiredFieldsValidator implements ValidatorInterface
{
    /**
     * @var array
     */
    private array $requiredFields = [
        Stockists::COL_NAME => 'Name',
        Stockists::COL_URL_KEY => 'URL Key',
        Stockists::COL_COUNTRY => 'Country',
        Stockists::COL_POSTCODE => 'Postcode',
    ];

    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        foreach ($this->requiredFields as $field => $label) {
            if (!isset($rowData[$field]) || trim((string)$rowData[$field]) === '') {
                $errors[] = __('%1 is required', $label);
            }
        }

        return $errors;
    }
}

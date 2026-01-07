<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

use Aligent\Stockists\Model\Import\Stockists;

class IdentifierValidator implements ValidatorInterface
{
    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        if (!isset($rowData[Stockists::COL_IDENTIFIER]) || trim($rowData[Stockists::COL_IDENTIFIER]) === '') {
            $errors[] = __('Identifier is required');
        } elseif (preg_match('/\s/', $rowData[Stockists::COL_IDENTIFIER])) {
            $errors[] = __('Identifier cannot contain whitespace');
        }

        return $errors;
    }
}

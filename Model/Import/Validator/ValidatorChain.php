<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

class ValidatorChain implements ValidatorInterface
{
    /**
     * @param ValidatorInterface[] $validators
     */
    public function __construct(private array $validators = [])
    {
    }

    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        foreach ($this->validators as $validator) {
            $validatorErrors = $validator->validate($rowData, $rowNumber);
            $errors = array_merge($errors, $validatorErrors);
        }

        return $errors;
    }
}

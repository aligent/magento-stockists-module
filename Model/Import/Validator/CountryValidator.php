<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Validator;

use Aligent\Stockists\Model\Import\Stockists;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class CountryValidator implements ValidatorInterface
{
    /**
     * @var array|null
     */
    private ?array $validCountryCodes = null;

    /**
     * @param CountryCollectionFactory $countryCollectionFactory
     */
    public function __construct(
        private readonly CountryCollectionFactory $countryCollectionFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function validate(array $rowData, int $rowNumber): array
    {
        $errors = [];

        if (isset($rowData[Stockists::COL_COUNTRY]) && trim($rowData[Stockists::COL_COUNTRY]) !== '') {
            $countryCode = strtoupper(trim($rowData[Stockists::COL_COUNTRY]));

            if (!in_array($countryCode, $this->getValidCountryCodes(), true)) {
                $errors[] = __('Invalid country code: %1', $rowData[Stockists::COL_COUNTRY]);
            }
        }

        return $errors;
    }

    /**
     * Get valid country codes
     *
     * @return array
     */
    private function getValidCountryCodes(): array
    {
        if ($this->validCountryCodes === null) {
            $collection = $this->countryCollectionFactory->create();
            $this->validCountryCodes = $collection->getColumnValues('country_id');
        }

        return $this->validCountryCodes;
    }
}

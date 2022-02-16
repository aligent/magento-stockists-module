<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\Data\TradingHoursInterface;
use Aligent\Stockists\Api\StockistValidationInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\ResourceModel\Country;
use Magento\Framework\Exception\LocalizedException;

class StockistValidation implements StockistValidationInterface
{
    /**
     * @var CountryFactory
     */
    private $countryFactory;
    /**
     * @var Country
     */
    private $countryResource;

    /**
     * StockistValidation constructor.
     * @param CountryFactory $countryFactory
     * @param Country $country
     */
    public function __construct(
        CountryFactory $countryFactory,
        Country $country
    ) {
        $this->countryFactory = $countryFactory;
        $this->countryResource = $country;
    }

    /**
     * @param StockistInterface $stockist
     * @return array
     */
    public function validate(StockistInterface $stockist) : array
    {
        $errors = [];
        if (!$stockist->getIdentifier()) {
            $errors[] = __('Identifier is missing');
        }

        if (!$stockist->getName()) {
            $errors[] = __('Name is missing');
        }

        if (!$stockist->getUrlKey()) {
            $errors[] = __('URL Key is missing');
        }

        $countryCode = $stockist->getCountry();

        if ($countryCode && !$this->isIso2CountryCode($countryCode)) {
            $errors[] = __('Country code is invalid');
        }

        return $errors;
    }

    private function isIso2CountryCode(string $countryCode): bool
    {
        $country = $this->countryFactory->create();
        try {
            $this->countryResource->loadByCode($country, $countryCode);
        } catch (LocalizedException $e) {
            return false;
        }

        if (!$country->getId()) {
            return false;
        }

        return true;
    }
}

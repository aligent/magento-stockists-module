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

    public function validate(StockistInterface $stockist) : bool
    {
        if (!$stockist->getIdentifier()) {
            return false;
        }

        if (!$stockist->getName()) {
            return false;
        }

        if (!$stockist->getUrlKey()) {
            return false;
        }

        if ($stockist->getIsActive() === null) {
            return false;
        }

        $tradingHours = $stockist->getHours();

        if ($tradingHours && !is_subclass_of($tradingHours, TradingHoursInterface::class)) {
            return false;
        }

        $countryCode = $stockist->getCountry();

        if ($countryCode && !$this->isIso2CountryCode($countryCode)) {
            return false;
        }

        return true;
    }

    private function isIso2CountryCode(string $countryCode)
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

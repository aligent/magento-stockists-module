<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\Data\TradingHoursInterface;
use Aligent\Stockists\Api\StockistValidationInterface;
use Magento\Framework\Model\AbstractModel;

class StockistValidation implements StockistValidationInterface
{
    public function validate(StockistInterface $stockist) : bool
    {
        if (!$stockist->getIdentifier())
        {
            return false;
        }

        if (!$stockist->getName())
        {
            return false;
        }

        $tradingHours = $stockist->getHours();

        if ($tradingHours && !is_subclass_of($tradingHours, TradingHoursInterface::class)) {
            return false;
        }

        $countryCode = $stockist->getCountry();

        if($countryCode && !$this->isIso2CountryCode($countryCode))
        {
            return false;
        }

        return true;
    }

    private function isIso2CountryCode(string $country_code)
    {
        // TODO: Validate country code passed in as string
        return true;
    }
}
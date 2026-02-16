<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class AddressLookupSource implements OptionSourceInterface
{
    public const NONE = 'none';
    public const AUSPOST = 'auspost';
    public const GOOGLE_API = 'google_api';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::NONE,
                'label' => __('None (Stockists Search)')
            ],
            [
                'value' => self::AUSPOST,
                'label' => __('AusPost Postcode Data')
            ],
            [
                'value' => self::GOOGLE_API,
                'label' => __('Google Places API')
            ]
        ];
    }
}

<?php

declare(strict_types=1);
namespace Aligent\Stockists\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provide option values for UI
 *
 * @api
 */
class GeocodingResquestSource implements OptionSourceInterface
{

    /**
     * Constants for Geocoding Request option values
     */
    public const DEFAULT  = 'default';
    public const REGION_BIASING  = 'region';
    public const COMPONENT_FILTERING  = 'component';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::DEFAULT,
                'label' => __('Default')
            ],
            [
                'value' => self::REGION_BIASING,
                'label' => __('Region Biasing')
            ],
            [
                'value' => self::COMPONENT_FILTERING,
                'label' => __('Component Filtering')
            ]
        ];
    }
}

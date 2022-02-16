<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Ui\Component\Listing\Column;

use Aligent\Stockists\Model\OptionSource\RegionSource;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Prepare grid column region.
 */
class Region extends Column
{
    /**
     * @var RegionSource
     */
    private $regionSource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param RegionSource $regionSource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        RegionSource $regionSource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->regionSource = $regionSource;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if ($dataSource['data']['totalRecords'] > 0) {
            $options = array_column($this->regionSource->toOptionArray(), 'label', 'value');

            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['region_id'])) {
                    $item['region'] = $options[$item['region_id']] ?? '';
                }
            }
            unset($item);
        }
        return $dataSource;
    }
}

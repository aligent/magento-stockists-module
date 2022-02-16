<?php

declare(strict_types=1);
namespace Aligent\Stockists\Model\OptionSource;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provide option values for UI
 *
 * @api
 */
class RegionSource implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var null|array
     */
    private $sourceData;

    /**
     * @param CollectionFactory $regionCollectionFactory
     */
    public function __construct(CollectionFactory $regionCollectionFactory)
    {
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): ?array
    {
        if (null === $this->sourceData) {
            $regionCollection = $this->regionCollectionFactory->create();
            $this->sourceData = $regionCollection->toOptionArray();
        }
        return $this->sourceData;
    }
}

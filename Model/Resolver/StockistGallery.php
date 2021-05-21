<?php

namespace Aligent\Stockists\Model\Resolver;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class StockistGallery implements ResolverInterface
{
    /**
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;

    /**
     * StockistGallery constructor.
     * @param FilterProvider $filterProvider
     */
    public function __construct(FilterProvider $filterProvider) {
        $this->filterProvider = $filterProvider;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): string
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var \Aligent\Stockists\Api\Data\StockistInterface $stockist */
        $stockist = $value['model'];
        $gallery = $stockist->getGallery();

        return $this->filterProvider->getPageFilter()->filter($gallery);
    }
}


<?php

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class StockistLocation implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var StockistInterface $stockist */
        $stockist = $value['model'];

        $locationDetails = [
            'lat' => $stockist->getLat(),
            'lng' => $stockist->getLng()
        ];

        // If we're resolving within the context of a geo-search, we can also enrich with the distance
        // that was calculated by the repository getList
        if (isset($value['distance'])) {
            $locationDetails['distance'] = $value['distance'];
        }

        return $locationDetails;
    }
}

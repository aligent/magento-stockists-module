<?php

namespace Aligent\Stockists\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Serialize\SerializerInterface;

class StockistHours implements ResolverInterface
{
    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * StockistHours constructor.
     * @param SerializerInterface $json
     */
    public function __construct(
        SerializerInterface $json
    )
    {
        $this->json = $json;
    }
    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var \Aligent\Stockists\Api\Data\StockistInterface $stockist */
        $stockist = $value['model'];
        $tradingHours = $stockist->getHours() ?? [];
        if (isset($tradingHours['public_holidays']) && $tradingHours['public_holidays']) {
            $tradingHours['public_holidays'] = $this->json->serialize($tradingHours['public_holidays']);
        }

        return $tradingHours;
    }
}

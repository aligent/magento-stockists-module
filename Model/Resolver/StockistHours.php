<?php

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Model\TradingHours;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
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
    ) {
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

        /** @var StockistInterface $stockist */
        $stockist = $value['model'];
        $tradingHours = $stockist->getHours() ?? [];
        if (!empty($tradingHours[TradingHours::PUBLIC_HOLIDAYS])) {
            $tradingHours[TradingHours::PUBLIC_HOLIDAYS] =
                $this->json->serialize($tradingHours[TradingHours::PUBLIC_HOLIDAYS]);
        }

        return $tradingHours;
    }
}

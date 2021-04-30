<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist\DataProcessor;

use Magento\Framework\Serialize\SerializerInterface;

class TradingHours implements \Aligent\Stockists\Api\Data\StockistDataProcessorInterface
{

    /**
     * @var \Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory
     */
    protected $tradingHoursFactory;

    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * TradingHours constructor.
     * @param \Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory $tradingHoursFactory
     */
    public function __construct(
        \Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory $tradingHoursFactory,
        SerializerInterface $json
    ) {
        $this->json = $json;
        $this->tradingHoursFactory = $tradingHoursFactory;
    }

    /**
     * @param array $data
     * @return array
     */
    public function execute(array $data): array
    {
        $tradingHours = $this->tradingHoursFactory->create();
        $tradingHoursData = $data[\Aligent\Stockists\Api\Data\StockistInterface::HOURS] ? $this->json->unserialize($data[\Aligent\Stockists\Api\Data\StockistInterface::HOURS]) : "{}";
        $tradingHours->setData($tradingHoursData);
        $data[\Aligent\Stockists\Api\Data\StockistInterface::HOURS] = $tradingHours;
        return $data;
    }
}

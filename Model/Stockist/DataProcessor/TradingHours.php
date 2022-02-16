<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist\DataProcessor;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Aligent\Stockists\Api\Data\StockistDataProcessorInterface;

class TradingHours implements StockistDataProcessorInterface
{

    /**
     * @var TradingHoursInterfaceFactory
     */
    protected $tradingHoursFactory;

    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * TradingHours constructor.
     * @param TradingHoursInterfaceFactory $tradingHoursFactory
     * @param SerializerInterface $json
     */
    public function __construct(
        TradingHoursInterfaceFactory $tradingHoursFactory,
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
        $tradingHoursData = $data[StockistInterface::HOURS] ?
            $this->json->unserialize($data[StockistInterface::HOURS]) : "{}";
        $tradingHours->setData($tradingHoursData);
        $data[StockistInterface::HOURS] = $tradingHours;
        return $data;
    }
}

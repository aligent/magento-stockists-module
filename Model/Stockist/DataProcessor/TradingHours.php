<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist\DataProcessor;

class TradingHours implements \Aligent\Stockists\Api\Data\StockistDataProcessorInterface {

    /**
     * @var \Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory
     */
    protected $tradingHoursFactory;

    /**
     * TradingHours constructor.
     * @param \Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory $tradingHoursFactory
     */
    public function __construct(\Aligent\Stockists\Api\Data\TradingHoursInterfaceFactory $tradingHoursFactory)
    {
        $this->tradingHoursFactory = $tradingHoursFactory;
    }

    /**
     * @param array $data
     * @return array
     */
    public function execute(array $data): array
    {
        $tradingHours = $this->tradingHoursFactory->create();
        $data[\Aligent\Stockists\Api\Data\StockistInterface::HOURS] = $tradingHours;
        // todo: hydrade trading hours data
        return $data;
    }

}

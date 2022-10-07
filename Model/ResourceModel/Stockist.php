<?php

namespace Aligent\Stockists\Model\ResourceModel;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Model\TradingHoursFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Stockist extends AbstractDb
{

    const TABLE_NAME_STOCKIST = 'stockist';
    const ID_FIELD_NAME = 'stockist_id';

    /**
     * @var JsonSerializer
     */
    private $json;

    /**
     * @var TradingHoursFactory
     */
    private $tradingHoursFactory;

    /**
     * @param Context $context
     * @param JsonSerializer $json
     * @param TradingHoursFactory $tradingHoursFactory
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        JsonSerializer $json,
        TradingHoursFactory $tradingHoursFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->json = $json;
        $this->tradingHoursFactory = $tradingHoursFactory;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_STOCKIST, self::ID_FIELD_NAME);
    }

    /**
     * JSON-encode the opening hours before persisting it to the database.
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object): AbstractDb
    {
        $storeIds = $object->getData(StockistInterface::STORE_IDS) ?? [0];
        $object->setData(StockistInterface::STORE_IDS, implode(',', $storeIds));

        $openingHours = $object->getData(StockistInterface::HOURS) ?? null;
        if (is_array($openingHours)) {
            $object->setData(StockistInterface::HOURS, $this->json->serialize($openingHours));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Once the JSON-encoded hours data has been persisted by save(), re-hydrate the model with the
     * array structure
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object): AbstractDb
    {
        $this->handleTradingHours($object);
        return parent::_afterSave($object);
    }

    /**
     * When loading a stockist model, unserialize the JSON-encoded opening hours into it's array form
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $object): AbstractDb
    {
        $this->handleTradingHours($object);
        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    private function handleTradingHours(AbstractModel $object): void
    {
        $rawHours = $object->getData(StockistInterface::HOURS);

        if ($rawHours) {
            $tradingHours = $this->tradingHoursFactory->create();
            $unserializedHours = $this->json->unserialize($rawHours);
            $tradingHours->setData($unserializedHours);
            $object->setData(StockistInterface::HOURS, $tradingHours);
        }

        $storeIds = $object->getData(StockistInterface::STORE_IDS);
        if ($storeIds) {
            $object->setData(StockistInterface::STORE_IDS, explode(',', $storeIds));
        }
    }
}

<?php

namespace Aligent\Stockists\Model\ResourceModel;

use Aligent\Stockists\Model\Stockist as StockistModel;
use Aligent\Stockists\Model\TradingHoursFactory;
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
    private  $tradingHoursFactory;

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
    )
    {
        parent::__construct($context, $connectionName);
        $this->json = $json;
        $this->tradingHoursFactory = $tradingHoursFactory;
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_STOCKIST, self::ID_FIELD_NAME);
    }

    /**
     * JSON-encode the opening hours data before persisting it to the database.
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeIds = $object->getData(StockistModel::STORE_IDS) ?? [0];
        $object->setData(StockistModel::STORE_IDS, implode(',', $storeIds));

        $openingHours = $object->getData(StockistModel::HOURS) ? $object->getData(StockistModel::HOURS)->getData() : null;
        if (is_array($openingHours)) {
            $object->setData(StockistModel::HOURS, $this->json->serialize($openingHours));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Once the JSON-encoded hours data has been persisted by save(), re-hydrate the model with the
     * array structure
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $rawHours = $object->getData(StockistModel::HOURS);

        if ($rawHours) {
            $tradingHours = $this->tradingHoursFactory->create();
            $unserializedHours = $this->json->unserialize($rawHours);
            $tradingHours->setData($unserializedHours);
            $object->setData(StockistModel::HOURS, $tradingHours);
        }

        $object->setData(StockistModel::STORE_IDS, explode(',', $object->getData(StockistModel::STORE_IDS)));
        return parent::_afterSave($object);
    }

    /**
     * When loading a stockist model, unserialize the JSON-encoded opening hours into it's array form
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $rawHours = $object->getData(StockistModel::HOURS);

        if ($rawHours) {
            $tradingHours = $this->tradingHoursFactory->create();
            $unserializedHours = $this->json->unserialize($rawHours);
            $tradingHours->setData($unserializedHours);
            $object->setData(StockistModel::HOURS, $tradingHours);
        }

        $storeIds = (string)$object->getData(StockistModel::STORE_IDS);
        if ($storeIds !== '') {
            $object->setData(StockistModel::STORE_IDS, explode(',', $storeIds));
        }
        return parent::_afterLoad($object);
    }
}

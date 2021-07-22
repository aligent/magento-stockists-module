<?php

namespace Aligent\Stockists\Model\ResourceModel;

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
        $storeIds = $object->getData('store_ids') ?? [0];
        $object->setData('store_ids', implode(',', $storeIds));

        $openingHours = $object->getData('hours') ? $object->getData('hours')->getData() : null;
        if (is_array($openingHours)) {
            $object->setData('hours', $this->json->serialize($openingHours));
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
        $rawHours = $this->json->unserialize($object->getData('hours'));

        if ($rawHours) {
            $tradingHours = $this->tradingHoursFactory->create();
            $tradingHours->setData($rawHours);
            $object->setData('hours', $tradingHours);
        }

        $object->setData('store_ids', explode(',', $object->getData('store_ids')));
        return parent::_afterSave($object);
    }

    /**
     * When loading a stockist model, unserialize the JSON-encoded opening hours into it's array form
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $rawHours = $this->json->unserialize($object->getData('hours'));

        if ($rawHours) {
            $tradingHours = $this->tradingHoursFactory->create();
            $tradingHours->setData($rawHours);
            $object->setData('hours', $tradingHours);
        }

        $object->setData('store_ids', explode(',', $object->getData('store_ids')));
        return parent::_afterLoad($object);
    }
}

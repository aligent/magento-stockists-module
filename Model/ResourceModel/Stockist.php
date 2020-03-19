<?php

namespace Aligent\Stockists\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Stockist extends AbstractDb
{
    /**
     * @var JsonSerializer
     */
    private $json;

    /**
     * @param Context $context
     * @param JsonSerializer $json
     * @param null $connectionName
     */
    public function __construct(Context $context, JsonSerializer $json, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
        $this->json = $json;
    }

    protected function _construct()
    {
        $this->_init('stockist', 'stockist_id');
    }

    /**
     * JSON-encode the opening hours data before persisting it to the database.
     * @param \Magento\Framework\DataObject $object
     */
    public function beforeSave(\Magento\Framework\DataObject $object)
    {
        $openingHours = $object->getData('hours') ?? [];
        if (is_array($openingHours)) {
            $object->setData('hours', $this->json->serialize($openingHours));
        }
        parent::beforeSave($object);
    }

    /**
     * Once the JSON-encoded hours data has been persisted by save(), re-hydrate the model with the
     * array structure
     * @param \Magento\Framework\DataObject $object
     */
    public function afterSave(\Magento\Framework\DataObject $object)
    {
        $openingHours = $object->getData('hours');
        $object->setData('hours', $this->json->unserialize($openingHours));
        parent::afterSave($object);
    }

    /**
     * When loading a stockist model, unserialize the JSON-encoded opening hours into it's array form
     * @param \Magento\Framework\DataObject $object
     */
    public function afterLoad(\Magento\Framework\DataObject $object)
    {
        $openingHours = $object->getData('hours');
        $object->setData('hours', $this->json->unserialize($openingHours));
        parent::afterLoad($object);
    }
}
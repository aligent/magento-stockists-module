<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\ResourceModel\AuspostPostcode;

use Aligent\Stockists\Model\AuspostPostcode as AuspostPostcodeModel;
use Aligent\Stockists\Model\ResourceModel\AuspostPostcode as AuspostPostcodeResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(AuspostPostcodeModel::class, AuspostPostcodeResource::class);
    }
}

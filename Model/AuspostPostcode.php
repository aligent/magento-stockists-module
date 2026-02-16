<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Model\ResourceModel\AuspostPostcode as AuspostPostcodeResource;
use Magento\Framework\Model\AbstractModel;

class AuspostPostcode extends AbstractModel
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(AuspostPostcodeResource::class);
    }
}

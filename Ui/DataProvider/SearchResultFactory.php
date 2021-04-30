<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
namespace Aligent\Stockists\Ui\DataProvider;

class SearchResultFactory extends \Magento\Ui\DataProvider\SearchResultFactory
{
    public function __construct(
        \Aligent\Stockists\Model\Stockist\Hydrator $hydrator,
        \Magento\Framework\Api\Search\DocumentFactory $documentFactory,
        \Magento\Framework\Api\Search\SearchResultFactory $searchResultFactory,
        \Magento\Framework\Api\AttributeValueFactory $attributeValueFactory
    ) {
        parent::__construct($hydrator, $documentFactory, $searchResultFactory, $attributeValueFactory);
    }
}

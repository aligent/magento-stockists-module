<?php

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Api\StockistRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Stockists implements ResolverInterface
{

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        StockistRepositoryInterface $stockistRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->stockistRepository = $stockistRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        // TODO: Implement resolve() method.
        $searchCriteria = $this->createSearchCriteria($args);
        $results = $this->stockistRepository->getList($searchCriteria);
    }

    private function createSearchCriteria($args)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(

        )->create();
    }
}

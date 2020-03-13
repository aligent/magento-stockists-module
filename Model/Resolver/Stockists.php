<?php

namespace Aligent\Stockists\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\GeoSearchCriteriaBuilder;

class Stockists implements ResolverInterface
{

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;
    /**
     * @var GeoSearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        StockistRepositoryInterface $stockistRepository,
        GeoSearchCriteriaBuilder $searchCriteriaBuilder
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

        return [
            'total_count' => $results->getTotalCount()
        ];
    }

    /**
     * @param $args
     * @return \Aligent\Stockists\Api\GeoSearchCriteriaInterface
     */
    private function createSearchCriteria($args)
    {
        $radius = (float)$args['location']['radius'];
        $units = $args['location']['unit'] ?? 'KM';

        // Normalize if necessary - the API supports KM (default) and MILES.
        if ($units !== 'KM') {
            $radius *= 1.609;
        }

        $searchCriteria = $this->searchCriteriaBuilder->setSearchRadius($radius)->setSearchOrigin([
            'lat' => (float)$args['location']['lat'],
            'lng' => (float)$args['location']['lng']
        ])->create();

        //TODO: Attach any other filters

        return $searchCriteria;
    }
}

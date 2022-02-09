<?php

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\GeoSearchCriteriaBuilder;
use Aligent\Stockists\Helper\Data as StockistHelper;

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

    /**
     * @var FilterBuilder
     */
    private FilterBuilder $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private FilterGroupBuilder $filterGroupBuilder;

    public function __construct(
        StockistRepositoryInterface $stockistRepository,
        GeoSearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        $this->stockistRepository = $stockistRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateSearchArguments($args);

        $searchCriteria = $this->createSearchCriteria($args);

        $activeFilter = $this->filterBuilder->setField(StockistInterface::IS_ACTIVE)
            ->setValue((bool) true)
            ->setConditionType('eq')
            ->create();
        $filterGroup = $this->filterGroupBuilder->addFilter($activeFilter)->create();
        $searchCriteria->setFilterGroups([$filterGroup]);
        $results = $this->stockistRepository->getList($searchCriteria);

        $locations = array_map(function ($location) use ($args) {
            /** @var \Aligent\Stockists\Model\Stockist $location */
            $locationData = $location->getData();

            /** Attach the model to the value so nested resolvers can use it to populate complex sub-fields */
            $locationData['model'] = $location;

            // Convert distance back to miles if requested
            if (isset($args['location']['unit']) && $args['location']['unit'] === StockistHelper::DISTANCE_UNITS_MILES) {
                $locationData['distance'] = (float)$locationData['distance'] / StockistHelper::RATIO_MILES_TO_KM;
            }
            return $locationData;
        }, $results->getItems());

        return [
            'total_count' => $results->getTotalCount(),
            'locations' => $locations
        ];
    }

    /**
     * @param $args
     * @return \Aligent\Stockists\Api\GeoSearchCriteriaInterface
     */
    public function createSearchCriteria($args)
    {
        $radius = (float)$args['location']['radius'] ?? 50;
        $units = $args['location']['unit'] ?? StockistHelper::DISTANCE_UNITS_KM;

        // Normalize to KM to match the computed distance
        if ($units === StockistHelper::DISTANCE_UNITS_MILES) {
            $radius *= StockistHelper::RATIO_MILES_TO_KM;
        }

        $searchCriteria = $this->searchCriteriaBuilder->setSearchRadius($radius)->setSearchOrigin([
            'lat' => (float)$args['location']['lat'],
            'lng' => (float)$args['location']['lng']
        ])->create();

        return $searchCriteria;
    }

    /**
     * @param array $args
     * @throws GraphQlInputException
     */
    private function validateSearchArguments(array $args)
    {
        if (!isset($args['location'])) {
            throw new GraphQlInputException(__('Location request is invalid.'));
        }

        if (!isset($args['location']['lat']) || !isset($args['location']['lng']) || !isset($args['location']['radius'])) {
            throw new GraphQlInputException(__('Invalid search coordinates provided.'));
        }
    }
}

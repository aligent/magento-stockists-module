<?php

namespace Aligent\Stockists\Model\Resolver;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
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
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

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
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $args = $this->defaultSearchArguments($args);
        $pageSize = $args['pageSize'] ?? 0;
        $currentPage = $args['currentPage'] ?? 0;

        $searchCriteria = $this->createSearchCriteria($args);
        $results = $this->stockistRepository->getList($searchCriteria);

        $locations = [];
        foreach ($results->getItems() as $location) {
            /** @var \Aligent\Stockists\Model\Stockist $location */
            $locationData = $location->getData();

            /** Attach the model to the value so nested resolvers can use it to populate complex subfields */
            $locationData['model'] = $location;

            // Convert distance back to miles if requested
            if ($args['location']['unit'] === StockistHelper::DISTANCE_UNITS_MILES) {
                $locationData['distance'] = (float)$locationData['distance'] / StockistHelper::RATIO_MILES_TO_KM;
            }
            $locations[] = $locationData;
        }

        $totalStockists = $results->getTotalCount();
        $totalPages = 0;
        if ($pageSize && $currentPage) {
            $locations = array_chunk($locations, $pageSize, true);
            $totalPages = count($locations);

            if ($currentPage <= $totalPages) {
                $locations = $locations[$currentPage - 1];
            } else {
                $locations = [];
            }
        }

        return [
            'total_count' => $results->getTotalCount(),
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $currentPage,
                'total_pages'=> $totalPages
            ],
            'locations' => $locations
        ];
    }

    /**
     * @param array $args
     * @return GeoSearchCriteriaInterface
     */
    public function createSearchCriteria(array $args): GeoSearchCriteriaInterface
    {
        $radius = $args['location']['radius'];
        $units = $args['location']['unit'];

        // Normalize to KM to match the computed distance
        if ($units === StockistHelper::DISTANCE_UNITS_MILES) {
            $radius *= StockistHelper::RATIO_MILES_TO_KM;
        }

        $this->searchCriteriaBuilder->setSearchRadius($radius);
        $this->searchCriteriaBuilder->setSearchOrigin(
            [
                'lat' => $args['location']['lat'],
                'lng' => $args['location']['lng']
            ]
        );

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $activeFilter = $this->filterBuilder->setField(StockistInterface::IS_ACTIVE)
            ->setValue(true)
            ->setConditionType('eq')
            ->create();
        $filterGroup = $this->filterGroupBuilder->addFilter($activeFilter)->create();
        $searchCriteria->setFilterGroups([$filterGroup]);

        return $searchCriteria;
    }

    /**
     * @param array $args
     * @return array
     */
    private function defaultSearchArguments(array $args): array
    {
        // default radius and units if not present
        $args['location']['radius'] = $args['location']['radius'] ?? 50.0;
        $args['location']['unit'] = $args['location']['unit'] ?? StockistHelper::DISTANCE_UNITS_KM;
        return $args;
    }
}

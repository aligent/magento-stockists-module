<?php

namespace Aligent\Stockists\Model;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist as StockistResource;
use Aligent\Stockists\Model\StockistFactory;
use Aligent\Stockists\Model\ResourceModel\Stockist\CollectionFactory as StockistCollectionFactory;
use Aligent\Stockists\Api\Data\StockistSearchResultsInterfaceFactory as SearchResultsFactory;
use Aligent\Stockists\Model\SearchCriteria\DistanceProcessor;

class StockistRepository implements StockistRepositoryInterface
{
    /**
     * @var \Aligent\Stockists\Model\StockistFactory
     */
    private $stockistFactory;
    /**
     * @var StockistResource
     */
    private $stockistResource;
    /**
     * @var StockistCollectionFactory
     */
    private $stockistCollectionFactory;
    /**
     * @var SearchResultsFactory
     */
    private $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var DistanceProcessor
     */
    private $distanceProcessor;

    /**
     * @param \Aligent\Stockists\Model\StockistFactory $stockistFactory
     * @param StockistResource $stockistResource
     * @param StockistCollectionFactory $stockistCollectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DistanceProcessor $distanceProcessor
     */
    public function __construct(
        StockistFactory $stockistFactory,
        StockistResource $stockistResource,
        StockistCollectionFactory $stockistCollectionFactory,
        SearchResultsFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        DistanceProcessor $distanceProcessor
    ) {
        $this->stockistFactory = $stockistFactory;
        $this->stockistResource = $stockistResource;
        $this->stockistCollectionFactory = $stockistCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->distanceProcessor = $distanceProcessor;
    }

    /**
     * @param $id
     * @return StockistInterface
     * @throws NoSuchEntityException
     */
    public function get($id): StockistInterface
    {
        /** @var \Aligent\Stockists\Model\Stockist $stockist */
        $stockist = $this->stockistFactory->create();
        $this->stockistResource->load($stockist, $id);
        if (!$stockist->getId()) {
            throw new NoSuchEntityException(__('Form submission with ID %1 does not exist', $id));
        }
        return $stockist;
    }

    public function getList(GeoSearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Aligent\Stockists\Model\ResourceModel\Stockist\Collection $collection */
        $collection = $this->stockistCollectionFactory->create();

        $this->distanceProcessor->process($searchCriteria, $collection);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Aligent\Stockists\Api\Data\StockistSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSizeFromGeoSearch($searchCriteria));
        return $searchResults;
    }

    public function save(StockistInterface $formSubmission): StockistInterface
    {
        $this->stockistResource->save($formSubmission);
        return $formSubmission;
    }
}
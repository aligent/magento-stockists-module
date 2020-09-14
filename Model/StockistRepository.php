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
use Magento\Framework\Model\AbstractModel;

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
     * @var StockistValidation
     */
    private $stockistValidation;


    /**
     * @param \Aligent\Stockists\Model\StockistFactory $stockistFactory
     * @param StockistResource $stockistResource
     * @param StockistCollectionFactory $stockistCollectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DistanceProcessor $distanceProcessor
     * @param StockistValidation $stockistValidation
     */
    public function __construct(
        StockistFactory $stockistFactory,
        StockistResource $stockistResource,
        StockistCollectionFactory $stockistCollectionFactory,
        SearchResultsFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        DistanceProcessor $distanceProcessor,
        StockistValidation $stockistValidation
    ) {
        $this->stockistFactory = $stockistFactory;
        $this->stockistResource = $stockistResource;
        $this->stockistCollectionFactory = $stockistCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->distanceProcessor = $distanceProcessor;
        $this->stockistValidation = $stockistValidation;
    }

    /**
     * @param string $identifier
     * @return StockistInterface
     * @throws NoSuchEntityException
     */
    public function get(string $identifier): StockistInterface
    {
        /** @var \Aligent\Stockists\Model\Stockist $stockist */
        $stockist = $this->stockistFactory->create();
        $this->stockistResource->load($stockist, $identifier, 'identifier');
        if (!$stockist->getId()) {
            throw new NoSuchEntityException(__('Stockist "%1" does not exist', $identifier));
        }
        return $stockist;
    }

    /**
     * @param GeoSearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
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

    /**
     * @param StockistInterface $stockist
     * @return StockistInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(StockistInterface $stockist): StockistInterface
    {
        $validationResult = $this->stockistValidation->validate($stockist);

        if (!$validationResult) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Invalid stockist data: %1', implode(',', $validationResult))
            );
        } else {
            try {
                $existingStockist = $this->get($stockist->getIdentifier());
                $stockist->setStockistId($existingStockist->getId());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(
                    __('Unknow error of getting stockist entity: %1', $e->getMessage())
                );
            }
            $this->stockistResource->save($stockist);
        }

        return $stockist;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getById(int $id): StockistInterface
    {
        /** @var \Aligent\Stockists\Model\Stockist $stockist */
        $stockist = $this->stockistFactory->create();
        $this->stockistResource->load($stockist, $id);
        if (!$stockist->getId()) {
            throw new NoSuchEntityException(__('Stockist with ID %1 does not exist', $id));
        }
        return $stockist;
    }

    /**
     * @inheritDoc
     */
    public function delete(StockistInterface $stockist): bool
    {
        $id = $stockist->getIdentifier();

        if (!is_subclass_of($stockist, AbstractModel::class)) {
            return false;
        }

        try {
            $this->stockistResource->delete($stockist);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('The "%1" stockist couldn\'t be removed.', $id)
            );
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteByIdentifier(string $identifier): bool
    {
        $stockist = $this->get($identifier);
        return $this->delete($stockist);
    }

    /**
     * @inheritDoc
     */
    public function deleteById(string $stockistId): bool
    {
        $stockist = $this->getById($stockistId);
        $this->stockistResource->delete($stockist);
        return true;
    }
}

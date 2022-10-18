<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\Data\StockistSearchResultsInterface;
use Aligent\Stockists\Model\ResourceModel\Stockist\Collection as StockistCollection;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
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
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\AbstractModel;

class StockistRepository implements StockistRepositoryInterface
{
    /**
     * @var StockistFactory
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
     * @param StockistFactory $stockistFactory
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
        /** @var Stockist $stockist */
        $stockist = $this->stockistFactory->create();
        $this->stockistResource->load($stockist, $identifier, StockistInterface::IDENTIFIER);
        if (!$stockist->getId()) {
            throw new NoSuchEntityException(__('Stockist "%1" does not exist', $identifier));
        }
        return $stockist;
    }

    /**
     * @param string $urlKey
     * @return StockistInterface
     * @throws NoSuchEntityException
     */
    public function getByUrlKey(string $urlKey): StockistInterface
    {
        /** @var Stockist $stockist */
        $stockist = $this->stockistFactory->create();
        $this->stockistResource->load($stockist, $urlKey, StockistInterface::URL_KEY);
        if (!$stockist->getId()) {
            throw new NoSuchEntityException(__('Stockist "%1" does not exist', $urlKey));
        }

        return $stockist;
    }

    /**
     * @param GeoSearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(GeoSearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var StockistCollection $collection */
        $collection = $this->stockistCollectionFactory->create();

        $this->distanceProcessor->process($searchCriteria, $collection);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var StockistSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSizeFromGeoSearch($searchCriteria));
        return $searchResults;
    }

    /**
     * @param StockistInterface $stockist
     * @return StockistInterface
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(StockistInterface $stockist): StockistInterface
    {
        // New non-nullable field which might not be considered in older API
        if ($stockist->getIsActive() === null) {
            $stockist->setIsActive(true);
        }
        $validationErrors = $this->stockistValidation->validate($stockist);

        if (!empty($validationErrors)) {
            throw new CouldNotSaveException(
                __('Invalid stockist data: %1', implode(',', $validationErrors))
            );
        } else {
            try {
                if ($stockist->getStockistId()) {
                    $existingStockist = $this->getById($stockist->getStockistId());
                    $stockist->setStockistId($existingStockist->getId());
                } elseif ($stockist->getIdentifier()) {
                    $existingStockist = $this->get($stockist->getIdentifier());
                    $stockist->setStockistId($existingStockist->getId());
                }
            } catch (NoSuchEntityException $e) {
                // Want to check whether the stockist exists in an attempt to update an existing one before
                // saving a new one. If it doesn't exist there is no need to do anything here.
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
        /** @var Stockist $stockist */
        $stockist = $this->stockistFactory->create();
        $this->stockistResource->load($stockist, $id);
        if (!$stockist->getId()) {
            throw new NoSuchEntityException(__('Stockist with ID %1 does not exist', $id));
        }
        return $stockist;
    }

    /**
     * @inheritDoc
     * @throws CouldNotSaveException
     * @throws StateException
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
            throw new StateException(
                __("The '%1' stockist couldn't be removed.", $id)
            );
        }

        return true;
    }

    /**
     * @inheritDoc
     * @param string $identifier
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteByIdentifier(string $identifier): bool
    {
        $stockist = $this->get($identifier);
        return $this->delete($stockist);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function deleteById(string $stockistId): bool
    {
        $stockist = $this->getById($stockistId);
        $this->stockistResource->delete($stockist);
        return true;
    }
}

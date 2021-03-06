<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist;

use Aligent\Stockists\Api\Data\StockistDataProcessorInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\MapperPool;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Validation\ValidationException;

/**
 * Class StockistHydrator
 * @api
 */
class Hydrator implements \Magento\Framework\EntityManager\HydratorInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StockistDataProcessorInterface[]|array
     */
    protected $dataProcessors;

    /**
     * @var MapperPool
     */
    protected $mapperPool;

    /**
     * @var TypeResolver
     */
    protected $typeResolver;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var Region
     */
    protected $regionResource;

    /**
     * StockistHydrator constructor.
     * @param DataObjectProcessor $dataObjectProcessor
     * @param MapperPool $mapperPool
     * @param TypeResolver $typeResolver
     * @param DataObjectHelper $dataObjectHelper
     * @param StockistDataProcessorInterface[] $dataProcessors
     * @param RegionFactory $regionFactory
     * @param Region $regionResource
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        MapperPool $mapperPool,
        TypeResolver $typeResolver,
        DataObjectHelper $dataObjectHelper,
        RegionFactory $regionFactory,
        Region $regionResource,
        array $dataProcessors = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->typeResolver = $typeResolver;
        $this->mapperPool = $mapperPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->regionFactory = $regionFactory;
        $this->regionResource = $regionResource;
        $this->dataProcessors = $dataProcessors;
    }

    /**
     * @param object $entity
     * @return array
     * @throws \Exception
     */
    public function extract($entity)
    {
        $entityType = $this->typeResolver->resolve($entity);
        $data = $this->dataObjectProcessor->buildOutputDataArray($entity, $entityType);
        $mapper = $this->mapperPool->getMapper($entityType);
        return $mapper->entityToDatabase($entityType, $data);
    }

    /**
     * @param StockistInterface $stockist
     * @param array $data
     * @return StockistInterface
     * @throws ValidationException
     */
    public function hydrate($stockist, array $data): StockistInterface
    {
        if (empty($data[StockistInterface::STOCKIST_ID])) {
            unset($data[StockistInterface::STOCKIST_ID]);
        }
        if (isset($data[StockistInterface::IS_ACTIVE]) && $data[StockistInterface::IS_ACTIVE] === 'true') {
            $data[StockistInterface::IS_ACTIVE] = 1;
        } else {
            $data[StockistInterface::IS_ACTIVE] = 0;
        }

        if (isset($data[StockistInterface::ALLOW_STORE_DELIVERY]) && $data[StockistInterface::ALLOW_STORE_DELIVERY] === 'true') {
            $data[StockistInterface::ALLOW_STORE_DELIVERY] = 1;
        } else {
            $data[StockistInterface::ALLOW_STORE_DELIVERY] = 0;
        }

        if (isset($data[StockistInterface::COUNTRY_ID])) {
            // possible todo: convert to full name?
            $data[StockistInterface::COUNTRY] = $data[StockistInterface::COUNTRY_ID];
        }

        if (isset($data[StockistInterface::REGION_ID])) {
            if (!$data[StockistInterface::REGION_ID]) {
                $data[StockistInterface::REGION] = "";
            } else {
                $region = $this->regionFactory->create();
                $this->regionResource->load($region, $data[StockistInterface::REGION_ID], 'region_id');
                $data[StockistInterface::REGION] = $region->getName();
            }
        }

        foreach ($this->dataProcessors as $dataProcessor) {
            if ($dataProcessor instanceof StockistDataProcessorInterface) {
                $data = $dataProcessor->execute($data);
            }
        }
        try {
            $this->dataObjectHelper->populateWithArray($stockist, $data, StockistInterface::class);
        } catch (\Exception $e) {
            throw new ValidationException(__($e->getMessage()));
        }
        return $stockist;
    }
}

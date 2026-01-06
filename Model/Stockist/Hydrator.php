<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist;

use Aligent\Stockists\Api\Data\StockistDataProcessorInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\MapperPool;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Validation\ValidationException;

class Hydrator implements HydratorInterface
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
    public function extract($entity): array
    {
        $entityType = $this->typeResolver->resolve($entity);
        $data = $this->dataObjectProcessor->buildOutputDataArray($entity, $entityType);
        $mapper = $this->mapperPool->getMapper($entityType);
        return $mapper->entityToDatabase($entityType, $data);
    }

    /**
     * @param StockistInterface $entity
     * @param array $data
     * @return StockistInterface
     * @throws ValidationException
     */
    public function hydrate($entity, array $data): StockistInterface
    {
        if (empty($data[StockistInterface::STOCKIST_ID])) {
            unset($data[StockistInterface::STOCKIST_ID]);
        }

        if (isset($data[StockistInterface::IS_ACTIVE])) {
            $data[StockistInterface::IS_ACTIVE] = $data[StockistInterface::IS_ACTIVE] === 'true';
        }

        if (isset($data[StockistInterface::COUNTRY_ID])) {
            $data[StockistInterface::COUNTRY] = $data[StockistInterface::COUNTRY_ID];
        }

        if (!empty($data[StockistInterface::REGION_ID])) {
            $region = $this->regionFactory->create();
            $this->regionResource->load($region, $data[StockistInterface::REGION_ID], 'region_id');
            $data[StockistInterface::REGION] = $region->getName();
        }

        foreach ($this->dataProcessors as $dataProcessor) {
            if ($dataProcessor instanceof StockistDataProcessorInterface) {
                $data = $dataProcessor->execute($data);
            }
        }
        try {
            $this->dataObjectHelper->populateWithArray($entity, $data, StockistInterface::class);
        } catch (\Exception $e) {
            throw new ValidationException(__($e->getMessage()));
        }
        return $entity;
    }
}

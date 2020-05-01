<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Stockist;

/**
 * Class StockistHydrator
 * @api
 */
class Hydrator implements \Magento\Framework\EntityManager\HydratorInterface {

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Aligent\Stockists\Api\Data\StockistDataProcessorInterface[]|array
     */
    protected $dataProcessors;

    /**
     * @var \Magento\Framework\EntityManager\MapperPool
     */
    protected $mapperPool;

    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    protected $typeResolver;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * StockistHydrator constructor.
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Aligent\Stockists\Api\Data\StockistDataProcessorInterface[] $dataProcessors
     */
    public function __construct(
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\EntityManager\MapperPool $mapperPool,
        \Magento\Framework\EntityManager\TypeResolver $typeResolver,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        array $dataProcessors = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->typeResolver = $typeResolver;
        $this->mapperPool = $mapperPool;
        $this->dataObjectHelper = $dataObjectHelper;
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
        $result = $mapper->entityToDatabase($entityType, $data);
        return $result;
    }

    /**
     * @param \Aligent\Stockists\Api\Data\StockistInterface $stockist
     * @param array $data
     * @return \Aligent\Stockists\Api\Data\StockistInterface
     */
    public function hydrate($stockist, array $data): \Aligent\Stockists\Api\Data\StockistInterface
    {
        if (empty($data[\Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID])) {
            unset($data[\Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID]);
        }

        if (empty($data[\Aligent\Stockists\Api\Data\StockistInterface::COUNTRY]) && !empty($data['country_id'])) {
            // possible todo: convert to full name?
            $data[\Aligent\Stockists\Api\Data\StockistInterface::COUNTRY] = $data['country_id'];
        }

        foreach ($this->dataProcessors as $dataProcessor) {
            if ($dataProcessor instanceof \Aligent\Stockists\Api\Data\StockistDataProcessorInterface) {
                $data = $dataProcessor->execute($data);
            }
        }
        try {
            $this->dataObjectHelper->populateWithArray($stockist, $data, \Aligent\Stockists\Api\Data\StockistInterface::class);
        } catch (\TypeError $e) {
            throw new \Magento\Framework\Validation\ValidationException(\__($e->getMessage()));
        }
        return $stockist;
    }

}

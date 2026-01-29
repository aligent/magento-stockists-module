<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Command;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\Data\StockistInterfaceFactory;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\Import\StockistConvert;
use Aligent\Stockists\Model\Stockist\Hydrator;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Append implements CommandInterface
{
    /**
     * @param StockistInterfaceFactory $stockistFactory
     * @param StockistRepositoryInterface $stockistRepository
     * @param Hydrator $hydrator
     * @param StockistConvert $stockistConvert
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly StockistInterfaceFactory $stockistFactory,
        private readonly StockistRepositoryInterface $stockistRepository,
        private readonly Hydrator $hydrator,
        private readonly StockistConvert $stockistConvert,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritdoc
     */
    public function execute(array $bunch): array
    {
        $created = 0;
        $updated = 0;

        foreach ($bunch as $rowData) {
            try {
                $stockistData = $this->stockistConvert->convert($rowData);
                $identifier = $stockistData['identifier'] ?? null;

                if (!$identifier) {
                    continue;
                }

                $stockist = $this->getExistingStockist($identifier);
                $isNew = ($stockist === null);

                if ($isNew) {
                    $stockist = $this->stockistFactory->create();
                }

                $stockist = $this->hydrator->hydrate($stockist, $stockistData);
                $this->stockistRepository->save($stockist);

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Exception $e) {
                $this->logger->error('Stockist import error: ' . $e->getMessage(), [
                    'row_data' => $rowData
                ]);
            }
        }

        return ['created' => $created, 'updated' => $updated, 'deleted' => 0];
    }

    /**
     * Get existing stockist by identifier
     *
     * @param string $identifier
     * @return StockistInterface|null
     */
    private function getExistingStockist(string $identifier): ?StockistInterface
    {
        try {
            return $this->stockistRepository->get($identifier);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}

<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Command;

use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\Import\Stockists;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class Delete implements CommandInterface
{
    /**
     * @param StockistRepositoryInterface $stockistRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly StockistRepositoryInterface $stockistRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritdoc
     */
    public function execute(array $bunch): array
    {
        $deleted = 0;

        foreach ($bunch as $rowData) {
            try {
                $identifier = $rowData[Stockists::COL_IDENTIFIER] ?? null;

                if (!$identifier) {
                    continue;
                }

                $stockist = $this->getExistingStockist(trim($identifier));

                if ($stockist !== null) {
                    $this->stockistRepository->delete($stockist);
                    $deleted++;
                }
            } catch (\Exception $e) {
                $this->logger->error('Stockist delete error: ' . $e->getMessage(), [
                    'row_data' => $rowData
                ]);
            }
        }

        return ['created' => 0, 'updated' => 0, 'deleted' => $deleted];
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

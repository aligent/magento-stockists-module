<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import\Command;

use Aligent\Stockists\Model\Import\AuspostPostcodes;
use Aligent\Stockists\Model\ResourceModel\AuspostPostcode as AuspostPostcodeResource;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class AuspostDelete implements CommandInterface
{
    /**
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritdoc
     */
    public function execute(array $bunch): array
    {
        $deleted = 0;
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(AuspostPostcodeResource::TABLE_NAME);

        foreach ($bunch as $rowData) {
            try {
                $pcode = trim((string)($rowData[AuspostPostcodes::COL_PCODE] ?? ''));
                $locality = trim((string)($rowData[AuspostPostcodes::COL_LOCALITY] ?? ''));
                $state = trim((string)($rowData[AuspostPostcodes::COL_STATE] ?? ''));

                if (empty($pcode) || empty($locality) || empty($state)) {
                    continue;
                }

                $deleted += $connection->delete($tableName, [
                    'pcode = ?' => $pcode,
                    'locality = ?' => $locality,
                    'state = ?' => $state,
                ]);
            } catch (\Exception $e) {
                $this->logger->error('AusPost postcode delete error: ' . $e->getMessage(), [
                    'row_data' => $rowData
                ]);
            }
        }

        return ['created' => 0, 'updated' => 0, 'deleted' => $deleted];
    }
}

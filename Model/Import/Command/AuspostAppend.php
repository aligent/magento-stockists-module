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

class AuspostAppend implements CommandInterface
{
    private const BATCH_SIZE = 500;

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
     * Columns to update on duplicate key
     */
    private const UPDATE_COLUMNS = ['comments', 'category', 'longitude', 'latitude'];

    /**
     * @inheritdoc
     */
    public function execute(array $bunch): array
    {
        $created = 0;
        $updated = 0;
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(AuspostPostcodeResource::TABLE_NAME);

        $rows = [];
        foreach ($bunch as $rowData) {
            $rows[] = $this->mapRow($rowData);

            if (count($rows) >= self::BATCH_SIZE) {
                $result = $this->insertBatch($connection, $tableName, $rows);
                $created += $result['created'];
                $updated += $result['updated'];
                $rows = [];
            }
        }

        if (!empty($rows)) {
            $result = $this->insertBatch($connection, $tableName, $rows);
            $created += $result['created'];
            $updated += $result['updated'];
        }

        return ['created' => $created, 'updated' => $updated, 'deleted' => 0];
    }

    /**
     * Map CSV row data to database columns
     *
     * @param array $rowData
     * @return array
     */
    private function mapRow(array $rowData): array
    {
        return [
            'pcode' => trim((string)($rowData[AuspostPostcodes::COL_PCODE] ?? '')),
            'locality' => trim((string)($rowData[AuspostPostcodes::COL_LOCALITY] ?? '')),
            'state' => trim((string)($rowData[AuspostPostcodes::COL_STATE] ?? '')),
            'comments' => trim((string)($rowData[AuspostPostcodes::COL_COMMENTS] ?? '')) ?: null,
            'category' => trim((string)($rowData[AuspostPostcodes::COL_CATEGORY] ?? '')) ?: null,
            'longitude' => is_numeric($rowData[AuspostPostcodes::COL_LONGITUDE] ?? null)
                ? (float)$rowData[AuspostPostcodes::COL_LONGITUDE]
                : null,
            'latitude' => is_numeric($rowData[AuspostPostcodes::COL_LATITUDE] ?? null)
                ? (float)$rowData[AuspostPostcodes::COL_LATITUDE]
                : null,
        ];
    }

    /**
     * Insert batch of rows, updating on duplicate (pcode, locality, state)
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param string $tableName
     * @param array $rows
     * @return array{created: int, updated: int}
     */
    private function insertBatch($connection, string $tableName, array $rows): array
    {
        try {
            $rowCount = count($rows);
            $affectedRows = $connection->insertOnDuplicate($tableName, $rows, self::UPDATE_COLUMNS);
            // insertOnDuplicate returns affected rows: 1 per insert, 2 per update
            $updated = $affectedRows - $rowCount;
            if ($updated < 0) {
                $updated = 0;
            }
            $created = $rowCount - $updated;

            return ['created' => $created, 'updated' => $updated];
        } catch (\Exception $e) {
            $this->logger->error('AusPost postcode import error: ' . $e->getMessage());
            return ['created' => 0, 'updated' => 0];
        }
    }
}

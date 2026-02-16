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

class AuspostReplace implements CommandInterface
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
     * @inheritdoc
     */
    public function execute(array $bunch): array
    {
        $created = 0;
        $updated = 0;
        $deleted = 0;
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(AuspostPostcodeResource::TABLE_NAME);

        $rows = [];
        foreach ($bunch as $rowData) {
            $rows[] = $this->mapRow($rowData);

            if (count($rows) >= self::BATCH_SIZE) {
                $result = $this->replaceBatch($connection, $tableName, $rows);
                $created += $result['created'];
                $updated += $result['updated'];
                $deleted += $result['deleted'];
                $rows = [];
            }
        }

        if (!empty($rows)) {
            $result = $this->replaceBatch($connection, $tableName, $rows);
            $created += $result['created'];
            $updated += $result['updated'];
            $deleted += $result['deleted'];
        }

        return ['created' => $created, 'updated' => $updated, 'deleted' => $deleted];
    }

    /**
     * Delete matching records then insert replacements
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param string $tableName
     * @param array $rows
     * @return array{created: int, updated: int, deleted: int}
     */
    private function replaceBatch($connection, string $tableName, array $rows): array
    {
        try {
            // Build WHERE conditions for all rows in this batch
            $conditions = [];
            foreach ($rows as $row) {
                $conditions[] = $connection->quoteInto('(pcode = ?', $row['pcode'])
                    . $connection->quoteInto(' AND locality = ?', $row['locality'])
                    . $connection->quoteInto(' AND state = ?)', $row['state']);
            }

            $deleted = $connection->delete($tableName, implode(' OR ', $conditions));
            $connection->insertMultiple($tableName, $rows);

            // Rows that were deleted and re-inserted count as updated
            $created = count($rows) - $deleted;
            if ($created < 0) {
                $created = 0;
            }
            $updated = min($deleted, count($rows));

            return ['created' => $created, 'updated' => $updated, 'deleted' => $deleted];
        } catch (\Exception $e) {
            $this->logger->error('AusPost postcode replace error: ' . $e->getMessage());
            return ['created' => 0, 'updated' => 0, 'deleted' => 0];
        }
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
}

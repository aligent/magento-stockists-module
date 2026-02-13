<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data;
use Magento\ImportExport\Helper\Data as ImportExportData;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use Magento\ImportExport\Model\ResourceModel\Helper;

class AuspostPostcodes extends AbstractEntity
{
    public const ENTITY_CODE = 'auspost_postcodes';

    public const COL_PCODE = 'pcode';
    public const COL_LOCALITY = 'locality';
    public const COL_STATE = 'state';
    public const COL_COMMENTS = 'comments';
    public const COL_CATEGORY = 'category';
    public const COL_LONGITUDE = 'longitude';
    public const COL_LATITUDE = 'latitude';

    private const ERROR_DUPLICATE_ROW = 'duplicateRow';

    /**
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var string[]
     */
    protected $validColumnNames = [
        self::COL_PCODE,
        self::COL_LOCALITY,
        self::COL_STATE,
        self::COL_COMMENTS,
        self::COL_CATEGORY,
        self::COL_LONGITUDE,
        self::COL_LATITUDE,
    ];

    /**
     * @var string
     */
    protected $masterAttributeCode = self::COL_PCODE;

    /**
     * @var array<string, true>
     */
    private array $seenKeys = [];

    /**
     * @param Data $jsonHelper
     * @param ImportExportData $importExportData
     * @param ImportData $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param array $commands
     */
    public function __construct(
        Data $jsonHelper,
        ImportExportData $importExportData,
        ImportData $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        private readonly array $commands = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
        $this->errorAggregator = $errorAggregator;
        $this->addMessageTemplate(
            self::ERROR_DUPLICATE_ROW,
            'Duplicate combination of pcode, locality and state found in row'
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntityTypeCode(): string
    {
        return self::ENTITY_CODE;
    }

    /**
     * @inheritdoc
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        $errors = [];

        if (empty($rowData[self::COL_PCODE])) {
            $errors[] = 'PCode is required';
        }

        if (empty($rowData[self::COL_LOCALITY])) {
            $errors[] = 'Locality is required';
        }

        if (empty($rowData[self::COL_STATE])) {
            $errors[] = 'State is required';
        }

        // Check for duplicate pcode+locality+state within the CSV
        if (empty($errors)) {
            $uniqueKey = strtoupper(
                trim((string)$rowData[self::COL_PCODE]) . '|'
                . trim((string)$rowData[self::COL_LOCALITY]) . '|'
                . trim((string)$rowData[self::COL_STATE])
            );

            if (isset($this->seenKeys[$uniqueKey])) {
                $errors[] = self::ERROR_DUPLICATE_ROW;
            } else {
                $this->seenKeys[$uniqueKey] = true;
            }
        }

        foreach ($errors as $error) {
            $this->addRowError($error, $rowNum);
        }

        return count($errors) === 0;
    }

    /**
     * @inheritdoc
     */
    protected function _importData(): bool
    {
        $behavior = $this->getBehavior();
        $command = $this->commands[$behavior] ?? null;

        if ($command === null) {
            return false;
        }

        $this->countItemsCreated = 0;
        $this->countItemsUpdated = 0;
        $this->countItemsDeleted = 0;

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $validRows = [];

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->validateRow($rowData, $rowNum)) {
                    $validRows[] = $rowData;
                }
            }

            if (!empty($validRows)) {
                $result = $command->execute($validRows);
                $this->countItemsCreated += $result['created'] ?? 0;
                $this->countItemsUpdated += $result['updated'] ?? 0;
                $this->countItemsDeleted += $result['deleted'] ?? 0;
            }
        }

        return true;
    }

    /**
     * Get valid column names
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }
}

<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Model\Import;

use Aligent\Stockists\Model\Import\Validator\ValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data;
use Magento\ImportExport\Helper\Data as ImportExportData;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportData;
use Magento\ImportExport\Model\ResourceModel\Helper;

class Stockists extends AbstractEntity
{
    public const ENTITY_CODE = 'stockists';

    public const COL_IDENTIFIER = 'identifier';
    public const COL_NAME = 'name';
    public const COL_URL_KEY = 'url_key';
    public const COL_IS_ACTIVE = 'is_active';
    public const COL_DESCRIPTION = 'description';
    public const COL_STREET = 'street';
    public const COL_CITY = 'city';
    public const COL_POSTCODE = 'postcode';
    public const COL_REGION = 'region';
    public const COL_REGION_CODE = 'region_code';
    public const COL_COUNTRY = 'country';
    public const COL_PHONE = 'phone';
    public const COL_EMAIL = 'email';
    public const COL_FAX = 'fax';
    public const COL_LAT = 'lat';
    public const COL_LNG = 'lng';
    public const COL_HOURS = 'hours';
    public const COL_STORE_IDS = 'store_ids';
    public const COL_META_TITLE = 'meta_title';
    public const COL_META_KEYWORDS = 'meta_keywords';
    public const COL_META_DESCRIPTION = 'meta_description';

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
        self::COL_IDENTIFIER,
        self::COL_NAME,
        self::COL_URL_KEY,
        self::COL_IS_ACTIVE,
        self::COL_DESCRIPTION,
        self::COL_STREET,
        self::COL_CITY,
        self::COL_POSTCODE,
        self::COL_REGION,
        self::COL_REGION_CODE,
        self::COL_COUNTRY,
        self::COL_PHONE,
        self::COL_EMAIL,
        self::COL_FAX,
        self::COL_LAT,
        self::COL_LNG,
        self::COL_HOURS,
        self::COL_STORE_IDS,
        self::COL_META_TITLE,
        self::COL_META_KEYWORDS,
        self::COL_META_DESCRIPTION,
    ];

    /**
     * @var string
     */
    protected $masterAttributeCode = self::COL_IDENTIFIER;

    /**
     * @param Data $jsonHelper
     * @param ImportExportData $importExportData
     * @param ImportData $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param ValidatorInterface $validator
     * @param array $commands
     */
    public function __construct(
        Data $jsonHelper,
        ImportExportData $importExportData,
        ImportData $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        private readonly ValidatorInterface $validator,
        private readonly array $commands = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
        $this->errorAggregator = $errorAggregator;
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
        $errors = $this->validator->validate($rowData, $rowNum);

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

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $validRows = [];

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->validateRow($rowData, $rowNum)) {
                    $validRows[] = $rowData;
                }
            }

            if (!empty($validRows)) {
                $command->execute($validRows);
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

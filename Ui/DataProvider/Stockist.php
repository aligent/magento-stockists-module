<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
namespace Aligent\Stockists\Ui\DataProvider;

use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Stockist extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{

    const STOCKIST_FORM_NAME = 'stockist_form_data_stockist';


    /**
     * @var \Aligent\Stockists\Api\StockistRepositoryInterface
     */
    protected $stockistRepository;

    /**
     * @var \Magento\Ui\DataProvider\SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var SerializerInterface
     */
    private $json;

    public function __construct(
        \Aligent\Stockists\Ui\DataProvider\SearchResultFactory $searchResultFactory,
        \Aligent\Stockists\Api\StockistRepositoryInterface $stockistRepository,
        \Magento\Backend\Model\Session $session,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Aligent\Stockists\Model\GeoSearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        SerializerInterface $json,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->session = $session;
        $this->json = $json;
        $this->stockistRepository = $stockistRepository;
        $this->searchResultFactory = $searchResultFactory;
    }


    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as &$item) {
            // todo: process hours output
            $item[StockistInterface::HOURS] = $this->json->serialize($item[StockistInterface::HOURS]);
            $item[StockistInterface::STORE_IDS] = \implode(',', $item[StockistInterface::STORE_IDS]);
            $item[StockistInterface::COUNTRY_ID] = $item[StockistInterface::COUNTRY];
            $item[StockistInterface::ALLOW_STORE_DELIVERY] = isset($item[StockistInterface::ALLOW_STORE_DELIVERY])
                && $item[StockistInterface::ALLOW_STORE_DELIVERY] === "1";
            $item[StockistInterface::IS_ACTIVE] = isset($item[StockistInterface::IS_ACTIVE])
                && $item[StockistInterface::IS_ACTIVE] === "1";

        }
        if (self::STOCKIST_FORM_NAME === $this->name) {
            // It is need for support of several fieldsets.
            // For details see \Magento\Ui\Component\Form::getDataSourceData
            if ($data['totalRecords'] > 0) {
                $stockistId = $data['items'][0][\Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID];
                $stockistGenenalData = $data['items'][0];
                $dataForSingle[$stockistId] = [
                    'general' => $stockistGenenalData,
                ];
                return $dataForSingle;
            }
            $sessionData = $this->session->getSourceFormData(true);
            if (null !== $sessionData) {
                // For details see \Magento\Ui\Component\Form::getDataSourceData
                $data = [
                    '' => $sessionData,
                ];
            }
        }
        return $data;
    }

    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->stockistRepository->getList($searchCriteria);
        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            \Aligent\Stockists\Api\Data\StockistInterface::STOCKIST_ID
        );
    }
}

<?php
/**
 * Copyright © Aligent Consulting. All rights reserved.
 */
namespace Aligent\Stockists\Ui\DataProvider;

use Aligent\Stockists\Api\GeoSearchCriteriaInterface;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Aligent\Stockists\Model\GeoSearchCriteriaBuilder;
use Aligent\Stockists\Model\TradingHours;
use Aligent\Stockists\Api\Data\StockistInterface;
use Magento\Backend\Model\Session;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Ui\DataProvider\SearchResultFactory;

class Stockist extends DataProvider
{

    const STOCKIST_FORM_NAME = 'stockist_form_data_stockist';

    /**
     * @var StockistRepositoryInterface
     */
    protected $stockistRepository;

    /**
     * @var SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var SerializerInterface
     */
    private $json;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    public function __construct(
        SearchResultFactory $searchResultFactory,
        StockistRepositoryInterface $stockistRepository,
        Session $session,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        GeoSearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        SerializerInterface $json,
        FilterBuilder $filterBuilder,
        RegionFactory $regionFactory,
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
        $this->regionFactory = $regionFactory;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = parent::getData();
        foreach ($data['items'] as &$item) {
            if (!empty($item[StockistInterface::HOURS][TradingHours::PUBLIC_HOLIDAYS])) {
                $item[StockistInterface::HOURS][TradingHours::PUBLIC_HOLIDAYS] =
                    $this->json->unserialize($item[StockistInterface::HOURS][TradingHours::PUBLIC_HOLIDAYS]);
            }
            $item[StockistInterface::HOURS] = $this->json->serialize($item[StockistInterface::HOURS]);
            $item[StockistInterface::STORE_IDS] = implode(',', $item[StockistInterface::STORE_IDS]);
            $item[StockistInterface::COUNTRY_ID] = $item[StockistInterface::COUNTRY];
            $item[StockistInterface::REGION_ID] = $this->regionFactory->create()->loadByName(
                $item[StockistInterface::REGION],
                $item[StockistInterface::COUNTRY_ID],
            )->getRegionId();
            $item[StockistInterface::ALLOW_STORE_DELIVERY] = (bool)$item[StockistInterface::ALLOW_STORE_DELIVERY];
            $item[StockistInterface::IS_ACTIVE] = (bool)$item[StockistInterface::IS_ACTIVE];

            // Make sure extension attributes are copied onto the item itself, then we can
            // reference them in Ui Components
            foreach ($item[StockistInterface::EXTENSION_ATTRIBUTES] ?? [] as $key => $value) {
                $item[$key] = $value;
            }
        }

        if (self::STOCKIST_FORM_NAME === $this->name) {
            // It is needed for support of several fieldsets.
            // For details see \Magento\Ui\Component\Form::getDataSourceData
            if ($data['totalRecords'] > 0) {
                $stockistId = $data['items'][0][StockistInterface::STOCKIST_ID];
                $stockistGeneralData = $data['items'][0];
                $dataForSingle[$stockistId] = [
                    'general' => $stockistGeneralData,
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

    /**
     * @inheritDoc
     */
    public function getSearchResult(): SearchResultInterface
    {
        /** @var GeoSearchCriteriaInterface $searchCriteria */
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->stockistRepository->getList($searchCriteria);
        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            StockistInterface::STOCKIST_ID
        );
    }
}

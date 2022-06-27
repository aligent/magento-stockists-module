<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\AdapterInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeocodeResultInterface;
use Aligent\Stockists\Api\GeocodeResultInterfaceFactory;
use Aligent\Stockists\Api\StockistRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\QueryParamsResolverInterface;
use Magento\Store\Model\ScopeInterface;

class GoogleMapsAdapter implements AdapterInterface
{
    const PATH= 'https://maps.googleapis.com/maps/api/geocode/';

    const XML_PATH_ENABLE_API_BIASING = 'stockists/geocode/enable_api_biaisng';

    const OUTPUT_FORMAT = 'json';

    /**
     * @var CurlFactory
     */
    protected $httpClientFactory;

    /**
     * @var QueryParamsResolverInterface
     */
    protected $queryParamsResolver;

    /**
     * @var Json
     */
    protected $serialiser;

    /**
     * @var GeocodeResultInterfaceFactory
     */
    private $geocodeResultFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StockistRepositoryInterface
     */
    private $stockistRepository;

    public function __construct(
        CurlFactory $httpClientFactory,
        QueryParamsResolverInterface $queryParamsResolver,
        Json $serialiser,
        GeocodeResultInterfaceFactory $geocodeResultFactory,
        ScopeConfigInterface $scopeConfig,
        StockistRepositoryInterface $stockistRepository
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->serialiser = $serialiser;
        $this->geocodeResultFactory = $geocodeResultFactory;
        $this->scopeConfig = $scopeConfig;
        $this->stockistRepository = $stockistRepository;
    }

    public function addressHasChangedFor($stockist): bool
    {
        if (!$stockist->getStockistId()) {
            return true;
        }

        $stockistOrig = $this->stockistRepository->getById($stockist->getStockistId());

        foreach (['street', 'city', 'region', 'country'] as $field) {
            $newData = $stockist->getData($field);
            $previousData = $stockistOrig->getData($field);

            if ($newData != $previousData) {
                return true;
            }
        }

        return false;
    }



    /**
     * @inheritDoc
     */
    public function buildRequest(StockistInterface $stockist, string $key): ?string
    {
        $address = $this->buildAddress($stockist);

        $queryParams = [
            'address' => $address,
            'key' => $key,
        ];

        // Restrict the address results to a specific area if Geocoding API's Region Biasing is enabled
        if ($this->isApiBiasingEnabled()) {
            $queryParams['region'] = $stockist->getCountry();

            $components = $this->buildComponents($stockist);
            $queryParams['components'] = $components;

        }

        $query = $this->queryParamsResolver->addQueryParams($queryParams)->getQuery();

        return $this::PATH . $this::OUTPUT_FORMAT . '?' . $query;
    }

    /**
     * @param Stockist $stockist
     * @return string
     */
    protected function buildAddress(Stockist $stockist): string
    {
        $params = [
            $stockist->getStreet(),
            $stockist->getCity(),
            $stockist->getRegion(),
            $stockist->getCountry()
        ];

        return implode(',', $params);
    }

    /**
     * @param Stockist $stockist
     * @return string
     */
    protected function buildComponents(Stockist $stockist): string
    {
        $params = [
            'country:' . $stockist->getCountry(),
            'postal_code:' . $stockist->getPostcode(),
        ];

        return implode('|', $params);
    }

    /**
     * @inheritDoc
     */
    public function performGeocode(string $request): ?array
    {
        $httpClient = $this->httpClientFactory->create();

        $httpClient->get($request);

        $response = $this->serialiser->unserialize($httpClient->getBody());

        /**
         * Google Maps API returns status and state any errors that occur as part
         * of the api response body...
         *
         * @see: https://developers.google.com/maps/documentation/geocoding/overview#StatusCodes
         */
        if ($response['status'] !== GoogleMapsGeocodeResult::STATUS_CODE_OK) {
            return [];
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(array $response): GeocodeResultInterface
    {
        $result = $this->geocodeResultFactory->create();
        $location = $response["results"][0]["geometry"]["location"] ?? null;

        if ($location) {
            $result->setData($response["status"], $location["lat"], $location["lng"]);
        }

        return $result;
    }

    /**
     * Check config value of stockists/geocode/enable_api_biaisng
     *
     * @return bool
     */
    protected function isApiBiasingEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_API_BIASING, ScopeInterface::SCOPE_STORE);
    }
}

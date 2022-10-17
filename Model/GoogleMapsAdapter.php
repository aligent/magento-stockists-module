<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\AdapterInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeocodeResultInterface;
use Aligent\Stockists\Api\GeocodeResultInterfaceFactory;
use Aligent\Stockists\Model\OptionSource\GeocodingResquestSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\QueryParamsResolverInterface;
use Magento\Store\Model\ScopeInterface;

class GoogleMapsAdapter implements AdapterInterface
{
    const PATH= 'https://maps.googleapis.com/maps/api/geocode/';

    const XML_PATH_GEOCODING_REQUEST_OPTION = 'stockists/geocode/request_option';

    const OUTPUT_FORMAT = 'json';

    /**
     * @var CurlFactory
     */
    protected $httpClientFactory;

    /**
     * @var QueryParamsResolverInterface
     */
    protected QueryParamsResolverInterface $queryParamsResolver;

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
     * @param CurlFactory $httpClientFactory
     * @param QueryParamsResolverInterface $queryParamsResolver
     * @param Json $serialiser
     * @param GeocodeResultInterfaceFactory $geocodeResultFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CurlFactory $httpClientFactory,
        QueryParamsResolverInterface $queryParamsResolver,
        Json $serialiser,
        GeocodeResultInterfaceFactory $geocodeResultFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->serialiser = $serialiser;
        $this->geocodeResultFactory = $geocodeResultFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $stockist
     * @return bool
     */
    public function addressHasChangedFor($stockist): bool
    {
        foreach (['street', 'city', 'region', 'country'] as $field) {
            if ($stockist->dataHasChangedFor($field)) {
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
            'key' => $key
        ];

        $queryParams = $this->addAdditionalRequestParams($stockist, $queryParams);
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
        $params = [];

        if ($stockist->getCountry()) {
            $params[] = 'country:' . $stockist->getCountry();
        }

        if ($stockist->getPostcode()) {
            $params[] = 'postal_code:' . $stockist->getPostcode();
        }

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
     * Check config value of stockists/geocode/request_option
     *
     * @return ?string
     */
    protected function getGeocodingRequestOption(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GEOCODING_REQUEST_OPTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Google Maps API now supports Region Biasing and Component Filtering in a Geocoding request
     * @see: https://developers.google.com/maps/documentation/geocoding/requests-geocoding#RegionCodes
     *
     * @param StockistInterface $stockist
     * @param array $queryParams
     * @return array
     */
    private function addAdditionalRequestParams(StockistInterface $stockist, array $queryParams): array
    {
        $requestOption = $this->getGeocodingRequestOption();

        switch ($requestOption) {
            case GeocodingResquestSource::REGION_BIASING:
                if ($stockist->getCountry()) {
                    $queryParams['region'] = $stockist->getCountry();
                }
                return $queryParams;
            case GeocodingResquestSource::COMPONENT_FILTERING:
                $components = $this->buildComponents($stockist);
                if (!empty($components)) {
                    $queryParams['components'] = $components;
                }
                return $queryParams;
        }

        return $queryParams;
    }
}

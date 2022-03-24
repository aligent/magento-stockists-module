<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\AdapterInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeocodeResultInterface;
use Aligent\Stockists\Api\GeocodeResultInterfaceFactory;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\QueryParamsResolverInterface;

class GoogleMapsAdapter implements AdapterInterface
{
    const PATH= 'https://maps.googleapis.com/maps/api/geocode/';

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

    public function __construct(
        CurlFactory $httpClientFactory,
        QueryParamsResolverInterface $queryParamsResolver,
        Json $serialiser,
        GeocodeResultInterfaceFactory $geocodeResultFactory
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->serialiser = $serialiser;
        $this->geocodeResultFactory = $geocodeResultFactory;
    }

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
     * @inheritDoc
     */
    public function performGeocode(string $request): ?array
    {
        $httpClient = $this->httpClientFactory->create();

        $httpClient->get($request);

        $response = $this->serialiser->unserialize($httpClient->getBody());

        /**
         * Google Maps API return status and state any errors that occur as part
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
}

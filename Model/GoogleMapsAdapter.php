<?php

namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\AdapterInterface;
use Aligent\Stockists\Api\Data\StockistInterface;
use Aligent\Stockists\Api\GeocodeResultInterface;

class GoogleMapsAdapter implements AdapterInterface
{
    const PATH= 'https://maps.googleapis.com/maps/api/geocode/';

    const OUTPUT_FORMAT = 'json';

    /**
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $httpClientFactory;

    /**
     * @var \Magento\Framework\Url\QueryParamsResolverInterface
     */
    protected $queryParamsResolver;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serialiser;

    /**
     * @var \Aligent\Stockists\Api\GeocodeResultInterfaceFactory
     */
    private $geocodeResultFactory;

    public function __construct(
        \Magento\Framework\HTTP\Client\CurlFactory $httpClientFactory,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver,
        \Magento\Framework\Serialize\Serializer\Json $serialiser,
        \Aligent\Stockists\Api\GeocodeResultInterfaceFactory $geocodeResultFactory
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

    public function performGeocode(string $request): ?array
    {
        $httpClient = $this->httpClientFactory->create();

        $httpClient->get($request);

        $response = $this->serialiser->unserialize($httpClient->getBody());

        /**
         * Google maps API return 200 and state any errors that occur as part
         * of the api response body...
         *
         * @see: https://developers.google.com/maps/documentation/geocoding/overview#StatusCodes
         */
        if ($response['status'] !== \Zend\Http\Response::STATUS_CODE_200) {
            return [];
        }

        return $response;
    }

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

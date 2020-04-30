<?php


namespace Aligent\Stockists\Model;

use Aligent\Stockists\Api\AdapterInterface;

class GoogleMapsAdapter implements AdapterInterface
{
    const PATH= 'https://maps.googleapis.com/maps/api/geocode/';
    const OUTPUT_FORMAT = 'json';

    protected $httpClientFactory;
    protected $queryParamsResolver;
    protected $serialiser;
    protected $resultJsonFactory;

    public function _construct(
        \Magento\Framework\HTTP\Client\CurlFactory $httpClientFactory,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver,
        \Magento\Framework\Serialize\Serializer\Json $serialiser,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->serialiser = $serialiser;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function buildRequest(string $address, string $key) :? string
    {
        $queryParams = [
            'address' => $address,
            'key' => $key
        ];

        $query = $this->queryParamsResolver->addQueryParams($queryParams)->getQuery();

        return $this::PATH . $this::OUTPUT_FORMAT . '?' . $query;;
    }

    public function performGeocode(string $request) :? array
    {
        $httpClient = $this->httpClientFactory->create();

        $httpClient->get($request);

        if ($httpClient->getStatus() !== \Zend\Http\Response::STATUS_CODE_200) {
            return false;
        }

        $response = $httpClient->getBody();

        return $this->serialiser->unserialize($response);
    }

    public function handleResponse(array $response) : \Magento\Framework\Controller\Result\Json
    {
        $result = $this->resultJsonFactory->create();
        $result->setData([
            'status' => $response->status,
            'lat' => $response->geometry->location->lat,
            'long' => $response->geometry->location->long
        ]);

        return $result;
    }
}
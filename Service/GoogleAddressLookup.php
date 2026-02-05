<?php
/**
 * Copyright (c) Aligent. (https://www.aligent.com.au)
 */
declare(strict_types=1);

namespace Aligent\Stockists\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class GoogleAddressLookup
{
    private const PLACES_AUTOCOMPLETE_URL = 'https://places.googleapis.com/v1/places:autocomplete';
    private const PLACES_DETAILS_URL = 'https://places.googleapis.com/v1/places/';

    private const XML_PATH_ADDRESS_LOOKUP_ENABLED = 'stockists/geocode/address_lookup_enabled';
    private const XML_PATH_GEOCODE_API_KEY = 'stockists/geocode/key';
    private const GOOGLE_API_LOOKUP_FAILED = 'Google API Lookup Failed';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly CurlFactory $curlFactory,
        private readonly Json $json,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Check if Google Address Lookup is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADDRESS_LOOKUP_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get address suggestions from Google Places API (New) in StockistSearchItem format
     *
     * @param string $query
     * @param string $countryCode
     * @param int|null $storeId
     * @return array
     */
    public function getAddressSuggestions(string $query, string $countryCode, ?int $storeId = null): array
    {
        if (!$this->isEnabled($storeId)) {
            return [];
        }

        $apiKey = $this->getApiKey($storeId);
        if (empty($apiKey)) {
            $this->logger->warning('Google Address Lookup is enabled but no API key is configured');
            return [];
        }

        try {
            $suggestions = $this->getAutocompleteSuggestions($query, $countryCode, $apiKey);
            return $this->getPlaceDetails($suggestions, $apiKey, $countryCode);
        } catch (\Exception $e) {
            $this->logger->error('Google Address Lookup error: ' . $e->getMessage(), [
                'query' => $query,
                'country' => $countryCode
            ]);
            return [];
        }
    }

    /**
     * Get API key for Google Places API
     *
     * @param int|null $storeId
     * @return string|null
     */
    private function getApiKey(?int $storeId = null): ?string
    {
        $apiKey = $this->scopeConfig->getValue(
            self::XML_PATH_GEOCODE_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $apiKey;
    }

    /**
     * Get autocomplete suggestions from Google Places API (New)
     *
     * @see https://developers.google.com/maps/documentation/places/web-service/place-autocomplete
     * @param string $query
     * @param string $countryCode
     * @param string $apiKey
     * @return array
     */
    private function getAutocompleteSuggestions(string $query, string $countryCode, string $apiKey): array
    {
        $requestBody = [
            'input' => $query,
            'includedRegionCodes' => [strtoupper($countryCode)]
        ];

        // Only restrict to address types if query doesn't contain a postcode pattern
        // This allows postcode-based searches to return broader results
        if (!$this->containsPostcode($query)) {
            $requestBody['includedPrimaryTypes'] = ['street_address', 'subpremise', 'premise', 'route'];
        }

        $response = $this->makePostRequest(
            self::PLACES_AUTOCOMPLETE_URL,
            $requestBody,
            $apiKey
        );

        if (empty($response['suggestions'])) {
            return [];
        }

        return $response['suggestions'];
    }

    /**
     * Check if query contains a postcode pattern
     *
     * @param string $query
     * @return bool
     */
    private function containsPostcode(string $query): bool
    {
        // Australian postcode: 4 digits (0200-9999)
        // US ZIP: 5 digits
        // UK postcode: alphanumeric pattern
        return (bool)preg_match('/\b\d{4,5}\b/', $query)
            || (bool)preg_match('/\b[A-Z]{1,2}\d[A-Z\d]?\s*\d[A-Z]{2}\b/i', $query);
    }

    /**
     * Get detailed place information for each suggestion
     *
     * @param array $suggestions
     * @param string $apiKey
     * @param string $countryCode
     * @return array
     */
    private function getPlaceDetails(array $suggestions, string $apiKey, string $countryCode): array
    {
        $items = [];

        foreach ($suggestions as $suggestion) {
            $placePrediction = $suggestion['placePrediction'] ?? null;
            if (!$placePrediction) {
                continue;
            }

            $placeId = $placePrediction['placeId'] ?? null;
            if (!$placeId) {
                continue;
            }

            $details = $this->fetchPlaceDetails($placeId, $apiKey);
            if (empty($details)) {
                continue;
            }

            $items[] = $this->formatAsStockistItem($details, $placePrediction, $countryCode);
        }

        return $items;
    }

    /**
     * Fetch place details from Google Places API (New)
     *
     * @see https://developers.google.com/maps/documentation/places/web-service/place-details
     *
     * @param string $placeId
     * @param string $apiKey
     * @return array
     */
    private function fetchPlaceDetails(string $placeId, string $apiKey): array
    {
        $fieldMask = 'id,formattedAddress,addressComponents,location';
        $url = self::PLACES_DETAILS_URL . $placeId;

        return $this->makeGetRequest($url, $apiKey, $fieldMask);
    }

    /**
     * Format place details as StockistSearchItem
     *
     * @param array $details
     * @param array $placePrediction
     * @param string $countryCode
     * @return array
     */
    private function formatAsStockistItem(array $details, array $placePrediction, string $countryCode): array
    {
        $addressComponents = $this->parseAddressComponents($details['addressComponents'] ?? []);
        $location = $details['location'] ?? [];

        $mainText = $placePrediction['structuredFormat']['mainText']['text'] ?? null;
        $secondaryText = $placePrediction['structuredFormat']['secondaryText']['text'] ?? null;
        $fullDescription = $mainText && $secondaryText ? "$mainText, $secondaryText" : ($mainText ?? $secondaryText);

        return [
            'name' => $mainText,
            'full_address' => $details['formattedAddress'] ?? $fullDescription,
            'description' => $fullDescription,
            'street' => $addressComponents['street'] ?? null,
            'city' => $addressComponents['city'] ?? null,
            'postcode' => $addressComponents['postcode'] ?? null,
            'region' => $addressComponents['region'] ?? null,
            'region_code' => $addressComponents['region_code'] ?? null,
            'country' => strtoupper($countryCode),
            'lat' => isset($location['latitude']) ? (float)$location['latitude'] : null,
            'lng' => isset($location['longitude']) ? (float)$location['longitude'] : null
        ];
    }

    /**
     * Parse Google address components (New API format) into structured data
     *
     * @see https://developers.google.com/maps/documentation/places/web-service/place-details#AddressComponent
     *
     * @param array $components
     * @return array
     */
    private function parseAddressComponents(array $components): array
    {
        $result = [
            'street' => '',
            'city' => null,
            'postcode' => null,
            'region' => null,
            'region_code' => null
        ];

        $streetNumber = '';
        $route = '';

        foreach ($components as $component) {
            $types = $component['types'] ?? [];
            $longText = $component['longText'] ?? '';
            $shortText = $component['shortText'] ?? '';

            if (in_array('street_number', $types)) {
                $streetNumber = $longText;
            } elseif (in_array('route', $types)) {
                $route = $longText;
            } elseif (in_array('locality', $types)) {
                $result['city'] = $longText;
            } elseif (in_array('sublocality_level_1', $types) && empty($result['city'])) {
                $result['city'] = $longText;
            } elseif (in_array('postal_code', $types)) {
                $result['postcode'] = $longText;
            } elseif (in_array('administrative_area_level_1', $types)) {
                $result['region'] = $longText;
                $result['region_code'] = $shortText;
            }
        }

        $result['street'] = trim($streetNumber . ' ' . $route) ?: null;

        return $result;
    }

    /**
     * Make POST request to Google Places API (New)
     *
     * @param string $url
     * @param array $body
     * @param string $apiKey
     * @return array
     */
    private function makePostRequest(string $url, array $body, string $apiKey): array
    {
        $curl = $this->curlFactory->create();
        $curl->setTimeout(10);
        $curl->addHeader('Content-Type', 'application/json');
        $curl->addHeader('X-Goog-Api-Key', $apiKey);
        $curl->post($url, $this->json->serialize($body));

        $responseBody = $curl->getBody();
        if (empty($responseBody)) {
            return [];
        }
        $response = $this->json->unserialize($responseBody);
        if (isset($response['error'])) {
            $this->logger->error(self::GOOGLE_API_LOOKUP_FAILED, $response['error']);
            throw new LocalizedException(__(self::GOOGLE_API_LOOKUP_FAILED));
        }
        return $response;
    }

    /**
     * Make GET request to Google Places API (New)
     *
     * @param string $url
     * @param string $apiKey
     * @param string $fieldMask
     * @return array
     */
    private function makeGetRequest(string $url, string $apiKey, string $fieldMask): array
    {
        $curl = $this->curlFactory->create();
        $curl->setTimeout(10);
        $curl->addHeader('X-Goog-Api-Key', $apiKey);
        $curl->addHeader('X-Goog-FieldMask', $fieldMask);
        $curl->get($url);

        $responseBody = $curl->getBody();
        if (empty($responseBody)) {
            return [];
        }
        $response = $this->json->unserialize($responseBody);
        if (isset($response['error'])) {
            $this->logger->error(self::GOOGLE_API_LOOKUP_FAILED, $response['error']);
            throw new LocalizedException(__(self::GOOGLE_API_LOOKUP_FAILED));
        }
        return $response;
    }
}

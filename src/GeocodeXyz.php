<?php

declare(strict_types=1);

namespace Cydrickn\Geocoder\Provider\GeocodeXyz;

use Geocoder\Collection;
use Geocoder\Exception\InvalidArgument;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Http\Provider\AbstractHttpProvider;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

class GeocodeXyz extends AbstractHttpProvider implements Provider
{
    const ENDPOINT_URL = 'https://geocode.xyz/';

    const AVAILABLE_OPTIONS = ['geomode', 'region', 'searchmode'];

    const OPTION_GEOMODE = 'geomode';
    const OPTION_REGION = 'region';
    const OPTION_SEARCHMODE = 'searchmode';

    const OPTION_VALUE_GEOMODE_STRICT = 'strictmode';
    const OPTION_VALUE_GEOMODE_NOTSTRICT  = 'nostrict';
    const OPTION_VALUE_SEARCHMODE_LOCATE = 'locate';
    const OPTION_VALUE_SEARCHMODE_SCANTEXT = 'scantext';

    const DEFAULT_OPTIONS = [
        static::OPTION_GEOMODE => static::OPTION_GEOMODE_NOTSTRICT,
        static::OPTION_REGION => 'World',
        static::OPTION_SEARCHMODE => static::OPTION_SEARCHMODE_LOCATE,
    ];

    const AVAILABLE_REGIONS = [
        "AF", "AX", "AL", "DZ", "AS", "AD", "AO", "AI", "AQ", "AG", "AR", "AM", "AW", "AU", "AT", "AZ", "BS", "BH",
        "BD", "BB", "BY", "BE", "BZ", "BJ", "BM", "BT", "BO", "BQ", "BA", "BW", "BR", "IO", "VG", "BN", "BG", "BF",
        "BI", "KH", "CM", "CA", "CV", "KY", "CF", "TD", "CL", "CN", "CX", "CC", "CO", "KM", "CG", "CK", "CR", "HR",
        "CU", "CW", "CY", "CZ", "CI", "DK", "DJ", "DM", "DO", "EC", "EG", "SV", "GQ", "ER", "EE", "ET", "FK", "FO",
        "FJ", "FI", "FR", "GF", "PF", "TF", "GA", "GM", "GE", "DE", "GH", "GI", "GR", "GL", "GD", "GP", "GU", "GT",
        "GG", "GN", "GW", "GY", "HT", "HN", "HK", "HU", "IS", "IN", "ID", "IR", "IQ", "IE", "IM", "IL", "IT", "JM",
        "JP", "JE", "JO", "KZ", "KE", "KI", "KS", "KW", "KG", "LA", "LV", "LB", "LS", "LR", "LY", "LI", "LT", "LU",
        "MO", "MK", "MG", "MW", "MY", "MV", "ML", "MT", "MH", "MQ", "MR", "MU", "YT", "MX", "FM", "MD", "MC", "MN",
        "ME", "MS", "MA", "MZ", "MM", "NA", "NR", "NP", "NL", "AN", "NC", "NZ", "NI", "NE", "NG", "NU", "NF", "KP",
        "MP", "NO", "OM", "PK", "PW", "PS", "PA", "PG", "PY", "PE", "PH", "PN", "PL", "PT", "PR", "QA", "RO","RU",
        "RW", "RE", "GS", "SH", "KN", "LC", "PM", "VC", "BL", "SX", "MF", "WS", "SM", "ST", "SA", "SN", "RS", "SC",
        "SL", "SG", "SK", "SI", "SB", "SO", "ZA", "KR", "SS", "ES", "LK", "SD", "SR", "SJ", "SZ", "SE", "CH", "SY",
        "TW", "TJ", "TZ", "TH", "TL", "TG", "TK", "TO", "TT", "TN", "TR", "TM", "TC", "TV", "UM", "UG", "UA", "AE",
        "UK", "US", "UY", "UZ", "VU", "VA", "VE", "VN", "VI", "WF", "EH", "YE", "CD", "ZM", "ZW",
        "Europe", "Oceania", "Asia", "SouthAmerica", "World",
    ];

    /**
     * @var string
     */
    private $auth;

    public function geocodeQuery(GeocodeQuery $query): Collection
    {
        $address = $query->getText();

        if (filter_var($address, FILTER_VALIDATE_IP)) {
            throw new UnsupportedOperation('Geocode.xyz Provider does not support IP addresses.');
        }
        $queryData = array_merge(self::DEFAULT_OPTIONS, $query->getAllData());
        $this->validateOptions($queryData);

        return $this->executeQuery($address, $queryData);
    }

    public function reverseQuery(ReverseQuery $query): Collection
    {
        $address = $query->getCoordinates()->getLatitude() . ',' . $query->getCoordinates()->getLongitude();
        $queryData = array_merge(self::DEFAULT_OPTIONS, $query->getAllData());
        $this->validateOptions($queryData);

        return $this->executeQuery($address, $queryData);
    }

    public function getName(): string
    {
        return 'geocodexyz';
    }

    public function __construct(\Http\Client\HttpClient $client, string $auth = '')
    {
        $this->auth = trim($auth);

        parent::__construct($client);
    }

    private function executeQuery(string $search, array $queryData, string $searchMode): AddressCollection
    {
        $url = self::ENDPOINT_URL . '?' . $this->generateUrlQueryString($search, $queryData);
        $response = json_decode($this->getUrlContents($url));

        try {
            $this->checkError($response);

            if ($searchMode === self::OPTION_VALUE_SEARCHMODE_LOCATE) {
                return $this->generateCollectionFromLocate($response);
            }
        } catch (Error\NoResultError $ex) {
            return new AddressCollection();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private function generateCollectionFromLocate(array $data): AddressCollection
    {
        $collection = [];

        $collection[] = $this->generateAddress([
            'country' => $data['standard']['countryname'],
            'country_code' => $data['standard']['prov'],
            'lng' => $data['longt'],
            'lat' => $data['latt'],
            'postal' => is_string($data['standard']['postal']) ? $data['standard']['postal'] : null,
            'address' => is_string($data['standard']['addresst']) ? $data['standard']['addresst'] : null,
            'city' => $data['standard']['city'],
        ]);
        $locations = $data['alt'] ? $data['alt']['loc'] ?? [] : [];
        if (count($locations) > 0 && !isset($locations[0])) {
            $collection[] = $this->generateAddress([
                'country' => $locations['countryname'],
                'country_code' => $locations['prov'],
                'lng' => $locations['longt'],
                'lat' => $locations['latt'],
                'postal' => is_string($locations['postal']) ? $locations['postal'] : null,
                'city' => $locations['city'],
            ]);
        } else {
            foreach ($locations as $location) {
                $collection[] = $this->generateAddress([
                    'country' => $location['countryname'],
                    'country_code' => $location['prov'],
                    'lng' => $location['longt'],
                    'lat' => $location['latt'],
                    'postal' => is_string($location['postal']) ? $location['postal'] : null,
                    'city' => $location['city'],
                ]);
            }
        }

        return new AddressCollection($collection);
    }

    private function generateAddress(array $data): \Geocoder\Model\Address
    {
        $address = new \Geocoder\Model\AddressBuilder($this->getName());
        $address
            ->setCountry($data['country'] ?? null)
            ->setCountryCode($data['country_code'] ?? null)
            ->setCoordinates($data['lat'], $data['lng'])
            ->setLocality($data['city'] ?? null)
            ->setSubLocality($data['address'] ?? null)
            ->setPostalCode($data['postal'] ?? null)
        ;

        return $address->build(\Geocoder\Model\Address::class);
    }

    private function checkError(array $response): void
    {
        if (array_key_exists('error', $response)) {
            throw Error\GeocodeXyzErrorFactory::create($response['code'], $response['description']);
        }
    }

    private function generateUrlQueryString(string $search, array $queryData): string
    {
        $clonedQueryData = $queryData;
        unset($clonedQueryData[self::OPTION_SEARCHMODE]);

        $clonedQueryData[$queryData[self::OPTION_GEOMODE]] = 1;
        $clonedQueryData[$queryData[self::OPTION_SEARCHMODE]] = $search;
        $clonedQueryData['geoit'] = 'json';
        $clonedQueryData['moreinfo'] = 1;

        if ($this->auth !== '') {
            $clonedQueryData['auth'] = $this->auth;
        }

        return http_build_query($clonedQueryData);
    }

    private function validateOptions(array $queryData): void
    {
        $geoMode = $queryData[self::OPTION_GEOMODE];
        $searchMode = $queryData[self::OPTION_SEARCHMODE];
        $region = explode(',', $queryData[self::OPTION_REGION]);

        if (!in_array($geoMode, [self::OPTION_VALUE_GEOMODE_STRICT, self::OPTION_VALUE_GEOMODE_NOTSTRICT])) {
            throw $this->createOptionInvalidArgumentException(
                self::OPTION_GEOMODE,
                [self::OPTION_VALUE_GEOMODE_STRICT, self::OPTION_VALUE_GEOMODE_NOTSTRICT]
            );
        }

        if (!in_array($searchMode, [self::OPTION_VALUE_SEARCHMODE_LOCATE, self::OPTION_VALUE_SEARCHMODE_SCANTEXT])) {
            throw $this->createOptionInvalidArgumentException(
                self::OPTION_SEARCHMODE,
                [self::OPTION_VALUE_SEARCHMODE_LOCATE, self::OPTION_VALUE_SEARCHMODE_SCANTEXT]
            );
        }

        foreach ($region as $value) {
            if (!in_array($value, self::AVAILABLE_REGIONS)) {
                throw $this->createOptionInvalidArgumentException(self::OPTION_REGION, self::AVAILABLE_REGIONS);
            }
        }
    }

    private function createOptionInvalidArgumentException(string $option, array $availableValues): InvalidArgument
    {
        return new InvalidArgument(
            sprintf('Invalid option %s value, the value must one of "%s"', $option, implode(', ', $availableValues))
        );
    }
}

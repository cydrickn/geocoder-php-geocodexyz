<?php

namespace Cydrickn\Geocoder\Provider\GeocodeXyz\Tests;

use Cydrickn\Geocoder\Provider\GeocodeXyz\GeocodeXyz;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\IntegrationTest\BaseTestCase;
use Geocoder\Query\GeocodeQuery;

class GeocodeXyzTests extends BaseTestCase
{
    protected function getCacheDir()
    {
        return __DIR__ . '/.cached_responses';
    }

    public function testGetName()
    {
        $provider = new GeocodeXyz($this->getMockedHttpClient());
        $this->assertSame('geocodexyz', $provider->getName());
    }

    /**
     * @expectedException UnsupportedOperation
     * @expectedExceptionMessage Geocode.xyz Provider does not support IP addresses.
     */
    public function testUnsupportedOperationForIPv4()
    {
        $provider = new GeocodeXyz($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('127.0.0.1'));
    }

    /**
     * @expectedException UnsupportedOperation
     * @expectedExceptionMessage Geocode.xyz Provider does not support IP addresses.
     */
    public function testUnsupportedOperationForIPv6()
    {
        $provider = new GeocodeXyz($this->getMockedHttpClient());
        $provider->geocodeQuery(GeocodeQuery::create('::1'));
    }

    /**
     * @dataProvider invalidOptionsDataProvider
     */
    public function testInvalidOptions(string $geomode, string $region, string $expectedMessage)
    {
        try {
            new GeocodeXyz($this->getMockedHttpClient(), ['geomode' => $geomode, 'region' => $region]);
        } catch (\Exception $ex) {
            $this->assertInstanceOf(\Geocoder\Exception\InvalidArgument::class, $ex);
            $this->assertSame($ex->getMessage(), $expectedMessage);
        }
    }

    protected function invalidOptionsDataProvider()
    {
        yield ['wrongmode', 'PH', 'Invalid option geomode value, the value must one of "strictmode, nostrict"'];
        yield ['nostrict', 'WRONG', 'Invalid option region value, the value must one of "' . implode(', ', GeocodeXyz::AVAILABLE_REGIONS) . '"'];
    }
}

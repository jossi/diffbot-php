<?php

namespace Diffbot\Test;

use \Diffbot\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    const INVALID_URL = 'htp:/invalidUrl.com';

    const VALID_URL   = 'http://www.validurl.com';

    const TOKEN = 'my_token';

    protected function getHttpClientStub()
    {
        return $this->getMock('\Diffbot\Http\ClientInterface');
    }

    public function test_isValidUrl_urlIsAnInteger_returnsFalse()
    {
        $url = 27;
        $client = new Client(self::TOKEN);
        $result = $client->isValidUrl($url);
        $this->assertFalse($result);
    }

    public function test_isValidUrl_urlIsNull_returnsFalse()
    {
        $url = null;
        $client = new Client(self::TOKEN);
        $result = $client->isValidUrl($url);
        $this->assertFalse($result);
    }

    public function test_isValidUrl_urlIsAnEmptyString_returnsFalse()
    {
        $url = '';
        $client = new Client(self::TOKEN);
        $result = $client->isValidUrl($url);
        $this->assertFalse($result);
    }

    public function test_isValidUrl_invalidUrl_returnsFalse()
    {
        $url = self::INVALID_URL;
        $client = new Client(self::TOKEN);
        $result = $client->isValidUrl($url);
        $this->assertFalse($result);
    }

    public function test_isValidUrl_validUrl_returnsTrue()
    {
        $url = self::VALID_URL;
        $client = new Client(self::TOKEN);
        $result = $client->isValidUrl($url);
        $this->assertTrue($result);
    }


    public function test_construct_noHttpClient_defaultHttpClientIsSet()
    {
        $url = self::VALID_URL;
        $client = new Client(self::TOKEN);
        $actual = $client->getHttpClient();
        $expected = '\Diffbot\Http\ClientInterface';

        $this->assertInstanceOf($expected, $actual);
    }

    public function test_construct_customHttpClient_customHttpClientIsSet()
    {
        $httpClientStub = $this->getHttpClientStub();
        $url = self::VALID_URL;
        $client = new Client(self::TOKEN);
        $client->setHttpClient($httpClientStub);

        $expected = get_class($httpClientStub);
        $actual = get_class($client->getHttpClient());

        $this->assertEquals($expected, $actual);
    }
}

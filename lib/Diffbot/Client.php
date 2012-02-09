<?php

namespace Diffbot;

use \Diffbot\Http\ClientInterface;
use \Diffbot\Http\Curl\Client as Curl;
use \Diffbot\Exception;

class Client
{
    const API_URL = 'http://www.diffbot.com/api/';

    const ARTICLE_TYPE = 'article';

    const FRONTPAGE_TYPE = 'frontpage';

    protected $token;

    protected $url;

    protected $params;

    protected $httpClient;

    public function __construct(
        $token,
        $url,
        array $params = array(),
        $httpClient = null,
        array $httpClientConfig = array()
    )
    {
        $this->setUrl($url);

        $this->setToken($token);

        $this->setParams($params);

        if (null === $httpClient) {
            $httpClient = new Curl($httpClientConfig);
        }

        $this->setHttpClient($httpClient);
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getArticle()
    {
        return $this->getPage(self::ARTICLE_TYPE);
    }

    public function getFrontpage()
    {
        return $this->getPage(self::FRONTPAGE_TYPE);
    }

    protected function getPage($type)
    {
        $targetUrl = $this->prepareUrl($type);
        return $this->getHttpClient()->get($targetUrl);
    }

    public function prepareUrl($type)
    {
        $params = array(
            'token' => $this->getToken(),
            'url'   => $this->getUrl()
        );

        $params = array_merge($params, $this->getParams());

        $finalUrl  = self::API_URL . $type;
        $finalUrl .= '?' . http_build_query($params);

        return $finalUrl;
    }

    public function isValidUrl($value)
    {
        $pattern = '~^
            (http|https)://                                 # protocol
            (
                ([\pL\pN\pS-]+\.)+[\pL]+                   # a domain name
                    |                                     #  or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}      # a IP address
                    |                                     #  or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # a IPv6 address
            )
            (:[0-9]+)?                 # a port (optional)
            (/?|/\S+)                  # a /, nothing or a / with something
        $~ixu';

        $value = (string) $value;
        if (!preg_match($pattern, $value)) {
            return false;
        }

        return true;
    }
}
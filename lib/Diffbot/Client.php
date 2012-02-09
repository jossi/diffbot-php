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

    protected $params;

    protected $httpClient;

    public function __construct(
        $token,
        array $params = array(),
        $httpClient = null,
        array $httpClientConfig = array()
    )
    {
        $this->setToken($token);

        $this->setParams($params);

        if (null === $httpClient) {
            $httpClient = new Curl($httpClientConfig);
        }

        $this->setHttpClient($httpClient);
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

    public function getArticle($url)
    {
        return $this->getPage($url, self::ARTICLE_TYPE);
    }

    public function getFrontpage($url)
    {
        return $this->getPage($url, self::FRONTPAGE_TYPE);
    }

    /*
    public function getArticleFromHtml($html)
    {
        return $this->getPageFromHtml($html, self::ARTICLE_TYPE);
    }

    public function getFrontpageFromHtml($html)
    {
        return $this->getPageFromHtml($html, self::FRONTPAGE_TYPE);
    }
    */

    protected function getPage($url, $type)
    {
        $targetUrl = $this->prepareUrl($url, $type);
        return $this->getHttpClient()->get($targetUrl);
    }

    /*
    protected function getPageFromHtml($html, $type)
    {
        $targetUrl = self::API_URL . $type;
        $httpClient = $this->getHttpClient();
        $httpClient->setOptions(
            array(
                CURLOPT_HTTPHEADER => array('Content-type: text/html', 'Content-length: ' . strlen($html)),
                CURLOPT_VERBOSE        => 1,
            )
        );
        $params = array(
            $html
        );
        return $httpClient->post($targetUrl, $params);
    }
    */

    protected function prepareUrl($url, $type)
    {
        $params = array(
            'token' => $this->getToken(),
            'url'   => $url
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
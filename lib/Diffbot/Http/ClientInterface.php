<?php

namespace Diffbot\Http;

interface ClientInterface
{
    public function get($url, $params = null);

    public function post($url, $params = null);
}

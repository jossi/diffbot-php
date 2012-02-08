<?php

namespace Diffbot\Http;

interface ClientInterface
{
    public function get($url, $params = null);
}

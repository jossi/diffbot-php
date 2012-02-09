<?php
require_once 'lib/Diffbot/Http/ClientInterface.php';
require_once 'lib/Diffbot/Http/Curl/Response.php';
require_once 'lib/Diffbot/Http/Curl/Client.php';
require_once 'lib/Diffbot/Exception.php';
require_once 'lib/Diffbot/Client.php';

$token = 'my_token';

$url = 'http://www.diffbot.com/blog/id/42';

$diffbot = new \Diffbot\Client($token, $url);
var_dump($diffbot->getArticle());
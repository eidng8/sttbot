<?php

use eidng8\Wiki;
use eidng8\Wiki\Analyst;
use eidng8\Wiki\Api\Api;
use eidng8\Wiki\Api\Http;

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

$api_url = 'http://startrektimelineswiki.com/w/api.php';
$api = new Api(new Http($api_url), __DIR__ . '/cache');
$wiki = new Wiki($api->parse(), $api->query(), $api->expandTemplates());

$analyst = new Analyst($wiki->missions(), $wiki->crew());
$analyst->crossRating();
$analyst->bestCrew();

$export = [
    'missions' => $analyst->getMissions()->export(),
    'crew'     => $analyst->getCrew()->export(),
];

file_put_contents(
    __DIR__ . '/www/data.json',
    json_encode($export, JSON_OPTIONS)
);

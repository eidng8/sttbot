<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

require __DIR__ . '/vendor/autoload.php';

use eidng8\Crew;
use eidng8\Wiki;
use eidng8\Wiki\Analyst;
use eidng8\Wiki\Api\Api;
use eidng8\Wiki\Api\Http;

define(
    'JSON_OPTIONS',
    JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE |
    JSON_PRESERVE_ZERO_FRACTION
);

define('CACHE_ROOT', __DIR__ . '/cache');

$api_url = 'http://startrektimelineswiki.com/w/api.php';
$api = new Api(new Http($api_url), CACHE_ROOT);
$wiki = new Wiki($api->parse(), $api->query(), $api->expandTemplates());

// get Missions list
// file_put_contents(
//     __DIR__ . '/www/missions.json',
//     json_encode($wiki->missions()->get(), JSON_OPTIONS)
// );

// get Crew list
// file_put_contents(
//     __DIR__ . '/www/crew.json',
//     json_encode(array_values($wiki->crew()->get()), JSON_OPTIONS)
// );

$analyst = new Analyst($wiki->missions(), $wiki->crew());
$analyst->crossRating();
$analyst->bestCrew();

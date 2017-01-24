#!/usr/bin/env php

<?php

require __DIR__ . '/vendor/autoload.php';

try {
    init($argv);

    $api_url = 'http://startrektimelineswiki.com/w/api.php';
    $api = new eidng8\Wiki\Api\Api(
        new eidng8\Wiki\Api\Http($api_url),
        __DIR__ . '/cache'
    );
    $wiki = new eidng8\Wiki(
        $api->parse(),
        $api->query(),
        $api->expandTemplates()
    );

    $analyst = new eidng8\Wiki\Analyst($wiki->missions(), $wiki->crew());
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
} catch (Exception $ex) {
    eidng8\Log\Log::err($ex->getMessage());
}

function init($args)
{
    if (!empty($args[1])) {
        $verbosity = substr_count($args[1], 'v');
        $levels = [
            Monolog\Logger::WARNING,
            Monolog\Logger::NOTICE,
            Monolog\Logger::INFO,
            Monolog\Logger::DEBUG,
        ];
        eidng8\Log\Log::setLevel($levels[$verbosity]);
    }

    eidng8\Log\Log::useStdio();
}

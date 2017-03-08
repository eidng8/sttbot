<?php

require __DIR__ . '/vendor/autoload.php';

$crewIndices = null;
$missionIndices = null;

try {
    if (!empty($argv[1])) {
        $verbosity = substr_count($argv[1], 'v');
        $levels = [
            Monolog\Logger::WARNING,
            Monolog\Logger::NOTICE,
            Monolog\Logger::INFO,
            Monolog\Logger::DEBUG,
        ];
        eidng8\Log\Log::setLevel($levels[$verbosity]);
    }

    eidng8\Log\Log::useStdio();

    if (!is_dir(__DIR__ . '/www')) {
        mkdir(__DIR__ . '/www', 0644);
    }

    $api_url = 'http://stt.wiki/w/api.php';
    $api = new eidng8\Wiki\Api\Api(
        new eidng8\Wiki\Api\Http($api_url),
        __DIR__ . '/cache'
    );

    $wiki = new eidng8\Wiki(
        $api->parse(),
        $api->query(),
        $api->expandTemplates()
    );

    $analyst = $wiki->analyse();

    $export = $wiki->export();

    $dir = __DIR__ . '/www';
    if (!is_dir($dir)) {
        mkdir($dir, 0644);
    }
    file_put_contents("$dir/data.json", json_encode($export, JSON_OPTIONS));
} catch (Exception $ex) {
    eidng8\Log\Log::err($ex->getMessage());
}

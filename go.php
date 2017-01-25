<?php

require __DIR__ . '/vendor/autoload.php';

$crewIndices = null;
$missionIndices = null;

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

    $missionIndices = missionIndex($export);
    $crewIndices = crewIndex($export);
    missionStats($analyst->getMissions(), $export);

    file_put_contents(
        __DIR__ . '/www/data.json',
        json_encode($export, JSON_OPTIONS)
    );
} catch (Exception $ex) {
    eidng8\Log\Log::err($ex->getMessage());
}

/**
 * @param array $args CLI arguments
 */
function init(array $args = null)
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

    if (!is_dir(__DIR__ . '/www')) {
        mkdir(__DIR__ . '/www', 0644);
    }
}

/**
 * @param \eidng8\Wiki\Templates\MissionList $missions
 * @param array                              $data
 */
function missionStats(
    eidng8\Wiki\Templates\MissionList $missions,
    array &$data
): void {
    $missions->each(function (eidng8\Wiki\Models\Mission $mission) use (&$data
    ) {
        if (empty($mission->steps)) {
            return;
        }

        global $crewIndices, $missionIndices;
        $midx = $missionIndices[$mission->name];
        foreach ($mission->steps as $idx => $step) {
            if (!empty($step['crew']['critical'])) {
                foreach ($step['crew']['critical'] as $member) {
                    $data['missions'][1][$midx]['steps'][$idx]['crew']['critical'][]
                        = $crewIndices[$member->name];
                }//end foreach
            }
            if (!empty($step['crew']['pass'])) {
                foreach ($step['crew']['pass'] as $member) {
                    $data['missions'][1][$midx]['steps'][$idx]['crew']['pass'][]
                        = $crewIndices[$member->name];
                }//end foreach
            }
            if (!empty($step['crew']['unlock'])) {
                foreach ($step['crew']['unlock'] as $member) {
                    $data['missions'][1][$midx]['steps'][$idx]['crew']['unlock'][]
                        = $crewIndices[$member->name];
                }//end foreach
            }
        }//end foreach
    });
}

/**
 * @param array $data
 *
 * @return array
 */
function missionIndex(array $data): array
{
    $indices = [];
    foreach ($data['missions'][1] as $idx => $mission) {
        $indices[$mission['name']] = $idx;
    }//end foreach
    return $indices;
}

/**
 * @param array $data
 *
 * @return array
 */
function crewIndex(array $data): array
{
    $indices = [];
    foreach ($data['crew'] as $idx => $member) {
        $indices[$member['name']] = $idx;
    }//end foreach
    return $indices;
}

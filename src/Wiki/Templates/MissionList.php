<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-17
 * Time: 23:52
 */

namespace eidng8\Wiki\Templates;

use eidng8\Wiki\Models\Mission as MissionModel;
use eidng8\Wiki\Models\Skills;
use eidng8\Wiki\WikiBase;

/**
 * Parse all missions
 */
class MissionList extends WikiBase
{
    /**
     * List of all missions, organized by episodes/cadet
     *
     * @var Mission[][]
     */
    protected $list;

    /**
     * @var \eidng8\Wiki\Models\Mission[][][]
     */
    protected $models = null;

    /**
     * Return mission list
     *
     * @param string $what
     *
     * @return array|\eidng8\Wiki\Models\Mission[][][]
     */
    public function get(string $what = null): array
    {
        if ($this->models) {
            return empty($what) ? $this->models : $this->models[$what];
        }

        $this->models = [];
        foreach ($this->list as $type => $episodes) {
            foreach ($episodes as $missions) {
                $tmp = [];
                /* @var Mission $mission */
                foreach ($missions as $mission) {
                    $tmp[] = $mission->get();
                }//end foreach
                $this->models[$type][] = $tmp;
            }//end foreach
        }//end foreach

        return empty($what) ? $this->models : $this->models[$what];
    }//end get()

    /**
     * Iterate through all away team missions and call the given function.
     * The callback is defined as:
     * function(Models\Mission $mission, int $index, int $episode, string $type)
     * - `$mission` is a mission model instance,
     * - `$index` is the index number within the episode
     * - `$episode` is the index of the episode
     * - `$type` can be either `'epissode'` or `'cadet'`
     *
     * @param callable $func
     */
    public function eachAway(callable $func): void
    {
        foreach ($this->list as $type => $episodes) {
            foreach ($episodes as $episode => $missions) {
                foreach ($missions as $index => $mission) {
                    /* @var Mission $mission */
                    $mission = $mission->get();
                    /* @var MissionModel $mission */
                    if (MissionModel::AWAY_TEAM === $mission->type) {
                        call_user_func_array(
                            $func,
                            [$mission, $index, $episode, $type]
                        );
                    }
                }//end foreach
            }//end foreach
        }//end foreach
    }//end eachAway()

    /**
     * Get mission by name
     *
     * @param string $name   Name of the mission, case insensitive
     * @param string $epName Name of episode, case insensitive
     *
     * @return MissionModel|null
     */
    public function byName(string $name, string $epName = null): ?MissionModel
    {
        $search = trim(strtolower($name));
        $epSearch = trim(strtolower($epName));
        foreach ($this->list as $episodes) {
            foreach ($episodes as $missions) {
                foreach ($missions as $mission) {
                    /* @var Mission $mission */
                    $model = $mission->get();
                    if ($search == strtolower($model->name)
                        && (empty($epSearch)
                            || strtolower($epSearch)
                               == strtolower($model->episode))
                    ) {
                        return $model;
                    }
                }//end foreach
            }//end foreach
        }//end foreach
        return null;
    }//end byName()

    /**
     * Fetch all missions from server and process them to models
     *
     * @return Mission[][]
     */
    public function fetch(): array
    {
        $templates = $this->fetchMissionList();
        $templates['episodes'] = $this->fetchTemplates($templates['episodes']);
        $cadets = $templates['cadet'][1];
        $templates['cadet'] = $this->fetchTemplates($templates['cadet']);

        $this->parseEpisodes($templates);
        $this->fetchCadetCrew($cadets, $templates['cadet']);

        return $this->list = $templates;
    }//end fetch()

    /**
     * Export all missions as array
     *
     * @return array
     */
    public function export(): array
    {
        $epi = 0;
        $episodes = [];
        $missions = [];
        $this->each(
            function (MissionModel $mission) use (
                &$episodes,
                &$missions,
                &$epi
            ) {
                $mission = $mission->toArray();

                // extract episode list
                if ($mission['episode'] != end($episodes)) {
                    $episodes[] = $mission['episode'];
                    $epi = count($episodes) - 1;
                }
                $mission['episode'] = $epi;

                // remove redundant data
                unset(
                    $mission['page'],
                    $mission['index'],
                    $mission['locks'],
                    $mission['traits']
                );

                if (MissionModel::SPACE_BATTLE === $mission['type']) {
                    $missions[] = $mission;
                    return;
                }

                // flatten steps array
                $this->flattenSteps($mission);
                $missions[] = $mission;
            }
        );

        return [$episodes, $missions];
    }//end export()

    /**
     * Iterate through all missions and call the given function.
     * The callback is defined as:
     * function(Models\Mission $mission, int $index, int $episode, string $type)
     * - `$mission` is a mission model instance,
     * - `$index` is the index number within the episode
     * - `$episode` is the index of the episode
     * - `$type` can be either `'epissode'` or `'cadet'`
     *
     * @param callable $func
     */
    public function each(callable $func): void
    {
        foreach ($this->list as $type => $episodes) {
            foreach ($episodes as $episode => $missions) {
                foreach ($missions as $index => $mission) {
                    /* @var Mission $mission */
                    call_user_func_array(
                        $func,
                        [$mission->get(), $index, $episode, $type]
                    );
                }//end foreach
            }//end foreach
        }//end foreach
    }//end each()

    /**
     * Fetch the list through API
     *
     * @return string[][]
     */
    private function fetchMissionList(): array
    {
        $this->parse->resetOptions();
        $this->parse->page('Missions');
        $content = $this->parse->get(true)['wikitext']['*'];
        $offset = strpos($content, 'Cadet Challenges');

        preg_match_all(
            '/^\{\{:(.+)}}$/um',
            substr($content, 0, $offset),
            $episodes
        );

        preg_match_all(
            '/^\{\{:(.+)}}$/um',
            $content,
            $cadet,
            PREG_PATTERN_ORDER,
            $offset
        );

        return compact('episodes', 'cadet');
    }//end fetchMissionList()

    /**
     * Fetch templates through API
     *
     * @param string[][] $matches
     *
     * @return array string[]
     */
    private function fetchTemplates(array $matches): array
    {
        $text = $this->expandTemplates->get(implode('', $matches[0]), true);
        $starts = [];
        $offset = 0;
        foreach ($matches[1] as &$template) {
            $template = str_replace('_', ' ', $template);
            $regex = "/===[\\s\\[]*{$template}[\\s\\]]*===/ui";
            preg_match($regex, $text, $match, PREG_OFFSET_CAPTURE, $offset);
            $offset = $match[0][1];
            $starts[] = $offset;
        }//end foreach

        $count = count($starts) - 1;
        foreach ($matches[1] as $idx => &$template) {
            if ($idx >= $count) {
                $template = substr($text, $starts[$idx]);
            } else {
                $template = substr(
                    $text,
                    $starts[$idx],
                    $starts[$idx + 1] - $starts[$idx]
                );
            }
        }//end foreach

        return $matches[1];
    }//end fetchTemplates()

    /**
     * Parse all episodes
     *
     * @param array $episodes
     */
    private function parseEpisodes(&$episodes): void
    {
        foreach ($episodes as &$items) {
            foreach ($items as &$episode) {
                $episode = $this->parseEpisode($episode);
            }//end foreach
        }//end foreach
    }//end parseEpisodes()

    /**
     * Parse a episode wiki text
     *
     * @param string $episode
     *
     * @return Mission[]
     */
    private function parseEpisode(string $episode): array
    {
        $regex = '/\'\'\'Mission (\d+)(\w?)\'\'\'[^\[]+\[\[([^\]]+)]]/iu';
        preg_match_all($regex, $episode, $missions);
        $missions = array_map(
            function ($mission) {
                $this->parse->page($mission);

                return $this->parse->get(true)['wikitext']['*'];
            },
            $missions[3]
        );

        preg_match('/^=+\[+([^\[\]]+)]+=+/', $episode, $matches);

        return $this->parseMissions($missions, $matches[1] ?? '');
    }//end parseEpisode()

    /**
     * Process missions wiki text
     *
     * @param array  $missions
     * @param string $episode
     *
     * @return array|Mission[]
     */
    private function parseMissions(array $missions, string $episode): array
    {
        $info = [];
        $adv = 'adv:' == strtolower(substr($episode, 0, 4));
        foreach ($missions as $mission) {
            /* @var Mission $miss */
            $miss = Mission::load($mission, null, ['advanced' => $adv]);
            if ($miss) {
                if ($adv) {
                    $miss->get()->episode = $episode;
                }
                $info[] = $miss;
            }
        }//end foreach
        $this->fetchImages($info);
        return $info;
    }//end parseMissions()

    /**
     * Fetch all cadet mission eligible crew
     *
     * @param array $names
     * @param array $cadet
     */
    private function fetchCadetCrew(array $names, array $cadet)
    {
        foreach ($cadet as $episode => $missions) {
            $this->parse->page($names[$episode], 2);
            preg_match_all(
                '/{{NamePic\|([^}]+)}}/imsu',
                $this->parse->get(true)['wikitext']['*'],
                $text
            );

            $eligible = $text[1] ?? null;
            foreach ($missions as $mission) {
                /* @var Mission $mission */
                foreach ($mission->get()->steps as $step) {
                    $step['eligible'] = $eligible;
                }//end foreach
            }//end foreach
        }//end foreach
    }//end fetchCadetCrew()

    /**
     * Flatten mission steps array
     *
     * @param array $mission
     */
    private function flattenSteps(array &$mission): void
    {
        foreach ($mission['steps'] as &$step) {
            if (empty($step['locks'])) {
                unset($step['locks']);
            }

            // flatten skills array
            $skills = [];
            foreach ($step['skills'] as $idx => $skill) {
                $skills = array_merge(
                    $skills,
                    Skills::skillNames($skill['names'])
                );
                if (!empty($skill['values'])) {
                    $step['req'][$idx] = $skill['values'];
                }
            }//end foreach
            $step['skills'] = $skills;

            // flatten traits array
            $traits = [];
            foreach ($step['traits'] as $idx => $trait) {
                $traits[$idx] = $trait['names'] ?? null;
                if (!empty($trait['values'])) {
                    $step['bonus'][$idx] = $trait['values'];
                }
            }//end foreach
            $step['traits'] = $traits;
        }//end foreach
    }//end flattenSteps()

    /**
     * Fetch all image URL
     *
     * @param Mission[] $missions
     *
     * @return void
     */
    private function fetchImages(array $missions): void
    {
        foreach ($missions as $mission) {
            $mission = $mission->get();
            $mission->image = $this->query->imageInfo(
                ["File:{$mission->image['file']}" => $mission->name],
                [
                    $mission->image['size'],
                    round($mission->image['size'] * 1.5),
                    $mission->image['size'] * 2,
                ]
            )[$mission->name];

            if (MissionModel::AWAY_TEAM != $mission->type) {
                continue;
            }
            foreach ($mission->steps as $step) {
                foreach ($step->images as $idx => &$image) {
                    if (empty($image)) {
                        continue;
                    }
                    $image = $this->query->imageInfo(
                        ["File:AT-$image.png" => $step->alt[$idx]]
                    )[$step->alt[$idx]];
                }//end foreach
            }//end foreach
        }//end foreach
    }//end validateModel()
}//end class

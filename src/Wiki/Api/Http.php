<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-10
 * Time: 22:18
 */

namespace eidng8\Wiki\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;

/**
 * Http service class
 */
final class Http
{
    /**
     * Guzzle Client instance
     *
     * @var Client
     */
    private $guzzle;

    /**
     * Http constructor.
     *
     * @param string $uri
     */
    public function __construct(string $uri)
    {
        $this->createClient($uri);
    }//end createMock()

    /**
     * Create Guzzle client
     *
     * @param string $uri
     *
     * @return void
     */
    protected function createClient(string $uri): void
    {
        // merge below into one statement causes PHPUnit reporting uncovered
        $opts = ['base_uri' => $uri];

        // request options
        $opts[RequestOptions::ALLOW_REDIRECTS] = true;
        $opts[RequestOptions::CONNECT_TIMEOUT] = 60;
        $opts[RequestOptions::SYNCHRONOUS] = true;
        $opts[RequestOptions::TIMEOUT] = 300;

        // $opts[RequestOptions::DEBUG] = $this->debugMode;

        $this->guzzle = new Client($opts);
    }//end __construct()

    /**
     * Creates a mocked client that will return the given responses.
     *
     * @param array $responses
     *
     * @return self
     */
    public static function shouldRespond(array $responses): self
    {
        $instance = new static('url');
        $instance->guzzle = new Client(
            ['handler' => HandlerStack::create(new MockHandler($responses))]
        );

        return $instance;
    }//end createClient()

    /**
     * Wiki parse API
     *
     * @param $params
     *
     * @return mixed
     */
    public function parse($params)
    {
        $params['action'] = 'parse';

        return $this->get($params);
    }//end get()

    /**
     * HTTP GET
     *
     * @param $params
     *
     * @return mixed
     */
    public function get($params)
    {
        $params['format'] = 'json';

        $res = (string)$this->guzzle->get('', ['query' => $params])->getBody();

        return json_decode($res, true);
    }//end parse()

    /**
     * Wiki query API
     *
     * @param $params
     *
     * @return mixed
     */
    public function query($params)
    {
        $params['action'] = 'query';

        return $this->get($params);
    }//end parse()

    /**
     * Wiki expand templates API
     *
     * @param $params
     *
     * @return mixed
     */
    public function expandTemplates($params)
    {
        $params['action'] = 'expandtemplates';

        return $this->get($params);
    }//end expandTemplates()
}//end class

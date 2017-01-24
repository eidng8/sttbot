<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 14:34
 */

namespace eidng8\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

/**
 * Logger
 * @method static bool debug(string $msg, array $context = [])
 * @method static bool info(string $msg, array $context = [])
 * @method static bool notice(string $msg, array $context = [])
 * @method static bool warn(string $msg, array $context = [])
 * @method static bool err(string $msg, array $context = [])
 * @method static bool crit(string $msg, array $context = [])
 * @method static bool alert(string $msg, array $context = [])
 */
final class Log
{

    /**
     * Unit test standard output content
     *
     * @var TestHandler
     */
    public static $testOutput;

    /**
     * Unit test standard error content
     *
     * @var TestHandler
     */
    public static $testErrorOutput;

    /**
     * The looger instance
     *
     * @var Logger
     */
    private static $output = null;

    /**
     * The looger instance
     *
     * @var Logger
     */
    private static $error = null;

    /**
     * Log level
     *
     * @var int
     */
    private static $level = Logger::WARNING;


    public static function setLevel(int $level): void
    {
        static::$level = $level;
    }//end setLevel()


    public static function setOutputStream($stream)
    {
        static::$output = new Logger('stthb-output');
        static::$output->pushHandler(
            new StreamHandler($stream, static::$level)
        );
    }//end setOutputStream()


    public static function setErrorStream($stream)
    {
        static::$error = new Logger('stthb-error');
        static::$error->pushHandler(
            new StreamHandler($stream, static::$level)
        );
    }//end setErrorStream()


    public static function forTest()
    {
        static::$output = new Logger(
            'stthb-output',
            [static::$testOutput = new TestHandler()]
        );
        static::$error = new Logger(
            'stthb-error',
            [static::$testErrorOutput = new TestHandler()]
        );
    }//end forTest()


    public static function __callStatic(string $name, array $arguments): bool
    {
        if (empty(static::$output)) {
            static::$output = new Logger('stthb');
            static::$output->pushHandler(
                new StreamHandler('php://output', static::$level)
            );
        }

        if (empty(static::$error)) {
            static::$error = new Logger('stthb');
            static::$error->pushHandler(
                new StreamHandler('php://stderr', static::$level)
            );
        }

        $stdout = ['debug', 'info'];
        return call_user_func_array(
            [
                in_array($name, $stdout) ? static::$output : static::$error,
                $name,
            ],
            $arguments
        );
    }
}//end class

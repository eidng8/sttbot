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
 * Class Log
 *
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


    /**
     * Set log level
     *
     * @param int $level
     */
    // public static function level(int $level = Logger::WARNING)
    // {
    //     static::$level = $level;
    // }//end level()

    /**
     * Get a logger instance
     *
     * @return LoggerChannels
     */
    // public static function logger(): LoggerChannels
    // {
    //     if (empty(static::logger())) {
    //         static::$logger = new LoggerChannels(static::$level);
    //     }
    //
    //     return static::logger();
    // }//end logger()

    /**
     * Log an info
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    // public static function info(string $msg, array $context = null): bool
    // {
    //     return static::logger()->info($msg, $context);
    // }//end info()

    /**
     * Log a warning message
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    // public static function warn(string $msg, array $context = null): bool
    // {
    //     return static::logger()->warn($msg, $context);
    // }//end warn()

    /**
     * Log a error message
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    // public static function error(string $msg, array $context = null): bool
    // {
    //     return static::logger()->error($msg, $context);
    // }//end error()

    /**
     * Log a aler message
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    // public static function alert(string $msg, array $context = null): bool
    // {
    //     return static::logger()->alert($msg, $context);
    // }//end alert()

    /**
     * Log a critical error message
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    // public static function critical(string $msg, array $context = null): bool
    // {
    //     return static::logger()->critical($msg, $context);
    // }//end critical()

    /**
     * Log a debug message
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    // public static function debug(string $msg, array $context = null): bool
    // {
    //     return static::logger()->debug($msg, $context);
    // }//end debug()
}//end class

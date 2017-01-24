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

    /**
     * Set log level.
     * Please note that error log isn't affected by this setting.
     *
     * @param int $level
     */
    public static function setLevel(int $level): void
    {
        static::$level = $level;
    }//end setLevel()

    /**
     * Creates a new logger with the given stream
     *
     * @param resource $stream
     */
    public static function setOutputStream($stream)
    {
        static::$output = new Logger('sttbot-output');
        static::$output->pushHandler(
            new StreamHandler($stream, static::$level)
        );
    }//end setOutputStream()

    /**
     * Creates a new error logger with the given stream
     *
     * @param resource $stream
     */
    public static function setErrorStream($stream)
    {
        static::$error = new Logger('sttbot-error');
        static::$error->pushHandler(
            new StreamHandler($stream, Logger::ERROR)
        );
    }//end setErrorStream()

    /**
     * Setup test logger.
     * All outputs are store in {@see $testOutput} and {@see $testErrorOutput}
     */
    public static function forTest()
    {
        static::$output = new Logger(
            'sttbot-output',
            [static::$testOutput = new TestHandler(static::$level)]
        );
        static::$error = new Logger(
            'sttbot-error',
            [static::$testErrorOutput = new TestHandler(Logger::ERROR)]
        );
    }//end forTest()

    public static function useStdio()
    {
        static::$output = new Logger('sttbot');
        static::$output->pushHandler(
            new StreamHandler('php://output', static::$level)
        );

        static::$error = new Logger('sttbot');
        static::$error->pushHandler(
            new StreamHandler('php://stderr', static::$level)
        );
    }//end useStdio()

    /**
     * {@inheritdoc}
     */
    public static function __callStatic(string $name, array $arguments): bool
    {
        $stdout = ['debug', 'info', 'notice', 'warn'];

        return call_user_func_array(
            [
                in_array($name, $stdout) ? static::$output : static::$error,
                $name,
            ],
            $arguments
        );
    }
}//end class

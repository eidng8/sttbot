<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-23
 * Time: 08:47
 */

namespace eidng8\Log;

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Monolog wrapper
 */
final class LoggerChannels
{

    /**
     * info channel
     *
     * @var Logger
     */
    private $info;

    /**
     * warning channel
     *
     * @var Logger
     */
    private $warn;

    /**
     * error channel
     *
     * @var Logger
     */
    private $error;

    /**
     * debug channel
     *
     * @var Logger
     */
    private $debug;


    /**
     * LoggerChannels constructor.
     *
     * @param int $level
     */
    public function __construct(int $level = Logger::WARNING)
    {
        $this->info = new Logger('INFO');
        if ($level >= Logger::INFO) {
            $this->info->pushHandler(new StreamHandler(STDOUT, Logger::INFO));
        } else {
            $this->info->pushHandler(new NullHandler(Logger::INFO));
        }

        $this->warn = new Logger('warn');
        if ($level >= Logger::WARNING) {
            $this->warn->pushHandler(
                new StreamHandler(STDOUT, Logger::WARNING)
            );
        } else {
            $this->warn->pushHandler(new NullHandler(Logger::WARNING));
        }

        $this->error = new Logger('error');
        if ($level >= Logger::ERROR) {
            $this->error->pushHandler(new StreamHandler(STDOUT, Logger::ERROR));
        } else {
            $this->error->pushHandler(new NullHandler(Logger::ERROR));
        }

        $this->debug = new Logger('debug');
        if ($level >= Logger::DEBUG) {
            $this->debug->pushHandler(new StreamHandler(STDOUT, Logger::DEBUG));
        } else {
            $this->debug->pushHandler(new NullHandler(Logger::DEBUG));
        }
    }//end __construct()


    /**
     * Log to info channel
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function info(string $msg, array $context): bool
    {
        return $this->info->info($msg, $context);
    }//end info()


    /**
     * Send warning message to warning channel
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function warn(string $msg, array $context): bool
    {
        return $this->warn->warn($msg, $context);
    }//end warn()


    /**
     * Send error message to error channel
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function error(string $msg, array $context): bool
    {
        return $this->error->error($msg, $context);
    }//end error()


    /**
     * Send alert message to error channel
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function alert(string $msg, array $context): bool
    {
        return $this->error->alert($msg, $context);
    }//end alert()


    /**
     * Send critical error message to error channel
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function critical(string $msg, array $context): bool
    {
        return $this->error->critical($msg, $context);
    }//end critical()


    /**
     * Send debug message to debug channel
     *
     * @param  string $msg     The log message
     * @param  array  $context The log context
     *
     * @return bool Whether the record has been processed
     */
    public function debug(string $msg, array $context): bool
    {
        return $this->debug->debug($msg, $context);
    }//end debug()
}//end class

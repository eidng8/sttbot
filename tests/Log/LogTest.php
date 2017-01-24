<?php
/**
 * Project:  sttbot
 * File:     LogTest.php
 * Created:  2017-01-24
 *
 * PHP Version >=5.6.0
 *
 * @package   Tpages\sttbot
 * @author    Tpages <dev-master@tpages.com>
 * @copyright 2013-2014 Guangzhou Tianao Internet Technology Limited.
 *            (c) 广州市天傲互联网科技有限公司。版权所有。
 * @license   http://www.tpages.com/license Tpages License and Agreement
 *            Any person receiving this material, shall treat it as confidential
 *            and not misuse, copy, disclose, distribute or retain the material
 *           in any way that amounts to a breach of confidentiality.
 *           任何收到该资料的人员，都必须对该资料进行保密。不得通过任何可能损害其保密性的方
 *           法或途径进行保留、复制、披露、及颁发。
 * @link      http://www.tpages.com
 */

/**
 * eidng8\Tests\Log 命名空间
 */
namespace eidng8\Tests\Log;

use eidng8\Log\Log;
use eidng8\Tests\TestCase;
use Monolog\Logger;

/**
 * LogTest
 */
class LogTest extends TestCase
{
    /**
     * @var string
     */
    private $chnOut = 'sttbot-output';

    /**
     * @var string
     */
    private $chnErr = 'sttbot-error';

    /**
     * @var string
     */
    private $msg = 'test msg';

    /**
     * @var array
     */
    private $cxt = ['this' => 'test'];

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Log::setLevel(Logger::DEBUG);
    }

    public function testSetLevel()
    {
        Log::setLevel(Logger::NOTICE);
        Log::forTest();

        Log::debug($this->msg, $this->cxt);
        $this->assertEmpty(Log::$testOutput->getRecords());

        Log::notice($this->msg, $this->cxt);
        list($record) = Log::$testOutput->getRecords();
        $this->checkRecord($record);
        $this->assertFalse(Log::$testOutput->hasDebugRecords());
        $this->assertFalse(Log::$testOutput->hasInfoRecords());
        $this->assertFalse(Log::$testOutput->hasWarningRecords());
        $this->assertFalse(Log::$testOutput->hasErrorRecords());
        $this->assertFalse(Log::$testOutput->hasCriticalRecords());
        $this->assertFalse(Log::$testOutput->hasAlertRecords());
        $this->assertFalse(Log::$testErrorOutput->hasDebugRecords());
        $this->assertFalse(Log::$testErrorOutput->hasInfoRecords());
        $this->assertFalse(Log::$testErrorOutput->hasNoticeRecords());
        $this->assertFalse(Log::$testErrorOutput->hasWarningRecords());
        $this->assertFalse(Log::$testErrorOutput->hasErrorRecords());
        $this->assertFalse(Log::$testErrorOutput->hasCriticalRecords());
        $this->assertFalse(Log::$testErrorOutput->hasAlertRecords());
    }//end testSetLevel()

    public function testErrorLog()
    {
        Log::setLevel(Logger::ALERT);
        Log::forTest();
        Log::err($this->msg, $this->cxt);
        list($record) = Log::$testErrorOutput->getRecords();
        $this->checkRecord($record, true);
        $this->assertFalse(Log::$testOutput->hasDebugRecords());
        $this->assertFalse(Log::$testOutput->hasInfoRecords());
        $this->assertFalse(Log::$testOutput->hasWarningRecords());
        $this->assertFalse(Log::$testOutput->hasErrorRecords());
        $this->assertFalse(Log::$testOutput->hasCriticalRecords());
        $this->assertFalse(Log::$testOutput->hasAlertRecords());
        $this->assertFalse(Log::$testErrorOutput->hasDebugRecords());
        $this->assertFalse(Log::$testErrorOutput->hasInfoRecords());
        $this->assertFalse(Log::$testErrorOutput->hasNoticeRecords());
        $this->assertFalse(Log::$testErrorOutput->hasWarningRecords());
        $this->assertFalse(Log::$testErrorOutput->hasCriticalRecords());
        $this->assertFalse(Log::$testErrorOutput->hasAlertRecords());
    }//end testErrorLog()

    public function testSetOutputStream()
    {
        $stream = fopen('php://memory', 'r+');
        Log::setLevel(Logger::DEBUG);
        Log::setOutputStream($stream);
        Log::info($this->msg, $this->cxt);
        rewind($stream);
        $actual = stream_get_contents($stream);
        fclose($stream);
        $this->assertNotFalse(
            strstr($actual, 'sttbot-output.INFO: test msg {"this":"test"} []')
        );
    }//end testSetOutputStream()

    public function testSetErrorStream()
    {
        $stream = fopen('php://memory', 'r+');
        Log::setLevel(Logger::ALERT);
        Log::setErrorStream($stream);
        Log::err($this->msg, $this->cxt);
        rewind($stream);
        $actual = stream_get_contents($stream);
        fclose($stream);
        $this->assertNotFalse(
            strstr($actual, 'sttbot-error.ERROR: test msg {"this":"test"} []')
        );
    }//end testSetErrorStream()

    /**
     * @param array $record
     * @param bool  $err
     */
    private function checkRecord(array $record, bool $err = false)
    {
        $this->assertSame(
            $err ? $this->chnErr : $this->chnOut,
            $record['channel']
        );
        $this->assertSame($this->msg, $record['message']);
        $this->assertSame($this->cxt, $record['context']);
    }//end checkRecord()
}//end class

<?php
/**
 * API removeListener Unit tests for HTML_Progress class.
 *
 * @version    $Id: HTML_Progress_TestCase_removeListener.php,v 1.6 2005/08/28 15:05:20 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress
 * @ignore
 */

class HTML_Progress_TestCase_removeListener extends PHPUnit_TestCase
{
    /**
     * HTML_Progress instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress_TestCase_removeListener($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL & ~E_NOTICE);

        $logger['push_callback'] = array(&$this, '_pushCallback'); // don't die when an exception is thrown
        $this->progress = new HTML_Progress($logger);
    }

    function tearDown()
    {
        unset($this->progress);
    }

    function _stripWhitespace($str)
    {
        return preg_replace('/\\s+/', '', $str);
    }

    function _methodExists($name)
    {
        if (substr(PHP_VERSION,0,1) < '5') {
            $n = strtolower($name);
        } else {
            $n = $name;
        }
        if (in_array($n, get_class_methods($this->progress))) {
            return true;
        }
        $this->assertTrue(false, 'method '. $name . ' not implemented in ' . get_class($this->progress));
        return false;
    }

    function _pushCallback($err)
    {
        // don't die if the error is an exception (as default callback)
        return HTML_PROGRESS_ERRORSTACK_PUSH;
    }

    function _getResult()
    {
        if ($this->progress->hasErrors()) {
            $err = $this->progress->getError();
            $this->assertTrue(false, $err['message']);
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * TestCases for method removeListener.
     *
     */
    function test_removeListener_fail_no_class()
    {
        if (!$this->_methodExists('removeListener')) {
            return;
        }
        $observer = 'log_progress2';
        $monitor = $this->progress->removeListener(new $observer);

        $this->assertTrue($monitor, $observer .' is not a valid listener or is not yet attached ');
    }

    function test_removeListener()
    {
        if (!$this->_methodExists('removeListener')) {
            return;
        }
        $observer = 'logit2';
        $monitor = $this->progress->addListener(new $observer);
        $monitor = $this->progress->removeListener(new $observer);

        $this->assertTrue($monitor, $observer .' is not a valid listener or is not yet attached ');
    }
}

require_once ('HTML/Progress/observer.php');
/**
 * @ignore
 */
class logit2 extends HTML_Progress_Observer
{
    function logit2()
    {
    }
}
/**
 * @ignore
 */
class log_progress2
{
}
?>
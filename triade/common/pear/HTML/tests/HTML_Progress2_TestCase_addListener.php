<?php
/**
 * API addListener Unit tests for HTML_Progress2 class.
 *
 * @version    $Id: HTML_Progress2_TestCase_addListener.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

class HTML_Progress2_TestCase_addListener extends PHPUnit_TestCase
{
    /**
     * HTML_Progress2 instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress2_TestCase_addListener($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL & ~E_NOTICE);

        $prefs= array('push_callback' => array(&$this, '_handleError'));
        $this->progress = new HTML_Progress2($prefs);
    }

    function tearDown()
    {
        unset($this->progress);
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

    function _handleError($code, $level)
    {
        // don't die if the error is an exception (as default callback)
        return PEAR_ERROR_RETURN;
    }

    function _getResult()
    {
        if ($this->progress->hasErrors()) {
            $err = $this->progress->getError();
            $msg = $err->getMessage() . '&nbsp;&gt;&gt;';
            $this->assertTrue(false, $msg);
        } else {
            $this->assertTrue(true);
        }
    }

    /**
     * TestCases for method addListener().
     */
    function test_addListener_fail_no_class()
    {
        if (!$this->_methodExists('addListener')) {
            return;
        }
        $observer = 'logit';
        $monitor = $this->progress->addListener($observer);

        $this->assertTrue($monitor, $observer .' is not a valid listener ');
    }

    function test_addListener()
    {
        if (!$this->_methodExists('addListener')) {
            return;
        }
        $observer = 'log_progress';
        $monitor = $this->progress->addListener(new $observer);

        $this->assertTrue($monitor, $observer .' is not a valid listener ');
    }
}

require_once ('HTML/Progress2/Observer.php');
/**
 * @ignore
 */
class logit
{
}
/**
 * @ignore
 */
class log_progress extends HTML_Progress2_Observer
{
    function log_progress()
    {
    }
}
?>
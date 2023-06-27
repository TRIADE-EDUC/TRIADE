<?php
/**
 * API getDM Unit tests for HTML_Progress class.
 *
 * @version    $Id: HTML_Progress_TestCase_getDM.php,v 1.6 2005/08/28 14:12:09 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress
 * @ignore
 */

class HTML_Progress_TestCase_getDM extends PHPUnit_TestCase
{
    /**
     * HTML_Progress instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress_TestCase_getDM($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL);

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
     * TestCases for method getDM.
     *
     */
    function test_getDM_fail()
    {
        if (!$this->_methodExists('getDM')) {
            return;
        }
        $dm =& $this->progress->getDM();

        $this->assertEquals($this, $dm, 'Wrong DM instance;');
    }

    function test_getDM()
    {
        if (!$this->_methodExists('getDM')) {
            return;
        }
        $dm =& $this->progress->getDM();

        $this->assertEquals($this->progress->_DM, $dm);
    }
}
?>
<?php
/**
 * API setCellCoordinates Unit tests for HTML_Progress_UI class.
 *
 * @version    $Id: HTML_Progress_TestCase_UI_setCellCoordinates.php,v 1.4 2005/08/28 14:12:09 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress
 * @ignore
 */

class HTML_Progress_TestCase_UI_setCellCoordinates extends PHPUnit_TestCase
{
    /**
     * HTML_Progress instance
     *
     * @var        object
     */
    var $progress;
    var $ui;

    function HTML_Progress_TestCase_UI_setCellCoordinates($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL & ~E_NOTICE);

        $logger['push_callback'] = array(&$this, '_pushCallback'); // don't die when an exception is thrown
        $this->progress = new HTML_Progress($logger);
        $this->ui =& $this->progress->getUI();
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
        if (in_array($n, get_class_methods($this->ui))) {
            return true;
        }
        $this->assertTrue(false, 'method '. $name . ' not implemented in ' . get_class($this->ui));
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
     * TestCases for method setCellCoordinates.
     *
     */
    function test_setCellCoordinates_fail_no_integer()
    {
        if (!$this->_methodExists('setCellCoordinates')) {
            return;
        }
        $this->ui->setCellCoordinates('',2);
        $this->_getResult();
    }

    function test_setCellCoordinates_fail_no_positive()
    {
        if (!$this->_methodExists('setCellCoordinates')) {
            return;
        }
        $this->ui->setCellCoordinates(-1,4);
        $this->_getResult();
    }

    function test_setCellCoordinates_fail_too_small()
    {
        if (!$this->_methodExists('setCellCoordinates')) {
            return;
        }
        $this->ui->setCellCoordinates(1,2);
        $this->_getResult();
    }

    function test_setCellCoordinates()
    {
        if (!$this->_methodExists('setCellCoordinates')) {
            return;
        }
        $this->ui->setCellCoordinates(5,5);
        $this->_getResult();
    }
}
?>
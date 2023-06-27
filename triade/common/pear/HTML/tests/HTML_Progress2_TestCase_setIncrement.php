<?php
/**
 * API setIncrement Unit tests for HTML_Progress2 class.
 *
 * @version    $Id: HTML_Progress2_TestCase_setIncrement.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

class HTML_Progress2_TestCase_setIncrement extends PHPUnit_TestCase
{
    /**
     * HTML_Progress2 instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress2_TestCase_setIncrement($name)
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
     * TestCases for method setIncrement().
     */
    function test_setIncrement_fail_no_integer()
    {
        if (!$this->_methodExists('setIncrement')) {
            return;
        }
        $this->progress->setIncrement('1');
        $this->_getResult();
    }

    function test_setIncrement_fail_zero()
    {
        if (!$this->_methodExists('setIncrement')) {
            return;
        }
        $this->progress->setIncrement(0);
        $this->_getResult();
    }

    function test_setIncrement()
    {
        if (!$this->_methodExists('setIncrement')) {
            return;
        }
        $this->progress->setIncrement(5);
        $this->_getResult();
    }
}
?>
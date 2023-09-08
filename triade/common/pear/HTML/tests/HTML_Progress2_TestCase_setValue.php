<?php
/**
 * API setValue Unit tests for HTML_Progress2 class.
 *
 * @version    $Id: HTML_Progress2_TestCase_setValue.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

class HTML_Progress2_TestCase_setValue extends PHPUnit_TestCase
{
    /**
     * HTML_Progress2 instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress2_TestCase_setValue($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL & ~E_NOTICE);

        $prefs= array('push_callback' => array(&$this, '_handleError'));
        $this->progress = new HTML_Progress2($prefs, HTML_PROGRESS2_BAR_HORIZONTAL, 10, 100);
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
     * TestCases for method setValue().
     */
    function test_setValue_fail_no_integer()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->progress->setValue('25');
        $this->_getResult();
    }

    function test_setValue_fail_less_than_min()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->progress->setValue(1);
        $this->_getResult();
    }

    function test_setValue_fail_greater_than_max()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->progress->setValue(200);
        $this->_getResult();
    }

    function test_setValue()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->progress->setValue(15);
        $this->_getResult();
    }
}
?>
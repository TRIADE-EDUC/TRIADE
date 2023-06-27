<?php
/**
 * API setMaximum Unit tests for HTML_Progress2 class.
 *
 * @version    $Id: HTML_Progress2_TestCase_setMaximum.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

class HTML_Progress2_TestCase_setMaximum extends PHPUnit_TestCase
{
    /**
     * HTML_Progress2 instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress2_TestCase_setMaximum($name)
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
     * TestCases for method setMaximum().
     */
    function test_setMaximum_fail_no_integer()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->progress->setMaximum('100');
        $this->_getResult();
    }

    function test_setMaximum_fail_no_positive()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->progress->setMaximum(-1);
        $this->_getResult();
    }

    function test_setMaximum_fail_less_min()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->progress->setMaximum(1);
        $this->_getResult();
    }

    function test_setMaximum()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->progress->setMaximum(10);
        $this->_getResult();
    }
}
?>
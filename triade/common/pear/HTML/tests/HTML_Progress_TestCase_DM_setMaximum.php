<?php
/**
 * API setMaximum Unit tests for HTML_Progress_DM class.
 *
 * @version    $Id: HTML_Progress_TestCase_DM_setMaximum.php,v 1.4 2005/08/28 14:12:09 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress
 * @ignore
 */

class HTML_Progress_TestCase_DM_setMaximum extends PHPUnit_TestCase
{
    /**
     * HTML_Progress instance
     *
     * @var        object
     */
    var $progress;
    var $dm;

    function HTML_Progress_TestCase_DM_setMaximum($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL & ~E_NOTICE);

        $logger['push_callback'] = array(&$this, '_pushCallback'); // don't die when an exception is thrown
        $this->progress = new HTML_Progress($logger);
        $this->dm =& $this->progress->getDM();
        $this->dm->setMinimum(10);
        $this->dm->setMaximum(100);
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
        if (in_array($n, get_class_methods($this->dm))) {
            return true;
        }
        $this->assertTrue(false, 'method '. $name . ' not implemented in ' . get_class($this->dm));
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
     * TestCases for method setMaximum.
     *
     */
    function test_setMaximum_fail_no_integer()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->dm->setMaximum('');
        $this->_getResult();
    }

    function test_setMaximum_fail_no_positive()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->dm->setMaximum(-1);
        $this->_getResult();
    }

    function test_setMaximum_fail_less_min()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->dm->setMaximum(1);
        $this->_getResult();
    }

    function test_setMaximum()
    {
        if (!$this->_methodExists('setMaximum')) {
            return;
        }
        $this->dm->setMaximum(10);
        $this->_getResult();
    }
}
?>
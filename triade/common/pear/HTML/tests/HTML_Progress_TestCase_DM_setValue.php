<?php
/**
 * API setValue Unit tests for HTML_Progress_DM class.
 *
 * @version    $Id: HTML_Progress_TestCase_DM_setValue.php,v 1.4 2005/08/28 14:12:09 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress
 * @ignore
 */

class HTML_Progress_TestCase_DM_setValue extends PHPUnit_TestCase
{
    /**
     * HTML_Progress instance
     *
     * @var        object
     */
    var $progress;
    var $dm;

    function HTML_Progress_TestCase_DM_setValue($name)
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
     * TestCases for method setValue.
     *
     */
    function test_setValue_fail_no_integer()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->dm->setValue('');
        $this->_getResult();
    }

    function test_setValue_fail_less_than_min()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->dm->setValue(1);
        $this->_getResult();
    }

    function test_setValue_fail_greater_than_max()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->dm->setValue(200);
        $this->_getResult();
    }

    function test_setValue()
    {
        if (!$this->_methodExists('setValue')) {
            return;
        }
        $this->dm->setValue(15);
        $this->_getResult();
    }
}
?>
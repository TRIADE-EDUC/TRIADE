<?php
/**
 * API setValue Unit tests for HTML_Progress_DM class.
 *
 * @version    $Id: HTML_Progress_TestCase_DM_new.php,v 1.2 2005/08/28 14:12:09 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress
 * @ignore
 */

class HTML_Progress_TestCase_DM_new extends PHPUnit_TestCase
{
    /**
     * HTML_Progress instance
     *
     * @var        object
     */
    var $progress;
    var $dm;

    function HTML_Progress_TestCase_DM_new($name)
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
    function test_new_fail_no_minimum()
    {
        $this->dm = new HTML_Progress_DM('nomin',1000);
        $this->_getResult();
    }

    function test_new_fail_no_maximum()
    {
        $this->dm = new HTML_Progress_DM(0,'nomax');
        $this->_getResult();
    }

    function test_new_fail_no_increment()
    {
        $this->dm = new HTML_Progress_DM(0,50,'noinc');
        $this->_getResult();
    }

    function test_new()
    {
        $this->dm = new HTML_Progress_DM(0,200,10);
        $this->_getResult();
    }
}
?>
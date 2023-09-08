<?php
/**
 * API setCellCount Unit tests for HTML_Progress2 class.
 *
 * @version    $Id: HTML_Progress2_TestCase_setCellCount.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

class HTML_Progress2_TestCase_setCellCount extends PHPUnit_TestCase
{
    /**
     * HTML_Progress2 instance
     *
     * @var        object
     */
    var $progress;

    function HTML_Progress2_TestCase_setCellCount($name)
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
     * TestCases for method setCellCount().
     */
    function test_setCellCount_fail_no_integer()
    {
        if (!$this->_methodExists('setCellCount')) {
            return;
        }
        $this->progress->setCellCount('20');
        $this->_getResult();
    }

    function test_setCellCount_fail_less_0()
    {
        if (!$this->_methodExists('setCellCount')) {
            return;
        }
        $this->progress->setCellCount(-1);
        $this->_getResult();
    }

    function test_setCellCount_horizontal_valid_width()
    {
        if (!$this->_methodExists('setCellCount')) {
            return;
        }
        $this->progress->setCellCount(1);
        $data = $this->progress->toArray();

        $this->assertEquals(19, $data['progress']['width'],
            'default-size HORIZONTAL-1-cell no-border : w=19 h=24.');
    }

    function test_setCellCount_vertical_valid_height()
    {
        if (!$this->_methodExists('setCellCount')) {
            return;
        }
        $this->progress->setOrientation(HTML_PROGRESS2_BAR_VERTICAL);
        $this->progress->setCellCount(2);
        $data = $this->progress->toArray();

        $this->assertEquals(36, $data['progress']['height'],
            'default-size VERTICAL-2-cells no-border : w=24 h=36.');
    }

    function test_setCellCount()
    {
        if (!$this->_methodExists('setCellCount')) {
            return;
        }
        $this->progress->setCellCount(16);

        $this->assertFalse($this->errorThrown, 'error thrown');
    }
}
?>
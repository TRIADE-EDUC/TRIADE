<?php
/**
 * Unit tests for HTML_Template_Sigma
 * 
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_Template_Sigma
 * @author      Alexey Borzov <avb@php.net>
 * @copyright   2001-2007 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id: Console_TestListener.php,v 1.2 2007/05/19 13:31:19 avb Exp $
 * @link        http://pear.php.net/package/HTML_Template_Sigma
 * @ignore
 */

/**
 * Test listener for console
 *
 * Borrowed this from MDB package, don't know who is the original author
 *
 * @category    HTML
 * @package     HTML_Template_Sigma
 * @version     1.1.6
 * @ignore
 */
class Console_TestListener extends PHPUnit_TestListener {
    function addError(&$test, &$t) {
        $this->_errors += 1;
        echo " Error $this->_errors in " . $test->getName() . " : $t\n";
    }

    function addFailure(&$test, &$t) {
        $this->_fails += 1;
        if ($this->_fails == 1) {
            echo "\n";
        }
        echo "Failure $this->_fails : $t\n";
    }

    function endTest(&$test) {
        if ($this->_fails == 0 && $this->_errors == 0) {
            echo ' Test passed';
        } else {
            echo "There were $this->_fails failures for " . $test->getName() . "\n";
            echo "There were $this->_errors errors for " . $test->getName() . "\n";
        }
        echo "\n";
    }

    function startTest(&$test) {
        $this->_fails = 0;
        $this->_errors = 0;
        echo get_class($test) . " : Starting " . $test->getName() .  " ...";
    }
}
?>
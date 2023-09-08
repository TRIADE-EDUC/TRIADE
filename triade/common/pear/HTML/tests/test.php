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
 * @version     CVS: $Id: test.php,v 1.5 2007/05/19 13:31:19 avb Exp $
 * @link        http://pear.php.net/package/HTML_Template_Sigma
 * @ignore
 */

/**
 * Class for file / directory manipulation from PEAR package
 */ 
require_once 'System.php';

$Sigma_cache_dir = System::mktemp('-d sigma');

// What class are we going to test?
// It is possible to also use the unit tests to test HTML_Template_ITX, which
// also implements Integrated Templates API
$IT_class = 'Sigma';
// $IT_class = 'ITX';

// Sigma_cache_testcase is useless if testing HTML_Template_ITX
$testcases = array(
    'Sigma_api_testcase',
    'Sigma_cache_testcase',
    'Sigma_usage_testcase',
    'Sigma_bug_testcase'
);

if (@file_exists('../' . $IT_class . '.php')) {
    require_once '../' . $IT_class . '.php';
} else {
    require_once 'HTML/Template/' . $IT_class . '.php';
}

require_once 'PHPUnit.php';

$suite =& new PHPUnit_TestSuite();

foreach ($testcases as $testcase) {
    include_once $testcase . '.php';
    $methods = preg_grep('/^test/i', get_class_methods($testcase));
    foreach ($methods as $method) {
        $suite->addTest(new $testcase($method));
    }
}

require_once './Console_TestListener.php';
$result =& new PHPUnit_TestResult();
$result->addListener(new Console_TestListener);

$suite->run($result);
?>

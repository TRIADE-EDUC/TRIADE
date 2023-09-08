<?php
/**
 * HTML output for PHPUnit suite tests.
 *
 * @version    $Id: index.php,v 1.3 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

require_once 'TestUnit.php';
require_once 'HTML_TestListener.php';
require_once 'HTML/Progress2.php';

$title = 'PhpUnit test run, HTML_Progress2 class';
?>
<html>
<head>
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="styles.css" type="text/css" />
</head>
<body>
<h1><?php echo $title; ?></h1>
<p>
This page runs all the phpUnit self-tests, and produces nice HTML output.
</p>
<p>
Unlike typical test run, <strong>expect many test cases to
fail</strong>.  Exactly those with <code>pass</code> in their name
should succeed.
</p>
<p>
For each test we display both the test result -- <span
class="Pass">ok</span>, <span class="Failure">FAIL</span>, or
<span class="Error">ERROR</span> -- and also a meta-result --
<span class="Expected">as expected</span>, <span
class="Unexpected">UNEXPECTED</span>, or <span
class="Unknown">unknown</span> -- that indicates whether the
expected test result occurred.  Although many test results will
be 'FAIL' here, all meta-results should be 'as expected', except
for a few 'unknown' meta-results (because of errors) when running
in PHP3.
</p>

<h2>Tests</h2>
<?php
$testcases = array(
    'HTML_Progress2_TestCase_setIndeterminate',
    'HTML_Progress2_TestCase_setBorderPainted',
    'HTML_Progress2_TestCase_setMinimum',
    'HTML_Progress2_TestCase_setMaximum',
    'HTML_Progress2_TestCase_setIncrement',
    'HTML_Progress2_TestCase_setValue',
    'HTML_Progress2_TestCase_moveStep',
    'HTML_Progress2_TestCase_getPercentComplete',
    'HTML_Progress2_TestCase_setOrientation',
    'HTML_Progress2_TestCase_setFillWay',
    'HTML_Progress2_TestCase_setCellCount',
    'HTML_Progress2_TestCase_getCellAttributes',
    'HTML_Progress2_TestCase_setCellAttributes',
    'HTML_Progress2_TestCase_setCellCoordinates',
    'HTML_Progress2_TestCase_getBorderAttributes',
    'HTML_Progress2_TestCase_getFrameAttributes',
    'HTML_Progress2_TestCase_setFrameAttributes',
    'HTML_Progress2_TestCase_getLabelAttributes',
    'HTML_Progress2_TestCase_setLabelAttributes',
    'HTML_Progress2_TestCase_addLabel',
    'HTML_Progress2_TestCase_removeLabel',
    'HTML_Progress2_TestCase_getProgressAttributes',
    'HTML_Progress2_TestCase_setScript',
    'HTML_Progress2_TestCase_drawCircleSegments',
    'HTML_Progress2_TestCase_setAnimSpeed',
    'HTML_Progress2_TestCase_setProgressHandler',
//    'HTML_Progress2_TestCase_addListener',
//    'HTML_Progress2_TestCase_removeListener'
);

$suite = new PHPUnit_TestSuite();

foreach ($testcases as $testcase) {
    include_once $testcase . '.php';
    $suite->addTestSuite($testcase);
}

$listener = new HTML_TestListener();
$result = TestUnit::run($suite, $listener);
$result->removeListener($listener);
$result->report();
?>
</body>
</html>
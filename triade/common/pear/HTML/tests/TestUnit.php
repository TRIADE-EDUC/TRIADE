<?php
/**
 * TestUnit runs a TestSuite and returns a TestResult object.
 * And more than PHPUnit attach a listener to TestResult.
 *
 * @version    $Id: TestUnit.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @ignore
 */

require_once 'PHPUnit.php';

/**
 * @ignore
 */
class TestUnit extends PHPUnit
{
    function &run(&$suite, $listener) {
        $result = new TestResult();
    $result->addListener($listener);
        $suite->run($result);

        return $result;
    }
}

/**
 * @ignore
 */
class TestResult extends PHPUnit_TestResult
{
    /* report result of test run */
    function report()
    {
        echo "</TABLE>";

        $nRun = $this->runCount();
        $nErrors = $this->errorCount();
        $nFailures = $this->failureCount();
        echo "<h2>Summary</h2>";

        printf("<p>%s test%s run.<br>", $nRun, ($nRun > 1) ? 's' : '');
        printf("%s error%s.<br>\n", $nErrors, ($nErrors > 1) ? 's' : '');
        printf("%s failure%s.<br>\n", $nFailures, ($nFailures > 1) ? 's' : '');
        if ($nFailures > 0) {
            echo "<h2>Failure Details</h2>";
                print("<ol>\n");
                $failures = $this->failures();
                while (list($i, $failure) = each($failures)) {
                    $failedTest = $failure->failedTest();
                    printf("<li>%s\n", $failedTest->getName() );
                    print("<ul>");
                    printf("<li>%s\n", $failure->thrownException() );
                    print("</ul>");
                }
                print("</ol>\n");
        }
    }
}
?>
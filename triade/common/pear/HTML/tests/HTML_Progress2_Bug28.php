<?php
/**
 * BUG #28 regression test for HTML_Progress2 class.
 *
 * @version    $Id: HTML_Progress2_Bug28.php,v 1.2 2005/08/18 09:40:39 farell Exp $
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    HTML_Progress2
 * @link       http://pear.php.net/bugs/bug.php?id=28
 * @ignore
 */

error_reporting(E_ALL);

function php_error_handler($errno, $errstr, $errfile, $errline)
{
    die("<b>myhandler</b> $errstr in $errfile at line $errline");
}
set_error_handler('php_error_handler');

require_once 'HTML/Progress2.php';
$pkg = array('PEAR', 'Config');
$bar = new HTML_Progress2();
$bar->setAnimSpeed(1000);
$bar->setIncrement(50);
?>
<style type="text/css">
<!--
body {
    background-color: lightgrey;
    font-family: Verdana, Arial;
}
<?php echo $bar->getStyle(); ?>
// -->
</style>
<script type="text/javascript">
<!--
<?php echo $bar->getScript(); ?>
//-->
</script>
<?php
echo $bar->toHtml();

for ($i=0; $i<10; $i++) {
    $bar->display();
    echo "installing package ... : ". $pkg[$i] ."<br /> \n";
    $bar->sleep();
    $bar->incValue();
}
?>
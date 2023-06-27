<?php
session_start();
echo '<html><head></head><body>';
if ((isset($_SESSION['name_dokeos'])) && (isset($_SESSION['passwd_dokeos'])))
{
	$url =  base64_encode('name=' . $_SESSION['name_dokeos'] . '&pwd='. $_SESSION['passwd_dokeos']);
	echo '<script language=Javascript>window.location.replace("./dokeos/index.php?'. $url .'");</script>';
}
else
	echo '<script language=Javascript>window.location.replace("./dokeos/index.php");</script>';
echo '</body></html>';
?>
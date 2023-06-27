<?php
include_once("./common/productid.php");
$id=PRODUCTID;
if (file_exists("./common/config.centralStageClient.php")) {
	include_once("./common/config.centralStageClient.php");
	$pass=PASSCENTRALSTAGE;
	$url=URLCENTRALSTAGE."/$url?id=$id&p=$pass";
}
?>

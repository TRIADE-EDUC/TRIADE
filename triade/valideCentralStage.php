<?php
session_start();
if ($_SESSION["membre"] == "menuadmin") {
	@unlink("./common/config.centralStage.php");
	$f=fopen("./common/config.centralStage.php","w");
	fwrite($f,"<?php\n");
	fwrite($f,"define(\"CENTRALSTAGE\",\"oui\");\n");
	fwrite($f,"?>\n");
        fclose($f);
}
sleep(1);
?>

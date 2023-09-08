<?php
if ($_SESSION["membre"] == "menuadmin") {

	clearstatcache();
	$date=date("d-m-Y H:i", filemtime("./updateBase.php"));

	if (file_exists("data/updateBase.log")) {
		$fp = fopen("data/updateBase.log", "r");
		$dateLog=fread($fp,filesize("data/updateBase.log"));
		fclose($fp);
	}

	if (($date != $dateLog) || (trim($dateLog) == "")) {

	// pour Moodle
	$cnx=cnx();
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='14'";
	execSql($sql);
	$host=HOST;
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (14, 'auth/db', 'host', '$host');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='15'";
	execSql($sql);
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (15, 'auth/db', 'type', 'mysql');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='16'";
	execSql($sql);
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (16, 'auth/db', 'sybasequoting', '0');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='17'";
	execSql($sql);
	$db=DB;
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (17, 'auth/db', 'name', '$db');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='18'";
	execSql($sql);
	$user=USER;
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (18, 'auth/db', 'user', '$user');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='19'";
	execSql($sql);
	$mdp=PWD;
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (19, 'auth/db', 'pass', '$mdp');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='20'";
	execSql($sql);
	$table=PREFIXE.'eleves';
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (20, 'auth/db', 'table', '$table');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='21'";
	execSql($sql);
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (21, 'auth/db', 'fielduser', 'nom');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='22'";
	execSql($sql);
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (22, 'auth/db', 'fieldpass', 'mdp_moodle');";
	execSql($sql);
	$sql="DELETE FROM `mdl_config_plugins` WHERE id='23'";
	execSql($sql);
	$sql="INSERT INTO `mdl_config_plugins` (`id`, `plugin`, `name`, `value`) VALUES (23, 'auth/db', 'passtype', 'md5');";
	execSql($sql);
	// fin de moodle

	$date=date("d-m-Y H:i", filemtime("./updateBase.php"));
	$fp = fopen("data/updateBase.log", "w");
	fwrite($fp,"$date");
	fclose($fp);

	Pgclose();


	}
}
?>

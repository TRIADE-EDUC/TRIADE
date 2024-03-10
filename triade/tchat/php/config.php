<?php
  if (file_exists('../../common/config.inc.php')) include_once('../../common/config.inc.php');
  if (file_exists('../common/config.inc.php')) include_once('../common/config.inc.php');
  if (file_exists('./common/config.inc.php')) include_once('./common/config.inc.php');
  $hostname = HOST;
  $username = USER;
  $password = PWD;
  $dbname = DB ;

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){ echo "Problème de connexion"; }
?>

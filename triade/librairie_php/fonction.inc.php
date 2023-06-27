<?php

include_once("./common/config.inc.php");


function db_connect()	//retourne l'identifiant de connexion ou FAUX
{
	
  $machine=HOST;
  $login=USER;
  $password=PWD;
  $db = mysqli_connect($machine,$login,$password) or die('Erreur de connexion avec la Base de Donnée');  // connexion à la base 
  if ($db && mysqli_select_db($db,DB) )
		return($db); 
  else
	  echo "Erreur de connexion avec la Base de Donnée";
}

?>

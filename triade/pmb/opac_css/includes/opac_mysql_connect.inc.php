<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_mysql_connect.inc.php,v 1.14 2017-07-31 13:21:40 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// pour éviter de déclarer les choses deux fois, on le fait
// seulement si la constante DB_CONNECT n'est pas définie

// attention : on oublie pas le pmb_mysql_close() dans le script PHP appelant !

if ( ! defined( 'DB_CONNECT' ) ) {
  define( 'DB_CONNECT', 1 );

	// connection MySQL et sélection base
	// aucun paramètre n'est obligatoire :

	// connection_mysql() fonctionne et utilise les variables des define
	// dans ce cas, les erreurs sont renvoyées

	// connection_mysql(1, 'base', 1, 1) ->
	// 1er paramètre (0 ou 1) : affiche un message d'erreur et die
	// si erreur à la connection. Mettre à 0 pour désactiver
	// 2nd paramètre : nom de la base de donnée
	// si vide, on utilise la variable définie dans DATA_BASE
	// 3ème paramètre (0 ou 1) : active ou désactive la sélection
	// d'une base (y compris celle définie dans DATA_BASE)
	// 4ème paramètre : active ou désactive le retour d'erreur qui coupe
	// le script quand la base n'est pas valide

	//inclusion du fichier des fonctions mysql
	include_once($include_path."/mysql_functions.inc.php");
  
	function connection_mysql($er_connec=1, $my_bd='', $bd=1, $er_bd=1) 	{
		global $opac_nb_documents;
		global $charset, $SQL_MOTOR_TYPE;
		global $charset, $SQL_MOTOR_TYPE, $time_zone, $time_zone_mysql;
		if(isset($time_zone) && trim($time_zone)) date_default_timezone_set($time_zone);//Pour l'heure PHP
		$my_connec = @pmb_mysql_connect(SQL_SERVER, USER_NAME, USER_PASS);
		if($my_connec===0 && $er_connec==1) die(my_error(0));
		if($bd) {
			$my_bd == '' ? $my_bd=DATA_BASE : $my_bd;
			if( pmb_mysql_select_db($my_bd, $my_connec)==0 && $er_bd==1 ) die(my_error(0));
		}
		$opac_nb_documents=(@pmb_mysql_result(pmb_mysql_query("select count(*) from notices",$my_connec),0,0))*1;

		if ($charset=='utf-8') pmb_mysql_query("set names utf8 ", $my_connec);
		else pmb_mysql_query("set names latin1 ", $my_connec);
		
		if ($SQL_MOTOR_TYPE) pmb_mysql_query("set storage_engine=$SQL_MOTOR_TYPE", $my_connec);
		if (isset($time_zone_mysql) && trim($time_zone_mysql)) pmb_mysql_query("SET time_zone = $time_zone_mysql",$my_connec);//Pour l'heure MySQL
		return $my_connec;
	}

	// fonction de gestion des erreurs de connection.
	// my_error(); ou my_error(1); affichent le numéro et la
	// description de la dernière erreur MySQL.
	// $erreur = my_error(0) stocke dans $erreur la chaîne
	// contenant le numéro et la description de la dernière
	// erreur MySQL.

	function my_error($echo=1) {
		if(!pmb_mysql_errno())
			return "";
		$erreur = 'erreur '.pmb_mysql_errno().' : '.pmb_mysql_error().'<br />';
		trigger_error($erreur, E_USER_ERROR);
		if($echo) echo $erreur;
		else {
			trigger_error($erreur, E_USER_ERROR);
			return $erreur;
		}
	}

}

?>

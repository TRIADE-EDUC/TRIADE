<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: genere_dataref.php,v 1.1 2021/04/29 08:39:10 dbellamy Exp $

// Ce script permet de générer le fichier pmb/tables/dataref.inc.php.
// Ce fichier reprend la liste des index des tables de PMB et est utilisé en :
// Administration >> Outils >> Maintenance MySQL >> Vérifier la présence des index sur les tables

// prevents script access
if(preg_match('/genere_dataref\.php/', $_SERVER['REQUEST_URI'])) {
	include('../includes/forbidden.inc.php');
	forbidden();
}

$host = 'localhost';
$user = 'bibli';
$pwd = 'bibli';
$db = "bibli";
$pmb_version = "7.4";
$out = "./dataref.inc.php";

$mysqli = new mysqli($host, $user, $pwd, $db);;
if (mysqli_connect_errno()) {
	printf("Echec de la connexion : %s\n", mysqli_connect_error());
	exit();
}
$mysqli->set_charset('utf8');

$r = $mysqli->query("select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version'");
$pmb_db_version = $r->fetch_all(MYSQLI_NUM)[0][0];
$r->free();

$r = $mysqli->query("select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_subversion'");
$pmb_db_subversion = $r->fetch_all(MYSQLI_NUM)[0][0];
$r->free();

$r = $mysqli->query("show tables");
if(false === $r) {
	printf("Erreur : %s\n", mysqli_connect_error());
	exit();
}
$tables = $r->fetch_all(MYSQLI_NUM);
$r->free();
//print_r($tables);

$indexes = [];
foreach($tables as $table) {	
	$table_name = $table['0'];
	$r = $mysqli->query("show index from ".$table_name);
	if(false !== $r) {
		$indexes[$table_name] = $r->fetch_all(MYSQLI_ASSOC);
	} else {
		printf("Erreur : %s\n", mysqli_connect_error());
	}
	$r->free();
}
//print_r($indexes);

$dt =new DateTime();
$data = '<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: genere_dataref.php,v 1.1 2021/04/29 08:39:10 dbellamy Exp $

// References des index sur les tables

// prevents direct script access
if (stristr($_SERVER[\'REQUEST_URI\'], ".inc.php")) die("no access");

// PMB version : '.$pmb_version.'
// PMB database version : '.$pmb_db_version.'
// PMB database subversion : '.$pmb_db_subversion.'

// Generated from DATABASE bibli on '.$dt->format('Y-m-d H:i:s').'

global $tabindexref;

';

file_put_contents($out, $data);

foreach($indexes as $table_name => $table_indexes) {
	file_put_contents($out, PHP_EOL.PHP_EOL."//  ###################### ".$table_name.PHP_EOL, FILE_APPEND);
	foreach($table_indexes as $index) {
		$data = '$tabindexref["'.$table_name.'"]["'.$index['Key_name'].'"][]="'.$index['Column_name'].'";'.PHP_EOL;
		file_put_contents($out, $data, FILE_APPEND);
	}
}

$mysqli->close();


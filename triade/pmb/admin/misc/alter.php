<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter.php,v 1.21 2019-06-05 13:13:19 btafforeau Exp $

// définition du minimum nécéssaire 
$base_path="../..";                            
$base_auth = "";  
$base_title = "";
require_once ("$base_path/includes/init.inc.php");  

function form_relance_auto ($maj_suivante="lancement", $etape="0", $nb_etapes) {

	global $msg;
	global $current_module;
	
	$dummy="<form class='form-$current_module' NAME=\"majbase\" METHOD=\"post\" ACTION=\"alter.php\">";
	$dummy.="<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"alter\">";
	$dummy.="<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"\">";
	$dummy.="<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"".$maj_suivante."\">";
	$dummy.="<INPUT NAME=\"etape\" TYPE=\"hidden\" value=\"".$etape."\">";
	$dummy.="<div class='erreur'>Patientez...</div>";
	$dummy.="<br />".$msg['alter_etape'].$etape." / ".$nb_etapes."<br />";
	$dummy.="</FORM>";
	$dummy.="<SCRIPT>setTimeout(\"document.majbase.submit()\",10);</SCRIPT>";
	return $dummy;
	}

function form_relance ($maj_suivante="lancement") {

	global $msg;
	global $current_module;
	
	$dummy="<form class='form-$current_module' NAME=\"majbase\" METHOD=\"post\" ACTION=\"alter.php\">";
	$dummy.="<INPUT NAME=\"categ\" TYPE=\"hidden\" value=\"alter\">";
	$dummy.="<INPUT NAME=\"sub\" TYPE=\"hidden\" value=\"\">";
	$dummy.="<INPUT NAME=\"action\" TYPE=\"hidden\" value=\"".$maj_suivante."\">";
	$dummy.="<br /><br /><a href=\"alter.php?categ=alter&sub=&action=".$maj_suivante."\">".$msg[1802]."</a><br />";
	$dummy.="</FORM>";
	//$dummy.="<SCRIPT>setTimeout(\"document.majbase.submit()\",2000);</SCRIPT>";
	return $dummy;
}

function traite_rqt($requete="", $message="") {

	global $dbh,$charset;
	
	$retour="";
	/*if($charset == "utf-8"){ //Contrairement au addon ce n'est pas à faire car dans les fichiers alter_vX.inc.php on fait un set names latin1
		$requete=utf8_encode($requete);
	}*/
	$res = pmb_mysql_query($requete, $dbh) ; 
	
	$erreur_no = pmb_mysql_errno();
	if (!$erreur_no) {
		$retour = "Successful";
	} else {
		switch ($erreur_no) {
			case "1060":
				$retour = "Field already exists, no problem.";
				break;
			case "1061":
				$retour = "Key already exists, no problem.";
				break;
			case "1091":
				$retour = "Object already deleted, no problem.";
				break;
			default:
				$retour = "<font color=\"#FF0000\">Error may be fatal : <i>".pmb_mysql_error()."<i></font>";
				break;
			}
	}		
	return "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($message) : $message)."</font></td><td><font size='1'>".$retour."</font></td></tr>";
}

settype ($action,"string");


/* vérification de l'existence de la table paramètres */
$query = "select count(1) from parametres ";
$req = pmb_mysql_query($query, $dbh);
if (!$req) { /* la table parametres n'existe pas... */
	$rqt = "CREATE TABLE if not exists parametres ( 
		id_param INT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT,
		type_param VARCHAR( 20 ) ,
		sstype_param VARCHAR( 20 ) ,
		valeur_param VARCHAR( 255 ) ,
		PRIMARY KEY ( id_param ) ,
		INDEX ( type_param , sstype_param ) 
		) " ;
	$res = pmb_mysql_query($rqt, $dbh) ;
}
		

$query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' ";
$req = pmb_mysql_query($query, $dbh);
if (pmb_mysql_num_rows($req) == 0) { /* la version de la base n'existe pas... */
	$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param) VALUES (0, 'pmb', 'bdd_version', 'v1.0')" ;
	$res = pmb_mysql_query($rqt, $dbh) ;
	$query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' ";
	$req = pmb_mysql_query($query, $dbh);
}

$data = pmb_mysql_fetch_array($req) ;
$version_pmb_bdd = $data['valeur_param'];

echo "<div id='contenu-frame'>";
echo "<h1>".$msg[1803]."<span class='bdd_version'>".$version_pmb_bdd."</span></h1>";  
echo "<h2>".$msg['pmb_v_db_as_it_should_be']."<span class='bdd_version'>".$pmb_version_database_as_it_should_be."</span></h2>";  

if ($action=="lancement" || !$action ) $deb_version_pmb_bdd = substr($version_pmb_bdd,0,2) ;
else $deb_version_pmb_bdd = substr($action,0,2) ;
	
switch ($deb_version_pmb_bdd) {
	case "v1":
		include ("./alter_v1.inc.php") ;
		break ;
	case "v2":
		include ("./alter_v2.inc.php") ;
		break ;
	case "v3":
		include ("./alter_v3.inc.php") ;
		break ;
	case "v4" :
		include ("./alter_v4.inc.php") ;
		break ;
	case "v5" :
		include ("./alter_v5.inc.php") ;
		break ;
}

echo "</div>";
print "</body></html>";

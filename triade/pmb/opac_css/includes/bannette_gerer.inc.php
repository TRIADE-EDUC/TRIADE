<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_gerer.inc.php,v 1.16 2019-01-16 11:51:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_show_categ_bannette && !$opac_allow_bannette_priv) die ("") ; 

// affichage du contenu d'une bannette
require_once($class_path."/bannette_abon.class.php");
require_once($base_path."/includes/bannette_func.inc.php");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;

$bannette_abon_instance = new bannette_abon(0, $id_empr);
if (isset($enregistrer) && $enregistrer=='PUB') {
	$bannette_abon_instance->save_bannette_abon($bannette_abon);
}

if (isset($enregistrer) && $enregistrer=='PRI') {
	$bannette_abon_instance->delete_bannette_abon($bannette_abon);
}

print "<div id='aut_details' class='aut_details_bannette'>\n";

if ($opac_allow_resiliation) {
	$aff = $bannette_abon_instance->gerer_abon_bannette ("PUB", "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!", "bannette-container-pub");
	if ($aff) 
		print "<h3><span>".$msg['dsi_bannette_gerer_pub']."</span></h3>\n".$aff;
	else 
		print "<h3><span>".$msg['dsi_bannette_gerer_pub']."</span></h3><br />".$msg['dsi_bannette_pub_no_alerts'];
	}
	 
if ($opac_allow_bannette_priv) {
	$aff = $bannette_abon_instance->gerer_abon_bannette ("PRI", "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!", "bannette-container-pri");
	if ($aff)
		 print "<h3><span>".$msg['dsi_bannette_gerer_priv']."</span></h3>\n".$aff ;
	else 
		print "<h3><span>".$msg['dsi_bannette_gerer_priv']."</span></h3><br />".$msg['dsi_bannette_priv_no_alerts'];
	} 

print "</div><!-- fermeture #aut_details -->\n";	
?>
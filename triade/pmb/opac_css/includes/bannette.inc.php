<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette.inc.php,v 1.20 2023/12/13 09:41:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $id_bannette, $id_empr, $begin_result_liste;
global $opac_bannette_notices_depliables, $opac_bannette_nb_liste;

$id_bannette = intval($id_bannette);

// affichage du contenu d'une bannette
require_once($base_path."/includes/bannette_func.inc.php");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;
print "<script src='./includes/javascript/tablist.js'></script>" ;
print "<div id='aut_details' class='aut_details_bannette'>\n";
$bannette = new bannette($id_bannette);
if ($id_bannette){
    print common::format_title($bannette->get_render_comment_public(), true)."<br />";

	//print common::format_title($bannette->get_render_comment_public())."<br />";
	$aff = pmb_bidi(affiche_bannette($id_bannette, 0, "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!")) ;
	if($aff){
		if ($opac_bannette_notices_depliables) print $begin_result_liste ;
		print $aff;
	} else {
		print $msg['empr_no_alerts'];
	}
} else {
    print common::format_hidden_title($msg['empr_menu_dsi']);
	$aff = pmb_bidi(affiche_bannettes($opac_bannette_nb_liste, "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!")) ;
	if ($opac_bannette_notices_depliables) print $begin_result_liste ;
	print $aff;
}
print "</div><!-- fermeture #aut_see -->\n";	
?>
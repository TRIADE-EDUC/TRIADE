<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id$

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $id, $cb, $expl_id;

// gestion des exemplaires
$notice = new mono_display($id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE);
print pmb_bidi("<div class='row'><b>".$notice->header."</b><br />");
print pmb_bidi($notice->isbd."</div>");

$nex = new exemplaire($cb, $expl_id,$id);

// visibilité des exemplaires
// $nex->explr_acces_autorise contient INVIS, MODIF ou UNMOD

if ($nex->explr_acces_autorise!="INVIS") {
	
	print "<div class='row'>";
	$nex->cb="";
	$nex->expl_id=0;
	print $nex->expl_form("./catalog.php?categ=expl_update&sub=create", notice::get_permalink($id));
	print "</div>";
} else {
	print "<div class='row'><div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
	print "<div class='colonne-suite'><span class='erreur'>".$msg["err_mod_expl"]."</span>&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='bouton' value=\"{$msg['bt_retour']}\" name='retour' onClick='history.back(-1);'></div></div>";	
}
?>
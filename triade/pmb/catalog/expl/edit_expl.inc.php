<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit_expl.inc.php,v 1.46 2023/07/26 15:07:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $id, $cb, $expl_id;

require_once ($class_path."/caddie/caddie_controller.class.php");
require_once ($class_path."/notice.class.php");

// gestion des exemplaires
$notice = new mono_display($id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE, '', '', '', 0, 0, 0, '', 0, false, true, 0, 0, 0, 0);
print pmb_bidi("<div class='row'><b>".$notice->header."</b><br />");
print pmb_bidi($notice->isbd."</div>");
$nex = new exemplaire($cb, $expl_id,$id);

// visibilité des exemplaires
// $nex->explr_acces_autorise contient INVIS, MODIF ou UNMOD

if ($nex->explr_acces_autorise!="INVIS") {
	
	print "<div class='row'>";
	print $nex->expl_form("./catalog.php?categ=expl_update&sub=update&org_cb=".urlencode($cb)."&expl_id=".$expl_id, notice::get_permalink($id));
	print "</div>";
	print "<div class='row notice-perio expl-carts'>";
	print caddie_controller::get_display_list_from_item('display', 'EXPL', $expl_id);
	print "</div>";
} else {
	print "<div class='row'><div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
	print "<div class='colonne-suite'><span class='erreur'>".$msg["err_mod_expl"]."</span>&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='bouton' value=\"{$msg['bt_retour']}\" name='retour' onClick='history.back(-1);'></div></div>";	
}
	
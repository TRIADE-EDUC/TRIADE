<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso_browser.php,v 1.5 2019/06/07 08:05:39 btafforeau Exp $

global $j_offset, $ancre, $browser_url, $limite_affichage, $restriction, $mode, $msg, $select;

// page d'affichage du browser de collections

// définition du minimum nécéssaire
$base_path="../../../..";
$class_path="$base_path/classes";
$include_path="$base_path/classes";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
require_once ("$base_path/includes/init.inc.php");
require_once ("$base_path/classes/authperso.class.php");

// javascript pour retrouver l'offset dans la liste des titres uniformes
$j_offset = "
<script type='text/javascript'>
<!--
function jump_anchor(anc) {
	// récupération de l'index de l'ancre
	for ( i = 0; i <= document.anchors.length; i++) {
		if(document.anchors[i].name == anc) {
			anc_index = i;
			break;
		}
	}
	if (document.all) {
		// code pour IE
		document.anchors[anc_index].scrollIntoView();
	} else {
		// mettre ici le code pour Mozilla et Netscape quand on aura trouvé
	}
}
// -->
jump_anchor('".(isset($ancre) ? $ancre : '')."');
</script>
";

// url du présent browser
$browser_url = "./authperso_browser.php";

print "<div id='contenu-frame'>";

if (!isset($limite_affichage) || $limite_affichage=="")$restriction = " limit 0,30 ";
else $restriction = "";

print "<a href='$browser_url?mode=".$mode."&limite_affichage=ALL'>$msg[tout_afficher]</a><br />";

// affichage de la liste
$select="window.parent.document.location='../../../../catalog.php?categ=search&mode=$mode&etat=aut_search&aut_type=authperso&authperso_id=".($mode - 1000)."&aut_id=!!auth_id!!&no_rec_history=1'; return(false);";
$authperso= new authperso($mode-1000);
print $authperso->get_search_list("<a href='#' onclick=\"$select\">!!isbd!!</a><br />",$restriction);

if(isset($ancre) && $ancre)
	print $j_offset;
pmb_mysql_close();

// affichage du footer
print "</div></body></html>";

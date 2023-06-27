<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_see.inc.php,v 1.4 2017-11-14 13:50:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage du contenu d'une bannette
require_once($base_path."/includes/bannette_func.inc.php");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (!$id_empr) $id_empr=$_SESSION["id_empr_session"] ;
print "<script type='text/javascript' src='./includes/javascript/tablist.js'></script>" ;
print "<div id='aut_details' class='aut_details_bannette'>\n";

if ($id_bannette){
	$bans=explode(",",$id_bannette);
	for($i=0 ; $i<count($bans) ; $i++){
		$bans[$i]+=0;
	}
	$aff = pmb_bidi(affiche_public_bannette(implode(",",$bans), 0, "./index.php?lvl=bannette_see&id_bannette=!!id_bannette!!"));
	if ($opac_bannette_notices_depliables) print $begin_result_liste ;
	print $aff;
}else{
	print $msg['bannette_see_nothing_to_see'];
}

print "</div><!-- fermeture #aut_see -->\n";	
?>
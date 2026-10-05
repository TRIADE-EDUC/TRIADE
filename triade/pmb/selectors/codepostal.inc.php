<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: codepostal.inc.php,v 1.12 2019/06/06 15:04:28 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion d'un élément à ne pas afficher
if (!$no_display) $no_display=0;

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=codepostal&caller=$caller&param1=$param1&param2=$param2&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn";

$selector_codepostal = new selector_codepostal(stripslashes($user_input));
$selector_codepostal->proceed();

// function d'affichage
function show_results($user_input, $nbr_lignes=0, $page=0, $id = 0) {
    global $nb_per_page, $rech_regexp;
	global $base_url;
	global $caller;
	global $class_path;
	global $no_display;
 	global $charset;
 	global $msg ;

	// on récupére le nombre de lignes
	$user_input = str_replace("*","%",$user_input);
	if($user_input=="") {
		$requete = "SELECT empr_cp, empr_ville FROM empr group by empr_cp, empr_ville ";
	} else {
		$requete = "SELECT empr_cp, empr_ville FROM empr where (empr_cp like '$user_input%' or empr_ville like '$user_input%') group by empr_cp, empr_ville ";
	}
	$res = pmb_mysql_query($requete);
	$nbr_lignes = pmb_mysql_num_rows($res);

	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;
	
	if($nbr_lignes) {
		// on lance la vraie requête
		if($user_input=="") 
			$requete = "SELECT empr_cp, empr_ville, count(id_empr) as nbre FROM empr group by empr_cp, empr_ville ORDER BY empr_cp, empr_ville LIMIT $debut,$nb_per_page ";
		else 
			$requete = "SELECT empr_cp, empr_ville, count(id_empr) as nbre  FROM empr where (empr_cp like '$user_input%' or empr_ville like '$user_input%') group by empr_cp, empr_ville ORDER BY empr_cp, empr_ville LIMIT $debut,$nb_per_page ";
		$res = pmb_mysql_query($requete);
		while(($cp_ville=pmb_mysql_fetch_object($res))) {
			print "<div class='row'>";
			print pmb_bidi("<a href='#' onclick=\"set_parent('$caller', '".htmlentities(addslashes($cp_ville->empr_ville),ENT_QUOTES, $charset)."', '".htmlentities(addslashes($cp_ville->empr_cp),ENT_QUOTES, $charset)."')\">$cp_ville->empr_cp - $cp_ville->empr_ville : $cp_ville->nbre</a>");
			print "</div>";

		}
		pmb_mysql_free_result($res);

		// constitution des liens
		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;

		// affichage de la pagination
		print "<hr /><div class='center'>";
		$url_base = $base_url."&rech_regexp=$rech_regexp&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		print $nav_bar;
		print '</div>';
	}
}
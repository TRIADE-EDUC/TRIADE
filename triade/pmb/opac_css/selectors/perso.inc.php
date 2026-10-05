<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perso.inc.php,v 1.4.4.1 2025/01/30 09:08:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$base_url = "./select.php?what=perso&caller=$caller&p1=$p1&p2=$p2&perso_id=$perso_id&custom_prefixe=".$custom_prefixe."&dyn=$dyn&perso_name=$perso_name";

require_once('./selectors/templates/sel_perso.tpl.php');
require_once($base_path.'/classes/parametres_perso.class.php');

$nb_per_page = intval($nb_per_page);

$persos=new parametres_perso($custom_prefixe);

$sel_header=str_replace("!!select_title!!",sprintf($msg["perso_select"],htmlentities($persos->t_fields[$perso_id]['TITRE'],ENT_QUOTES,$charset)),$sel_header);
// affichage du header
print $sel_header;
print $jscript;
if(!isset($recherche)) $recherche = '';
if($recherche){
	$f_user_input=rawurldecode($recherche);
}

$type=$persos->t_fields[$perso_id]['TYPE'];
$options=$param=$persos->t_fields[$perso_id]['OPTIONS'][0];
$resultat_count=$requete="";
$marclist_tab = array();
$has_searchable = true;
$has_paginated = true;
if ($type=="list") {
	$requete_count="select count(".$custom_prefixe."_custom_list_value) from ".$custom_prefixe."_custom_lists where ".$custom_prefixe."_custom_champ=".$perso_id;
	$requete="select ".$custom_prefixe."_custom_list_value, ".$custom_prefixe."_custom_list_lib from ".$custom_prefixe."_custom_lists where ".$custom_prefixe."_custom_champ=".$perso_id;
	if ($f_user_input) {
		$recherche=$f_user_input;
		$f_user_input=str_replace("*","%",$f_user_input);
		$requete.=" and ".$custom_prefixe."_custom_list_lib like '%".$f_user_input."%'";
		$requete_count.=" and ".$custom_prefixe."_custom_list_lib like '%".$f_user_input."%'";
	}
	$requete.=" order by ordre limit ".($page*$nb_per_page).",$nb_per_page";
	$resultat_count=pmb_mysql_query($requete_count);
} elseif ($type=="marclist") {
	$marclist_type = new marc_list($options['DATA_TYPE'][0]['value']);
	$marclist_tab_count=count($marclist_type->table);

	switch($options['DATA_TYPE'][0]['value']) {
		case "lang" :
		case "country" :
		case "function" :
			$favorite = false;
			// affichage d'un sommaire par lettres
			foreach($marclist_type->table as $key => $val) {
				$alphabet[] = strtoupper(convert_diacrit(pmb_substr($val,0,1)));
				if (isset($marclist_type->tablefav[$key]) && $marclist_type->tablefav[$key]) $favorite=true;
			}
			$alphabet = array_unique($alphabet);

			if(!isset($letter) || !$letter) {
				if ($favorite)
					$letter = "Fav";
				else
					$letter = "a";
			}
			print "<div class='row'>";
			if ($favorite) {
				if ($letter!='Fav') {
					print "<a href='$base_url&letter=Fav'>".$msg['favoris']."</a> ";
				} else {
					print "<strong><u>".$msg['favoris']."</u></strong> ";
				}
			}
			foreach($alphabet as $dummykey=>$char) {
				$present = pmb_preg_grep("/^$char/i", $marclist_type->table);
				if(is_countable($present) && sizeof($present) && strcasecmp($letter, $char)) {
					print "<a href='$base_url&letter=$char'>$char</a> ";
				} else if(!strcasecmp($letter, $char)) {
					print "<strong><u>$char</u></strong> ";
				}
			}
			print "</div><hr />";

			if (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
				asort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
				ksort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
				arsort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
				krsort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="3") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
				$marclist_type->table = array_reverse($marclist_type->table, true);
			}
			// Sinon on ne fait rien, le tableau est déjà trié avec l'attribut order

			reset($marclist_type->table);

			foreach($marclist_type->table as $code=>$libelle ) {
				if((preg_match("/^$letter/i", convert_diacrit($libelle))) ||(($letter=='Fav')&&($marclist_type->tablefav[$code]))) {
					$marclist_tab[$code] = $libelle;
				}
			}
			$has_searchable = false;
			$has_paginated = false;
			break;
		default:
			if (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
				asort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="1")) {
				ksort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="2") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
				arsort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="1") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
				krsort($marclist_type->table);
			} elseif (($options['METHOD_SORT_VALUE'][0]['value']=="3") && ($options['METHOD_SORT_ASC'][0]['value']=="2")) {
				$marclist_type->table = array_reverse($marclist_type->table, true);
			}
			// Sinon on ne fait rien, le tableau est déjà trié avec l'attribut order

			reset($marclist_type->table);

			if ($f_user_input) {
				$recherche=$f_user_input;
				$marclist_filter = array();
				foreach ($marclist_type->table as $code=>$libelle) {
					if (preg_match("/^$f_user_input/i",$libelle)) {
						$marclist_filter[$code] = $libelle;
					}
				}
				$marclist_tab_count=count($marclist_filter);
				$limit_debut = $page*$nb_per_page;
				$n=0;
				foreach ($marclist_filter as $code=>$libelle) {
					if (($n >= $limit_debut) && ($n<= $limit_debut+$nb_per_page)) {
						$marclist_tab[$code] = $libelle;
					}
					$n++;
				}
			} else {
				$limit_debut = $page*$nb_per_page;
				$n=0;
				foreach ($marclist_type->table as $code=>$libelle) {
					if (($n >= $limit_debut) && ($n<= $limit_debut+$nb_per_page)) {
						$marclist_tab[$code] = $libelle;
					}
					$n++;
				}
			}
			break;
	}

} else {
	$requete="create temporary table temp_perso_list ENGINE={$default_tmp_storage_engine} ".$options['QUERY'][0]['value'];
	pmb_mysql_query($requete);

	$resultat=pmb_mysql_query("show columns from temp_perso_list");
	if($resultat && pmb_mysql_num_rows($resultat)){
		$id_field=pmb_mysql_result($resultat,0,0);
		$lib_field=pmb_mysql_result($resultat,1,0);
		$requete_count="select count($id_field) from temp_perso_list";
		$requete="select $id_field, $lib_field from temp_perso_list";
		if ($f_user_input) {
			$recherche=$f_user_input;
			$f_user_input=str_replace("*","%",$f_user_input);
			$requete.=" where ".$lib_field." like '%".$f_user_input."%'";
			$requete_count.=" where ".$lib_field." like '%".$f_user_input."%'";
		}

		$requete.=" order by $lib_field limit ".($page*$nb_per_page).",$nb_per_page";
		$resultat_count=pmb_mysql_query($requete_count);
	}
}

if ($has_searchable) {
	$sel_search_form=str_replace("!!deb_rech!!",htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset),$sel_search_form);
	print $sel_search_form;
}

$nbr_lignes=0;
$resultat2="";
if($resultat_count && $requete && pmb_mysql_num_rows($resultat_count)){
	$nbr_lignes=@pmb_mysql_result($resultat_count,0,0);
	$resultat2=pmb_mysql_query($requete);
}

if($resultat2 && pmb_mysql_num_rows($resultat2)){
	while($r=pmb_mysql_fetch_row($resultat2)) {
		print pmb_bidi("<a href='#' onClick=\"set_parent('$caller', '".htmlentities(addslashes($r[0]),ENT_QUOTES,$charset)."','".htmlentities(addslashes($r[1]),ENT_QUOTES,$charset)."' )\">".htmlentities($r[1],ENT_QUOTES,$charset)."</a><br />");
	}
}

if (count($marclist_tab)) {
	$nbr_lignes = $marclist_tab_count;
	foreach ($marclist_tab as $code=>$label) {
		print pmb_bidi("<div class='row'>
						<div class='colonne2' style='width: 80%;'>
							<a href='#' onClick=\"set_parent('$caller', '".htmlentities(addslashes($code),ENT_QUOTES,$charset)."','".htmlentities(addslashes($label),ENT_QUOTES,$charset)."' )\">".htmlentities($label,ENT_QUOTES,$charset)."</a>
							</div>
						<div class='colonne2'  style='width: 20%;'>
							$code
							</div>
						</div>");
	}
}

if ($has_paginated) {
	// constitution des liens
	$nbepages = ceil($nbr_lignes/$nb_per_page);
	$suivante = $page+1;
	$precedente = $page-1;
	// affichage du lien précédent si nécéssaire
	print "<div class='row'>&nbsp;<hr /></div><div class='center'>";
	if($precedente >= 0) {
		print "<a href='$base_url&page=$precedente&nbr_lignes=$nbr_lignes&recherche=".rawurlencode($recherche)."'><img src='".get_url_icon('left.gif')."' title='$msg[48]' alt='[$msg[48]]' class='align_middle' /></a>";
	}
	print "<b>".($page+1)."/$nbepages</b>";

	if($suivante<$nbepages)
		print "<a href='$base_url&page=$suivante&nbr_lignes=$nbr_lignes&recherche=".rawurlencode($recherche)."'><img src='".get_url_icon('right.gif')."' title='$msg[49]' alt='[$msg[49]]' class='align_middle' /></a>";
	print '</div>';
}

print $sel_footer;
?>
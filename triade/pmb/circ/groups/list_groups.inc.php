<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_groups.inc.php,v 1.21 2017-07-07 12:39:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage de la liste des groupes pour sélection
function list_group($clef, $filter_list, $group_list, $nav_bar, $nb_total) {
	global $group_list_tmpl;
 	global $charset;
	$group_list_tmpl = str_replace("!!filter_list!!", $filter_list, $group_list_tmpl);
 	$group_list_tmpl = str_replace("!!cle!!", $clef, $group_list_tmpl);
 	if ($nb_total>0) $group_list_tmpl = str_replace("<!--!!nb_total!!-->", "(".$nb_total.")", $group_list_tmpl);
	$group_list_tmpl = str_replace("!!list!!", $group_list, $group_list_tmpl);
	$group_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $group_list_tmpl);
	print pmb_bidi($group_list_tmpl);
}

if (!isset($nbr_lignes)) $nbr_lignes = 0;
if (!isset($page)) $page = 0;

// nombre de références par pages
if (!$nb_per_page) {
	if ($nb_per_page_author != "")
	$nb_per_page = $nb_per_page_author ;
	else $nb_per_page = 10;
}
$group_location_id = array();

// traitement de la saisie utilisateur
if ($empr_groupes_localises) {
	if ($group_location_id_list) {
		$group_location_id = explode(',',$group_location_id_list);
	}
	//Toutes les localisations sélectionnées
	if ((in_array('-1',$group_location_id))||(!count($group_location_id))) {
		if ($group_query) {
			$clause = " WHERE libelle_groupe like '%".str_replace("*", "%", $group_query)."%' ";
		} else {
			$clause = '' ;
		}
	} else {
		if ($group_query) {
			$clause = " WHERE libelle_groupe like '%".str_replace("*", "%", $group_query)."%' AND (empr2.empr_location IN (".implode(',',$group_location_id).") ";
		} else {
			$clause = " WHERE (empr2.empr_location IN (".implode(',',$group_location_id).") ";
		}
		//Aucune localisation
		if (in_array('-2',$group_location_id)) {
			$clause .= " OR empr2.empr_location IS NULL";
		}
		$clause .=")";
	}
} else {
	if ($group_query) {
		$clause = " WHERE libelle_groupe like '%".str_replace("*", "%", $group_query)."%' ";
	} else {
		$clause = '' ;
	}
}
	
// formulaire de restriction
$filter_list = "<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$PHP_SELF?categ=$categ&action=$action&group_location_id=&group_query=$group_query' method='post'>";
$filter_list.="<div class='row'><label class='etiquette' for='group_query'>".$msg['908']."</label><input class='saisie-80em' id='group_query' type='text' value='".$group_query."' name='group_query' title='".$msg['3001']."' /></div>";
if ($empr_groupes_localises){
	$filter_list.="<div class='row'>".group::gen_combo_box_grp($group_location_id,1)."</div>";
}
$filter_list.="
 		<div class='row'><input type=text name=nb_per_page size=2 value=$nb_per_page class='petit' /> ".$msg['1905']."</div>
 		<div class='row'>
 				<input type='button' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.submit();\">&nbsp;<input type='button' class='bouton' value='".$msg['909']."' onClick='document.location=\"./circ.php?categ=groups&action=create\"' />
 		</div>
	</form>";
			
// on récupére le nombre de lignes 
if(!$nbr_lignes) {
	$requete = "SELECT COUNT(1) FROM groupe LEFT JOIN empr empr2 ON id_empr = resp_groupe $clause ";
	$res = pmb_mysql_query($requete, $dbh);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);
}


if(!$page) $page=1;
$debut =($page-1)*$nb_per_page;

if($nbr_lignes) {
	// on lance la vraie requête
	$group_list = '';
	$requete = "SELECT id_groupe, libelle_groupe, resp_groupe, concat(IFNULL(empr.empr_prenom,'') ,' ',IFNULL(empr.empr_nom,'')) as resp_name, count( empr_id ) as nb_empr, empr2.empr_location FROM groupe LEFT JOIN empr_groupe ON groupe_id = id_groupe left join empr on resp_groupe = empr.id_empr
	 LEFT JOIN empr empr2 ON resp_groupe = empr2.id_empr $clause group by id_groupe, libelle_groupe, resp_groupe, resp_name ORDER BY libelle_groupe LIMIT $debut,$nb_per_page ";
	$res = pmb_mysql_query($requete, $dbh);
	if ((pmb_mysql_num_rows($res) > 1)||($page>1)) {
		$parity=1;
		$group_list .= "<tr><th>".$msg[904]."</th><th>".$msg[913]."</th><th>".$msg['circ_group_emprunteur']."</th><th>".$msg['349']."</th><th>".$msg['reserv_en_cours']."</th>";
		while($rgroup=pmb_mysql_fetch_object($res)) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$nb_pret=0;
			$requete = "SELECT count( pret_idempr ) as nb_pret FROM empr_groupe,pret where groupe_id=$rgroup->id_groupe and empr_id = pret_idempr";
			$res_pret = pmb_mysql_query($requete, $dbh);
			if (pmb_mysql_num_rows($res_pret)) {
				$rpret=pmb_mysql_fetch_object($res_pret);
				$nb_pret=$rpret->nb_pret;	
			}
			$nb_resa=0;
			$requete = "SELECT count( resa_idempr ) as nb_resa FROM empr_groupe,resa where groupe_id=$rgroup->id_groupe and empr_id = resa_idempr";
			$res_resa = pmb_mysql_query($requete, $dbh);
			if (pmb_mysql_num_rows($res_resa)) {
				$rresa=pmb_mysql_fetch_object($res_resa);
				$nb_resa=$rresa->nb_resa;	
			}
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./circ.php?categ=groups&action=showgroup&groupID=$rgroup->id_groupe';\" ";
     			$group_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
		  			<td>$rgroup->libelle_groupe</td>
					<td>$rgroup->resp_name</td>
					<td>$rgroup->nb_empr</td>
					<td>$nb_pret</td>
					<td>$nb_resa</td>
					</tr>";
    	}
		pmb_mysql_free_result($res);

		$group_location_id_link = '';
		if ($empr_groupes_localises) {
			$group_location_id_link = '&group_location_id_list='.implode(',',$group_location_id);
		}
		$nav_bar = aff_pagination ("$PHP_SELF?categ=groups&action=listgroups".$group_location_id_link."&group_query=".$group_query, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		// affichage du résultat
		list_group($group_query, $filter_list, $group_list, $nav_bar, $nbr_lignes);
	} else {
		$rgroup = $rgroup=pmb_mysql_fetch_object($res);
		$groupID = $rgroup->id_groupe;
		include('./circ/groups/show_group.inc.php');
	}
} else {
	// la requête n'a produit aucun résultat
	print pmb_bidi($filter_list);
	error_message($msg[917], str_replace('!!group_cle!!', htmlentities(stripslashes($group_query),ENT_QUOTES, $charset), $msg[918]), 0, './circ.php?categ=groups');
}

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fournisseur.inc.php,v 1.19 2017-11-21 13:38:21 dgoron Exp $

/*
 * caller	= nom du formulaire appelant
 * param1	= id du champ dans lequel retourner l'id fournisseur dans le formulaire appelant
 * param2	= id du champ dans lequel retourner le libelle fournisseur dans le formulaire appelant
 * param3	= id du champ dans lequel retourner l'adresse fournisseur dans le formulaire appelant
 *
 * id_bibli	= identifiant de la structure à laquelle sont rattachés les fournisseurs
 * deb_rech = entree utilisateur pour la recherche
 */

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$base_url = "./select.php?what=fournisseur&caller=$caller&param1=$param1&param2=$param2&param3=$param3&id_bibli=$id_bibli&no_display=$no_display&bt_ajouter=$bt_ajouter";

// contenu popup sélection fournisseur
require_once('./selectors/templates/sel_fournisseur.tpl.php');
require_once($class_path.'/entites.class.php');

// affichage du header
print $sel_header;

// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
if($f_user_input=="" && $user_input=="") {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;	
}

if($bt_ajouter == "no"){
	$bouton_ajouter="";
}else{
	$bouton_ajouter= "<input type='button' class='bouton_small' onclick=\"document.location='$base_url&action=add&deb_rech='+this.form.f_user_input.value\" value='".$msg['acquisition_ajout_fourn']."' />";
}

switch($action){
	case 'add':
		if(count(entites::get_entities())) {
			$fournisseur_form = str_replace("!!sel_bibli!!", entites::getBibliHtmlSelect(SESSuserid, entites::getSessionBibliId(), FALSE, array('class'=>'saisie-50em','id'=>'id_bibli','name'=>'id_bibli')), $fournisseur_form);
			$fournisseur_form = str_replace("!!deb_saisie!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $fournisseur_form);
			print $fournisseur_form;
		} else {
			//Pas de bibliothèques définies pour l'utilisateur
			$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
			error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', '');
		}
		break;
	case 'update':
		if(count(entites::get_entities())) {
			// vérification validité des données fournies.
			if (entites::exists_rs($raison,$id_bibli,0)) {
				error_form_message($raison.$msg["acquisition_raison_already_used"]);
				break;
			}
			$entites = new entites();
			$entites->type_entite = '0';
			$entites->num_bibli = $id_bibli;
			$entites->raison_sociale = $raison;
			$entites->num_cp_client = $num_cp;
			$entites->save();
		} else {
			//Pas de bibliothèques définies pour l'utilisateur
			$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
			error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', '');
		}
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($raison),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($dbh, $raison, 0, 0);
		break;
	default:
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($dbh, $user_input, $nbr_lignes, $page);
		break;
}



// affichage des membres de la page
function show_results($dbh, $user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
 	global $charset;
	global $msg;
	global $id_bibli;

	//comptage
	if($user_input=="") {
		$nbr_lignes = entites::getNbFournisseurs($id_bibli); 
	} else {
		$aq=new analyse_query(stripslashes($user_input));
		if ($aq->error) {
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
			exit;
		}
		$nbr_lignes = entites::getNbFournisseurs($id_bibli, $aq);
	}
	
	if (!$page) $page=1;
	$debut = ($page-1)*$nb_per_page;

	if($nbr_lignes) {
		//liste
			if($user_input=="") {
				$res = entites::list_fournisseurs($id_bibli, $debut, $nb_per_page);
			} else {
				$res = entites::list_fournisseurs($id_bibli, $debut, $nb_per_page, $aq);
			}

		while(($row=pmb_mysql_fetch_object($res))) {
			$entry = $row->raison_sociale;
			$adresse = '';
			if ($caller!='form_abonnement') {
				$coord = entites::get_coordonnees($row->id_entite, '1');
				if (pmb_mysql_num_rows($coord) != 0) {
					$coord = pmb_mysql_fetch_object($coord);
					if($coord->libelle != '') $adresse = htmlentities(addslashes($coord->libelle), ENT_QUOTES, $charset)."\\n";
					if($coord->contact !='') $adresse.=  htmlentities(addslashes($coord->contact), ENT_QUOTES, $charset)."\\n";
					if($coord->adr1 != '') $adresse.= htmlentities(addslashes($coord->adr1), ENT_QUOTES, $charset)."\\n";
					if($coord->adr2 != '') $adresse.= htmlentities(addslashes($coord->adr2), ENT_QUOTES, $charset)."\\n";
					if($coord->cp !='') $adresse.= htmlentities(addslashes($coord->cp), ENT_QUOTES, $charset).' ';
					if($coord->ville != '') $adresse.= htmlentities(addslashes($coord->ville), ENT_QUOTES, $charset);
				}
			}
			print pmb_bidi("
			<a href='#' onclick=\"set_parent('$caller', '$row->id_entite', '".htmlentities(addslashes($entry),ENT_QUOTES,$charset)."', '$adresse' )\">$entry</a>");
			print "<br />";
		
		}
		pmb_mysql_free_result($res);

		// constitution des liens
		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;

		// affichage du lien précédent si nécessaire
		print '<hr /><div class="center">';
		if($precedente > 0)
		print "<a href='$base_url&page=$precedente&nbr_lignes=$nbr_lignes&user_input=".rawurlencode(stripslashes($user_input))."&no_display=$no_display'><img src='".get_url_icon('left.gif')."' border='0' title='$msg[48]' alt='[$msg[48]]' hspace='3' class='align_middle' /></a>";
		for($i = 1; $i <= $nbepages; $i++) {
			if($i==$page)
				print "<b>$i/$nbepages</b>";
		}

	if($suivante<=$nbepages)
		print "<a href='$base_url&page=$suivante&nbr_lignes=$nbr_lignes&user_input=".rawurlencode(stripslashes($user_input))."&no_display=$no_display'><img src='".get_url_icon('right.gif')."' border='0' title='$msg[49]' alt='[$msg[49]]' hspace='3' class='align_middle' /></a>";
	}
		print '</div>';
}

print $sel_footer;
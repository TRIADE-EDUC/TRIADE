<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fournisseur.inc.php,v 1.20 2020/08/31 12:51:36 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//parametres
global $caller;		//nom du formulaire appelant
global $param1;		//id du champ dans lequel retourner l'id fournisseur dans le formulaire appelant
global $param2;		//id du champ dans lequel retourner le libelle fournisseur dans le formulaire appelant
global $param3;		//id du champ dans lequel retourner l'adresse fournisseur dans le formulaire appelant
global $id_bibli;	//identifiant de la structure à laquelle sont rattachés les fournisseurs
global $no_display;
global $bt_ajouter;
global $action;
global $deb_rech;	//entree utilisateur pour la recherche

global $user_input;
global $f_user_input;
global $raison;
global $num_cp;

global $nbr_lignes, $page, $nb_per_page;

global $class_path, $msg, $charset;

// contenu popup selection fournisseur
$base_url = "./select.php?what=fournisseur&caller=$caller&param1=$param1&param2=$param2&param3=$param3&id_bibli=$id_bibli&no_display=$no_display&bt_ajouter=$bt_ajouter";
require_once './selectors/templates/sel_fournisseur.tpl.php';
global $sel_header, $jscript, $sel_search_form, $fournisseur_form, $sel_footer;

require_once "{$class_path}/entites.class.php";

// affichage du header
print $sel_header;

// traitement en entree des requetes utilisateur
if ($deb_rech) {
	$f_user_input = $deb_rech ;
}
if($f_user_input == "" && $user_input == "") {
	$user_input = '';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) {
		$f_user_input = $user_input;
	}
	if (($f_user_input)&&(!$user_input)) {
		$user_input = $f_user_input;
	}
}

//action
if(!isset($action)) {
	$action = '';
}

if(!isset($id_bibli)) {
	$id_bibli = 0;
}
$id_bibli = intval($id_bibli);

//liste de tous les etablissements
$all_biblis = entites::get_etablissements_by_user(0);

//liste des etablissements auxquels a acces l'utilisateur
$allowed_biblis = entites::get_etablissements_by_user(SESSuserid);

//etablissement par defaut pour l'utilisateur
$deflt_bibli = entites::getDefaultBibliId();

//etablissement selectionne
if(!$id_bibli) {
	$id_bibli = $deflt_bibli;
}

//affichage du bouton ajouter
$bouton_ajouter = "<input type='button' class='bouton_small' onclick=\"document.location='$base_url&action=add&deb_rech='+this.form.f_user_input.value\" value='".$msg['acquisition_ajout_fourn']."' />";
switch(true) {
	case ($bt_ajouter == 'no') :
	case (empty($allowed_biblis)) :
	case ( !array_key_exists($id_bibli, $allowed_biblis) ) :
		$bouton_ajouter = '';
		break;
	default :
		break;
}

// l'utilisateur peut-il changer d'etablissement ?
$sel_bibli_opts = [];
$sel_bibli = '';
switch(true) {
	case ( ($caller == 'form_abonnement') && ($action != 'add') ) :
	case ( ($caller == 'saisie_editeur') && ($action != 'add') ) :
		$sel_bibli_opts = [
		'class'=>'saisie-50em',
		'id'=>'id_bibli',
		'name'=>'id_bibli',
		'onchange'=>'this.form.submit();'
				];
		$sel_bibli = entites::get_hmtl_select_etablissements(0, $id_bibli, FALSE, $sel_bibli_opts);
		break;
	case ( ($caller == 'form_abonnement') && ($action == 'add') ) :
	case ( ($caller == 'saisie_editeur') && ($action == 'add') ) :
		$sel_bibli = entites::get_hmtl_select_etablissements(SESSuserid, $id_bibli, FALSE, $sel_bibli_opts);
		break;
	default :
		break;
}

switch($action){
	
	case 'add':
		
		if(empty($allowed_biblis)) {
			//Pas de bibliotheques definies pour l'utilisateur
			$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
			error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', '');
		} else {
			$fournisseur_form = str_replace("!!sel_bibli!!", $sel_bibli, $fournisseur_form);
			$fournisseur_form = str_replace("!!deb_saisie!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $fournisseur_form);
			print $fournisseur_form;
		}
		break;
		
	case 'update':
		
		if(empty($allowed_biblis)) {
			//Pas de bibliotheques definies pour l'utilisateur
			$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";
			error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', '');
		} else {
			// verification validite des donnees fournies.
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
		}
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($raison),ENT_QUOTES,$charset), $sel_search_form);
		$sel_search_form = str_replace("<!-- sel_bibli -->", $sel_bibli, $sel_search_form);
		$sel_search_form = str_replace("<!-- bouton_ajouter -->", $bouton_ajouter, $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($raison, 0, 0);
		break;
		
	default:
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		$sel_search_form = str_replace("<!-- sel_bibli -->", $sel_bibli, $sel_search_form);
		$sel_search_form = str_replace("<!-- bouton_ajouter -->", $bouton_ajouter, $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($user_input, $nbr_lignes, $page);
		break;
		
}


// affichage des membres de la page
function show_results($user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $charset;
	global $msg;
	global $id_bibli;
	global $no_display;
	
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
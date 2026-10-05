<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: commande.inc.php,v 1.7 2019/08/29 10:05:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


// traitement en entrée des requêtes utilisateur
if ($deb_rech) $f_user_input = $deb_rech ;
if($f_user_input=="" && $user_input=="") {
	$user_input='';
} else {
	// traitement de la saisie utilisateur
	if ($user_input) $f_user_input=$user_input;
	if (($f_user_input)&&(!$user_input)) $user_input=$f_user_input;
}

if(isset($id_cde)) {
	$id_cde = intval($id_cde) ;
} else {
	$id_cde= 0;
}

if(isset($id_bibli)) {
	$id_bibli = intval($id_bibli);
} else {
	$id_bibli = 0;
}
if (!$id_bibli) {
	$id_bibli = entites::getSessionBibliId();
}
$label_bibli = entites::getRaisonSociale([$id_bibli], 0)[$id_bibli];

if(isset($id_exercice)) {
	$id_exercice = intval($id_exercice);
} else {
	$id_exercice = 0;
}
$tab_active_exercices = exercices::getActiveExercicesByEntite($id_bibli);
if(count($tab_active_exercices) && !array_key_exists($id_exercice, $tab_active_exercices)) {
	reset($tab_active_exercices);
	$id_exercice = key($tab_active_exercices);
}
$label_exercice = $tab_active_exercices[$id_exercice];

if(!isset($original_action)) {
	$original_action = $action;
}

if(isset($id_fou)) {
	$id_fou = intval($id_fou);
} else {
	$id_fou = 0;
}

$base_url = "./select.php?what=commande&action={$original_action}&original_action={$original_action}&callback={$callback}&id_cde={$id_cde}&bt_ajouter={$bt_ajouter}";
$add_url = "./select.php?what=commande&action=add&original_action={$original_action}&callback={$callback}&id_cde={$id_cde}&id_bibli={$id_bibli}&id_exercice={$id_exercice}&bt_ajouter={$bt_ajouter}";
$cancel_url = "./select.php?what=commande&action={$original_action}&original_action={$original_action}&callback={$callback}&id_cde={$id_cde}&id_bibli={$id_bibli}&id_exercice={$id_exercice}&bt_ajouter={$bt_ajouter}";
$update_url = "./select.php?what=commande&action=update&original_action={$original_action}&callback={$callback}&id_cde={$id_cde}&id_bibli={$id_bibli}&id_exercice={$id_exercice}&bt_ajouter={$bt_ajouter}";

if(!defined('TYP_ACT_CDE')) define('TYP_ACT_CDE', 0);	//				0 = Commande

require_once('./selectors/templates/sel_commande.tpl.php');
require_once($class_path."/entites.class.php");
require_once($class_path."/analyse_query.class.php");
require_once($class_path."/actes.class.php");

$sel_bibli = entites::getBibliHtmlSelect(SESSuserid, $id_bibli, false, array('name'=>'id_bibli', 'id'=>'id_bibli', 'onchange'=>'submit();'));
$sel_exercice = exercices::getHtmlSelect($id_bibli, $id_exercice, false, array('name'=>'id_exercice', 'id'=>'id_exercice', 'onchange'=>'submit();'));

$done = false;

switch($action){

	case 'add':
		print $sel_header_add;
		$commande_form = str_replace('!!label_bibli!!', htmlentities($label_bibli, ENT_QUOTES,$charset), $commande_form);
		$commande_form = str_replace('!!label_exercice!!', htmlentities($label_exercice, ENT_QUOTES,$charset), $commande_form);
		$commande_form = str_replace('!!id_bibli!!', $id_bibli, $commande_form);
		print $commande_form;
		$done = true;
		break;

	case 'update':
		$acte = new actes();
		$acte->type_acte = TYP_ACT_CDE;
		$acte->num_entite = $id_bibli;
		$acte->num_exercice = $id_exercice;
		$acte->statut = STA_ACT_AVA;
		$acte->num_fournisseur = $id_fou;
		$acte->numero = $num_cde;
		$acte->nom_acte = $nom_acte;
		$acte->save();
		$id_acte = $acte->id_acte;
		print $jscript;
		print "<script type='text/javascript'>set_parent('".$id_acte."', '".$id_bibli."', '".$id_exercice."', '".$callback."');</script>";
		break;

	case 'transfer_lines'  :
	case 'duplicate_lines' :
	default:
	    break;
}

if(!$done) {
    print $sel_header;
    // affichage des membres de la page
    $sel_search_form = str_replace('!!deb_rech!!', stripslashes($f_user_input), $sel_search_form);

    if($bt_ajouter == "no"){
        $bouton_ajouter="";
    }else{
        $bouton_ajouter= "<input type='button' class='bouton_small' onclick=\"document.location='$add_url'\" value='".$msg['acquisition_ajout_cde']."' />";
    }

    $sel_search_form=str_replace('!!sel_bibli!!', $sel_bibli, $sel_search_form);
    $sel_search_form=str_replace('!!sel_exercice!!', $sel_exercice, $sel_search_form);
    $sel_search_form = str_replace('!!bouton_ajouter!!', $bouton_ajouter, $sel_search_form);

    print $sel_search_form;
    print $jscript;

    show_commandes($user_input, STA_ACT_AVA, $nbr_lignes, $page);
}


function show_commandes ($user_input, $statut=0, $nbr_lignes=0, $page=0, $id = 0) {

	global $nb_per_page;
	global $base_url;
	global $caller;
	global $msg;
	global $no_display ;
	global $charset;
	global $id_bibli;
	global $id_exercice;
	global $callback;
	global $id_cde;
	global $sortBy;
	
	// traitement de la saisie utilisateur
	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;

	// comptage
	if(!$nbr_lignes) {
		if(!$user_input) {
			$nbr_lignes = entites::getNbActes($id_bibli, TYP_ACT_CDE, $statut, 0, '', $id_exercice);
		} else {
			$aq=new analyse_query(stripslashes($user_input),0,0,0,0);
			if ($aq->error) {
				error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
				exit;
			}
			$nbr_lignes = entites::getNbActes($id_bibli, TYP_ACT_CDE, $statut, $aq, $user_input, $id_exercice);
		}
	} else {
		$aq=new analyse_query(stripslashes($user_input),0,0,0,0);
	}

	if ($nbr_lignes) {
		// liste
		if (empty($sortBy)) {
			$sortBy = '-date_acte';
		}
		if(!$user_input) {
			$res = entites::listActes($id_bibli, TYP_ACT_CDE, $statut, $debut, $nb_per_page, 0, '', $sortBy, $id_exercice);
		} else {
			$res = entites::listActes($id_bibli, TYP_ACT_CDE, $statut, $debut, $nb_per_page, $aq, $user_input, $sortBy, $id_exercice);
		}

		print "<table>
				<tr>
					<th>".htmlentities($msg['38'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_ach_fou2'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_cde_date_cde'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_cde_date_ech'], ENT_QUOTES, $charset)."</th>
					<th>".htmlentities($msg['acquisition_cde_nom'], ENT_QUOTES, $charset)."</th>
				</tr>";
		while ($row=pmb_mysql_fetch_object($res)) {
			if ($id_cde == $row->id_acte){
				$nbr_lignes--;
				continue;
			}
			print "
				<tr>
					<td>
						<a href='#' onclick=\"set_parent('".$row->id_acte."','".$id_bibli."', '".$id_exercice."',  '".$callback."')\">".htmlentities($row->numero,ENT_QUOTES,$charset)."</a>
					</td>
					<td>". htmlentities($row->raison_sociale,ENT_QUOTES,$charset) ."</td>
					<td>".format_date($row->date_acte)."</td>
					<td>";
					if ($row->date_ech_calc != "00000000") {
						print format_date($row->date_ech_calc);
					}
			print"	</td>
					<td>".$row->nom_acte."</td>
				</tr>";
		}
		print "</table>";

		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;

		// affichage de la pagination
		print "<div class='row'>&nbsp;<hr /></div><div class='center'>";
		$url_base = $base_url."&user_input=".rawurlencode(stripslashes($user_input));
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		print $nav_bar;
		print "</div>";
	}
}



<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: amendes_relances.inc.php,v 1.16 2023/07/10 12:49:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $charset, $action, $lang;
global $pmb_gestion_amende;
global $quota, $elements;
global $relance_1, $relance_2, $relance_3, $statut_perdu;
global $finance_relance_1, $finance_relance_2, $finance_relance_3, $finance_statut_perdu;

//Gestion des amendes
require_once("$include_path/templates/finance.tpl.php");
require_once($class_path."/quotas.class.php");
require_once($class_path."/parameters/parameter.class.php");

function show_amende_parameters() {
	global $msg;
	global $charset;
	global $finance_relance_1,$finance_relance_2,$finance_relance_3,$finance_statut_perdu;
	$requete="select statut_libelle from docs_statut where idstatut='".$finance_statut_perdu."'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$statut_perdu=pmb_mysql_result($resultat,0,0);
	} else $statut_perdu="";
	print "
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_relance_1"]."</div><div class='colonne3'>$finance_relance_1</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_relance_2"]."</div><div class='colonne3'>$finance_relance_2</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_relance_3"]."</div><div class='colonne3'>$finance_relance_3</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'>
			<div class='colonne3' style='text-align:right;padding-right:10px'>".$msg["finance_statut_perdu"]."</div><div class='colonne3'>".$statut_perdu."</div><div class='colonne_suite'>&nbsp;</div>
		</div>
		<div class='row'></div>
		<div class='row'><input type='button' class='bouton' value='".$msg["finance_amende_modifier"]."' onClick=\"document.location='./admin.php?categ=finance&sub=amendes_relance&action=modif';\"></div>
	";
}

function show_lost_status_form() {
	global $msg,$charset;
	global $finance_statut_perdu,$finance_recouvrement_lecteur_statut;
	$result ="
	<form method='POST' action='admin.php?categ=finance&sub=amendes_relance&action=updateloststatus'>
	<h3>".$msg["finance_statut_perdu_expl_empr"]."</h3>	
		<div class='form-contenu'>		
			<div class='row'> 
				<div class='colonne2'>
					".$msg["finance_statut_expl_perdu"].":
				</div>		
				<div class='colonne_suite'>
					!!statut_expl!!
				</div>
			</div>
			<div class='row'> 
				<div class='colonne2'>
					".$msg["finance_statut_lecteur_en_recouvrement"].":
				</div>		
				<div class='colonne_suite'>
					!!statut_empr!!
				</div>				
			</div>	
			<div class='row'>	
			</div>	
		</div>	
		<div class='row'>	
			<input type='submit' class='bouton' value='".$msg["finance_amende_modifier"]."'\">			
		</div>	
	</form>	
	";	
	
	$requete="select idstatut,statut_libelle from docs_statut order by statut_libelle";
	$resultat=pmb_mysql_query($requete);
	$list_statut="<select name='statut_perdu' id='statut_perdu'>\n";
	while ($r=pmb_mysql_fetch_object($resultat)) {
		$list_statut.="<option value='".$r->idstatut."' ";
		if ($r->idstatut==$finance_statut_perdu) $list_statut.="selected='selected' ";
		$list_statut.=">".htmlentities($r->statut_libelle,ENT_QUOTES,$charset)."</option>\n";
	}
	$list_statut.="</select>\n";
	$result = str_replace( "!!statut_expl!!",$list_statut,$result);	
	
	
	$requete="select idstatut,statut_libelle from empr_statut";
	$resultat=pmb_mysql_query($requete);
	$list_statut="<select name='statut_empr' id='statut_empr'>\n<option value='0' ";
	if(!$finance_recouvrement_lecteur_statut)$list_statut.="selected='selected' ";
	$list_statut.=">".htmlentities($msg["finance_statut_lecteur_no_change"],ENT_QUOTES,$charset)."</option>\n";
	while ($r=pmb_mysql_fetch_object($resultat)) {
		$list_statut.="<option value='".$r->idstatut."' ";
		if ($r->idstatut==$finance_recouvrement_lecteur_statut) $list_statut.="selected='selected' ";
		$list_statut.=">".htmlentities($r->statut_libelle,ENT_QUOTES,$charset)."</option>\n";
	}
	$list_statut.="</select>\n";	
	$result = str_replace( "!!statut_empr!!",$list_statut,$result);	
	echo $result;
}

function update_loststatus_fromform() {
	global $statut_perdu, $statut_empr;
	
	parameter::update('finance', 'statut_perdu', stripslashes($statut_perdu));
	parameter::update('finance', 'recouvrement_lecteur_statut', stripslashes($statut_empr));
}

if ($pmb_gestion_amende==1) {
	$admin_layout = str_replace('!!menu_sous_rub!!', $msg["finance_amendes_relances"], $admin_layout);  
	  print $admin_layout;
		switch ($action) {
			case 'update':
				//Mise à jour !!
				parameter::update('finance', 'relance_1', stripslashes($relance_1));
				parameter::update('finance', 'relance_2', stripslashes($relance_2));
				parameter::update('finance', 'relance_3', stripslashes($relance_3));
				parameter::update('finance', 'statut_perdu', stripslashes($statut_perdu));
				show_amende_parameters();
				break;
			case 'modif':
				//Formulaire de mise à jour
				$interface_form = new interface_admin_form('finance_amende_form');
				$interface_form->set_label($msg["finance_amende_relance_parameters"]);
				
				$interface_content_form = new interface_content_form();
				$interface_content_form->add_element('relance_1', 'finance_relance_1')
				->add_input_node('float', $finance_relance_1);
				$interface_content_form->add_element('relance_2', 'finance_relance_2')
				->add_input_node('float', $finance_relance_2);
				$interface_content_form->add_element('relance_3', 'finance_relance_3')
				->add_input_node('float', $finance_relance_3);
				$interface_content_form->add_element('statut_perdu', 'finance_statut_perdu')
				->add_query_node('select', 'select idstatut as id,statut_libelle as label from docs_statut order by label', $finance_statut_perdu);
				$interface_form->set_content_form($interface_content_form->get_display());
				print $interface_form->get_display_parameters();
				break;
			default:
				//Gestion simple
				show_amende_parameters();
				break;
		}
} else {
	$menu_sous_rub=$msg["finance_amendes"];
	
	//Gestion par quotas
	$descriptor = "$include_path/quotas/own/$lang/finances.xml";
	if ($quota) $qt=new quota($quota,$descriptor); else quota::parse_quotas($descriptor);
	$admin_menu_quotas="<span><a href='./admin.php?categ=finance&sub=amendes_relance&action=edit_loststatus'>".$msg["finance_statut_perdu_expl_empr"]."</a></span>&nbsp;";
	$_quotas_types_ = quota::$_quotas_[$descriptor]['_types_'];
	for ($i=0; $i<count($_quotas_types_); $i++) {	
		if ($_quotas_types_[$i]["FILTER_ID"]=="amende_relance") {
			$admin_menu_quotas.="<span><a href='./admin.php?categ=finance&sub=amendes_relance&quota=".$_quotas_types_[$i]["ID"]."'>".$_quotas_types_[$i]["SHORT_COMMENT"]."</a></span>\n";
			if ($quota==$_quotas_types_[$i]["ID"]) {
				$menu_sous_rub.=" > ".$_quotas_types_[$i]["SHORT_COMMENT"];
				if ($elements) $menu_sous_rub.=" > ".$qt->get_title_by_elements_id($elements);
			}
		}
	}
	$admin_layout = str_replace('!!menu_sous_rub!!', $menu_sous_rub, $admin_layout);  
    print $admin_layout;
	print "<div class='row'>".$admin_menu_quotas."</div><div class='row'>&nbsp;</div>";	
	
	switch ($quota) {
		case "":
			switch ($action) {
				case "edit_loststatus":
					show_lost_status_form();
					break;
				case "updateloststatus":
					update_loststatus_fromform();
					show_lost_status_form();
					break;
				default:
					break;
			}
			break;
		default:
			if (!$elements) {
				$query_compl="&quota=$quota";
				include("./admin/quotas/quotas_list.inc.php");
			} else {
				$query_compl="&quota=$quota";
				include("./admin/quotas/quota_table.inc.php");
			}
			break;
	}
	
}
?>
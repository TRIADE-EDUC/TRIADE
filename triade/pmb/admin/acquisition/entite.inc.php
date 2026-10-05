<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entite.inc.php,v 1.38.8.1 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $msg;
global $id, $raison, $max_coord, $mod_, $no_;

//gestion des coordonnees des etablissements
require_once("$class_path/entites.class.php");
require_once("$include_path/templates/coordonnees.tpl.php");

function show_list_coord() {
	print list_configuration_acquisition_entite_ui::get_instance()->get_display_list();
}

//Traitement des actions
switch($action) {
	case 'update':
		// vérification validité des données fournies.( pas deux raisons sociales identiques)
		$nbr = entites::exists_rs($raison,0,$id);
		if ($nbr > 0) {
			error_form_message($raison.$msg["acquisition_raison_already_used"]);
			break;
		}

		$biblio = new entites($id);
		$biblio->type_entite = '1';
		$biblio->set_properties_from_form();
		$biblio->save();

		if ($id) {
			//màj des autorisations dans les rubriques
			$biblio->majAutorisations();
		}

		$id = $biblio->id_entite;

		for($i=1; $i <= $max_coord; $i++) {
			switch ($mod_[$i]) {
				case '1' :
					$coord = new coordonnees($no_[$i]);
					$coord->num_entite = $id;
					if ($i == 1 || $i == 2) $coord->type_coord = $i; else $coord->type_coord = 0;
					$coord->set_properties_from_form($i);
					$coord->save();
					break;
				case '-1' :
					if($no_[$i]) {
						$coord = new coordonnees($no_[$i]);
						$coord->delete($no_[$i]);
					}
					break;
				default :
					break;
			}
		}
		show_list_coord();
		break;
	case 'add':
	    $biblio = new entites();
		print $biblio->get_form();
		break;
	case 'modif':
		if (entites::exists($id)) {
			$biblio = new entites($id);
			print $biblio->get_form();
		} else {
			show_list_coord();
		}
		break;
	case 'del':
		if($id) {
			$total2 = entites::getNbFournisseurs($id);
			$total3 = entites::has_exercices($id);
			$total4 = entites::has_budgets($id);
			$total5 = entites::has_suggestions($id);
			$total7 = entites::has_actes($id,1);
			if (($total2+$total3+$total4+$total5+$total7)==0) {
				entites::staticDelete($id);
				show_list_coord();
			} else {
				$msg_suppr_err = $msg['acquisition_entite_used'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_fou'] ;
				if ($total3) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_exe'] ;
				if ($total4) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_bud'] ;
				if ($total5) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_sug'] ;
				if ($total7) $msg_suppr_err .= "<br />- ".$msg['acquisition_entite_used_act'] ;

				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=entite');
			}
		} else {
			show_list_coord();
		}
		break;
	default:
		show_list_coord();
		break;
}
?>

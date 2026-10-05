<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.95.2.1.2.1 2025/03/06 08:09:03 dbellamy Exp $

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "EDIT_AUTH";
$base_title = "\$msg[6]";
$base_noheader=1;
$base_use_dojo = true;

global $msg, $charset, $class_path, $include_path;
global $categ, $sub, $action, $dest, $current_module;
global $pmb_indexation_lang;
global $id;

require_once ("$base_path/includes/init.inc.php");
require_once($class_path."/modules/module_edit.class.php");
require_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once("$class_path/marc_table.class.php");
require_once("$class_path/docs_location.class.php");
require_once("$class_path/author.class.php");
require_once("$include_path/notice_authors.inc.php");
require_once("$include_path/notice_categories.inc.php");
require_once("$include_path/resa_func.inc.php");
require_once("$include_path/resa_planning_func.inc.php");

require_once("$include_path/explnum.inc.php");
require_once($class_path."/serialcirc_diff.class.php");
require_once($class_path."/serialcirc_print_fields.class.php");
require_once ($class_path."/spreadsheetPMB.class.php");
// modules propres à edit.php ou à ses sous-modules
require("$include_path/templates/edit.tpl.php");
require_once ($class_path."/campaigns/campaigns_controller.class.php");
require_once ($class_path."/visits_statistics/visits_statistics_controller.class.php");
require_once ($class_path."/visits_statistics/visits_statistics_date_controller.class.php");

// création de la page
switch($dest) {
	case "TABLEAU":
		break;
	case "TABLEAUHTML":
		break;
	case "TABLEAUCSV":
		break;
	case "EXPORT_NOTI":
		break;
	case "PLUGIN_FILE": // utiliser pour les plugins
		break;
	default:
        header ("Content-Type: text/html; charset=".$charset);
		print $std_header."<body class='$current_module claro' id='body_current_module' page_name='$current_module'>";
		break;
}
module_edit::get_instance()->proceed_header();

switch($categ) {
	// EDITIONS LIEES AUX NOTICES
	case "notices":
		switch($sub) {
			case "resa" :
			default :
				include("./edit/notices.inc.php");
				break;
			}
		break;
	case "serialcirc_diff":
		switch($sub) {
			case "export_empr" :
			default :
				$serialcirc_diff = new serialcirc_diff($id_serialcirc,$num_abt);
				$gen_tpl = new serialcirc_print_fields($serialcirc_diff->id);
				$worksheet = new spreadsheetPMB();
				$worksheet->write(0,0,$serialcirc_diff->serial_info['serial_name']);
				$worksheet->write(0,1,$serialcirc_diff->serial_info['abt_name']);

				$i = 2;
				$j = 0;
				// On récupère les noms de colonnes
				$header_list = $gen_tpl->get_header_list();
				foreach ($header_list as $header) {
					$worksheet->write($i, $j, $header);
					$j++;
				}
				$i++;
				$j = 0;
				foreach($serialcirc_diff->diffusion as $diff){
					if($diff['empr_type'] == SERIALCIRC_EMPR_TYPE_empr){
						$data['empr_id'] = $diff['empr']['id_empr'];
						$data_fields = $gen_tpl->get_line($data);
						foreach ($data_fields as $field) {
							$worksheet->write($i, $j, $field);
							$j++;
						}
						$i++;
						$j = 0;
					}else{
						$group_name= $diff['empr_name'];
						if(count($diff['group'])){
							foreach($diff['group'] as $empr){
								$data['empr_id'] = $empr['num_empr'];
								$data_fields = $gen_tpl->get_line($data);
								$data_fields[] = $group_name;
								if($empr['responsable']){
									$data_fields[] = $msg["serialcirc_group_responsable"];
								}
								foreach ($data_fields as $field) {
									$worksheet->write($i, $j, $field);
									$j++;
								}
								$i++;
								$j = 0;
							}
						}
					}
				}
				$worksheet->download('Circulation.xls');
			break;
		}
	break;
	// EDITIONS LIEES AUX EMPRUNTEURS
	case "empr":
		$restrict="";
		switch($sub) {
			case "limite" :
				$restrict = " ((to_days(empr_date_expiration) - to_days(now()) ) <=  $pmb_relance_adhesion ) and empr_date_expiration >= now() ";
				include("./edit/empr_list.inc.php");
				break;
			case "depasse" :
				$restrict = " empr_date_expiration < now() ";
				include("./edit/empr_list.inc.php");
				break;
			case "cashdesk" :
				$titre_page = $msg["1120"].": ".$msg["cashdesk_edition_menu"];
				include("./edit/cashdesk.inc.php");
				break;
			case "categ_change" :
				if (isset($categ_action) && $categ_action=="change_categ_empr") {
					if(isset($readers_edition_ui_selected_objects)) {
						for ($i=0; $i<count($readers_edition_ui_selected_objects); $i++) {
							$id_empr=$readers_edition_ui_selected_objects[$i];
							if(!empty($readers_edition_ui_categ_change[$id_empr])) {
								$act = $readers_edition_ui_categ_change[$id_empr];
								if ($act!=0) {
									// on modifie la catégorie du lecteur si demandé
									if($id_empr){
										$requete="update empr set empr_categ=$act where id_empr=$id_empr";
										pmb_mysql_query($requete);
									}
								}
							}
						}
					}
				}
				$restrict = " ((((age_min<> 0) || (age_max <> 0)) && (age_max >= age_min)) && (((DATE_FORMAT( curdate() , '%Y' )-empr_year) < age_min) || ((DATE_FORMAT( curdate() , '%Y' )-empr_year) > age_max))) ";
				include("./edit/empr_list.inc.php");
				break;
			default :
			case "encours" :
				$sub = "encours" ;
				$restrict = " empr_date_expiration >= now() ";
				include("./edit/empr_list.inc.php");
				break;
		}
		break ;
	// EDITIONS LIEES AUX PERIODIQUES
	case "serials":
		switch($sub) {
			/* en attente d'une gestion correcte du bulletinage, actuellement absente de la base de données.
			case "manquant" :
				include("./edit/serials_manq.inc.php");
				break;
			*/
			case "circ_state" :
				include("./edit/serials_circ_state.inc.php");
				break;
			case "simple_circ" :
				include("./edit/serials_simple_circ.inc.php");
				break;
			case "collect" :
			default :
				$sub = "collect" ;
				include("./edit/serials_coll.inc.php");
				break;
			}
		break;

	// EDITIONS DES STATISTIQUES
	case "procs":
		switch($dest) {
			case "TABLEAUCSV":
			default:
				include_once("./edit/procs.inc.php");
				break;
			}
		break;

	// CODES A BARRES
	case "cbgen":
		switch($sub) {
			default :
			case "libre" :
				$sub = "libre" ;
				include("./edit/cbgenlibre.inc.php");
				break;
			}
		break;

	//LES TRANSFERTS
	case "transferts" :
		require_once ("./edit/transferts.inc.php");
	break;

	//DEMANDES DE TRANSFERTS
	case "transferts_demandes" :
		print list_transferts_demandes_ui::get_instance()->get_display_list();
		break;

	//STATISTIQUES DE L'OPAC
	case "stat_opac" :
		include("./edit/stat_opac.inc.php");
		break;

	//OPAC
	case "opac" :
		switch($sub) {
			case "campaigns" :
				campaigns_controller::proceed($id);
				break;
			case 'visits_statistics':
			    global $visits_statistics_ui_date;
			    if (!empty($visits_statistics_ui_date)) {
			        $matches = [];
			        if(pmb_preg_match("#(\d{4})[-/\.](\d{2})[-/\.](\d{2})#",$visits_statistics_ui_date, $matches)) {
			            visits_statistics_controller::proceed($id);
			        }
			    } else {
			        visits_statistics_date_controller::proceed($id);
			    }
				break;
		}
		break;

	// Edition Template de notices
	case "tpl" :
		switch($sub) {
			case "serialcirc" :
				include("./edit/serialcirc_tpl.inc.php");
				break;
			case "notice" :
			default :
				include("./edit/notice_tpl.inc.php");
			break;
			case "bannette" :
				include("./edit/bannette_tpl.inc.php");
				break;
			case "print_cart_tpl" :
			    include("./edit/print_cart_tpl.inc.php");
			    break;
		}
	break;
	case "state" :
		include($base_path."/edit/editions_state/main.inc.php");
		break;
	case "pnb" :
		include($base_path."/edit/pnb.inc.php");
		break;
	case 'contribution_area':
	    lists_controller::set_list_ui_class_name("list_contributions_ui");
	    lists_controller::proceed();
	    break;
	// EDITIONS LIEES AUX EXEMPLAIRES
	default:
	case "expl":
		$categ = "expl" ;
		switch($sub) {
				case "ppargroupe" :
					include("./edit/expl_groupe.inc.php");
					break;
				case "rpargroupe" :
					include("./edit/expl_groupe.inc.php");
					break;
				case "retard" :
					include("./edit/expl.inc.php");
					break;
				case "retard_par_date" :
					include("./edit/expl.inc.php");
					break;
				case "owner" :
					$critere_requete=" order by idlender, expl_cote, expl_cb ";
					include("./edit/expl_owner.inc.php");
					break;
				case 'short_loans' :
					include("./edit/expl.inc.php");
					break;
				case 'unreturned_short_loans' :
					include("./edit/expl.inc.php");
					break;
				case 'overdue_short_loans' :
					include("./edit/expl.inc.php");
					break;
				case 'archives' :
					include("./edit/expl.inc.php");
					break;
				default :
				case "encours" :
					$sub = "encours" ;
					include("./edit/expl.inc.php");
					break;
				}
			break;
		case 'sticks_sheet' :
			switch($sub) {
				case 'models' :
					include("./edit/sticks_sheet_models.inc.php");
					break;
			}
			break;
		case 'barcodes_sheets' :
			switch($sub) {
				case 'models' :
					require_once($class_path."/barcodes_sheets/barcodes_sheets_controller.class.php");
					barcodes_sheets_controller::proceed($id);
					break;
			}
			break;
		case 'plugin' :
			$plugins = plugins::get_instance();
			$file = $plugins->proceed("edit",$plugin,$sub);
			if($file){
				include $file;
			}
			break;
}
module_edit::get_instance()->proceed_footer();
switch($dest) {
	case "TABLEAU":
	case "TABLEAUCSV":
	case "EXPORT_NOTI":
	case "PLUGIN_FILE":
		break;
	case "TABLEAUHTML":
		break;
	default:
		print "</body>" ;
		break;
}

pmb_mysql_close();

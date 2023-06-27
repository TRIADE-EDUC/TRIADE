<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_dashboard.class.php,v 1.11 2019-06-12 12:48:06 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

require_once($class_path."/scheduler/scheduler_tasks.class.php");
require_once($class_path."/scheduler/scheduler_task_docnum.class.php");
require_once($class_path."/scheduler/scheduler_progress_bar.class.php");
require_once($base_path."/admin/planificateur/templates/tache_rapport.tpl.php");
require_once($class_path.'/list/list_scheduler_dashboard_ui.class.php');

class scheduler_dashboard {
	
	public function __construct() {
		
	}
	
	public function get_display_list() {
		$list_scheduler_dashboard_ui = new list_scheduler_dashboard_ui();
		return $list_scheduler_dashboard_ui->get_display_list();
	}
	
	//retourne le nombre de tâches associé à un type de tâche
	public function get_nb_docnum($id_tache) {
		$id_tache += 0;
		$result = pmb_mysql_query("select * from taches t, taches_docnum tdn where t.id_tache=tdn.num_tache and id_tache=".$id_tache);
		return pmb_mysql_num_rows($result);
	}
	
	// Envoi d'une commande pour l'interprétation...
	public function command_waiting($id_tache,$cmd=''){
		global $dbh,$msg, $charset;
	
		$requete_sql = "select status, commande from taches where id_tache='".$id_tache."' and end_at='0000-00-00 00:00:00'";
		$result = pmb_mysql_query($requete_sql);
		if(pmb_mysql_num_rows($result) == "1") {
			$status = pmb_mysql_result($result, 0,"status");
			$commande = pmb_mysql_result($result, 0,"commande");
		} else {
			$status = '';
			$commande = 0;
		}
	
		// une commande a déjà été envoyée auparavant...
		if ($commande != '0') {
			$cmd = $commande;
		}
	
		if ($cmd != '') {
			//check command - la commande envoyée est vérifié par rapport au status
			$scheduler_tasks = new scheduler_tasks();
			foreach($scheduler_tasks->tasks as $tasks_type) {
				$states = $tasks_type->get_states();
				foreach ($states as $state) {
					if ($state["id"] == $status) {
						foreach($state["nextState"] as $nextState) {
							$commands = $tasks_type->get_commands();
							foreach($commands as $command) {
								if ($nextState["command"] == $command["name"]) {
									if ($command["id"] == $cmd)
										pmb_mysql_query("update taches set commande=".$cmd.", next_state='".constant($nextState["value"])."' where id_tache=".$id_tache, $dbh);
								}
							}
						}
					}
				}
			}
		}
	
		$rs = pmb_mysql_query("select t.start_at, t.commande, p.calc_next_date_deb, p.calc_next_heure_deb
			from taches t , planificateur p
			where t.num_planificateur = p.id_planificateur
			and id_tache=".$id_tache);
		$tpl = "<td id='commande_tache_".$id_tache."' class='center'>";
		if ($rs) {
			$row = pmb_mysql_fetch_object($rs);
			if($row->start_at == '0000-00-00 00:00:00') {
				$tpl .= htmlentities(formatdate($row->calc_next_date_deb),ENT_QUOTES,$charset)." ".htmlentities($row->calc_next_heure_deb,ENT_QUOTES,$charset);
			} else if (($row->start_at != '0000-00-00 00:00:00') && ($row->commande != NULL) && $row->commande) {
				$tpl .= utf8_normalize($msg["planificateur_command_$row->commande"]);
			}
		}
		$tpl .= "</td>";
	
		return $tpl;
	}
	
	public static function get_report_datas($id) {
		$id += 0;
		$query = "SELECT t.id_tache, p.num_type_tache, p.libelle_tache, t.start_at, t.end_at, t.status, t.indicat_progress, t.rapport, t.num_planificateur FROM taches t,planificateur p
				Where t.num_planificateur = p.id_planificateur
				And t.id_tache=".$id."
				order by p.calc_next_date_deb DESC";
		$res=pmb_mysql_query($query);
	
		if (pmb_mysql_num_rows($res)) {
			$r = pmb_mysql_fetch_object($res);
			$task["id_tache"]=$r->id_tache;
			$task["num_planificateur"]=$r->num_planificateur;
			$task["libelle_tache"]=$r->libelle_tache;
			$task["start_at"]= explode (" ",$r->start_at);
			$task["end_at"]= explode (" ",$r->end_at);
			$task["status"] = $r->status;
			$task["indicat_progress"] = $r->indicat_progress;
			$task["rapport"] = unserialize(htmlspecialchars_decode($r->rapport, ENT_QUOTES));
		} else {
			$task["id_tache"]="";
			$task["num_planificateur"]="";
			$task["libelle_tache"]="";
			$task["start_at"]="";
			$task["end_at"]="";
			$task["status"] = "";
			$task["indicat_progress"] = "";
			$task["rapport"] = "";
		}
		return $task;
	}
	
	//appelée si show_report non existant classe spécifique fille
	public static function get_display_details($details) {
		global $charset;
	
		$display = '';
		if (is_array($details)) {
			$display = "<table>";
			foreach ($details as $ligne) {
				if (is_array($ligne)) {
					foreach ($ligne as $une_ligne) {
						$display .= html_entity_decode($une_ligne, ENT_QUOTES, $charset)."<br />";
					}
				} else {
					$display .= html_entity_decode($ligne, ENT_QUOTES, $charset);
				}
			}
			$display .= "</table>";
		}
	
		return $display;
	}
	
	public static function get_css_for_pdf_report() {
		return "
			<style type='text/css'>
				.report_title {
					font-weight : bold;
				}
				table.scheduler_task_details_infos {
					width: 100%;
					border-width: 1px;
					border-style: solid;
					border-color: gray;
					margin-top: 10px;
				}
				table.scheduler_task_details_infos th  {
				
				}
				.cols_header { width:40%; }
				.cols2header { width:40%; }
				.cols_header2 { width:60%; }
				.cols2header2 { width:60%; }
				</style>
			";
	}
	
	protected static function get_report_details($show_logs=1) {
		global $msg;
		global $task_report_details;
		global $task_id, $type_task_id;
		
		$task_datas = scheduler_dashboard::get_report_datas($task_id);
		
		$report=$task_report_details;
		$report=str_replace("!!date_mysql!!",formatdate(pmb_mysql_result(pmb_mysql_query("select curdate()"), 0)),$report);
		$report=str_replace("!!id!!",$task_datas["id_tache"],$report);
		$report=str_replace("!!libelle_task!!",stripslashes($task_datas["libelle_tache"]),$report);
		$report=str_replace("!!date_dern_exec!!",formatdate($task_datas['start_at'][0]),$report);
		$report=str_replace("!!heure_dern_exec!!",$task_datas['start_at'][1],$report);
		$report=str_replace("!!date_fin_exec!!",($task_datas['end_at'][0] != '0000-00-00' ? formatdate($task_datas['end_at'][0]) : ''),$report);
		$report=str_replace("!!heure_fin_exec!!",($task_datas['end_at'][1] != '00:00:00' ? $task_datas['end_at'][1] : ''),$report);
		$report=str_replace("!!status!!",$msg["planificateur_state_".$task_datas["status"]],$report);
		$report=str_replace("!!percent!!",$task_datas["indicat_progress"],$report);
		
		$report=str_replace("!!rapport!!", scheduler_dashboard::get_display_details($task_datas["rapport"]), $report);
			
		$log_errors = '';
		$log_filename = 'scheduler_'.scheduler_tasks::get_catalog_element($type_task_id, 'NAME').'_task_'.$task_id.'.log';
		$log_errors_content = scheduler_log::get_content($log_filename);
		if($show_logs && $log_errors_content) {
			$log_errors .= '
					<table>
						<tr><th>'.$log_filename.'</th></tr>
						<tr><td>
							<div class="error">'.$log_errors_content.'</div>
						</td></tr>
					</table>';
		}
		$report=str_replace("!!log_errors!!", $log_errors, $report);
		return $report;
	}
	
	public static function get_report() {
		global $report_task, $report_error;
		global $task_id, $type_task_id;
	
		$task_id += 0;
		$query = "select id_tache from taches where id_tache=".$task_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			//affiche le rapport avec passage du template
			$report_task = str_replace("!!print_report!!", "<a onclick=\"openPopUp('./pdf.php?pdfdoc=rapport_tache&type_task_id=$type_task_id&task_id=".$task_id."', 'print_PDF')\" href=\"#\"><img src='".get_url_icon('print.gif')."' alt='Imprimer...' /></a>", $report_task);
			$report_task = str_replace("!!type_tache_name!!", scheduler_tasks::get_catalog_element($type_task_id, 'COMMENT'), $report_task);
			$report_task = str_replace("!!details!!", static::get_report_details(), $report_task);
			return $report_task;
		} else {
			return $report_error;
		}
	}
	
	public static function show_pdf_report() {
		global $msg, $dbh, $base_path, $task_id, $type_task_id;
		global $pmb_pdf_font;
		
		$task_id += 0;
		$query = "select id_tache from taches where id_tache=".$task_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$html2pdf = new Html2Pdf('P','A4','fr');
			$template = static::get_css_for_pdf_report();
			$template .= '<div class="report_title">'.scheduler_tasks::get_catalog_element($type_task_id, 'COMMENT').'</div>';
			$template .= static::get_report_details(0);
			$html2pdf->writeHTML($template);
			$html2pdf->output('scheduler_'.$task_id.'.pdf');
		}
	}
	
	//documents numériques par tâches en cours ou exécutées
	public function get_display_docsnum($task_id) {
		$task_id += 0;
		$query = "SELECT id_tache_docnum, tache_docnum_nomfichier, tache_docnum_mimetype,tache_docnum_extfichier, tache_docnum_repertoire FROM taches_docnum WHERE num_tache = '".$task_id."'";
		$res = pmb_mysql_query($query);
		$tab_docnum = array();
		if ($res) {
			while ($row=pmb_mysql_fetch_object($res)) {
				$t=array();
				$t["id_tache_docnum"] = $row->id_tache_docnum;
				$t["tache_docnum_nomfichier"] = $row->tache_docnum_nomfichier;
				$t["tache_docnum_mimetype"] = $row->tache_docnum_mimetype;
				$t["tache_docnum_extfichier"] = $row->tache_docnum_extfichier;
				$t["tache_docnum_repertoire"] = $row->tache_docnum_repertoire;
				$tab_docnum[] = $t;
			}
		}
		$display = "<tr style='cursor: pointer' >";
		$scheduler_task_docnum = new scheduler_task_docnum();
		$display .= $scheduler_task_docnum->show_docnum_table($tab_docnum);
		$display .= "</tr>";
		return $display;
	}
}
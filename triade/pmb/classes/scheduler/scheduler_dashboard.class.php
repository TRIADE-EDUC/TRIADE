<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_dashboard.class.php,v 1.19 2024/03/08 07:36:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Spipu\Html2Pdf\Html2Pdf;

global $base_path, $class_path;
require_once($class_path."/scheduler/scheduler_tasks.class.php");
require_once($class_path."/scheduler/scheduler_progress_bar.class.php");
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($base_path."/admin/planificateur/templates/tache_rapport.tpl.php");

class scheduler_dashboard {
	
	public function get_display_list() {
		$list_scheduler_dashboard_ui = new list_scheduler_dashboard_ui();
		return $list_scheduler_dashboard_ui->get_display_list();
	}
	
	// Envoi d'une commande pour l'interprétation...
	public function command_waiting($id_tache,$cmd=''){
		global $msg;
	
		$id_tache = intval($id_tache);
		$scheduler_task = scheduler_task::get_instance($id_tache);
		if (!empty($cmd)) {
			$scheduler_task->update_command($cmd);
		}
		if($scheduler_task->start_at == '0000-00-00 00:00:00') {
			if(!empty($scheduler_task->commande)) {
				return $msg['planificateur_command_'.$scheduler_task->commande];
			} else {
				return formatdate($scheduler_task->calc_next_date_deb)." ".$scheduler_task->calc_next_heure_deb;
			}
		} else if (($scheduler_task->start_at != '0000-00-00 00:00:00') && !empty($scheduler_task->commande)) {
			return $msg['planificateur_command_'.$scheduler_task->commande];
		}
		return '';
	}
	
	//appelée si show_report non existant classe spécifique fille
	public static function get_display_details($details, $msg_statut) {
		global $charset;
	
		$display = "<table class='scheduler_task_details_display'>";
		if(!empty($details)) {
			foreach ($details as $ligne) {
				if (is_array($ligne)) {
					foreach ($ligne as $une_ligne) {
						$display .= html_entity_decode($une_ligne, ENT_QUOTES, $charset)."<br />";
					}
				} else {
					$display .= html_entity_decode($ligne, ENT_QUOTES, $charset);
				}
			}
		}
		if(!empty($msg_statut)) {
			foreach ($msg_statut as $action) {
				//une action est-elle en cours ?
				if(!empty($action['progression']) && $action['progression'] < 100) {
					if(!empty($action['details'])) {
						if(!empty($action['details']['title'])) {
							$display .= "<tr><th class='scheduler_report_section'>".htmlentities($action['details']['title'], ENT_QUOTES, $charset)."</th></tr>";
							$display .= "<tr><td class='scheduler_report_content'>".htmlentities($action['details']['message'], ENT_QUOTES, $charset)."</td></tr>";
						} else {
							foreach ($action['details'] as $detail) {
								$display .= "<tr><th class='scheduler_report_section'>".htmlentities($detail['title'], ENT_QUOTES, $charset)."</th></tr>";
								$display .= "<tr><td class='scheduler_report_content'>".htmlentities($detail['message'], ENT_QUOTES, $charset)."</td></tr>";
							}
						}
					}
				}
				
			}
		}
		$display .= "</table>";
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
				.cols_header { width:40%; }
				.cols2header { width:40%; }
				.cols_header2 { width:60%; }
				.cols2header2 { width:60%; }
				</style>
			";
	}
	
	protected static function get_report_details($id=0, $show_logs=1) {
		global $charset;
		global $task_report_details;
		
		$id = intval($id);
		$scheduler_task = new scheduler_task($id);
		$start_at = explode (" ", $scheduler_task->start_at);
		$end_at = explode (" ", $scheduler_task->end_at);
		$report=$task_report_details;
		$report=str_replace("!!date_mysql!!",formatdate(pmb_mysql_result(pmb_mysql_query("select curdate()"), 0)),$report);
		$report=str_replace("!!libelle_task!!",htmlentities($scheduler_task->libelle_tache, ENT_QUOTES, $charset),$report);
		$report=str_replace("!!date_dern_exec!!",formatdate($start_at[0]),$report);
		$report=str_replace("!!heure_dern_exec!!",$start_at[1],$report);
		$report=str_replace("!!date_fin_exec!!",($end_at[0] != '0000-00-00' ? formatdate($end_at[0]) : ''),$report);
		$report=str_replace("!!heure_fin_exec!!",($end_at[1] != '00:00:00' ? $end_at[1] : ''),$report);
		$report=str_replace("!!status!!", htmlentities($scheduler_task->get_status_label(), ENT_QUOTES, $charset),$report);
		$report=str_replace("!!percent!!", $scheduler_task->indicat_progress,$report);
		
		$report=str_replace("!!rapport!!", static::get_display_details($scheduler_task->report, $scheduler_task->msg_statut), $report);
			
		$log_errors = '';
		$type_id = scheduler_task::get_num_type_from_id($id);
		$log_filename = 'scheduler_'.scheduler_tasks::get_catalog_element($type_id, 'NAME').'_task_'.$id.'.log';
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
		$report=str_replace("!!id!!", $id, $report);
		return $report;
	}
	
	public static function get_report($id) {
		global $report_task, $report_error;
	
		$id = intval($id);
		if ($id) {
			//affiche le rapport avec passage du template
			$report_task = str_replace("!!print_report!!", "<a onclick=\"openPopUp('./pdf.php?pdfdoc=rapport_tache&id=".$id."', 'print_PDF')\" href=\"#\"><img src='".get_url_icon('print.gif')."' alt='Imprimer...' /></a>", $report_task);
			$type_id = scheduler_task::get_num_type_from_id($id);
			$report_task = str_replace("!!type_tache_name!!", scheduler_tasks::get_catalog_element($type_id, 'COMMENT'), $report_task);
			$report_task = str_replace("!!details!!", static::get_report_details($id), $report_task);
			$report_task=str_replace("!!id!!",$id,$report_task);
			return $report_task;
		} else {
			return $report_error;
		}
	}
	
	public static function show_pdf_report($id) {
		$id = intval($id);
		if($id) {
			$html2pdf = new Html2Pdf('P','A4','fr');
			$template = static::get_css_for_pdf_report();
			$template .= '<page orientation="l" backtop="2mm" backbottom="2mm" backleft="2mm" backright="5mm" >';
			$type_id = scheduler_task::get_num_type_from_id($id);
			$template .= '<div class="report_title">'.scheduler_tasks::get_catalog_element($type_id, 'COMMENT').'</div>';
			$template .= '<div width="100%">';
			$template .= encoding_normalize::utf8_normalize(static::get_report_details($id, 0));
			$template .= '</div>';
			$template .= '</page>';
			$html2pdf->writeHTML($template);
			$html2pdf->output('scheduler_'.$id.'.pdf');
		}
	}
}
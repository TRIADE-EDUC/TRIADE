<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tache_rapport.tpl.php,v 1.10 2024/01/05 10:29:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $charset;
global $report_task;
global $task_report_details;
global $report_error;

// Default Params
$param = array();
$param['font_face']		= 'Times New Roman, Verdana, Arial, Helvetica'; // Default font to use
$param['font_size']		= 13; // Font size in px
$param['bg_color']		= '#EEEEEE';
$param['bg2color']		= '#DDDDDD';
$param['today_bg_color']	= '#A0C0C0';
$param['font_today_color']	= '#990000';
$param['font_color']		= '#000000';
$param['font_nav_bg_color']	= '#A9B4B3';
$param['font_nav_color']	= '#FFFFFF';
$param['font_header_color']	= '#FFFFFF';
$param['border_color']	= '#3f6551';
	
$report_task = '
<style type="text/css">
	<!--
	.cols_header { background-color : '.$param['bg_color'].'; width:40%; }
	.cols2header { background-color : '.$param['bg2color'].'; width:40%; }
	.cols_header2 { background-color : '.$param['bg_color'].'; width:60%; }
	.cols2header2 { background-color : '.$param['bg2color'].'; width:60%; }
	.rapportTop_!!id!! 	{  font-family: '.$param['font_face'].'; font-size: '.($param['font_size']+2).'px; font-style: normal;  }
	.rapportTache_!!id!! {  font-size: '.$param['font_size'].'px; border: 0px; overflow: auto; height:200px; }
	-->
</style>';

//template report task
$report_task .= '
<div id="div_rapport_!!id!!" style="display:block; " class="rapportTop_!!id!!">
	<div class="right"><a href="#" onClick="parent.kill_frame(\'frame_notice_preview\');return false;"><img src="'.get_url_icon('close.gif').'" style="border:0px" class="align_right"></a></div>
</div>
<br />
!!print_report!!
<b>'.$msg['planificateur_type'].' :</b>
<span class="header_title">!!type_tache_name!!</span>
<br />
<br />
!!details!!
';

$task_report_details = '
<div class="scheduler_task_details">
	<table class="scheduler_task_details_infos">
		<tr>
			<td class="cols2header">'.htmlentities($msg["planificateur_task_name"], ENT_QUOTES, $charset).' :</td>
			<td class="cols2header2">!!libelle_task!!</td>
		</tr>
		<tr>
			<td class="cols_header">'.htmlentities($msg["tache_date_generation"], ENT_QUOTES, $charset).' :</td>
			<td class="cols_header2">!!date_mysql!!</td>
		</tr>
		<tr>
			<td class="cols2header">'.htmlentities($msg["tache_date_dern_exec"], ENT_QUOTES, $charset).' :</td>
			<td class="cols2header2">!!date_dern_exec!!</td>
		</tr>
		<tr>
			<td class="cols_header">'.htmlentities($msg["tache_heure_dern_exec"], ENT_QUOTES, $charset).' :</td>
			<td class="cols_header2">!!heure_dern_exec!!</td>
		</tr>
		<tr>
			<td class="cols2header">'.htmlentities($msg["tache_date_fin_exec"], ENT_QUOTES, $charset).' :</td>
			<td class="cols2header2">!!date_fin_exec!!</td>
		</tr>
		<tr>
			<td class="cols_header">'.htmlentities($msg["tache_heure_fin_exec"], ENT_QUOTES, $charset).' :</td>
			<td class="cols_header2">!!heure_fin_exec!!</td>
		</tr>
		<tr>
			<td class="cols2header">'.htmlentities($msg["tache_statut"], ENT_QUOTES, $charset).' :</td>
			<td class="cols2header2">!!status!! (!!percent!!%)</td>
		</tr>
	</table>
</div>
<div class="row">
	<div class="center"><label for="space">&nbsp;</label></div>
</div>
<div class="row">
	<div class="align_left rapportExec">
		<table id="tache_report" style="width:100%">
			<tr class="center">
				<th>'.htmlentities($msg["tache_report_execution"], ENT_QUOTES, $charset).'</th>
			</tr>
		</table>
	</div>
	<div class="align_left rapportTache_!!id!!">
		!!rapport!!
		!!log_errors!!
	</div>
</div>';

//template report task
$report_error= '
<div id="div_rapport_error" style="display:block; ">
	<br />
	<br />
	<div class="row">
		<div class="center"><h2>'.$msg["planificateur_report_error"].'</h2></div>
	</div>
</div>';
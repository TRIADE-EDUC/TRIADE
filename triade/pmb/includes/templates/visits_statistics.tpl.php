<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visits_statistics.tpl.php,v 1.5.8.1 2024/10/15 14:31:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $visits_statistics_form, $visits_statistics_main_line, $visits_statistics_service_line, $visits_statistics_form_counter, $visits_statistics_shortcut_button, $msg;
$visits_statistics_form = '
		<div class="row" id="visits_statistics">
			<div class="!!module!!">
				<form name="" action="" method="post" onsubmit="return false;" class="form-!!module!!">
					<h3>'.$msg['dashboard_visits_statistics'].' (!!visits_statistics_date!!)</h3>
					<div class="form-contenu">
						<div class="row  visits_statistics_buttons">
							!!visits_statistics_shortcuts_buttons!!
						</div>
						<table class="row visits_statistics_table">
							!!visits_statistics_main_lines!!
							!!visits_statistics_service_lines!!
						</table>
					</div>
					<div class="row"></div>
				</form>
			</div>
			<script type="text/javascript">
				require(["apps/pmb/VisitsStatistics"], function(VisitsStatistics) {
					new VisitsStatistics();
				});
			</script>
		</div>';

$visits_statistics_main_line = '
		<tr class="visits_statistics_row" style="vertical-align:middle;line-height:27px;">
			<td class="visits_statistics_cell">
				!!visits_statistics_color!!
				&nbsp;!!visits_statistics_main_name!!
			</td>
			<td class="visits_statistics_cell">
				!!visits_statistics_main_counter!!
			</td>
			<td colspan="2"></td>
		</tr>';

$visits_statistics_service_line = '
		<td class="visits_statistics_cell" style="vertical-align:middle;line-height:27px;">
			!!visits_statistics_color!!
			&nbsp;!!visits_statistics_service_name!!
		</td>
		<td class="visits_statistics_cell">
			!!visits_statistics_service_counter!!
		</td>';

$visits_statistics_form_counter = '
		<input type="button" class="bouton visits_statistics_button" value="-" id="visits_statistics_!!counter_type!!_remove_button" counter_type="!!counter_type!!" action="remove"/>
		<input type="text" class="visits_statistics_input" value="!!count!!" size="2" maxlength="4" id="visits_statistics_!!counter_type!!_input" counter_type="!!counter_type!!" style="text-align: right;"/>
		<input type="button" class="bouton visits_statistics_button" value="+" id="visits_statistics_!!counter_type!!_add_button" counter_type="!!counter_type!!" action="add"/>';

$visits_statistics_shortcut_button = '
		<input type="button" id="visits_statistics_shortcut_button_!!counter_type!!" class="visits_statistics_button" value="" counter_type="!!counter_type!!" action="add" style="width: 40px; height: 40px; background-color:!!visits_statistics_color!!; cursor: pointer;" title="!!visits_statistics_title!!"/>';
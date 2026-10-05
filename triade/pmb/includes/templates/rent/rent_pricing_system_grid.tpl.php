<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_system_grid.tpl.php,v 1.2.16.1 2025/03/19 11:33:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_pricing_system_grid_js_form_tpl, $rent_pricing_system_grid_content_form_tpl;
global $rent_pricing_system_grid_content_form_interval_tpl, $rent_pricing_system_grid_content_form_percent_tpl;
global $msg, $current_module, $charset;

$rent_pricing_system_grid_js_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<script type='text/javascript'>
	var msg_pricing_system_grid_reset_confirm = '".addslashes($msg['pricing_system_grid_reset_confirm'])."';
	var msg_pricing_system_grid_error_first_interval = '".addslashes($msg['pricing_system_grid_error_first_interval'])."';
	var msg_pricing_system_grid_error_interval = '".addslashes($msg['pricing_system_grid_error_interval'])."';
	var msg_pricing_system_grid_error_value = '".addslashes($msg['pricing_system_grid_error_value'])."';
	var msg_pricing_system_grid_error = '".addslashes($msg['pricing_system_grid_error'])."';
</script>";

$rent_pricing_system_grid_content_form_tpl = "
<div class='row'>
	<h3>
		".htmlentities($msg['pricing_system_grid_intervals'],ENT_QUOTES,$charset)."
		<input class='bouton' type='button' value='+' onClick=\"pricing_system_grid_add_interval();\" />
	</h3>
</div>
<div class='row'>
	<div class='colonne10'>
		<label class='etiquette'>".htmlentities($msg['pricing_system_grid_time_start'],ENT_QUOTES,$charset)."</label>
	</div>		
	<div class='colonne10'>
		<label class='etiquette'>".htmlentities($msg['pricing_system_grid_time_end'],ENT_QUOTES,$charset)."</label>
	</div>		
	<div class='colonne10'>
		<label class='etiquette'>".$msg['pricing_system_grid_price']."</label>
	</div>		
</div>
<div id='intervals_content'>
	!!grid_form_interval_tpl!!	
</div>					
<div class='row'>&nbsp;</div>
<div class='row'>
	<h3>".htmlentities($msg['pricing_system_grid_extra'],ENT_QUOTES,$charset)."</h3>
</div>
<div class='row'>
	<div class='colonne10'>
		<label class='etiquette' for='pricing_system_grid_extra_time'>".htmlentities($msg['pricing_system_grid_time'],ENT_QUOTES,$charset)."</label>
	</div>		
	<div class='colonne10'>
		<label class='etiquette' for='pricing_system_grid_extra_price'>".$msg['pricing_system_grid_price']."</label>
	</div>
</div>	
<div class='row'>		
	<div class='colonne10'>
		<input type='number' min='0' id='pricing_system_grid_extra_time' name='pricing_system_grid_extra[0][time]' class='saisie-5em' value='!!extra_time!!' />
	</div>
	<div class='colonne10'>
		<input type='text' id='pricing_system_grid_extra_price' name='pricing_system_grid_extra[0][price]' class='saisie-5em' value='!!extra_price!!' />
	</div>	
</div>
<div class='row'>&nbsp;</div>
<div class='row'>
	<h3>".htmlentities($msg['pricing_system_grid_not_used'],ENT_QUOTES,$charset)."</h3>
</div>
<div class='row'>
	<div class='colonne10'>
		<label class='etiquette' for='pricing_system_grid_not_used_price'>".$msg['pricing_system_grid_price']."</label>
	</div>
</div>			
<div class='row'>
	<input type='text' id='pricing_system_grid_not_used_price' name='pricing_system_grid_not_used[0][price]' class='saisie-5em' value='!!not_used_price!!' />
</div>			
<div class='row'>&nbsp;</div>
<div class='row'>
	<h3>
		".htmlentities($msg['pricing_system_grid_percents'],ENT_QUOTES,$charset)."
		<input class='bouton' type='button' value='+' onClick=\"pricing_system_grid_add_percent();\" />
	</h3>
</div>
<div class='row'>
	<div class='colonne10'>
		<label class='etiquette'>".$msg['pricing_system_grid_percent']."</label>
	</div>		
</div>
<div id='percents_content'>
	!!grid_form_percent_tpl!!	
</div>				
<div class='row'></div>
<input type='hidden' name='pricing_system_grid_interval_max' id='pricing_system_grid_interval_max' value='!!interval_max!!' />
<input type='hidden' name='pricing_system_grid_percent_max' id='pricing_system_grid_percent_max' value='!!percent_max!!' />
";

$rent_pricing_system_grid_content_form_interval_tpl = "
<div class='row' id='pricing_system_grid_interval_!!indice!!'>	
	<div class='colonne10'>
		<input type='number' min='0' id='pricing_system_grid_interval_time_start_!!indice!!' name='pricing_system_grid_intervals[!!indice!!][time_start]' class='saisie-5em' value='!!time_start!!' />
	</div>
	<div class='colonne10'>
		<input type='number' min='0' id='pricing_system_grid_interval_time_end_!!indice!!' name='pricing_system_grid_intervals[!!indice!!][time_end]' class='saisie-5em' value='!!time_end!!' />
	</div>
	<div class='colonne10'>		
		<input id='pricing_system_grid_interval_price_!!indice!!' name='pricing_system_grid_intervals[!!indice!!][price]' class='saisie-5em' value='!!price!!' />
	</div>	
	<div class='colonne10'>
		!!button_raz!!
	</div>	
</div>
";

$rent_pricing_system_grid_content_form_percent_tpl = "
<div class='row' id='pricing_system_grid_percent_column_!!indice!!'>
	<div class='colonne10'>
		<input type='text' id='pricing_system_grid_percent_!!indice!!' name='pricing_system_grid_percents[!!indice!!]' class='saisie-5em' value='!!percent!!' />
	</div>
	<div class='colonne10'>
		<input class='bouton' type='button' value='".$msg['raz']."' onClick=\"pricing_system_grid_delete_percent('pricing_system_grid_percent_column_!!indice!!');\" />	
	</div>
</div>
";
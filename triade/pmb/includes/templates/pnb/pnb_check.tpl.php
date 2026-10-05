<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_check.tpl.php,v 1.3 2023/04/25 13:40:14 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $pnb_check_form, $current_module, $msg;

$pnb_check_form['default'] = "
<form class='form-$current_module' name='pnb_check_form' id='pnb_check_form' action='admin.php?categ=pnb&sub=check&action=!!action!!' method='post'>
	
<h3>".$msg['admin_pnb_check_title']."</h3>
	
	<div class='form-contenu'>
	
		<p>{$msg['admin_pnb_check_intro_1']}</p>
		<p>{$msg['admin_pnb_check_intro_2']}</p>

		<!-- get_offer_files_description -->
		<!-- get_offer_files_result -->

		<!-- check_offer_files_description -->
		<!-- check_offer_files_result -->


		<div class='row'>
			<div class='left'>
				<input type='submit' class='bouton' value='".$msg['admin_pnb_check_bt_continue']."' />
				&nbsp;<!-- last_report -->
			</div>
		</div>
	</div>
</form>
";


$pnb_check_form['get_offer_files_description'] = "
<p>
	<label>{$msg['admin_pnb_check_get_offer_files_label']}</label>
</p>
<p>{$msg['admin_pnb_check_get_offer_files_comment']}</p>
";


$pnb_check_form['error'] = "
<div class='erreur'><!-- error --></div>
";

$pnb_check_form['offer_files_list'] = "
<p>
	<label>{$msg['admin_pnb_check_get_offer_files_list']}</label>
</p>
<ul>
	<!-- offer_files_list --> 
</ul>
";

$pnb_check_form['offer_files_item'] = "
<li><!-- offer_files_item --></li>
<input type='hidden' name='offer_files[]' value='!!offer_file!!' />
";


$pnb_check_form['report'] = "
<div class='form-$current_module' >

<h3>".$msg['admin_pnb_check_report_title']."</h3>

	<div class='form-contenu'>
			
		<div class='row'>
			<a href='./temp/pnb_check.txt' alt='".$msg['admin_pnb_check_dl_report']."' title='".$msg['admin_pnb_check_dl_report']."' download='pnb_check.txt' type='text/plain' >".$msg['admin_pnb_check_dl_report']."</a>
		</div>
		<hr />

		<!-- report -->

		<hr />
		<div class='row'>
			<a href='./temp/pnb_check.txt' alt='".$msg['admin_pnb_check_dl_report']."' title='".$msg['admin_pnb_check_dl_report']."' download='pnb_check.txt' type='text/plain' >".$msg['admin_pnb_check_dl_report']."</a>
		</div>
		<hr />

	</div>
</div>";

$pnb_check_form['last_report'] = "
<a href='./admin.php?categ=pnb&sub=check&action=view_last_report' alt='".$msg['admin_pnb_check_view_last_report']."' title='".$msg['admin_pnb_check_view_last_report']."' >".$msg['admin_pnb_check_view_last_report']."</a>";

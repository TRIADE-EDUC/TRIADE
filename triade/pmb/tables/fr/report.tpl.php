<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: report.tpl.php,v 1.2 2023/04/07 14:25:37 dbellamy Exp $

if(preg_match('/install_inc\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php'); 
	forbidden();
}

global $install_msg;

$report_tpl['header'] = "
	<head>
		<title>{$install_msg['report_window_title']}</title>
		<meta charset='utf-8'>
		<meta http-equiv='pragma' content='no-cache'>
		<meta http-equiv='expires' content='Wed, 30 Sept 2001 12:00:00 GMT'>
		<style type='text/css'>
		body
		{
			font-family: Verdana, Arial, sans-serif;
			background: #eeeae4;
			text-align: center;
		}
		.bouton {
			color: #fff;
			font-size: 12pt;
			font-weight: bold;
			border: 1px outset #D47800;
			background-color: #5483AC;
		}
	
		.bouton:hover {
			border-style: inset;
			border: 1px solid #ED8600;
			background-color: #7DC2FF;
		}
		#conteneur
		{
			text-align: left;
		}
		table 
		{
			width:100%;
			table-layout: fixed;
			text-align: left;
		}
		td 
		{
			text-align: left;
			vertical-align: top; 
		} 
		td.first
		{
			width:60%;
			font-weight: bold;
		}
		h2
		{
			color: #090051;
		}
		</style>
	</head>
";

$report_tpl['page'] = $report_tpl['header'] . "
	<body>
		<h1 style='text-align:center;'>{$install_msg['report_title']}</h1>
		<div id='conteneur'> 

			<h2>{$install_msg['report_params']}</h2>

				<table>
					<tr> 
						<td class='first'>{$install_msg['report_lang']}</td>
						<td><!-- lang --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_charset']}</td>
						<td><!-- charset --></td>
					</tr>
				</table>

				<table>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_system_user']}</td>
						<td><!-- mysql_system_user --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_system_pwd']}</td>
						<td><!-- mysql_system_pwd --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_system_host']}</td>
						<td><!-- mysql_system_host --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_system_db']}</td>
						<td><!-- mysql_system_db --></td>
					</tr>
				</table>

				<table>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_pmb_user']}</td>
						<td><!-- mysql_pmb_user --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_pmb_pwd']}</td>
						<td><!-- mysql_pmb_pwd --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_pmb_db']}</td>
						<td><!-- mysql_pmb_db --></td>
					</tr>
				</table>
			<hr />

			<h2>{$install_msg['report_db_install']}</h2>
				<table>
					<tr> 
						<td class='first'>{$install_msg['report_mysql_system_user_connect']}</td>
						<td><!-- report_mysql_system_user_connect --></td>
					</tr>
	
					<!-- tpl_drop_pmb_db -->
					<!-- tpl_create_pmb_db -->
					<!-- tpl_create_pmb_user -->
					<!-- tpl_mysql_pmb_user_connect -->
	
					<tr> 
						<td class='first'>{$install_msg['report_select_db']}</td>
						<td><!-- report_select_db --></td>
					</tr>

                    <!-- tpl_alter_db_charset -->

					<tr> 
						<td class='first'>{$install_msg['report_create_connexion_files']}</td>
						<td><!-- report_create_connexion_files --></td>
					</tr>

                    <tr>
						<td class='first'>{$install_msg['report_alter_mysql_variables']}</td>
						<td><!-- report_alter_mysql_variables --></td>
					</tr>

					<tr> 
						<td class='first'>{$install_msg['report_drop_temp_files']}</td>
						<td><!-- report_drop_temp_files --></td>
					</tr>
				</table>
			<hr />

			<h2>{$install_msg['report_load']}</h2>
				<table>
					<!-- tpl_load_structure -->
	
					<!-- tpl_load_minimum -->
					<!-- tpl_load_essential -->
					<!-- tpl_load_data_test -->
					<!-- tpl_load_pageo -->
					<!-- tpl_load_zen -->
	
					<!-- tpl_load_unesco -->
					<!-- tpl_load_agneaux -->
					<!-- tpl_load_environnement -->
					<!-- tpl_load_no_thesaurus -->
	
					<!-- tpl_load_bm_chambery -->
					<!-- tpl_load_dewey -->
					<!-- tpl_load_indexint_100 -->
					<!-- tpl_load_no_indexation -->
				</table>
			<hr />

			<!-- tpl_reindexation -->

			<h2>{$install_msg['report_finalisation']}</h2>
				<table>
					<tr> 
						<td class='first'>{$install_msg['report_update_pmb_admin_password']}</td>
						<td><!-- report_update_pmb_admin_password --></td>
					</tr>
					<tr> 
						<td class='first'>{$install_msg['report_rename_install_scripts']}</td>
						<td><!-- report_rename_install_scripts --></td>
					</tr>
				</table>
			<hr />

			<!-- tpl_bdd_version -->

			<div style='text-align:center;' >
				<a href='../' target='_blank'><!-- report_home_link --></a> 
			</div>
			<br />

		</div>
	</body>
</html>
";

$report_tpl['drop_pmb_db'] = "
<tr> 
	<td class='first'>{$install_msg['report_drop_pmb_db']}</td>
	<td><!-- report_drop_pmb_db --></td>
</tr>
";

$report_tpl['create_pmb_db'] = "
<tr>
	<td class='first'>{$install_msg['report_create_pmb_db']}</td>
	<td><!-- report_create_pmb_db --></td>
</tr>
";

$report_tpl['create_pmb_user'] = "
<tr>
	<td class='first'>{$install_msg['report_create_pmb_user']}</td>
	<td><!-- report_create_pmb_user --></td>
</tr>
";

$report_tpl['mysql_pmb_user_connect'] = "
<tr>
	<td class='first'>{$install_msg['report_mysql_pmb_user_connect']}</td>
	<td><!-- report_mysql_pmb_user_connect --></td>
</tr>
";

$report_tpl['alter_db_charset'] = "
<tr>
	<td class='first'>{$install_msg['report_alter_db_charset']}</td>
	<td><!-- report_alter_db_charset --></td>
</tr>
";

$report_tpl['load_structure'] = "
<tr> 
	<td class='first'>{$install_msg['report_load_structure']}</td>
	<td><!-- report_load_structure --></td>
</tr>
";

$report_tpl['load_minimum'] = "
<tr>
	<td class='first'>{$install_msg['report_load_minimum']}</td>
	<td><!-- report_load_minimum --></td>
</tr>
";

$report_tpl['load_essential'] = "
<tr>
	<td class='first'>{$install_msg['report_load_essential']}</td>
	<td><!-- report_load_essential --></td>
</tr>
";
$report_tpl['load_data_test'] = "
<tr>
	<td class='first'>{$install_msg['report_load_data_test']}</td>
	<td><!-- report_load_data_test --></td>
</tr>
";

$report_tpl['load_pageo'] = "
<tr>
	<td class='first'>{$install_msg['report_load_pageo']}</td>
	<td><!-- report_load_pageo --></td>
</tr>
";

$report_tpl['load_zen'] = "
<tr>
	<td class='first'>{$install_msg['report_load_zen']}</td>
	<td><!-- report_load_zen --></td>
</tr>
";

$report_tpl['load_unesco'] = "
<tr>
	<td class='first'>{$install_msg['report_load_unesco']}</td>
	<td><!-- report_load_unesco --></td>
</tr>
";

$report_tpl['load_agneaux'] = "
<tr>
	<td class='first'>{$install_msg['report_load_agneaux']}</td>
	<td><!-- report_load_agneaux --></td>
</tr>
";

$report_tpl['load_environnement'] = "
<tr>
	<td class='first'>{$install_msg['report_load_environnement']}</td>
	<td><!-- report_load_environnement --></td>
</tr>
";

$report_tpl['load_no_thesaurus'] = "
<tr>
	<td class='first'>{$install_msg['report_load_no_thesaurus']}</td>
	<td></td>
</tr>
";

$report_tpl['load_bm_chambery'] = "
<tr>
	<td class='first'>{$install_msg['report_load_bm_chambery']}</td>
	<td><!-- report_load_bm_chambery --></td>
</tr>
";

$report_tpl['load_dewey'] = "
<tr>
	<td class='first'>{$install_msg['report_load_dewey']}</td>
	<td><!-- report_load_dewey --></td>
</tr>
";

$report_tpl['load_indexint_100'] = "
<tr>
	<td class='first'>{$install_msg['report_load_indexint_100']}</td>
	<td><!-- report_load_indexint_100 --></td>
</tr>
";

$report_tpl['load_no_indexation'] = "
<tr>
	<td class='first'>{$install_msg['report_load_no_indexation']}</td>
	<td></td>
</tr>
";

$report_tpl['bdd_version'] = "
<div style='text-align:center;'>
	<h3><!-- report_bdd_version --></h3>
</div>
<div style='text-align:center;'>
	{$install_msg['report_bdd_version_info']}
</div>
<br />
";

$report_tpl['error_page'] = $report_tpl['header'] . "
	<body>
		<h1 style='text-align:center;'>{$install_msg['report_title']}</h1>
		<div id='conteneur'> 
			<br />
			{$install_msg['report_form_error']}&nbsp;
			<input type='button' class='bouton' onclick='window.history.go(-1);' value='{$install_msg['report_retry']}' />
		</div>
	</body>
</html>
";
			
			
$report_tpl['reindexation'] = "
<h2>{$install_msg['report_reindexation']}</h2>
	<table>
		<!-- tpl_reindexation_row -->
	</table>
<hr />
";

$report_tpl['reindexation_row'] = "
<tr> 
	<td class='first'>%1s</td>
	<td>%2s</td>
</tr>
";

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install.tpl.php,v 1.1 2021/05/03 10:13:12 dbellamy Exp $

if(preg_match('/install_inc\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php');
	forbidden();
}

global $install_msg, $pmb_version_database_as_it_should_be, $lang, $charset;
global $db_user, $user_password, $mysql_host;

$install_page = "
<!DOCTYPE html>
<html>
	<head>
		<title>{$install_msg['install_window_title']}</title>
		<meta charset='utf-8'>
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
		input.saisie:focus
		{
			background: #666;
			color: #fff;
		}
		td
		{
			text-align: left;
		}
		td.etiquette
		{
			text-align: right;
			font-weight: bold;
			font-size: smaller;
		}
		h2
		{
			color: #090051;
		}
		</style>
	</head>
	<body>
		<h1 style='text-align:center;'>{$install_msg['install_title']}</h1>
		<div id='conteneur'>
		
			<h3>{$install_msg['install_preamble_1']}</h3>
			
			<h3 style='text-align:center;color:red;'>{$install_msg['install_preamble_2']}</h3>
			
			<p>{$install_msg['install_preamble_3']}</p>
			
			<blockquote>
			
				<p>{$install_msg['install_preamble_bdd_create']}</p>
				<ul>
					<li>{$install_msg['install_preamble_bdd_create_1']}</li>
					<li>{$install_msg['install_preamble_bdd_create_2']}</li>
				</ul>
				
				<p>{$install_msg['install_preamble_bdd_fill']}</p>
				<ul>
					<li>{$install_msg['install_preamble_bdd_fill_1']}</li>
					<li>{$install_msg['install_preamble_bdd_fill_2']}</li>
					<li>{$install_msg['install_preamble_bdd_fill_3']}</li>
				</ul>
				
				<p>{$install_msg['install_preamble_thesaurus']}</p>
				<ul>
<!-- 					<li>{$install_msg['install_preamble_thesaurus_1']}</li> -->
					<li>{$install_msg['install_preamble_thesaurus_2']}</li>
<!-- 					<li>{$install_msg['install_preamble_thesaurus_3']}</li> -->
				</ul>
				
				<p>{$install_msg['install_preamble_indexation']}</p>
				<ul>
					<li>{$install_msg['install_preamble_indexation_1']}</li>
<!-- 					<li>{$install_msg['install_preamble_indexation_2']}</li> -->
					<li>{$install_msg['install_preamble_indexation_3']}</li>
				</ul>
			</blockquote>
			
			<hr />
			
			<form method='post' action='install_rep.php'>
			
				<h2>{$install_msg['install_system_param']}</h2>
				
				<p>{$install_msg['install_system_param_intro']}</p>
				
				<table style='width:100%;border:0;' >
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_system_param_mysql_user']}</td>
						<td><input class='saisie' name='usermysql' type='text' id='usermysql' readonly value='$db_user' /></td>
					</tr>
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_system_param_mysql_pwd']}</td>
						<td><input class='saisie' name='passwdmysql' type='password' id='passwdmysql' readonly value='$user_password' /></td>
					</tr>
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_system_param_mysql_server']}</td>
						<td><input class='saisie' name='dbhost' type='text' id='dbhost' readonly value='$mysql_host' /></td>
					</tr>
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_system_param_mysql_bdd']}</td>
						<td>
							<input class='saisie' name=\"dbnamedbhost\" type=\"text\" onChange=\"
						        if (this.form.dbnamedbhost.value!='') {
						        	this.form.user.value='';
						        	this.form.passwd.value='';
						        	this.form.dbname.value='';
						        	this.form.user.style.display = 'none';
						        	this.form.passwd.style.display = 'none';
						        	this.form.dbname.style.display = 'none';
						        	document.getElementById('fixeuser').style.display = 'inline';
						        	document.getElementById('fixepasswd').style.display = 'inline';
						        	document.getElementById('fixedbname').style.display = 'inline';
						        } else {
					        		this.form.user.style.display = 'block';
					        		this.form.passwd.style.display = 'block';
					        		this.form.dbname.style.display = 'block';
					        		document.getElementById('fixeuser').style.display = 'none';
					        		document.getElementById('fixepasswd').style.display = 'none';
					        		document.getElementById('fixedbname').style.display = 'none';
				        		}
						        \" />
						</td>
					</tr>
				</table>
				
				<p>{$install_msg['install_system_param_comments']}</p>
				
	    		<hr />
	    		
				<h2>{$install_msg['install_pmb_param']}</h2>
				
				<p>{$install_msg['install_pmb_param_intro']}</p>
				
				<table style='width:100%; border:0;'>
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_pmb_param_mysql_user']}</td>
						<td>
							<input class='saisie' type='text' name='user' value='bibli' />
							<div id='fixeuser' style='display:none;'>
								<strong style='color:#FF0000;'>{$install_msg['install_setby_system_param']}</strong>
							</div>
						</td>
					</tr>
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_pmb_param_mysql_pwd']}</td>
						<td>
							<input class='saisie' name='passwd' type='password' value='bibli' />
							<div id='fixepasswd' style='display:none;' >
								<strong style='color:#FF0000;' >{$install_msg['install_setby_system_param']}</strong>
							</div>
						</td>
					</tr>
					<tr>
						<td style='width:200px;' class='etiquette'>{$install_msg['install_pmb_param_mysql_bdd']}</td>
						<td>
							<input class='saisie' type='text' name='dbname' value='bibli' />
							<div id='fixedbname' style='display:none;' >
								<strong style='color:#FF0000;' >{$install_msg['install_setby_system_param']}</strong>
							</div>
						</td>
					</tr>
				</table>
				
				<p>{$install_msg['install_pmb_param_comments']}</p>
				
				<hr />
				
				<h2>{$install_msg['install_pmb_data_loading']}</h2>
				
				<table style='width:100%; border:0;'>
					<tr>
						<td style='width:200px;text-align:right;' >
							<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
							<input name='structure' type='checkbox' value='1' checked readonly style='display:none;' />
						</td>
	        			<td>{$install_msg['install_pmb_data_loading_structure']}</td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
							<input name='minimum' type='checkbox' value='1' checked readonly style='display:none;' />
						</td>
						<td>{$install_msg['install_pmb_data_loading_minimum']}</td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<span id='fixeessential' style='display:none;'>
								<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
							</span>
	            			<input type='checkbox' name='essential' value='1'onClick=\"
								if (this.form.essential.checked) {
									document.getElementById('options_part').style.display = 'inline';
									this.form.data_test_cms.checked = false ;
									this.form.data_test_zen.checked = false ;
								}
							\" />
						</td>
						<td>{$install_msg['install_pmb_data_loading_essential']}</td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<input type='checkbox' name='data_test' value='1' onClick=\"
								if (this.form.data_test.checked) {
									this.form.essential.checked = true ;
									this.form.thesaurus[2].checked = true ;
									this.form.indexint[3].checked = true ;
									this.form.data_test_cms.checked = false ;
									this.form.data_test_zen.checked = false ;
									document.getElementById('fixeessential').style.display = 'inline';
									document.getElementById('fixeagneaux').style.display = 'inline';
									document.getElementById('fixe100cases').style.display = 'inline';
									document.getElementById('options_part').style.display = 'inline';
								} else {
									document.getElementById('fixeessential').style.display = 'none';
									document.getElementById('fixeagneaux').style.display = 'none';
									document.getElementById('fixe100cases').style.display = 'none';
								}
							\" />
						</td>
						<td>{$install_msg['install_pmb_data_loading_test']}</td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<input type='checkbox' name='data_test_cms' value='1' onClick=\"
								if (this.form.data_test_cms.checked) {
									this.form.essential.checked = false ;
									this.form.data_test.checked = false ;
									this.form.data_test_zen.checked = false ;
									this.form.thesaurus[2].checked = true ;
									this.form.indexint[3].checked = true ;
									document.getElementById('options_part').style.display = 'none';
									document.getElementById('fixeessential').style.display = 'none';
								} else {
									document.getElementById('options_part').style.display = 'inline';
								}
							\" />
						</td>
						<td>{$install_msg['install_pmb_data_loading_pageo']}</td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<input type='checkbox' name='data_test_zen' value='1' onClick=\"
								if (this.form.data_test_zen.checked) {
									this.form.essential.checked = false ;
									this.form.data_test.checked = false ;
									this.form.data_test_cms.checked = false ;
									this.form.thesaurus[2].checked = true ;
									this.form.indexint[3].checked = true ;
									document.getElementById('options_part').style.display = 'none';
									document.getElementById('fixeessential').style.display = 'none';
								} else {
									document.getElementById('options_part').style.display = 'inline';
								}
							\" />
						</td>
						<td>{$install_msg['install_pmb_data_loading_zen']}</td>
					</tr>
				</table>
				
				<div id='options_part' >
				
					<hr />
					
					<h2>{$install_msg['install_thesaurus_choice']}</h2>
					
					<table style='width:100%; border:0;'>
						<tr>
							<td style='width:200px;text-align:right;'><input name='thesaurus' type='radio' value='aucun'></td>
							<td>{$install_msg['install_thesaurus_none']}</td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input name='thesaurus' type='radio' value='unesco'></td>
							<td>{$install_msg['install_thesaurus_unesco']}</td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'>
								<span id='fixeagneaux' style='display:none'>
									<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
								</span>
								<input name='thesaurus' type='radio' value='agneaux'>
							</td>
							<td>{$install_msg['install_thesaurus_agneaux']}</td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input name='thesaurus' type='radio' value='environnement'></td>
							<td style='text-align:left;'>{$install_msg['install_thesaurus_environnement']}</td>
						</tr>
					</table>
					
					<hr />
					
					<h2>{$install_msg['install_indexation_choice']}</h2>
					
				    <table style='width:100%; border:0;'>
						<tr>
							<td style='width:200px;text-align:right;'><input name='indexint' type='radio' value='aucun'></td>
							<td>{$install_msg['install_indexation_none']}</td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input name='indexint' type='radio' value='chambery'></td>
							<td>{$install_msg['install_indexation_bm_chambery']}</td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input name='indexint' type='radio' value='dewey'></td>
							<td>{$install_msg['install_indexation_dewey']}</td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'>
								<span id='fixe100cases' style='display:none'>
									<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
								</span>
								<input name='indexint' type='radio' value='marguerite'>
							</td>
							<td>{$install_msg['install_indexation_100']}</td>
						</tr>
					</table>
					
				</div>
				
				<hr />
				
				<p style='text-align:center;'>
					<input type='submit' class='bouton' value='{$install_msg['install_bdd_create']}' />
					<input type='hidden' name='lang' value='{$lang}' />
					<input type='hidden' name='charset' value='{$charset}' />
					<input type='hidden' name='Submit' value='OK' />
				</p>
				
			</form>
		</div>
	</body>
</html>
";

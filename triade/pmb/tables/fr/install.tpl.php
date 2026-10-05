<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install.tpl.php,v 1.2 2023/04/07 14:25:37 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) {
	include('../../includes/forbidden.inc.php'); 
	forbidden();
}

global $install_msg, $install_lang, $charset, $mysql_variables, $alter_session_variables;
global $db_user, $user_password, $mysql_host;

$dbname_pattern = '^[a-z|0-9|_]+$';
$dbuser_pattern = '^[a-z|0-9|_]+$';

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
		.helper {
			text-align : left;
			font-size : 0.8em;
			color: black;
		}
		.helper_error {
			color: red;
		}
        .helper_hide {
			display: none;
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
					<li>{$install_msg['install_preamble_thesaurus_1']}</li>
					<li>{$install_msg['install_preamble_thesaurus_2']}</li>
					<li>{$install_msg['install_preamble_thesaurus_3']}</li>
				</ul>

				<p>{$install_msg['install_preamble_indexation']}</p>
				<ul>
					<li>{$install_msg['install_preamble_indexation_1']}</li>
					<li>{$install_msg['install_preamble_indexation_2']}</li>
					<li>{$install_msg['install_preamble_indexation_3']}</li>
				</ul>
			</blockquote>
			
			<hr />

			<form method='post' action='install_rep.php' onSubmit='return check_form();'>
	
				<h2>{$install_msg['install_system_param']}</h2>
	
				<!--<p>{$install_msg['install_system_param_intro']}</p>-->
                <p>{$install_msg['install_system_param_comments']}</p>

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
							<input class='saisie' id='dbnamedbhost' name='dbnamedbhost' type='text' onKeyUp='check_dbnamedbhost();' />
                            <div class='helper' id='dbnamedbhost_helper' >{$install_msg['install_system_param_mysql_bdd_helper']}</div>
						</td>
					</tr>
				</table>

	    		<hr />

				<h2>{$install_msg['install_pmb_param']}</h2>

				<p>{$install_msg['install_pmb_param_intro']}</p>

				<table style='width:100%; border:0;'>
					<tr> 
						<td style='width:200px;' class='etiquette'>{$install_msg['install_pmb_param_mysql_user']}</td>
						<td>
							<input class='saisie' type='text' name='user' value='bibli' onKeyUp='check_user();' />
							<div id='fixeuser' style='display:none;'>
								<strong style='color:#FF0000;'>{$install_msg['install_setby_system_param']}</strong>
							</div>
                            <div class='helper' id='user_helper' >{$install_msg['install_system_param_mysql_user_helper']}</div>
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
							<input class='saisie' type='text' name='dbname' value='bibli' onKeyUp='check_dbname();' />
							<div id='fixedbname' style='display:none;' >
								<strong style='color:#FF0000;' >{$install_msg['install_setby_system_param']}</strong>
							</div>
                            <div class='helper' id='dbname_helper' >{$install_msg['install_system_param_mysql_bdd_helper']}</div>
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
	        			<td><label>{$install_msg['install_pmb_data_loading_structure']}</label></td>
					</tr>
					<tr> 
						<td style='width:200px;text-align:right;'>
							<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
							<input name='minimum' type='checkbox' value='1' checked readonly style='display:none;' />
						</td>
						<td><label>{$install_msg['install_pmb_data_loading_minimum']}</label></td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<span id='fixeessential' style='display:none;'>
								<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
							</span>
	            			<input type='checkbox' id='essential' name='essential' value='1'onClick=\"
								if (this.form.essential.checked) {
									document.getElementById('options_part').style.display = 'inline';
									this.form.data_test_cms.checked = false ;
									this.form.data_test_zen.checked = false ;
								}
							\" />
						</td>
						<td><label for='essential'>{$install_msg['install_pmb_data_loading_essential']}</label></td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<input type='checkbox' id='data_test' name='data_test' value='1' onClick=\"
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
						<td><label for='data_test'>{$install_msg['install_pmb_data_loading_test']}</label></td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<input type='checkbox' id='data_test_cms' name='data_test_cms' value='1' onClick=\"
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
						<td><label for='data_test_cms'>{$install_msg['install_pmb_data_loading_pageo']}</label></td>
					</tr>
					<tr>
						<td style='width:200px;text-align:right;'>
							<input type='checkbox' id='data_test_zen' name='data_test_zen' value='1' onClick=\"
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
						<td><label for='data_test_zen'>{$install_msg['install_pmb_data_loading_zen']}</label></td>
					</tr>
				</table>

				<div id='options_part' >

					<hr />
	
					<h2>{$install_msg['install_thesaurus_choice']}</h2>
	
					<table style='width:100%; border:0;'>
						<tr>
							<td style='width:200px;text-align:right;'><input name='thesaurus' id='thesaurus_none' type='radio' value='aucun' checked ></td>
							<td><label for='thesaurus_none'>{$install_msg['install_thesaurus_none']}</label></td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input id='thesaurus_unesco' name='thesaurus' type='radio' value='unesco'></td>
							<td><label for='thesaurus_unesco'>{$install_msg['install_thesaurus_unesco']}</label></td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'>
								<span id='fixeagneaux' style='display:none'>
									<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
								</span>
								<input id='thesaurus_agneaux' name='thesaurus' type='radio' value='agneaux'>
							</td>
							<td><label for='thesaurus_agneaux'>{$install_msg['install_thesaurus_agneaux']}</label></td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input id='thesaurus_environnemment' name='thesaurus' type='radio' value='environnement'></td>
							<td style='text-align:left;'><label for='thesaurus_environnement'>{$install_msg['install_thesaurus_environnement']}</td>
						</tr>
					</table>
	
					<hr />
	
					<h2>{$install_msg['install_indexation_choice']}</h2>
	
				    <table style='width:100%; border:0;'>
						<tr>
							<td style='width:200px;text-align:right;'><input id='indexint_none' name='indexint' type='radio' value='aucun' checked ></td>
							<td><label for='indexint_none'>{$install_msg['install_indexation_none']}</label></td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input id='indexint_chambery' name='indexint' type='radio' value='chambery'></td>
							<td><label for='indexint_chambery'>{$install_msg['install_indexation_bm_chambery']}</label></td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'><input id='indexint_dewey' name='indexint' type='radio' value='dewey'></td>
							<td><label for='indexint_dewey'>{$install_msg['install_indexation_dewey']}</label></td>
						</tr>
						<tr>
							<td style='width:200px;text-align:right;'>
								<span id='fixe100cases' style='display:none'>
									<strong style='color:#FF0000;' >{$install_msg['install_mandatory']}</strong>
								</span>
								<input id='indexint_marguerite' name='indexint' type='radio' value='marguerite'>
							</td>
							<td><label for='indexint_marguerite'>{$install_msg['install_indexation_100']}</label></td>
						</tr>
					</table>
	
				</div>

				<hr />

				<p style='text-align:center;'> 
					<input type='submit' class='bouton' value='{$install_msg['install_bdd_create']}' />
					<input type='hidden' name='install_lang' value='{$install_lang}' />
                    <input type='hidden' name='mysql_variables' value='{$mysql_variables}' />
                    <input type='hidden' name='alter_session_variables' value='{$alter_session_variables}' />
					<input type='hidden' name='charset' value='{$charset}' />
					<input type='hidden' name='Submit' value='OK' />
				</p>

                <script>
                    function check_dbnamedbhost() {

                        let form = document.forms[0];
                        let dbnamedbhost = form.dbnamedbhost;
                        let dbnamedbhost_helper = document.getElementById('dbnamedbhost_helper');
                        let dbname_helper =  document.getElementById('dbname_helper');
                        let user_helper = document.getElementById('user_helper');
                        let fixeuser = document.getElementById('fixeuser');
                        let fixepasswd = document.getElementById('fixepasswd');
                        let fixedbname = document.getElementById('fixedbname');

                        if(dbnamedbhost.value!='' & !dbnamedbhost.value.match('$dbname_pattern')) {
                            dbnamedbhost_helper.className='helper helper_error';
                        } else {
                            dbnamedbhost_helper.className='helper';
                        }
                        if (dbnamedbhost.value!='') {
                            form.user.value='';
                            form.passwd.value='';
                            form.dbname.value='';
                            form.user.style.display = 'none';
                            form.passwd.style.display = 'none';
                            form.dbname.style.display = 'none';
                            fixeuser.style.display = 'inline';
                            fixepasswd.style.display = 'inline';
                            fixedbname.style.display = 'inline';
                            dbname_helper.className = 'helper helper_hide';
                            user_helper.className = 'helper helper_hide';
                        } else {
                            form.user.style.display = 'block';
                            form.passwd.style.display = 'block';
                            form.dbname.style.display = 'block';
                            fixeuser.style.display = 'none';
                            fixepasswd.style.display = 'none';
                            fixedbname.style.display = 'none';
                            dbname_helper.className = 'helper';
                            user_helper.className = 'helper';
                        }
                    }

                    function check_user() {

                        let form = document.forms[0];
                        let user = form.user;
                        let user_helper = document.getElementById('user_helper');

                        if(user.value!='' & !user.value.match('$dbuser_pattern')) {
                            user_helper.className='helper helper_error';
                        } else {
                            user_helper.className='helper';
                        }
                    }

                    function check_dbname() {

                        let form = document.forms[0];
                        let dbname = form.dbname;
                        let dbname_helper = document.getElementById('dbname_helper');

                        if(dbname.value!='' & !dbname.value.match('$dbname_pattern')) {
                            dbname_helper.className='helper helper_error';
                        } else {
                            dbname_helper.className='helper';
                        }
                    }

                    function check_form() {

                        let form = document.forms[0];
                        let dbnamedbhost = form.dbnamedbhost;
                        let dbnamedbhost_helper = document.getElementById('dbnamedbhost_helper');
                        let dbname = form.dbname;
                        let dbname_helper = document.getElementById('dbname_helper');
                        let user = form.user;
                        let user_helper = document.getElementById('user_helper');

                        if(dbnamedbhost.value=='' & dbname.value=='') {
                            dbname.focus();
                            return false;
                        }
                        if(dbnamedbhost.value!='' & !dbnamedbhost.value.match('$dbname_pattern')) {
                            dbnamedbhost.focus();
                            dbnamedbhost_helper.className='helper helper_error';
                            return false;
                        }
                        if(dbname.value!='' & !dbname.value.match('$dbname_pattern')) {
                            dbname.focus();
                            dbname_helper.className='helper helper_error';
                            return false;
                        }
                        if(user.value!='' & !user.value.match('$dbuser_pattern')) {
                            user.focus();
                            user_helper.className='helper helper_error';
                            return false;
                        }
                        return true;
                    }
                </script>
			</form>
		</div>
	</body>
</html>";

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: language.tpl.php,v 1.2 2023/04/07 14:25:37 dbellamy Exp $

if(preg_match('/language\.tpl\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php'); 
	forbidden();
}

global $language_page;

$language_page = "
<!DOCTYPE html>
<html>
	<head>
		<title>PMB setup</title>
		<meta charset='utf-8'>
		<style type='text/css'>
			body {
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
		</style>
	</head>
	<body>
		<span class='center'>
			<form method='post' action='install.php'>
				<h1><img src='../images/logo_pmb_rouge.png'>&nbsp;&nbsp;install</h1>

				<h3>Langue: </h3>
				<select id='lang' name='install_lang'>
					<option value='en' >Anglais</option>
					<option value='ca' >Catalan</option>
					<option value='es' >Espagnol</option>
					<option value='fr' selected >Fran&ccedil;ais</option>
					<option value='it' >Italien</option>
					<option value='pt' >Portuguais</option>
				</select>
				<input type='hidden' name='install_step' value='requirements' />
				<button class='bouton' type='submit'>Continuer</button>
			</form>	
		</span>
	</body>
</html>
";

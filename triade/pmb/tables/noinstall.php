<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: install.php,v 1.28 2017-10-27 14:14:16 ngantier Exp $

// plus rien ici : reprise d'un script d'une autre install
if(preg_match('/noinstall\.php/', $_SERVER['REQUEST_URI'])) {
	include('../includes/forbidden.inc.php'); forbidden();
	}

include('../includes/config.inc.php');

header("Content-Type: text/html; charset=$charset");

$sel_lang="
	<html>
	<head>
		<title>PMB setup</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		<style type=\"text/css\">
			body {
				font-family: \"Verdana\", \"Arial\", sans-serif;
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
	<form method='post' action=install.php>
	<h1><img src='../images/logo_pmb_rouge.png'>&nbsp;&nbsp;install</h1>
	<br />
	<h3>Encodage de caractère (charset) :</h3><br />
	<ul> 
		<li>
    		<input type=\"radio\" name=\"charset\" value=\"iso-8859-1\"/>iso-8859-1<br/>
			Cette option ne permet que la cr&eacute;ation d'une base vide, elle est &agrave; utiliser principalement pour la restauration d'une ancienne base en iso-8859-1
   		</li>
		<br/>
     	<li>
     		<input type=\"radio\" name=\"charset\" checked value=\"utf-8\"/>utf-8<br/>
			Cette option est &agrave; privil&eacute;gier pour tous les autres cas de figure
    	</li>
	</ul>
	<h3>Langue:</h3><br />
	<table>
		<tr>
			<td><input type='submit' class='bouton' name='submit' value='Français'></td>
			<td>&nbsp;&nbsp;</td>
			<td><input type='submit' class='bouton' name='submit' value='Italiano'></td>
			<td>&nbsp;&nbsp;</td>
			<td><input type='submit' class='bouton' name='submit' value='English'></td>
			<td>&nbsp;&nbsp;</td>
			<td><input type='submit' class='bouton' name='submit' value='Català'></td>
			<td>&nbsp;&nbsp;</td>
			<td><input type='submit' class='bouton' name='submit' value='Español'></td>
			<td>&nbsp;&nbsp;</td>
			<td><input type='submit' class='bouton' name='submit' value='Portuguese'></td>
		<tr>
	</table>

	</form>	
	</span>
	</body>
	</html>
";

if(!isset($_REQUEST['submit'])){
	$_REQUEST['submit']="";
}
switch ($_REQUEST['submit']){
	case 'Italiano':
		$lang='it';
		break;
	case 'Français':
		$lang='fr';
		break;
	case 'English':
		$lang='en';
		break;
	case 'Català':
		$lang='ca';
		break;
	case 'Español':
		$lang='es';
		break;
	case 'Portuguese':
		$lang='pt';
		break;
	default:
		print $sel_lang;
}

if(!isset($_REQUEST['charset'])){
	$_REQUEST['charset']="";
}
$charset = $_REQUEST['charset'];
if ($lang && $lang != $default_lang){
	include("./$lang/install_inc.php");
	print $header;
	if($charset == "utf-8"){
		print $body;
	}else{
		print $body_iso;
	}
	print $footer;
}
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.php,v 1.40 2023/08/28 14:04:12 tsamson Exp $
/*
Mode d'emploi des transactions client - serveur utilisant les requettes Ajax.
Cette technique permet d'interroger le serveur dynamiquement sans recharger toute la page.

Une transaction s'effectue en envoyant une commande via un script javascript dans le formulaire d'une page html.
Ce script est codé sous forme d'une classe javascript dans le fichier /javascript/http_request.js

Usage et exemple d'envoie d'une requette coté client.
	Dans cet exemple, on teste la validitée de la date avant de commiter le formulaire.
	....
		// Inclusion du script Ajax
		<script type='text/javascript' src='./javascript/http_request.js'></script>
		<script language="JavaScript">
	
		function CheckDataAjax() {
			// Récupération de la valeur de l'objet 'DirectDate'
			var DirectDate = document.Cal.DirectDate.value;
			// Construction de la requette 
			var url= "./ajax.php?module=ajax&categ=misc&fname=verifdate&p1=" + DirectDate;
			// On initialise la classe:
			var test_date = new http_request();
			// Exécution de la requette
			if(test_date.request(url)){
				// Il y a une erreur. Afficher le message retourné
				alert ( test_date.get_text() );			
			}else { 
				// La date est valide, on commit
				document.getElementById('date_directe').value = DirectDate;
				return 1;	
			}
		}
		</script>
			
		<form name="Cal" id="Cal" method='post' action='./test.php'>
		<input type='text' name='DirectDate' size=10 value='10/08/2007'>
		<input type='button' value="Send" onClick="if(CheckDataAjax()) submit();">
		</form>
	....
	
Explication du code:
	Construction de la requette 'url': 
	url= "./ajax.php?module=ajax&categ=misc&fname=verifdate&p1=" + DirectDate;
	Les parametres module,categ permettent de parser la commande au bon endroit dans 
	la structure de codage de PMB
	
	Plusieurs paramètres optionnels de la fonction 'request' permettent de faire des POST
	en mode synchrone ou pas.
	Pour plus de précisions, voir l'entete de la procédure dans: ./javascript/http_request.js
	
Coté serveur, on se rend dans le bon module, grace aux paramètres passés dans 'url'.
Dans l'exemple, module=ajax , categ=misc .
Ainsi le fichier /ajax/ajax_main.inc.php parse la commande à /pmb/ajax/misc/misc.inc.php

Cette métodologie devra être respectée pour chaque requette Ajax, afin de localiser le traitement 
facilement et de réutiliser au mieux le code existant du module

*/

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;  
$base_nodojo = 1;  
$clean_pret_tmp=1;
$base_is_http_request=1;

//avant l'inclusion faudrait peut-être s'occuper de la gestion des droits dans les différentes requetes...
switch($_GET['module']){
	case "cms" :
		$base_auth = "CMS_AUTH";
		break;	
	case "autorites" :
		$base_auth = "AUTORITES_AUTH";
		break;
	case 'catalog':
		$base_auth = "CATALOGAGE_AUTH";
		break;
	case 'circ':
		$base_auth = "CIRCULATION_AUTH";
		break;		
	case 'admin':
		$base_auth = "ADMINISTRATION_AUTH";
		break;
	case 'demandes':
		$base_auth = "DEMANDES_AUTH";
		break;	
	case 'acquisition':
		$base_auth = "ACQUISITION_AUTH";
		break;
	case 'fichier':
		$base_auth = "FICHES_AUTH";
		break;
	case "edit" :
		$base_auth = "EDIT_AUTH";
		break;
	case "modelling" :
		$base_auth = "MODELLING_AUTH";
		break;
	case "frbr" :
		$base_auth = "FRBR_AUTH";
		break;
	case "selectors" :
		$base_auth = "";
		break;
	case 'animations':
		$base_auth = "ANIMATION_AUTH";
		break;
	case 'account':
		$base_auth = "PREF_AUTH|ADMINISTRATION_AUTH";
		break;
	case 'audit':
	    $base_auth = "CATALOGAGE_AUTH";
	    break;
}

require_once ($base_path . "/includes/init.inc.php");

if(!SESSrights) exit;

global $module, $is_iframe, $charset, $object_type, $user_input;

// inclusion des fonctions utiles pour renvoyer la réponse à la requette recu 
require_once ($base_path . "/includes/ajax.inc.php");
/*	
 * Parse la commande Ajax du client vers 
 * $module est passé dans l'url,envoyé par http_send_request, in http_request.js script file
 * les valeurs envoyées dans les requêtes en ajax du client vers le serveur sont encodées
 * exclusivement en utf-8 donc décodage de toutes les variables envoyées si nécessaire
*/

if(!isset($is_iframe)) {
    $is_iframe = 0;
} else {
    $is_iframe = intval($is_iframe);
}

if (strtoupper($charset)!="UTF-8" && !$is_iframe) {
	$t=array_keys($_POST);	
	foreach($t as $v) {
		global ${$v};
		encoding_normalize::utf8_decode(${$v});
	}
	$t=array_keys($_GET);	
	foreach($t as $v) {
		global ${$v};	
		encoding_normalize::utf8_decode(${$v});
	}
	//On décode aussi les POST et les GET en plus de les mettre en global 
	$_POST = encoding_normalize::utf8_decode($_POST);
	$_GET = encoding_normalize::utf8_decode($_GET);
}

$main_file="./$module/ajax_main.inc.php";
switch($module) {
	case 'ajax':
		include($main_file);
		break;
	case 'autorites':		
		include($main_file);
		break;		
	case 'catalog':
		include($main_file);
		break;
	case 'circ':
		include($main_file);
		break;		
	case 'admin':
		include($main_file);
		break;
	case 'demandes':
		include($main_file);
		break;	
	case 'acquisition':
		include($main_file);
		break;
	case 'fichier':
		include($main_file);
		break;
	case 'cms':
		include($main_file);
		break;
	case "edit" :
		include($main_file);
		break;
	case "dsi" :
		include($main_file);
		break;
	case "dashboard" :
		include($main_file);
		break;
	case "selectors":
		global $what,$concept_scheme;
		if($what == 'ontology'){
    		if(!is_array($concept_scheme) && $concept_scheme != ''){
    		    $concept_scheme = explode(",",$concept_scheme);
    		}else{
    		    $concept_scheme = [];
    		}
		}
		require_once($base_path.'/selectors/classes/selector_controller.class.php');
		if(!isset($user_input)) $user_input = '';
		$selector_controller = new selector_controller(stripslashes($user_input));
		$selector_controller->proceed();
		
		break;
	case "modelling" :
		include($main_file);
		break;
	case "frbr" :
		include($main_file);
		break;
	case "animations" :
		include($main_file);
		break;
	case "account" :
		include($main_file);
		break;
	case "semantic" :
	    include($main_file);
	    break;
	case "audit":
	    lists_controller::proceed_ajax($object_type);
	    break;
	default:
		//tbd
	break;	
}		
?>
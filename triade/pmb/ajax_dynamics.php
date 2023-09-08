<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_dynamics.php,v 1.5 2017-07-12 15:15:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;  
$base_nodojo = 1;  
$clean_pret_tmp=1;

require_once ($base_path . "/includes/init.inc.php");

if(!SESSrights) exit;

// inclusion des fonctions utiles pour renvoyer la réponse à la requette recu 
require_once ($base_path . "/includes/ajax.inc.php");

function utf8_decode_pmb(&$var) {
	if(is_array($var)){
		foreach($var as $val) {
			utf8_decode_pmb($val);
		}
	}
	else $var=utf8_decode($var);
}

function array_uft8_decode($tab){
	foreach($tab as $key => $val) {
		if(is_array($val)){
			$tab[$key] = array_uft8_decode($val);
		}else{
			$tab[$key] = utf8_decode($val);
		}
	}
	return $tab;
}

if (strtoupper($charset)!="UTF-8") {
	$t=array_keys($_POST);	
	foreach($t as $v) {
		global ${$v};
		utf8_decode_pmb(${$v});
	}
	$t=array_keys($_GET);	
	foreach($t as $v) {
		global ${$v};	
		utf8_decode_pmb(${$v});
	}
	//On décode aussi les POST et les GET en plus de les mettre en global 
	$_POST = array_uft8_decode($_POST);
	$_GET = array_uft8_decode($_GET);
}

require_once($base_path."/$module/ajax/dynamics/".$typeElt.".class.php");

$elt = new $typeElt($id_elt,$fieldElt);

switch($quoifaire){
			
	case 'edit':
		$elt->make_display();
		break;
	case 'save':
		$elt->update();
		break;
}
		
ajax_http_send_response($elt->display);
?>
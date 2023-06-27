<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq.inc.php,v 1.2 2018-06-08 15:05:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// afin de résoudre un pb d'effacement de la variable $id_empr par empr_included, bug à trouver
if (empty($id_empr)) $id_empr=$_SESSION["id_empr_session"] ;
if(!isset($faq_page)) $faq_page = '';
if(!isset($faq_filters)) $faq_filters = '';

switch($lvl){
	case "faq" :
		require_once($class_path."/faq.class.php");
		$faq = new faq($faq_page,0,$faq_filters);
		print $faq->show();
		break;
	case "question" :
		
		break;
}
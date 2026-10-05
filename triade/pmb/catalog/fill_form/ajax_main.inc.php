<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.3 2019/06/05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quoi, $id, $sub;

require_once($class_path.'/encoding_normalize.class.php');
require_once($class_path.'/form_mapper/form_mapper.class.php');

if($quoi && $id && $sub){
	$mapper = form_mapper::getMapper($quoi,$id);
	if($mapper){
		$mapper->setId($id);
		$mapping = $mapper->getMapping($sub);
		print encoding_normalize::json_encode($mapping);
	}else{
		print encoding_normalize::json_encode(array('mapping'=> 'false'));
	}
		
}

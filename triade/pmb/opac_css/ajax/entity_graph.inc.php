<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity_graph.inc.php,v 1.3 2021/12/21 10:30:24 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $type, $id;

require_once($class_path."/entity_graph.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/authority.class.php");

switch($sub){
	case 'get_graph':
		if($type == 'record'){
			$entity = notice::get_notice($id);
		}else if($type == 'authority'){
			//$entity = new authority($id);
			$entity = authorities_collection::get_authority('authority', $id);
		}
		session_write_close();
		$entity_graph = new entity_graph($entity, $type);
		$entity_graph->get_recursive_graph(1);
		print $entity_graph->get_json_entities_graphed(false);
		break;
	case 'get_next_additionnal':
	    global $node;
	    $node = encoding_normalize::json_decode(stripslashes($node), true);
	    $additionnal_nodes = entity_graph::make_additionnal_nodes($node);
	    print encoding_normalize::json_encode($additionnal_nodes);
	    break;
}

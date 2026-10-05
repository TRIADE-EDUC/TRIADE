<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature.inc.php,v 1.13 2022/06/08 14:45:16 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub, $class_path, $id, $action, $id_parent, $record_child_data, $record_formation_id, $formation_hash;

switch($sub){
	case 'record_child':		
		require_once($class_path."/nomenclature/nomenclature_record_child.class.php");
		$record_child = new nomenclature_record_child($id);
		switch($action){
			case "create" :
				print encoding_normalize::json_encode($record_child->create_record_child($id_parent, $record_child_data));
				break;
			case "get_child" :
				print $record_child->get_child($id_parent, $record_child_data);
				break;
			case "get_possible_values" :
				print encoding_normalize::json_encode($record_child->get_possible_values($id_parent));
				break;
			case "create_children":
				$child_id = $record_child->get_child($id_parent, $record_child_data);
				if($child_id){
					print encoding_normalize::json_encode(array('new_record' => false, 'id'=>$child_id));
				}else{
					$return = $record_child->create_record_child($id_parent, $record_child_data);
					$return['new_record'] = true;
					print encoding_normalize::json_encode($return);
				}
				break;
			case "update_record":
				$record_child->update_record_child($record_child_data);
				break;
			case "delete_record":
				$record_child->delete_record_child();
				break;
			case "get_explnums":
				require_once($class_path."/notice.class.php");
				print encoding_normalize::json_encode(notice::get_notice_explnum_list($record_child->get_id()));
				break;
		}
		break;
	case 'record_formation':
		require_once($class_path."/nomenclature/nomenclature_record_formation.class.php");
		$record_formation = new nomenclature_record_formation($record_formation_id);
		switch($action){
			case 'save_nomenclature':
				$data = $record_formation->save_form(${$formation_hash});
				if($data){
					print encoding_normalize::json_encode($data);
				}else{
					print 0;
				}
				break;
		}
	
		break;
	case "nomenclature_record_formations":
	    require_once($class_path."/nomenclature/nomenclature_record_formations.class.php");
	    $record_formation = new nomenclature_record_formations($id_parent);
	    switch($action){
	        case "reorder_children":
	            $record_formation->reorder_children();
	            break;
	    }
	    
	    break;
}
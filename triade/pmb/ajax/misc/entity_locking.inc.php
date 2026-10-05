<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity_locking.inc.php,v 1.4 2022/02/21 08:12:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $entity_id, $entity_type, $user_id;

require_once($class_path."/entity_locking.class.php");

$entity_id = intval($entity_id);
$entity_type = intval($entity_type);
switch($sub){
	case 'unlock_entity':
	    if(isset($entity_id) && isset($entity_type) && isset($user_id)){
	        $entity_locking = new entity_locking($entity_id, $entity_type);
	        $entity_locking->set_user_id($user_id);
	        $entity_locking->unlock_entity();
	    }
		break;
		
	case 'poll':
	    if(isset($entity_id) && isset($entity_type) && isset($user_id)){
	        $entity_locking = new entity_locking($entity_id, $entity_type);
	        $entity_locking->set_user_id($user_id);
	        $entity_locking->refresh_date();
	    }
	    break;
	case 'check':
	    if(isset($entity_id) && isset($entity_type) && isset($user_id)){
	        $entity_locking = new entity_locking($entity_id, $entity_type);
	        print $entity_locking->is_available();
	    }
	    break;
}

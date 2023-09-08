<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entity_locking.inc.php,v 1.3 2019-05-29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $entity_id, $entity_type, $user_id;

require_once($class_path."/entity_locking.class.php");

switch($sub){
	case 'unlock_entity':
	    if(isset($entity_id) && isset($entity_type) && isset($user_id)){
	        $entity_locking = new entity_locking($entity_id*1, $entity_type*1);
	        $entity_locking->set_user_id($user_id);
	        $entity_locking->unlock_entity();
	    }
		break;
		
	case 'poll':
	    if(isset($entity_id) && isset($entity_type) && isset($user_id)){
	        $entity_locking = new entity_locking($entity_id*1, $entity_type*1);
	        $entity_locking->set_user_id($user_id);
	        $entity_locking->refresh_date();
	    }
	    break;
	case 'check':
	    if(isset($entity_id) && isset($entity_type) && isset($user_id)){
	        $entity_locking = new entity_locking($entity_id*1, $entity_type*1);
	        print $entity_locking->is_available();
	    }
	    break;
}

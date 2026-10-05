<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2021/12/09 09:00:59 dgoron Exp $

global $class_path, $include_path, $sub, $msg, $old_cb, $new_cb;

require_once($class_path.'/expl.class.php');
require_once($include_path.'/connecteurs_out_common.inc.php');
switch($sub){
	case 'update_cb':
		if(isset($old_cb) && isset($new_cb) && (trim($old_cb) != '' && trim($new_cb) !='')){
			switch(exemplaire::update_cb($old_cb, $new_cb)){
				case 0://Le nouveau code est déjà utilisé dans pmb
					print encoding_normalize::json_encode(array('status'=>0, 'message'=>$msg['pointage_message_code_utilise']));
					break;
				case 1://La mise à jour a fonctionnée
					print encoding_normalize::json_encode(array('status'=>1, 'message'=>''));
					break;
				case 2://Impossible d'effectuer la mise à jour
					print encoding_normalize::json_encode(array('status'=>2, 'message'=>$msg['circ_edit_cb_end_up']));
					break;
				case 3: //L'utilisateur n'a pas les droits nécessaires pour effectuer la mise à jour
					print encoding_normalize::json_encode(array('status'=>3, 'message'=>$msg[12]));
					break;
			}		
		}
		break;
}



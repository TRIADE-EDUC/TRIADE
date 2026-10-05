<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chat_tools.class.php,v 1.1.16.1 2025/02/12 12:34:07 dbellamy Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))	die("no access");


/**
 * class chat_tools
 *
 */
class chat_tools {

    public  function init_session() {
        if(!isset($_SESSION['chat'])) $_SESSION['chat'] = array();
        if(!isset($_SESSION['chat']['users_list'])) $_SESSION['chat']['users_list'] = array();
        if(!isset($_SESSION['chat']['chats'])) $_SESSION['chat']['chats'] = array();
    }

    public function get_user_info($user_num) {

	    $query = "SELECT userid, username, nom, prenom, login
            FROM users WHERE userid=" . $user_num;
	    $res = pmb_mysql_query($query);
	    $pmbuser = '';
	    if($pmbuser_row = pmb_mysql_fetch_assoc($res)) {
	        $pmbuser = $pmbuser_row;
	    }
	    return $pmbuser;
	}

	public function set_users_list_state($state) {
	    $_SESSION['chat']['users_list'] = $state;
	}

	public function set_chats_state($state) {
	    $_SESSION['chat']['chats'] = $state;
	}

	public function get_users_list_state() {
	    return $_SESSION['chat']['users_list'];
	}

	public function get_chats_state() {
	    return $_SESSION['chat']['chats'];
	}

	public function get_type_id($params) {
	    if (is_array($params)) {
	        $user_type_id = $params['user_type_id'];
	    } elseif (is_object($params)) {
	        $user_type_id = $params->user_type_id;
	    } else {
	        $user_type_id = $params;
	    }
	    $type_id = explode('_', $user_type_id);
	    if (count($type_id) == 2) {
	        return intval($type_id[0]);
	    } else {
	        return 0;
	    }
	}

	public function get_id($params) {
	    if (is_array($params)) {
	        $user_type_id = $params['user_type_id'];
	    } elseif (is_object($params)) {
	        $user_type_id = $params->user_type_id;
	    } else {
	        $user_type_id = $params;
	    }
	    $type_id = explode('_', $user_type_id);
	    if (count($type_id) == 2) {
	        return intval($type_id[1]);
	    } else {
	        return 0;
	    }
	}


} // end of class

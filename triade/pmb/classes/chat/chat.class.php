<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chat.class.php,v 1.2 2022/02/10 13:11:12 dgoron Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))	die("no access");

global $class_path;
require_once($class_path."/chat/chat_tools.class.php");
	
/**
 * class chat
 * 
 */
define('CHAT_TYPE_PMB_USER', 0);
define('CHAT_TYPE_GROUP', 1);

class chat extends chat_tools{
	    
    /**
     * Identifiant de l'utilisateur
     * @var int
     */
    private $user_id;
    private $user_type;
    private $user_type_id;   
 
    
    public function __construct( $user_id = 0) {
        global $PMBuserid;
        $this->user_id = intval($user_id);
        if (!$this->user_id) {
            $this->user_id = $PMBuserid;
        }
        $this->user_type = CHAT_TYPE_PMB_USER;
        $this->user_type_id = $this->user_type . '_' . $this->user_id;
        $this->init_session();
        
	} // end of member function __construct
	
	public function get_users_list_data($actif = 0, $name_search = '') {
	    
	    $query = "SELECT distinct userid, username, nom, prenom, login 
            FROM users LEFT JOIN sessions ON username=login  
            WHERE param_chat_activate=1 and userid !=" . $this->user_id;	
	    if ($name_search) {
	        $query.= " and nom LIKE '%" . $name_search . "%' ";
	    }
	    $query.= " ORDER BY username ";
	    $pmbusers_res = pmb_mysql_query($query);
	    $pmbusers = array();
	    $count = 0;
	    while($pmbusers_row = pmb_mysql_fetch_assoc($pmbusers_res)) {	        
	        if ($actif && !$pmbusers_row['login']) continue;	        
	        $pmbusers[$count] = $pmbusers_row;	        
	        $pmbusers[$count]['user_type_id'] = CHAT_TYPE_PMB_USER . '_' . $pmbusers_row['userid'];
	        $count++;
	    }
	    return $pmbusers;
	}
		    
	
	public function get_messages_of_group($group_id) {
	    $query = "SELECT * FROM chat_messages 
                        LEFT JOIN chat_users_groups ON chat_user_group_num  = " . $group_id . " AND chat_user_group_user_type = " . $this->user_type . "
                        AND chat_user_group_user_num = " . $this->user_id . "
                        WHERE  	chat_message_to_user_type = " . CHAT_TYPE_GROUP . " and  chat_message_to_user_num  = " . $group_id . "                        
            ORDER BY chat_message_date DESC LIMIT 50";
	    
	    $messages_res = pmb_mysql_query($query);
	    $messages = array();
	    $count = 0;
	    while($row = pmb_mysql_fetch_assoc($messages_res)) {
	        $messages[$count] = $row;
	        if($row['chat_message_from_user_type'] == $this->user_type && $row['chat_message_from_user_num'] == $this->user_id) {
	            $messages[$count]['my_message'] = 1;
	        } else {
	            $messages[$count]['my_message'] = 0;
	        }
	        $messages[$count]['formated_date'] = formatdate($row['chat_message_date'], 1);
	        $messages[$count]['from_user_type_id'] = $row['chat_message_from_user_type'] . '_' . $row['chat_message_from_user_num'];
	        $messages[$count]['to_user_type_id'] = $row['chat_message_to_user_type'] . '_' . $row['chat_message_to_user_num'];
	        
	        $count++;
	    }
	    return $messages;
	}
	
	public function get_messages($user_type_id) {
	    
	    $type_id = $this->get_type_id($user_type_id);
	    $user_num = $this->get_id($user_type_id);
	    
	    $user_num = intval($user_num);
	    if (!$user_num) return;
	    
	    if ($type_id == CHAT_TYPE_GROUP) {
	        return $this->get_messages_of_group($user_num);
	    }
	    
	    $query = "SELECT * FROM chat_messages 
            WHERE (chat_message_from_user_type=" . $type_id . " and chat_message_from_user_num=" . $user_num . " and 
                   chat_message_to_user_type = " . $this->user_type . " and chat_message_to_user_num = " . $this->user_id . ")
                or (chat_message_to_user_type = " . $type_id . " and chat_message_to_user_num=" . $user_num . " and 
                    chat_message_from_user_type=" . $this->user_type . " and  chat_message_from_user_num = " . $this->user_id . ")
            ORDER BY chat_message_date DESC LIMIT 50";
	    $messages_res = pmb_mysql_query($query);
	    $messages = array();
	    $count = 0;
	    while($row = pmb_mysql_fetch_assoc($messages_res)) {
	        $messages[$count] = $row;
	        if($row['chat_message_from_user_type'] == $this->user_type && $row['chat_message_from_user_num'] == $this->user_id) {
	            $messages[$count]['my_message'] = 1;
	        } else {
	            $messages[$count]['my_message'] = 0;
	        }
	        $messages[$count]['formated_date'] = formatdate($row['chat_message_date'], 1);
	        $messages[$count]['from_user_type_id'] = $row['chat_message_from_user_type'] . '_' . $row['chat_message_from_user_num'];
	        $messages[$count]['to_user_type_id'] = $row['chat_message_to_user_type'] . '_' . $row['chat_message_to_user_num'];
	        $count++;
	    }
	    return $messages;
	}

	public function send_message($params) {
	    
	    if (!$this->get_id($params)) return;
	    if (!$params->message) return;
	    
	    $query = "INSERT INTO chat_messages SET
                chat_message_to_user_type=" . $this->get_type_id($params) . " ,
                chat_message_to_user_num=" . $this->get_id($params) . " ,
                chat_message_from_user_type = " . $this->user_type . ",
                chat_message_from_user_num = " . $this->user_id . ",
                chat_message_text = '" . addslashes($params->message) . "'
            ";
	    pmb_mysql_query($query);
	    if ($this->get_type_id($params) == CHAT_TYPE_GROUP) {
	        // Nouveau message pour tous les membres de ce groupe
	        $query = "UPDATE chat_users_groups SET chat_user_group_unread_messages_number = chat_user_group_unread_messages_number + 1 WHERE
                chat_user_group_num = " . $this->get_id($params);
	        pmb_mysql_query($query);
	    }
	    return 1;
	}
	
	public function delete_message($params) {
	    
		$params->id_chat_message = intval($params->id_chat_message);
	    if (!$params->id_chat_message) return;
	    
	    $query = "DELETE FROM chat_messages WHERE id_chat_message=" . $params->id_chat_message;
	    pmb_mysql_query($query);
	    return $query;
	}
	
	public function get_notifications() {	
	    
	    $query = "SELECT chat_message_from_user_type, chat_message_from_user_num, count(*) as nb
            FROM chat_messages
            WHERE chat_message_to_user_type = " . $this->user_type . " and chat_message_to_user_num = " . $this->user_id . " 
            and chat_message_read = 0
            GROUP BY chat_message_from_user_num";	    
	    $res = pmb_mysql_query($query);
	    $users = array();
	    $number = 0;
	    while($row = pmb_mysql_fetch_object($res)) {
	        $users[$row->chat_message_from_user_num] = $row->nb;
	        $number+= $row->nb;
	    }
	    // Nouveau message dans les groupes auqel j'appartient ?
	    $query = "SELECT chat_user_group_num, chat_user_group_unread_messages_number as nb FROM chat_users_groups WHERE
                chat_user_group_unread_messages_number  > 0 and
                chat_user_group_user_type = " . $this->user_type . " and
                chat_user_group_user_num = " . $this->user_id;
	    $res = pmb_mysql_query($query);	    
	    $groups = array();
	    while($row = pmb_mysql_fetch_object($res)) {
	        $groups[$row->chat_user_group_num] = $row->nb;
	        $number+= $row->nb;
	    }
	    
	    return array(
	        'users' => $users,
	        'groups' => $groups,
	        'number'=> $number,
	    );
	}
	
	public function set_messages_read($params) {
	    
	    $type_id = $this->get_type_id($params);
	    $user_num = $this->get_id($params);
	    if ($type_id == CHAT_TYPE_GROUP) {
	        $query = "UPDATE chat_users_groups SET chat_user_group_unread_messages_number=0 WHERE
                chat_user_group_num =  " . $user_num . " and
                chat_user_group_user_type = " . $this->user_type . " and
                chat_user_group_user_num = " . $this->user_id;
	        pmb_mysql_query($query);	    
	        return;
	    }
	    $query = "UPDATE chat_messages SET
            chat_message_read = 1
            WHERE chat_message_from_user_type = " . $type_id . " and chat_message_from_user_num = " . $user_num . " 
              and chat_message_to_user_type  = " . $this->user_type . " and chat_message_to_user_num=" .$this->user_id;
	    pmb_mysql_query($query);	 
	}
	
	public function get_chats_info($chats) {
	    
	    $chats_info = array();
	    if(!is_array($chats)) return $chats_info;
	    foreach ($chats as $chat) {
	        if (!isset($chat->id) || !$chat->id) continue;
	        $chats_info[$chat->id]['userid'] = $chat->id;
	        $chats_info[$chat->id]['messages'] = $this->get_messages($chat->id);
	    }
	    return $chats_info;
	}
	
	public function get_chat($params='') {
	    if ($params->firstAcess) {
	       $params->chats = $this->get_chats_state();
	    }
	    return array(
	        'firstAcess' =>  $params->firstAcess,
	        'users_list' => array( 
	            'users' => $this->get_users_list_data(),
	            'notifications' => $this->get_notifications(),
	            'expandState' => 0,
	        ),
	        'chats' => $this->get_chats_info($params->chats),
	        'users_list_state' => $this->get_users_list_state(),
	        'chats_state' => $this->get_chats_state(),
	        'groups_list' => array( 
	            'users' => $this->get_groups_list_data(),
	         ),
	    );
	}
	
	public function save_state($params='') {	   
	    $this->set_users_list_state($params->user_list);
	    $this->set_chats_state($params->chats);
	}
	
	public function get_state() {
	    return array(
	        'user_list' => $this->get_users_list_state(),
	        'chats' => $this->get_chats_state(),
	    );
	}
	
	public function chat_group_delete($params) {
	    
		$params->id = intval($params->id);
	    if (!$params->id) return;
	    
	    $query = "DELETE FROM chat_users_groups WHERE chat_user_group_num = " . $params->id;
	    pmb_mysql_query($query);	    
	    $query = "DELETE FROM chat_goups WHERE id_chat_group = " . $params->id;
	    pmb_mysql_query($query);
	}
	    
	public function chat_group_save($params) {
	    
		$params->id = intval($params->id);	// id du group    
	    $query_part = " chat_groups SET
            chat_group_name = '" . $params->name . "',
            chat_group_author_user_type = " . $this->user_type . ",
            chat_group_author_user_num = " .$this->user_id;
	    if (!$params->id) {
	        $query = "INSERT INTO " . $query_part;
	    } else {
	        $query = "UPDATE " . $query_part . " WHERE id_chat_group = " . $params->id;	        
	    }
	    pmb_mysql_query($query);
	    if (!$params->id) {
	        $params->id = pmb_mysql_insert_id();
	    } else {
	       $query = "DELETE FROM chat_users_groups WHERE chat_user_group_num = " . $params->id;
	       pmb_mysql_query($query);
	    }	  
	    
	    if (!count($params->users)) {
	        $params->users = array();
	    }
	    $params->users[] = $this->user_type . '_' .$this->user_id;
	    if (count($params->users)) {
	        foreach ($params->users as $user) {
	            $type_id = $this->get_type_id($user);
	            $user_num = $this->get_id($user);
	            
	            $query = "INSERT INTO chat_users_groups SET
                        chat_user_group_num = " . $params->id .",
                        chat_user_group_user_type = " . $type_id .",
                        chat_user_group_user_num = " . $user_num;
	            pmb_mysql_query($query);
	        }
	    }
	}
	
	public function get_groups_list_data() {
	    $query = "SELECT distinct chat_user_group_num, chat_group_name  FROM chat_users_groups 
                        LEFT JOIN chat_groups ON chat_user_group_num = id_chat_group
                        WHERE chat_user_group_user_type = " . $this->user_type . " 
                        AND chat_user_group_user_num = " . $this->user_id . " ORDER BY chat_group_name ";
	    $res = pmb_mysql_query($query);
	    $groups = array();
	    $count = 0;
	    while($row = pmb_mysql_fetch_assoc($res)) {	    
	        $groups[$count] = $row;	        
	        $groups[$count]['user_type_id'] = CHAT_TYPE_GROUP . '_' . $row['chat_user_group_num'];
	        $groups[$count]['nom'] = $row['chat_group_name'];
	        $query_users = "SELECT * FROM chat_users_groups WHERE chat_user_group_num = " . $row['chat_user_group_num'];	    
	        $res_users = pmb_mysql_query($query_users);	
	        $count_users = 0;
	        while($row_users = pmb_mysql_fetch_assoc($res_users)) {
	            $groups[$count]['users'][$count_users] = $row_users;
	            $groups[$count]['users'][$count_users]['user_type_id'] = $row_users['chat_user_group_user_type'] . '_' . $row_users['chat_user_group_user_num'];
	            $count_users++;
	        }
	        
	        $count++;
	    }
	    return $groups;
	}
	
	public function proceed() {
	    global $action;	    
	    global $chat_params;	    
	    
	    switch ($action) {
	        case 'exec':
	            $params = json_decode(stripslashes($chat_params));	            
	            $method = $params->method;
	            return encoding_normalize::json_encode(array(
	                'params' => $params,
	                'data' => $this->$method($params->params)
	            ));
	            break;
	    }
	}
	

} // end of class

<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_setting.class.php,v 1.9.2.2 2024/07/24 06:45:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/mails/mails.class.php");
require_once($include_path."/templates/mails/mail_setting.tpl.php");

class mail_setting {
    
	protected $id;
	
	protected $classname;
	
	protected $sender;
	
	protected $copy_cc;
	
	protected $copy_bcc;
	
	protected $reply;
	
	protected $associated_campaign;
	
	protected $folder_path;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	protected function init_properties() {
		$this->classname = '';
		$this->sender = '';
		$this->copy_cc = '';
		$this->copy_bcc = '';
		$this->reply = '';
		$this->associated_campaign = 0;
	}
	
	protected function fetch_data() {
		$this->init_properties();
		if(!static::table_exists()) {
			return false;
		}
		if($this->id) {
			$query = "select * from mails_settings where id_mail_setting = ".$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_assoc($result);
				$this->classname = $row['mail_setting_classname'];
				$this->sender = $row['mail_setting_sender'];
				$this->copy_cc = $row['mail_setting_copy_cc'];
				$this->copy_bcc = $row['mail_setting_copy_bcc'];
				$this->reply = $row['mail_setting_reply'];
				$this->associated_campaign = $row['mail_setting_associated_campaign'];
			}
		}
	}
	
	protected function get_values_sender_reply() {
		global $msg;
	
		$options =array();
		if(strpos($this->classname, 'opac') !== false) {
			$options = array(
					'reader' => $msg['379'],
					'docs_location' => $msg['location'],
					'parameter' => $msg['opac_view_form_parameters'].' : biblio_name / biblio_email'
			);
		} else {
			$options = array(
					'user' => $msg['86'],
					'docs_location' => $msg['location'],
                    'parameter' => $msg['opac_view_form_parameters'].' : biblio_name / biblio_email'
			);
			if(strpos($this->classname, 'accounting') !== false) {
				$options['accounting_bib_coords'] = $msg['acquisition_coord_lib'];
			}
		}
		return $options;
	}
	
	protected function get_copy_cc_datalist_options() {
	    $values = static::get_values_copy_cc();
	    $options = array_values($values);
	    sort($options);
	    return $options;
	}
	
	public function format_copy_cc() {
	    if(!empty($this->copy_cc)) {
	        $values = static::get_values_copy_cc();
	        if(!empty($values[$this->copy_cc])) {
	            return $values[$this->copy_cc];
	        }
	        return $this->copy_cc;
	    }
	}
	
	protected function get_copy_bcc_selector_options() {
		global $msg;
		
		return array(
            '0' => $msg['39'],
		    '1' => $msg['40']
		);
	}
	
	protected function get_associated_campaign_selector_options() {
	    global $msg;
	    
	    return array(
	        '0' => $msg['39'],
	        '1' => $msg['40']
	    );
	}
	
	public function get_content_form() {
	    global $msg;
	    
		$interface_content_form = new interface_content_form(static::class);
		$element = $interface_content_form->add_element('mail_setting_classname', 'mail_setting_classname');
		$element->add_html_node($this->get_label());
		$element->add_input_node('hidden', $this->classname);
		$interface_content_form->add_element('mail_setting_sender', 'mail_setting_sender')
		->add_select_node($this->get_values_sender_reply(), $this->sender);
		$element = $interface_content_form->add_element('mail_setting_copy_cc', 'mail_setting_copy_cc');
		$element->add_input_node('text', $this->format_copy_cc())
		->set_attributes(array('list' => 'mail_setting_copy_cc_datalist'));
		$element->add_datalist_node($this->get_copy_cc_datalist_options())
		->set_id('mail_setting_copy_cc_datalist')
		->set_name('mail_setting_copy_cc_datalist');
		
		$interface_content_form->add_element('mail_setting_copy_bcc', 'mail_setting_copy_bcc_description')
		->add_select_node($this->get_copy_bcc_selector_options(), $this->copy_bcc);
		$element = $interface_content_form->add_element('mail_setting_reply', 'mail_setting_reply_description');
		$element->add_input_node('boolean', $this->reply)
		->set_click("if(this.checked) { document.getElementById('mail_setting_reply').disabled = false; } else { document.getElementById('mail_setting_reply').disabled = true; }")
// 		->set_class('switch')
		->set_id('mail_setting_reply_active')
		->set_name('mail_setting_reply_active');
		$element = $element->add_select_node($this->get_values_sender_reply(), $this->reply);
		if(empty($this->reply)) {
		    $element->set_attributes(array('disabled' => 'disabled'));
		}
// 		$interface_content_form->add_element('mail_setting_associated_campaign', 'mail_setting_associated_campaign')
// 		->add_select_node($this->get_associated_campaign_selector_options(), $this->associated_campaign);

		//Nota Bene sur l'option de localisation
		$html_node = $msg['location']." = ";
		if(strpos($this->classname, 'mail_opac_') === false) {
		    $html_node .= $msg['deflt2docs_location'];
		} else {
		    $html_node .= $msg['editions_datasource_empr_location'];
		}
		$interface_content_form->add_element('mail_setting_nota_bene', 'mail_setting_nota_bene', 'flat')
		->add_html_node($html_node);
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_form('mail_setting_form');
		$interface_form->set_label($msg['mail_setting_edit']);
		$interface_form->set_object_id(($this->is_in_database() ? $this->id : 0))
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->get_label()." ?")
		->set_content_form($this->get_content_form())
		->set_table_name('mails_settings');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form() {
	    global $mail_setting_classname;
		global $mail_setting_sender, $mail_setting_copy_cc, $mail_setting_copy_bcc, $mail_setting_reply;
// 		global $mail_setting_associated_campaign;
		
		if(empty($this->classname)) {
		    $this->classname = stripslashes($mail_setting_classname);
		}
		$this->sender = stripslashes($mail_setting_sender);
		$copy_cc = stripslashes($mail_setting_copy_cc);
		if(!empty($copy_cc)) {
		    $values = static::get_values_copy_cc();
		    $flipped_values = array_flip($values);
		    if(array_key_exists($copy_cc, $flipped_values)) {
		        $this->copy_cc = $flipped_values[$copy_cc];
		    } else {
		        $this->copy_cc = $copy_cc;
		    }
		} else {
		    $this->copy_cc = '';
		}
		$this->copy_bcc = stripslashes($mail_setting_copy_bcc);
		$this->reply = (isset($mail_setting_reply) ? stripslashes($mail_setting_reply) : '');
// 		$this->associated_campaign = intval($mail_setting_associated_campaign);
	}
	
	public function set_properties_from_folder($folder_path) {
		$instance = $this->classname::get_instance();
		$settings = $instance->get_settings();
		if(!empty($settings['sender'])) {
			$this->sender = $settings['sender'];
		}
		if(!empty($settings['copy_cc'])) {
			$this->copy_cc = $settings['copy_cc'];
		}
		if(!empty($settings['copy_bcc'])) {
			$this->copy_bcc = $settings['copy_bcc'];
		}
		if(!empty($settings['reply'])) {
			$this->reply = $settings['reply'];
		}
		if(!empty($settings['associated_campaign'])) {
			$this->associated_campaign = $settings['associated_campaign'];
		}
	}
	
	public function is_in_database() {
	    if($this->id) {
    		$query = 'SELECT * FROM mails_settings WHERE id_mail_setting = '.$this->id;
    		$result = pmb_mysql_query($query);
    		if(pmb_mysql_num_rows($result)) {
    			return true;
    		}
	    }
		return false;
	}
	
	public function save() {
		if($this->is_in_database()){
			$query = "update mails_settings set ";
			$clause = " where id_mail_setting = ".$this->id;
		}else{
			$query = "insert into mails_settings set 
			";
			$clause= "";
		}
		$query.= "
            mail_setting_classname = '".addslashes($this->classname)."',
			mail_setting_sender = '".addslashes($this->sender)."',
			mail_setting_copy_cc = '".addslashes($this->copy_cc)."',
			mail_setting_copy_bcc = '".addslashes($this->copy_bcc)."',
			mail_setting_reply = '".addslashes($this->reply)."',
			mail_setting_associated_campaign = '".intval($this->associated_campaign)."'
			".$clause;
		
		$result = pmb_mysql_query($query);
		if($result){
			return true;
		}
		return false;
	}
	
	public static function delete($id) {
		$id = intval($id);
		$query = "delete from mails_settings where id_mail_setting = '".$id."'";
		pmb_mysql_query($query);
		return true;
	}
	
	public function get_id() {
	    if(!empty($this->id)) {
	        return $this->id;
	    }
		return $this->classname;
	}
	
	public function get_classname() {
		return $this->classname;
	}
	
	public function get_sender() {
		return $this->sender;
	}
	
	public function get_copy_cc() {
		return $this->copy_cc;
	}
	
	public function get_copy_bcc() {
		return $this->copy_bcc;
	}
	
	public function get_reply() {
		return $this->reply;
	}
	
	public function is_associated_campaign() {
		return $this->associated_campaign;
	}
	
	public function get_folder_path() {
		return $this->folder_path;
	}
	
	public function get_label() {
		$message = mails::get_message($this->classname);
		if($message) {
			return $message;
		}
		return $this->classname;
	}
	
	public function set_classname($classname) {
		$this->classname = $classname;
		return $this;
	}
	
	public function set_sender($sender) {
	    $values = $this->get_values_sender_reply();
	    //Test sur la cohérence de la valeur $sender transmise en argument
	    if(empty($sender) || array_key_exists($sender, $values) !== false) {
	        $this->sender = $sender;
	    }
		return $this;
	}
	
	public function set_copy_cc($copy_cc) {
		$this->copy_cc = $copy_cc;
		return $this;
	}
	
	public function set_copy_bcc($copy_bcc) {
		$this->copy_bcc = $copy_bcc;
		return $this;
	}
	
	public function set_reply($reply) {
	    $values = $this->get_values_sender_reply();
	    //Test sur la cohérence de la valeur $sender transmise en argument
	    if(empty($reply) || array_key_exists($reply, $values) !== false) {
	        $this->reply = $reply;
	    }
		return $this;
	}
	
	public function set_associated_campaign($associated_campaign) {
		$this->associated_campaign = $associated_campaign;
		return $this;
	}
	
	public function set_folder_path($folder_path) {
		$this->folder_path = $folder_path;
		return $this;
	}
	
	public function is_confidential() {
		return false;
	}
	
	public static function get_id_from_classname($classname) {
		if(!static::table_exists()) {
			return 0;
		}
		$query = "SELECT id_mail_setting FROM mails_settings WHERE mail_setting_classname = '".addslashes($classname)."'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'id_mail_setting');
		}
		return 0;
	}
	
	protected static function table_exists() {
		$query = "SHOW TABLES LIKE 'mails_settings'";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
	
	public static function get_values_copy_cc() {
	    $values = [];
	    $query = "SELECT userid, username, prenom, nom, user_email FROM users ORDER BY userid";
	    $result = pmb_mysql_query($query);
	    while ($row = pmb_mysql_fetch_object($result)) {
	        if(empty($row->prenom) && empty($row->nom)) {
	            $values['user_'.$row->userid] = $row->prenom.' '.$row->nom.' <'.$row->user_email.'>';
	        } else {
	            $values['user_'.$row->userid] = $row->username.' <'.$row->user_email.'>';
	        }
	    }
	    return $values;
	}
}
	

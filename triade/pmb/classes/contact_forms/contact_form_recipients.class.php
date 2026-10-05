<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_recipients.class.php,v 1.9 2023/10/10 07:39:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/contact_forms/contact_form_objects.class.php");
require_once($class_path."/contact_forms/contact_form_parameters.class.php");

class contact_form_recipients {
	
	protected $id;
	
	/**
	 * Liste des destinataires par mode
	 */
	protected $recipients;
	
	/**
	 * Mode
	 * @var string
	 */
	protected $mode;
	
	protected $message = "";
	
	/**
	 * Constructeur
	 * @param string $mode
	 */
	public function __construct($id=0, $mode='') {
		$this->id = intval($id);
		$this->set_mode($mode);
		$this->_init_recipients();
		$this->fetch_data();
	}
	
	/**
	 * Initialisation
	 */
	protected function _init_recipients() {
		$this->recipients = array(
				'by_persons' => array(),
				'by_objects' => array(),
				'by_locations' => array()
		);
	}
	
	/**
	 *  Données provenant de la base de données
	 */
	protected function fetch_data() {
		
		$query = 'select contact_form_recipients from contact_forms where id_contact_form='.$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->contact_form_recipients) {
			    $recipients = encoding_normalize::json_decode($row->contact_form_recipients, true);
			    if(is_array($recipients)) {
			        $this->recipients = $recipients;
			    }
			}
		}
	}
	
	protected function _get_recipients_lines($id) {
		global $msg, $charset;
		
		return "
		<tr class='odd'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_recipient_name'], ENT_QUOTES, $charset)."</i></td>
			<td><input type='text' class='saisie-30em' id='recipient_".$this->mode."_".$id."_name' name='recipients[".$this->mode."][".$id."][name]' value='".(isset($this->recipients[$this->mode][$id]['name']) ? htmlentities($this->recipients[$this->mode][$id]['name'], ENT_QUOTES, $charset) : '')."' /></td>	
		</tr>
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_recipient_email'], ENT_QUOTES, $charset)."</i></td>
			<td><input type='text' class='saisie-30em' id='recipient_".$this->mode."_".$id."_email' name='recipients[".$this->mode."][".$id."][email]' value='".(isset($this->recipients[$this->mode][$id]['email']) ? htmlentities($this->recipients[$this->mode][$id]['email'], ENT_QUOTES, $charset) : '')."' /></td>	
		</tr>
		<tr class='odd'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_recipient_copy_email'], ENT_QUOTES, $charset)."</i></td>
			<td><input type='text' class='saisie-30em' id='recipient_".$this->mode."_".$id."_copy_email' name='recipients[".$this->mode."][".$id."][copy_email]' value='".(isset($this->recipients[$this->mode][$id]['copy_email']) ? htmlentities($this->recipients[$this->mode][$id]['copy_email'], ENT_QUOTES, $charset) : '')."' /></td>	
		</tr>
		";
	}
	
	protected function _get_display_lines_email_object_free_entry() {
	    global $msg, $charset;
	    
	    $display = "
			<tr id='recipient_".$this->mode."_0'>
				<td colspan='2'>
					<table>
						<tr><th colspan='2'>".htmlentities($msg['admin_opac_contact_form_recipients_email_object_free_entry'], ENT_QUOTES, $charset)."</th></tr>";
	    $display .= $this->_get_recipients_lines(0);
	    $display .= "</table>
						</td>
					</tr>";
	    return $display;
	}
	
	protected function _get_display_content_list_by_objects() {
		$display = "";
		$contact_form_objects=new contact_form_objects($this->id);
		if(count($contact_form_objects->get_objects())) {
			foreach ($contact_form_objects->get_objects() as $object) {
				$display .= "
					<tr id='recipient_".$this->mode."_".$object->get_id()."'>
						<td colspan='2'>
							<table>
								<tr><th colspan='2'>".$object->get_label()."</th></tr>";
				$display .= $this->_get_recipients_lines($object->get_id());
				$display .= "</table>
						</td>
					</tr>";
			}
		}
		return $display;
	}
	
	protected function _get_display_content_list_by_locations() {
		
		$display = "";
		$query = "select idlocation, location_libelle from docs_location order by location_libelle";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$display .= "
				<tr id='recipient_".$this->mode."_" . $row->idlocation . "'>
					<td colspan='2'>
						<table>
							<tr><th colspan='2'>".$row->location_libelle."</th></tr>";
			$display .= $this->_get_recipients_lines($row->idlocation);
			$display .= "</table>
					</td>
				</tr>";
		}
		return $display;
	}
	
	protected function _get_display_content_list_by_persons() {
		global $msg, $charset;
		
		$display = "<tr><th colspan='2'>".htmlentities($msg['admin_opac_contact_form_recipient_add'], ENT_QUOTES, $charset)." <input type='button' class='bouton' id='contact_form_button_add' name='contact_form_button_add' value='".htmlentities($msg['req_bt_add_line'], ENT_QUOTES, $charset)."' onclick=\"document.location='".static::format_url("&action=add&mode=".$this->mode."&id=".$this->id)."';\" /></th></tr>";
		if(count($this->recipients['by_persons'])) {
			foreach ($this->recipients['by_persons'] as $key=>$person) {
				$display .= "
					<tr id='recipient_".$this->mode."_".$key."'>
						<td colspan='2'>
							<table>
								<tr><th colspan='2'>".(!empty($this->recipients[$this->mode][$key]['name']) ? $this->recipients[$this->mode][$key]['name'] : htmlentities($msg['admin_opac_contact_form_recipient_without_name'], ENT_QUOTES, $charset))."</th></tr>";
				$display .= $this->_get_recipients_lines($key);
				$display .= "<tr><td></td><td><input type='button' class='bouton' id='contact_form_button_delete' name='contact_form_button_delete' value=\"".htmlentities($msg['admin_opac_contact_form_recipient_delete'], ENT_QUOTES, $charset)."\" onclick=\"document.location='".static::format_url("&action=delete&mode=".$this->mode."&id=".$this->id."&recipient_key=".$key)."';\" /></td></tr>
							</table>
						</td>
					</tr>";
			}
		}
		return $display;
	}
	
	/**
	 * Liste des destinataires par mode
	 */
	public function get_display_content_list() {
		$display = "";
		switch ($this->mode) {
			case 'by_persons':
				$display .= $this->_get_display_content_list_by_persons();
				break;
			case 'by_objects':
			    $display .= $this->_get_display_lines_email_object_free_entry();
			    $display .= $this->_get_display_content_list_by_objects();
				break;
			case 'by_locations':
				$display .= $this->_get_display_content_list_by_locations();
				break;
		}
		return $display;
	}
		
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		global $msg, $charset;
		
		$display = "
		<tr>
			<th>".htmlentities($msg['admin_opac_contact_form_parameter_label'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['admin_opac_contact_form_parameter_value'],ENT_QUOTES,$charset)."</th>
		</tr>
		";
		return $display;
	}
	
	/**
	 * Affiche la liste
	 */
	public function get_display_list() {
		global $base_path, $msg, $charset;
		global $current_module;
		
		$display = "<form id='contact_form_recipients' name='contact_form_recipients' class='form-".$current_module."' action='".static::format_url("&action=save&mode=".$this->mode."&id=".$this->id)."' method='post'>
			<div class='form-contenu'>";
		if($this->message != "") {
			$display .= "<span class='erreur'>".htmlentities($this->message, ENT_QUOTES, $charset)."</span>";
		}
		$display .= "
				<div class='row'>
					<label>".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode'], ENT_QUOTES, $charset)."</label>
					".contact_form_parameters::gen_recipients_mode_selector($this->mode, "document.location='".static::format_url("&mode='+this.value+'&id=".$this->id)."'")."
				</div>
				<div class='row'>&nbsp;</div>";
		//Affichage de la liste des destinataires selon le mode
		$display .= "<table id='recipients_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->recipients)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>
			</div>
			<div class='row'>
				<input type='button' class='bouton' value='".$msg['76']."' onclick=\"document.location='".$base_path."/admin.php?categ=contact_forms'\" />
				<input type='button' class='bouton' value='".$msg['admin_opac_contact_form_recipients_save']."' onClick = valid_contact_form() />
			</div>
		</form>";
		
		$reci = encoding_normalize::json_encode($this->recipients[$this->mode]);
		$display .= "<script>
                    var form_is_valid = true;
                    function valid_contact_form(){
                        var recipient =".$reci.";
                        for(var key in recipient){
							if(document.getElementById('recipient_".$this->mode."_' + key + '_email')) {
								var email = document.getElementById('recipient_".$this->mode."_' + key + '_email').value;
								var name = document.getElementById('recipient_".$this->mode."_' + key + '_name').value;
								var copy_email = document.getElementById('recipient_".$this->mode."_' + key + '_copy_email').value;
								if (name == ''){
									alert(\"".sprintf($msg['onto_error_no_minima'], $msg['67'])."\");
									return;
								} else if (email == ''){
									alert(\"".sprintf($msg['onto_error_no_minima'], $msg['58'])."\");
									return;
								}else{
									if(!check_mail(email)){
										alert(email+\" : ".lcfirst($msg['761'])."\");
										return;
									}
									if(copy_email != ''){
										if(!check_mail(copy_email)){
											alert(copy_email+\" : ".lcfirst($msg['761'])."\");
											return;
										}
									}
								}
							}
                        }
                        document.getElementById('contact_form_recipients').submit();
                    }

                    function check_mail(mail){
                        var regex = /(^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\"\.+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)/;
                        var result = mail.match(regex);
                        if(null == result){
                            return false;
                        }
                        return true;
                    }

                    </script>";
		return $display;
	}
	
	public static function is_incomplete($recipient) {
		if((trim($recipient['name']) == '') || (trim($recipient['email']) == '')) {
			return true;
		} else {
			return false;
		}
	}
	
	public function set_properties_from_form() {
		global $recipients;
		
		$this->recipients[$this->mode] = stripslashes_array($recipients[$this->mode]);
	}
	
	public function save() {
		global $msg;
		
		$query = "update contact_forms set
				contact_form_recipients = '".addslashes(encoding_normalize::json_encode($this->recipients))."'
				where id_contact_form='".$this->id."'";
		$result = pmb_mysql_query($query);
		if($result) {
			$this->message = $msg['admin_opac_contact_form_recipients_save_success'];
			return true;
		} else {
			$this->message = $msg['admin_opac_contact_form_recipients_save_error'];
			return false;
		}
	}
	
	public function add() {
	    if(!is_array($this->recipients[$this->mode])) {
	        $this->recipients[$this->mode] = array();
	    }
	    $this->recipients[$this->mode][] = array();
	}
	
	public function delete($id) {
		if(isset($this->recipients[$this->mode][$id])) {
			unset($this->recipients[$this->mode][$id]);
		}
	}
	
	public function unset_recipient($id) {
		if(is_array($this->recipients[$this->mode][$id])) {
			unset($this->recipients[$this->mode][$id]);
		}
	}
	
	public function get_recipients() {
		return $this->recipients;
	}
	
	public function get_mode() {
		return $this->mode;
	}
	
	public function set_mode($mode) {
		if(!$mode) {
			$contact_form_parameters = new contact_form_parameters($this->id);
			$parameters = $contact_form_parameters->get_parameters();
			$mode = $parameters['recipients_mode']; 
		}
		$this->mode = $mode;
	}
	
	public function get_message() {
		return $this->message;
	}
	
	public function set_message($message) {
		$this->message = $message;
	}
	
	protected static function format_url($url='') {
		global $base_path;
		
		return $base_path.'/admin.php?categ=contact_forms&sub=recipients'.$url;
	}
}
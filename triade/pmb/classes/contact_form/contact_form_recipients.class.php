<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_recipients.class.php,v 1.3 2018-12-06 09:45:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/contact_form/contact_form_objects.class.php");
require_once($class_path."/contact_form/contact_form_parameters.class.php");

class contact_form_recipients {
		
	/**
	 * Liste des destinataires par mode
	 */
	protected $recipients;
	
	/**
	 * Mode
	 * @var string
	 */
	protected $mode;
	
	/**
	 * Constructeur
	 * @param string $mode
	 */
	public function __construct($mode) {
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
		
		$query = 'select valeur_param from parametres where type_param="pmb" and sstype_param="contact_form_recipients_lists"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->valeur_param) {
				$this->recipients = unserialize($row->valeur_param);
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
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_recipient_transmitter_email'], ENT_QUOTES, $charset)."</i></td>
			<td><input type='text' class='saisie-30em' id='recipient_".$this->mode."_".$id."_transmitter_email' name='recipients[".$this->mode."][".$id."][transmitter_email]' value='".(isset($this->recipients[$this->mode][$id]['transmitter_email']) ? htmlentities($this->recipients[$this->mode][$id]['transmitter_email'], ENT_QUOTES, $charset) : '')."' /></td>	
		</tr>	
		";
	}
	
	protected function _get_display_content_list_by_objects() {
		$display = "";
		$contact_form_objects=new contact_form_objects();
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
				<tr id='recipient_".$this->mode."_".$id."'>
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
		global $base_path;
		
		$display = "<tr><th colspan='2'>".htmlentities($msg['admin_opac_contact_form_recipient_add'], ENT_QUOTES, $charset)." <input type='button' class='bouton' id='contact_form_button_add' name='contact_form_button_add' value='".htmlentities($msg['req_bt_add_line'], ENT_QUOTES, $charset)."' onclick=\"document.location='".$base_path."/admin.php?categ=contact_form&sub=recipients&action=add&mode=".$this->mode."';\" /></th></tr>";
		if(count($this->recipients['by_persons'])) {
			foreach ($this->recipients['by_persons'] as $id=>$person) {
				$display .= "
					<tr id='recipient_".$this->mode."_".$id."'>
						<td colspan='2'>
							<table>
								<tr><th colspan='2'>".(!empty($this->recipients[$this->mode][$id]['name']) ? $this->recipients[$this->mode][$id]['name'] : htmlentities($msg['admin_opac_contact_form_recipient_without_name'], ENT_QUOTES, $charset))."</th></tr>";
				$display .= $this->_get_recipients_lines($id);
				$display .= "<tr><td></td><td><input type='button' class='bouton' id='contact_form_button_delete' name='contact_form_button_delete' value=\"".htmlentities($msg['admin_opac_contact_form_recipient_delete'], ENT_QUOTES, $charset)."\" onclick=\"document.location='".$base_path."/admin.php?categ=contact_form&sub=recipients&action=delete&mode=".$this->mode."&id=".$id."';\" /></td></tr>
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
		global $msg, $charset;
		
		$display = "";
		switch ($this->mode) {
			case 'by_persons':
				$display .= $this->_get_display_content_list_by_persons();
				break;
			case 'by_objects':
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
		
		$display = "<form class='form-".$current_module."' action='".$base_path."/admin.php?categ=contact_form&sub=recipients&mode=".$this->mode."&action=save' method='post'>
			<div class='form-contenu'>
				<div class='row'>
					<label>".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode'], ENT_QUOTES, $charset)."</label>
					".contact_form_parameters::gen_recipients_mode_selector($this->mode, "document.location='".$base_path."/admin.php?categ=contact_form&sub=recipients&mode='+this.value")."
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
				<input type='submit' class='bouton' value='".$msg['admin_opac_contact_form_recipients_save']."' />
			</div>
		</form>";
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
		
		$query = "update parametres set
				valeur_param = '".addslashes(serialize($this->recipients))."'
				where type_param='pmb' and sstype_param='contact_form_recipients_lists'";
		pmb_mysql_query($query);
	}
	
	public function add() {
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
			$contact_form_parameters = new contact_form_parameters();
			$parameters = $contact_form_parameters->get_parameters();
			$mode = $parameters['recipients_mode']; 
		}
		$this->mode = $mode;
	}
}
<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contact_form_parameters.class.php,v 1.4 2018-12-06 09:45:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class contact_form_parameters {
	
	/**
	 * Liste des paramètres
	 */
	protected $parameters;
	
	protected $updated_in_database = true;
	
	protected $message = "";
	
	public function __construct() {
		$this->_init_parameters();
		$this->fetch_data();
	}
	
	protected function _get_field($type='text', $display=0, $mandatory=0, $readonly=0) {
		return array(
			'type' => $type,
			'display' => $display,
			'mandatory' => $mandatory,
			'readonly' => $readonly
		);
	}
	
	protected function _init_parameters() {
		$this->parameters = array(
				'fields' => array(
					'name' => $this->_get_field('text', 1, 1),
					'firstname' => $this->_get_field('text', 1, 1),
					'group' => $this->_get_field(),
					'email' => $this->_get_field('email', 1, 1, 1),
					'tel' => $this->_get_field()
				),
				'recipients_mode' => 'by_persons',
				'email_content' => $this->_get_email_content_template(),
				'confirm_email' => 1
		);
	}
	
	protected function fetch_data() {
		$query = 'select valeur_param from parametres where type_param="pmb" and sstype_param="contact_form_parameters"';
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			if($row->valeur_param) {
				$parameters = unserialize($row->valeur_param);
				foreach ($this->parameters['fields'] as $name=>$field) {
					if(is_array($parameters['fields'][$name])) {
						if(!$this->parameters['fields'][$name]['readonly']) {
							$this->parameters['fields'][$name]['display'] = $parameters['fields'][$name]['display'];
							$this->parameters['fields'][$name]['mandatory'] = $parameters['fields'][$name]['mandatory'];
						}
					} else {
						$this->updated_in_database = false;
					}
				}
				if($parameters['recipients_mode']) {
					$this->parameters['recipients_mode'] = $parameters['recipients_mode'];
				}
				if($parameters['email_content']) {
					$this->parameters['email_content'] = $parameters['email_content'];
				}
				if($parameters['confirm_email'] == 0) {
					$this->parameters['confirm_email'] = 0;
				}
				if(!$this->updated_in_database) {
					$this->save();
					$this->updated_in_database = true;
				}
			}
		}
	}
	
	protected function _get_display_field($name) {
		global $msg, $charset;
		return 
			htmlentities($msg['admin_opac_contact_form_parameter_display_field'], ENT_QUOTES, $charset)."
			<input type='checkbox' id='parameter_display_field_".$name."' name='parameter_fields[".$name."][display]' value='1' ".($this->parameters['fields'][$name]['display'] ? "checked='checked'" : "")." ".($this->parameters['fields'][$name]['readonly'] ? "disabled='disabled'" : "")." />
			".($this->parameters['fields'][$name]['readonly'] ? "<input type='hidden' id='parameter_display_field_".$name."' name='parameter_fields[".$name."][display]' value='1' />" : "")."
			".htmlentities($msg['admin_opac_contact_form_parameter_mandatory_field'], ENT_QUOTES, $charset)."
			<input type='checkbox' id='parameter_mandatory_field_".$name."' name='parameter_fields[".$name."][mandatory]' value='1' ".($this->parameters['fields'][$name]['mandatory'] ? "checked='checked'" : "")." ".($this->parameters['fields'][$name]['readonly'] ? "disabled='disabled'" : "")." />
			".($this->parameters['fields'][$name]['readonly'] ? "<input type='hidden' id='parameter_mandatory_field_".$name."' name='parameter_fields[".$name."][mandatory]' value='1' />" : "")."
		";
	}
	
	public static function gen_recipients_mode_selector($selected='', $onchange='') {
		global $base_path, $msg, $charset;
		return "
			<select name='parameter_recipients_mode' onchange=\"".$onchange."\">
				<option value='by_persons' ".($selected == 'by_persons' ? "selected='selected'" : "").">".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode_by_persons'], ENT_QUOTES, $charset)."</option>
				<option value='by_objects' ".($selected == 'by_objects' ? "selected='selected'" : "").">".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode_by_objects'], ENT_QUOTES, $charset)."</option>
				<option value='by_locations' ".($selected == 'by_locations' ? "selected='selected'" : "").">".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode_by_locations'], ENT_QUOTES, $charset)."</option>
			</select>
		";
	}
	
	protected function _get_display_toggle($name) {
		global $msg, $charset;
		return "
		<input type='checkbox' id='parameter_".$name."' name='parameter_".$name."' class='switch' value='1' ".($this->parameters[$name] ? "checked='checked'" : "")." />
		<label for='parameter_".$name."'>".htmlentities($msg['admin_opac_contact_form_parameter_confirm_email_active'], ENT_QUOTES, $charset)."</label>";
	}
	
	protected function _get_display_permalink() {
		global $opac_url_base;
		
		return "<a href = '".$opac_url_base."index.php?lvl=contact_form"."' target='_blank'>".$opac_url_base."index.php?lvl=contact_form</a>";
	}
	
	/**
	 * Liste des paramètres
	 */
	public function get_display_content_list() {
		global $msg, $charset;
		
		$display = "
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_name'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_field('name')."</td>	
		</tr>
		<tr class='odd'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_firstname'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_field('firstname')."</td>	
		</tr>
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_group'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_field('group')."</td>	
		</tr>
		<tr class='odd'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_email'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_field('email')."</td>	
		</tr>
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_tel'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_field('tel')."</td>	
		</tr>
		<tr class='odd'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_recipients_mode'], ENT_QUOTES, $charset)."</i></td>
			<td>
				".self::gen_recipients_mode_selector($this->parameters['recipients_mode'])."
			</td>	
		</tr>
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_email_content'], ENT_QUOTES, $charset)."</i></td>
			<td><textarea id='parameter_email_content' name='parameter_email_content' class='saisie-50em' rows='15' cols='55'>".$this->parameters['email_content']."</textarea>
			</td>
		</tr>
		<tr class='odd'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_confirm_email'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_toggle('confirm_email')."</td>
		</tr>
		<tr class='even'>
			<td><i>".htmlentities($msg['admin_opac_contact_form_parameter_permalink'], ENT_QUOTES, $charset)."</i></td>
			<td>".$this->_get_display_permalink()."</td>
		</tr>
		";
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
	 * Affiche la recherche + la liste des décomptes
	 */
	public function get_display_list() {
		global $msg, $charset;
		global $current_module;
		
		$display = "
			<form class='form-".$current_module."' action='./admin.php?categ=contact_form&sub=parameters&action=save' method='post'>
				<div class='form-contenu'>";
		if($this->message != "") {
			$display .= "<span class='erreur'>".htmlentities($this->message, ENT_QUOTES, $charset)."</span>";
		}
		//Affichage de la liste des paramètres
		$display .= "<table id='parameters_list'>";
		$display .= $this->get_display_header_list();
		$display .= $this->get_display_content_list();
		$display .= "</table>
				</div>
				<div class='row'>
					<input type='submit' class='bouton' value='".$msg['admin_opac_contact_form_parameters_save']."' />
				</div>
			</form>";
		return $display;
	}
	
	public function set_properties_from_form() {
		global $parameter_fields;
		global $parameter_recipients_mode;
		global $parameter_email_content;
		global $parameter_confirm_email;
		
		if(is_array($parameter_fields)) {
			foreach ($this->parameters['fields'] as $name=>$field) {
				if(isset($parameter_fields[$name]['display'])) {
					$this->parameters['fields'][$name]['display'] = $parameter_fields[$name]['display'];
				} else {
					$this->parameters['fields'][$name]['display'] = 0;
				}
				if(isset($parameter_fields[$name]['mandatory'])) {
					$this->parameters['fields'][$name]['mandatory'] = $parameter_fields[$name]['mandatory'];
				} else {
					$this->parameters['fields'][$name]['mandatory'] = 0;
				}
			}
		}
		$this->parameters['recipients_mode'] = $parameter_recipients_mode;
		if(trim($parameter_email_content)) {
			$this->parameters['email_content'] = stripslashes($parameter_email_content);
		} else {
			$this->parameters['email_content'] = $this->_get_email_content_template();
		}
		$this->parameters['confirm_email'] = ($parameter_confirm_email ? 1 : 0);
	}
	
	public function save() {
		global $msg;
		
		$query = "update parametres set
				valeur_param = '".addslashes(serialize($this->parameters))."'
				where type_param='pmb' and sstype_param='contact_form_parameters'";
		$result = pmb_mysql_query($query);
		if($result) {
			$this->message = $msg['admin_opac_contact_form_parameters_save_success'];
			return true;
		} else {
			$this->message = $msg['admin_opac_contact_form_parameters_save_error'];
			return false;
		}
	}
	
	protected function _get_email_content_template() {
		global $include_path;
		
		$email_content = '';
		if (file_exists($include_path.'/templates/contact_form/email_content_subst.html')) {
			$template_path =  $include_path.'/templates/contact_form/email_content_subst.html';
		} else {
			$template_path =  $include_path.'/templates/contact_form/email_content.html';
		}
		if (file_exists($template_path)) {
			$email_content = file_get_contents($template_path);
		}
		return $email_content;
	}
	
	public function get_parameters() {
		return $this->parameters;
	}
	
	public function get_message() {
		return $this->message;
	}
	
	public function set_message($message) {
		$this->message = $message;
	}
}
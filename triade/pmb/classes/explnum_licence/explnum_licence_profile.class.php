<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_profile.class.php,v 1.11 2023/07/13 11:49:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/interface/admin/interface_admin_docnum_licence_form.class.php');
require_once($include_path.'/templates/explnum_licence/explnum_licence_profile.tpl.php');
require_once($class_path.'/translation.class.php');

/**
 * Classe de gestion des profils de régimes de licence
 * @author apetithomme, vtouchard
 *
 */
class explnum_licence_profile {
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Libellé du profil de régime de licence
	 * @var string
	 */
	protected $label;
	
	/**
	 * URI
	 * @var string
	 */
	protected $uri;
	
	/**
	 * Droits associés
	 * @var explnum_licence_right
	 */
	protected $rights;
	
	/**
	 * Identifiant du régime de licence
	 * @var int $explnum_licence_num
	 */
	protected $explnum_licence_num;
	
	/**
	 * URL du logo
	 * @var string
	 */
	protected $logo_url;
	
	/**
	 * Phrase d'explication
	 * @var string
	 */
	protected $explanation;
	
	/**
	 * Droits de citation
	 * @var string $quotation_rights
	 */
	protected $quotation_rights;
	
	public function __construct($id = 0) {
		$this->id = intval($id);
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('explnum_licence_profile_label', 'docnum_statut_libelle')
		->add_input_node('text', $this->get_label())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_profile_label'));
		$interface_content_form->add_element('explnum_licence_profile_uri', 'explnum_licence_uri')
		->add_input_node('text', $this->get_uri())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_profile_uri'));
		$interface_content_form->add_element('explnum_licence_profile_logo_url', 'explnum_licence_logo_url')
		->add_input_node('text', $this->get_logo_url())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_profile_logo_url'));
		$interface_content_form->add_element('explnum_licence_profile_explanation', 'explnum_licence_explanation')
		->add_textarea_node($this->get_explanation())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_profile_explanation'));
		
		$interface_content_form->add_element('explnum_licence_profile_quotation_rights_vars', 'explnum_licence_profile_quotation_rights', 'flat')
		->add_html_node($this->get_quotation_variables_selector());

		$interface_content_form->add_element('explnum_licence_profile_quotation_rights')
		->add_textarea_node($this->get_quotation_rights())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_profile_quotation_rights'));
		
		$interface_content_form->add_element('explnum_licence_profile_linked_rights', 'explnum_licence_profile_linked_rights')
		->add_html_node($this->generate_rights_checkboxes());
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_docnum_licence_form('explnumlicenceprofileform');
		if(!$this->id){
			$interface_form->set_label($msg['explnum_licence_profile_new']);
		}else{
			$interface_form->set_label($msg['explnum_licence_profile_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_what('profiles')
		->set_num_explnum_licence($this->explnum_licence_num)
		->set_confirm_delete_msg($msg['explnum_licence_profile_confirm_delete'])
		->set_content_form($this->get_content_form())
		->set_table_name('explnum_licence_profiles')
		->set_field_focus('explnum_licence_profile_label');
		return $interface_form->get_display();
	}
	
	protected function get_quotation_variables() {
		global $msg;
				
		$quotation_variables = array(
		    'explnum_nom' => $msg['explnum_nom'],
		    'tit1' => $msg['237'],
		    'permalink' => $msg['cms_editorial_form_permalink']
		);
		$p_perso = new parametres_perso("explnum");		
		if (!$p_perso->no_special_fields) {
    		foreach ($p_perso->t_fields as $field) {
    		    $quotation_variables[$field["NAME"]] = $field["TITRE"] . " : " .$field["NAME"];
    		}
		}
		return $quotation_variables;
	}
	
	protected function get_quotation_variables_selector() {
		global $admin_explnum_licence_quotation_variable_selector, $admin_explnum_licence_quotation_variable_selector_option;
		// ISBD notice, permalink, auteur
		$variables = $this->get_quotation_variables();
		
		$selector = $admin_explnum_licence_quotation_variable_selector;
		$options = '';
		foreach ($variables as $value => $label) {
			$option = $admin_explnum_licence_quotation_variable_selector_option;
			$option = str_replace('!!option_value!!', $value, $option);
			$option = str_replace('!!option_label!!', $label, $option);
			$options.= $option;
		}
		$selector = str_replace('!!variable_selector_options!!', $options, $selector);
		
		return $selector;
	}
	
	public function set_properties_from_form(){
		global $explnum_licence_profile_label, $explnum_licence_profile_uri, $explnum_licence_profile_logo_url;
		global $explnum_licence_profile_explanation, $explnum_licence_profile_quotation_rights, $explnum_licence_profile_rights;
		
		$this->label = stripslashes($explnum_licence_profile_label);
		$this->uri = stripslashes($explnum_licence_profile_uri);
		$this->logo_url = stripslashes($explnum_licence_profile_logo_url);
		$this->explanation = stripslashes($explnum_licence_profile_explanation);
		$this->quotation_rights = stripslashes($explnum_licence_profile_quotation_rights);
		$this->rights = array();
		if (is_array($explnum_licence_profile_rights)) {
			foreach ($explnum_licence_profile_rights as $right){
				$this->rights[$right] = new explnum_licence_right($right);
			}
		}
	}
	
	public function save(){
		$query = '';
		$clause = '';
		if($this->id){
			$query.= 'update ';
			$clause = ' where id_explnum_licence_profile = '.$this->id;
		}else{
			$query.= 'insert into '; 
		}
		
		$query.= 'explnum_licence_profiles set
				explnum_licence_profile_explnum_licence_num = "'.addslashes($this->explnum_licence_num).'",
				explnum_licence_profile_label = "'.addslashes($this->label).'",
				explnum_licence_profile_uri = "'.addslashes($this->uri).'",
				explnum_licence_profile_logo_url = "'.addslashes($this->logo_url).'",
				explnum_licence_profile_explanation = "'.addslashes($this->explanation).'",
				explnum_licence_profile_quotation_rights = "'.addslashes($this->quotation_rights).'"';
		$query.= $clause;
		
		pmb_mysql_query($query);
		
		if(!$this->id){
			$this->id = pmb_mysql_insert_id();
		}
		pmb_mysql_query('delete from explnum_licence_profile_rights where explnum_licence_profile_num = '.$this->id);
		$rights_ids = array_keys($this->rights);
		
		$query = '';
		for($i=0 ; $i<count($rights_ids) ; $i++){
			if($query){
				$query.= ',';
			}
			$query.= ' ('.$this->id.', '.$rights_ids[$i].') ';
		}
		if($query){
			$query = 'insert into explnum_licence_profile_rights (explnum_licence_profile_num, explnum_licence_right_num) values '.$query;
			pmb_mysql_query($query);
		}

		$translation = new translation($this->id, 'explnum_licence_profiles');
		$translation->update_small_text('explnum_licence_profile_label');
		$translation->update_small_text('explnum_licence_profile_uri');
		$translation->update_small_text('explnum_licence_profile_logo_url');
		$translation->update_text('explnum_licence_profile_explanation');
		$translation->update_text('explnum_licence_profile_quotation_rights');
	}
	
	public function fetch_data() {
		if (!$this->id) {
			return false;
		}
		$query = 'select 
				explnum_licence_profiles.explnum_licence_profile_explnum_licence_num, 
				explnum_licence_profiles.explnum_licence_profile_label, 
				explnum_licence_profiles.explnum_licence_profile_uri, 
				explnum_licence_profiles.explnum_licence_profile_logo_url, 
				explnum_licence_profiles.explnum_licence_profile_explanation, 
				explnum_licence_profiles.explnum_licence_profile_quotation_rights 
				from explnum_licence_profiles
				where explnum_licence_profiles.id_explnum_licence_profile = '.$this->id;
		
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if (count($row)) {			
			$this->explnum_licence_num = $row['explnum_licence_profile_explnum_licence_num'];
			$this->label = $row['explnum_licence_profile_label'];
			$this->uri = $row['explnum_licence_profile_uri'];
			$this->logo_url = $row['explnum_licence_profile_logo_url'];
			$this->explanation = $row['explnum_licence_profile_explanation'];
			$this->quotation_rights = $row['explnum_licence_profile_quotation_rights'];
		}
	}
	
	public function delete($force = false) {
		if (!$this->id) {
			return false;
		}
		
		if($force || !$this->is_used()) {
			pmb_mysql_query('delete from explnum_licence_profile_explnums where explnum_licence_profile_explnums_profile_num = '.$this->id);
			pmb_mysql_query('delete from explnum_licence_profile_rights where explnum_licence_profile_num = '.$this->id);
			pmb_mysql_query('delete from explnum_licence_profiles where id_explnum_licence_profile = '.$this->id);

			translation::delete($this->id, 'explnum_licence_profiles');
			return true;
		}
		return false;
	}
	
	public function set_explnum_licence_num($explnum_licence_num) {
		$this->explnum_licence_num = intval($explnum_licence_num);
		return $this;
	}
	
	public function is_used() {
		$result = pmb_mysql_query('select explnum_licence_profile_explnums_explnum_num from explnum_licence_profile_explnums where explnum_licence_profile_explnums_profile_num = '.$this->id.' limit 1');
		if (pmb_mysql_num_rows($result)) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return explnum_licence_right
	 */
	public function get_rights(){
		if(!isset($this->rights)){
			$this->rights = array();
			$query = 'select explnum_licence_right_num
					from explnum_licence_profile_rights 
					where explnum_licence_profile_num = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_assoc($result)) {
					$this->rights[$row['explnum_licence_right_num']] = new explnum_licence_right($row['explnum_licence_right_num']);
				}
			}
		}
		return $this->rights;
	}
	
	protected function generate_rights_checkboxes(){
		global $admin_explnum_checkbox_template, $msg;
		
		$explnum_licence = new explnum_licence($this->explnum_licence_num);
		$this->get_rights();
		$used_rights = array_keys($this->rights);
		$rights_available = $explnum_licence->get_rights();
		$html = '';
		if (!count($rights_available)) {
			$html.= $msg['explnum_licence_no_right_defined'];
		}
		foreach($rights_available as $right){
			$checkbox_template = str_replace('!!admin_explnum_right_label!!', $right->get_label(), $admin_explnum_checkbox_template);
			$checkbox_template = str_replace('!!admin_explnum_right_id!!', $right->get_id(), $checkbox_template);
			$checkbox_template = str_replace('!!admin_explnum_right_checked!!', (in_array($right->get_id(), $used_rights) ? ' checked="checked "' : ''), $checkbox_template);
			$html.= $checkbox_template;
		}
		return $html;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		if (!isset($this->label)) {
			$this->fetch_data();
		}
		return $this->label;
	}
	
	public function get_logo_url() {
		if (!isset($this->logo_url)) {
			$this->fetch_data();
		}
		return $this->logo_url;
	}
	
	public function get_uri() {
		if (!isset($this->uri)) {
			$this->fetch_data();
		}
		return $this->uri;
	}
	
	public function get_explnum_licence_num() {
		if (!isset($this->explnum_licence_num)) {
			$this->fetch_data();
		}
		return $this->explnum_licence_num;
	}
	
	public function get_explanation(){
		return $this->explanation;
	}
	
	public function get_quotation_rights(){
		if (!isset($this->quotation_rights)) {
			$this->fetch_data();
		}
		return $this->quotation_rights;
	}
	
	public function get_quotation_rights_for_explnum($explnum_id) {
		global $opac_url_base;
		
		$quotation = $this->get_quotation_rights();
		
		$query = 'SELECT explnum_nom, explnum_notice, explnum_bulletin FROM explnum WHERE explnum_id = "'.$explnum_id.'"';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_assoc($result);
			$quotation = str_replace('{{ explnum_nom }}', $row['explnum_nom'], $quotation);
			if ($row['explnum_notice']) {
				$quotation = str_replace('{{ permalink }}', $opac_url_base.'index.php?lvl=notice_display&id='.$row['explnum_notice'], $quotation);
				$query = 'SELECT tit1 FROM notices WHERE notice_id = '.$row['explnum_notice'];
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_assoc($result);
					$quotation = str_replace('{{ tit1 }}', $row['tit1'], $quotation);
				}
			} else if ($row['explnum_bulletin']) {
				$quotation = str_replace('{{ permalink }}', $opac_url_base.'/index.php?lvl=bulletin_display&id='.$row['explnum_bulletin'], $quotation);
				$query = 'SELECT bulletin_titre FROM bulletins WHERE bulletin_id = '.$row['explnum_bulletin'];
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_assoc($result);
					$quotation = str_replace('{{ tit1 }}', $row['bulletin_titre'], $quotation);
				}
			}
			$p_perso = new parametres_perso("explnum");
			if (!$p_perso->no_special_fields) {
			    $p_perso->get_values($explnum_id);
			    $values = $p_perso->values;
			    foreach ($values as $field_id => $vals ) {
			        $parametres_perso = array();
			        foreach ($vals as $value) {
			            $parametres_perso[] = $p_perso->get_formatted_output(array($value), $field_id);			        
			        }			        
			        $quotation = str_replace('{{ ' . $p_perso->t_fields[$field_id]["NAME"] . ' }}', implode(' ', $parametres_perso), $quotation);			        
			    }
			}
		}
		return $quotation;
	}
}
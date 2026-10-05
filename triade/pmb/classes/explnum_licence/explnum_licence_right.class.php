<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_right.class.php,v 1.8 2023/07/13 11:49:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/interface/admin/interface_admin_docnum_licence_form.class.php');
require_once($include_path.'/templates/explnum_licence/explnum_licence_right.tpl.php');

/**
 * Classe de gestion des profils de régimes de licence
 * @author apetithomme, vtouchard
 *
 */
class explnum_licence_right {
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
	 * Type (autorisation / interdiction) 
	 * @var integer
	 */
	protected $type;
	
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
	
	public function __construct($id = 0) {
		$this->id = intval($id);
	}
	
	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$element = $interface_content_form->add_element('explnum_licence_right_type', 'explnum_licence_right_type');
		$element->add_input_node('radio', '1')->set_label_code('explnum_licence_right_quotation_right_authorisation')
		->set_checked((!$this->id || !empty($this->get_type()) ? true : false));
		$element->add_input_node('radio', '0')->set_label_code('explnum_licence_right_quotation_right_prohibition')
		->set_checked(($this->id && empty($this->get_type()) ? true : false));
		$interface_content_form->add_element('explnum_licence_right_label', 'docnum_statut_libelle')
		->add_input_node('text', $this->get_label())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_right_label'));
		
		$interface_content_form->add_element('explnum_licence_right_logo_url', 'explnum_licence_logo_url')
		->add_input_node('text', $this->get_logo_url())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_right_logo_url'));
		
		$interface_content_form->add_element('explnum_licence_right_explanation', 'explnum_licence_explanation')
		->add_textarea_node($this->get_explanation())
		->set_attributes(array('data-translation-fieldname'=> 'explnum_licence_right_explanation'));
		
		return $interface_content_form->get_display();
	}
	
	public function get_form() {
		global $msg;
		
		$interface_form = new interface_admin_docnum_licence_form('explnumlicencerightform');
		if(!$this->id){
			$interface_form->set_label($msg['explnum_licence_right_new']);
		}else{
			$interface_form->set_label($msg['explnum_licence_right_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_what('rights')
		->set_num_explnum_licence($this->explnum_licence_num)
		->set_confirm_delete_msg($msg['explnum_licence_right_confirm_delete'])
		->set_content_form($this->get_content_form())
		->set_table_name('explnum_licence_rights')
		->set_field_focus('explnum_licence_right_label');
		return $interface_form->get_display();
	}
	
	public function set_properties_from_form(){
		global $explnum_licence_right_label, $explnum_licence_right_logo_url;
		global $explnum_licence_right_explanation, $explnum_licence_right_type;
		
		$this->label = stripslashes($explnum_licence_right_label);
		$this->logo_url = stripslashes($explnum_licence_right_logo_url);
		$this->explanation = stripslashes($explnum_licence_right_explanation);
		$this->type = stripslashes($explnum_licence_right_type);
	}
	
	public function save(){
		$query = '';
		$clause = '';
		if($this->id){
			$query.= 'update ';
			$clause = ' where id_explnum_licence_right = '.$this->id;
		}else{
			$query.= 'insert into '; 
		}
		
		$query.= 'explnum_licence_rights set
				explnum_licence_right_explnum_licence_num = "'.addslashes($this->explnum_licence_num).'",
				explnum_licence_right_label = "'.addslashes($this->label).'",
				explnum_licence_right_logo_url = "'.addslashes($this->logo_url).'",
				explnum_licence_right_explanation = "'.addslashes($this->explanation).'",
				explnum_licence_right_type = "'.addslashes($this->type).'"';
		$query.= $clause;
		
		pmb_mysql_query($query);
		if(!$this->id) {
			$this->id = pmb_mysql_insert_id();
		}
		$translation = new translation($this->id, 'explnum_licence_rights');
		$translation->update_small_text('explnum_licence_right_label');
		$translation->update_small_text('explnum_licence_right_logo_url');
		$translation->update_text('explnum_licence_right_explanation');
	}
	
	public function fetch_data() {
		if (!$this->id) {
			return false;
		}
		$query = 'select explnum_licence_right_explnum_licence_num, explnum_licence_right_label, explnum_licence_right_logo_url, explnum_licence_right_explanation, explnum_licence_right_type 
				from explnum_licence_rights where id_explnum_licence_right = '.$this->id;
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_assoc($result);
		if (count($row)) {
			$this->explnum_licence_num = $row['explnum_licence_right_explnum_licence_num'];
			$this->label = $row['explnum_licence_right_label'];
			$this->logo_url = $row['explnum_licence_right_logo_url'];
			$this->explanation = $row['explnum_licence_right_explanation'];
			$this->type = $row['explnum_licence_right_type'];
		}
	}
	
	public function delete() {
		if (!$this->id) {
			return false;
		}
		pmb_mysql_query('delete from explnum_licence_profile_rights where explnum_licence_right_num = '.$this->id);
		
		pmb_mysql_query('delete from explnum_licence_rights where id_explnum_licence_right = '.$this->id);
		translation::delete($this->id, 'explnum_licence_rights');
	}
	
	public function set_explnum_licence_num($explnum_licence_num) {
		$this->explnum_licence_num = intval($explnum_licence_num);
		return $this;
	}
	
	public function get_label(){
		if(!isset($this->label)){
			$this->fetch_data();
		}
		return $this->label;
	}
	
	public function get_id(){
		return $this->id;
	}
	
	public function get_logo_url() {
		if (!isset($this->logo_url)) {
			$this->fetch_data();
		}
		return $this->logo_url;
	}
	
	public function get_explanation() {
		if (!isset($this->explanation)) {
			$this->fetch_data();
		}
		return $this->explanation;
	}
	
	public function get_type() {
		if (!isset($this->type)) {
			$this->fetch_data();
		}
		return $this->type;
	}
	
	public function get_explnum_licence_num() {
		if (!isset($this->explnum_licence_num)) {
			$this->fetch_data();
		}
		return $this->explnum_licence_num;
	}
}
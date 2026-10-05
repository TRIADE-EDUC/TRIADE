<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_pricing_system.class.php,v 1.6 2023/07/05 15:32:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/interface/admin/interface_admin_acquisition_form.class.php');
require_once($include_path."/templates/rent/rent_pricing_system.tpl.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/rent/rent_pricing_system_grid.class.php");

class rent_pricing_system {
	
	/**
	 * Identifiant du système de tarification
	 * @var integer
	 */
	protected $id;
	
	/**
	 * Libellé du système de tarification
	 * @var string
	 */
	protected $label;
	
	/**
	 * Description du système de tarification
	 * @var string
	 */
	
	protected $desc;
	
	/**
	 * Liste des pourcentages du système de tarification
	 * @var array
	 */
	protected $percents;
	
	/**
	 * Exercice associé
	 * @var exercices
	 */
	protected $exercice;

	public function __construct($id) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		$this->label = '';
		$this->desc = '';
		$this->percents = array();
		$this->exercice = new exercices(0);
		if ($this->id) {
			$query = 'select * from rent_pricing_systems where id_pricing_system = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->id = $row->id_pricing_system;
				$this->label = $row->pricing_system_label;
				$this->desc = $row->pricing_system_desc;
				$this->percents = unserialize($row->pricing_system_percents);
				$this->exercice = new exercices($row->pricing_system_num_exercice);
			}
		}
	}
	
	protected function get_entity(){
		global $id_entity;
		
		return new entites($id_entity);
	}
	
	/**
	 * Sélecteur des exercices comptables en cours
	 */
	protected function gen_selector_exercices() {
		global $msg;
				
		$display = '';		
 		$query = exercices::listByEntite($this->get_entity()->id_entite,1);
 		$display=gen_liste($query,'id_exercice','libelle', 'pricing_system_exercices', '', $this->exercice->id_exercice, 0,$msg['pricing_system_exercices_empty'],0,$msg['pricing_system_exercices_default_value']);
 		
		return $display;
	}

	public function get_content_form() {
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->add_element('pricing_system_label', 'pricing_system_label')
		->add_input_node('text', $this->label);
		$interface_content_form->add_element('pricing_system_desc', 'pricing_system_desc')
		->add_textarea_node($this->desc)
		->set_rows(3)
		->set_attributes(array('wrap' => 'virtual'));
		$entity = $this->get_entity();
		$interface_content_form->add_element('pricing_system_entities', 'pricing_system_associated_entity')
		->add_html_node($entity->raison_sociale);
		$interface_content_form->add_element('pricing_system_exercices', 'pricing_system_associated_exercice')
		->add_html_node($this->gen_selector_exercices());
		return $interface_content_form->get_display();
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg;
		global $rent_pricing_system_js_content_form_tpl;
		
		$interface_form = new interface_admin_acquisition_form('pricing_system_form');
		if(!$this->id){
			$interface_form->set_label($msg['pricing_system_form_add']);
		}else{
			$interface_form->set_label($msg['pricing_system_form_edit']);
		}
		$interface_form->set_object_id($this->id)
		->set_id_entity($this->get_entity()->id_entite)
		->set_confirm_delete_msg($msg['pricing_system_delete_confirm'])
		->set_content_form($rent_pricing_system_js_content_form_tpl.$this->get_content_form())
		->set_table_name('rent_pricing_systems')
		->set_field_focus('pricing_system_label')
		->set_duplicable(true);
		return $interface_form->get_display();
	}

	/**
	 * Provenance du formulaire
	 */
	public function set_properties_from_form(){
		global $pricing_system_label;
		global $pricing_system_desc;
		global $pricing_system_exercices;
		
		$this->label = stripslashes($pricing_system_label);
		$this->desc = stripslashes($pricing_system_desc);
		$this->exercice = new exercices($pricing_system_exercices);

	}

	/**
	 * Sauvegarde
	 */
	public function save(){
		
		if($this->id) {
			$query = 'update rent_pricing_systems set ';
			$where = 'where id_pricing_system= '.$this->id;
		} else {
			$query = 'insert into rent_pricing_systems set ';
			$where = '';
		}
		$query .= '
				pricing_system_label = "'.addslashes($this->label).'",
				pricing_system_desc = "'.addslashes($this->desc).'",
				pricing_system_percents = "'.addslashes(serialize($this->percents)).'",
				pricing_system_num_exercice = "'.$this->exercice->id_exercice.'"
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
				$rent_pricing_system_grid = new rent_pricing_system_grid($this->id);
				$rent_pricing_system_grid->init_default_grid();
				$rent_pricing_system_grid->save();
			}
			return true;
		} else {
			return false;
		}
	}
		
	/**
	 * Sauvegarde des pourcentages depuis la grille
	 */
	public function save_percents(){
		
		if(!$this->id) {
			return false;
		}			
		$query = '
				update rent_pricing_systems set 
				pricing_system_percents = "'.addslashes(serialize($this->percents)).'"
				where id_pricing_system= '.$this->id;
		$result = pmb_mysql_query($query);
		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Suppression
	 */
	public function delete(){
		global $msg;
		
		if($this->id) {
			$query = "select count(*) from rent_accounts where account_num_pricing_system = ".$this->id;
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_result($result, 0, 0)) {
				return array(
						'msg_to_display' => $msg['pricing_system_cant_delete'].'<br /><br />',
						'state' => false
				);
			} else {
				$query = "delete from rent_pricing_systems where id_pricing_system= ".$this->id;
				pmb_mysql_query($query);
				return array(
						'msg_to_display' => $msg['pricing_system_success_delete'].'<br /><br />',
						'state' => true
				);
			}
		}
		return array(
				'msg_to_display' => '',
				'state' => false
		);
	}

	public function get_id() {
		return $this->id;
	}

	public function get_label() {
		return $this->label;
	}
	
	public function get_desc() {
		return $this->desc;
	}
	
	public function get_percents() {
		return $this->percents;
	}
	
	public function get_exercice() {
		return $this->exercice;
	}
	
	public function set_id($id) {
		$this->id = $id;
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function set_desc($desc) {
		$this->desc = $desc;
	}
	
	public function set_percents($percents) {
		$this->percents = $percents;
	}
	
	public function set_exercice($exercice) {
		$this->exercice = $exercice;
	}
}
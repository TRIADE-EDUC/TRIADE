<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: threshold.class.php,v 1.5 2023/07/07 07:02:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/entites.class.php");
require_once($include_path."/templates/threshold.tpl.php");

class threshold {
	
	/**
	 * Identifiant du seuil
	 * @var integer
	 */
	protected $id;
	
	/**
	 * Libellé
	 * @var string
	 */
	protected $label;
	
	/**
	 * Montant
	 * @var float
	 */
	protected $amount;
	
	/**
	 * Montant HT/TTC
	 * @var integer
	 */
	protected $amount_tax_included;
	
	/**
	 * Pied de page
	 * @var string
	 */
	protected $footer;
	
	/**
	 * Etablissement associé
	 * @var entites
	 */
	protected $entity;
	
	public function __construct($id=0) {
		$this->id = intval($id);
		$this->fetch_data();
	}
	
	/**
	 * Data
	 */
	protected function fetch_data() {
		$this->label = '';
		$this->amount = '0.00';
		$this->footer = '';
		$this->entity = null;
		if ($this->id) {
			$query = 'select * from thresholds where id_threshold = '.$this->id;
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->label = $row->threshold_label;
				$this->amount = $row->threshold_amount;
				$this->amount_tax_included = $row->threshold_amount_tax_included;
				$this->footer = $row->threshold_footer;
				$this->entity = new entites($row->threshold_num_entity);
			}
		}
	}
		
	public function get_content_form() {
		global $pmb_gestion_devise;
		
		$interface_content_form = new interface_content_form(static::class);
		$interface_content_form->set_grid_model('flat_column_25');
		$interface_content_form->add_element('threshold_entity', 'threshold_entity')
		->add_html_node('<b>'.$this->entity->raison_sociale.'</b>');
		$interface_content_form->add_element('threshold_num_entity')
		->add_input_node('hidden', $this->entity->id_entite);
		$interface_content_form->add_element('threshold_label', 'threshold_label')
		->add_input_node('text', $this->label)
		->set_class('saisie-30em');
		$interface_content_form->add_element('threshold_amount', 'threshold_amount')
		->add_input_node('float', $this->amount)
		->set_label($pmb_gestion_devise);
		$interface_content_form->add_element('threshold_amount_tax_included', 'threshold_amount_tax_included')
		->add_input_node('boolean', $this->amount_tax_included);
		$interface_content_form->add_element('threshold_footer', 'threshold_footer')
		->add_textarea_node($this->footer)
		->set_cols(55)
		->set_rows(10);
		return $interface_content_form->get_display();
	}
	
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg;
		
		$interface_form = new interface_form('threshold_form');
		$interface_form->set_label($msg['threshold_form_edit']);
		$interface_form->set_object_id($this->id)
		->set_confirm_delete_msg($msg['threshold_delete_confirm'])
		->set_content_form($this->get_content_form())
		->set_table_name('thresholds')
		->set_field_focus('threshold_label');
		
		$url_base = $interface_form->get_url_base();
		if(!empty($this->entity)) {
			$url_base .= "&id_entity=".$this->entity->id_entite;
		}
		$interface_form->set_url_base($url_base);
		return $interface_form->get_display();
	}

	/**
	 * Provenance du formulaire
	 */
	public function set_properties_from_form(){
		global $threshold_label;
		global $threshold_amount;
		global $threshold_amount_tax_included;
		global $threshold_footer;
		global $threshold_num_entity;
		
		$this->label = stripslashes($threshold_label);
		$this->amount = floatval(stripslashes($threshold_amount));
		$this->amount_tax_included = intval($threshold_amount_tax_included);
		$this->footer = stripslashes($threshold_footer);
		$this->entity = new entites($threshold_num_entity);
	}
	
	/**
	 * Sauvegarde
	 */
	public function save(){
		if($this->id) {
			$query = 'update thresholds set ';
			$where = 'where id_threshold= '.$this->id;
		} else {
			$query = 'insert into thresholds set ';
			$where = '';
		}
		$query .= '
				threshold_label = "'.addslashes($this->label).'",
				threshold_amount = "'.addslashes($this->amount).'",
				threshold_amount_tax_included = "'.addslashes($this->amount_tax_included).'",
				threshold_footer = "'.addslashes($this->footer).'",	
				threshold_num_entity = "'.$this->entity->id_entite.'"		
				'.$where;
		$result = pmb_mysql_query($query);
		if($result) {
			if(!$this->id) {
				$this->id = pmb_mysql_insert_id();
			}
			return true;
		} else {
			return false;
		}
	}
			
	/**
	 * Suppression
	 */
	public function delete(){
		if($this->id) {
			$query = "delete from thresholds where id_threshold = ".$this->id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function get_data() {
		return array(
			'id' => $this->id,
			'label' => $this->label,
			'amount' => $this->amount,
			'amount_tax_included' => $this->amount_tax_included,
			'footer' => $this->footer
		);
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_amount() {
		return $this->amount;
	}
	
	public function get_amount_tax_included() {
		return $this->amount_tax_included;
	}
	
	public function get_footer() {
		return $this->footer;
	}
	
	public function get_entity() {
		return $this->entity;
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function set_amount($amount) {
		$this->amount = $amount;
	}
	
	public function set_amount_tax_included($amount_tax_included) {
		$this->amount_tax_included = $amount_tax_included;
	}
	
	public function set_footer($footer) {
		$this->footer = $footer;
	}
	
	public function set_entity($id_entity) {
		$this->entity = new entites($id_entity);
	}
}
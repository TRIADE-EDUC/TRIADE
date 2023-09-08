<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: threshold.class.php,v 1.2 2016-08-04 10:00:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
	
	public function __construct($id) {
		$this->id = $id*1;
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
		
	/**
	 * Formulaire
	 */
	public function get_form(){
		global $msg,$charset,$base_path;
		global $threshold_form_tpl;
		
		$form = $threshold_form_tpl;
		
		$form = str_replace("!!entity_label!!",$this->entity->raison_sociale,$form);
		$form = str_replace("!!num_entity!!",$this->entity->id_entite,$form);
		$form = str_replace("!!label!!",$this->label,$form);
		$form = str_replace("!!amount!!",$this->amount,$form);
		$form = str_replace("!!amount_tax_included!!",($this->amount_tax_included ? "checked='checked'" : ""),$form);
		$form = str_replace("!!footer!!",$this->footer,$form);
		if($this->id) {
			$form = str_replace('!!button_delete!!', "<input type='button' class='bouton' id='threshold_button_delete' name='threshold_button_delete' value='".$msg['supprimer']."' onclick=\"if(threshold_delete()) {document.location='".$base_path."/admin.php?categ=acquisition&sub=thresholds&action=delete&id=".$this->id."&id_entity=".$this->entity->id_entite."'}\" />", $form);
		} else {
			$form = str_replace('!!button_delete!!', "", $form);
		}
		$form = str_replace("!!id!!",$this->id,$form);
		return $form;
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
		$this->amount_tax_included = $threshold_amount_tax_included*1;
		$this->footer = stripslashes($threshold_footer);
		$this->entity = new entites($threshold_num_entity*1);
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
		global $msg;
		
		if($this->id) {
			$query = "delete from thresholds where id_threshold = ".$this->id;
			$result = pmb_mysql_query($query);
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
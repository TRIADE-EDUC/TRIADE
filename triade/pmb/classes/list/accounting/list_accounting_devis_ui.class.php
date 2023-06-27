<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_devis_ui.class.php,v 1.2 2018-04-23 13:25:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/accounting/list_accounting_ui.class.php");

class list_accounting_devis_ui extends list_accounting_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_button_add() {
		global $msg;
	
		return "<input class='bouton' type='button' value='".$msg['acquisition_ajout_'.$this->get_initial_name()]."' onClick=\"document.location='".static::get_controller_url_base()."&action=modif&id_bibli=".$this->filters['entite']."&id_".$this->get_initial_name()."=0';\" />";
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'numero' => '38',
						'num_fournisseur' => 'acquisition_ach_fou2',
						'date_acte' => 'acquisition_cde_date_cde',
						'statut' => 'acquisition_statut',
						'print_mail' => ''
				)
		);
	}
	
	protected function init_default_columns() {
		if ($this->filters['status'] != STA_ACT_ALL) {
			$this->add_column_selection();
		}
		$this->add_column('numero', '38');
		$this->add_column('num_fournisseur');
		$this->add_column('date_acte');
		$this->add_column('statut');
		$this->add_column('print_mail');
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			if($this->filters['status'] != STA_ACT_ALL) {
				//Bouton recevoir
				if ($this->filters['status'] == STA_ACT_ENC){
					$this->selection_actions[] = $this->get_selection_action('rec', $msg['acquisition_dev_bt_rec'], 'save.gif', $this->get_link_action('list_rec', 'rec'));
				}
					
				//Bouton archiver
				if ($this->filters['status'] == STA_ACT_REC || $this->filters['status'] == STA_ACT_ENC){
					$this->selection_actions[] = $this->get_selection_action('archive', $msg['acquisition_act_bt_arc'], 'folderclosed.gif', $this->get_link_action('list_arc', 'arc'));
				}
					
				//Bouton supprimer
				$this->selection_actions[] = $this->get_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('list_delete', 'sup'));
			}
		}
		return $this->selection_actions;
	}
	
	public function get_type_acte() {
		return TYP_ACT_DEV;
	}
	
	public function get_initial_name() {
		return 'dev';
	}
	
	public static function run_arc_object($object) {
		if($object->type_acte==TYP_ACT_DEV) {
			$object->statut=($object->statut | STA_ACT_ARC);
			$object->update_statut();
		}
	}
	
	public static function run_rec_object($object) {
		if($object->type_acte==TYP_ACT_DEV) {
			$object->statut=STA_ACT_REC;
			$object->update_statut();
		}
	}
	
	public static function run_delete_object($object) {
		if ($object->type_acte==TYP_ACT_DEV) {
			$object->delete();
		}
	}
}
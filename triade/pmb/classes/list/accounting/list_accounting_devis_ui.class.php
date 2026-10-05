<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_devis_ui.class.php,v 1.7.10.2 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_accounting_devis_ui extends list_accounting_ui {
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_interface_button($msg['acquisition_ajout_'.$this->get_initial_name()], ['location' => static::get_controller_url_base()."&action=modif&id_bibli=".$this->filters['entite']."&id_".$this->get_initial_name()."=0"]);;
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
    				    'commentaires' => 'acquisition_commentaires',
    				    'commentaires_i' => 'acquisition_commentaires_i',
						'print_mail' => 'print_mail'
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
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		if($this->filters['status'] != STA_ACT_ALL) {
			//Bouton recevoir
			if ($this->filters['status'] == STA_ACT_ENC){
				$this->add_selection_action('rec', $msg['acquisition_dev_bt_rec'], 'save.gif', $this->get_link_action('list_rec', 'rec'));
			}
			
			//Bouton archiver
			if ($this->filters['status'] == STA_ACT_REC || $this->filters['status'] == STA_ACT_ENC){
				$this->add_selection_action('archive', $msg['acquisition_act_bt_arc'], 'folderclosed.gif', $this->get_link_action('list_arc', 'arc'));
			}
			
			//Bouton supprimer
			$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('list_delete', 'sup'));
		}
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
		    actes::delete($object->id_acte);
		}
	}
}
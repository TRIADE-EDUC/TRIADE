<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_invoices_ui.class.php,v 1.3 2018-05-26 06:51:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/accounting/list_accounting_ui.class.php");

class list_accounting_invoices_ui extends list_accounting_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'numero' => '38',
						'num_acte_parent' => 'acquisition_act_num_cde',
						'num_fournisseur' => 'acquisition_ach_fou2',
						'date_acte' => 'acquisition_fac_date_rec',
						'statut' => 'acquisition_statut'
				)
		);
	}
	
	protected function init_default_columns() {
		if($this->filters['status'] == STA_ACT_REC) {
			$this->add_column_selection();
		}
		$this->add_column('numero');
		$this->add_column('num_acte_parent');
		$this->add_column('num_fournisseur');
		$this->add_column('date_acte');
		$this->add_column('statut');
		$this->add_column_print('fact');
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			if($this->filters['status'] == STA_ACT_REC) {
				//Bouton payer
				$this->selection_actions[] = $this->get_selection_action('pay', $msg['acquisition_fac_bt_pay'], 'pay.png', $this->get_link_action('list_pay', 'pay'));
			}
		}
		return $this->selection_actions;
	}
	
	public function get_type_acte() {
		return TYP_ACT_FAC;
	}
	
	public function get_initial_name() {
		return 'fac';
	}
	
	public static function run_pay_object($object) {
		if($object->type_acte==TYP_ACT_FAC && $object->statut=STA_ACT_REC) {
			$object->statut=STA_ACT_PAY;
			$object->update_statut();

			//La commande correspondante est-elle entierement payee
			$id_cde = liens_actes::getParent($object->id_acte);
			$tab_pay = liens_actes::getChilds($id_cde, TYP_ACT_FAC);
			$paye= true;
			while (($row_pay = pmb_mysql_fetch_object($tab_pay))) {
				if(($row_pay->statut & STA_ACT_PAY) != STA_ACT_PAY){
					$paye = false;
					break;
				}
			}
			if ($paye) {
				$cde=new actes($id_cde);
				$cde->statut = ($cde->statut | STA_ACT_PAY);
				$cde->update_statut();
			}
		}
	}
}
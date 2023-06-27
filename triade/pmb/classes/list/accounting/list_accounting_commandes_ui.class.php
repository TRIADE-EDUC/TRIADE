<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_accounting_commandes_ui.class.php,v 1.4 2019-02-26 15:49:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/accounting/list_accounting_ui.class.php");

class list_accounting_commandes_ui extends list_accounting_ui {
		
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
						'date_echeance' => 'acquisition_cde_date_ech',
						'statut' => 'acquisition_statut',
						'print_mail' => ''
				)
		);
	}
	
	protected function init_default_columns() {
		if ($this->filters['status'] && $this->filters['status'] != STA_ACT_ALL && $this->filters['status'] != STA_ACT_ARC) {
			$this->add_column_selection();
		}
		$this->add_column('numero');
		$this->add_column('num_fournisseur');
		$this->add_column('date_acte');
		$this->add_column('date_echeance');
		$this->add_column('statut');
		$this->add_column('print_mail');
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
	
		$content = '';
		switch($property) {
			case 'date_echeance':
				if($object->date_ech_calc != '00000000') {
					$content .= formatdate($object->date_ech_calc);
				}
				break;
			case 'statut':
				$st = (($object->statut) & ~(STA_ACT_FAC | STA_ACT_PAY | STA_ACT_ARC));
				switch ($st) {
					case STA_ACT_AVA :
						$statut = htmlentities($msg['acquisition_cde_aval'], ENT_QUOTES, $charset);
						break;
					case STA_ACT_ENC :
						$statut = htmlentities($msg['acquisition_cde_enc'], ENT_QUOTES, $charset);
						break;
					case STA_ACT_REC :
						$statut = htmlentities($msg['acquisition_cde_liv'], ENT_QUOTES, $charset);
						break;
					default :
						$statut = htmlentities($msg['acquisition_cde_enc'], ENT_QUOTES, $charset);
				}
				if( ($object->statut & STA_ACT_PAY) == STA_ACT_PAY ) {
					$st_fac = htmlentities($msg['acquisition_act_pay'], ENT_QUOTES, $charset); 
				} elseif( ($object->statut & STA_ACT_FAC) == STA_ACT_FAC ) {
						$st_fac = htmlentities($msg['acquisition_act_fac'], ENT_QUOTES, $charset); 
				} else 
					$st_fac = '';
				if ($st_fac) $statut.='&nbsp;/&nbsp;'.$st_fac;
				if(($object->statut & STA_ACT_ARC) == STA_ACT_ARC) {
					$content .= '<s>'.$statut.'</s>';
				} else {
					$content .= $statut;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			switch($this->filters['status']) {
				case STA_ACT_AVA :
					//Bouton valider
					$this->selection_actions[] = $this->get_selection_action('valid', $msg['acquisition_act_bt_val'], 'tick.gif', $this->get_link_action('list_valid', 'val'));
					
					//Bouton supprimer
					$this->selection_actions[] = $this->get_selection_action('delete', $msg['63'], 'interdit.gif', $this->get_link_action('list_delete', 'sup'));
					break;
				case STA_ACT_ENC :
					$this->selection_actions[] = $this->get_selection_action('sold', $msg['acquisition_cde_bt_sol'], 'sold.png', $this->get_link_action('list_sold', 'sol'));
					break;
				case STA_ACT_REC :
					$this->selection_actions[] = $this->get_selection_action('arc', $msg['acquisition_act_bt_arc'], 'folderclosed.gif', $this->get_link_action('list_arc', 'arc'));
					break;
				default:
					break;
			}
		}
		return $this->selection_actions;
	}
	
	public function get_type_acte() {
		return TYP_ACT_CDE;
	}
	
	public function get_initial_name() {
		return 'cde';
	}
	
	public static function run_valid_object($object) {
		if ($object->type_acte == TYP_ACT_CDE && $object->statut==STA_ACT_AVA) {
			$object->statut=STA_ACT_ENC;
			$object->date_valid=date("Y-m-d");
			$object->save();
		}
	}
	
	public static function run_arc_object($object) {
		//Commande archivée
		$object->statut = ($object->statut | STA_ACT_ARC);
		$object->update_statut();
		
		//Archivage des factures et bl correspondants
		$list_childs = liens_actes::getChilds($object->id_acte);
		while (($row = pmb_mysql_fetch_object($list_childs))) {
			$act = new actes($row->num_acte_lie);
			$act->statut = ($act->statut | STA_ACT_ARC);
			$act->update_statut();
		}
	}
	
	public static function run_sold_object($object) {
		global $comment, $ref, $date_pay, $num_pay;
		
		//Commande considérée comme soldée
		$object->statut = ($object->statut & (~STA_ACT_ENC));
		$object->statut = ($object->statut | STA_ACT_REC);
		
		//Les quantites livrees sur la commande sont-elles entierement facturees
		//Si oui statut commande >> facture
		$tab_cde = actes::getLignes($object->id_acte);
		$facture = true;
		while(($row_cde = pmb_mysql_fetch_object($tab_cde))) {
			$tab_liv = lignes_actes::getLivraisons($row_cde->id_ligne);
			$tab_fac = lignes_actes::getFactures($row_cde->id_ligne);
			$nb_liv = 0;
			while (($row_liv = pmb_mysql_fetch_object($tab_liv))) {
				$nb_liv = $nb_liv + $row_liv->nb;
			}
			$nb_fac = 0;
			while(($row_fac = pmb_mysql_fetch_object($tab_fac))) {
				$nb_fac = $nb_fac + $row_fac->nb;
			}
			if ($nb_liv > $nb_fac) {
				$facture = false;
				break;
			}
		}
		
		if ($facture) {
			$object->statut = ($object->statut | STA_ACT_FAC); //Pas de reste à facturer >>Statut commande = facturée
		
			//Si de plus toutes les factures sont payées, Statut commande=payé
			$tab_pay = liens_actes::getChilds($object->id_acte, TYP_ACT_FAC);
			$paye= true;
			while (($row_pay = pmb_mysql_fetch_object($tab_pay))) {
				if(($row_pay->statut & STA_ACT_PAY) != STA_ACT_PAY){
					$paye = false;
					break;
				}
			}
			if ($paye) $object->statut = ($object->statut | STA_ACT_PAY);
		} else {
			$object->statut = ($object->statut & (~STA_ACT_FAC));	//Reste à facturer >>Statut commande = non facturée
		}
		$object->numero=addslashes($object->numero);
		$object->commentaires = trim($comment);
		$object->commentaires_i = addslashes($object->commentaires_i);
		$object->reference = trim($ref);
		$object->date_paiement = $date_pay;
		$object->num_paiement = trim($num_pay);
		$object->devise = addslashes($object->devise);
		$object->save();
	}
	
	public static function run_delete_object($object) {
		if ($object->type_acte==TYP_ACT_CDE && $object->statut==STA_ACT_AVA) {
			$object->delete();
		}
	}
}
<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_cashdesk_transactions_ui.class.php,v 1.1.2.3 2024/09/11 14:19:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_cashdesk_transactions_ui extends list_cashdesk_ui {
		
	protected function _get_query_base() {
    
        $query = "SELECT * FROM transactions
                JOIN comptes ON compte_id=id_compte
                JOIN empr ON id_empr=proprio_id
                LEFT JOIN cashdesk ON cashdesk_num=cashdesk_id
                LEFT JOIN transaction_payment_methods ON transaction_payment_method_num=transaction_payment_method_id
        ";
        return $query;
    }
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('cashdesks');
		$this->add_selected_filter('date_effective');
	}
	
	protected function init_default_settings() {
	    parent::init_default_settings();
	    $this->set_setting_display('search_form', 'unfoldable_filters', false);
	    $this->set_setting_column('transactype_name', 'align', 'left');
	    $this->set_setting_column('date_enrgt', 'datatype', 'date');
	    $this->set_setting_column('date_effective', 'datatype', 'date');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'cashdesk_name' => 'cashdesk_edition_name',
    				    'transactype_name' => 'cashdesk_edition_transac_name',
				        'empr' => 'Nom du lecteur',
				        'compte' => 'Type de compte',
				        'date_enrgt' => 'finance_list_tr_date_enrgt',
				        'date_effective' => 'Date effective',
    				    'montant' => 'finance_montant',
				        'sens' => 'finance_list_tr_deb_cred',
				        'commentaire' => 'finance_list_tr_comment',
    				    'payment_method' => 'cashdesk_edition_transac_payment_method'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('empr');
		$this->add_column('compte');
		$this->add_column('date_enrgt');
		$this->add_column('sens');
		$this->add_column('montant');
		$this->add_column('commentaire');
		$this->add_column('payment_method');
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('empr');
	    $this->add_applied_sort('compte');
	    $this->add_applied_sort('date_enrgt', 'desc');
	}
	
	protected function _add_query_filters() {
	    parent::_add_query_filters();
	    $this->_add_query_filter_multiple_restriction('transactypes', 'type_compte_id', 'integer');
	    $this->_add_query_filter_interval_restriction('date_effective', 'date_effective', 'datetime');
	}
	
	protected function _get_object_property_empr($object) {
	    return $object->empr_nom." ".$object->empr_prenom;
	}
	
	protected function _get_object_property_compte($object) {
	    global $msg;
	    
	    switch (intval($object->type_compte_id)) {
	        case 1 :
	            return $msg["finance_solde_abt"];
	        case 2 :
	            return $msg["finance_solde_amende"];
	        case 3 :
	            return $msg["finance_solde_pret"];
	        case 22 :
	            return $msg["transactype_empr_animation"];
	        case 0 :
	            
	            return "ALLER CHERCHER LE row->transactype_name";
	    }
	    printr($object);
	}
	
	protected function _get_object_property_montant($object) {
	    return $this->format_price(($object->montant*$object->sens));
	}
	
	protected function _get_object_property_sens($object) {
	    global $msg;
	    
	    if($object->sens == 1) {
	        return $msg['finance_form_empr_libelle_credit'];
	    } else {
	        return $msg['finance_form_empr_libelle_debit'];
	    }
	}
	
	protected function _get_object_property_payment_method($object) {
	    return $object->transaction_payment_method_name;
	}
}
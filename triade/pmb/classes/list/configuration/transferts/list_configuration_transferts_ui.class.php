<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_transferts_ui.class.php,v 1.2 2021/02/23 07:44:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_transferts_ui extends list_configuration_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		static::$module = 'admin';
		static::$categ = 'transferts';
		static::$sub = str_replace(array('list_configuration_transferts_', '_ui'), '', static::class);
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		global $sub;
		if(isset($msg["admin_tranferts_".$sub])) {
			return htmlentities($msg["admin_tranferts_".$sub], ENT_QUOTES, $charset);
		}
		return '';
	}
	
	protected function add_selector_status_parameter($type_param, $sstype_param, $label_code, $empty_label_code='') {
		global $msg;
		
		$values = array();
		if($empty_label_code) {
			$values[] = array(
					"value" => "0",
					"label" => $msg[$empty_label_code]
			);
		}
		$values[] = array (
				"query" => "SELECT idstatut, statut_libelle FROM docs_statut order by statut_libelle",
				"affichage" => "SELECT statut_libelle FROM docs_statut WHERE idstatut=!!id!!"
		);
		$this->add_parameter($type_param, $sstype_param, $label_code, $values);
	}
	
	protected function add_selector_parameter($type_param, $sstype_param, $label_code, $values = array()) {
		global $msg;
		
		if(empty($values)) {
			$values = array (
					array ("value" => "0", "label" => $msg["39"] ),
					array ("value" => "1", "label" => $msg["40"] )
			);
		}
		$this->add_parameter($type_param, $sstype_param, $label_code, $values);
	}
}
<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_account_types.class.php,v 1.6 2022/02/18 13:10:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/rent/rent_account_types.tpl.php");
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/budgets.class.php");
require_once($class_path."/marc_table.class.php");

class rent_account_types {	

	/**
	 * Instance de la classe entites
	 * @var entites
	 */
	protected $entity;
	
	/**
	 * Instance de la classe exercices
	 * @var exercices
	 */
	protected $exercice;
	
	/**
	 * Types de décompte
	 * @var array
	 */
	protected $account_types;
	
	/**
	 * Message d'information pour l'utilisateur
	 * @var string
	 */
	protected $messages;

	/**
	 * Message d'information pour l'utilisateur
	 * @var string
	 */
	protected $request_type_pref_account_messages;
	
	public function __construct($id_entity=0, $id_exercice=0) {
		$this->entity = new entites($id_entity);
		if(!$id_exercice) {
			$id_exercice = $this->get_last_exercice();
		}
		$this->exercice = new exercices($id_exercice);
		$this->fetch_data();
	}
	
	protected function fetch_data() {

		$marclist = new marc_list('rent_account_type');
		
		$this->account_types = array();
		foreach ($marclist->table as $key => $label) {
			$account_type = array();
			$account_type['code'] = $key;
			$account_type['label'] = $label;
			$query = 'select account_type_num_section from rent_account_types_sections where account_type_num_exercice='.$this->exercice->id_exercice.' and account_type_marclist="'.$key.'"';
			$result = pmb_mysql_query($query);
			if($result && pmb_mysql_num_rows($result)) {
				$account_type['section'] = pmb_mysql_result($result, 0, 'account_type_num_section');
			} else {
				$account_type['section'] = 0;
			}
			$this->account_types[] = $account_type;
		}
		$this->messages = '';
		$this->request_type_pref_account_messages = '';
	}
	
	protected function get_last_exercice() {
		$query = "select id_exercice from exercices where num_entite = '".$this->entity->id_entite."' and (statut &  '".STA_EXE_ACT."') = '".STA_EXE_ACT."' order by date_debut desc limit 1";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			return pmb_mysql_result($result, 0, 'id_exercice'); 
		} else {
			return 0;
		}
	}
	
	protected function gen_selector_exercices() {
		$display = '';
		$query = exercices::listByEntite($this->entity->id_entite,1);
		$display=gen_liste($query,'id_exercice','libelle', 'account_types_exercices', 
			'document.location=\'./admin.php?categ=acquisition&sub=account_types&id_entity='.$this->entity->id_entite.'&id_exercice=\'+this.value', $this->exercice->id_exercice, 0, '',0, '');
			
		return $display;
	}
	
	protected function gen_selector_sections($code, $selected = 0) {
		global $msg;
		
		$display = '<select name="account_types_sections['.$code.']" id="account_types_sections_'.$code.'">';		
		$result_budgets = budgets::listByExercice($this->exercice->id_exercice);
		$temp_display='';
		while($row_budget = pmb_mysql_fetch_object($result_budgets)){	
			$query = budgets::listRubriques($row_budget->id_budget);
			$result_sections = pmb_mysql_query($query);			
			if(pmb_mysql_num_rows($result_sections)){	
				$temp_display.='<optgroup label="'.$row_budget->libelle.'">'; 
				while($row_section = pmb_mysql_fetch_object($result_sections)){	
					$temp_display.='<option value="'.$row_section->id_rubrique.'" '.($selected == $row_section->id_rubrique ? 'selected=selected' : '').'>'.$row_section->libelle.'</option>';
				}
				$temp_display.='</optgroup>';
			}
		}	
		if(!$temp_display){
			$display.='<option value="0">'.$msg['account_types_section_empty'].'</option>';			
		}else{
			$display.='<option value="0">'.$msg['account_types_section_first'].'</option>';
		}
		$display.=$temp_display.'</select>';
		return $display;
	}
	
	public function get_list() {
		global $rent_account_types_list_tpl;
		global $rent_account_type_line_tpl;
	
		$display = $rent_account_types_list_tpl;
	
		$lines = '';
		$parity = 1;
		foreach ($this->account_types as $account_type) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
				
			$line = $rent_account_type_line_tpl;
			$line = str_replace('!!odd_even!!', $pair_impair, $line);
			$line = str_replace('!!label!!', $account_type['label'], $line);
			$line = str_replace('!!sections!!', $this->gen_selector_sections($account_type['code'], $account_type['section']), $line);
			$lines .= $line;
		}
		$display = str_replace('!!account_types_lines!!', $lines, $display);
		$display = str_replace('!!id_entity!!', $this->entity->id_entite, $display);
		$display = str_replace('!!id_exercice!!', $this->exercice->id_exercice, $display);
		$display = str_replace('!!exercices!!', $this->gen_selector_exercices(), $display);
		$display = str_replace('!!messages!!', $this->get_messages(), $display);
		
		return $display;
	}

	public static function get_request_type_pref_account($key) {
		global $acquisition_request_type_pref_account;
		$param = (array) json_decode($acquisition_request_type_pref_account);
		if (isset($param[$key])) return $param[$key];
		return ''; 
	}
	
	public function get_request_type_pref_account_list() {
		global $rent_request_type_pref_account_list_tpl;
		global $rent_request_type_pref_account_tpl;
	
		$display = $rent_request_type_pref_account_list_tpl;
	
		$lines = '';
		$parity = 1;
		$marclist = new marc_list('rent_request_type');
		foreach ($marclist->table as $key => $label) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
	
			$line = $rent_request_type_pref_account_tpl;
			$line = str_replace('!!odd_even!!', $pair_impair, $line);
			$line = str_replace('!!request_type_label!!', $label, $line);
			$line = str_replace('!!accounts!!', $this->gen_selector_accounts($key, $this->get_request_type_pref_account($key)), $line);
			$lines .= $line;
		}
		$display = str_replace('!!rent_request_type_pref_account_lines!!', $lines, $display);
		$display = str_replace('!!id_entity!!', $this->entity->id_entite, $display);
		$display = str_replace('!!id_exercice!!', $this->exercice->id_exercice, $display);
		$display = str_replace('!!messages!!', $this->get_request_type_pref_account_messages(), $display);
	
		return $display;
	}

	public function gen_selector_accounts($key, $pref_account_key) {		
		$marclist = new marc_select('rent_account_type', "pref_account[".$key."]", $pref_account_key);
		return $marclist->display;
	}

	public function save_request_type_pref_account() {
		global $acquisition_request_type_pref_account;
		global $pref_account;

		$acquisition_request_type_pref_account = json_encode($pref_account);
		$query = 'update parametres set valeur_param="'.addslashes($acquisition_request_type_pref_account).'" where type_param="acquisition" and sstype_param="request_type_pref_account" ';
		$result = pmb_mysql_query($query);
		if(!$result) {
			return false;
		}
		return true;
	}
	
	public function set_properties_from_form() {
		global $account_types_sections;
		
		$this->account_types = array();
		$marclist = new marc_list('rent_account_type');
		foreach ($marclist->table as $key => $label) {
			$account_type = array();
			$account_type['code'] = $key;
			$account_type['label'] = $label;
			if($account_types_sections[$key]){
				$account_type['section']=$account_types_sections[$key];
			} else {
				$account_type['section'] = 0;
			}
			$this->account_types[] = $account_type;
		}
	}
	
	public function save() {
		$this->delete();
		foreach ($this->account_types as $account_type) {	
			$query = 'insert into rent_account_types_sections set 
					account_type_num_exercice='.$this->exercice->id_exercice.',
					account_type_num_section='.$account_type['section'].',
					account_type_marclist="'.$account_type['code'].'"';
			$result = pmb_mysql_query($query);
			if(!$result) {				
				return false;
			}			
		}
		return true;	
	}

	public function delete() {
		$query = "delete from rent_account_types_sections where account_type_num_exercice= ".$this->exercice->id_exercice;
		pmb_mysql_query($query);
	}
	
	public function get_account_types() {
		return $this->account_types;
	}
	
	public function set_account_types($account_types) {
		$this->account_types = $account_types;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function set_messages($messages) {
		$this->messages = $messages;
	}

	public function get_request_type_pref_account_messages() {
		return $this->request_type_pref_account_messages;
	}
	
	public function set_request_type_pref_account_messages($messages) {
		$this->request_type_pref_account_messages = $messages;
	}
}
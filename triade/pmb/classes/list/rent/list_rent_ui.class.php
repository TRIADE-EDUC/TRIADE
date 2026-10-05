<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_ui.class.php,v 1.10.2.4 2024/09/03 07:01:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/entites.class.php");
require_once($class_path."/exercices.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/rent/rent_pricing_system.class.php");

class list_rent_ui extends list_ui {
		
    protected function get_form_title() {
        global $msg, $sub;
        
        return $msg['search'].' : '.$msg['acquisition_rent_'.$sub];
    }
    
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('id', 'desc');
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('id', 'align', 'center');
		$this->set_setting_column('date', 'align', 'center');
		$this->set_setting_column('date', 'datatype', 'date');
		$this->set_setting_column('valid_date', 'datatype', 'date');
		$this->set_setting_column('receipt_limit_date', 'datatype', 'date');
		$this->set_setting_column('receipt_effective_date', 'datatype', 'date');
		$this->set_setting_column('return_date', 'datatype', 'date');
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$this->is_displayed_add_filters_block = false;
		$search_form = "<script src='javascript/pricing_systems.js'></script>";
		$search_form .= parent::get_search_form();
		return $search_form;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('entity', 'integer');
		$this->set_filter_from_form('exercice', 'integer');
		$this->set_filter_from_form('exercices', 'integer');
		$this->set_filter_from_form('type');
		$this->set_filter_from_form('num_publisher', 'integer');
		$this->set_filter_from_form('num_supplier', 'integer');
		$this->set_filter_from_form('num_author', 'integer');
		$this->set_filter_from_form('date_start');
		$this->set_filter_from_form('date_end');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'rent_pricing_systems':
				$query = 'select id_pricing_system as id, pricing_system_label as label from rent_pricing_systems order by label';
				break;
			case 'exercices':
			    $query = "select id_exercice as id, libelle as label from exercices where num_entite = '".$this->filters['entity']."' and (statut & '1') = '1' order by date_debut desc";
                break;
		}
		return $query;
	}
	
	protected function get_search_filter_entity() {
		return entites::getBibliHtmlSelect(SESSuserid, $this->filters['entity'], false, array('id' => $this->objects_type.'_entity', 'name' => $this->objects_type.'_entity', 'onchange'=>'account_load_exercices(this.value, "'.$this->objects_type.'");'));
	}
	
	protected function get_search_filter_exercice() {
		global $msg;
		entites::setSessionBibliId($this->filters['entity']);
		$query = exercices::listByEntite($this->filters['entity'],1);
		return gen_liste($query,'id_exercice','libelle', $this->objects_type.'_exercice', '', $this->filters['exercice'], 0,$msg['acquisition_account_exercices_empty'],0, $msg['all']);
	}
	
	protected function get_search_filter_exercices() {
	    global $msg;
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('exercices'), 'exercices', $msg['all']);
	}
	
	protected function get_search_filter_type() {
		global $msg;
		$invoice_types = new marc_select('rent_account_type', $this->objects_type.'_type', $this->filters['type'], '', 0, $msg['acquisition_account_type_select_all']);
		return $invoice_types->display;
	}
	
	protected function get_search_filter_num_publisher() {
		global $msg, $charset;
		
		$publisher_display = '';
		if($this->filters['num_publisher']) {
			$publisher = new editeur($this->filters['num_publisher']);
			$publisher_display = $publisher->display;
		}
		return "<input type='text' id='".$this->objects_type."_publisher' autfield='".$this->objects_type."_num_publisher' completion='publishers' class='saisie-20emr' value='".htmlentities($publisher_display, ENT_QUOTES, $charset)."' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=editeur&caller=".$this->get_form_name()."&p1=".$this->objects_type."_num_publisher&p2=".$this->objects_type."_publisher&deb_rech='+this.form.".$this->objects_type."_publisher.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.".$this->objects_type."_publisher.value=''; this.form.".$this->objects_type."_num_publisher.value='0'; \" />
				<input type='hidden' id='".$this->objects_type."_num_publisher' name='".$this->objects_type."_num_publisher' value='".$this->filters['num_publisher']."' />";
	}
	
	protected function get_search_filter_num_supplier() {
		global $msg, $charset;
		
		$supplier_display = '';
		if($this->filters['num_supplier']) {
			$supplier = new entites($this->filters['num_supplier']);
			$supplier_display = $supplier->raison_sociale;
		}
		return "<input type='text' id='".$this->objects_type."_supplier' autfield='".$this->objects_type."_num_supplier' completion='fournisseur' class='saisie-20emr' value='".htmlentities($supplier_display, ENT_QUOTES, $charset)."' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=fournisseur&caller=".$this->get_form_name()."&param1=".$this->objects_type."_num_supplier&param2=".$this->objects_type."_supplier&id_bibli='+this.form.".$this->objects_type."_entities.value+'&deb_rech='+this.form.".$this->objects_type."_supplier.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.".$this->objects_type."_supplier.value=''; this.form.".$this->objects_type."_num_supplier.value='0'; \" />
				<input type='hidden' id='".$this->objects_type."_num_supplier' name='".$this->objects_type."_num_supplier' value='".$this->filters['num_supplier']."' />";
	}
	
	protected function get_search_filter_num_author() {
		global $msg, $charset;
		
		$author_display = '';
		if($this->filters['num_author']) {
			$author = new auteur($this->filters['num_author']);
			$author_display = $author->display;
		}
		return "<input type='text' id='".$this->objects_type."_author' autfield='".$this->objects_type."_num_author' completion='authors' class='saisie-20emr' value='".htmlentities($author_display, ENT_QUOTES, $charset)."' autocomplete='off' />
				<input type='button' class='bouton_small' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=".$this->get_form_name()."&param1=".$this->objects_type."_num_author&param2=".$this->objects_type."_author&deb_rech='+this.form.".$this->objects_type."_author.value, 'selector')\"/>
				<input type='button' class='bouton_small' value='".$msg['raz']."'  onclick=\"this.form.".$this->objects_type."_author.value=''; this.form.".$this->objects_type."_num_author.value='0'; \" />
				<input type='hidden' id='".$this->objects_type."_num_author' name='".$this->objects_type."_num_author' value='".$this->filters['num_author']."' />";
	}
	
	protected function get_search_filter_num_pricing_system() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('rent_pricing_systems'), 'num_pricing_system', $msg['demandes_localisation_all']);
	}
	
	protected function get_search_filter_pricing_systems() {
	    global $msg;
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('rent_pricing_systems'), 'pricing_systems', $msg['demandes_localisation_all']);
	}
	
	protected function get_search_filter_pricing_system_except() {
	    global $msg;
	    
	    return $this->get_search_filter_boolean_selection('pricing_system_except', $msg['all']);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function _get_object_property_num_user($object) {
		return $object->get_user()->prenom.' '.$object->get_user()->nom;
	}
	
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'num_pricing_system':
				return "select pricing_system_label from rent_pricing_systems where id_pricing_system = ".$this->filters[$property];
			case 'exercices':
			    return "select libelle from exercices where id_exercice IN (".implode(',', $this->filters[$property]).")";
		}
		return '';
	}
	
	protected function _get_query_human_entity() {
		if($this->filters['entity']) {
			$entity = new entites($this->filters['entity']);
			return $entity->raison_sociale;
		}
		return '';
	}
	
	protected function _get_query_human_exercice() {
		if($this->filters['exercice']) {
			$exercice = new exercices($this->filters['exercice']);
			return $exercice->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_type() {
		if($this->filters['type']) {
			$account_types = new marc_list('rent_account_type');
			return $account_types->table[$this->filters['type']];
		}
	}
	
	protected function _get_query_human_num_publisher() {
		if($this->filters['num_publisher']) {
			$publisher = new editeur($this->filters['num_publisher']);
			return $publisher->display;
		}
		return '';
	}
	
	protected function _get_query_human_num_supplier() {
		if($this->filters['num_supplier']) {
			$supplier = new entites($this->filters['num_supplier']);
			return $supplier->raison_sociale;
		}
		return '';
	}
	
	protected function _get_query_human_num_author() {
		if($this->filters['num_author']) {
			$author = new auteur($this->filters['num_author']);
			return $author->display;
		}
		return '';
	}
	
	protected function _get_query_human_num_pricing_system() {
		if($this->filters['num_pricing_system']) {
			$rent_pricing_system = new rent_pricing_system($this->filters['num_pricing_system']);
			return $rent_pricing_system->get_label();
		}
		return '';
	}
	
	protected function _get_query_human_pricing_systems() {
	    $labels = [];
	    if(!empty($this->filters['pricing_systems'])) {
	        foreach ($this->filters['pricing_systems'] as $num_pricing_system) {
	            $rent_pricing_system = new rent_pricing_system($num_pricing_system);
	            $labels[] = $rent_pricing_system->get_label();
	        }
	    }
	    return $labels;
	}
	
	protected function _get_query_human_pricing_system_except() {
	    global $msg;
	    
        if($this->filters['pricing_system_except'] != '' && $this->filters['pricing_system_except'] != -1) {
            if($this->filters['pricing_system_except']) {
                return $msg['40'];
            } else {
                return $msg['39'];
            }
        }
	}
	
	protected function _get_query_human_date() {
		return $this->_get_query_human_interval_date('date');
	}
	
	protected function get_error_message_empty_selection($action=array()) {
		global $msg, $sub;
		return $msg['acquisition_'.$sub.'_checked_empty'];
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	public function has_rights() {
	    global $acquisition_rent_requests_activate;
	    
	    if(!$acquisition_rent_requests_activate) {
	        return false;
	    }
	    return parent::has_rights();
	}
}
<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_expl_ui.class.php,v 1.20.4.1 2025/05/06 09:11:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc/serialcirc_expl.class.php");
require_once($class_path."/expl.class.php");

class list_serialcirc_expl_ui extends list_serialcirc_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT * FROM serialcirc_expl
				JOIN exemplaires ON serialcirc_expl.num_serialcirc_expl_id = exemplaires.expl_id
				JOIN bulletins ON exemplaires.expl_bulletin = bulletins.bulletin_id
				JOIN serialcirc ON serialcirc_expl.num_serialcirc_expl_serialcirc = serialcirc.id_serialcirc
				left JOIN abts_abts ON exemplaires.expl_abt_num = abts_abts.abt_id ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		$object_instance = new serialcirc_expl($row->id_serialcirc_expl);
		
		$object_instance->set_abt_num($row->expl_abt_num);
		$object_instance->set_abt_name($row->abt_name);
		
		$object_instance->set_bulletin_numero($row->bulletin_numero);
		$object_instance->set_mention_date($row->mention_date);
		$object_instance->set_bulletin_notice($row->bulletin_notice);
		$object_instance->set_bulletin_id($row->bulletin_id);
		$object_instance->set_num_notice($row->num_notice);
		
		$object_instance->set_serialcirc_type($row->serialcirc_type);
		$object_instance->set_serialcirc_checked($row->serialcirc_checked);
		
		return $object_instance;
	}
	
	protected function add_object($row) {
		$object_instance = $this->get_object_instance($row);
		if($this->is_visible_by_fast_filters($object_instance)) {
			$classements = $object_instance->get_classements();
			if(!empty($classements)) {
				foreach ($classements as $classement) {
					$object_instance->set_classement($classement);
					$this->objects[] = clone($object_instance);
				}
			}
		}
	}
	
	protected function fetch_data() {
		parent::fetch_data();
		$this->pager['nb_results'] = count($this->objects);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date_date', 'desc');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'periode' => 'serialcirc_circ_list_bull_circulation_periode',
						'serial' => 'serialcirc_circ_list_bull_circulation_perodique',
						'bulletin_numero' => 'serialcirc_circ_list_bull_circulation_numero',
						'abonnement' => 'serialcirc_circ_list_bull_circulation_abonnement',
						'expl_cb' => 'serialcirc_circ_list_bull_circulation_cb',
						'destinataire' => 'serialcirc_circ_list_bull_circulation_destinataire',
						'actions' => 'serialcirc_circ_list_bull_circulation_actions',
						'classement' => 'serialcirc_circ_list_bull_circulation_classement'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['serialcirc_circ_list_bull_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_display_html_content_selection() {
		return "<div class='center'><input type='checkbox' id='".$this->objects_type."_selection_!!expl_id!!' name='".$this->objects_type."_selection[!!expl_id!!]' class='".$this->objects_type."_selection' value='!!expl_id!!' data-classement='!!classement!!' aria-labelledby='".$this->get_name_selection_objects()."_accessibility_label_!!id!!'></div>";
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!expl_id!!', $object->get_expl_id(), $value);
		$value = str_replace('!!classement!!', $object->get_classement(), $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('periode');
		$this->add_column('serial');
		$this->add_column('bulletin_numero');
		$this->add_column('abonnement');
		$this->add_column('expl_cb');
		$this->add_column('destinataire');
		$this->add_column('actions');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		if(empty($this->available_filters['main_fields']) && empty($this->available_filters['custom_fields'])) {
			$this->set_setting_display('search_form', 'visible', false);
		}
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'sorts', false);
		$this->set_setting_display('grouped_objects', 'display_counter', true);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_filter('abts_abts', 'selection_type', 'selector');
		$this->set_setting_filter('serials', 'selection_type', 'selector');
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_selection_actions('repair_diffusion', 'visible', false);
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'destinataire', 'actions'
		);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'abts_abts' => 'abts_onglet_abt',
						'serials' => 'serials_query',
// 						'no_ret' => 'serialcirc_no_ret_circ',
				)
		);
		if($pmb_lecteurs_localises){
			$this->available_filters['main_fields']['location'] = 'serialcirc_circ_list_location_title';
		}
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises, $deflt_docs_location;
		
		$this->filters = array(
				'location' => ($pmb_lecteurs_localises ? $deflt_docs_location : 0),
				'abts_abts' => array(),
				'serials' => array(),
				'no_ret' => -1,
				'point_expl_id' => 0
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'classement');
	}
	
	protected function init_default_selected_filters() {
		global $pmb_lecteurs_localises;
		
		if($pmb_lecteurs_localises){
			$this->add_selected_filter('location');
		}
		$this->add_selected_filter('abts_abts');
		$this->add_selected_filter('serials');
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$print_link = array(
				'onClick' => "my_serialcirc_print_all_sel_list_diff",
				'confirm' => '',
				'classements' => 'to_be_circ|in_circ|no_ret_circ'
		);
		$this->add_selection_action('print', $msg['serialcirc_circ_list_imprimer_bt'], '', $print_link);
		
		$comeback_link = array(
				'onClick' => "my_serialcirc_comeback_multiple_expl",
				'confirm' => '',
				'classements' => 'in_circ'
		);
		$this->add_selection_action('comeback', $msg['serialcirc_circ_list_bull_circulation_comeback_multiple_bt'], '', $comeback_link);
		
		$delete_circ_link = array(
				'onClick' => "my_serialcirc_delete_circ_multiple_expl",
				'confirm' => '',
				'classements' => 'no_ret_circ'
		);
		$this->add_selection_action('delete_circ', $msg['serialcirc_circ_list_bull_circulation_delete_circ_multiple_bt'], '', $delete_circ_link);
		$repair_link = array(
				'href' => static::get_controller_url_base()."&action=list_repair_diffusion",
				'confirm' => '',
				'classements' => 'no_ret_circ'
		);
		$this->add_selection_action('repair_diffusion', $msg['serialcirc_circ_list_repair_bt'], '', $repair_link);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('location', 'integer');
		$this->set_filter_from_form('abts_abts', 'integer');
		$this->set_filter_from_form('serials', 'integer');
		$this->set_filter_from_form('no_ret', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'locations':
				$query = 'SELECT distinct idlocation as id, location_libelle as label FROM docs_location, docsloc_section WHERE num_location=idlocation ORDER BY label';
				break;
			case 'abts_abts':
				$statuses = abts_status::get_ids_bulletinage_active();
				$query = 'SELECT abt_id as id, abt_name as label FROM abts_abts WHERE statut_id IN ('.implode(',', $statuses).') ORDER BY label';
				break;
			case 'serials':
				$query = 'SELECT distinct notice_id as id, tit1 as label FROM notices JOIN abts_abts ON num_notice = notice_id ORDER BY label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_location() {
		global $msg;
		return $this->get_search_filter_simple_selection($this->get_selection_query('locations'), 'location', $msg["all_location"]);
	}
	
	protected function get_search_filter_abts_abts() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('abts_abts'), 'abts_abts', $msg["all"]);
	}
	
	protected function get_search_filter_serials() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('serials'), 'serials', $msg["all"]);
	}
	
	protected function get_search_filter_no_ret() {
		global $msg;
		
		$options = array(
				-1 => $msg['all'],
				0 => $msg['40'],
				1 => $msg['39'],
		);
		return $this->get_search_filter_simple_selection('', 'no_ret', '', $options);
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('location', 'expl_location', 'integer');
		$this->_add_query_filter_multiple_restriction('abts_abts', 'abts_abts.abt_id', 'integer');
		$this->_add_query_filter_multiple_restriction('serials', 'bulletin_notice', 'integer');
		if($this->filters['no_ret'] !== -1) {
			$this->query_filters [] = 'serialcirc_no_ret = "'.$this->filters['no_ret'].'"';
		}
		$this->_add_query_filter_simple_restriction('point_expl_id', 'expl_id', 'integer');
	}
	
	protected function _get_object_property_classement($object) {
		global $msg;
		
		switch ($object->get_classement()) {
			case 'alert':
				return $msg["serialcirc_circ_list_bull_alerter"];
			case 'to_be_circ':
				return $msg["serialcirc_circ_list_bull_circuler"];
			case 'in_circ':
				return $msg["serialcirc_circ_list_bull_circulation"];
			case 'in_late':
				return $msg["serialcirc_circ_list_bull_retards"];
			case 'no_ret_circ':
				return $msg['serialcirc_no_ret_circ'];
		}
		return $object->get_classement();
	}
	
	protected function _get_object_property_periode($object) {
		return $object->get_mention_date();
	}
	
	protected function _get_object_property_serial($object) {
		return serial::get_notice_title($object->get_bulletin_notice());
	}
	
	protected function _get_object_property_bulletin_numero($object) {
		return $object->get_bulletin_numero();
	}
	
	protected function _get_object_property_abonnement($object) {
		return $object->get_abt_name();
	}
	
	protected function _get_object_property_expl_cb($object) {
		return $object->get_exemplaire()->cb;
	}
	
	protected function _get_object_property_actions_button($label, $function_name, $object) {
		global $charset;
		
		return "<input type=\"button\" class='bouton' value='".htmlentities($label, ENT_QUOTES, $charset)."' onClick=\"".$function_name."('".$object->get_classement()."','".$object->get_expl_id()."'); return false;\"/>&nbsp;";
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'serial':
				$serial = $this->_get_object_property_serial($object);
				$content .= "<a href='".serial::get_permalink($object->get_bulletin_notice())."'>". htmlentities($serial,ENT_QUOTES,$charset)."</a>";
				break;
			case 'bulletin_numero':
				$bulletin_numero = $this->_get_object_property_bulletin_numero($object);
				$content .= "<a href='".bulletinage::get_permalink($object->get_bulletin_id())."'>". htmlentities($bulletin_numero,ENT_QUOTES,$charset)."</a>";
				break;
			case 'expl_cb':
				$expl_cb = $this->_get_object_property_expl_cb($object);
				$content .= exemplaire::get_cb_link($expl_cb);
				break;
			case 'abonnement':
			    $abonnement = $this->_get_object_property_abonnement($object);
			    if (!empty($abonnement)) {
				    $content .= "<a href='./catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$object->get_abt_num()."'>".htmlentities($abonnement,ENT_QUOTES,$charset)."</a>";
			    }
				break;
			case 'destinataire':
			    $content .= "<label class='visually-hidden' for='".$object->get_classement()."_group_circ_select_".$object->get_expl_id()."'>
                    ".htmlentities($msg['serialcirc_circ_list_bull_circulation_destinataire']." ".$msg['serialcirc_circ_list_bull_circulation_cb'].$this->_get_object_property_expl_cb($object), ENT_QUOTES, $charset)."
                </label>";
			    $content .= $object->build_diff_sel();
				switch ($object->get_classement()) {
					case 'alert':
						$content .= "<div id='circ_actions_alert_".$object->get_expl_id()."' class='erreur'></div>";
						break;
					case 'to_be_circ':
						break;
					case 'in_circ':
						if($content != "") {
						    $content .= get_expandBase_button('circ_detail_in_circ_'.$object->get_expl_id());
							$content .= "<div id='circ_detail_in_circ_".$object->get_expl_id()."Child' style='display:none;'>";
							$content .= $object->build_empr_list();
							$content .= "</div>";
						}
						break;
					case 'in_late':
						if($content != "") {
						    $content .= get_expandBase_button('circ_detail_in_late_'.$object->get_expl_id());
							$content .= "<div id='circ_detail_in_late_".$object->get_expl_id()."Child' style='display:none;'>";
							$content .= $object->build_empr_list();
							$content .= "</div>";
						}
						break;
				}
				break;
			case 'actions':
				switch ($object->get_classement()) {
					case 'alert':
						$content .= "
							<input type=\"button\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_bull_ajouter_sommaire_bt"],ENT_QUOTES,$charset)."' onClick=\"my_serialcirc_circ_list_bull_ajouter_sommaire('alert','".$object->get_bulletin_id()."'); return false;\"/>&nbsp;
						";
						if(!$object->is_alerted()) {
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_envoyer_alert_bt"], "my_serialcirc_circ_list_bull_envoyer_alert", $object);
						}
						break;
					case 'to_be_circ':
						$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_imprimer_bt"], "my_serialcirc_print_list_circ",  $object);
						$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_annuler_bt"], "my_serialcirc_delete_circ", $object);
						break;
					case 'in_circ':
						if ($object->get_serialcirc_checked()) {
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_call_bt"], "my_serialcirc_call_expl", $object);
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_go_bt"], "my_serialcirc_do_trans", $object);
						}
						$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_annuler_bt"], "my_serialcirc_delete_circ", $object);
						$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_comeback_bt"], "my_serialcirc_comeback_expl", $object);
						$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_imprimer_bt"], "my_serialcirc_print_list_circ",  $object);
						$content .= "<div id='circ_actions_in_circ_".$object->get_expl_id()."' class='erreur'></div>";
						break;
					case 'in_late':
						$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_call_bt"], "my_serialcirc_call_expl", $object);
						if($object->get_serialcirc_type() == SERIALCIRC_TYPE_rotative){
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_go_bt"], "my_serialcirc_do_trans", $object);
						}else{
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_go_return_bt"], "my_serialcirc_callinsist_expl", $object);
						}
						$content .= "<div id='circ_actions_in_late_".$object->get_expl_id()."' class='erreur'></div>";
						break;
					case 'no_ret_circ':
						$content .= $this->_get_object_property_actions_button($msg["63"], "my_serialcirc_delete_circ", $object);
						if($object->is_lost_num_serialcirc_abt()) {
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_repair_bt"], "my_serialcirc_repair_list_circ", $object);
						} else {
							$content .= $this->_get_object_property_actions_button($msg["serialcirc_circ_list_bull_circulation_imprimer_bt"], "my_serialcirc_print_list_circ",  $object);
						}
						break;
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function is_used_aria_labelledby($property, $matches = []) {
	    return parent::is_used_aria_labelledby($property, ['expl_cb']);
	}
	
	protected function _get_query_human_location() {
		if($this->filters['location']) {
			$docs_location = new docs_location($this->filters['location']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_abts_abts() {
		if(!empty($this->filters['abts_abts'])) {
			$labels = array();
			foreach ($this->filters['abts_abts'] as $abt_id) {
				$abts_abonnement = new abts_abonnement($abt_id);
				$labels[] = $abts_abonnement->abt_name;
			}
			return implode(', ', $labels);
		}
		return '';
	}
	
	protected function _get_query_human_serials() {
		if(!empty($this->filters['serials'])) {
			$labels = array();
			foreach ($this->filters['serials'] as $serial_id) {
				$labels[] = notice::get_notice_title($serial_id);
			}
			return implode(', ', $labels);
		}
	}
	
	protected function _get_query_human_no_ret() {
		global $msg;
		if($this->filters['no_ret'] !== -1) {
			if($this->filters['no_ret']) {
				$msg['39'];
			} else {
				$msg['40'];
			}
		}
		return '';
	}
	
	protected function get_js_actions_script() {
		global $msg;
		
		$js_script = "
			<script type='text/javascript' src='./javascript/serialcirc.js'></script>
			<script type='text/javascript'>
				
				function my_serialcirc_circ_list_bull_ajouter_sommaire(zone,bull_id){
					var url = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id='+bull_id;
					window.open(url,'blank');
				}
				
				function my_serialcirc_circ_list_bull_envoyer_alert	(zone,expl_id){
					if (confirm('".addslashes($msg["serialcirc_envoyer_alert"])."')){
						serialcirc_circ_list_bull_envoyer_alert(expl_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + expl_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_alert"])."';
					}else{
								
					}
				}
								
				function my_serialcirc_print_list_circ(zone,expl_id){
					var start_diff_id=0;
					if(document.getElementById(zone + '_group_circ_select_' + expl_id)) {
						var  obj=document.getElementById(zone + '_group_circ_select_' + expl_id);
						for(var i=0 ; i<obj.options.length; i++){
							if(obj.options[i].selected){
								start_diff_id=obj.options[i].value;
							}
						}
					}
					serialcirc_print_list_circ(expl_id,start_diff_id);
				}
					
				function my_serialcirc_repair_list_circ(zone,expl_id){
					serialcirc_repair_list_circ(expl_id);
				}
			
				function my_serialcirc_comeback_expl(zone,expl_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_retour"])."')){
						var info = serialcirc_comeback_expl(expl_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + expl_id);
						if(obj) obj.innerHTML=info;
					}else{
							
					}
				}
							
				function my_serialcirc_comeback_multiple_expl(selection){
					if (selection.length > 0) {
						if (confirm('".addslashes($msg["serialcirc_multiple_expl_confirm_retour"])."')){
							if(document.getElementById('".$this->objects_type."_selection_action_comeback_link')) {
								document.getElementById('".$this->objects_type."_selection_action_comeback_link').disabled=true;
							}
							for (var i = 0; i < selection.length; i++) {
								var info = serialcirc_comeback_expl(selection[i]);
								var obj=document.getElementById('circ_actions_in_circ_' + selection[i]);
								if(obj){
									obj.innerHTML=info;
								}
							}
							if(document.getElementById('".$this->objects_type."_selection_action_comeback_link')) {
								document.getElementById('".$this->objects_type."_selection_action_comeback_link').disabled=false;
							}
						}else{
										
						}
					} else {
						alert('".addslashes($msg["serialcirc_multiple_expl_retour_no_selected"])."');
					}
				}
								
				function my_serialcirc_delete_circ_multiple_expl(selection){
					if (selection.length > 0) {
						if (confirm('".addslashes($msg["serialcirc_multiple_expl_confirm_delete_circ"])."')){
							if(document.getElementById('".$this->objects_type."_selection_action_delete_circ_link')) {
								document.getElementById('".$this->objects_type."_selection_action_delete_circ_link').disabled=true;
							}
							for (var i = 0; i < selection.length; i++) {
								var info = serialcirc_delete_circ(selection[i]);
								var obj=document.getElementById('circ_actions_no_ret_circ_' + selection[i]);
								if(obj){
									obj.innerHTML=info;
								}
							}
							if(document.getElementById('".$this->objects_type."_selection_action_delete_circ_link')) {
								document.getElementById('".$this->objects_type."_selection_action_delete_circ_link').disabled=false;
							}
						}else{
										
						}
					} else {
						alert('".addslashes($msg["serialcirc_multiple_expl_delete_circ_no_selected"])."');
					}
				}
								
				function my_serialcirc_call_expl(zone,expl_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_call_expl"])."')){
						serialcirc_call_expl(expl_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + expl_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_call_expl"])."';
					}else{
								
					}
				}
								
				function my_serialcirc_do_trans(zone,expl_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_do_trans"])."')){
						serialcirc_do_trans(expl_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + expl_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_do_trans"])."';
					}else{
								
					}
				}
								
				function my_serialcirc_delete_circ(zone,expl_id){
								
					if (confirm('".addslashes($msg["serialcirc_confirm_delete"])."')){
						serialcirc_delete_circ(expl_id);
						if(document.getElementById('tr_'+zone+'_'+expl_id)) {
							document.getElementById('tr_'+zone+'_'+expl_id).parentNode.removeChild(document.getElementById('tr_'+zone+'_'+expl_id));
						}
					}else{
							
					}
				}
							
				function my_serialcirc_callinsist_expl(zone,expl_id){
					serialcirc_callinsist_expl(expl_id);
				}
							
				function my_serialcirc_copy_accept(zone,copy_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_copy"])."')){
						serialcirc_copy_accept(copy_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + copy_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_copy"])."';
					}
				}
								
				function my_serialcirc_copy_none(zone,copy_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_copy_none"])."')){
						serialcirc_copy_none(copy_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + copy_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_copy_none"])."';
					}
				}
								
				function my_serialcirc_resa_accept(zone,expl_id,empr_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_resa"])."')){
						serialcirc_resa_accept(expl_id,empr_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + expl_id+ '_' + empr_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_resa"])."';
					}
				}
								
				function my_serialcirc_resa_none(zone,expl_id,empr_id){
					if (confirm('".addslashes($msg["serialcirc_confirm_resa_none"])."')){
						serialcirc_resa_none(expl_id,empr_id);
						var  obj=document.getElementById('circ_actions_'+ zone + '_' + expl_id+ '_' + empr_id);
						if(obj) obj.innerHTML='".addslashes($msg["serialcirc_info_resa_none"])."';
					}
				}
								
				function my_serialcirc_print_all_sel_list_diff(selection){
					var expl_start_empr= new Array();
					if(!selection.length) return;
					var cpt=0;
					for(var i=0 ; i<selection.length ; i++){
						var expl_id=selection[i];
						if(document.getElementById('to_be_circ_group_circ_select_' + expl_id)) {
							var start_diff_id=document.getElementById('to_be_circ_group_circ_select_' + expl_id).value;
						} else {
							var start_diff_id=0;
						}
						expl_start_empr[cpt]= new Array();
						expl_start_empr[cpt]['expl_id']=expl_id;
						expl_start_empr[cpt]['start_diff_id']=start_diff_id;
						cpt++;
					}
					if(cpt>0) serialcirc_print_all_sel_list_diff(expl_start_empr);
				}
			</script>
			<script type='text/javascript' src='./javascript/tablist.js'></script>
		";
		return $js_script;
	}
	
	/**
	 * Affichage de la liste des objets
	 * @return string
	 */
	public function get_display_objects_list() {
		$display = parent::get_display_objects_list();
		if(static::class != 'list_serialcirc_expl_pointage_ui') {
			$display .= list_serialcirc_copy_ui::get_instance()->get_display_objects_list();
			$display .= list_serialcirc_circ_ui::get_instance(array('hold_asked' => 1))->get_display_objects_list();
		}
		return $display;
	}
	
	public function get_display_list() {
		global $msg, $charset;
		
		$display = parent::get_display_list();
		$display .= "
			<br />
			<div class='row'>
				<input type=\"submit\" class='bouton' value='".htmlentities($msg["serialcirc_circ_list_actualiser_bt"],ENT_QUOTES,$charset)."' onClick=\"document.location='".static::get_controller_url_base()."'; return false;\"/>&nbsp;
			</div>";
		$display .= $this->get_js_actions_script();
		return $display;
	}
	
	protected function add_event_on_selection_action($action=array()) {
		$display = "
			on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function(event) {
				var selection = new Array();
				query('.".$this->objects_type."_selection:checked').forEach(function(node) {
					".(isset($action['link']['classements']) && $action['link']['classements'] ? "
						var classements = '".$action['link']['classements']."';
					" : "
						var classements = '';
					")."
					if(classements && classements.includes(node.getAttribute('data-classement'))) {
						selection.push(node.value);
					}
				});
				if(selection.length) {
					var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
					if(!confirm_msg || confirm(confirm_msg)) {
						".(isset($action['link']['href']) && $action['link']['href'] ? "
							var selected_objects_form = domConstruct.create('form', {
								action : '".$action['link']['href']."',
								name : '".$this->objects_type."_selected_objects_form',
								id : '".$this->objects_type."_selected_objects_form',
								method : 'POST'
							});
							selection.forEach(function(selected_option) {
								var selected_objects_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->get_name_selected_objects()."[]',
									value : selected_option
								});
								domConstruct.place(selected_objects_hidden, selected_objects_form);
							});
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							domConstruct.destroy(dom.byId('".$this->objects_type."_selected_objects_form'));
							"
								: "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
						".(isset($action['link']['showConfiguration']) && $action['link']['showConfiguration'] ? $this->objects_type."_show_configuration('".$action['name']."'); event.preventDefault(); return false;" : "")."
					}
				} else {
					alert('".addslashes($this->get_error_message_empty_selection($action))."');
					event.preventDefault();
					return false;
				}
			});
		";
		return $display;
	}
	
	public static function repair_diffusion() {
		global $location_id, $serialcirc_expl_ui_location;
		
		
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			if(empty($location_id) && !empty($serialcirc_expl_ui_location)) {
				$location_id = $serialcirc_expl_ui_location;
			}
			foreach ($selected_objects as $id) {
				$serialcirc = new serialcirc($location_id);
				$serialcirc->repair_diffusion($id);
			}
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/circ.php?categ=serialcirc';
	}
}
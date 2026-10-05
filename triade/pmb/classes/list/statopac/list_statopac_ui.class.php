<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_statopac_ui.class.php,v 1.22.4.1 2025/02/21 13:24:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_statopac_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT statopac_request.idproc as id, statopac_request.*, 
			statopac_vues.id_vue, statopac_vues.date_consolidation, statopac_vues.nom_vue, statopac_vues.comment as comment_vue, statopac_vues.date_debut_log, statopac_vues.date_fin_log  
			FROM statopac_vues
			LEFT JOIN statopac_request ON  statopac_request.num_vue = statopac_vues.id_vue';
		return $query;
	}
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if ($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE) {
			$this->objects[] = $row;
		}
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'nom_vue':
	            return 'nom_vue,name';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	public function get_display_search_form() {
		if(static::class == 'list_statopac_ui') {
			//Ne pas retourner le formulaire car non compatible avec le formulaire d'encapsulation
			return '';
		} else {
			return parent::get_display_search_form();
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['expanded_display'] = 0;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'execute', 'name', 'configuration', 'export'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('nom_vue');
	    $this->add_applied_sort('name');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'nom_vue');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'name' => '705',
						'autorisations' => '25',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'name' => '',
				'autorisations' => array()
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('name');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function get_search_filter_autorisations() {
		//TODO
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => '705',
						'comment' => '707',
						'configuration' => '1600'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_execute();
		$this->add_column('name');
		$this->add_column('configuration');
		$this->add_column_export();
	}
	
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id_vue!!', $object->id_vue, $value);
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function add_column_execute() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['708'],
				'link' => static::get_controller_url_base().'&section=view_list&act=exec_req&id_req=!!id!!&id_view=!!id_vue!!'
		);
		$this->add_column_simple_action('execute', '', $html_properties);
	}
	
	protected function add_column_export() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['procs_bt_export'],
				'link' => './export.php?quoi=stat&act=save_req&id_req=!!id!!&id_view=!!id_vue!!'
		);
		$this->add_column_simple_action('export', '', $html_properties);
	}
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_interface_button($msg['stat_add_view'], ['location' => static::get_controller_url_base()."&section=view_gestion&act=add_view"]);
	}
	
	protected function get_buttons_list() {
		global $base_path, $msg;
		return "
		<div class='row'>
			<div class='left'>
				".$this->get_button_add()."
				<input class='bouton' type='submit' value=\"".$msg['stat_consolide_view']."\" onClick=\"document.view.action='".static::get_controller_url_base()."&section=view_list&act=consolide_view'\"/>
			</div>
			<div class='right'>
				<a href='".$base_path."/includes/interpreter/doc?group=consolidation' target='_blank'>".$msg['interpreter_doc_consolidation_link']."</a>
			</div>
		</div>";
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg, $charset;
		
		$grouped_label = parent::get_grouped_label($object, $property);
		if(static::class == 'list_statopac_ui') {
			$view_scope = htmlentities($msg['stat_no_scope'],ENT_QUOTES,$charset);
			$min_date=$object->date_debut_log;
			$max_date=$object->date_fin_log;
			if ($min_date!='0000-00-00 00:00:00' && $max_date!='0000-00-00 00:00:00') {
				$view_scope = sprintf(htmlentities($msg['stat_view_scope'],ENT_QUOTES,$charset),formatdate($min_date),formatdate($max_date));
			}
			$lien = "<a href='".static::get_controller_url_base()."&section=view_gestion&act=update_view&id_view=".$object->id_vue."'>".htmlentities($grouped_label,ENT_QUOTES, $charset)."</a>";
			$space = "<small><span style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' style='width:10px; height:10px' alt='' /></span></small>";
			$checkbox = "<input type='checkbox' class='checkbox' id='box".$object->id_vue."' name='list_ck[]' value='".$object->id_vue."' />";
			$date_conso='';
			if ($object->date_consolidation!=='0000-00-00 00:00:00') {
				$date_conso = sprintf($msg['stat_view_date_conso'],formatdate($object->date_consolidation,true),$view_scope);
			}
			$grouped_label = $space.$checkbox.$space.$lien.$space.$date_conso;
		}
		return $grouped_label;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= "<strong>".$object->name."</strong><br />
					<small>".$object->comment."</small>";
				break;
			case 'configuration':
				$query_parameters = array();
				if (preg_match_all("|!!(.*)!!|U",$object->requete,$query_parameters)) {
					$content .= "<a href='".static::get_controller_url_base()."&section=view_list&act=configure&id_req=".$object->idproc."'>".$msg["procs_options_config_param"]."</a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_default_attributes_format_cell($object, $property) {
		switch ($property) {
			case 'name':
				return array(
						'onclick' => "document.location=\"".static::get_controller_url_base()."&section=query&act=update_request&id_req=".$object->idproc."&id_view=".$object->id_vue."\""
				);
			default:
				return array();
		}
	}
	
	protected function gen_plus($id, $titre, $contenu, $maximise=0) {
		global $msg, $charset, $open_view;
		
		$cleaned_title = strip_tags(html_entity_decode($titre, ENT_QUOTES, $charset));
		if(strpos($cleaned_title, "(") !== false) {
		  $cleaned_title = substr($cleaned_title, 0, strpos($cleaned_title, "("));
		}
		$cleaned_title = trim($cleaned_title);
		$id_view = stat_view::get_id_from_label($cleaned_title);
		if(static::class == 'list_statopac_ui') {
			$contenu .= "
			<div class='row'>
                ".$this->get_interface_button_small($msg['stat_add_request'], ['location' => static::get_controller_url_base()."&section=query&act=update_request&id_view=".$id_view])."
                ".$this->get_interface_button_small($msg['stat_imp_request'], ['location' => static::get_controller_url_base()."&section=import&id_view=".$id_view])."
			</div>
			";
		}
		$open_view = intval($open_view);
		if ($open_view == $id_view) {
			$maximise = 1;
		}
		return parent::gen_plus($id, $titre, $contenu, $maximise);
	}
	
	//Liste des options de consolidation
	protected function get_options_consolidation() {
		global $msg, $charset;
		
		$space = "<small><span style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' style='width:10px' height='10' alt='' /></span></small>";
		
		$min_date='';
		$max_date='';
		$stat_scope = htmlentities($msg['stat_no_scope'],ENT_QUOTES,$charset);
		$q_sc = 'select min(date_log) as min_date, max(date_log) as max_date from statopac';
		$r_sc = pmb_mysql_query($q_sc);
		if ($r_sc && pmb_mysql_num_rows($r_sc)) {
			$res_sc=pmb_mysql_fetch_object($r_sc);
			$min_date=$res_sc->min_date;
			$max_date=$res_sc->max_date;
			if ($min_date!='0000-00-00 00:00:00' && $max_date!='0000-00-00 00:00:00') {
				$stat_scope = sprintf(htmlentities($msg['stat_scope'],ENT_QUOTES,$charset),formatdate($min_date),formatdate($max_date));
			}
		}
		$options = "<div id='opt_consoParent' class='notice-parent'>";
		$options .= get_expandBase_button('opt_conso');
		$options .= "$space <span class='notice-heada'>".htmlentities($msg['stat_options_consolidation'],ENT_QUOTES,$charset)."</span>";
		$options .= "$space $stat_scope";
		$options .= "</div>";
		$options_contenu ="<div class='row'>
					<input type='radio' class='radio' id='id_lot' name='conso' value='1' checked='checked' onClick=\"document.getElementById('remove_data').checked=false;\" />
						<label for='id_lot'>$msg[stat_last_consolidation]</label><br /><br />
					<input type='radio' class='radio' id='id_interval' name='conso' value='2' onClick=\"document.getElementById('remove_data').checked=false;\" />
						<label for='id_interval'>$msg[stat_interval_consolidation] </label><br /><br />
					<input type='radio' class='radio' id='id_debut' name='conso' value='3' onClick=\"document.getElementById('remove_data').checked=false;\" />
						<label for='id_debut'>$msg[stat_echeance_consolidation]</label><br /><br />
					<input type='checkbox' name='remove_data' id='remove_data' value='1'/>
						<label for='remove_data'>$msg[stat_remove_data]</label><br /><br />
					<input type='checkbox' name='remove_data_interval' id='remove_data_interval' value='1'/>
						<label for='remove_data_interval'>$msg[stat_remove_data_interval]</label><br />
					</div>
			";
		$options.="<div id='opt_consoChild' class='notice-child' style='margin-bottom: 6px; display: none;'>$options_contenu</div>";
		
		$btn_date_deb = "<input type='date' name='date_deb' value='!!date_deb!!'/>";
		$btn_date_fin = "<input type='date' name='date_fin' value='!!date_fin!!'/>";
		$btn_date_echeance = "<input type='date' name='date_ech' value='!!date_ech!!'/>";
		$btn_remove_data_interval_date_deb = "<input type='date' name='remove_data_interval_date_deb' value=''/>";
		$btn_remove_data_interval_date_fin = "<input type='date' name='remove_data_interval_date_fin' value=''/>";
		
		$date_debut = strftime("%Y-%m-%d", mktime(0, 0, 0, date('m'), date('d')-1, date('y')));
		$btn_date_deb=str_replace("!!date_deb!!",$date_debut,$btn_date_deb);
		$btn_date_deb=str_replace("!!date_deb_lib!!",formatdate($date_debut),$btn_date_deb);
		$date_fin = today();
		$btn_date_fin=str_replace("!!date_fin!!",$date_fin,$btn_date_fin);
		$btn_date_fin=str_replace("!!date_fin_lib!!",formatdate($date_fin),$btn_date_fin);
		$date_echeance = today();
		$btn_date_echeance=str_replace("!!date_ech!!",$date_echeance,$btn_date_echeance);
		$btn_date_echeance=str_replace("!!date_ech_lib!!",formatdate($date_echeance),$btn_date_echeance);
		$options=str_replace("!!date_deb_btn!!",$btn_date_deb,$options);
		$options=str_replace("!!date_fin_btn!!",$btn_date_fin,$options);
		$options=str_replace("!!echeance_btn!!",$btn_date_echeance,$options);
		$options=str_replace("!!remove_data_interval_date_deb!!",$btn_remove_data_interval_date_deb,$options);
		$options=str_replace("!!remove_data_interval_date_fin!!",$btn_remove_data_interval_date_fin,$options);
		
		return $options;
	}
	
	protected function get_display_content_object_list($object, $indice) {
	    if(empty($object->id)) {
	        return '';
	    }
	    return parent::get_display_content_object_list($object, $indice);
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg;
		
		if(static::class == 'list_statopac_ui') {
			$requete_vue = "select * from statopac_vues order by date_consolidation desc, nom_vue";
			$res = pmb_mysql_query($requete_vue);
			if(pmb_mysql_num_rows($res) == 0){
				$options_consolidation = '';
			} else {
				$options_consolidation = $this->get_options_consolidation();
			}
			$display = "
			<form class='form_view' id='view' name='view' method='post' action='".static::get_controller_url_base()."&section=view_gestion' >
				<h3>$msg[stat_view_list]</h3>
				<div class='form-contenu'>
					".parent::get_display_list()."
					<br />
					".$options_consolidation."
					<br />
					".$this->get_buttons_list()."
					<br />
				</div>
			</form>";
		} else {
			$display = parent::get_display_list();
		}
		return $display;
	}
	
	protected function get_error_message_empty_selection($action=array()) {
		global $msg;
		return $msg['stat_no_view_created'];
	}
}
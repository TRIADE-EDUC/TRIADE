<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_demandes_actions_ui.class.php,v 1.11.4.1 2025/05/06 15:31:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_demandes_actions_ui extends list_demandes_ui {
	
	protected function _get_query_base() {
		$query = 'select id_action from demandes
			join demandes_actions on num_demande=id_demande
			join demandes_users du on du.num_demande=id_demande 	
				';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new demandes_actions($row->id_action);
	}
		
	protected function get_title() {
		global $sub, $msg, $charset;
		
		switch ($sub) {
			case 'com':
				return "<h3>".htmlentities($msg['demandes_action_com'], ENT_QUOTES, $charset)."</h3><br />";
			case 'rdv_plan':
				return "<h3>".htmlentities($msg['demandes_menu_rdv_planning'], ENT_QUOTES, $charset)."</h3><br />";
			case 'rdv_val':
				return "<h3>".htmlentities($msg["demandes_menu_rdv_a_valide"], ENT_QUOTES, $charset)."</h3><br />";
		}
	}
	
	protected function get_form_title() {
		global $sub, $msg;
		
		switch ($sub) {
			case 'com':
				return $msg['demandes_action_com'];
			case 'rdv_plan':
				return $msg['demandes_menu_rdv_planning'];
			case 'rdv_val':
				return $msg["demandes_menu_rdv_a_valide"];
		}
	}
	
	protected function init_default_selected_filters() {
		$this->selected_filters = array();
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('id_action');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'titre_demande');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $msg;
	
		parent::init_available_columns();
		$this->available_columns['main_fields']['properties_action'] = '';
		$this->available_columns['main_fields']['type_action'] = 'demandes_action_type';
		$this->available_columns['main_fields']['sujet_action'] = 'demandes_action_sujet';
		$this->available_columns['main_fields']['detail_action'] = 'demandes_action_detail';
		$this->available_columns['main_fields']['statut_action'] = 'demandes_action_statut';
		$this->available_columns['main_fields']['date_action'] = 'demandes_action_date';
		$this->available_columns['main_fields']['deadline_action'] = 'demandes_action_date_butoir';
		$this->available_columns['main_fields']['creator'] = 'demandes_action_createur';
		$this->available_columns['main_fields']['time_elapsed'] = $msg['demandes_action_time_elapsed']." (".$msg['demandes_action_time_unit'].")";
		$this->available_columns['main_fields']['cout'] = 'demandes_action_cout';
		$this->available_columns['main_fields']['progression_action'] = 'demandes_action_progression';
		$this->available_columns['main_fields']['notes'] = 'demandes_action_nbnotes';
	}
	
	protected function init_default_columns() {
	    $this->add_column_selection();
		$this->add_column('sujet_action');
		$this->add_column('detail_action');
		$this->add_column('date_action');
		$this->add_column('time_elapsed');
		$this->add_column('progression_action');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('date_demande', 'datatype', 'date');
		$this->set_setting_column('date_prevue', 'datatype', 'date');
		$this->set_setting_column('deadline_demande', 'datatype', 'date');
		$this->set_setting_column('date_action', 'datatype', 'date');
		$this->set_setting_column('deadline_action', 'datatype', 'date');
		$this->set_setting_column('notes', 'datatype', 'integer');
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	protected function get_message_for_selection() {
		return list_ui::get_message_for_selection();
	}
	
	public function get_error_message_empty_list() {
		global $sub, $msg, $charset;
		
		switch ($sub) {
			case 'com':
				return htmlentities($msg['demandes_no_com'], ENT_QUOTES, $charset);
			case 'rdv_plan':
				return htmlentities($msg["demandes_no_rdv_plan"], ENT_QUOTES, $charset);
			case 'rdv_val':
				return htmlentities($msg["demandes_no_rdv_val"], ENT_QUOTES, $charset);
		}
	}
	
	protected function get_selection_actions() {
		global $sub, $msg;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			switch ($sub) {
				case 'com':
					$close_fil_link = array(
							'href' => static::get_controller_url_base()."&act=close_fil",
					);
					$this->selection_actions[] = $this->get_selection_action('close_fil', $msg['demandes_action_close_fil'], '', $close_fil_link);
					break;
				case 'rdv_plan':
					$close_rdv_link = array(
							'href' => static::get_controller_url_base()."&act=close_rdv",
					);
					$this->selection_actions[] = $this->get_selection_action('close_rdv', $msg['demandes_action_close_rdv'], '', $close_rdv_link);
					break;
				case 'rdv_val':
					$val_rdv_link = array(
							'href' => static::get_controller_url_base()."&act=val_rdv",
					);
					$this->selection_actions[] = $this->get_selection_action('val_rdv', $msg['demandes_action_valid_rdv'], '', $val_rdv_link);
					break;
			}
		}
		return $this->selection_actions;
	}
	
	protected function get_display_others_actions() {
		return "";
	}
	
	protected function get_grouped_label($object, $property) {
		$grouped_label = "<a onclick=\"document.location='./demandes.php?categ=gestion&act=see_dmde&iddemande=".$object->get_demande()->id_demande."'\" style='cursor:pointer;'>";
		$grouped_label .= parent::get_grouped_label($object, $property);
		$grouped_label .= "</a>";
		return $grouped_label;
	}
	
	protected function _get_object_property_titre_demande($object) {
		return $object->get_demande()->titre_demande;
	}
	
	protected function _get_object_property_theme_demande($object) {
		return $this->get_themes()->getLabel($object->get_demande()->theme_demande);
	}
	
	protected function _get_object_property_type_demande($object) {
		return $this->get_types()->getLabel($object->get_demande()->type_demande);
	}
	
	protected function _get_object_property_etat_demande($object) {
		return $object->get_demande()->workflow->getStateCommentById($object->etat_demande);
	}
	
	protected function _get_object_property_demandeur($object) {
		return emprunteur::get_name($object->get_demande()->num_demandeur, 1);
	}
	
	protected function _get_object_property_date_demande($object) {
		return $object->get_demande()->date_demande;
	}
	
	protected function _get_object_property_date_prevue($object) {
		return $object->get_demande()->date_prevue;
	}
	
	protected function _get_object_property_deadline_demande($object) {
		return $object->get_demande()->deadline_demande;
	}
	
	protected function _get_object_property_type_action($object) {
		return $object->workflow->getTypeCommentById($object->type_action);
	}
	
	protected function _get_object_property_statut_action($object) {
		return $object->workflow->getStateCommentById($object->statut_action);
	}
	
	protected function _get_object_property_creator($object) {
		return $object->getCreateur($object->actions_num_user,$object->actions_type_user);
	}
	
	protected function _get_object_property_notes($object) {
		return count($object->notes);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset, $pmb_gestion_devise;
		
		$content = '';
		switch($property) {
			case 'properties_action':
				if($object->actions_read_gestion == 1){
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par //onclick=\"change_read_action('read".$action->id_action."','$action->id_action','$action->num_demande', true); return false;\"
					$content .= "<img title=\"\" id=\"read".$object->id_action."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' style='display:none'>
								<img title=\"" . htmlentities($msg['demandes_new'], ENT_QUOTES, $charset). "\" id=\"read".$object->id_action."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."'>";
				} else {
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par onclick=\"change_read_action('read".$action->id_action."','$action->id_action','$action->num_demande', true); return false;\"
					$content .= "<img title=\"\" id=\"read".$object->id_action."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' >
								<img title=\"" . htmlentities($msg['demandes_new'], ENT_QUOTES, $charset). "\" id=\"read".$object->id_action."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."' style='display:none'>";
				}
				break;
			case 'time_elapsed':
			    $content .= $object->time_elapsed.$msg['demandes_action_time_unit'];
				break;
			case 'cout':
				$content .= $object->cout.$pmb_gestion_devise;
				break;
			case 'progression_action':
				$content .= "<img src='".get_url_icon('jauge.png')."' style='height:16px;' width=\"".$object->progression_action."%\" title='".$object->progression_action."%' alt='".$object->progression_action."%' />";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		$attributes['onclick'] = "window.location=\"./demandes.php?categ=action&act=see&idaction=".$object->id_action."#fin\"";
		return $attributes;
	}
}
<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_records_bulletins_collstate_edition_ui.class.php,v 1.13 2023/12/18 15:55:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_records_bulletins_collstate_edition_ui extends list_records_bulletins_ui {
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		parent::init_available_filters();
		$this->available_filters['main_fields']['user_query'] = '1914';
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('user_query');
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = 100;
	}
	
	protected function init_default_columns() {
		$this->add_column('empty', 'caddie_de_NOTI');
		$this->add_column('caddie');
		$this->add_column('bulletin_numero');
		$this->add_column('mention_date');
		$this->add_column('aff_date_date');
		$this->add_column('bulletin_titre');
		$this->add_column('expl');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'record_header');
	}
	
	protected function _get_query_field_order($sort_by) {
	    return " pert, index_sew, date_date DESC, bulletin_id DESC";
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('user_query');
		parent::set_filters_from_form();
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_display_search_form() {
		$this->is_displayed_add_filters_block = false;
		$display_search_form = parent::get_display_search_form();
		return $display_search_form;
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		global $msg, $charset;
		
		$display = '';
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			foreach($grouped_objects as $group_label=>$objects) {
				// lien d'ajout d'une notice mère à un caddie
				$cart_click_noti = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=".$objects[0]->bulletin_notice."', 'cart')\"";
				$url = "./catalog.php?categ=serials&sub=view&serial_id=".$objects[0]->bulletin_notice;
				$display .= "
					<tr>
						<td>
							<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".htmlentities($msg['400'], ENT_QUOTES, $charset)."' ".$cart_click_noti.">
						</td>
						<td class='list_ui_content_list_group ".$this->objects_type."_content_list_group' colspan='".(count($this->columns)-1)."'>
							<a href='".$url."'>
                                ".$group_label."
                            </a>
						</td>
					</tr>";
				foreach ($objects as $i=>$object) {
					$display .= $this->get_display_content_object_list($object, $i);
				}
			}
		} else {
			foreach ($this->objects as $i=>$object) {
				$display .= $this->get_display_content_object_list($object, $i);
			}
		}
		return $display;
	}
}
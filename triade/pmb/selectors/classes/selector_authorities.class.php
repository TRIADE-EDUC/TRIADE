<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_authorities.class.php,v 1.22.4.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector.class.php");
require_once($class_path."/searcher_selectors_tabs.class.php");
require_once($class_path."/search_perso.class.php");
require_once($class_path."/search_authorities.class.php");
require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/elements_list/elements_authorities_selectors_list_ui.class.php");

// require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

class selector_authorities extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		global $msg;
		global $action;
		global $form_display_mode;

		$entity_form = '';
		switch($action){
			case 'simple_search':
			    $entity_form = $this->get_simple_search_form();
				break;
			case 'advanced_search':
			    $entity_form = $this->get_advanced_search_form();
				break;
			case 'add':
			    $entity_form = '<div id="tab_container">';
				if($form_display_mode == 2) {
				    $entity_form.= $this->get_html_button($this->get_change_link(1), $msg['selector_toggle_simple_entry_data']);
					ob_start();
					$this->get_advanced_form();
					$entity_form.= ob_get_contents();
					ob_end_clean();

				} else {
				    $entity_form.= $this->get_html_button($this->get_change_link(2), $msg['selector_toggle_full_entry_data']);
				    $entity_form.= $this->get_form();
				}
				$entity_form.='</div>';
				break;
			case 'update':
			    ob_start();
			    if($form_display_mode == 2) {
					$saved_id = $this->get_advanced_save();
				} else {
					$saved_id = $this->save();
				}

// 				/**
// 				 * TODO -> retourner une structure json de l'objet que l'ont vient d'ajouter (isbd + id);
// 				 */
// 				print $this->get_search_form();
// 				print $this->get_js_script();
// 				if($saved_id) {
// 					print $this->get_display_object(0, $saved_id);
// 				}

				$entities_controller = $this->get_entities_controller_instance($saved_id);
				$instance = $entities_controller->get_object_instance();
				$html = ob_get_clean();
				print
				   '<textarea>'.encoding_normalize::json_encode(array(
						'id' => $saved_id,
				   		'id_authority' => $this->get_authority_instance(0, $saved_id)->get_id(),
						'isbd' => $instance->get_isbd(),
				   		'type' => 'authorities',
				        'html' => $html
				)).'</textarea>';
				break;
			case 'results_search':
				ob_start();
				print $this->results_search();
				$results_search = ob_get_contents();
				ob_end_clean();
				$entity_form = $results_search;
				break;
			case 'element_display':
				global $id_authority;
				$id_authority = intval($id_authority);
				if($id_authority) {
					$elements_authorities_selectors_list_ui = new elements_authorities_selectors_list_ui(array($id_authority), 1, 1);
					$elements = $elements_authorities_selectors_list_ui->get_elements_list();
					search_authorities::get_caddie_link();
					$entity_form = $elements;
				}
				break;
			default:
			    print $this->get_sel_header_template();
			    print $this->get_js_script();
			    print $this->get_sel_footer_template();
			    print $this->get_sub_tabs();
				break;
		}
		if ($entity_form) {
		    header("Content-Type: text/html; charset=UTF-8");
		    print encoding_normalize::utf8_normalize($entity_form);
		}
	}

	protected function get_advanced_categ() {
		global $what;

		$categ = '';
		switch($what) {
			case 'auteur':
				$categ = 'auteurs';
				break;
			case 'editeur':
				$categ = 'editeurs';
				break;
			case 'collection':
				$categ = 'collections';
				break;
			case 'subcollection':
				$categ = 'souscollections';
				break;
			case 'categorie':
				$categ = 'categories';
				break;
			case 'serie':
				$categ = 'series';
				break;
			case 'indexint':
				$categ = 'indexint';
				break;
			case 'titre_uniforme':
				$categ = 'titres_uniformes';
				break;
			case 'authperso':
				$categ = 'authperso';
				break;
		}
		return $categ;
	}

	protected function get_advanced_form() {
		global $form_display_mode;

		$entities_controller = $this->get_entities_controller_instance();
		$entities_controller->set_url_base(static::get_base_url()."&action=update&form_display_mode=".$form_display_mode);
		$entities_controller->proceed_form();
	}

	protected function get_advanced_save() {
		$entities_controller = $this->get_entities_controller_instance();
		$entities_controller->set_url_base(static::get_base_url());
		return $entities_controller->proceed_update();
	}

	protected function get_add_link() {
		global $no_display;
		global $pmb_popup_form_display_mode;
		global $form_display_mode;

		$link = static::get_base_url();
		if(!$form_display_mode) {
			$form_display_mode = $pmb_popup_form_display_mode;
		}
		if($form_display_mode == 2) {
			$link .= "&categ=".$this->get_advanced_categ()."&sub=form";
		}
		$link .= "&form_display_mode=".$form_display_mode;
		$link .= "&action=add&deb_rech='+this.form.f_user_input.value+'&no_display=".$no_display;
		return $link;
	}

	protected function get_add_label() {
		global $msg;
		return $msg[static::class.'_add'];
	}

	protected function get_search_form() {
		global $charset;
		global $bt_ajouter;

		$sel_search_form = $this->get_sel_search_form_template();
		if($bt_ajouter == "no"){
			$sel_search_form = str_replace("!!bouton_ajouter!!", '', $sel_search_form);
		} else {
			$bouton_ajouter = "<input type='button' class='bouton_small' onclick=\"document.location='".$this->get_add_link()."'\" value='".htmlentities($this->get_add_label(), ENT_QUOTES, $charset)."' />";
			$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		}
		return $sel_search_form;
	}

	protected function get_display_list() {
		$display_list = '';
		$searcher_instance = $this->get_searcher_instance();
		$this->nbr_lignes = $searcher_instance->get_nb_results();
		if($this->nbr_lignes) {
			$sorted_objects = $searcher_instance->get_sorted_result('default', $this->get_start_list(), $this->get_nb_per_page_list());
			foreach ($sorted_objects as $object_id) {
				$display_list .= $this->get_display_object($object_id);
			}
			$display_list .= $this->get_pagination();
		} else {
			$display_list .= $this->get_message_not_found();
		}
		return $display_list;
	}

	public function get_sel_search_form_template() {
		global $msg, $charset;

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				!!bouton_ajouter!!
			</form>
			<script type='text/javascript'>
				<!--
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
				-->
			</script>
		";
		return $sel_search_form;
	}

	protected function get_message_not_found() {
		global $msg;
		return $msg['no_'.str_replace('selector_', '', static::class).'_found'];
	}

	protected function get_change_link($display_mode) {
		global $no_display;
		global $deb_rech;

		$link = static::get_base_url();
		if($display_mode == 2) {
			$link .= "&categ=".$this->get_advanced_categ()."&sub=form";
		}
		$link .= "&form_display_mode=".$display_mode;
		$link .= "&action=add&deb_rech=".$deb_rech."&no_display=".$no_display;
		return $link;
	}

	protected function get_html_button($location='', $label='') {
		global $charset;

		return "<input type='button' id='switch_input_type' class='bouton_small' onclick=\"document.location='".$location."'\" value='".htmlentities($label, ENT_QUOTES, $charset)."' />";
	}

	protected function get_search_fields_filtered_objects_types() {
		return array($this->get_objects_type(), "authorities");
	}

	protected function get_searcher_tabs_instance() {
		if(!isset($this->searcher_tabs_instance)) {
			$this->searcher_tabs_instance = new searcher_selectors_tabs('authorities');
		}
		return $this->searcher_tabs_instance;
	}

	protected function get_search_perso_instance($id=0) {
		return new search_perso($id, 'AUTHORITIES');
	}

	protected function get_search_instance() {
		$search = new search_authorities(true, 'search_fields_authorities');
		$search->add_context_parameter('in_selector', true);
		return $search;
	}

	protected function get_searcher_instance() {
		$searcher = searcher_factory::get_searcher($this->objects_type, '', $this->user_input);
		if(method_exists($searcher, 'add_context_parameter')) {
			$searcher->add_context_parameter('in_selector', true);
		}
		return $searcher;
	}
}
?>
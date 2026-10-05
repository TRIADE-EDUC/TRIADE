<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector.class.php,v 1.30.2.1 2025/01/16 10:24:12 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/selectors/selector_model.class.php");

class selector {
	protected $user_input;

	protected $nbr_lignes;

	protected $objects_type;

	protected $searcher_tabs_instance;

	public function __construct($user_input=''){
		$this->user_input = static::format_user_input($user_input);
	}

	public function proceed() {
		global $page;

		print $this->get_sel_header_template();
		print $this->get_search_form();
		print $this->get_js_script();
		if(!$this->user_input) {
			$this->user_input = '*';
		}
		show_results($this->user_input, $this->nbr_lignes, $page, 0);
		print $this->get_sel_footer_template();
	}

	protected function get_form() {
		$form = '';
		return $form;
	}

	protected function save() {

	}

	protected function get_search_form() {
		$sel_search_form = $this->get_sel_search_form_template();
		return $sel_search_form;
	}

	protected function get_simple_search_form() {
		global $current_module;
		global $msg;
		global $mode, $what;

		$form = "";
		//onglets de recherche objets
		$searcher_tabs = $this->get_searcher_tabs_instance();
		if(empty($mode)){
    		if ($what) {
        		$searcher_tabs->set_current_mode();
    		} else {
    			$mode = $searcher_tabs->get_mode_objects_type($this->get_objects_type());
    		}
		}
		else {
    		$searcher_tabs->set_current_mode($mode);
		}

		$form .= "
    		<form id='".$this->get_sel_search_form_name()."' name='".$this->get_sel_search_form_name()."' class='form-".$current_module."' action='' method='post' onSubmit='return searcher_tabs_check_form(this);'>
    		<div class='form-contenu'>";
		$form .= $searcher_tabs->get_content_form();
		$form .= "
		<div class='row'></div>
		</div>
		<div class='row'>
		<input type='hidden' value='$mode' name='mode'/>
		<input class='bouton' type='submit' id='launch_search_button' value='".$msg['search']."' />
	    		</div>
    		</form>";
		$form .= $searcher_tabs->get_script_js_form($this->get_sel_search_form_name());
		return $form;
	}

	protected function get_advanced_search_form() {
		global $search;
		global $pmb_extended_search_dnd_interface;
		global $mode;
		global $search_data;

		$advanced_search_form = '';
		//onglets de recherche objets
		$searcher_tabs = $this->get_searcher_tabs_instance();
		$searcher_tabs->set_current_mode($searcher_tabs->get_mode_multi_search_criteria());

		$tab = $searcher_tabs->get_tab($searcher_tabs->get_mode_multi_search_criteria());

		$sc = $this->get_search_instance();
		$sc->set_filtered_objects_types($this->get_search_fields_filtered_objects_types());
		if($tab['PREDEFINEDSEARCH'] && !(is_array($search) && count($search))) {
			$search_perso = $this->get_search_perso_instance($tab['PREDEFINEDSEARCH']);
			$sc->unserialize_search($search_perso->query);
		}

		// Ré-affichage d'une recherche
		if (isset($search_data) && unserialize($search_data) !== false) {
		    $sc->unserialize_search(stripslashes($search_data));
		} elseif (!empty($search_data)) {
		    $sc->json_decode_search(stripslashes($search_data));
		}

		/**
		 * TODO: Ajouter une url ou dériver le showform
		 */
		$advanced_search_form .= $sc->show_form('','');
		if ($pmb_extended_search_dnd_interface){
			if(!isset($search_perso) || !is_object($search_perso)) {
				$search_perso = $this->get_search_perso_instance();
			}
			$advanced_search_form .= '<div id="search_perso" style="display:none">'.$search_perso->get_forms_list().'</div>';
		}
		return $advanced_search_form;
	}

	protected function get_js_script() {
		global $jscript;
		global $jscript_common_selector;
		global $param1, $param2, $p1, $p2;
		global $infield;

		if(!isset($jscript)) $jscript = $jscript_common_selector;
		$jscript = str_replace('!!param1!!', ($param1 ? $param1 : $p1), $jscript);
		$jscript = str_replace('!!param2!!', ($param2 ? $param2 : $p2), $jscript);
		$jscript = str_replace('!!infield!!', $infield, $jscript);
		return $jscript;
	}

	protected function get_start_list() {
		global $page;

		$page = intval($page);
		if(!$page) {
			return 0;
		} else {
			return ($page-1)*$this->get_nb_per_page_list();
		}
	}

	protected function get_nb_per_page_list() {
		global $nb_per_page;

		$nb_per_page = intval($nb_per_page);
		return $nb_per_page;
	}

	protected function get_display_object($id=0, $object_id=0) {

	}

	protected function get_display_list() {

	}

	protected function results_search() {
		global $mode;
		$searcher_tabs = $this->get_searcher_tabs_instance();
		$searcher_tabs->set_current_mode($mode);
		$searcher_tabs->proceed_search();
	}

	protected function get_message_not_found() {
	}

	protected function get_link_pagination() {
	    global $charset;

	    if(strtolower($charset) == "iso-8859-1") {
	        //enlevons les caractères accentués pour les PMB en ISO-8859-1
	        //pas sûr que l'on soit systématiquement sur du contenu Ajax ici
	        //sinon il faudrait faire un ut8_normalize
	        $link = static::get_base_url()."&user_input=".rawurlencode(convert_diacrit($this->user_input));
	    } else {
	        $link = static::get_base_url()."&user_input=".rawurlencode($this->user_input);
	    }
		return $link;
	}

	public function get_pagination() {
		global $page;

		// constitution des liens
		if(!$page) {
			$page = 1;
		}

		// affichage du lien précédent si nécéssaire
		$pagination = "<div class='row'>&nbsp;<hr /></div><div class='center'>";
		$pagination .= aff_pagination ($this->get_link_pagination(), $this->nbr_lignes, $this->get_nb_per_page_list(), $page, 10, false, true) ;
		$pagination .= "</div>";
		return $pagination;
	}

	public function get_title() {
		global $msg;
		return $msg[static::class];
	}

	public function get_sel_header_template() {
		global $charset;
		global $base_path;

		$sel_header = "
			<div id='att' style='z-Index:1000'></div>
			<script src='".$base_path."/javascript/ajax.js'></script>
			<div class='row'>
				<label for='selector_title' class='etiquette'>".htmlentities($this->get_title(),ENT_QUOTES,$charset)."</label>
				</div>
			<div class='row'>
			";
		return $sel_header;
	}

	protected function get_sel_search_form_name() {
		if($this->objects_type) {
			return "selector_".$this->objects_type."_search_form";
		} else {
			return "selector_search_form";
		}
	}

	public function get_sel_search_form_template() {
		global $msg, $charset;

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
			</form>
			<script type='text/javascript'>
				<!--
				document.forms['".$this->get_sel_search_form_name()."'].elements['f_user_input'].focus();
				-->
			</script>
			<hr />
		";
		return $sel_search_form;
	}

	public function get_sel_footer_template() {
		$sel_footer = "</div>";
		return $sel_footer;
	}

	protected function get_parameters_tabs(){
		$selector_model = new selector_model(str_replace('selector_', '', static::class));
		$parameters_tabs = $selector_model->get_parameters_tabs();
		if(!empty($parameters_tabs)) {
			return $parameters_tabs;
		}
		return array();
	}

	protected function get_sub_tabs(){
	    global $bt_ajouter;
		$current_url = static::get_base_url();
		$current_url = str_replace('select.php?', 'ajax.php?module=selectors&', $current_url);

		$searcher_tab = $this->get_searcher_tabs_instance();
		return "
				<div id='widget-container'></div>
				<script type='text/javascript'>
					require(['apps/pmb/form/FormSelector', 'dojo/dom'], function(FormSelector, dom){
						new FormSelector({doLayout: false, bt_ajouter:'".$bt_ajouter."', selectorURL:'".$current_url."', multicriteriaMode: '".$searcher_tab->get_mode_multi_search_criteria()."', parametersTabs: '".encoding_normalize::json_encode($this->get_parameters_tabs())."'}, 'widget-container');
					});
				</script>
				";
	}

	public function get_objects_type() {
		return $this->objects_type;
	}

	protected function get_searcher_tabs_instance() {
	}

	protected function get_search_perso_instance($id=0) {
	}

	protected function get_search_instance() {
	}

	// traitement en entrée des requêtes utilisateur
	public static function format_user_input($user_input='') {
		global $deb_rech;
		global $f_user_input;

		if ($deb_rech) {
			$user_input = stripslashes($deb_rech);
		} else {
			if(!$user_input) {
				if($f_user_input) {
					$user_input = stripslashes($f_user_input);
				}
			}
		}
		return $user_input;
	}

	public static function get_params_url() {
		global $param1, $param2, $p1, $p2;

		$params_url = ($param1 ? "&param1=".urlencode($param1) : "").
		($param2 ? "&param2=".urlencode($param2) : "").
		($p1 ? "&p1=".urlencode($p1) : "").
		($p2 ? "&p2=".urlencode($p2) : "");
		return $params_url;
	}

	public static function get_base_url() {
		global $base_path;
		global $what, $caller, $tab;
		global $no_display, $bt_ajouter, $dyn, $callback, $infield;
		global $max_field, $field_id, $field_name_id, $add_field;
		global $module_from;

		// gestion d'un élément à ne pas afficher
		if (!$no_display) $no_display=0;

		$base_url = $base_path."/select.php?what=".$what;

		if($caller) 		$base_url .= "&caller=".$caller;
		if($tab) 		$base_url .= "&tab=".$tab;
		$base_url .= static::get_params_url();
		if($no_display) 	$base_url .= "&no_display=".$no_display;
		if($bt_ajouter) 	$base_url .= "&bt_ajouter=".$bt_ajouter;
		if($dyn) 			$base_url .= "&dyn=".$dyn;
		if($callback) 		$base_url .= "&callback=".$callback;
		if($infield) 		$base_url .= "&infield=".$infield;
		if($max_field) 		$base_url .= "&max_field=".$max_field;
		if($field_id) 		$base_url .= "&field_id=".$field_id;
		if($field_name_id) 	$base_url .= "&field_name_id=".$field_name_id;
		if($add_field) 		$base_url .= "&add_field=".$add_field;
		if($module_from) 	$base_url .= "&module_from=".$module_from;

		foreach($_GET as $name => $value){
			if(strpos($base_url, $name) === false){
				$base_url .= "&".$name."=".rawurlencode($value);
			}
		}
		return $base_url;
	}

}
?>
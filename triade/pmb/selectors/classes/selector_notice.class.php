<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_notice.class.php,v 1.13.2.2 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/sel_notice.tpl.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/encoding_normalize.class.php");
require_once($class_path."/elements_list/elements_records_selectors_list_ui.class.php");

class selector_notice extends selector {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'records';
	}

	public function proceed() {
		global $action;

		$entity_form = '';
		switch($action){
			case 'simple_search':
			    $entity_form = $this->get_simple_search_form();
				break;
			case 'advanced_search':
			    $entity_form = $this->get_advanced_search_form();
				break;
			case 'results_search':
				ob_start();
				print $this->results_search();
				$entity_form = ob_get_contents();
				ob_end_clean();
				break;
			case 'add':
			    $entity_form = '<div id="tab_container">';
				ob_start();
				$this->get_advanced_form();
				$entity_form.= ob_get_contents();
				ob_end_clean();
				$entity_form.='</div>';
				break;
			case 'update':
				$saved_id = $this->get_advanced_save();
				$entity_form =
				'<textarea>'.encoding_normalize::json_encode(array(
						'id' => $saved_id,
						'type' => 'records',

				)).'</textarea>';
				break;
			case 'element_display':
				global $id;
				$id = intval($id);
				if($id) {
					$elements_records_selectors_list_ui = new elements_records_selectors_list_ui(array($id), 1, 1);
					$entity_form = $elements_records_selectors_list_ui->get_elements_list();
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

	protected function get_advanced_form() {
		$entities_controller = $this->get_entities_controller_instance();
		$entities_controller->set_url_base(static::get_base_url()."&action=update");
		$entities_controller->proceed_form();
	}

	protected function get_advanced_save() {
		$entities_controller = $this->get_entities_controller_instance();
		$entities_controller->set_url_base(static::get_base_url());
		return $entities_controller->proceed_update();
	}

	protected function get_display_list() {
		$searcher_instance = $this->get_searcher_instance();
		$this->nbr_lignes = $searcher_instance->get_nb_results();
		if($this->nbr_lignes) {
			$sorted_objects = $searcher_instance->get_sorted_result('default', $this->get_start_list(), $this->get_nb_per_page_list());
			foreach ($sorted_objects as $object_id) {
				$display_list .= $this->get_display_object(0, $object_id);
			}
			$display_list .= $this->get_pagination();
		} else {
			$display_list .= $this->get_message_not_found();
		}
		return $display_list;
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;
		global $niveau_biblio, $modele_id, $serial_id;

		$display = '';
		if($niveau_biblio){
			$location="./catalog.php?categ=serials&sub=modele&act=copy&modele_id=$modele_id&serial_id=$serial_id&new_serial_id=".$object_id;
			$mono_display = new mono_display($object_id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
			$display .= "
				<div class='row'>
					<div class='left'>
						<a href='#' onclick=\"copier_modele('$location')\">".$mono_display->header_texte."</a>
					</div>
					<div class='right'>
					".htmlentities($mono_display->notice->code,ENT_QUOTES,$charset)."
					</div>
				</div>";
		}

		else{
			$mono_display = new mono_display($object_id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
			$display .= "
				<div class='row'>
					<div class='left'>
						<a href='#' onclick=\"set_parent('$caller', '".$object_id."', '".trim(htmlentities(addslashes(str_replace("\r"," ",str_replace("\n"," ",strip_tags($mono_display->header_texte)))),ENT_QUOTES,$charset)." ".($mono_display->notice->code ? "(".$mono_display->notice->code.")" : ""))."','$callback')\">".$mono_display->result."</a>
					</div>
					<div class='right'>
						".htmlentities($mono_display->notice->code,ENT_QUOTES,$charset)."
					</div>
				</div>";
		}
		return $display;
	}

	protected function get_searcher_instance() {
		$searcher = searcher_factory::get_searcher('records', '', $this->user_input);
		$searcher->add_restrict_no_display();
		return $searcher;
	}

	protected function get_entities_controller_instance($id=0) {
		return new entities_records_controller($id);
	}

	protected function get_typdocfield() {
		global $msg, $charset;
		global $typdoc_query;

		// récupération des types de documents utilisés.
		$query = "SELECT count(typdoc), typdoc ";
		$query .= "FROM notices where typdoc!='' GROUP BY typdoc";
		$result = pmb_mysql_query($query);
		$toprint_typdocfield = "  <option value=''>".$msg['tous_types_docs']."</option>\n";
		$doctype = new marc_list('doctype');
		$obj = array();
		$qte = array();
		while ($rt = pmb_mysql_fetch_row($result)) {
			$obj[$rt[1]]=1;
			$qte[$rt[1]]=$rt[0];
		}
		foreach ($doctype->table as $key=>$libelle){
			if (isset($obj[$key]) && $obj[$key]==1){
				$toprint_typdocfield .= "  <option ";
				$toprint_typdocfield .= " value='$key'";
				if ($typdoc_query == $key) $toprint_typdocfield .=" selected='selected' ";
				$toprint_typdocfield .= ">".htmlentities($libelle." (".$qte[$key].")",ENT_QUOTES, $charset)."</option>\n";
			}
		}
		return $toprint_typdocfield;
	}

	public function get_sel_search_form_template() {
		global $msg, $charset;
		global $pmb_show_notice_id, $id_restrict;

		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				<select id='typdoc-query' name='typdoc_query'>
					".$this->get_typdocfield()."
				</select>";
		if ($pmb_show_notice_id) {
			$sel_search_form .="<br>".$msg['notice_id_libelle']." <input type='text' name='id_restrict' value=\"".$id_restrict."\" class='saisie-5em'>";
		} else {
			$sel_search_form .="<input type='hidden' name='id_restrict' value=''>";
		}
		$sel_search_form .="&nbsp;
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

	public static function get_params_url() {
		global $typdoc_query;
		global $id_restrict;
		global $niveau_biblio;
		global $modele_id;
		global $serial_id;

		$params_url = parent::get_params_url();
		$params_url .= ($typdoc_query ? "&typdoc_query=".$typdoc_query : "");
		$params_url .= ($id_restrict ? "&id_restrict=".$id_restrict : "");
		$params_url .= ($niveau_biblio ? "&niveau_biblio=".$niveau_biblio : "");
		$params_url .= ($modele_id ? "&modele_id=".$modele_id : "");
		$params_url .= ($serial_id ? "&serial_id=".$serial_id : "");
		return $params_url;
	}

	protected function get_search_fields_filtered_objects_types() {
		return array();
	}

	protected function get_searcher_tabs_instance() {
		if(!isset($this->searcher_tabs_instance)) {
			$this->searcher_tabs_instance = new searcher_selectors_tabs('records');
		}
		return $this->searcher_tabs_instance;
	}

	protected function get_search_perso_instance($id=0) {
		return new search_perso($id);
	}

	protected function get_search_instance() {
		$search = new search();
		$search->add_context_parameter('in_selector', true);
		return $search;
	}
}
?>
<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_notice.class.php,v 1.3 2018-11-27 15:41:30 ngantier Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");
require($base_path."/selectors/templates/sel_notice.tpl.php");
require_once($class_path."/mono_display.class.php");

class selector_notice extends selector {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'records';
	}
	
	public function proceed() {
		global $msg;
		global $action;
		global $pmb_allow_authorities_first_page;
		global $page;
		
		$entity_form = '';
		switch($action){
			case 'simple_search':
// 				print encoding_normalize::utf8_normalize($this->get_simple_search_form());
			    $entity_form = $this->get_search_form();
				break;
			case 'advanced_search':
// 				print encoding_normalize::utf8_normalize($this->get_advanced_search_form());
				break;
			case 'results_search':
// 				print $this->results_search();
				show_results($this->user_input, $this->nbr_lignes, $page);
				break;
			default:
				print $this->get_sel_header_template();
				print $this->get_js_script();
				if($pmb_allow_authorities_first_page || $this->user_input!= ""){
					if(!$this->user_input) {
						$this->user_input = '*';
					}
// 					print $this->get_display_list();
				}
				print $this->get_sel_footer_template();
				print $this->get_sub_tabs();
				break;
		}
		if ($entity_form) {
		    header("Content-Type: text/html; charset=UTF-8");
		    print encoding_normalize::utf8_normalize($entity_form);
		}
	}
	
	protected function get_display_list() {
		global $nb_per_page;
		global $page;
		global $no_display;
	
		$display_list = '';
		if(!$page) {
			$debut = 0;
		} else {
			$debut = ($page-1)*$nb_per_page;
		}
		$searcher_instance = $this->get_searcher_instance();
		$this->nbr_lignes = $searcher_instance->get_nb_results();
		if($this->nbr_lignes) {
			$sorted_objects = $searcher_instance->get_sorted_result('default', $debut, $nb_per_page);
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
		global $msg, $charset;
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
						<a href='#' onclick=\"set_parent('$caller', '".$object_id."', '".trim(htmlentities(addslashes(strip_tags($mono_display->header_texte)),ENT_QUOTES,$charset)." ".($mono_display->notice->code ? "($mono_display->notice->code)" : ""))."','$callback')\">".$mono_display->result."</a>
					</div>
					<div class='right'>
						".htmlentities($mono_display->notice->code,ENT_QUOTES,$charset)."
					</div>
				</div>";
		}
		return $display;
	}
		
	protected function get_searcher_instance() {
		return searcher_factory::get_searcher('records', '', $this->user_input);
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
		return new search();
	}
}
?>
<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_query_list.class.php,v 1.3 2017-10-10 09:47:00 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");

class selector_query_list extends selector {
	
	protected $search;
	
	protected $search_xml_file;
	
	protected $search_field_id;
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->search = new search($this->search_xml_file);
	}

	public function proceed() {
		global $page;
	
		print $this->get_sel_header_template();
		print $this->get_search_form();
		print $this->get_js_script();
		if(!$this->user_input) {
			$this->user_input = '*';
		}
		print $this->get_display_list();
		print $this->get_sel_footer_template();
	}
	
	protected function get_filter_result($list) {
		foreach ($list as $key=>$value) {
			if(!preg_match('`'.str_replace('*', '', addslashes($this->user_input)).'`i', $value)) {
				unset($list[$key]);
			}
		}
		return $list;
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
		$p=explode('_', $this->search_field_id);
		if($p[0] == 'f') {
			$query=$this->search->fixedfields[$p[1]]["INPUT_OPTIONS"]["QUERY"][0]["value"];
			if ($this->search->fixedfields[$p[1]]["INPUT_FILTERING"] == "yes") {
				$this->search->access_rights();
				$query = str_replace("!!acces_j!!", $this->search->tableau_access_rights["acces_j"], $query);
				$query = str_replace("!!statut_j!!", $this->search->tableau_access_rights["statut_j"], $query);
				$query = str_replace("!!statut_r!!", $this->search->tableau_access_rights["statut_r"], $query);
			}
		}
		$result = pmb_mysql_query($query);
		if($result) {
			$list = array();
			while ($row = pmb_mysql_fetch_array($result)) {
				$list[$row[0]] = $row[1];
			}
			if($this->user_input && $this->user_input != '*') {
				$list = $this->get_filter_result($list);
			}
			$this->nbr_lignes = count($list);
			if($this->nbr_lignes) {
				$list = array_slice($list, $debut, $nb_per_page, true);
				foreach ($list as $key=>$element) {
					$display_list .= $this->get_display_element($key, $element);
				}
				$display_list .= $this->get_pagination();
			} else {
				$display_list .= $this->get_message_not_found();
			}
		}
		return $display_list;
	}
	
	protected function get_display_element($key='', $value='') {
		global $msg, $charset;
		global $caller;
		global $callback;
		
		$display = '';
		$display .= pmb_bidi("<a href='#' onclick=\"set_parent('$caller', '".$key."', '".htmlentities(addslashes($value),ENT_QUOTES, $charset)."','$callback')\">".
		htmlentities($value,ENT_QUOTES, $charset)."</a><br />");
		return $display;
	}
	
	public function get_sel_search_form_template() {
		global $msg, $charset;
	
		$sel_search_form ="
			<form name='".$this->get_sel_search_form_name()."' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				<input type='hidden' id='search_xml_file' name='search_xml_file' value='".$this->search_xml_file."' />
				<input type='hidden' id='search_field_id' name='search_field_id' value='".$this->search_field_id."' />
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
		return $msg["searcher_no_result"];
	}
	
	protected function get_link_pagination() {
		$link = static::get_base_url()."&user_input=".rawurlencode($this->user_input)."&search_xml_file=".$this->search_xml_file."&search_field_id=".$this->search_field_id;
		return $link;
	}
	
	public function get_title() {
		$p=explode('_', $this->search_field_id);
		return $this->search->fixedfields[$p[1]]['TITLE'];
	}
	
	public function set_search_xml_file($search_xml_file) {
		$this->search_xml_file = $search_xml_file;
	}
	
	public function set_search_field_id($search_field_id) {
		$this->search_field_id = $search_field_id;
	}
}
?>
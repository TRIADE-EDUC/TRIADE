<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_groups.class.php,v 1.1 2018-10-29 09:02:07 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");

class selector_groups extends selector {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
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

	protected function get_display_query() {
		if(!$this->user_input) {
			return "SELECT id_groupe, libelle_groupe FROM groupe ";
		} else {
			return "SELECT id_groupe, libelle_groupe FROM groupe WHERE libelle_groupe like '".str_replace("*", "%", $this->user_input)."%' ";
		}
	}
	
	protected function get_display_element($index='', $value='') {
		global $charset;
		global $caller;
		global $callback;
		
		$display = "
			<div class='row'>
				<a href='#' onclick=\"set_parent('$caller', '".$index."', '".htmlentities(addslashes($value),ENT_QUOTES, $charset)."','$callback')\">".htmlentities($value,ENT_QUOTES, $charset)."</a>
			</div>";
		return $display;
	}
	
	protected function get_display_list() {
		global $nb_per_page;
		global $page;
		
		$display_list = '';
		if(!$page) {
			$debut = 0;
		} else {
			$debut = ($page-1)*$nb_per_page;
		}
		$query = $this->get_display_query();
		$result = pmb_mysql_query($query);
		if($result) {
			$list = array();
			while ($row = pmb_mysql_fetch_array($result)) {
				$list[$row[0]] = $row[1];
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
}
?>
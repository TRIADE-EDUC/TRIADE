<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_connectors.class.php,v 1.1 2019-03-13 12:04:05 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");
require_once($base_path."/admin/connecteurs/in/oai/oai_protocol.class.php");

class selector_connectors extends selector {
	
	protected $source_id;
	
	protected $type;
	
	protected $parameters;
	
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
		global $charset;
		global $nb_per_page;
		global $page;
		global $base_path;
		
		$display_list = '';
		if(!$page) {
			$debut = 0;
		} else {
			$debut = ($page-1)*$nb_per_page;
		}
		$list = array();
		$user_input = str_replace('*', '', $this->user_input);
		
		$conn = connecteurs::get_connector_instance_from_source_id($this->source_id);
		if ($conn) {
			$source_params = $conn->get_source_params($this->source_id);
			$parameters = unserialize($source_params["PARAMETERS"]);
			switch ($conn->get_id()) {
				case 'oai':
					//Intérogation du serveur
					$oai_p=new oai20($parameters['url'],$charset, $conn->timeout);
					if (!$oai_p->error) {
						if ($oai_p->has_feature("SETS")) {
							foreach ($oai_p->sets as $code=>$set) {
								if(!$user_input || strpos($set['name'], $user_input) !== false) {
									$list[$code] = $set['name'].($set['description'] ? " (".$set['description'].")" : "");
								}
							}
						}
					}
					break;
			}
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
		return $display_list;
	}
	
	public function set_source_id($source_id) {
		$this->source_id = $source_id;
	}
}
?>
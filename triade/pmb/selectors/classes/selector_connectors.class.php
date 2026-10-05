<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_connectors.class.php,v 1.7.4.1.2.1 2025/03/25 10:21:10 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/classes/selector.class.php");
require_once($base_path."/admin/connecteurs/in/oai/oai_protocol.class.php");

class selector_connectors extends selector {

	protected $source_id;

	protected $source_url;

	protected $connector_path;

	protected $connector_name;

	protected $type;

	protected $parameters;

	public function proceed() {
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
		global $base_path;

		$display_list = '';
		$list = array();
		$user_input = str_replace('*', '', $this->user_input);

		if(!$this->source_id && $this->source_url && $this->connector_path && $this->connector_name) {
		    require_once($base_path."/admin/connecteurs/in/".$this->connector_path."/".$this->connector_name.".class.php");
		    eval("\$conn=new ".$this->connector_name."(\"".$base_path."/admin/connecteurs/in/".$this->connector_path."\");");
		} else {
		    $conn = connecteurs::get_connector_instance_from_source_id($this->source_id);
		}
		if ($conn) {
			$source_params = $conn->get_source_params($this->source_id);
			$parameters = unserialize($source_params["PARAMETERS"]);
			if(empty($parameters['url']) && !empty($this->source_url)) {
			    $parameters['url'] = $this->source_url;
			}
			switch ($conn->get_id()) {
				case 'c3rb' :
			    case 'cairn':
				case 'divercities' :
				case 'oai':
					//Interrogation du serveur
					$oai_p = new oai20($parameters['url'], $charset, $conn->timeout);
					if (empty($oai_p->error)) {
						if (!empty($oai_p->has_feature("SETS"))) {
							foreach ($oai_p->sets as $code => $set) {
								if (empty($user_input) || strpos($set['name'], $user_input) !== false) {
									$description = '';
									if(!empty($set['description'])) {
										if(strlen($set['description']) > 80) {
											$description .= substr($set['description'], 0, 80)." [...]";
										} else {
											$description .= $set['description'];
										}
									}
									$list[$code] = $set['name'].($description ? " (".$description.")" : "");
								}
							}
						}
					}
					break;
				default:
				    break;
			}
		}
		$this->nbr_lignes = count($list);
		if($this->nbr_lignes) {
			$list = array_slice($list, $this->get_start_list(), $this->get_nb_per_page_list(), true);
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

	public function set_source_url($source_url) {
	    $this->source_url = $source_url;
	}

	public function set_connector_path($connector_path) {
	    $this->connector_path = $connector_path;
	}

	public function set_connector_name($connector_name) {
	    $this->connector_name = $connector_name;
	}
}
?>
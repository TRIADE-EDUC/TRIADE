<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: divercities.class.php,v 1.5.4.1 2025/03/25 10:21:10 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
global $lang;

require_once($base_path."/admin/connecteurs/in/oai/oai.class.php");
require_once($base_path."/admin/connecteurs/in/oai/oai_protocol.class.php");
require_once($class_path."/sessions_tokens.class.php");

if (version_compare(PHP_VERSION,'5','>=') && extension_loaded('xsl')) {
	require_once($include_path.'/xslt-php4-to-php5.inc.php');
}

class divercities extends oai {

	const DIVERCITIES_URL_DEFAULT = "https://export.divercities.eu/oai";
	const DIVERCITIES_AUTHENTICATION_URL_PATTERN_DEFAULT = "https://accounts.divercities.eu/users/auth/pmb?service=!!SERVICE!!&dest=!!DEST!!&bibid=!!STATION_ID!!&uid=!!UID!!&token=!!TOKEN!!";
	const DIVERCITIES_METADATAPREFIX_DEFAULT = 'oai1dtouch_dc';
	const DIVERCITIES_XSL_TRANSFORM_DEFAULT = 'oai1dtouch_dc.xsl';
	const DIVERCITIES_XSL_TRANSFORM_FILE_DEFAULT = __DIR__.'/xslt/oai1dtouch_dc.xsl';


	public function __construct($connector_path='') {
		parent::__construct($connector_path);
		$this->timeout = 30;

	}

	public function get_id() {
    	return "divercities";
    }

 	public function get_messages($connector_path) {
    	global $lang;

    	$oai_file_name = '';
    	if (is_readable($connector_path."/../oai/messages/".$lang.".xml")) {
    		$oai_file_name=$connector_path."/../oai/messages/".$lang.".xml";
    	} else if (is_readable($connector_path."/../oai/messages/fr_FR.xml")) {
    		$oai_file_name=$connector_path."/../oai/messages/fr_FR.xml";
    	}
    	$file_name = '';
    	if (is_readable($connector_path."/messages/".$lang.".xml")) {
    		$file_name=$connector_path."/messages/".$lang.".xml";
    	} else if (is_readable($connector_path."/messages/fr_FR.xml")) {
    		$file_name=$connector_path."/messages/fr_FR.xml";
    	}
    	if ($oai_file_name) {
    		$xmllist = new XMLlist($oai_file_name);
    		$xmllist->analyser();
    		$this->msg=$xmllist->table;
    	}
    	if ($file_name) {
    		$xmllist=new XMLlist($file_name);
    		$xmllist->analyser();
    		$this->msg+=$xmllist->table;
    	}
    }

    public function source_get_property_form($source_id) {

    	global $base_path, $charset;

    	$url = '';
    	$clean_base_url = '';
    	$sets = [];
    	$formats = '';
    	$xsl_transform = [];
    	$del_deleted = 0;
    	$clean_html = '';
    	$divercities_authentication_url_pattern = '';
    	$divercities_authentication_source_id = 0;

    	$params = $this->unserialize_source_params($source_id);
    	if (!empty($params["PARAMETERS"])) {
    		foreach ($params["PARAMETERS"] as $key => $val) {
    			${$key} = $val;
    		}
    	}

    	if(!$source_id) {
	    	$url = divercities::DIVERCITIES_URL_DEFAULT;
	    	$formats = divercities::DIVERCITIES_METADATAPREFIX_DEFAULT;
	    	$divercities_authentication_url_pattern = divercities::DIVERCITIES_AUTHENTICATION_URL_PATTERN_DEFAULT;
	    	if (is_readable(divercities::DIVERCITIES_XSL_TRANSFORM_FILE_DEFAULT)) {
	    		$xsl_transform = [
	    			'name'	=> divercities::DIVERCITIES_XSL_TRANSFORM_DEFAULT,
	    			'code'	=> file_get_contents(divercities::DIVERCITIES_XSL_TRANSFORM_FILE_DEFAULT),
	    		];
	    	}
	    }

    	$form = "
	    <div class='row'>
    		<div class='colonne3'>
    			<label for='url'>".$this->msg["oai_url"]."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' name='url' id='url' class='saisie-80em' value='".htmlentities($url, ENT_QUOTES, $charset)."'/>
    		</div>
	    </div>
		<div class='row'>
			<div class='colonne3'>
				<label for='clean_base_url'>".$this->msg["oai_clean_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='clean_base_url' id='clean_base_url' value='1' ".($clean_base_url ? "checked" : "")."/>
			</div>
		</div>
		<div class='row'>
		";
    	if (empty($url)) {
    		$form .= "<h3 style='text-align:center'>".$this->msg["rec_addr"]."</h3>";

    	} else {
    		//Interrogation du serveur
    		$oai_p = new oai20($url, $charset, $params["TIMEOUT"]);
    		if ($oai_p->error) {
    			$form .= "<h3 style='text-align:center'>".sprintf($this->msg["error_contact_server"], $oai_p->error_message)."</h3>";
    		} else {
    			$form .= "<h3 style='text-align:center'>".$oai_p->repositoryName."</h3>";
    			if (!empty($oai_p->description)) {
    				$form .= "
					<div class='row'>
						<div class='colonne3'>
							<label>".$this->msg["oai_desc"]."</label>
						</div>
						<div class='colonne_suite'>
							".htmlentities($oai_p->description, ENT_QUOTES, $charset)."
						</div>
					</div>
					";
    			}
    			$form .= "
				<div class='row'>
					<div class='colonne3'>
						<label>".$this->msg["oai_older_metatdatas"]."</label>
					</div>
					<div class='colonne_suite'>
						".formatdate($oai_p->earliestDatestamp)."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".$this->msg["oai_email_admin"]."</label>
					</div>
					<div class='colonne_suite'>
						".$oai_p->adminEmail."
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label>".$this->msg["oai_granularity"]."</label>
					</div>
					<div class='colonne_suite'>
						".($oai_p->granularity=="YYYY-MM-DD" ? $this->msg["oai_one_day"] : $this->msg["oai_minute"])."
					</div>
				</div>";

    			if ($oai_p->has_feature("SETS")) {
    				$form .= "
    				<div class='row'>
    					<div class='colonne3'>
    						<label for='sets'>".$this->msg["oai_sets_to_sync"]."</label>
    					</div>
    					<div class='colonne_suite'>";

    				$elements = array();
    				foreach ($oai_p->sets as $code => $set) {
    					if (array_search($code, $sets) !== false) {
    						$elements[] = array('id' => $code, 'name' => $set['name'].($set['description'] ? " (".$set['description'].")" : ""));
    					}
    				}
    				$form .= "<script src='".$base_path."/javascript/ajax.js'></script>";
    				templates::init_completion_attributes(array(
    						array('name' => 'att_id_filter', 'value' => $source_id),
    						array('name' => 'source_url', 'value' => urlencode($url)),
    						array('name' => 'connector_path', 'value' => $this->get_id()),
    						array('name' => 'connector_name', 'value' => $this->get_id())
    				));
    				templates::init_selection_attributes(array(
    						array('name' => 'source_id', 'value' => $source_id),
    						array('name' => 'source_url', 'value' => urlencode($url)),
    						array('name' => 'connector_path', 'value' => $this->get_id()),
    						array('name' => 'connector_name', 'value' => $this->get_id())
    				));

    				$form .= oai::get_syncronised_sets_template($elements, 'source_form', 'sets', 'set_id', 'connectors', true);
    				$form .= "</div></div>";
    			}
    			$form .= "
				<div class='row'>
					<div class='colonne3'>
						<label for='formats'>".$this->msg["oai_preference_format"]."</label>
					</div>
					<div class='colonne_suite'>
						<select name='formats' id='formats'>";
    			if (!is_array($formats)) {
    				$formats = array($formats);
    			}
    			$nb_metadatas = count($oai_p->metadatas);
    			for ($i = 0; $i < $nb_metadatas; $i++) {
    				$form .= "<option value='".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."' alt='".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."' title='".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."' ".(@array_search($oai_p->metadatas[$i]["PREFIX"], $formats) !== false ? "selected" : "").">".htmlentities($oai_p->metadatas[$i]["PREFIX"], ENT_QUOTES, $charset)."</option>\n";
    			}
    			$form .= "</select>";
    			if (!empty($xsl_transform)) {
    				$form .= "<br /><i>".sprintf($this->msg["oai_xslt_file_linked"], $xsl_transform["name"])."</i> :".$this->msg["oai_del_xslt_file"]." <input type='checkbox' name='del_xsl_transform' value='1'/>";
    			}
    			$form .= "</div></div>";
    			$form .= "
				<div class='row'>
				    <div class='colonne3'>
				        <label>".$this->msg['oai_xslt_file']."</label>
		            </div>
				    <div class='colonne_suite'>
				            <input type='file' name='xslt_file' />
		            </div>
	            </div>";
    			if ($oai_p->deletedRecord == "persistent" || $oai_p->deletedRecord == "transient") {
    				$form .= "
    				<div class='row'>
    					<div class='colonne3'>
    						<label>".sprintf($this->msg["oai_del_marked_elts"], ($oai_p->deletedRecord == "persistent" ? $this->msg["oai_del_marked_persistent"] : $this->msg["oai_del_marked_temp"]))."</label>
    					</div>
    					<div class='colonne_suite'>
    						<label for='del_yes'>".$this->msg["oai_yes"]."</label><input type='radio' name='del_deleted' id='del_yes' value='1' ".($del_deleted == 1 ? "checked" : "").">
    						<label for='del_no'>".$this->msg["oai_no"]."</label><input type='radio' name='del_deleted' id='del_no' value='0' ".($del_deleted == 0 ? "checked" : "").">
    					</div>
    				</div>";
    			}
    		}
    	}

    	$form.= "
    	</div>
	   	<div class='row'>
    		<div class='colonne3'>
    			<label for='clean_html'>".$this->msg["oai_clean_html"]."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='checkbox' name='clean_html' id='clean_html' value='1' ".($clean_html ? "checked" : "")."/>
    		</div>
    	</div>
    	<div class='row'></div>";

    	$form.= "
 	    <div class='row'>
    		<div class='colonne3'>
    			<label for='divercities_authentication_url_pattern'>".$this->msg["divercities_authentication_url_pattern"]."</label>
    		</div>
    		<div class='colonne_suite'>
    			<input type='text' name='divercities_authentication_url_pattern' id='divercities_authentication_url_pattern' class='saisie-80em' value='".htmlentities($divercities_authentication_url_pattern, ENT_QUOTES, $charset)."'/>
    		</div>
	    </div>";

    	$authentication_sources_selector = $this->get_authentication_sources_selector($divercities_authentication_source_id);
    	if($authentication_sources_selector == '') {
    		$form.= "
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<h3 >".$this->msg['divercities_authentication_source_error']."</h3>
			</div>
			<div class='row'></div>";
    	} else {
    		$form.= "
			<div class='row'>
	    		<div class='colonne3'>
	    			<label for='clean_html'>".$this->msg['divercities_authentication_source']."</label>
	    		</div>
	    		<div class='colonne_suite'>".$authentication_sources_selector."</div>
    	<div class='row'></div>";
    	}

    	$form.= "
    	<script>
		    document.getElementsByClassName('form-admin')[0].addEventListener('keypress', function(e) {
		    if (e.keyCode == 13) {
			    e.preventDefault();
            }});
	    </script>";

    	return $form;
    }


    protected function get_authentication_sources_selector($divercities_authentication_source_id) {

    	global $charset;

    	$sources = $this->get_authentication_sources();
    	if(empty($sources)) {
    		return '';
    	}
    	$selector = "<select id='divercities_authentication_source_id' name='divercities_authentication_source_id'>";
    	foreach($sources as $v) {
    		$selector.= "<option value='".$v['id']."' ";
    		if($divercities_authentication_source_id == $v['id']) {
    			$selector.= "selected ";
    		}
    		$selector.=">";
    		$selector.= htmlentities($v['name'], ENT_QUOTES, $charset);
    		$selector.= "</option>";
    	}
    	$selector.= "</select>";
    	return $selector;
    }


    protected function get_authentication_sources() {

    	$connectors_out_list = new connecteurs_out();
    	$sources = [];

		foreach($connectors_out_list->connectors as $connector_out) {
			if($connector_out->path == 'divercities') {
				foreach($connector_out->sources as $source) {
					$sources[] = [
						'id'			=> $source->id,
						'name'			=> $source->name,
						'station_id' 	=> $source->config['station_id'],
						'shared_key' 	=> $source->config['shared_key'],
					];
				}
			}
    	}
    	return $sources;
    }


    public function make_serialized_source_properties($source_id) {

    	global $url,$clean_base_url,$formats,$del_deleted,$del_xsl_transform,$clean_html;
    	global $divercities_authentication_url_pattern, $divercities_authentication_source_id;

    	$t=array();
    	$t["url"]=stripslashes($url);
    	$t["clean_base_url"]=$clean_base_url;
    	$t["sets"]=templates::get_values_completion_field_from_form('sets');
    	$t["formats"]=$formats;
    	$t["del_deleted"]=$del_deleted;
    	$t["clean_html"]=$clean_html;

    	switch (true) {
    		//Ajout fichier xslt
    		case ($_FILES["xslt_file"])&&(!$_FILES["xslt_file"]["error"]) :
    			$xslt_file_content = [
    				'name'	=> $_FILES["xslt_file"]["name"],
    				'code'	=> file_get_contents($_FILES["xslt_file"]["tmp_name"])
    				];
    			$t["xsl_transform"] = $xslt_file_content;
    			break;

    		//Suppression fichier xslt
    		case $del_xsl_transform :
    			$t["xsl_transform"] = '';
    			break;

    		//Création source
    		case (!$source_id && is_readable(divercities::DIVERCITIES_XSL_TRANSFORM_FILE_DEFAULT) ) :
    			$xslt_file_content = [
    				'name'	=> divercities::DIVERCITIES_XSL_TRANSFORM_DEFAULT,
    				'code'	=> file_get_contents(divercities::DIVERCITIES_XSL_TRANSFORM_FILE_DEFAULT),
    			];
    			$t["xsl_transform"] = $xslt_file_content;
    			break;

    		default :
	    		$oldparams=$this->get_source_params($source_id);
	    		if ($oldparams["PARAMETERS"]) {
	    			//Anciens paramètres
	    			$oldvars=unserialize($oldparams["PARAMETERS"]);
	    		}
	    		$t["xsl_transform"] = $oldvars["xsl_transform"];
    			break;
    	}

    	$t['divercities_authentication_url_pattern'] = stripslashes($divercities_authentication_url_pattern);
    	$t['divercities_authentication_source_id'] = intval($divercities_authentication_source_id);
    	$this->sources[$source_id]["PARAMETERS"]=serialize($t);
    }


    /**
     *
     * @param string $recid
     * @param array $params
     *
     * @return string
     */
	public static function get_resource_link($ref, $params=[]) {

		if(empty($ref) || empty($params) || empty($params['source_id']) || empty($params['empr_id'])) {
			return '';
		}
		$conn = new static();
		$source_params = $conn->unserialize_source_params($params['source_id']);

		$link = $source_params['PARAMETERS']['divercities_authentication_url_pattern'];
		$station_id = '';
		$shared_key = '';
		$token = '';
		$divercities_authentication_source_id = $source_params['PARAMETERS']['divercities_authentication_source_id'];
		$divercities_authentication_source = $conn::get_authentication_source_by_id($divercities_authentication_source_id);
		if(!empty($divercities_authentication_source['station_id']) && !empty($divercities_authentication_source['shared_key']) ) {
			$station_id = $divercities_authentication_source['station_id'];
			$shared_key = $divercities_authentication_source['shared_key'];
			$token = md5($params['empr_id'].$shared_key.$station_id);
		}
		$data = $conn->get_ref($params['source_id'], $ref);
		$service = '';
		if(!empty($data[901]['a'][0]['value'])) {
			$service = $data[901]['a'][0]['value'];
		}
		$dest = '';
		if(!empty($data[902]['a'][0]['value'])) {
			$dest = $data[902]['a'][0]['value'];
		}

		$link = str_replace('!!SERVICE!!', $service, $link);
		$link = str_replace('!!DEST!!', $dest, $link);
		$link = str_replace('!!STATION_ID!!', $station_id, $link);
		$link = str_replace('!!UID!!', $params['empr_id'], $link);
		$link = str_replace('!!TOKEN!!', $token, $link);
		return $link;

	}

	/**
	 *
	 * @param string $url
	 * @param int $authentication_source_id
	 * @param int $empr_id
	 *
	 * @return string
	 */
	public static function get_link_with_authenfication($url, $authentication_source_id, $empr_id) {

		if(empty($url) || empty($authentication_source_id) || empty($empr_id)) {
			return '';
		}

		$station_id = '';
		$shared_key = '';
		$token = '';
		$divercities_authentication_source = connector::get_authentication_source_by_id($authentication_source_id);
		if(!empty($divercities_authentication_source['station_id']) && !empty($divercities_authentication_source['shared_key']) ) {
			$station_id = $divercities_authentication_source['station_id'];
			$shared_key = $divercities_authentication_source['shared_key'];
			$token = md5($empr_id.$shared_key.$station_id);
			$url.=  "&bibid={$station_id}&uid={$empr_id}&token={$token}";
		}
		return $url;
	}

}
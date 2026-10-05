<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cairn.class.php,v 1.11.4.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($base_path."/admin/connecteurs/in/oai/oai.class.php");
require_once($base_path."/admin/connecteurs/in/oai/oai_protocol.class.php");
require_once($class_path."/sessions_tokens.class.php");

if (version_compare(PHP_VERSION,'5','>=') && extension_loaded('xsl')) {
	require_once($include_path.'/xslt-php4-to-php5.inc.php');
}

class cairn extends oai {

	/**
	 * Identifiant PMB à transmettre à CAIRN
	 * @var string
	 */
	protected $pmb_id;


	/**
	 * Identifiant fourni par CAIRN
	 * @var string
	 */
	protected $cairn_id;


    public function __construct($connector_path="")
    {
    	parent::__construct($connector_path);
    	$this->unserialize_parameters();
    }

    public function get_messages($connector_path) {
    	global $lang;

    	if (file_exists($connector_path."/../oai/messages/".$lang.".xml")) {
    		$oai_file_name=$connector_path."/../oai/messages/".$lang.".xml";
    	} else if (file_exists($connector_path."/../oai/messages/fr_FR.xml")) {
    		$oai_file_name=$connector_path."/../oai/messages/fr_FR.xml";
    	} else {
    		$oai_file_name='';
    	}
    	if (file_exists($connector_path."/messages/".$lang.".xml")) {
    		$file_name=$connector_path."/messages/".$lang.".xml";
    	} else if (file_exists($connector_path."/messages/fr_FR.xml")) {
    		$file_name=$connector_path."/messages/fr_FR.xml";
    	} else {
    		$file_name='';
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

    /**
     *
     * {@inheritDoc}
     * @see oai::get_id()
     */
    public function get_id()
    {
    	return "cairn";
    }

    /**
     *
     * {@inheritDoc}
     * @see oai::is_repository()
     */
	public function is_repository()
	{
	    return connector::REPOSITORY_YES;
	}

	/**
	 * Génération un token constitué de l'id lecteur, de l'identifiant institution et de l'identifiant transmis par PMB à CAIRN
	 */
	public function get_token() {
		if (!$this->cairn_id || !$this->pmb_id || !$_SESSION['user_code']) {
			return '';
		}
		$sessions_tokens = new sessions_tokens('cairn');
		$sessions_tokens->set_SESSID($_COOKIE["PmbOpac-SESSID"]);

		$token = $sessions_tokens->get_token();

		if(!$token) {
			$arguments = array(md5($_COOKIE["PmbOpac-SESSID"]),$this->cairn_id,$this->pmb_id);
			$token = $sessions_tokens->generate_token_from_arguments($arguments);
		}
		return $token;
	}

	public function get_sso_params() {
		$token = $this->get_token();
		if ($token) {
			return '&idsso='.$this->cairn_id.'&pmbtoken='.$token;
		}
		return '';
	}

	/**
	 * Génération de l'identifiant anonyme persistant d'un lecteur
	 * @param string $empr_login Login lecteur
	 */
	public function get_empr_id($empr_login) {
		if(!$empr_login) {
			return false;
		}
		$query = "select empr_cb from empr where empr_login = '".$empr_login."'";
		$result = pmb_mysql_query($query);

		if(pmb_mysql_num_rows($result)){
			$empr_cb = pmb_mysql_result($result, 0,0);
		}

		return md5($empr_cb);
	}

	/**
	 * Génération de l'identifiant unique PMB à transmettre à Cairn
	 */
	public function set_pmb_id($cairn_id) {
		$this->pmb_id = md5($cairn_id.microtime());
		return $this;
	}

	public function get_pmb_id() {
		return $this->pmb_id;
	}

	public function get_cairn_id() {
		return $this->cairn_id;
	}

	public function unserialize_parameters() {
		if ($this->parameters) {
			$param = unserialize($this->parameters);
			$this->pmb_id= $param['id_pmb'];
			$this->cairn_id=$param['id_cairn'];
		} else {
			$this->pmb_id = "";
			$this->cairn_id = "";
		}
	}


	//Formulaire des propriétés générales
	public function get_property_form() {
		global $charset;

		$r = parent::get_property_form();

		//Affichage du formulaire en fonction de $this->parameters
		$r .= "<script>
				function generate_id_pmb () {
					var id_cairn =  document.getElementById('id_cairn');
					if (id_cairn.value) {
						var conf = true;
						if (document.getElementById('id_pmb').value) {
							conf = confirm('".$this->msg["confirm_generation"]."');
						}
						if (conf) {
							var request = new http_request();
							var url = 'ajax.php?module=admin&categ=connector&sub=in&act=cairn_generate_id_pmb';
		     				request.request(url,true,'&id_cairn='+id_cairn.value,false,update_pmb_id);
						}
					} else {
						alert('".$this->msg["alert_id_cairn"]."');
					}
				}

				function update_pmb_id (data) {
					document.getElementById('id_pmb').value = data;
				}
			</script>
			<div class='row'>
				<div class='colonne3'><label for='id_cairn'>".$this->msg["id_cairn"]."</label></div>
				<div class='colonne-suite'><input type='text' id='id_cairn' name='id_cairn' value='".htmlentities($this->cairn_id,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='id_pmb'>".$this->msg["id_pmb"]."</label></div>
				<div class='colonne-suite'>
					<input type='text' id='id_pmb' name='id_pmb' value='".htmlentities($this->pmb_id,ENT_QUOTES,$charset)."' readonly/>
					<input type='button' class='bouton' name='button_generate_id' id='button_generate_id' value='".$this->msg["generate_id"]."' onclick ='generate_id_pmb()'/>
				</div>
			</div>";
		return $r;
	}

	public function make_serialized_properties() {
		global $sets_completion, $id_pmb, $id_cairn;
		//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
		$param = array();
		$param['sets_completion']=$sets_completion ?? 0;
		$param['id_pmb']=$id_pmb;
		$param['id_cairn']=$id_cairn;
		$this->parameters = serialize($param);
	}

	public function get_resource_link($url) {
		// TODO Générer le lien de la ressource avec le token à partir de l'url originale de la ressource
		return $url;
	}

	public function source_get_property_form($source_id) {
    	$params = $this->get_source_params($source_id);
    	$vars = array();
		if (!empty($params["PARAMETERS"])) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars = unserialize($params["PARAMETERS"]);
		}
		if (empty($vars['url'])) {
			$vars['url'] = 'http://oai.cairn.info/oai.php';
		}
		self::$sources_params[$source_id]["PARAMETERS"] = serialize($vars);
		return parent::source_get_property_form($source_id);
	}
}
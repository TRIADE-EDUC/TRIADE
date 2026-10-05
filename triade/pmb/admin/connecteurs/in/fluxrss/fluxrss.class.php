<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: fluxrss.class.php,v 1.6.4.2 2025/04/16 12:16:53 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/search.class.php");

class fluxrss extends connector {
	//Variables internes pour la progression de la récupération des notices
	public $current_set;			//Set en cours de synchronisation
	public $total_sets;			//Nombre total de sets sélectionnés
	public $metadata_prefix;		//Préfixe du format de données courant
	public $n_recu;				//Nombre de notices reçues
	public $xslt_transform;		//Feuille xslt transmise
	public $sets_names;			//Nom des sets pour faire plus joli !!
	public $schema_config;

	protected $default_enrichment_template; // Template par défaut de l'enrichissement

    public function __construct($connector_path = "")
    {
    	parent::__construct($connector_path);
    	$this->set_default_enrichment_template();
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "fluxrss";
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::is_repository()
     */
	public function is_repository()
	{
	    return connector::REPOSITORY_YES;
	}

   public function source_get_property_form($source_id) {
    	global $charset;

    	$params = $this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars = unserialize($params["PARAMETERS"]);
		}
		//URL
		if (!isset($vars['url']))	$vars['url'] = "";
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["fluxrss_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='url' id='url' class='saisie-80em' value='".htmlentities($vars['url'],ENT_QUOTES,$charset)."'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["fluxrss_xslt_file"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='file' name='xslt_file' />";
			if (!empty($vars['xsl_transform'])) {
				$form.= "<br /><i>" . sprintf($this->msg["fluxrss_xslt_file_linked"], $vars['xsl_transform']["name"]) . "</i> : " . $this->msg["fluxrss_del_xslt_file"] . "<input type='checkbox' name='del_xsl_transform' value='1'/>";
			}
			$form.= "
			</div>
		</div>";
    	// Template de l'enrichissement
		$form.= "<div class='row'>
				<div class='colonne3'><label>".$this->msg["fluxrss_enrichment_template"]."</label></div>
				<div class='colonne-suite'>
					<textarea name='enrichment_template'>".(!empty($vars['enrichment_template']) ? stripslashes($vars['enrichment_template'] ?? "") : stripslashes($this->default_enrichment_template ?? ""))."</textarea>
				</div>
			</div>";

		$form.="
		<div class='row'></div>";
		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $url, $enrichment_template, $del_xsl_transform;

    	$t = array();
    	$t["url"] = stripslashes($url);
    	$t["del_deleted"] = null;

    	//Vérification du fichier
    	if (($_FILES["xslt_file"]) && (!$_FILES["xslt_file"]["error"])) {
    		$xslt_file_content = array();
    		$xslt_file_content["name"] = $_FILES["xslt_file"]["name"];
    		$xslt_file_content["code"] = file_get_contents($_FILES["xslt_file"]["tmp_name"]);
    		$t["xsl_transform"] = $xslt_file_content;
    	} elseif ($del_xsl_transform) {
    		$t["xsl_transform"] = "";
    	} else {
    		$oldparams = $this->get_source_params($source_id);
    		if ($oldparams["PARAMETERS"]) {
    			//Anciens paramètres
    			$oldvars = unserialize($oldparams["PARAMETERS"]);
    		}
    		$t["xsl_transform"] = $oldvars["xsl_transform"];
    	}
    	$t['enrichment_template'] = ($enrichment_template ? $enrichment_template : addslashes($this->default_enrichment_template));
		$this->sources[$source_id]["PARAMETERS"] = serialize($t);
	}

	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		parent::fetch_default_global_values();
		$this->timeout = 40;
		$this->repository = 1;
	}

	//Formulaire des propriétés générales
	public function get_property_form() {
		$this->fetch_global_properties();
	}

	public function make_serialized_properties() {

		$keys = array();
		$this->parameters=serialize($keys);
	}

	public function progress($query, $token) {

		$callback_progress = $this->callback_progress;
		if ($token["completeListSize"]) {
			$percent = ($this->current_set / $this->total_sets) + (($token["cursor"] / $token["completeListSize"]) / $this->total_sets);
			$nlu = $this->n_recu;
			$ntotal = "inconnu";
		} else {
			$percent = ($this->current_set / $this->total_sets);
			$nlu = $this->n_recu;
			$ntotal = "inconnu";
		}
		call_user_func($callback_progress, $percent, $nlu, $ntotal);
	}

	public function cancel_maj($source_id) {
		return true;
	}

	public function break_maj($source_id) {
		return true;
	}

	public function maj_entrepot($source_id, $callback_progress = "", $recover = false, $recover_env = "") {
		global $base_path, $charset;

		$this->n_recu = 0;
		$this->callback_progress = $callback_progress;
		$params=$this->get_source_params($source_id);
		$this->fetch_global_properties();
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars = unserialize($params["PARAMETERS"]);
			foreach ($vars as $key => $val) {
				global ${$key};
				${$key} = $val;
			}
		}
		if (!isset($url)) {
			$this->error_message = $this->msg["fluxrss_unconfigured"];
			$this->error = 1;
			return;
		}
		$this->xslt_transform = $vars["xsl_transform"]["code"];
		if($this->xslt_transform == "") {
		    $this->xslt_transform = file_get_contents($base_path."/admin/connecteurs/in/fluxrss/xslt/rss.xsl");
		}
		//Recherche de la dernière date...
		$requete = "select unix_timestamp(max(date_import)) from entrepot_source_".$source_id." where 1;";
		$resultat = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$last_date = pmb_mysql_result($resultat,0,0);
			if ($last_date) {
				$last_date+= 3600*24;
			}
		}
		$ch = curl_init();
		// configuration des options CURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		configurer_proxy_curl($ch, $url);
	 	$xml = curl_exec($ch);

	 	if ($charset == 'utf-8') {
	 		$xml = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
	 			'|[\x00-\x7F][\x80-\xBF]+'.
	 			'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
	 			'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
	 			'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
	 			'?', $xml);
	 	} else {
			$xml = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]/', '', $xml);
			$xml = preg_replace('/[\x91\x92]/', '\x27', $xml);
		}

	 	$xslt = new XSLTProcessor();
	 	$xslDoc = new DOMDocument();
	 	$xslDoc->loadXML($this->xslt_transform);
	 	$xslt->importStylesheet($xslDoc);
	 	$xmlDoc = new DOMDocument();
	 	$xmlDoc->loadXML($xml);
	 	$out = $xslt->transformToXML($xmlDoc);
	 	if ($out) {
		 	$rss = new DOMDocument();
		 	$rss->loadXML($out);
 		 	$entries = $rss->getElementsByTagName('item');
		 	foreach ($entries as $entry) {
	 		 	$data_notice = array();
	 		 	if ($entry->childNodes->length) {
	 		 		foreach ($entry->childNodes as $field) {
	 		 			$key = $field->nodeName;
	 		 			$val = $field->nodeValue;
	 		 			if ($val) {
	 		 				$data_notice[$key] = ($charset != 'utf-8' ? encoding_normalize::utf8_decode($val) : $val);
	 		 			}
	 		 		}
	 		 	}
	 			$this->rec_record($this->notice_2_uni($data_notice), $source_id);
		 	}
	 	}
 		curl_close($ch);
		return $this->n_recu;
	}

	public function notice_2_uni($nt) {

		$unimarc=array();

		if($nt["guid"]) {
			$unimarc["001"][0] = $nt["guid"];
		} else {
			$unimarc["001"][0] = $nt["title"];
		}
		$unimarc["200"][0]["a"][0] = $nt["title"];
		// Résumé
		if ($nt["description"]) $unimarc["330"][0]["a"][0] = $nt["description"];
		// Auteur
		if ($nt["author"]) {
			$unimarc["700"][0]["a"][0] = $nt["author"];
			$unimarc["700"][0]["4"][0] = '070';
		}
		// Link
		if ($nt["link"]) $unimarc["856"][0]["u"][0] = $nt["link"];

		if ($nt["category"]) $unimarc["610"][0]["a"][0] = $nt["category"];

		if ($nt["pubDate"]) { // date édition
			$unimarc["210"][0]["d"][] = $nt["pubDate"];
		}
		// source
		$unimarc["801"][0]["a"][0] = "FR";
		$unimarc["801"][0]["b"][0] = $this->get_id();
		return $unimarc;
	}

	public function rec_record($record, $source_id) {
		global $charset, $base_path, $url, $search_index;

		$date_import = date("Y-m-d H:i:s",time());

		//Recherche du 001
		$ref = $record["001"][0];
		//Mise à jour
		if ($ref) {
			//Si conservation des anciennes notices, on regarde si elle existe
			if (!$this->del_old) {
				$ref_exists = $this->has_ref($source_id, $ref);
				if($ref_exists) return 1;
			}
			//Si pas de conservation des anciennes notices, on supprime
			if ($this->del_old) {
				$this->delete_from_entrepot($source_id, $ref);
				$this->delete_from_external_count($source_id, $ref);
			}
			//Si pas de conservation ou reférence inexistante
			if (($this->del_old) || ((!$this->del_old) && (!$ref_exists))) {
				//Insertion de l'entête
				$n_header["rs"] = "*";
				$n_header["ru"] = "*";
				$n_header["el"] = "1";
				$n_header["bl"] = "m";
				$n_header["hl"] = "0";
				$n_header["dt"] = $this->types[$search_index[$url][0]];
				if (!$n_header["dt"]) $n_header["dt"] = "a";

				$n_header["001"] = $record["001"][0];
				//Récupération d'un ID
				$recid = $this->insert_into_external_count($source_id, $ref);

				foreach($n_header as $hc => $code) {
					$this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid);
				}
				$field_order = 0;
				foreach ($record as $field => $val) {
					for ($i=0; $i < count($val); $i++) {
						if (is_array($val[$i])) {
							foreach ($val[$i] as $sfield => $vals) {
								for ($j = 0; $j < count($vals); $j++) {
									$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, $sfield, $field_order, $j, $vals[$j], $recid);
								}
							}
						} else {
							$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, '', $field_order, 0, $val[$i], $recid);
						}
						$field_order++;
					}
				}
				$this->rec_isbd_record($source_id, $ref, $recid);
				$this->n_recu++;
			}
		}
	}

	public function getTypeOfEnrichment($source_id) {
		$type['type'] = array(
				array(
						"code" => "fluxrss",
						"label" => $this->msg['fluxrss']
				)
		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function getEnrichment($notice_id, $source_id, $type="", $enrich_params=array()) {
		$enrichment = array();
		return $enrichment;
	}

	public function getEnrichmentHeader(){
		$header = array();
		return $header;
	}

	/**
	 * Définit le template par défaut de l'enrichissement
	 */
	private function set_default_enrichment_template() {
	}
}// class end



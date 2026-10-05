<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: isidore.class.php,v 1.9.2.2.2.2 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");

class isidore extends connector {
    protected $aut_function;
    protected $api_url;

    public function __construct($connector_path="")
    {
    	parent::__construct($connector_path);
    	$this->api_url = 'https://api.isidore.science/resource/search?output=json';
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "isidore";
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
    	$params=$this->get_source_params($source_id);
    	if ($params["PARAMETERS"]) {
    	    //Affichage du formulaire avec $params["PARAMETERS"]
    	    $vars=unserialize($params["PARAMETERS"]);
    	    foreach ($vars as $key=>$val) {
    	        global ${$key};
    	        ${$key}=$val;
    	    }
    	}
    	if (!isset($isidore_hal_domains)) {
    		$isidore_hal_domains = '';
    	}
    	if (!isset($isidore_doc_types)) {
    		$isidore_doc_types = array();
    	}

    	$curl = new Curl();
    	$curl->timeout = 60;
    	$curl->set_option('CURLOPT_SSL_VERIFYPEER',false);

    	$nb_per_pass = 50;
    	$page_nb = 1;

    	$response = $curl->get($this->api_url."&facet=discipline,replies=99&replies=0");
		$json_content = json_decode($response->body, true);

		$hal_domains = $json_content['response']['replies']['facets']['facet']['node'];

    	$response = $curl->get($this->api_url."&facet=type,replies=99&replies=0");
		$json_content = json_decode($response->body, true);

		$doc_types = $json_content['response']['replies']['facets']['facet']['node'];

    	$form= "
		<div class='row'>
			<div class='colonne3'>
				<label for='isidore_hal_domains'>".$this->msg["isidore_hal_domains"]."</label>
			</div>
			<div class='colonne_suite'>
				<select name='isidore_hal_domains'>";

    	if (!empty($hal_domains) && is_countable($hal_domains)) {
        	for ($i = 0; $i < count($hal_domains); $i++) {
        		$form.= "
        				<option value='".$hal_domains[$i]['@key']."' ".(($hal_domains[$i]['@key'] == $isidore_hal_domains) ? "selected='selected'" : "").">".htmlentities($hal_domains[$i]['label']['$'], ENT_QUOTES, $charset)." ".sprintf($this->msg['isidore_nb_items'], $hal_domains[$i]['@items'])."</option>";
        	}
    	}
    	$form.= "
				</select>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='isidore_doc_types'>".$this->msg["isidore_doc_types"]."</label>
			</div>
			<div class='colonne_suite'>
				<select name='isidore_doc_types[]' multiple='multiple' size='".(is_countable($doc_types) && count($doc_types) <= 30 ? count($doc_types) : 30)."'>";

    	if (!empty($doc_types) && is_countable($doc_types)) {
        	for ($i = 0; $i < count($doc_types); $i++) {
        		$form.= "
        				<option value='".$doc_types[$i]['@key']."' ".(in_array($doc_types[$i]['@key'], $isidore_doc_types) ? "selected='selected'" : "").">".htmlentities($doc_types[$i]['label']['$'], ENT_QUOTES, $charset)."</option>";
        	}
    	}

    	$form.= "
				</select>
			</div>
		</div>";
    	return $form;
    }

	public function make_serialized_source_properties($source_id) {
	    global $isidore_hal_domains, $isidore_doc_types;
	    $t["isidore_hal_domains"] = $isidore_hal_domains;
	    $t["isidore_doc_types"] = $isidore_doc_types;
	    $this->sources[$source_id]["PARAMETERS"] = serialize($t);
	}

    public function rec_record($record, $source_id, $search_id) {
    	global $charset;

    	$date_import = date("Y-m-d H:i:s",time());

    	//Recherche du 001
    	$ref = $record["001"][0];
    	//Mise à jour
    	if ($ref) {
    		$ref_exists = $this->has_ref($source_id, $ref);
    		if ($ref_exists) return false;

    		//Si conservation des anciennes notices, on regarde si elle existe
    		$ref_exists = false;
    		if (!$this->del_old) {
    			$ref_exists = $this->has_ref($source_id, $ref);
    		}
    		//Si pas de conservation des anciennes notices, on supprime
    		if ($this->del_old) {
    			$this->delete_from_entrepot($source_id, $ref);
    			$this->delete_from_external_count($source_id, $ref);
    		}
    		if (($this->del_old) || ((!$this->del_old)&&(!$ref_exists))) {
    			//Insertion de l'entête
				$n_header["rs"] = "*";
				$n_header["ru"] = "*";
				$n_header["el"] = "1";
				$n_header["bl"] = "m";
				$n_header["hl"] = "0";
				$n_header["dt"] = "g";

				//Récupération d'un ID
				$recid = $this->insert_into_external_count($source_id, $ref);
				foreach($n_header as $hc=>$code) {
					$this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid, $search_id);
				}

				$field_order=0;
				foreach ($record as $field=>$val) {
					for ($i=0; $i<count($val); $i++) {
						if (is_array($val[$i])) {
							foreach ($val[$i] as $sfield=>$vals) {
								for ($j=0; $j<count($vals); $j++) {
									if ($charset!="utf-8") {
										$vals[$j] = encoding_normalize::clean_cp1252($vals[$j], 'utf-8');
										$vals[$j] = encoding_normalize::utf8_decode($vals[$j]);
									}
									$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, $sfield, $field_order, $j, $vals[$j], $recid, $search_id);
								}
							}
						} else {
							if ($charset!="utf-8") {
								$val[$i] = encoding_normalize::clean_cp1252($val[$i], 'utf-8');
								$val[$i] = encoding_normalize::utf8_decode($val[$i]);
							}
							$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, '', $field_order, 0, $val[$i], $recid, $search_id);
						}
						$field_order++;
					}
				}
				$this->rec_isbd_record($source_id, $ref, $recid);
    		}
    	}
    	return true;
    }

	public function maj_entrepot($source_id, $callback_progress="", $recover=false, $recover_env="") {
		global $form_radio, $form_from;

		$this->fetch_global_properties();
		$keys = unserialize($this->parameters);

		$this->callback_progress = $callback_progress;
		$params = $this->unserialize_source_params($source_id);
		$p = $params["PARAMETERS"];
		$this->source_id = $source_id;
		$this->n_recu = 0;
		$this->n_total = 0;

		$modification_date = '';
		if ($form_radio == 'last_sync') {
			$sql = " SELECT MAX(UNIX_TIMESTAMP(date_import)) FROM entrepot_source_" . $source_id;
			$res = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
			$modification_date = date("Y-m-d", $res);
		} else if ($form_radio == 'date_sync') {
			$modification_date = $form_from;
			if(is_int($modification_date)) {
				$modification_date = date("Y-m-d", $modification_date);
			}
		}

		$url = $this->api_url.'&discipline='.urlencode($p['isidore_hal_domains']).'&after='.$modification_date;
		$curl = new Curl();
		$curl->timeout = 60;
		$curl->set_option('CURLOPT_SSL_VERIFYPEER', false);
		@mysql_set_wait_timeout();

		if (empty($p['isidore_doc_types'])) {
			$p['isidore_doc_types'] = array('');
		}
		$type_filter = "";
		// On commence par compter le nombre d'enregistrement à récupérer
		for ($i = 0; $i < count($p['isidore_doc_types']); $i++) {
			$type_filter .= ($p['isidore_doc_types'][$i] ? '&type='.urlencode($p['isidore_doc_types'][$i]) : '');
		}
		$nb_per_pass = 250;
		$url .= $type_filter . '&sort=date,ASC&replies=' . $nb_per_pass;
		$response = $curl->get($url);
		$json_content = json_decode($response->body);
		$this->n_total = $json_content->response->replies->meta->{'@items'};
		$this->progress();
		if (!$this->n_total) {
			return $this->n_recu;
		}
		while (true) {
			foreach ($json_content->response->replies->content->reply as $record) {
				if($this->rec_record($this->isidore_2_uni($record), $source_id, '')) {
					$this->n_recu++;
					$this->progress();
				}
			}
			if (empty($json_content->response->replies->page->{'@next'})) {
				break;
			}
			$page_nb = $json_content->response->replies->page->{'@next'};
			$response = $curl->get($url.'&page='.$page_nb);
			$json_content = json_decode($response->body);
		}
		return $this->n_recu;
	}

    public function progress() {
    	$callback_progress = $this->callback_progress;
		if ($this->n_total) {
			$percent = ($this->n_recu / $this->n_total);
			$nlu = $this->n_recu;
			$ntotal = $this->n_total;
		} else {
			$percent = 0;
			$nlu = $this->n_recu;
			$ntotal = "inconnu";
		}
		call_user_func($callback_progress, $percent, $nlu, $ntotal);
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::getSynchroForm()
     */
    public function getSynchroForm($source_id, $sync_form = "sync_form")
    {
    	global $form_from;
    	global $form_radio;

    	$source_id = intval($source_id);
    	$sync_form = in_array($sync_form, [ "sync_form", "planificateur_form"]) ? $sync_form : "sync_form";

    	$sql = " SELECT MAX(UNIX_TIMESTAMP(date_import)) FROM entrepot_source_" . $source_id;
    	$res = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
    	$latest_date_database_string = $res ? formatdate(date("Y-m-d", $res)) : "<i>" . $this->msg["isidore_nonotice_sync"] . "</i>";

    	$dateuntil = "";
    	$form = "<blockquote role='presentation'>";
    	$form .= "
				" . $this->msg["isidore_get_notices"] . "
				<br /><br />
				<input type='radio' name='form_radio' value='last_sync' " . ((($form_radio == "last_sync") || !$form_radio) ? "checked" : "") . " />" . $this->msg["isidore_last_sync"] . " <br />
				<input type='radio' name='form_radio' value='date_sync' " . (($form_radio == "date_sync") ? "checked" : "") . " />" . $this->msg["isidore_sync_from"] . "
				<input type='hidden' name='form_from' value='" . ($form_from ? $form_from : date("Y-m-d", $form_from)) . "' />
				<input type=\"text\" readonly size=\"10\" name=\"form_from_lib\" value=\"" . (($form_from != '') ? formatdate($form_from) : formatdate(date("Y-m-d", $form_from))) . "\">";
		if($form_from != '') {
			$form_from = strtotime($form_from);
		} else {
			$form_from = time();
		}
		$form .= "
				<input class='bouton' type='button' name='form_from_button' value='".$this->msg['isidore_form_from_select']."' onClick=\"openPopUp('./select.php?what=calendrier&caller=$sync_form&date_caller=" . date("Ymd", $form_from) . "&param1=form_from&param2=form_from_lib&auto_submit=NO&date_anterieure=YES', 'date_adhesion', 250, 300, -2, -2, 'toolbar=no, dependent=yes, resizable=yes')\"   />
    			<br />
    					";

    	$form .= "<br />" . sprintf($this->msg["isidore_syncinfo_date_baserecent"], $latest_date_database_string) . "<br /><br />";

    	$form .= "</blockquote>";
    	return $form;
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::get_maj_environnement()
     */
    public function get_maj_environnement($source_id) {
    	global $form_from;
    	global $form_radio;
    	$envt=array();
    	$envt["form_from"]=$form_from;
    	$envt["form_radio"]=$form_radio;
    	return $envt;
    }

    public function isidore_2_uni($nt) {

		$unimarc = array();
		$auttotal = array();

		// Construction du 001
		$unimarc["001"][0] = $this->get_id().':'.$nt->isidore->url->{"$"};

		// title
		if (!empty($nt->isidore->title)) {
			$titles = $nt->isidore->title;
			if (!is_array($titles)) {
				$titles = array($titles);
			}
			//TODO voir la gestion des langues pour l'instant on filtre
			$titles = array_values(array_filter($titles, function($title) { return !isset($title->{'@added'}); }));
			for ($i = 0; $i < count($titles); $i++) {
				$title = $titles[$i];
				if (is_object($title)) {
					if (!empty($title->{'$'})) {
						$title = $title->{'$'};
					} else {
						$title = "";
					}
				}
				if(! is_string($title)) {
					continue;
				}
				switch ($i) {
					case 0 :
						$unimarc["200"][0]["a"][0] = $title;
						break;
					case 1 :
						$unimarc["200"][0]["c"][0] = $title;
						break;
					case 2 :
						$unimarc["200"][0]["d"][0] = $title;
						break;
					case 3 :
						$unimarc["200"][0]["e"][0] = $title;
						break;
				}
			}
		}

		// url
		$unimarc["856"][0]["u"][0] = $nt->isidore->url->{"$"};

		// publicationDate
		if(!($publicationDate = formatdate($nt->isidore->date->normalizedDate))) {
			$publicationDate = $nt->isidore->date->normalizedDate;
		}
		$unimarc["210"][0]["d"][0] = $publicationDate;

		// Auteurs
		$authors = $nt->isidore->enrichedCreators->creator;
		if (!is_array($authors)) {
			$authors = array($authors);
		}
		if (count($authors) > 1) {
			$autf = "701";
		}else {
			$autf = "700";
		}
		for ($i=0; $i<count($authors); $i++) {
			$autt = array();
			if(empty($authors[$i]->lastname) && empty($authors[$i]->firstname)) {
				continue;
			}
			$autt["a"][0] = is_string($authors[$i]->lastname) ? $authors[$i]->lastname : "";
			$autt["b"][0] = is_string($authors[$i]->firstname) ? $authors[$i]->firstname : "";
			$autt["4"][0] = "070";
			$unimarc[$autf][] = $autt;
			$auttotal[] = $authors[$i];
		}

		// Résumé
		if (!empty($nt->isidore->abstract)) {
			$unimarc["330"][0]["a"][0] = '';
			$summaries = $nt->isidore->abstract;
			if (!is_array($summaries)) {
				$summaries = array($summaries);
			}
			foreach ($summaries as $summary) {
				$summary_label = $summary;
				if (is_object($summary)) {
					if (!empty($summary->{'$'})) {
						if(isset($summary->_label)) {
							$summary_label = $summary->_label;
						} else if(isset($summary->{'$'})) {
							$summary_label = $summary->{'$'};
						} else {
							$summary_label = "";
						}
					}
				}
				if ($unimarc["330"][0]["a"][0]) {
					$unimarc["330"][0]["a"][0].= "\n";
				}
				if(is_string($summary_label)) {
					$unimarc["330"][0]["a"][0].= $summary_label;
				}
			}
		}

		// Mots clés
		if (!empty($nt->isidore->subjects)) {
			$subjects = $nt->isidore->subjects->subject;
			if (!is_array($subjects)) {
				$subjects = array($subjects);
			}
			foreach($subjects as $subject) {
				$subject_label = $subject;
				if (is_object($subject)) {
					if (!empty($subject->{'$'})) {
						$subject_label = $subject->{'$'};
					}
				}
				if(is_string($subject_label)) {
					$keyword = array(
							'a' => array($subject_label)
					);
					$unimarc["610"][] = $keyword;
				}
			}
		}

		// Collection
		if (!empty($nt->isidore->source_info->collectionLabel)) {
			$unimarc['410'][0]['t'][0] = $nt->isidore->source_info->collectionLabel->{'$'};
		}

		return $unimarc;
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
	 * Permet de verifier les donnees passees dans l'environnement
	 *
	 * @param int $source_id
	 * @param array $env
	 * @return array
	 */
	public function check_environnement($source_id, $env) {

	    if (!in_array($env['form_radio'], ['last_sync', 'date_sync'], true)) {
	        $env['form_radio'] = 'last_sync';
	    }

	    // Format accepte : "Y-m-d"
	    if (!preg_match("/^[0-9]{4}(-[0-9]{2}){2}$/", $env['form_from'])) {
	        $env['form_from'] = date("Y-m-d", time());
	    }

	    $clean_env = [];
	    $clean_env['form_radio'] = $env['form_radio'] ?? "";
	    $clean_env['form_from'] = $env['form_from'];
	    $clean_env['form_from_lib'] = formatdate($env['form_from']);

	    return $clean_env;
	}
}

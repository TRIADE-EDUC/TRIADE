<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: toutapprendre.class.php,v 1.12.4.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/search.class.php");
require_once($base_path."/cms/modules/common/includes/pmb_h2o.inc.php");


class toutapprendre extends connector {

	//Variables internes pour la progression de la récupération des notices
	public $current_set;			//Set en cours de synchronisation
	public $total_sets;			//Nombre total de sets sélectionnés
	public $metadata_prefix;		//Préfixe du format de données courant
	public $n_recu;				//Nombre de notices reçues
	public $xslt_transform;		//Feuille xslt transmise
	public $sets_names;			//Nom des sets pour faire plus joli !!
	public $schema_config;

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
    public function get_id()
    {
    	return "toutapprendre";
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
		//URL
		if (!isset($url))
			$url = "http://biblio.toutapprendre.com/cours/catalogue.asp?id=353&complet=yes";
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["toutapprendre_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='url' id='url' class='saisie-60em' value='".htmlentities($url,ENT_QUOTES,$charset)."'/>
			</div>
		</div>
		<div class='row'></div>";

		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $url;
    	$t = array();
    	$t["url"]=stripslashes($url);
		$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}

	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		parent::fetch_default_global_values();
		$this->timeout=40;
		$this->repository=1;
	}

	public function progress($query,$token) {
		$callback_progress=$this->callback_progress;
		if ($token["completeListSize"]) {
			$percent=($this->current_set/$this->total_sets)+(($token["cursor"]/$token["completeListSize"])/$this->total_sets);
			$nlu=$this->n_recu;
			$ntotal="inconnu";
			//$nlu=$token["cursor"];
			//$ntotal=$token["completeListSize"];
		} else {
			$percent=($this->current_set/$this->total_sets);
			$nlu=$this->n_recu;
			$ntotal="inconnu";
		}
		call_user_func($callback_progress,$percent,$nlu,$ntotal);
	}

	public function cancel_maj($source_id) {
		return true;
	}

	public function break_maj($source_id) {
		return true;
	}

	public function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {
		global $base_path,$charset;

		$this->n_recu=0;
		$this->callback_progress=$callback_progress;
		$params=$this->get_source_params($source_id);
		$this->fetch_global_properties();
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		if (!isset($url)) {
			$this->error_message = $this->msg["toutapprendre_unconfigured"];
			$this->error = 1;
			return;
		}
		//Recherche de la dernière date...
		$requete="select unix_timestamp(max(date_import)) from entrepot_source_".$source_id." where 1;";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$last_date=pmb_mysql_result($resultat,0,0);
			if ($last_date) {
				$last_date+=3600*24;
			}
		}

		$ch = curl_init();

		$addr=$url;
		// configuration des options CURL
		curl_setopt($ch, CURLOPT_URL, $addr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		configurer_proxy_curl($ch,$addr);
	 	$xml=curl_exec($ch);

	 	if($charset=='utf-8') $xml = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
	 			'|[\x00-\x7F][\x80-\xBF]+'.
	 			'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
	 			'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
	 			'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/',
	 			'?', $xml );

	 	$this->dom = new DomDocument();
	 	$this->dom->encoding = $charset;

	 	if(!@$this->dom->loadXML($xml)) return 0;

 		 $cours = $this->dom->getElementsByTagName('cours');
 		 foreach($cours as $cour){
 		 	$data_notice=array();
 		 	if($cour->childNodes->length) {
 		 		foreach($cour->childNodes as $i) {
 		 			if($charset!='utf-8')$val= encoding_normalize::utf8_decode($i->nodeValue);
 		 			else $val=$i->nodeValue;
 		 			$data_notice[$i->nodeName] =$val;

 		 		}
 		 	}
 			$this->rec_record($this->notice_2_uni($data_notice),$source_id);

 		}
 		curl_close($ch);
		return $this->n_recu;
	}

	public function notice_2_uni($nt) {

		$unimarc=array();
		$unimarc["001"][0]=$nt["pk"];

		$unimarc["200"][0]["a"][0]=$nt["titre"];

		//Editeur
		if ($nt["editeur"]) $unimarc["210"][0]["c"][0]=$nt["editeur"];

		//Résumé
		if ($nt["description"])  $unimarc["330"][0]["a"][0]=$nt["description"];

		// Link demo
		if(count($nt["demo"])) $unimarc["856"][0]["u"][0]=$nt["demo"];

		// Keywords
		if ($nt["categorie"])  $unimarc["610"][0]["a"][0]=$nt["categorie"];

		// vignette
		if ($nt["thumbnail"])	$unimarc["896"][0]["a"][0]=$nt["thumbnail"];

		// collation
		if ($nt["dureeCours"])  $unimarc["215"][0]["a"][0]=$nt["dureeCours"];

		return $unimarc;
	}

	public function rec_record($record,$source_id) {
		global $base_path,$url,$search_index;

		$date_import=date("Y-m-d H:i:s",time());

		//Recherche du 001
		$ref=$record["001"][0];
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
			if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
				//Insertion de l'entête
				$n_header=array();
				$n_header["rs"]="*";
				$n_header["ru"]="*";
				$n_header["el"]="1";
				$n_header["bl"]="m";
				$n_header["hl"]="0";
				$n_header["dt"]=$this->types[$search_index[$url][0]];
				if (!$n_header["dt"]) $n_header["dt"]="a";

				$n_header["001"]=$record["001"][0];
				//Récupération d'un ID
				$recid = $this->insert_into_external_count($source_id, $ref);

				foreach($n_header as $hc=>$code) {
					$this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid);
				}

				$field_order=0;
				foreach ($record as $field=>$val) {
					for ($i=0; $i<count($val); $i++) {
						if (is_array($val[$i])) {
							foreach ($val[$i] as $sfield=>$vals) {
								for ($j=0; $j<count($vals); $j++) {
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

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
		$enrichment= array();

		$params=$this->get_source_params($source_id);
		if ($params['PARAMETERS']) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
		}

		$fields = array();

		$query = "select recid from notices_externes where num_notice = ".$notice_id;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($row = pmb_mysql_fetch_object($result)) {
				$external_infos = explode(" ", $row->recid);

				$query = "select ufield, usubfield, field_order, value from entrepot_source_".$external_infos[1]." where connector_id = '".$external_infos[0]."' and source_id = '".$external_infos[1]."' and ref = '".$external_infos[2]."'";
				$result = pmb_mysql_query($query);
				if ($result && pmb_mysql_num_rows($result)) {
					while ($row = pmb_mysql_fetch_object($result)) {
						// Si c'est une leçon, on a affaire à un champ répétable
						if ($row->ufield == 917) {
							$fields[$row->ufield][$row->field_order][$row->usubfield] = $row->value;
						} else {
							$fields[$row->ufield][$row->usubfield] = $row->value;
						}
					}
				}
			}
		}
		$lessons = array();

		// Titre
		$lessons['title'] = $fields[200]['a'];

		// Editeur
		$lessons['publisher'] = $fields[210]['c'];

		// Durée
		$lessons['duration'] = $fields[215]['a'];

		// Catégorie
		$lessons['category'] = $fields[610]['a'];

		// Démo
		$lessons['demo'] = $fields[856]['u'];

		// Vignette
		$lessons['thumbnail'] = $fields[896]['a'];

		// leçons
		$lessons['lessons'] = array();

		foreach ($fields[917] as $lesson) {
			$lessons['lessons'][] = array(
					'id' => $lesson['a'],
					'title' => $lesson['b']
			);
		}

		$lessons['base_url'] = $this->get_token();

		$enrichment[$type]['content'] = H2o::parseString(stripslashes($vars['enrichment_template']))->render(array("lessons"=>$lessons));
		$enrichment['source_label'] = $this->msg['toutapprendre_enrichment_source'];

		return $enrichment;
	}

	public function get_token() {
		$infos = unserialize($this->parameters);
		if($_SESSION['user_code'] && isset($infos['privatekey'])) {
			global $empr_cb, $empr_nom, $empr_prenom;

			$date = date("YmdHi");
			$hash = md5($empr_cb.$date.$infos['privatekey']);

			return "http://biblio.toutapprendre.com/ws/wsUrl.aspx?iduser=".$empr_cb."&firstname=".$empr_prenom."&lastname=".$empr_nom."&etablissement=".$infos['establishmentid']."&d=".$date."&hash=".$hash."&pkl=";
		}
		return '';
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::enrichment_is_allow()
	 */
	public function enrichment_is_allow()
	{
	    return connector::ENRICHMENT_YES;
	}

	public function getTypeOfEnrichment($notice_id, $source_id){
		$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
		}

		$type = array();

		// On n'affiche l'onglet que si le champ perso est renseigné
		$query = "select 1 from notices_custom_values where notices_custom_champ = ".$vars['cp_field']." and notices_custom_origine= ".$notice_id;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$type['type'] = array(
					array(
							"code" => "toutapprendre",
							"label" => $this->msg['toutapprendre_toutapprendre']
					)
			);
			$type['source_id'] = $source_id;
		}
		return $type;
	}

	public function getEnrichmentHeader($source_id){
		$header= array();
		return $header;
	}
}// class end



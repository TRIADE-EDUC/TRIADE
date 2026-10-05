<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: artevod.class.php,v 1.24.4.1 2025/04/16 12:16:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once("$class_path/curl.class.php");
if (version_compare(PHP_VERSION,'5','>=') && extension_loaded('xsl')) {
	require_once($include_path.'/xslt-php4-to-php5.inc.php');
}

class artevod extends connector {
	//Variables internes pour la progression de la récupération des notices
	public $profile;			//Profil ArteVOD
	public $n_recu;				//Nombre de notices reçues

	protected $default_enrichment_template; // Template par défaut de l'enrichissement

	public function __construct($connector_path="")
	{
		parent::__construct($connector_path);
		$xml=file_get_contents($connector_path."/profil.xml");
		$this->profile=_parser_text_no_function_($xml,"ARTEVODCONFIG");
		$this->set_default_enrichment_template();
	}

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
    public function get_id()
    {
    	return "artevod";
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
   	    global $charset, $url, $cp_field, $enrichment_template;
    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		$searchindexes=$this->profile["SEARCHINDEXES"][0]["SEARCHINDEX"];
		if (!$url) $url=$searchindexes[0]["URL"];
		$form = "";
		if (count($searchindexes) > 1) {
			$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='search_indexes'>".$this->msg["artevod_search_in"]."</label>
				</div>
				<div class='colonne_suite'>
					<select name='url' id='url' >";
				for ($i=0; $i<count($searchindexes); $i++) {
					$form.="<option value='".$searchindexes[$i]["URL"]."' ".($url==$searchindexes[$i]["URL"]?"selected":"").">".$this->get_libelle($searchindexes[$i]["COMMENT"])."</option>\n";
				}
				$form.="
				</select>
				</div>
			</div>";
		} else {
			$form .= "
			<input type='hidden' id='url' name='url' value='".$searchindexes[0]["URL"]."' />
			";
		}

		// Champ perso de notice à utiliser
		$form .= "<div class='row'>
				<div class='colonne3'><label for='source_name'>".$this->msg["artevod_source_field"]."</label></div>
				<div class='colonne-suite'>
					<select name='cp_field'>";
    	$query = "select idchamp, titre from notices_custom where datatype='integer'";
    	$result = pmb_mysql_query($query);
    	if($result && pmb_mysql_num_rows($result)){
    		while($row = pmb_mysql_fetch_object($result)){
    			$form.="
    					<option value='".$row->idchamp."' ".($row->idchamp == $cp_field ? "selected='selected'" : "").">".htmlentities($row->titre,ENT_QUOTES,$charset)."</option>";
    		}
    	}else{
    		$form.="
    					<option value='0'>".$this->msg["artevod_no_field"]."</option>";
    	}
    	$form.="
    				</select>
				</div>
			</div>";

    	// Template de l'enrichissement
		$form .= "<div class='row'>
				<div class='colonne3'><label for='source_name'>".$this->msg["artevod_enrichment_template"]."</label></div>
				<div class='colonne-suite'>
					<textarea id='enrichment_template' name='enrichment_template'>".($enrichment_template ? stripslashes($enrichment_template) : stripslashes($this->default_enrichment_template))."</textarea>
				</div>
			</div>
			<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
			<script type='text/javascript'>
			 	pmbDojo.aceManager.initEditor('enrichment_template');
			</script>
		";

		$form .= "<div class='row'></div>";
		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $url, $cp_field, $enrichment_template;

    	$t=array();
    	$t["url"]=$url;
    	$t["cp_field"] = $cp_field;
    	$t['enrichment_template'] = ($enrichment_template ? $enrichment_template : addslashes($this->default_enrichment_template));

    	$this->sources[$source_id]["PARAMETERS"]=serialize($t);
    }

    /**
     * Formulaire des propriétés générales
     */
	public function get_property_form() {
    	global $charset;
    	$this->fetch_global_properties();
    	//Affichage du formulaire en fonction de $this->parameters
    	if ($this->parameters) {
    		$keys = unserialize($this->parameters);
    		$url_referer= $keys['url_referer'];
    		$privatekey=$keys['privatekey'];
    	} else {
    		$url_referer="";
    		$privatekey="";
    	}
    	$r="<div class='row'>
				<div class='colonne3'><label for='url_referer'>".$this->msg["artevod_url_referer"]."</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-30em' id='url_referer' name='url_referer' value='".htmlentities($url_referer,ENT_QUOTES,$charset)."'/></div>
			</div>
			<div class='row'>
				<div class='colonne3'><label for='privatekey'>".$this->msg["artevod_private_key"]."</label></div>
				<div class='colonne-suite'><input type='text' class='saisie-50em' id='privatekey' name='privatekey' value='".htmlentities($privatekey,ENT_QUOTES,$charset)."'/></div>
			</div>";
    	return $r;
    }

    public function make_serialized_properties() {
    	global $url_referer, $privatekey;
    	//Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
    	$keys = array();

    	$keys['url_referer']=$url_referer;
    	$keys['privatekey']=$privatekey;
    	$this->parameters = serialize($keys);
    }

    public function maj_entrepot($source_id, $callback_progress="", $recover=false, $recover_env="") {
    	global $base_path;

    	$this->fetch_global_properties();
    	$keys = unserialize($this->parameters);

		$this->callback_progress = $callback_progress;
		$params = $this->unserialize_source_params($source_id);
		$p = $params["PARAMETERS"];
		$this->source_id = $source_id;
		$this->n_recu = 0;
		$this->n_total = 0;

		$url = $p["url"];

		$curl = new Curl();
		$curl->timeout = 60;
		$curl->set_option('CURLOPT_SSL_VERIFYPEER',false);
		@mysql_set_wait_timeout();

 		$nb_per_pass = 50;
 		$page_nb = 1;

		$response = $curl->get($url."?page_size=".$nb_per_pass."&page_nb=".$page_nb);
 		$json_content = json_decode($response->body);

 		if(count($json_content) && $response->headers['Status-Code'] == 200){
 			$this->n_total = $response->headers['X-Total-Count'];

 			$query = "select name from notices_custom where idchamp = ".$p['cp_field'];
 			$result = pmb_mysql_query($query);
 			if ($row = pmb_mysql_fetch_object($result)) {
 				$cp_artevod = array('cp_artevod' => $row->name);
 			} else {
 				$cp_artevod = array();
 			}
 			$sortir = false;
 			while (!$sortir) {
 				foreach ($json_content as $record) {
 					$statut = $this->rec_record($this->artevod_2_uni($record, $cp_artevod), $source_id, '');
		    		$this->n_recu++;
		    		$this->progress();
 					if(!$statut) {
 						$sortir = true;
 						break;
 					}
 				}
 				$page_nb++;
 				if(!$sortir) {
					$response = $curl->get($url."?page_size=".$nb_per_pass."&page_nb=".$page_nb);
	 				$json_content = json_decode($response->body);
 				}
 				if (!count($json_content)) {
 					break;
 				}
 			}
 		} else {
 			$this->error = true;
 			$this->error_message = $this->msg["artevod_error_auth"];
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

	public function artevod_2_uni($nt, $cp) {

		$unimarc = array();
		$auttotal = array();
		$naut = 0;

		// Construction du 001
		$unimarc["001"][0] = $this->get_id().':'.$nt->id;

		// title
		$unimarc["200"][0]["a"][0] = html_entity_decode($nt->title,ENT_QUOTES,'UTF-8');

		// description (html) -> Notes
		if($nt->description) {
			$unimarc["330"][0]["a"][0] = html_entity_decode(strip_tags($nt->description),ENT_QUOTES,'UTF-8');
		}

		// productionYear (2014)
		if ($nt->productionYear) {
			//$unimarc[""][0][""][0] = $nt->productionYear;
		}

		// posterUrl (http://prod-mednum.universcine.com/media/58/da/58da559ff0fa3.jpeg)
		if ($nt->posterUrl) {
			$unimarc["896"][0]["a"][0] = $nt->posterUrl;
		}

		// url (http://prod-mednum.universcine.com/une-saison-a-la-juilliard-school)
		if ($nt->url) {
			$unimarc["856"][0]["u"][0] = $nt->url;
		}

		// trailerUrl (http://media.universcine.com/0f/a3/0fa3f154-c07a-11e3-bfdd-e59cda21687c.mp4)
		if ($nt->trailerUrl) {
			$unimarc["897"][0]["a"][0] = $nt->trailerUrl;
			$unimarc["897"][0]["b"][0] = 'TRAILER_'.basename($nt->trailerUrl);
		}

		// duration (6240)
		if($nt->duration) {
			$unimarc["215"][0]["a"][0] = floor($nt->duration/60).':'.str_pad($nt->duration%60, 2, '0', STR_PAD_LEFT);
		}

		/* audioLanguages (array)
                (
                    [0] => stdClass Object
                        (
                            [type] => Language
                            [code] => eng
                        )
                )
		*/
		$audioLanguages = $nt->audioLanguages;
		if (count($audioLanguages)) {
			for ($i=0; $i<count($audioLanguages); $i++) {
				$autt = array();
				$autt["a"][0] = $audioLanguages[$i]->code;
				$unimarc['101'][] = $autt;
			}
		}

		/* directors (array)
                (
                    [0] => stdClass Object
                        (
                            [type] => Person
                            [fullName] => Max Nichols
                            [familyName] => Nichols
                            [givenName] => Max
                        )
                )
		*/
		$authors = $nt->directors;
		if (count($authors)) {
			if (($naut + count($authors)) > 1) {
				$autf = "701";
			}else {
				$autf = "700";
			}
			for ($i=0; $i<count($authors); $i++) {
				$autt = array();
				$autt["a"][0] = $authors[$i]->familyName;
				$autt["b"][0] = $authors[$i]->givenName;
				$autt["4"][0] = "300";
				$unimarc[$autf][] = $autt;
				$auttotal[] = $authors[$i];
			}
			$naut+= count($authors);
		}

		/* actors (array)
                (
                    [0] => stdClass Object
                        (
                            [type] => Person
                            [fullName] => Analeigh Tipton
                            [familyName] => Tipton
                            [givenName] => Analeigh
                        )
                )
		*/
		$authors = $nt->actors;
		if (count($authors)) {
			$autf = "702";
			for ($i=0; $i<count($authors); $i++) {
				$autt = array();
				$autt["a"][0] = $authors[$i]->familyName;
				$autt["b"][0] = $authors[$i]->givenName;
				$autt["4"][0] = "005";
				$unimarc[$autf][] = $autt;
				$auttotal[] = $authors[$i];
			}
			$naut+= count($authors);
		}

		// publicationDate (2017-03-28)
		if ($nt->publicationDate) {
			if(!($publicationDate = formatdate($nt->publicationDate))) {
				$publicationDate = $nt->publicationDate;
			}
			$unimarc["210"][0]["d"][0] = $publicationDate;
		}

		/* genres (array)
						(
							[0] => Documentaire
							[1] => Théâtre, cirque et danse
						)
		*/
		$unimarc["610"] = array();
		$genres = $nt->genres;
		if (count($genres)) {
			foreach($genres as $genre) {
				$keyword = array(
						'a' => array($genre)
				);
				$unimarc["610"][] = $keyword;
			}
		}
		/* themes (array)
				(
                    [0] => Comédie romantique
                )
		*/
		$themes = $nt->themes;
		if (count($themes)) {
			foreach($themes as $theme) {
				$keyword = array(
						'a' => array($theme)
				);
				$unimarc["610"][] = $keyword;
			}
		}

		// productionCountry (US)
		if ($nt->productionCountry) {
			$unimarc["210"][0]["a"][0] = $nt->productionCountry;
		}

		/* codes  => Array
                (
                    [0] => stdClass Object
                        (
                            [type] => Le meilleur du cinéma
                            [code] => 622040
                        )
                )
        */
		$codes = $nt->codes;
		if (count($codes)) {
			for ($i=0; $i<count($codes); $i++) {
				$autt = array();
				$autt["t"][0] = $codes[$i]->type;
				$autt["v"][0] = $codes[$i]->code;
				$unimarc['410'][] = $autt; // Collection
			}
		}

		/* medias (array)[0] => stdClass Object
                        (
                            [type] => POSTER
                            [url] => http://prod-mednum.universcine.com/media/58/da/58da57b51bd44.jpeg
                            [modificationDate] => 2017-03-28T14:32:22
                        )
		*/
		$medias = $nt->medias;
		if (count($medias)) {
			for ($i=0; $i<count($medias); $i++) {
				if ($medias[$i]->url == $nt->trailerUrl) {
					continue;
				}
				$autt = array();
				$autt["a"][0] = $medias[$i]->url;
				$autt["b"][0] = $medias[$i]->type.'_'.basename($medias[$i]->url);
				$unimarc['897'][] = $autt;
			}
		}

		// target_audience (array)
		$target_audiences = $nt->targetAudiences;
		if (count($target_audiences)) {
			$unimarc["215"][0]["c"][0] = '';
			foreach($target_audiences as $target_audience) {
				if ($unimarc["215"][0]["c"][0]) {
					$unimarc["215"][0]["c"][0].= '; ';
				}
				$unimarc["215"][0]["c"][0].= $target_audience->code;
			}
		}

		if($cp['cp_artevod']) {
			$unimarc["900"][0]["a"][0] = $nt->id;
			$unimarc["900"][0]["n"][0] = $cp['cp_artevod'];
		}

		$unimarc["801"][0]["a"][0] = 'FR';
		$unimarc["801"][0]["b"][0] = 'ArteVOD';

		return $unimarc;
	}

    public function rec_record($record, $source_id, $search_id) {
    	global $charset, $base_path;

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
    			$n_header = array();
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
								$vals[$i] = encoding_normalize::clean_cp1252($vals[$i], 'utf-8');
								$vals[$i] = encoding_normalize::utf8_decode($vals[$i]);
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

    /**
     *
     * {@inheritDoc}
     * @see connector::enrichment_is_allow()
     */
    public function enrichment_is_allow()
    {
        return connector::ENRICHMENT_YES;
    }

	public function getTypeOfEnrichment($source_id){
		$type = array();
		$type['type'] = array(
			array(
				"code" => "artevod",
				"label" => $this->msg['artevod_vod']
			)
		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
		$enrichment= array();
		return $enrichment;
	}

	public function getEnrichmentHeader(){
		$header= array();
		return $header;
	}

	/**
	 * Définit le template par défaut de l'enrichissement
	 */
	private function set_default_enrichment_template() {
		$this->default_enrichment_template = "{* Template par défaut *}
<div class='enrichment_artevod_container'>

	<div class='enrichment_artevod_mediatheque_numerique' style='text-align:center;'>
		<img src='./images/mediatheque_numerique.png' style='margin:5px;' title='Médiathèque numérique' />
	</div>

	{* titre *}
	{% if film.title %}
		<h2 class='enrichment_artevod_title'>{{ film.title }}</h2>
	{% endif %}

	<div class='ui-clearfix enrichment_artevod_infos-container'>
		{* affiche *}
		{% if film.poster %}
			<p class='enrichment_artevod_poster'><img alt='{{ film.title }}' src='{{ film.poster }}'/></p>
		{% endif %}
		<div class='enrichment_artevod-aside'>

			{* auteurs *}
			{% if film.authors %}
				<p class='enrichment_artevod_authors'>De {{ film.authors }}</p>
			{% endif %}

			{* acteurs *}
			{% if film.actors %}
				<p class='enrichment_artevod_actors'>Avec {{ film.actors }}</p>
			{% endif %}

			{* infos *}
			{% if film.production_year %}
				<p class='enrichment_artevod_year'>Date de sortie : {{ film.production_year }}</p>
			{% endif %}
			{% if film.production_countries %}
				<p class='enrichment_artevod_country'>Pays : {{ film.production_countries }}</p>
			{% endif %}
			{% if film.languages.langues.0.langue %}
				<p class='enrichment_artevod_language'>
					{% for language in film.languages.langues %}
						{% if loop.first %}Langue :{% endif %}{{ language.langue }}{%if !loop.last %}, {% endif %}
					{% endfor %}
				</p>
			{% endif %}
			{% if film.target_audience %}
				<p class='enrichment_artevod_audience'>Public : {{ film.target_audience }}</p>
			{% endif %}

			{* durée *}
			{% if film.duration %}
				<p class='enrichment_artevod_duration'>Durée : {{ film.duration }}</p>
			{% endif %}

			{* genres *}
			{% for genre in film.genres %}
				{% if loop.first %}<p class='enrichment_artevod_genres'>Genre(s) : {% endif %}
					<a href='./index.php?lvl=more_results&mode=keyword&user_query={{genre}}&tags=ok'> {{genre}}</a>
				{%if !loop.last %}, {% endif %}
			{% endfor %}

			{* lien vers la ressource *}
			{% if film.externaluri %}
				<p class='enrichment_artevod_externaluri'>
					<a href='{{ film.externaluri }}' target='_BLANK'>
						<i class='external-link-alt'>
							<svg version='1.1' id='Calque_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
                                 width='24px' height='24px' viewBox='0 0 24 24' enable-background='new 0 0 24 24' xml:space='preserve'>
                            <path d='M24,2.333v5.333c0,0.894-1.082,1.333-1.707,0.707l-1.488-1.488L10.658,17.032c-0.391,0.391-1.023,0.391-1.414,0L8.301,16.09
                                c-0.391-0.391-0.391-1.024,0-1.415L18.448,4.528l-1.487-1.488c-0.629-0.628-0.184-1.707,0.707-1.707H23
                                C23.553,1.333,24,1.781,24,2.333z M16.959,12.616l-0.666,0.667C16.105,13.471,16,13.725,16,13.99V20H2.667V6.667h11
                                c0.266,0,0.52-0.105,0.707-0.292l0.668-0.667C15.67,5.077,15.225,4,14.334,4H2C0.896,4,0,4.896,0,6v14.666c0,1.105,0.896,2,2,2
                                h14.666c1.105,0,2-0.895,2-2v-7.342C18.666,12.433,17.59,11.986,16.959,12.616z'/>
							</svg>
						</i> Voir le programme
					</a>
				</p>
			{% endif %}
		</div>
	</div>
	<div class='ui-clearfix enrichment_artevod_description-container'>

	{* extrait *}
	{% for trailer in film.trailers %}
		<video class='enrichment_artevod_trailer' width='400px' controls src='{{ trailer }}' >{{ 'Voir l extrait' | links_to film.externaluri }}</video>
	{% endfor %}

	{* description *}
	{% if film.description %}
		<p class='enrichment_artevod_description'><h4 class='artevod_description_title'>Synopsis :</h4>{{ film.description }}</p>
	{% endif %}
	</div>

	{* photos *}
	{% for photo in film.photos %}
		{% if loop.first %}<ul class='enrichment_artevod_photos ui-thumbnav'>{% endif %}
			<li style=''>
				<a href='{{ photo }}' data-uk-lightbox=\"{group:'group1'}\" data-lightbox-type='image' title='photo' >
					<img style='' src='{{ photo }}' alt='photo' class='enrichment_artevod_photo' />
				</a>
			</li>
		{% if loop.last %}</ul>{% endif %}
	{% endfor %}
</div>";
	}
}// class end
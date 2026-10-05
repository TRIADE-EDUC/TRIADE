<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_rss.class.php,v 1.20.2.1 2025/01/17 10:40:41 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/curl.class.php");

class cms_module_common_datasource_rss extends cms_module_common_datasource{

	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_rss",
			"cms_module_common_selector_type_article",
			"cms_module_common_selector_type_section",
			"cms_module_common_selector_type_article_generic",
			"cms_module_common_selector_type_section_generic"
		);
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		global $cms_module_common_datasource_rss_limit,$cms_module_common_datasource_rss_timeout;

		$this->parameters= array();
		$this->parameters['nb_max_elements'] = (int) $cms_module_common_datasource_rss_limit;
		$this->parameters['timeout'] = (int) $cms_module_common_datasource_rss_timeout;
		return parent::save_form();
	}

	public function get_form(){
	    if(!isset($this->parameters['nb_max_elements'])) {
	        $this->parameters['nb_max_elements'] = '';
	    }
	    if(!isset($this->parameters['timeout'])) {
	        $this->parameters['timeout'] = '2';
	    }
		$form = parent::get_form();
		$form.= "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_datasource_rss_limit'>".$this->format_text($this->msg['cms_module_common_datasource_rss_limit'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_datasource_rss_limit' value='".$this->parameters['nb_max_elements']."'/>
				</div>
			</div>
			<div class='row'>
					<div class='colonne3'>
					<label for='cms_module_common_datasource_rss_timeout'>".$this->format_text($this->msg['cms_module_common_datasource_rss_timeout'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_datasource_rss_timeout' value='".$this->parameters['timeout']."'/>
				</div>
			</div>";
		return $form;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas() {
		//on commence par récupérer l'identifiant retourné par le sélecteur...
	    if (is_array($this->selectors) && $this->parameters['selector'] != "") {
			for ($i = 0; $i < count($this->selectors); $i++) {
				if ($this->selectors[$i]['name'] == $this->parameters['selector']) {
					$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
					break;
				}
			}
			$loaded = false;
			$aCurl = new Curl();
			$aCurl->timeout = $this->parameters['timeout'] ?? 15; // 15 secondes si pas de valeur, c'est déjà beaucoup
			$url = $selector->get_value();
			if (is_array($url)) {
				$url = $url[0];
			}
			
			$actual_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			if (!empty($url) && $actual_url != $url) {
    			$content = $aCurl->get($url);
    			$flux = $content->body;
    			if ($flux && $content->headers['Status-Code'] == 200) {
    			  $rss = new domDocument();
    			  $loaded = $rss->loadXML($flux);
    			}
			}
			$informations = array();
			if ($loaded) {
				//les infos sur le flux...
				//Flux RSS
				if ($rss->getElementsByTagName("channel")->length > 0) {
					$channel = $rss->getElementsByTagName("channel")->item(0);
					$elements = array(
						'title',
						'description',
						'generator',
						'link'
					);
					$informations = $this->get_informations($channel,$elements,1);
					//on va lire les infos des items...
					$informations['items'] =array();
					$items = $rss->getElementsByTagName("item");
					$elements = array(
						'title',
						'description',
						'link',
						'guid',
						'date',
						'pubDate',
						'creator',
						'subject',
						'format',
						'language',
						'source',
					);
					for($i=0 ; $i<$items->length ; $i++){
						if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
							$informations['items'][]=$this->get_informations($items->item($i),$elements,false);
						}
					}
					
				//Flux ATOM
				} elseif($rss->getElementsByTagName("feed")->length > 0) {
					$feed = $rss->getElementsByTagName("feed")->item(0);
					$atom_elements = array(
							'title',
							'subtitle',
							'link',
							'updated',
							'author',
							'id',
					);
					$informations = $this->get_atom_informations($feed,$atom_elements,1);
					//on va lire les infos des entries...
					$informations['items'] =array();
					$entries = $rss->getElementsByTagName("entry");
					$atom_elements = array(
							'title',
							'link',
							'id',
							'author',
							'issued',
							'modified',
							'published',
							'content',
							'source',
					);
					for($i=0 ; $i<$entries->length ; $i++){
						if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
							$informations['items'][]=$this->get_atom_informations($entries->item($i),$atom_elements,false);
						}
					}
				}
			}
			return $informations;
		}
		return false;
	}

	protected function get_informations($node,$elements,$first_only=false){
		$informations = array();
		foreach($elements as $element){
			$items = $node->getElementsByTagName($element);
			switch ($element) {
				case "pubDate" :
					$element = "date";
					break;
			}
			if($items->length == 1 || $first_only){
				$informations[$element] = $this->charset_normalize($items->item(0)->nodeValue,"utf-8");
			}else{
				for($i=0 ; $i<$items->length ; $i++){
					$informations[$element][] = $this->charset_normalize($items->item($i)->nodeValue,"utf-8");
				}
			}
		}
		return $informations;
	}

	protected function get_atom_informations($node,$atom_elements,$first_only=false){
		$informations = array();
		foreach($atom_elements as $atom_element){
			$items = $node->getElementsByTagName($atom_element);
			switch ($atom_element) {
				case "published" :
					$element = "date";
				break;
				case "author" :
					if($first_only) {
						$element = "generator";
					} else {
						$element = "creator";
					}
					break;
				case "content" :
					$element = "description";
					break;
				default:
					$element = $atom_element;
					break;
			}

			if($items->length == 1 || $first_only){
				if ($element == "link") {
					$informations[$element] = $this->charset_normalize($items->item(0)->getAttribute('href'),"utf-8");
				} else {
					$informations[$element] = $this->charset_normalize($items->item(0)->nodeValue,"utf-8");
				}
			}else{
				if ($element == "link") {
					for($i=0 ; $i<$items->length ; $i++){
						$informations[$element][] = $this->charset_normalize($items->item(0)->getAttribute('href'),"utf-8");
					}
				} else {
					for($i=0 ; $i<$items->length ; $i++){
						$informations[$element][] = $this->charset_normalize($items->item($i)->nodeValue,"utf-8");
					}
				}
			}
		}
		return $informations;
	}

	public function get_format_data_structure(){
		return array(
			array(
				'var' => "title",
				'desc' => $this->msg['cms_module_common_datasource_rss_title_desc']
			),
			array(
				'var' => "subtitle",
				'desc' => $this->msg['cms_module_common_datasource_rss_subtitle_desc']
			),
			array(
				'var' => "description",
				'desc' => $this->msg['cms_module_common_datasource_rss_description_desc']
			),
			array(
				'var' => "generator",
				'desc' => $this->msg['cms_module_common_datasource_rss_generator_desc']
			),
			array(
				'var' => "link",
				'desc' => $this->msg['cms_module_common_datasource_rss_link_desc']
			),
			array(
				'var' => "items",
				'desc' => $this->msg['cms_module_common_datasource_rss_items_desc'],
				'children' => array(
					array(
						'var' => "items[i].title",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_title_desc']
					),
					array(
						'var' => "items[i].description",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_description_desc']
					),
					array(
						'var' => "items[i].link",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_link_desc']
					),
					array(
						'var' => "items[i].guid",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_guid_desc']
					),
					array(
						'var' => "items[i].date",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_date_desc']
					),
					array(
						'var' => "items[i].creator",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_creator_desc']
					),
					array(
						'var' => "items[i].subject",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_subject_desc']
					),
					array(
						'var' => "items[i].format",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_format_desc']
					),
					array(
						'var' => "items[i].language",
						"desc" => $this->msg['cms_module_common_datasource_rss_item_language_desc']
					),
					array(
							'var' => "items[i].source",
							"desc" => $this->msg['cms_module_common_datasource_rss_item_source_desc']
					)
				)
			),
		);
	}
}
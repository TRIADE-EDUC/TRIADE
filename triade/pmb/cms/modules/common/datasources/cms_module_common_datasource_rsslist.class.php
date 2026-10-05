<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_rsslist.class.php,v 1.4.2.1 2025/01/17 10:40:41 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/curl.class.php";

class cms_module_common_datasource_rsslist extends cms_module_common_datasource{

	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_available_selectors() {
		return array(
			'cms_module_common_selector_type_article',
			'cms_module_common_selector_type_section'
		);
	}

	public function save_form() {
	    global $cms_module_common_datasource_rsslist_limit, $cms_module_common_datasource_rsslist_timeout;

		$this->parameters = array();
		$this->parameters['nb_max_elements'] = (int) $cms_module_common_datasource_rsslist_limit;
		$this->parameters['timeout'] = (int) $cms_module_common_datasource_rsslist_timeout;
		
		return parent::save_form();
	}

	public function get_form() {
	    if(!isset($this->parameters['nb_max_elements'])) {
	        $this->parameters['nb_max_elements'] = '';
	    }
	    if(!isset($this->parameters['timeout'])) {
	        $this->parameters['timeout'] = '2';
	    }
		$form = parent::get_form();
		$form .= "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_datasource_rsslist_limit'>".$this->format_text($this->msg['cms_module_common_datasource_rsslist_limit'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_datasource_rsslist_limit' value='".$this->parameters['nb_max_elements']."'/>
				</div>
			</div>
			<div class='row'>
					<div class='colonne3'>
					<label for='cms_module_common_datasource_rsslist_timeout'>".$this->format_text($this->msg['cms_module_common_datasource_rsslist_timeout'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' name='cms_module_common_datasource_rsslist_timeout' value='".$this->parameters['timeout']."'/>
				</div>
			</div>";
		
		return $form;
	}
	
	public function get_datas() {
	    if (is_array($this->selectors) && $this->parameters['selector'] != "") {
		    $nb_selectors = count($this->selectors);
			for ($i = 0; $i < $nb_selectors; $i++) {
				if ($this->selectors[$i]['name'] == $this->parameters['selector']) {
					$selector = new $this->parameters['selector']($this->selectors[$i]['id']);
					break;
				}
			}
			
			$loaded = false;
			$aCurl = new Curl();
			$aCurl->timeout = (!empty($this->parameters['timeout']) ? $this->parameters['timeout'] : 15); // 15 secondes si pas de valeur, c'est déjà beaucoup
			$urls = $selector->get_value();
			
			$informations = [];
			foreach ($urls as $url) {
    			$actual_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    			if (!empty($url) && $actual_url != $url) {
        			$content = $aCurl->get($url);
        			$flux = $content->body;
        			if ($flux && $content->headers['Status-Code'] == 200) {
        			  $rss = new domDocument();
        			  $loaded = $rss->loadXML($flux);
        			}
    			}
    			
    			if ($loaded) {
    				if ($rss->getElementsByTagName("channel")->length > 0) {
    					$channel = $rss->getElementsByTagName("channel")->item(0);
    					$elements = array(
    						'title',
    						'description',
    						'generator',
    						'link'
    					);
    					$information = $this->get_informations($channel, $elements, 1);
    					$information['items'] = array();
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
    						'source'
    					);
    					for ($i = 0; $i < $items->length; $i++) {
    						if ($this->parameters['nb_max_elements'] == 0 || $i < $this->parameters['nb_max_elements']) {
    							$information['items'][] = $this->get_informations($items->item($i), $elements, false);
    						}
    					}
    				} elseif ($rss->getElementsByTagName("feed")->length > 0) {
    					$feed = $rss->getElementsByTagName("feed")->item(0);
    					$atom_elements = array(
    						'title',
    						'subtitle',
    						'link',
    						'updated',
    						'author',
    						'id'
    					);
    					$information = $this->get_atom_informations($feed, $atom_elements, 1);
    					$information['items'] = array();
    					$entries = $rss->getElementsByTagName("entry");
    					$atom_elements = array(
    						'title',
    						'link',
    						'id',
    						'author',
    						'issued',
    						'modified',
    						'published',
    						'content'
    					);
    					for ($i = 0; $i < $entries->length; $i++) {
    						if ($this->parameters['nb_max_elements'] == 0 || $i < $this->parameters['nb_max_elements']) {
    							$information['items'][] = $this->get_atom_informations($entries->item($i), $atom_elements, false);
    						}
    					}
    				}
    				$informations['rsslist'][] = $information;
    			}
			}
			
			return $informations;
		}
		
		return false;
	}

	protected function get_informations($node, $elements, $first_only = false) {
		$informations = array();
		foreach ($elements as $element) {
			$items = $node->getElementsByTagName($element);
			switch ($element) {
				case "pubDate":
					$element = "date";
					break;
			}
			if ($items->length == 1 || $first_only) {
				$informations[$element] = $this->charset_normalize($items->item(0)->nodeValue, "utf-8");
			} else {
				for ($i = 0; $i < $items->length; $i++) {
					$informations[$element][] = $this->charset_normalize($items->item($i)->nodeValue, "utf-8");
				}
			}
		}
		
		return $informations;
	}

	protected function get_atom_informations($node, $atom_elements, $first_only = false) {
		$informations = array();
		foreach ($atom_elements as $atom_element) {
			$items = $node->getElementsByTagName($atom_element);
			switch ($atom_element) {
				case "published":
					$element = "date";
				    break;
				case "author":
					$element = "creator";
					if ($first_only) {
						$element = "generator";
					}
					break;
				case "content":
					$element = "description";
					break;
				default:
					$element = $atom_element;
					break;
			}

			if ($items->length == 1 || $first_only) {
			    $elem = $items->item(0)->nodeValue;
				if ($element == "link") {
				    $elem = $items->item(0)->getAttribute('href');
				}
			    $informations[$element] = $this->charset_normalize($elem, "utf-8");
			} else {
				for ($i = 0; $i < $items->length; $i++) {
				    $elem = $items->item($i)->nodeValue;
				    if ($element == "link") {
				        $elem = $items->item(0)->getAttribute('href');
				    }
				    $informations[$element][] = $this->charset_normalize($elem, "utf-8");
				}
			}
		}
		
		return $informations;
	}

	public function get_format_data_structure() {
		return array(
			array(
				'var' => "title",
				'desc' => $this->msg['cms_module_common_datasource_rsslist_title_desc']
			),
			array(
				'var' => "subtitle",
				'desc' => $this->msg['cms_module_common_datasource_rsslist_subtitle_desc']
			),
			array(
				'var' => "description",
				'desc' => $this->msg['cms_module_common_datasource_rsslist_description_desc']
			),
			array(
				'var' => "generator",
				'desc' => $this->msg['cms_module_common_datasource_rsslist_generator_desc']
			),
			array(
				'var' => "link",
				'desc' => $this->msg['cms_module_common_datasource_rsslist_link_desc']
			),
			array(
				'var' => "items",
				'desc' => $this->msg['cms_module_common_datasource_rsslist_items_desc'],
				'children' => array(
					array(
						'var' => "items[i].title",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_title_desc']
					),
					array(
						'var' => "items[i].description",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_description_desc']
					),
					array(
						'var' => "items[i].link",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_link_desc']
					),
					array(
						'var' => "items[i].guid",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_guid_desc']
					),
					array(
						'var' => "items[i].date",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_date_desc']
					),
					array(
						'var' => "items[i].creator",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_creator_desc']
					),
					array(
						'var' => "items[i].subject",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_subject_desc']
					),
					array(
						'var' => "items[i].format",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_format_desc']
					),
					array(
						'var' => "items[i].language",
						"desc" => $this->msg['cms_module_common_datasource_rsslist_item_language_desc']
					),
					array(
							'var' => "items[i].source",
							"desc" => $this->msg['cms_module_common_datasource_rsslist_item_source_desc']
					)
				)
			),
		);
	}
}
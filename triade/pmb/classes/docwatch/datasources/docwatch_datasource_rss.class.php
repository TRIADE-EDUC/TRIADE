<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_rss.class.php,v 1.17 2019-02-25 13:24:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/datasources/docwatch_datasource.class.php");
/**
 * class docwatch_datasource_rss
 * 
 */
class docwatch_datasource_rss extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct
	
	protected function get_items_datas($link){
		if($link){
			$datas = array();
			@ini_set("zend.ze1_compatibility_mode", "0");
			$informations = array();
			$loaded=false;
			$aCurl = new Curl();
			$aCurl->timeout=2;
			$content = $aCurl->get($link);
			$flux=$content->body;
			if(!isset($this->parameters['nb_max_elements'])) $this->parameters['nb_max_elements']=0;
			if($flux && $content->headers['Status-Code'] == 200){
				$rss = new domDocument();
				$old_errors_value = false;
				if(libxml_use_internal_errors(true)){
					$old_errors_value = true;
				}
				$loaded=$rss->loadXML($flux);
				if(!count(libxml_get_errors())){
					if($loaded){
						//les infos sur le flux...
						//Flux RSS	
						$sxe = new SimpleXMLElement($flux);
						$ns=$sxe->getNamespaces(true);
						if(!isset($ns["dc"])) $ns["dc"] = '';
						$informations['items'] =array();
						if ($rss->getElementsByTagName("channel")->length > 0) {
							$channel = $rss->getElementsByTagName("channel")->item(0);
							$elements = array(
									'url'
							);
							$informations = $this->get_informations($channel,$elements,1);
							//on va lire les infos des items...
								
							$rss_items = $rss->getElementsByTagName("item");
							$elements = array(
									'title',
									'description',
									'link',
									'pubDate',
									'category',
									'content'
							);
							$count=0;
							for($i=0 ; $i<$rss_items->length ; $i++){
								if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
									$informations['items'][$count]=$this->get_informations($rss_items->item($i),$elements,false);
									if($ns["dc"]){
										$namespace_dc_date = $rss->getElementsByTagNameNS($ns["dc"], 'date')->item($i)->nodeValue;
										if($namespace_dc_date){
											$informations['items'][$count]['pubDate'] = str_replace(array("T","Z"), " ", $namespace_dc_date);
										}
										$namespace_dc_subject = $rss->getElementsByTagNameNS($ns["dc"], 'subject')->item($i)->nodeValue;
										if($namespace_dc_subject){
											$informations['items'][$count]['subject'] = $namespace_dc_subject;
										}
									}
									$count++;
								}
							}
							//Flux ATOM
						} elseif($rss->getElementsByTagName("feed")->length > 0) {
												
							$feed = $rss->getElementsByTagName("feed")->item(0);
							$atom_elements = array(
									'url',
							);
							$informations = $this->get_atom_informations($feed,$atom_elements,1);
							//on va lire les infos des entries...
							$informations['items'] =array();
							$entries = $rss->getElementsByTagName("entry");
							$atom_elements = array(
									'title',
									'link',
									'published',
									'content'
							);
							for($i=0 ; $i<$entries->length ; $i++){
								if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
									$informations['items'][]=$this->get_atom_informations($entries->item($i),$atom_elements,false);
								}
							}
						}
						foreach ($informations['items'] as $rss_item) {
							$data = array();
							$data["type"] = "rss";
							$data["title"] = $rss_item["title"];
							if(is_array($rss_item["description"])) {
								$data["summary"] = $rss_item["description"][0];
							} else {
								$data["summary"] = $rss_item["description"];
							}
							if(!isset($rss_item["content"])) $rss_item["content"] = '';
							$data["content"] = $rss_item["content"];
							$data["url"] = $rss_item["link"];
							if($rss_item["pubDate"]) $data["publication_date"] = date ( 'Y-m-d H:i:s' , strtotime($rss_item["pubDate"]));
							else $data["publication_date"] ="";
														
							$data["logo_url"] = $informations["url"];
							$data["descriptors"] = "";
							if(isset($rss_item["category"]) && is_array($rss_item["category"])){
								$data["tags"] = array_map("strip_tags", $rss_item["category"]);
							}else{
								$data["tags"] = strip_tags($rss_item["category"]);
							}
							$datas[] = $data;
						}
					}
				}else{
					libxml_clear_errors();
				}
			}
			libxml_use_internal_errors($old_errors_value);
			@ini_set("zend.ze1_compatibility_mode", "1");
			return $datas;
		}else{
			return false;
		}
	}
	
	protected function delete_media_tags($node) {
		
		//Cas particulier nom d'espace media (yahoo) : les balises sont doublées dans un noms d'espace "media"
		try {
			$items = $node->getElementsByTagNameNS('http://search.yahoo.com/mrss/','*');
			if($items->length>0){
				$node->removeChild($items->item(0));
			}
		} catch(Exception $e) {
		
		}
		
		return $node;
	}

	protected function get_informations($node,$elements,$first_only=false){
		global $charset;
		$informations = array();
		
		$node = $this->delete_media_tags($node);
		
		foreach($elements as $element){
			$items = $node->getElementsByTagName($element);
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
		global $charset;
		$informations = array();
		
		$node = $this->delete_media_tags($node);
		
		foreach($atom_elements as $atom_element){
			$items = $node->getElementsByTagName($atom_element);
			switch ($atom_element) {
				case "published" :
					$element = "pubDate";
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
	
	public function get_available_selectors(){
		global $msg;
		return array(
				"docwatch_selector_rss" => $msg['dsi_docwatch_selector_rss_select']
		);
	}


} // end of docwatch_datasource_notices_rss


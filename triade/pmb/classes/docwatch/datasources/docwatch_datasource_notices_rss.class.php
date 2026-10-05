<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_notices_rss.class.php,v 1.10 2020/06/22 12:34:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/datasources/docwatch_datasource_notices.class.php");
/**
 * class docwatch_datasource_notices_rss
 * 
 */
class docwatch_datasource_notices_rss extends docwatch_datasource_notices{

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
	
	protected function get_items_datas($items){
		$datas = array();
		
		foreach($items as $item) {
			$informations = array();
			$notice = new notice($item);
			
			//s'agit-il d'une notice de flux RSS ?
			$eformat=array();
			$eformat = explode(' ', $notice->eformat) ;
			if ($eformat[0]=='RSS' && ($notice->lien != '')) {
				$loaded=false;
				$aCurl = new Curl();
				$aCurl->timeout=2;
				$content = $aCurl->get($notice->lien);
				$flux=$content->body;
				if($flux && $content->headers['Status-Code'] == 200){
					$rss = new domDocument();
					$old_errors_value = false;
					if(libxml_use_internal_errors(true)){
						$old_errors_value = true;
					}
					$loaded=$rss->loadXML($flux);
				}
				if(!count(libxml_get_errors())){
					if($loaded){
						//les infos sur le flux...
						//Flux RSS
						if ($rss->getElementsByTagName("channel")->length > 0) {
							$channel = $rss->getElementsByTagName("channel")->item(0);
							$elements = array(
									'url'
							);
							$informations = $this->get_informations($channel,$elements,1);
							//on va lire les infos des items...
							$informations['items'] =array();
							$rss_items = $rss->getElementsByTagName("item");
							$elements = array(
									'title',
									'description',
									'link',
									'pubDate',
									'category',
							);
							for($i=0 ; $i<$rss_items->length ; $i++){
								if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
									$informations['items'][]=$this->get_informations($rss_items->item($i),$elements,false);
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
									'content',
							);
							for($i=0 ; $i<$entries->length ; $i++){
								if($this->parameters['nb_max_elements']==0 || $i < $this->parameters['nb_max_elements']){
									$informations['items'][]=$this->get_atom_informations($entries->item($i),$atom_elements,false);
								}
							}
						}
						foreach ($informations['items'] as $rss_item) {
							$data = array();
							$data["num_notice"] = "0";
							$data["type"] = "rss";
							$data["title"] = $rss_item["title"];
							$data["summary"] = $rss_item["description"];
							$data["content"] = $rss_item["description"];
							$data["url"] = $rss_item["link"];
							$data["publication_date"] = date ( 'Y-m-d H:i:s' , strtotime($rss_item["pubDate"]));
							$data["logo_url"] = $informations["url"];
							$data["descriptors"] = "";
							if(is_array($rss_item["category"])){
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
				libxml_use_internal_errors($old_errors_value);
			}
		}
		return $datas;
	}

	protected function get_informations($node,$elements,$first_only=false){
		global $charset;
		$informations = array();
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


} // end of docwatch_datasource_notices_rss


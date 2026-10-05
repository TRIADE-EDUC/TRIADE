<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_documents.class.php,v 1.7 2022/02/18 09:55:13 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_documents extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}
	
	protected function get_sub_selectors(){
		return array(
			"cms_module_common_selector_generic_article",
			"cms_module_common_selector_generic_section",
			"cms_module_common_selector_generic_portfolio_collection"
		);
	}	
		
	public function get_value(){
		if(!$this->value){
			$this->value = array();
			$type_selector= $this->get_selected_sub_selector();
			//en fonction de la source des doc, les requetes changent...
			switch($this->parameters['sub_selector']){
				//docs associés à un article
				case "cms_module_common_selector_generic_article" :
					$id_article = $type_selector->get_value();
					$id_article = intval($id_article);
					$query = "select document_link_num_document from cms_documents_links where document_link_type_object = 'article' and document_link_num_object = '".($id_article*1)."'";
					$result = pmb_mysql_query($query);
					
					$this->value['type_object'] = 'article';
					$this->value['num_object'] = $id_article;
					
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
						    $this->value['ids'][] = (int) $row->document_link_num_document;
						}
					}
					break;
				//doc associé à une rubrique
				case "cms_module_common_selector_generic_section" :
					$id_section = $type_selector->get_value();
					$id_section = intval($id_section);
					$query = "select document_link_num_document from cms_documents_links where document_link_type_object = 'section' and document_link_num_object = '".($id_section*1)."'";
					$result = pmb_mysql_query($query);
						
					$this->value['type_object'] = 'section';
					$this->value['num_object'] = $id_section;
						
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
						    $this->value['ids'][] = (int) $row->document_link_num_document;
						}
					}
					break;
				//doc associé d'une collection tout simplement
				case "cms_module_common_selector_generic_portfolio_collection" :
					$id_collection = $type_selector->get_value();
					$id_collection = intval($id_collection);
					$query = "select id_document from cms_documents where document_type_object = 'collection' and document_num_object = '".($id_collection*1)."'";
					$result = pmb_mysql_query($query);
						
					$this->value['type_object'] = 'collection';
					$this->value['num_object'] = $id_collection;
						
					if(pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
						    $this->value['ids'][] = (int) $row->id_document;
						}
					}
					break;
			}
		}
		return $this->value;
	}
}
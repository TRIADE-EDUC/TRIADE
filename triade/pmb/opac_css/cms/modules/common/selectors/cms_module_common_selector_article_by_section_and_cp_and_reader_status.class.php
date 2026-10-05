<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_article_by_section_and_cp_and_reader_status.class.php,v 1.1 2021/03/16 13:04:26 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
class cms_module_common_selector_article_by_section_and_cp_and_reader_status extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		
	}
	
	public function get_sub_selectors(){
		return array(
			"cms_module_common_selector_section",
			"cms_module_common_selector_type_article_filter",
		    "cms_module_common_selector_readers_status"
		);
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
		if(!$this->value){
		    $parent = new cms_module_common_selector_section($this->get_sub_selector_id("cms_module_common_selector_section"));
		    $id_rubrique = $parent->get_value();
		    
		    $cp = new cms_module_common_selector_type_article_filter($this->get_sub_selector_id("cms_module_common_selector_type_article_filter"));
		    $field = $cp->get_value();
		    
		    $articles = [];
		    $query = "SELECT id_article FROM cms_articles WHERE num_section = ". $id_rubrique . " AND article_num_type = " . $field["type"];
		    $result = pmb_mysql_query($query);
		    while ($row = pmb_mysql_fetch_assoc($result)) {
		        $articles[] = $row['id_article'];
		    }

		    $reader_status = new cms_module_common_selector_readers_status();
		    $num_status = $reader_status->get_value();
		    
	        $fields = new cms_editorial_parametres_perso($field['type']);
		    foreach ($articles as $article){
		        $fields->get_values($article);
                if(in_array($num_status,$fields->values[$field['field']])){
                    $this->value = $article;
                    break;
                }
		    }
		}
		return $this->value;
	}
}
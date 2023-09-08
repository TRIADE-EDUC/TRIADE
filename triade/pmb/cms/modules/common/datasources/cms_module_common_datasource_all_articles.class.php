<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_all_articles.class.php,v 1.11 2017-01-20 09:59:54 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_all_articles extends cms_module_common_datasource_list{
	
	protected $all_article_order = array();
	 
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array();
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"publication_date",
			"id_article",
			"article_title",
			"article_order"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$this->all_article_order = array();
		if($this->parameters["sort_by"] == 'article_order') {
			$this->get_datas_order(0);
		} else { 
			$query = "select id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date from cms_articles";
			if ($this->parameters["sort_by"] != "") {
				$query .= " order by ".$this->parameters["sort_by"];
				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
			}
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) > 0){
				while($row = pmb_mysql_fetch_object($result)){
					$this->all_article_order[] = $row->id_article;
				}
			}
		}
		$this->all_article_order = $this->filter_datas("articles",$this->all_article_order);
		if ($this->parameters["nb_max_elements"] > 0) $this->all_article_order = array_slice($this->all_article_order, 0, $this->parameters["nb_max_elements"]);
		
		return $this->all_article_order;
	}
	
	function get_datas_order($section_num) {		
		$query_section = "select id_section from cms_sections where section_num_parent=".$section_num." 
				order by section_order";
		if ($this->parameters["sort_order"] != "") $query_section .= " ".$this->parameters["sort_order"];	
		
		$result_section = pmb_mysql_query($query_section);
		if(pmb_mysql_num_rows($result_section)){
			while($row_section = pmb_mysql_fetch_object($result_section)){
								
				$query_article = "select id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date from cms_articles
					where num_section=".$row_section->id_section." order by article_order";
				if ($this->parameters["sort_order"] != "") $query_article .= " ".$this->parameters["sort_order"];
				
				$result_article = pmb_mysql_query($query_article);
				if(pmb_mysql_num_rows($result_article)){
					while($row_article = pmb_mysql_fetch_object($result_article)){
						$this->all_article_order[] = $row_article->id_article;
					}
				}
				// cette section a des enfants section ?
				$this->get_datas_order($row_section->id_section);
			}
		}
	}
}
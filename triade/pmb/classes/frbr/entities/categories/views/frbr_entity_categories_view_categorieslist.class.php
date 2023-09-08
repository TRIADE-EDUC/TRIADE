<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_categories_view_categorieslist.class.php,v 1.4 2018-06-13 10:34:01 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_categories_view_categorieslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for category in categories %}
<h3>{{category.name}}</h3>
<blockquote>{{category.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_categories_view_categorieslist_title"];
		$render_datas['categories'] = array();
		if(is_array($datas)){
			foreach($datas as $category_id){
				$render_datas['categories'][] = new authority(0, $category_id, AUT_TABLE_CATEG);
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_categories_view_title']
		);
		$categories = array(
			'var' => "categories",
			'desc' => $this->msg['frbr_entity_categories_view_categories_desc'],
			'children' => authority::get_properties(AUT_TABLE_CATEG,"categories[i]")
		);
		$format[] = $categories;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
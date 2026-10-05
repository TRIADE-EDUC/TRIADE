<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_categories_view_categorieslist.class.php,v 1.6 2021/03/01 11:04:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_categories_view_categorieslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for category in categories %}
<h3>{{category.libelle}}</h3>
<blockquote>{{category.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_categories_view_categorieslist_title"];
		$render_datas['categories'] = array();
		if(is_array($datas)){
			foreach($datas as $category_id){
				$render_datas['categories'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $category_id, 'type_object' => AUT_TABLE_CATEG]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_categories'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_categories'][$key])) {
		            $render_datas['grouped_categories'][$key] = [];
		        }
		        $render_datas['grouped_categories'][$key]['label'] = $group["label"];
		        $render_datas['grouped_categories'][$key]["values"] = [];
		        foreach ($group["values"] as $category_id) {
		            $render_datas['grouped_categories'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $category_id, 'type_object' => AUT_TABLE_CATEG]);
		        }
		    }
		    usort($render_datas['grouped_categories'], function ($item1, $item2) {
		        return $item1['label'] <=> $item2['label'];
		    });
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
		$format[] = array(
		    'var' => "grouped_categories",
		    'desc' => $this->msg['frbr_entity_categories_view_grouped_categories'],
		    'children' => [
		        [
		            'var' => "grouped_categories.key.label",
		            'desc' => $this->msg['frbr_entity_categories_view_grouped_categories_label']
		            
		        ],
		        [
		            'var' => "grouped_categories.key.values",
		            'desc' => $this->msg['frbr_entity_categories_view_grouped_categories_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
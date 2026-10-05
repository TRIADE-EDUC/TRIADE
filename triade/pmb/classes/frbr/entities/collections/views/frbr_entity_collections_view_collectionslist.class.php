<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_collections_view_collectionslist.class.php,v 1.4 2021/03/01 11:04:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_collections_view_collectionslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for collection in collections %}
<h3>{{collection.name}}</h3>
<blockquote>{{collection.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_collections_view_collectionslist_title"];
		$render_datas['collections'] = array();
		if(is_array($datas)){
			foreach($datas as $collection_id){
				$render_datas['collections'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $collection_id, 'type_object' => AUT_TABLE_COLLECTIONS]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_collections'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_collections'][$key])) {
		            $render_datas['grouped_collections'][$key] = [];
		        }
		        $render_datas['grouped_collections'][$key]['label'] = $group["label"];
		        $render_datas['grouped_collections'][$key]["values"] = [];
		        foreach ($group["values"] as $collection_id) {
		            $render_datas['grouped_collections'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $collection_id, 'type_object' => AUT_TABLE_COLLECTIONS]);
		        }
		    }
		    usort($render_datas['grouped_collections'], function ($item1, $item2) {
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
			'desc' => $this->msg['frbr_entity_collections_view_title']
		);
		$collections = array(
			'var' => "collections",
			'desc' => $this->msg['frbr_entity_collections_view_collections_desc'],
			'children' => authority::get_properties(AUT_TABLE_COLLECTIONS,"collections[i]")
		);
		$format[] = $collections;
		$format[] = array(
		    'var' => "grouped_collections",
		    'desc' => $this->msg['frbr_entity_authors_view_grouped_collections'],
		    'children' => [
		        [
		            'var' => "grouped_collections.key.label",
		            'desc' => $this->msg['frbr_entity_coollections_view_grouped_collections_label']
		            
		        ],
		        [
		            'var' => "grouped_collections.key.values",
		            'desc' => $this->msg['frbr_entity_collections_view_grouped_collections_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
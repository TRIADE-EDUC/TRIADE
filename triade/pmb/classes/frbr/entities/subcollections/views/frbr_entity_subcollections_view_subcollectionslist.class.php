<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_subcollections_view_subcollectionslist.class.php,v 1.4 2021/03/01 11:04:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_subcollections_view_subcollectionslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for subcollection in subcollections %}
<h3>{{subcollection.name}}</h3>
<blockquote>{{subcollection.comment}}</blockquote>
{% endfor %}
</div>";
	}
	
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_subcollections_view_subcollectionslist_title"];
		$render_datas['subcollections'] = array();
		if(is_array($datas)){
			foreach($datas as $subcollection_id){
				$render_datas['subcollections'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $subcollection_id, 'type_object' => AUT_TABLE_SUB_COLLECTIONS]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_subcollections'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_subcollections'][$key])) {
		            $render_datas['grouped_subcollections'][$key] = [];
		        }
		        $render_datas['grouped_subcollections'][$key]['label'] = $group["label"];
		        $render_datas['grouped_subcollections'][$key]["values"] = [];
		        foreach ($group["values"] as $subcollection_id) {
		            $render_datas['grouped_subcollections'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $subcollection_id, 'type_object' => AUT_TABLE_SUB_COLLECTIONS]);
		        }
		    }
		    usort($render_datas['grouped_subcollections'], function ($item1, $item2) {
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
			'desc' => $this->msg['frbr_entity_subcollections_view_title']
		);
		$subcollections = array(
			'var' => "subcollections",
			'desc' => $this->msg['frbr_entity_subcollections_view_subcollections_desc'],
			'children' => authority::get_properties(AUT_TABLE_SUB_COLLECTIONS,"subcollections[i]")
		);
		$format[] = $subcollections;
		$format[] = array(
		    'var' => "grouped_subcollections",
		    'desc' => $this->msg['frbr_entity_subcollections_view_grouped_subcollections'],
		    'children' => [
		        [
		            'var' => "grouped_subcollections.key.label",
		            'desc' => $this->msg['frbr_entity_subcollections_view_grouped_subcollections_label']
		            
		        ],
		        [
		            'var' => "grouped_subcollections.key.values",
		            'desc' => $this->msg['frbr_entity_subcollections_view_grouped_subcollections_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
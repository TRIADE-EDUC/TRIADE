<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_publishers_view_publisherslist.class.php,v 1.6 2023/12/07 15:02:48 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_publishers_view_publisherslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for publisher in publishers %}
<h3>{{publisher.name}}</h3>
<div>{{publisher.comment}}</div>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_publishers_view_publisherslist_title"];
		$render_datas['publishers'] = array();
		if(is_array($datas)){
			foreach($datas as $publisher_id){
				$render_datas['publishers'][]= authorities_collection::get_authority('authority', 0, ['num_object' => $publisher_id, 'type_object' => AUT_TABLE_PUBLISHERS]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_publishers'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_publishers'][$key])) {
		            $render_datas['grouped_publishers'][$key] = [];
		        }
		        $render_datas['grouped_publishers'][$key]['label'] = $group["label"];
		        $render_datas['grouped_publishers'][$key]["values"] = [];
		        foreach ($group["values"] as $publisher_id) {
		            $render_datas['grouped_publishers'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $publisher_id, 'type_object' => AUT_TABLE_PUBLISHERS]);
		        }
		    }
		    usort($render_datas['grouped_publishers'], function ($item1, $item2) {
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
			'desc' => $this->msg['frbr_entity_publishers_view_title']
		);
		$publishers = array(
			'var' => "publishers",
			'desc' => $this->msg['frbr_entity_publishers_view_publishers_desc'],
			'children' => authority::get_properties(AUT_TABLE_PUBLISHERS,"publishers[i]")
		);
		$format[] = $publishers;
		$format[] = array(
		    'var' => "grouped_publishers",
		    'desc' => $this->msg['frbr_entity_publishers_view_grouped_publishers'],
		    'children' => [
		        [
		            'var' => "grouped_publishers.key.label",
		            'desc' => $this->msg['frbr_entity_publishers_view_grouped_publishers_label']
		            
		        ],
		        [
		            'var' => "grouped_publishers.key.values",
		            'desc' => $this->msg['frbr_entity_publishers_view_grouped_publishers_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_view_workslist.class.php,v 1.8 2023/12/07 15:02:47 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_view_workslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for work in works %}
<h3>{{work.name}}</h3>
<div>{{work.comment}}</div>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_works_view_workslist_title"];
		$render_datas['works'] = array();
		if(is_array($datas)){
			foreach($datas as $work_id){
				$render_datas['works'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $work_id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
			}
		}
	    if(!empty($grouped_datas)){
	        $render_datas['grouped_works'] = [];
	        foreach($grouped_datas as $key => $group){
	            if (!isset($render_datas['grouped_works'][$key])) {
	                $render_datas['grouped_works'][$key] = [];
	            }
	            $render_datas['grouped_works'][$key]['label'] = $group["label"];
	            $render_datas['grouped_works'][$key]["values"] = [];
	            foreach ($group["values"] as $work_id) {
	                $render_datas['grouped_works'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $work_id, 'type_object' => AUT_TABLE_TITRES_UNIFORMES]);
	            }
	        }
		    usort($render_datas['grouped_works'], function ($item1, $item2) {
		        return $item1['label'] <=> $item2['label'];
		    });
	    }
		//on rappelle le tout...
	    return parent::render($render_datas, $grouped_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_works_view_title']
		);
		$works = array(
			'var' => "works",
			'desc' => $this->msg['frbr_entity_works_view_works_desc'],
			'children' => authority::get_properties(AUT_TABLE_TITRES_UNIFORMES, 'works[i]')
		);
		$format[] = $works;
		$format[] = array(
		    'var' => "grouped_works",
		    'desc' => $this->msg['frbr_entity_works_view_grouped_works'],
		    'children' => [
		        [
		            'var' => "grouped_works.key.label",
		            'desc' => $this->msg['frbr_entity_works_view_grouped_works_label']
		            
		        ],
		        [
		            'var' => "grouped_works.key.values",
		            'desc' => $this->msg['frbr_entity_works_view_grouped_works_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
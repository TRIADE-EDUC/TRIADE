<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_indexint_view_indexintlist.class.php,v 1.7 2023/12/07 15:02:47 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_indexint_view_indexintlist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for ind in indexint %}
<h3>{{ind.name}}</h3>
<div>{{ind.comment}}</div>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_indexint_view_indexintlist_title"];
		$render_datas['indexint'] = array();
		if(is_array($datas)){
			foreach($datas as $indexint_id){
				$render_datas['indexint'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $indexint_id, 'type_object' => AUT_TABLE_INDEXINT]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_indexint'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_indexint'][$key])) {
		            $render_datas['grouped_indexint'][$key] = [];
		        }
		        $render_datas['grouped_indexint'][$key]['label'] = $group["label"];
		        $render_datas['grouped_indexint'][$key]["values"] = [];
		        foreach ($group["values"] as $indexint_id) {
		            $render_datas['grouped_indexint'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $indexint_id, 'type_object' => AUT_TABLE_INDEXINT]);
		        }
		    }
		    usort($render_datas['grouped_indexint'], function ($item1, $item2) {
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
			'desc' => $this->msg['frbr_entity_indexint_view_title']
		);
		$indexint = array(
			'var' => "indexint",
			'desc' => $this->msg['frbr_entity_indexint_view_indexint_desc'],
			'children' => authority::get_properties(AUT_TABLE_INDEXINT,"indexint[i]")
		);
		$format[] = $indexint;
		$format[] = array(
		    'var' => "grouped_indexint",
		    'desc' => $this->msg['frbr_entity_indexint_view_grouped_indexint'],
		    'children' => [
		        [
		            'var' => "grouped_indexint.key.label",
		            'desc' => $this->msg['frbr_entity_indexint_view_grouped_indexint_label']
		            
		        ],
		        [
		            'var' => "grouped_indexint.key.values",
		            'desc' => $this->msg['frbr_entity_indexint_view_grouped_indexint_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
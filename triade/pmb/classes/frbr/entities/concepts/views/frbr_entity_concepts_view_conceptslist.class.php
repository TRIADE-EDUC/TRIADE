<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_view_conceptslist.class.php,v 1.3 2021/03/01 11:04:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_view_conceptslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for concept in concepts %}
<h3>{{concept.uri}}</h3>
<blockquote>{{concept.broaders_list}}</blockquote>
<blockquote>{{concept.narrowers_list}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_concepts_view_conceptslist_title"];
		$render_datas['concepts'] = array();
		if(is_array($datas)){
			foreach($datas as $concept_id){
				$render_datas['concepts'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $concept_id, 'type_object' => AUT_TABLE_CONCEPT]);
			}
		}
		if(!empty($grouped_datas)){
		    $render_datas['grouped_concepts'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_concepts'][$key])) {
		            $render_datas['grouped_concepts'][$key] = [];
		        }
		        $render_datas['grouped_concepts'][$key]['label'] = $group["label"];
		        $render_datas['grouped_concepts'][$key]["values"] = [];
		        foreach ($group["values"] as $concept_id) {
		            $render_datas['grouped_concepts'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $concept_id, 'type_object' => AUT_TABLE_CONCEPT]);
		        }
		    }
		    usort($render_datas['grouped_concepts'], function ($item1, $item2) {
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
			'desc' => $this->msg['frbr_entity_concepts_view_title']
		);
		$concepts = array(
			'var' => "concepts",
			'desc' => $this->msg['frbr_entity_concepts_view_concepts_desc'],
			'children' => authority::get_properties(AUT_TABLE_CONCEPT,"concepts[i]")
		);
		$format[] = $concepts;
		$format[] = array(
		    'var' => "grouped_concepts",
		    'desc' => $this->msg['frbr_entity_concepts_view_grouped_concepts'],
		    'children' => [
		        [
		            'var' => "grouped_concepts.key.label",
		            'desc' => $this->msg['frbr_entity_concepts_view_grouped_concepts_label']
		            
		        ],
		        [
		            'var' => "grouped_concepts.key.values",
		            'desc' => $this->msg['frbr_entity_concepts_view_grouped_concepts_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
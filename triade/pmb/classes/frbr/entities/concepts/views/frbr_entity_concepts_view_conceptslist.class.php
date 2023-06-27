<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_view_conceptslist.class.php,v 1.2 2018-06-13 10:34:01 vtouchard Exp $

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
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_concepts_view_conceptslist_title"];
		$render_datas['concepts'] = array();
		if(is_array($datas)){
			foreach($datas as $concept_id){
				$render_datas['concepts'][] = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
			}
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
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
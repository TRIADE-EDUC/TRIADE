<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_view.class.php,v 1.5 2023/12/07 15:02:46 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/skos/skos_concept.class.php");

class frbr_entity_concepts_view extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
<h3>{{concept.uri}}</h3>
<div>{{concept.broaders_list}}</div>
<div>{{concept.narrowers_list}}</div>
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_concepts_view_title"];
		$render_datas['concept'] = authorities_collection::get_authority('authority', 0, ['num_object' => $datas[0], 'type_object' => AUT_TABLE_CONCEPT]);
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_concepts_view_title']
		);
		$concept = array(
			'var' => "concept",
			'desc' => $this->msg['frbr_entity_concepts_view_label'],
			'children' => authority::get_properties(AUT_TABLE_CONCEPT, "concept")
		);
		$format[] = $concept;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
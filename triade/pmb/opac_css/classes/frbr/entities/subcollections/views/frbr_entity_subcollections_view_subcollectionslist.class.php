<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_subcollections_view_subcollectionslist.class.php,v 1.4 2018-07-26 15:25:52 tsamson Exp $

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
	
	public function render($datas){	
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
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
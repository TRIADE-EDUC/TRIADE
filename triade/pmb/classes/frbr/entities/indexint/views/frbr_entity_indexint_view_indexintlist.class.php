<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_indexint_view_indexintlist.class.php,v 1.3 2018-06-13 10:34:01 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_indexint_view_indexintlist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for ind in indexint %}
<h3>{{ind.name}}</h3>
<blockquote>{{ind.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_indexint_view_indexintlist_title"];
		$render_datas['indexint'] = array();
		if(is_array($datas)){
			foreach($datas as $indexint_id){
				$render_datas['indexint'][] = new authority(0, $indexint_id, AUT_TABLE_INDEXINT);
			}
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
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
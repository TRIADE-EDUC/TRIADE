<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_collections_view.class.php,v 1.4 2021/08/09 13:48:24 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_collections_view extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
<h3>{{collection.name}}</h3>
<blockquote>{{collection.comment}}</blockquote>
</div>";
	}
		
	public function render($datas, $grouped_datas = []){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_collections_view_title"];
		$render_datas['collection'] = new authority(0, $datas[0], AUT_TABLE_COLLECTIONS);
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_collections_view_title']
		);
		$collection = array(
			'var' => "collection",
			'desc' => $this->msg['frbr_entity_collections_view_label'],
			'children' => authority::get_properties(AUT_TABLE_COLLECTIONS,"collection")
		);
		$format[] = $collection;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
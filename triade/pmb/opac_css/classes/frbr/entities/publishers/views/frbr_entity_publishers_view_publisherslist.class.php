<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_publishers_view_publisherslist.class.php,v 1.4 2018-07-26 15:25:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_publishers_view_publisherslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for publisher in publishers %}
<h3>{{publisher.name}}</h3>
<blockquote>{{publisher.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas){	
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
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
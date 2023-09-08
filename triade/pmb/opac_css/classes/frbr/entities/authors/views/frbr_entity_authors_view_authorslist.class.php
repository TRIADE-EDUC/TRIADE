<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_view_authorslist.class.php,v 1.3 2018-07-26 15:25:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/author.class.php");

class frbr_entity_authors_view_authorslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for author in authors %}
<h3>{{author.name}}</h3>
<blockquote>{{author.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_authors_view_authorslist_title"];
		$render_datas['authors'] = array();
		if(is_array($datas)){
			foreach($datas as $author){
				$render_datas['authors'][] = authorities_collection::get_authority('authority', 0, ['num_object' => $author, 'type_object' => AUT_TABLE_AUTHORS]);
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_authors_view_title']
		);
		$works = array(
			'var' => "authors",
			'desc' => $this->msg['frbr_entity_authors_view_authors_desc'],
			'children' => authority::get_properties(AUT_TABLE_AUTHORS,"authors[i]"),
		);
		$format[] = $works;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
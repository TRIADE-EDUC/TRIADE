<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_view.class.php,v 1.4 2019-02-20 14:03:33 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
require_once($class_path."/author.class.php");

class frbr_entity_authors_view extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
<h3>{{author.name}}</h3>
<blockquote>{{author.comment}}</blockquote>
</div>";
	}
		
	public function render($datas){	
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_authors_view_title"];
		$render_datas['author'] = new authority(0, $datas[0], AUT_TABLE_AUTHORS);
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_authors_view_title']
		);
		$author = array(
			'var' => "author",
			'desc' => $this->msg['frbr_entity_authors_view_label'],
			'children' => $this->prefix_var_tree(auteur::get_format_data_structure(),"author")
		);
		$format[] = $author;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
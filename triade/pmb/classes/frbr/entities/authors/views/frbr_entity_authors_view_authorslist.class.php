<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authors_view_authorslist.class.php,v 1.4 2021/02/26 15:56:28 moble Exp $

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
		
	public function render($datas, $grouped_datas = []){	
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
		if(!empty($grouped_datas)){
		    $render_datas['grouped_authors'] = [];
		    foreach($grouped_datas as $key => $group){
		        if (!isset($render_datas['grouped_authors'][$key])) {
		            $render_datas['grouped_authors'][$key] = [];
		        }
		        $render_datas['grouped_authors'][$key]['label'] = $group["label"];
		        $render_datas['grouped_authors'][$key]["values"] = [];
		        foreach ($group["values"] as $author_id) {
		            $render_datas['grouped_authors'][$key]["values"][] = authorities_collection::get_authority('authority', 0, ['num_object' => $author_id, 'type_object' => AUT_TABLE_AUTHORS]);
		        }
		    }
		    usort($render_datas['grouped_authors'], function ($item1, $item2) {
		        return $item1['label'] <=> $item2['label'];
		    });
		}
		//on rappelle le tout...
		return parent::render($render_datas, $grouped_datas = []);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_authors_view_title']
		);
		$authors = array(
			'var' => "authors",
			'desc' => $this->msg['frbr_entity_authors_view_authors_desc'],
			'children' => authority::get_properties(AUT_TABLE_AUTHORS,"authors[i]"),
		);
		$format[] = $authors;
		$format[] = array(
		    'var' => "grouped_authors",
		    'desc' => $this->msg['frbr_entity_authors_view_grouped_authors'],
		    'children' => [
		        [
		            'var' => "grouped_authors.key.label",
		            'desc' => $this->msg['frbr_entity_authors_view_grouped_authors_label']
		            
		        ],
		        [
		            'var' => "grouped_authors.key.values",
		            'desc' => $this->msg['frbr_entity_authors_view_grouped_authors_values']
		            
		        ]
		    ]
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
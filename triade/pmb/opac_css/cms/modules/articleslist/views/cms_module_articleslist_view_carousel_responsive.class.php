<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_articleslist_view_carousel_responsive.class.php,v 1.9.2.1 2025/01/17 10:40:42 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_articleslist_view_carousel_responsive extends cms_module_common_view_carousel_responsive {
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<ul id='carousel_{{id}}'>
	{% for record in records %}
		<li class='{{id}}_item'>
			<a href='{{record.link}}' alt='{{record.title}}' title='{{record.title}}'>
				<img src='{% if record.logo.exists %}{{record.logo.vign}}{% else %}{{no_image_url}}{% endif %}'/>
				<br />
			</a>
		</li>
	{% endfor %}
</ul>
";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_articleslist_view_link'>".$this->format_text($this->msg['cms_module_articleslist_view_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("article");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		$this->save_constructor_link_form("article");
		return parent::save_form();
	}
	
	public function render($datas) {
		$render_datas = array();
		$render_datas['records']=array();
		$links = ["article" => $this->get_constructed_link("article", "!!id!!")];
		if (is_countable($datas['articles'])) {
    		for($i=0; $i < count($datas['articles']); $i++) {
    			$article = cms_provider::get_instance("article", $datas['articles'][$i]);
    			$render_datas['records'][] = $article->format_datas($links);
    		}
		}
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){
		$datas = cms_article::get_format_data_structure("article",false);
		$datas[] = array(
			'var' => "link",
			'desc'=> $this->msg['cms_module_articleslist_view_carousel_link_desc']
		);
		$format_datas = array(
			array(
				'var' => "records",
				'desc' => $this->msg['cms_module_carousel_view_carousel_records_desc'],
				'children' => $this->prefix_var_tree($datas,"records[i]")
			)
		);
		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}
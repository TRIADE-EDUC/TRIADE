<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sectionslist_view_carousel.class.php,v 1.11.2.2 2025/01/21 15:29:49 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sectionslist_view_carousel extends cms_module_carousel_view_carousel{


	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<ul id='carousel_{{id}}'>
	{% for section in sections %}
		<li class='{{id}}_item'>
			<a href='{{section.link}}' title='{{section.title}}'>
				<img src='{{section.vign}}' alt=''/>
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
				<label for='cms_module_sectionslist_view_link'>".$this->format_text($this->msg['cms_module_sectionslist_view_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("section");
		$form.="
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}

	public function save_form(){
		$this->save_constructor_link_form("section");
		return parent::save_form();
	}

	public function render($datas){
		$sections = array();
		$links = [
		    "article" => $this->get_constructed_link("article", "!!id!!"),
		    "section" => $this->get_constructed_link("section", "!!id!!")
		];
		$local_ids = $datas;
		if (isset($datas["sections"])) {
		    $local_ids = $datas["sections"];
		}
		if (is_countable($local_ids)) {
			for($i=0 ; $i<count($local_ids) ; $i++){
				$section = new cms_section($local_ids[$i]);
				$infos = $section->format_datas($links);
				$sections[]= $infos;
			}
		}
		return parent::render(array('sections' => $sections));
	}

	public function get_format_data_structure(){
		$datas = cms_section::get_format_data_structure(false,false);
		$datas[] = array(
			'var' => "link",
			'desc'=> $this->msg['cms_module_sectionslist_view_carousel_link_desc']
		);

		$format_datas = array(
			array(
				'var' => "sections",
				'desc' => $this->msg['cms_module_sectionslist_view_carousel_sections_desc'],
				'children' => $this->prefix_var_tree($datas,"sections[i]")
			)
		);
		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
}
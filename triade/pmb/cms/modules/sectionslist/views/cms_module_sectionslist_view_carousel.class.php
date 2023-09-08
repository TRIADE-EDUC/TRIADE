<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_sectionslist_view_carousel.class.php,v 1.5 2017-07-27 14:50:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_sectionslist_view_carousel extends cms_module_carousel_view_carousel{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
<ul id='carousel_{{id}}'>
	{% for section in sections %}
		<li class='{{id}}_item'>
			<a href='{{section.link}}' alt='{{section.title}}' title='{{section.title}}'>
				<img src='{{section.vign}}'/>
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
	
	public function render($ids){
		$datas = array();
		for($i=0 ; $i<count($ids) ; $i++){
			$section = new cms_section($ids[$i]);
			$infos = $section->format_datas(false,false);
			$infos['link']=$this->get_constructed_link("section",$infos['id']);
			$datas[]= $infos;
		}
		return parent::render(array('sections' => $datas));
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
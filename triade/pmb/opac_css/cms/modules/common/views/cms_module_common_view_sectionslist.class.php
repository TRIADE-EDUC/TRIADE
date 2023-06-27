<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_sectionslist.class.php,v 1.13 2018-08-24 08:44:59 plmrozowski Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_sectionslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for section in sections %}
<h3>{{section.title}}</h3>
<img src='{{section.logo.large}}' alt=''/>
<blockquote>{{section.resume}}</blockquote>
<blockquote>{{section.content}}</blockquote>
{% endfor %}
</div>";
	}
	
	public function get_form(){
		global $msg;
		
		if(!isset($this->parameters['load_articles_data'])) $this->parameters['load_articles_data'] = 0;
		
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_sectionslist_link_section'>".$this->format_text($this->msg['cms_module_common_view_sectionslist_build_section_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("section");
		$form.="
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_sectionslist_link_article'>".$this->format_text($this->msg['cms_module_common_view_sectionslist_build_article_link'])."</label>
			</div>
			<div class='colonne-suite'>";
		$form.= $this->get_constructor_link_form("article");
		$form.="	
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_sectionslist_load_articles_data'>".$this->format_text($this->msg['cms_module_common_view_sectionslist_load_articles_data'])."</label>
			</div>
			<div class='colonne-suite'>
				".$msg[39]." <input type='radio' name='cms_module_common_view_sectionslist_load_articles_data' value='0' ".(!$this->parameters['load_articles_data'] ? "checked='checked'" : "")." />
				".$msg[40]." <input type='radio' name='cms_module_common_view_sectionslist_load_articles_data' value='1' ".($this->parameters['load_articles_data'] ? "checked='checked'" : "")." />
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_sectionslist_load_articles_data;
		
		$this->save_constructor_link_form("section");
		$this->save_constructor_link_form('article');
		$this->parameters['load_articles_data'] = $cms_module_common_view_sectionslist_load_articles_data+0;
		return parent::save_form();
	}
	
	public function render($datas){
		if(!isset($this->parameters['load_articles_data'])) $this->parameters['load_articles_data'] = 0;
		
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = "Liste de rubriques";
		$render_datas['sections'] = array();
		if(is_array($datas) && count($datas)){
			foreach($datas as $section){
				$cms_section = cms_provider::get_instance("section",$section);
				//Dans le cas d'une liste de rubriques affichée via un template django, on écrase les valeurs de lien définies par celles du module
				if($this->parameters['links']['section']['var'] && $this->parameters['links']['section']['page']){
					$cms_section->set_var_name($this->parameters['links']['section']['var']);
					$cms_section->set_num_page($this->parameters['links']['section']['page']);
					$cms_section->update_permalink();
				}
				if($this->parameters['load_articles_data']) {
					$infos= $cms_section->format_datas(true, true, true, true);
				} else {
					$infos= $cms_section->format_datas(true, false, true, true);
				}
				$infos['link'] = $this->get_constructed_link("section",$section);
				if (!empty($infos['articles'])) {
					foreach ($infos['articles'] as $i=>$article) {
						$infos['articles'][$i]['link'] = $this->get_constructed_link("article",$article["id"]);
					}
				}
				if($cms_section->num_parent) {
					$infos['parent']['link'] = $this->get_constructed_link("section",$infos['parent']["id"]);
				}
				if(count($infos['children'])) {
					foreach ($infos['children'] as $i=>$child) {
						$infos['children'][$i]['link'] = $this->get_constructed_link("section",$child["id"]);
					}
				}
				$render_datas['sections'][]=$infos;
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['cms_module_common_view_title']
		);
		$sections = array(
			'var' => "sections",
			'desc' => $this->msg['cms_module_common_view_section_desc'],
			'children' => $this->prefix_var_tree(cms_section::get_format_data_structure(true, true, true, true),"sections[i]")
		);
		$sections['children'][] = array(
			'var' => "sections[i].link",
			'desc'=> $this->msg['cms_module_common_view_section_link_desc']
		);
		foreach ($sections['children'] as $i => $section) {
			if($section['var'] == 'sections[i].parent') {
				$sections['children'][$i]['children'][] = array(
						'var' => "sections[i].parent.link",
						'desc'=> $this->msg['cms_module_common_view_section_link_desc']
				);
			}
			if($section['var'] == 'sections[i].children') {
				$sections['children'][$i]['children'][] = array(
						'var' => "sections[i].children[i].link",
						'desc'=> $this->msg['cms_module_common_view_section_link_desc']
				);
			}
			if($section['var'] == 'sections[i].articles') {
				$sections['children'][$i]['children'][] = array(
						'var' => "sections[i].articles[i].link",
						'desc'=> $this->msg['cms_module_common_view_article_link_desc']
				);
			}
		}
		$format[] = $sections;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_articleslist.class.php,v 1.12 2018-05-16 14:18:35 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_articleslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for article in articles %}
<h3>{{article.title}}</h3>
<img src='{{article.logo.large}}'/>
<blockquote>{{article.resume}}</blockquote>
<blockquote>{{article.content}}</blockquote>
{% endfor %}
</div>";
	}
	
	public function get_form(){
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_articleslist_view_link'>".$this->format_text($this->msg['cms_module_common_view_articleslist_build_article_link'])."</label>
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
	
	public function render($datas){	
		$render_datas = $this->get_render_datas($datas);
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	protected function get_render_datas($datas) {
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = "Liste d'articles";
		$render_datas['articles'] = array();
		if(is_array($datas)){
			foreach($datas as $article){
				$cms_article = new cms_article($article);

				//Dans le cas d'une liste d'articles affichée via un template django, on écrase les valeurs de lien définies par celles du module
				if($this->parameters['links']['article']['var'] && $this->parameters['links']['article']['page']){
					$cms_article->set_var_name($this->parameters['links']['article']['var']);
					$cms_article->set_num_page($this->parameters['links']['article']['page']);
					$cms_article->update_permalink();
				}
				$infos= $cms_article->format_datas();
				$infos['link'] = $this->get_constructed_link("article",$article);
				$render_datas['articles'][]=$infos;
			}
		}
		return $render_datas;
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['cms_module_common_view_title']
		);
		$sections = array(
			'var' => "articles",
			'desc' => $this->msg['cms_module_common_view_articles_desc'],
			'children' => $this->prefix_var_tree(cms_article::get_format_data_structure(),"articles[i]")
		);
		$sections['children'][] = array(
			'var' => "articles[i].link",
			'desc'=> $this->msg['cms_module_common_view_article_link_desc']
		);
		$format[] = $sections;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_articleslist.class.php,v 1.19.2.1 2025/01/08 14:32:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_articleslist extends cms_module_common_view_django{


	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for article in articles %}
<h3>{{article.title}}</h3>
<img src='{{article.logo.large}}' alt=''/>
<div>{{article.resume}}</div>
<div>{{article.content}}</div>
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

		// Données de la pagination
		if(isset($datas['paging']) && $datas['paging']['activate']) {
		    $render_datas['paging'] = $datas['paging'];
		}
		$links = ["article" => $this->get_constructed_link("article", "!!id!!")];

		if(is_array($datas)){
		    $articles = isset($datas["articles"]) ? $datas["articles"] : $datas;
		    foreach($articles as $article){
				$render_datas['articles'][]= new cms_editorial_data($article, 'article', $links);
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
		$format[] = array(
		    'var' => "paginator",
		    'desc' => $this->msg['cms_module_common_view_list_paging_title'],
		    'children' => array(
		        array(
		            'var' => "paginator.paginator",
		            'desc' => $this->msg['cms_module_common_view_list_paging_paginator_title']
		        ),
		        array(
		            'var' => "paginator.nbPerPageSelector",
		            'desc' => $this->msg['cms_module_common_view_list_paging_nb_per_page_title']
		        ),
		        array(
		            'var' => "paginator.navigator",
		            'desc' => $this->msg['cms_module_common_view_list_paging_navigator_title']
		        )
		    )
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}
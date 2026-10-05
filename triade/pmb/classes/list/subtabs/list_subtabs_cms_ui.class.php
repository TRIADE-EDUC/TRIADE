<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_subtabs_cms_ui.class.php,v 1.1.10.1 2024/11/29 07:40:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_subtabs_cms_ui extends list_subtabs_ui {
	
	public function get_title() {
		global $msg;
		
		$title = "";
		switch (static::$categ) {
			case 'build':
				$title .= $msg['cms_menu_build_block'];
				break;
			case 'pages':
				$title .= $msg['cms_menu_pages'];
				break;
			case 'frbr_pages':
				$title .= $msg['frbr_pages_menu'];
				break;
			case 'editorial':
				$title .= $msg['cms_menu_editorial'];
				break;
			case 'section':
				$title .= $msg['cms_menu_editorial_section'];
				break;
			case 'article':
				$title .= $msg['cms_menu_editorial_article'];
				break;
			case 'collection':
				$title .= $msg['cms_menu_editorial_collection'];
				break;
		}
		return $title;
	}
	
	public function get_sub_title() {
		global $msg, $id;
		
		$sub_title = "";
		switch (static::$categ) {
			case 'pages':
				switch (static::$sub) {
					case 'edit':
						$sub_title .= (!$id ? $msg["cms_new_page_form_title"]:$msg["cms_page_form_title"]);
						break;
					case 'save':
						$sub_title .= $msg["cms_menu_page_add"];
						break;
					case 'del':
						$sub_title .= $msg["cms_menu_page_delete"];
						break;
					default:
						$sub_title .= $msg["cms_menu_page_list"];
						break;
				}
				break;
			case 'frbr_pages':
				switch (static::$sub) {
					case 'edit':
						$sub_title .= (!$id ? $msg["frbr_page_add"]:$msg["frbr_page_edit"]);
						break;
					case 'build':
						$sub_title .= $msg["frbr_page_tree_build"];
						break;
					case 'cadres':
						$sub_title .= $msg["cms_build_cadres"];
						break;
					default:
						$sub_title .= $msg["frbr_page_list_menu"];
						break;
				}
				break;
			case 'editorial':
				$sub_title .= $msg["cms_menu_editorial_sections_list"];
				break;
			case 'section':
				switch (static::$sub) {
					case 'edit':
						$sub_title .= ($id!= "new" ? $msg["cms_section_form_title"]:$msg["cms_new_section_form_title"]);
						break;
					case 'save':
						$sub_title .= $msg["cms_menu_editorial_sections_add"];
						break;
					case 'delete':
						$sub_title .= $msg["cms_menu_editorial_sections_delete"];
						break;
					default:
						$sub_title .= $msg["cms_menu_editorial_sections_list"];
						break;
				}
				break;
			case 'article':
				switch (static::$sub) {
					case 'edit':
						$sub_title .= ($id!= "new" ? $msg["cms_article_form_title"]:$msg["cms_new_article_form_title"]);
						break;
					case 'save':
						$sub_title .= $msg["cms_menu_editorial_articles_add"];
						break;
					case 'delete':
						$sub_title .= $msg["cms_menu_editorial_articles_delete"];
						break;
					default:
						$sub_title .= $msg["cms_menu_editorial_articles_list"];
						break;
				}
				break;
			case 'collection':
				if(static::$sub == 'documents') {
					$sub_title .= " > documents ";
				}
				$sub_title .= $msg['cms_menu_editorial_sections_list'];
				break;
			default:
				$sub_title .= parent::get_sub_title();
				break;
		}
		return $sub_title;
	}
	
	protected function _init_subtabs() {
		switch (static::$categ) {
			case 'build':
				//Construction
				$this->add_subtab('block', 'cms_menu_build_page_layout');
				break;
			case 'frbr_pages':
				//Pages FRBR
				$this->add_subtab('list', 'frbr_pages_menu');
				$this->add_subtab('cadres', 'cms_build_cadres');
				break;
		}
	}
}
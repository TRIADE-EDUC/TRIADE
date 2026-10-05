<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_cms_ui.class.php,v 1.3.8.1 2025/04/10 06:56:04 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_tabs_cms_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $cms_active;
		
		if(SESSrights & CMS_BUILD_AUTH) {
			if($cms_active == 1) {
				$this->add_tab('cms_menu_build', 'build', 'cms_menu_build_block', 'block');
    			$this->add_tab('cms_menu_build', 'pages', 'cms_menu_pages', 'list');
			} elseif ($cms_active == 2) {
				$this->add_tab('cms_menu_build', 'portal', 'cms_menu_build_block');
				$this->add_tab('cms_menu_build', 'toolkits', 'cms_build_toolkits');
			}
			$this->add_tab('cms_menu_build', 'frbr_pages', 'frbr_pages_menu', 'list');
		}
		if($cms_active) {
			$this->add_tab('cms_menu_editorial', 'editorial', 'cms_menu_editorial_gest', 'list');
			$this->add_tab('cms_menu_editorial', 'section', 'cms_new_section_form_title', 'edit', '&id=new');
			$this->add_tab('cms_menu_editorial', 'article', 'cms_new_article_form_title', 'edit', '&id=new');
			$this->add_tab('cms_menu_editorial', 'collection', 'cms_collections_form_title');
		}
		if($cms_active) {
			$modules_parser = new cms_modules_parser();
			$managed_modules = $modules_parser->get_managed_modules();
			foreach($managed_modules as $managed_module){
				$this->add_tab('cms_manage_module_menu', 'manage', $managed_module['name'], $managed_module['sub'], '&action=get_form');
			}
		}
	}
}
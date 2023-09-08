<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.31 2018-09-20 09:45:59 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/cms/cms_editorial_tree.class.php");
require_once($class_path."/cms/cms_articles.class.php");
require_once($class_path."/cms/cms_section.class.php");
require_once($class_path."/cms/cms_article.class.php");
require_once($class_path."/cms/cms_logo.class.php");
require_once($class_path."/cms/cms_build.class.php");
require_once($class_path."/cms/cms_pages.class.php");
require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");
require_once($class_path."/frbr/frbr_page.class.php");

switch($categ){
	case 'list_sections' :
	//	header('Content-type: application/json;charset=utf-8');
		$sections = new cms_editorial_tree();
		print $sections->get_json_list();
		break;
	case 'update_section' :
		header('Content-type: text/html;charset='.$charset);
		$sections = new cms_editorial_tree();
		$result = $sections->update_children($new_children,$num_parent);
		print $result;
		break;
	case "get_tree" :
		header('Content-type: text/html;charset='.$charset);
		print cms_editorial_tree::get_tree();
		break;
	case "get_infos" :
		header('Content-type: text/html;charset='.$charset);
		switch($type){
			case "section" :
				$section = new cms_section($id);
				$entity_locking = new entity_locking($id, TYPE_CMS_SECTION);
				if($entity_locking->is_locked()){
				    print $entity_locking->get_locked_form();
				    break;
				}
				print $section->get_ajax_form("cms_section_edit","cms_section_edit");
				$entity_locking->lock_entity();
				break;
			case "article" :
				$article = new cms_article($id);
				$entity_locking = new entity_locking($id, TYPE_CMS_ARTICLE);
				if($entity_locking->is_locked()){
				    print $entity_locking->get_locked_form();
				    break;
				}
				print $article->get_ajax_form("cms_article_edit","cms_article_edit");
				$entity_locking->lock_entity();
				break;
			case "list_articles" :
				$articles = new cms_articles($id);
				print $articles->get_tab();
				break;
		}
		break;
	case "save_section" :
		//header('Content-type: text/html;charset=iso-8859-1');
		$section = new cms_section();
		$section->get_from_form();
		$section->save();
		break;
	case "save_article" :
		//header('Content-type: text/html;charset=iso-8859-1');
		$article = new cms_article();
		$article->get_from_form();
		$article->save();
		break;
	case "delete_section" :
		$section = new cms_section($id);
		$res = $section->delete($force_delete);
		if($res!==true){
			$result =array(
				"status" => "need_approval",
				"error_message" => encoding_normalize::utf8_normalize($res)
			);
		}else{
			$result = array(
				'status' => "ok"
			);
		}
		print json_encode($result);
		break;
	case "delete_article" :
		$article = new cms_article($id);
		$res = $article->delete();
		if($res!==true){
			$result =array(
				"status" => "ko",
				"error_message" => encoding_normalize::utf8_normalize($res)
			);
		}else{
			$result = array(
				'status' => "ok"
			);
		}
		print json_encode($result);
		break;
	case "duplicate_section" :
		$section = new cms_section($id);
		$section->duplicate($recursive);
		break;
	case "duplicate_article" :
		$article = new cms_article($id);
		$article->duplicate();
		break;
	case "edit_logo" :
		$logo = new cms_logo($id,$quoi);
		print $logo->get_field();
		break;
	case 'update_article' :
		header('Content-type: text/html;charset='.$charset);
		$articles = explode(",",$articles);
		$order = 1;
		foreach($articles as $id_article){
			$article = new cms_article($id_article);
			$article->update_parent_section($num_section,$order);
			$order++;
		}
		break;
	case "build" :
		if (SESSrights & CMS_BUILD_AUTH) {
			switch($action){
				case "save":			
					$cms_build=new cms_build();
					ajax_http_send_response($cms_build->save_opac(unserialize(stripslashes($cms_build_info)),unserialize(stripslashes($cms_data))));						
				break;
			}
		}
		break;
	case "module" :
		include($base_path."/cms/ajax/modules/main.inc.php");
		break;
	case "pages" :
		if (SESSrights & CMS_BUILD_AUTH) {
			switch($sub){
				case "save":			
					$cms_page=new cms_page($id);
					$cms_page->get_from_form();			
					$cms_page->save();			
					$cms_pages=new cms_pages();		
					ajax_http_send_response($cms_pages->get_list($cms_build_pages_ajax_tpl,$cms_build_pages_tpl_item));
				break;
				case "edit":				
					$cms_page=new cms_page($id);
					ajax_http_send_response ($cms_page->get_form(1));		
				break;
				case "page_save_classement" :
					$cms= new cms_page();
					$cms->save_page_classement($id_page,$classement);	
					$cms_pages = new cms_pages();
					ajax_http_send_response($cms_pages->get_list($cms_build_pages_ajax_tpl,$cms_build_pages_tpl_item));
				break;
				case "del":			
					$cms_page=new cms_page($id);	
					$cms_page->delete();			
					$cms_pages=new cms_pages();		
					ajax_http_send_response($cms_pages->get_list($cms_build_pages_ajax_tpl,$cms_build_pages_tpl_item));
				break;
					
			}
		}
		break;	
	case "versions" :
		if (SESSrights & CMS_BUILD_AUTH) {
			switch($sub){
				case "save":			
					$cms_build=new cms_build();				
					$cms_build->save_version_form($id);
					ajax_http_send_response ($cms_build->build_versions_list_ajax());	
				break;			
				case "del_version":			
					$cms_build=new cms_build();
					ajax_http_send_response ($cms_build->version_delete($id));		
				break;			
				case "del_cms":			
					$cms_build=new cms_build();
					$cms_build->cms_delete($id);
					ajax_http_send_response ($cms_build->build_versions_list_ajax());		
				break;
				case "edit":			
					$cms_build=new cms_build();						
					ajax_http_send_response ($cms_build->get_version_form($id,1));		
				break;
				
			}
		}
		break;
	case "get_type_form" :
		ajax_http_send_response(cms_editorial_types::get_editable_form($id,$elem,$type_id));
		break;
	case "documents" :
	case "document" :
		include($base_path."/cms/ajax/documents/main.inc.php");
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case "toolkits" :
		if (SESSrights & CMS_BUILD_AUTH) {
			switch($action){
				case "save":
					if(is_array($cms_toolkits) && count($cms_toolkits)) {
						foreach ($cms_toolkits as $name=>$toolkit) {
							$cms_toolkit = new cms_toolkit($name);
							$cms_toolkit->set_active($toolkit['active']);
							$cms_toolkit->set_data((array) $toolkit['data']);
							$cms_toolkit->save();
						}
					}
					ajax_http_send_response(cms_toolkits::get_json_title());
					break;
			}
		}
		break;
	case "frbr_pages" :
		if(!isset($autoloader)) {
			$autoloader = new autoloader();
		}
		$autoloader->add_register("frbr_entities",true);
		if (SESSrights & CMS_BUILD_AUTH) {			
			switch($sub){
				case "get_form":
					$frbr_page = new frbr_page($num_page);
					ajax_http_send_response($frbr_page->get_form());
					break;
				case "parameters_form":
					ajax_http_send_response(frbr_page::get_parameters_form($type));
					break;				
			}
		}
		break;
	case "frbr_entities" :
		if (SESSrights & CMS_BUILD_AUTH) {
			include($base_path."/cms/ajax/frbr_entities/main.inc.php");
		}
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("cms",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'collection' :
	case 'collections' :
		include($base_path."/cms/ajax/collections/main.inc.php");
		break;
	case 'grid' :
		require_once($class_path."/grid.class.php");
		grid::proceed($datas);
		break;
}	
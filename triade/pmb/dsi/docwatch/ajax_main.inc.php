<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.49 2024/01/26 13:15:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $action, $type, $form, $msg, $charset, $PMBuserid;
global $id, $watch_id, $num_watch, $item_id, $notice_id, $class, $deflt_docwatch_watch_filter_deleted;
global $title, $ttl, $logo_url, $desc, $owner, $allowed_users, $parent;
global $record_status, $record_types, $indexation_lang, $record_default_lang, $watch_record_is_new;
global $article_type, $article_parent, $article_status, $section_type, $section_parent, $section_status, $boolean_expression;
global $watch_rss_link, $watch_rss_lang, $watch_rss_copyright, $watch_rss_editor, $watch_rss_webmaster, $watch_rss_image_title, $watch_rss_image_website;
global $className;

require_once($class_path."/docwatch/docwatch_watches.class.php");
require_once($class_path."/docwatch/docwatch_item.class.php");
require_once($class_path."/docwatch/docwatch_logo.class.php");
require_once($class_path."/encoding_normalize.class.php");


switch($sub) {
	case "watches":
		switch($action) {
			case "get_datas":
				$docwatch_watches = new docwatch_watches(0);
				print encoding_normalize::json_encode(array($docwatch_watches));
				break;
			case "save_category" :
				$docwatch_category = new docwatch_category($id);
				$docwatch_category->set_parent(stripslashes($parent));
				$docwatch_category->set_title(stripslashes($title));
				$result = $docwatch_category->save();
				$response = "";
				if($docwatch_category->get_id()){
					$response = new docwatch_watches($docwatch_category->get_id());
				}
			
				$response = array(
					'result' => $result,
					'elementId' => $docwatch_category->get_id(),
					'response' => $response
				);
				print encoding_normalize::json_encode($response);
				break;
			case "delete_category":
				$docwatch_category = new docwatch_category($id);
				$result = $docwatch_category->delete();
				$response = "";
				if(!$result){
					$response = $docwatch_category->get_error();
				}
				$response = array(
					'result' => $result,
					'elementId' => $docwatch_category->get_id(),
					'response' => $response
				);
				print encoding_normalize::json_encode($response);
				break;
			case "save_watch":
				$docwatch_watch = new docwatch_watch($id);
				if(($docwatch_watch->get_id() != 0 && $docwatch_watch->check_rights()) || ($docwatch_watch->get_id() == 0 && (in_array(SESSuserid, $allowed_users)) || ($PMBuserid==1))){
					$docwatch_watch->set_title(stripslashes($title));
					$docwatch_watch->set_ttl(stripslashes($ttl));
					$docwatch_watch->set_logo_url(stripslashes($logo_url));
					$docwatch_watch->set_desc(stripslashes($desc));
					$docwatch_watch->set_owner(stripslashes($owner));
					if(!$allowed_users){
						$allowed_users = array();
					}
					$docwatch_watch->set_allowed_users($allowed_users);
					$docwatch_watch->set_num_category(stripslashes($parent));
					
					$docwatch_watch->set_record_default_status(stripslashes($record_status));
					$docwatch_watch->set_record_default_type(stripslashes($record_types));
					$docwatch_watch->set_record_default_index_lang(stripslashes(($indexation_lang == "--" ? "" : $indexation_lang)));
					$docwatch_watch->set_record_default_lang(stripslashes($record_default_lang));
					$docwatch_watch->set_record_default_is_new(stripslashes($watch_record_is_new));
					
					$docwatch_watch->set_article_default_content_type(stripslashes($article_type));
					$docwatch_watch->set_article_default_parent(stripslashes($article_parent));
					$docwatch_watch->set_article_default_publication_status(stripslashes($article_status));
					
					$docwatch_watch->set_section_default_content_type(stripslashes($section_type));
					$docwatch_watch->set_section_default_parent(stripslashes($section_parent));
					$docwatch_watch->set_section_default_publication_status(stripslashes($section_status));

					$docwatch_watch->set_watch_rss_link(stripslashes($watch_rss_link));
					$docwatch_watch->set_watch_rss_lang(stripslashes($watch_rss_lang));
					$docwatch_watch->set_watch_rss_copyright(stripslashes($watch_rss_copyright));
					$docwatch_watch->set_watch_rss_editor(stripslashes($watch_rss_editor));
					$docwatch_watch->set_watch_rss_webmaster(stripslashes($watch_rss_webmaster));
					$docwatch_watch->set_watch_rss_image_title(stripslashes($watch_rss_image_title));
					$docwatch_watch->set_watch_rss_image_website(stripslashes($watch_rss_image_website));
					
					$docwatch_watch->set_boolean_expression(stripslashes($boolean_expression));
					
					$result = $docwatch_watch->save();
					$response = "";
					if($docwatch_watch->get_id()){
						$response = $docwatch_watch->get_informations();
					}
					$response = array(
						'result' => $result,
						'elementId' => $docwatch_watch->get_id(),
						'response' => $response
					);
					print encoding_normalize::json_encode($response);
				}else{
					$response = array(
							'result' => false,
							'elementId' => $docwatch_watch->get_id(),
							'response' => ""
					);
					print encoding_normalize::json_encode($response);
				}
				break;
			case "delete_watch":
				$docwatch_watch = new docwatch_watch($id);
				$result = $docwatch_watch->delete();
				$response = "";
				if(!$result){
					$response = $docwatch_watch->get_error();
				}
				$response = array(
					'result' => $result,
					'elementId' => $docwatch_watch->get_id(),
					'response' => $response
				);
				print encoding_normalize::json_encode($response);
				break;
			case "purge_items_mark_as_deleted":
			    $docwatch_watch = new docwatch_watch($id);
			    $result = $docwatch_watch->purge_items_mark_as_deleted();
			    $response = "";
			    if(!$result){
			        $response = $docwatch_watch->get_error();
			    }
			    $response = array(
			        'result' => $result,
			        'elementId' => $docwatch_watch->get_id(),
			        'response' => $response
			    );
			    print encoding_normalize::json_encode($response);
			    break;
			case "update_children" :
				switch ($type) {
					case "category":
						if (isset($children)) {
							$children = explode(",", $children);
							if (count($children)) {
								foreach ($children as $child) {
									$query = "UPDATE docwatch_categories SET category_num_parent='".$id."' WHERE id_category='".$child."'";
									$result = pmb_mysql_query($query);
									if (!$result) {
										$response = $msg["dsi_docwatch_tree_error_database"];
										break;
									}
								}
							}
						}
						break;
					case "watch":
						if (isset($children)) {
							$children = explode(",", $children);
							if (count($children)) {
								foreach ($children as $child) {
									$query = "UPDATE docwatch_watches SET watch_num_category='".$id."' WHERE id_watch='".$child."'";
									$result = pmb_mysql_query($query);
									if (!$result) {
										$response = $msg["dsi_docwatch_tree_error_database"];
										break;
									}
								}
							}
						}
						break;
					case "source":
						if (isset($children)) {
							$children = explode(",", $children);
							if (count($children)) {
								foreach ($children as $child) {
									$query = "UPDATE docwatch_datasources SET datasource_num_watch='".$id."' WHERE id_datasource='".$child."'";
									$result = pmb_mysql_query($query);
									if (!$result) {
										$response = $msg["dsi_docwatch_tree_error_database"];
										break;
									}
								}
							}
						}
						break;
				}
				$response = array(
						'result' => $result,
						'response' => (isset($response) ? $response : "")
				);
				print encoding_normalize::json_encode($response);
				break;
			case "get_logo_form" :
				$docwatch_logo = new docwatch_logo($id);
				print encoding_normalize::json_encode(
						$docwatch_logo->get_form()
					);
				break;
			case "edit_logo" :
				$docwatch_logo = new docwatch_logo($id);
				print $docwatch_logo->get_field();
				break;
		}
		break;
	case "items":
		switch($action){
			case "get_items":
				if($watch_id){
					$docwatch_watch = new docwatch_watch($watch_id);
					$docwatch_watch->sync();
					$docwatch_watch->fetch_items();
					if($docwatch_watch->check_rights()){
						$response = array(
								'items'=>$docwatch_watch->get_normalized_items(),
								'formated_last_date'=>date("c",strtotime($docwatch_watch->get_last_date())), 
								'sources_updated'=>$docwatch_watch->get_synced_datasources(),
								'deflt_docwatch_watch_filter_deleted' => $deflt_docwatch_watch_filter_deleted
						);
						print encoding_normalize::json_encode($response);
					}
				}
				break;
			case "markItemAsRead":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					$docwatch_item->set_status(1);
					if($docwatch_item->save()) {
						$return["state"] = true;
						$return["item"] = $docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "itemRestore":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					$docwatch_item->set_status(1);
					if($docwatch_item->save()) {
						$return["state"] = true;
						$return["item"] = $docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "markItemAsUnread":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					$docwatch_item->set_status(0);
					if($docwatch_item->save()) {
						$return["state"] = true;
						$return["item"] = $docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "markItemAsInteresting":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					$docwatch_item->set_interesting(1);
					if($docwatch_item->save()) {
						$return["state"] = true;
						$return["item"] = $docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "markItemAsUninteresting":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					$docwatch_item->set_interesting(0);
					if($docwatch_item->save()) {
						$return["state"] = true;
						$return["item"] = $docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "itemCreateNotice":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					$record=$docwatch_item->create_notice();
					if($record['id']) {
						$return["state"] = true;
						$return["record"] = $record;
						$return["item"] = $docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "itemDeleteCreatedNotice" :
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				$notice_id = intval($notice_id);
				if($notice_id){
					notice::del_notice($notice_id);
					$query = "update docwatch_items set	item_num_notice = 0 where item_num_notice = '".$notice_id."'";
					pmb_mysql_query($query);
				}
				break;
			case "itemCreateSection":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){					
					$docwatch_item = new docwatch_item($item_id);
					$section=$docwatch_item->create_section();
					if($section['id']) {
						$return["state"] = true;
						$return["section"] = $section;
						$return["item"] = $docwatch_item->get_normalized_item();
					}					
				}
				print encoding_normalize::json_encode($return);
				break;
			case "itemCreateArticle":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){							
					$docwatch_item = new docwatch_item($item_id);
					$article=$docwatch_item->create_article();
					if($article['id']) {
						$return["state"] = true;
						$return["article"] = $article;
						$return["item"] = $docwatch_item->get_normalized_item();
					}					
				}
				print encoding_normalize::json_encode($return);
				break;
			case "deleteItem":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					$docwatch_item = new docwatch_item($item_id);
					if($docwatch_item->mark_as_deleted()) {
						$return["state"] = true;
						$return["item"] = array("id" => $item_id);
					}
				}
				print encoding_normalize::json_encode($return);
				break;
			case "itemIndex":
				$return = array();
				$return["action"] = $action;
				$return["state"] = false;
				if($item_id){
					if($charset != 'utf-8'){
						$data = encoding_normalize::utf8_normalize($data);
					}
					$data=json_decode(stripslashes($data),true);
					$docwatch_item = new docwatch_item($item_id);
					if($docwatch_item->index($data)) {
						$return["state"] = true;
						//$return["item"] = array("id" => $item_id,"descriptors_isbd" => $docwatch_item->get_descriptors_isbd(),"tags_isbd" => $docwatch_item->get_tags_isbd());
						$return["item"] =$docwatch_item->get_normalized_item();
					}
				}
				print encoding_normalize::json_encode($return);
				break;
		}
		break;
	case "sources":
		switch($action){
			case "get_sources":
				if($watch_id){
					$docwatch_watch = new docwatch_watch($watch_id);
					print encoding_normalize::json_encode($docwatch_watch->get_normalized_datasources());
				}
				break;
			case "get_form" :
				$id = intval($id);
				if($id){
					$query = "select id_datasource,datasource_type from docwatch_datasources where id_datasource = '".$id."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$datasource = new $row->datasource_type($row->id_datasource);
					}
				}else{
					if(class_exists($class)){
						$datasource = new $class();
					}
				}
				if(is_object($datasource)){
					print $datasource->get_form();
				}
				break;
			case "get_selector_form" :
				$id = intval($id);
				if($id){
					$query = "select id_selector,selector_type from docwatch_selectors where id_selector= '".$id."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$selector = new $row->selector_type($row->id_selector);
					}
				}else{
					if(class_exists($class)){
						$selector = new $class();
					}
				}
				if(isset($selector) && is_object($selector)){
					print $selector->get_form();
				}
				break;
			case "get_sub_selector_form" :
				$id = intval($id);
				if($id){
					$query = "select id_selector,selector_type from docwatch_selectors where id_selector= '".$id."'";
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result)){
						$row = pmb_mysql_fetch_object($result);
						$selector = new $row->selector_type($row->id_selector);
					}
				}else{
					if(class_exists($class)){
						$selector = new $class();
					}
				}
				if(isset($selector) && is_object($selector)){
					print $selector->get_ajax_form();
				}
				break;
			case "save_source" :
				if(class_exists($className)){
					$docwatch_datasource = new $className($id_datasource);
					//TODO: Comme pour la veille
					$docwatch_datasource->set_from_form();
					$docwatch_datasource->set_num_watch($num_watch);
			
					$result = $docwatch_datasource->save();
					if($docwatch_datasource->get_id()){
						$response = $docwatch_datasource->get_normalized_datasource();
					}
					$response = array(
							'result' => $result,
							'elementId' => $docwatch_datasource->get_id(),
							'response' => $response
					);
					print encoding_normalize::json_encode($response);
				}
				break;
			case "duplicate_source" :
				if(class_exists($className)){
					$docwatch_datasource = new $className($id_duplicated_datasource);
					$docwatch_datasource->set_duplicate_from_id($id_duplicated_datasource);
					$docwatch_datasource->set_id(0);
					$docwatch_datasource->set_title($title);
					$docwatch_datasource->set_num_watch($num_watch);
					$docwatch_datasource->change_parameter_selector_to_type();
					$result = $docwatch_datasource->save();
					if($docwatch_datasource->get_id()){
						$response = $docwatch_datasource->get_normalized_datasource();
					}
					$response = array(
							'result' => $result,
							'elementId' => $docwatch_datasource->get_id(),
							'response' => $response
					);
					print encoding_normalize::json_encode($response);
				}
			case "delete_source":
				$docwatch_datasource= new docwatch_datasource($id);
				$result = $docwatch_datasource->delete();
				if($result){
					$response = array(
							'result' => $result,
							'elementId' => $docwatch_datasource->get_id(),
							'response' => $response
					);
					print encoding_normalize::json_encode($response);
				}
				break;
			case "get_env":
				$element = new $elem();
				if(!isset($var)) $var = '';
				print $element->get_page_env_select($pageid,$name,$var);
				break;
		}	
		break;	
	case "forms" :
		require_once($class_path."/docwatch/docwatch_ui.class.php");
		switch($action) {
			case "get_form":
				switch($form){
					case "docwatch_watch_form_tpl":
						print docwatch_ui::get_watch_form();
						break;
					case "docwatch_category_form_tpl" :
						print docwatch_ui::get_category_form();
						break;
					case "docwatch_source_duplicate_form_tpl" :
						print docwatch_ui::get_source_duplicate_form();
						break;
				}
				break;
			case "get_datas":
				print encoding_normalize::json_encode(array(
						"categoryForm" => docwatch_ui::get_category_form(),
						"watchForm" => docwatch_ui::get_watch_form()
				));
				break;
		}
		break;
	default:
		break;
}	

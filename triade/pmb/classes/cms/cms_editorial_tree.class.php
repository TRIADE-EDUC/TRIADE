<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_tree.class.php,v 1.18.2.1 2024/07/26 09:13:37 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/cms/cms_editorial_tree.tpl.php");

//gère l'arbre éditorial
class cms_editorial_tree {
	public $tree=array();		// tableau représentant l'arbre
	protected $inc_articles;	// booléen précisant si on veut les articles avec ou non...

	public function __construct($inc_articles=true){
		$this->inc_articles = $inc_articles;
	}

	protected function fetch_data(){
		global $charset;

		$rqt = "select id_section, section_title, section_num_parent, if(section_logo!='',1,0) as logo_exist, editorial_publication_state_label, editorial_publication_state_class_html  from cms_sections left join cms_editorial_publications_states on id_publication_state=section_publication_state order by section_order";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$infos = array(
					'id' => $row->id_section,
					'title' => $row->section_title,
					'type' => ($row->section_num_parent == 0 ? "root_section": 'section'),
					'state_label' => htmlentities($row->editorial_publication_state_label,ENT_QUOTES,$charset),
					'class_html' => $row->editorial_publication_state_class_html
				);
				if($row->logo_exist == 1){
					$infos['icon'] =  "./cms_vign.php?type=section&id=".$row->id_section."&mode=small_vign";
				}
				$sub_rqt = "select id_section, section_order from cms_sections where section_num_parent='".$row->id_section."' ORDER BY section_order ASC";
				$sub_res = pmb_mysql_query($sub_rqt);
				if(pmb_mysql_num_rows($sub_res)){
					while($sub_row = pmb_mysql_fetch_object($sub_res)){
						$infos['children'][]['_reference'] = $sub_row->id_section;
					}
				}
				if($this->inc_articles){
					$art_rqt = "select id_article, article_title, if(article_logo!='',1,0) as logo_exist, editorial_publication_state_label, editorial_publication_state_class_html from cms_articles left join cms_editorial_publications_states on id_publication_state=article_publication_state where num_section ='".$row->id_section."' ORDER BY article_order ASC";
					$art_res = pmb_mysql_query($art_rqt);
					if(pmb_mysql_num_rows($art_res)){
						//on ajout un éléments Articles qui contiendra la liste des articles
						$infos['children'][]['_reference']= "articles_".$row->id_section;
						$art_content_infos = array(
							'id' => "articles_".$row->id_section,
							'title' => "Articles",
							'type' => 'articles'
						);
						while ($art_row = pmb_mysql_fetch_object($art_res)){
							$art_content_infos['children'][]['_reference']= "article_".$art_row->id_article;
							$art_infos = array(
								'id' => "article_".$art_row->id_article,
								'title' => $art_row->article_title,
								'type' => 'article',
								'state_label' => htmlentities($art_row->editorial_publication_state_label, ENT_QUOTES,$charset),
								'class_html' => $art_row->editorial_publication_state_class_html
							);
							if($art_row->logo_exist == 1){
								$art_infos['icon'] =  "./cms_vign.php?type=article&id=".$art_row->id_article."&mode=small_vign";
							}
							$this->tree[]=$art_infos;
						}
						$this->tree[]=$art_content_infos;
					}
				}
				$this->tree[]=$infos;
			}
		}
	}

	public function get_json_list(){
		if(count($this->tree) == 0){
			$this->fetch_data();
		}
		$json = array(
			'identifier' => 'id',
			'label' => 'title',
			'items' => $this->tree
		);
		return encoding_normalize::json_encode($json);
	}

	public static function get_listing(){
		global $cms_editorial_tree_layout;
		return $cms_editorial_tree_layout;
	}

	public static function set_tree_selected_item($item_id=0, $item_type='article'){
	    global $cms_editorial_tree_selected_item;

	    $tree_selected_item = $cms_editorial_tree_selected_item;
	    $tree_selected_item = str_replace('!!item_id!!', $item_id, $tree_selected_item);
	    $tree_selected_item = str_replace('!!item_type!!', $item_type, $tree_selected_item);
	    return $tree_selected_item;
	}

	public static function get_tree(){
		global $cms_editorial_tree_content,$msg,$base_path,$cms_active_image_cache;

		//Un article ou une rubrique plus récent que le cache ?
		$cms_editorial_tree_content = str_replace('!!cms_editorial_clean_cache_button!!', '<div data-dojo-type=\'dijit/form/Button\' data-dojo-props=\'id:"clean_cache_button",title:"'.cms_cache::get_cache_formatted_last_date().'",onclick:"if(confirm(\"'.$msg['cms_clean_cache_confirm'].'\")){document.location=\"'.$base_path.'/cms.php?categ=editorial&sub=list&action=clean_cache\";}"\'>'.$msg['cms_clean_cache'].'</div>', $cms_editorial_tree_content);
		if($cms_active_image_cache == 1){
			$cms_editorial_tree_content = str_replace("!!cms_editorial_clean_cache_img!!", '<div data-dojo-type=\'dijit/form/Button\' data-dojo-props=\'id:"clean_cache_button_img",onclick:"if(confirm(\"'.$msg['cms_clean_cache_confirm_img'].'\")){document.location=\"'.$base_path.'/cms.php?categ=editorial&sub=list&action=clean_cache_img\";}"\'>'.$msg['cms_clean_cache_img'].'</div>', $cms_editorial_tree_content);
		}else{
			$cms_editorial_tree_content = str_replace("!!cms_editorial_clean_cache_img!!", "", $cms_editorial_tree_content);
		}
		return $cms_editorial_tree_content;
	}

	public function update_children($children,$num_parent){
		$children = explode(",", $children);
		$cpt = 1;
		foreach ($children as $child) {
			$rqt = "UPDATE cms_sections SET section_num_parent='".$num_parent."', section_order='".$cpt."' WHERE id_section='".$child."'";
			$res = pmb_mysql_query($rqt);
			$cpt++;
			if (!$res) return "$rqt";
		}
		return "done";
	}
}
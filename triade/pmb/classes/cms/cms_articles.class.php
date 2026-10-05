<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_articles.class.php,v 1.6 2022/03/10 15:19:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/cms/cms_articles.tpl.php");

//gère une liste d'articles
class cms_articles {
	public $num_section;	// id de la rubrique parente
	protected $recursive;	// boléen définissant si on veut aussi les articles des rubriques filles...
	public $list;			// tableau contenant les ids des articles à lister...
	public $section_title;	// titre de la rubrique 
	
	public function __construct($num_section=0,$recursive = false){
		$this->num_section = $num_section;
		$this->recursive = $recursive;
		$this->list = array();
		$this->fetch_data();
	}
	
	protected function fetch_data(){
		
		$this->_recursive_fetch_data($this->num_section);
	}
	
	protected function _recursive_fetch_data($num_parent=0){
		$num_parent = intval($num_parent);
		if($num_parent != 0){
			$rqt = "select id_article from cms_articles where num_section='".$num_parent."'";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					$this->list[]=$row->id_article;
				}
			}
		}
		if($this->recursive){
			$rqt = "select id_section from cms_sections where section_num_parent = '".$num_parent."'";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					$this->_recursive_fetch_data($row->id_section);
				}
			}	
		}
	}
	
	public function get_list(){
		return $this->list;
	}
	
	public function get_nb_articles(){
		return count($this->list);
	}	
	
	public function get_section_title(){
		if(!$this->section_title){
			$rqt = "select section_title from cms_sections where id_section='".$this->num_section."'";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)){
				$this->section_title = pmb_mysql_result($res,0,0);
			}
		}
		return $this->section_title;
	}
	
	public function get_tab(){
		global $msg;
		global $cms_articles_list, $cms_articles_list_item;
		
		$list = str_replace("!!cms_articles_list_title!!", sprintf($msg['cms_articles_list_title'],$this->get_section_title()),$cms_articles_list);
		if($this->get_nb_articles()){
			$rqt = "select * from cms_articles where id_article in (".implode(",",$this->list).")";
			$res = pmb_mysql_query($rqt);
			$items = "";
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					$type_label = '';
					$rqt_type = "select editorial_type_label from cms_editorial_types where id_editorial_type = '".$row->article_num_type."'";
					$res_type = pmb_mysql_query($rqt_type);
					if(pmb_mysql_num_rows($res_type)){
						$row_type = pmb_mysql_fetch_object($res_type);
						$type_label = $row_type->editorial_type_label;							
					}
					$item = str_replace('!!cms_article_type!!', $type_label, $cms_articles_list_item);
					$item = str_replace('!!cms_article_id!!', $row->id_article, $item);
					$item = str_replace('!!cms_article_title!!', $row->article_title, $item);
					$items.= $item;
				}	
			}
		}		
		return str_replace("!!items!!",$items,$list);
	}
	
	public function get_num_section(){
		return $this->num_section;
	}
}

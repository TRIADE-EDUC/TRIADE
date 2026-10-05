<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_article.class.php,v 1.41.6.1 2025/01/08 14:32:05 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/cms/cms_editorial.class.php");
require_once($class_path."/avis.class.php");

class cms_article extends cms_editorial {

	public function __construct($id = 0, $num_parent = 0) {
		//on gère les propriétés communes dans la classe parente
		parent::__construct($id,"article",$num_parent);

		$this->opt_elements = array(
			'contenu' => true
		);
	}

	protected function fetch_data(){
		if(!$this->id)
			return false;

		// les infos générales...
		$rqt = "SELECT
			article_num_type,
			article_title,
			article_resume,
			article_contenu,
			article_publication_state,
			article_start_date,
			article_end_date,
			num_section,
			article_creation_date,
			article_update_timestamp
		FROM cms_articles WHERE id_article ='".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);

			$this->num_type = $row->article_num_type;
			$this->title = $row->article_title;
			$this->resume = $row->article_resume;
			$this->contenu = $row->article_contenu;
			$this->publication_state = $row->article_publication_state;
			$this->start_date = $row->article_start_date;
			$this->end_date = $row->article_end_date;
			$this->num_parent = $row->num_section;
			$this->create_date = $row->article_creation_date;
			$this->last_update_date = $row->article_update_timestamp;

            unset($row);
            pmb_mysql_free_result($res);
		}

		if(strpos($this->start_date,"0000-00-00")!== false){
			$this->start_date = "";
		}
		if(strpos($this->end_date,"0000-00-00")!== false){
			$this->end_date = "";
		}
	}

	public function save(){
		if($this->id){
			$save = "update ";
			$order = "";
			$clause = "where id_article = '".$this->id."'";
		}else{
			$save = "insert into ";

			//on place le nouvel article à la fin par défaut
			$query = "SELECT id_article FROM cms_articles WHERE num_section='".$this->num_parent."'";
			$result = pmb_mysql_query($query);
			$order = ",article_order = '".(pmb_mysql_num_rows($result)+1)."' ";

			$clause = "";
		}
		$save.= "cms_articles set
		article_title = '".addslashes($this->title)."',
		article_resume = '".addslashes($this->resume)."',
		article_contenu = '".addslashes($this->contenu)."',
		article_publication_state = '".addslashes($this->publication_state)."',
		article_start_date = '".addslashes($this->start_date)."',
		article_end_date = '".addslashes($this->end_date)."',
		num_section = '".addslashes($this->num_parent)."',
		article_num_type = '".$this->num_type."' ".
		(!$this->id ? ",article_creation_date=sysdate() " :"")."
		$order"."
		$clause";
		pmb_mysql_query($save);
		if(!$this->id) $this->id = pmb_mysql_insert_id();
		//au tour des descripteurs...
		//on commence par tout retirer...
		$del = "delete from cms_articles_descriptors where num_article = '".$this->id."'";
		pmb_mysql_query($del);
		$this->get_descriptors();
		for($i=0 ; $i<count($this->descriptors) ; $i++){
			$rqt = "insert into cms_articles_descriptors set num_article = '".$this->id."', num_noeud = '".$this->descriptors[$i]."',article_descriptor_order='".$i."'";
			pmb_mysql_query($rqt);
		}

		//et maintenant le logo...
		$this->save_logo();

		//enfin les éléments du type de contenu
		$types = new cms_editorial_types("article");
		$types->save_type_form($this->num_type,$this->id);

		$this->save_concepts();

		$this->maj_indexation();

		$this->save_documents();
	}

	public function duplicate($num_parent = 0) {
		$num_parent = intval($num_parent);
		if (!$num_parent) $num_parent = $this->num_parent;

		//on place le nouvel article à la fin par défaut
		$query = "SELECT id_article FROM cms_articles WHERE num_section='".$num_parent."'";
		$result = pmb_mysql_query($query);
		if ($result) $order = ",article_order = '".(pmb_mysql_num_rows($result)+1)."' ";
		else $order = ",article_order = 1";

		$insert = "insert into cms_articles set
		article_title = '".addslashes($this->title)."',
		article_resume = '".addslashes($this->resume)."',
		article_contenu = '".addslashes($this->contenu)."',
		article_logo = '".addslashes($this->logo->data)."',
		article_publication_state ='".addslashes($this->publication_state)."',
		article_start_date = '".addslashes($this->start_date)."',
		article_end_date = '".addslashes($this->end_date)."',
		num_section = '".addslashes($num_parent)."',
		article_num_type = '".$this->num_type."',
		article_creation_date=sysdate() ".$order;

		pmb_mysql_query($insert);
		$id = pmb_mysql_insert_id();

		//au tour des descripteurs...
		$this->get_descriptors();
		for($i=0 ; $i<count($this->descriptors) ; $i++){
			$rqt = "insert into cms_articles_descriptors set num_article = '".$id."', num_noeud = '".$this->descriptors[$i]."',article_descriptor_order='".$i."'";
			pmb_mysql_query($rqt);
		}

		//on crée la nouvelle instance
		$new_article = new cms_article($id);

		//enfin les éléments du type de contenu
		$types = new cms_editorial_types("article");
		$types->duplicate_type_form($this->num_type,$id,$this->id);
		$new_article->maj_indexation();

		$new_article->documents_linked = $this->get_documents();
		$new_article->save_documents();
		return $id;
	}

	public function get_parent_selector(){
		$opts.=$this->_recurse_parent_select();
		return $opts;
	}

	protected function _recurse_parent_select($parent=0,$lvl=0){
		global $charset;
		$opts = "";
		$parent = intval($parent);
		$rqt = "select id_section, section_title from cms_sections where section_num_parent = '".$parent."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				$opts.="
				<option value='".$row->id_section."'".($this->num_parent == $row->id_section ? " selected='selected'" : "").">".str_repeat("&nbsp;&nbsp;",$lvl).htmlentities($row->section_title,ENT_QUOTES,$charset)."</option>";
				$opts.=$this->_recurse_parent_select($row->id_section,$lvl+1);
			}
		}
		return $opts;
	}

	public function update_parent_section($num_section,$order=0){
		$this->num_section = intval($num_section);
		$order = intval($order);
		$update = "update cms_articles set num_section ='".$this->num_section."', article_order = '".$order."' where id_article = '".$this->id."'";
		pmb_mysql_query($update);
	}

	protected function is_deletable(){
		return true;
	}

	private function format_datas_lang(&$array, $lang) {
		$lang = strtolower($lang);
		foreach ($this->fields_type as $key => $value) {
			switch ($key) {
				case "article_title_".$lang:
					if ($value) $array["title"] = $value["values"][0]["format_value"];
					break;
				case "article_resume_".$lang:
					if ($value)	$array["resume"] = $value["values"][0]["format_value"];
					break;
				case "article_contenu_".$lang:
					if ($value)	$array["content"] = $value["values"][0]["format_value"];
					break;
				default:
					if (strpos($key, $lang) !== false) {
						if ($value) $array["fields_type"][str_replace("_".$lang, "", $key)] = $value;
					}
			}
		}
	}

	public function update_permalink(){
		if(isset($this->formated_datas) && isset($this->formated_datas['permalink'])){
			$this->formated_datas['permalink'] = $this->get_permalink();
		}
	}

	public static function get_format_data_structure($type="article",$full=true){
		return cms_editorial::get_format_data_structure($type,$full);
	}

	public function get_display_avis_detail() {
		$avis = new avis($this->id, AVIS_ARTICLES);
		return $avis->get_display_detail();
	}
}
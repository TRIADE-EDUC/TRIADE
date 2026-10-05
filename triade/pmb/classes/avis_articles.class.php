<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_articles.class.php,v 1.4 2017/11/07 15:17:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/avis.class.php");
require_once($class_path."/cms/cms_article.class.php");

class avis_articles extends avis {
	
	public function __construct($object_id = 0) {
		$this->object_type = AVIS_ARTICLES;
		parent::__construct($object_id);
	}

	protected function _get_select_query() {
		return ", id_article ";
	}
	
	protected function _get_join_query() {
		return "left join cms_articles on cms_articles.id_article = avis.num_notice ";
	}
	
	protected function _get_sort_query() {
		return "order by dateAjout desc ";
	}
	
	public function get_display_list() {
		global $msg;
		global $pmb_javascript_office_editor;
		global $begin_result_liste;
	
		$query = $this->get_query();
		$result = pmb_mysql_query($query);
		$display = '';
		if (pmb_mysql_num_rows($result)) {
			//affichage des articles
			$display .= "<script type=\"text/javascript\" src='./javascript/dyn_form.js'></script>";
			$display .= "<script type=\"text/javascript\" src='./javascript/http_request.js'></script>";
			$display .= $begin_result_liste;
			$id_article=0;
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($id_article!=$row->id_article) {
					if ($id_article!=0) $display .=  "</ul><br />" ;
					$id_article=$row->id_article;
					$cms_article = new cms_article($row->id_article);
					$content = $cms_article->title."<br /><br />";
					if($cms_article->resume) {
						$content .= "
							<div class='avis_article_resume'>
								<h4>".$msg['cms_editorial_form_resume']."</h4>
								<p class='align_justify'>".$cms_article->resume."</p>
							</div>";
					}
					if($cms_article->contenu) {
						$content .= "
							<div class='avis_article_content'>
								<h4>".$msg['cms_editorial_form_contenu']."</h4>
								<p class='align_justify'>".$cms_article->contenu."</p>
							</div>";
					}
					$display .= gen_plus($row->id_article, $cms_article->title, $content);
					$display .=  "<ul>" ;
				}
				if($pmb_javascript_office_editor)	{
					$office_editor_cmd=" if (typeof(tinyMCE) != 'undefined') tinyMCE_execCommand('mceAddControl', true, 'avis_desc_".$row->id_avis."');	 ";
				} else {
					$office_editor_cmd="";
				}
				$display .= "<div id='avis_".$row->id_avis."' onclick=\"make_form('".$row->id_avis."'); $office_editor_cmd\">";
				$display .= self::get_display_review($row);
				$display .= "</div><div id='update_$row->id_avis'></div>
				<br />";
			}
			$display .=  "</ul><br />" ;
		}
		return $display;
	}
	
	public static function delete_from_object($id) {
		$query = "delete from avis where num_notice=".$id." and type_object = ".AVIS_ARTICLES;
		pmb_mysql_query($query);
	}
}
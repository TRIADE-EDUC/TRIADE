<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_articles.class.php,v 1.10 2019-03-19 14:38:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/cms/cms_article.class.php");
/**
 * class docwatch_datasource_articles
 * 
 */
class docwatch_datasource_articles extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct
	
	/**
	 * Génération de la structure de données representant les items de type article
	 *
	 */
	
	protected function get_items_datas($selector_values){
		global $dbh;
		$articles_retour = array();
		if(count($selector_values)){
			foreach($selector_values as $id){
				$article_instance = new cms_article($id);
				$article_data = $article_instance->format_datas();
				$article = array();
				$article['type'] = 'article';
				$article['num_article'] = $article_data['id'];
				$article['title'] = $article_data['title'];
				$article['summary'] = $article_data['resume'];
				$article['content'] = $article_data['content'];
				if($article_data['start_date'] == ""){
					$article['publication_date'] = extraitdate($article_data['create_date']);
				}
				else{
					$article['publication_date'] = extraitdate($article_data['start_date']);
				}
				$article['logo_url'] = $article_data['logo']['large'];
				$article['url'] = $this->get_constructed_link("article", $article_data['id']);
				if(count($article_data['descriptors'])){
				    $descriptors = array();
				    for($i=0 ; $i<count($article_data['descriptors']) ; $i++){
				        $descriptors[]  = array('id' => $article_data['descriptors'][$i]['id']);
				    }
				    $article['descriptors'] = $descriptors;
				}
				$articles_retour[] = $article;
			}
		}
		return $articles_retour;
	}
	
	public function filter_datas($datas, $user=0){
		return $this->filter_articles($datas, $user);
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
			'docwatch_selector_articles_by_sections' => $msg['docwatch_selector_articles_by_sections'],
			'docwatch_selector_articles_type_article_generic' => $this->msg['docwatch_datasource_selector_articles_type_article_generic'],
			'docwatch_selector_articles_type_article' => $this->msg['docwatch_datasource_selector_articles_type_article']
		);
	}

	public function get_form_content(){
		global $msg,$charset;
		$form = parent::get_form_content();
		$form .= "
		<div class='row'>&nbsp;</div>
 		<div class='row'>
 			<label>".htmlentities($msg['dsi_docwatch_datasource_articles_link_select'],ENT_QUOTES,$charset)."</label>
 		</div>
 		<div class='row'>
 			".$this->get_constructor_link_form("article",get_class($this))."
 		</div>";
		return $form;
	}
		
	public function set_from_form() {
		$this->save_constructor_link_form("article",get_class($this));
		parent::set_from_form();
	}
	
} // end of docwatch_datasource_articles


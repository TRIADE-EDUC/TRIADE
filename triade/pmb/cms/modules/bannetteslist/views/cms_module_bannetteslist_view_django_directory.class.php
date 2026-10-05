<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_bannetteslist_view_django_directory.class.php,v 1.4 2023/06/07 10:27:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

class cms_module_bannetteslist_view_django_directory extends cms_module_common_view_bannetteslist{

	protected function get_record_template_form() {
		if(!isset($this->parameters['django_directory'])) $this->parameters['django_directory'] = '';
		$form = "
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_bannetteslist_view_django_directory'>".$this->format_text($this->msg['cms_module_bannetteslist_view_django_directory'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='cms_module_bannetteslist_view_django_directory'>";
		$form.= $this->get_directories_options($this->parameters['django_directory']);
		$form.= "
				</select>
			</div>
		</div>";
		return $form;
	}
	
	public function save_form(){
		global $cms_module_bannetteslist_view_django_directory;
	
		$this->parameters['django_directory'] = $cms_module_bannetteslist_view_django_directory;
		return parent::save_form();
	}
	
	public function render($datas){
		global $dbh;
		global $include_path;
		global $opac_url_base;
		global $opac_show_book_pics;
		global $opac_book_pics_url;
		global $record_css_already_included;
		global $opac_bannette_notices_order;
	
		//on gère l'affichage des banettes				
		foreach($datas["bannettes"] as $i => $bannette) {
			$datas['bannettes'][$i]['link'] = $this->get_constructed_link('bannette',$datas['bannettes'][$i]['id']);
			
			if($this->parameters['nb_notices']) $limitation = " LIMIT ". $this->parameters['nb_notices'];
			$requete = "select * from bannette_contenu, notices where num_bannette='".$datas['bannettes'][$i]['id']."' 
			and notice_id=num_notice";
			if($opac_bannette_notices_order){
				$requete.= " order by ".$opac_bannette_notices_order;
			}
			$requete.= " ".$limitation;
		
			$resultat = pmb_mysql_query($requete, $dbh);
			$cpt_record=0;
			$datas["bannettes"][$i]['records']=array();
			$thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			while ($r=pmb_mysql_fetch_object($resultat)) {	
				$content="";
				$url_vign = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $r->num_notice);
				if(!empty($this->parameters['django_directory'])) {
					if (!$record_css_already_included) {
						if (file_exists($include_path."/templates/record/".$this->parameters['django_directory']."/styles/style.css")) {
							$content .= "<link type='text/css' href='./includes/templates/record/".$this->parameters['django_directory']."/styles/style.css' rel='stylesheet'></link>";
						}
						$record_css_already_included = true;
					}
					$content .= record_display::get_display_in_result($r->num_notice, $this->parameters['django_directory']);
				}
				$datas["bannettes"][$i]['records'][$cpt_record]['id']=$r->num_notice;
				$datas["bannettes"][$i]['records'][$cpt_record]['title']=$r->title;
				$datas["bannettes"][$i]['records'][$cpt_record]['link']=$this->get_constructed_link("notice",$r->num_notice);
				$datas["bannettes"][$i]['records'][$cpt_record]['url_vign']=$url_vign;
				$datas["bannettes"][$i]['records'][$cpt_record]['content']=$content;
				$cpt_record++;
			}		
		}
		$this->render_already_generated = true;
		//on rappelle le tout...
		return parent::render($datas);
	}
	
	public function get_directories_options($selected = '') {
		global $opac_notices_format_django_directory;
		
		if (!$selected) {
			$selected = $opac_notices_format_django_directory;
		}
		if (!$selected) {
			$selected = 'common';
		}
		$dirs = array_filter(glob('./opac_css/includes/templates/record/*'), 'is_dir');
		$tpl = "";
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$tpl.= "<option ".(basename($dir) == basename($selected) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".basename($dir)."</option>";
			}
		}
		return $tpl;
	}
}
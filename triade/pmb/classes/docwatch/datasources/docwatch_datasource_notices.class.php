<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_notices.class.php,v 1.11 2023/06/07 10:27:46 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

require_once($class_path."/docwatch/datasources/docwatch_datasource.class.php");
require_once($class_path."/docwatch/selectors/docwatch_selector_notices.class.php");
require_once($class_path."/docwatch/docwatch_item.class.php");
require_once($class_path."/notice.class.php");

/**
 * class docwatch_datasource_notices
 * 
 */
class docwatch_datasource_notices extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * 
	 * @access private
	 */
	private $selector;
	
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct
	
	/**
	 * Génération de la structure de données representant les items de type notice
	 * @return array
	 */
	
	protected function get_items_datas($items){
		global $pmb_opac_url, $pmb_keyword_sep;
		global $opac_show_book_pics, $opac_book_pics_url;
		$records = array();
		if(count($items)){
		    $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
			foreach($items as $item) {
				$notice = new notice($item);
				$record = array();
				$logo_url = '';
				$record['type'] = 'notice';
				$record["num_notice"] = $notice->id;
				$record["title"] = $notice->tit1;
				if ($this->parameters['docwatch_datasource_notices_noticetpl_as_summary']) {
					if(!isset($tpl)){
						$tpl = new notice_tpl_gen($this->parameters['docwatch_datasource_notices_noticetpl_as_summary']);
					}
					$record["summary"] = $tpl->build_notice($notice->id);
				} else {
					$record["summary"] = $notice->n_resume;
				}
				$record["content"] = $notice->n_contenu;
				$record["url"] = $pmb_opac_url."index.php?lvl=notice_display&id=".$notice->id;
				$record["logo_url"] = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $notice->id);
				$record["publication_date"] = $notice->date_parution;
				$record["descriptors"] = $notice->categories;
				if(count($record["descriptors"])) {
				    foreach ($record["descriptors"] as $i=>$descriptor) {
				        $record["descriptors"][$i]['id'] = $descriptor['categ_id'];
				    }
				}
				$record["tags"] = ($notice->index_l ? explode($pmb_keyword_sep, $notice->index_l) : "");
				$records[] = $record;
			}
		}
		return $records;
	}

	public function filter_datas($datas, $user=0){
		return $this->filter_notices($datas, $user);
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
			"docwatch_selector_notices_caddie" => $msg['dsi_docwatch_selector_notices_caddie']
		);
	}
	
	public function get_form_content(){
		global $msg, $charset;
		
		if (!isset($this->parameters['docwatch_datasource_notices_noticetpl_as_summary'])) {
			$this->parameters['docwatch_datasource_notices_noticetpl_as_summary'] = 0;
		}
		
		$form = parent::get_form_content();
		$form .= "<div class='row'>&nbsp;</div>
 		<div class='row'>
 			<label>".htmlentities($msg['dsi_docwatch_datasource_notices_noticetpl_as_summary'],ENT_QUOTES,$charset)."</label>
 		</div>
 		<div class='row'>
 			".notice_tpl_gen::gen_tpl_select("docwatch_datasource_notices_noticetpl_as_summary",$this->parameters['docwatch_datasource_notices_noticetpl_as_summary'], "", 0, 0, $msg['1003'])."
 		</div>
		";
		return $form;
	}
	
	public function set_from_form() {
		global $docwatch_datasource_notices_noticetpl_as_summary;
	
		$this->parameters['docwatch_datasource_notices_noticetpl_as_summary'] = $docwatch_datasource_notices_noticetpl_as_summary;
		parent::set_from_form();
	}


} // end of docwatch_datasource_notices


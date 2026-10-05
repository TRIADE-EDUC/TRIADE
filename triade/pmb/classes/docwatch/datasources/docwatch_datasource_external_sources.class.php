<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_external_sources.class.php,v 1.5 2022/01/04 12:44:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/external.inc.php");
require_once($class_path."/z3950_notice.class.php");

/**
 * class docwatch_datasource_external_sources
 * 
 */
class docwatch_datasource_external_sources extends docwatch_datasource{

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
	 * Génération de la structure de données representant les items d'entrepôts
	 * @return array
	 */
	
	protected function get_items_datas($items){
		$records = array();
		if(count($items)){
			foreach($items as $item) {
				$infos=entrepot_to_unimarc($item);
				if($infos['notice']){
					$z=new z3950_notice("unimarc",$infos['notice'],$infos['source_id']);
					$record = array();
					$record["num_notice"] = 0;
					$record["type"] = "repository";
					$record["title"] = $z->titles[0];
					$record["summary"] = $z->abstract_note;
					$record["content"] = $z->content_note;
					$record["url"] = $z->link_url;
					$record["logo_url"] = $z->thumbnail_url;
					$query = "select distinct date_import from entrepot_source_".$z->source_id." where recid=".$item;
					$result = pmb_mysql_query($query);
					if ($result) {
						$row = pmb_mysql_fetch_object($result);
						$record["publication_date"] = $row->date_import;
					}
 					$record["descriptors"] = array();
 					$record["tags"] = array();
					$records[] = $record;
				}
			}
		}
		return $records;
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
			"docwatch_selector_external_sources" => $msg['dsi_docwatch_selector_external_sources']
		);
	}


} // end of docwatch_datasource_external_sources


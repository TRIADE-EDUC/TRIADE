<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_tpl.class.php,v 1.9 2023/08/28 14:04:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/h2o/pmb_h2o.inc.php");

class bannette_tpl {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------	
	public $id = 0;		// MySQL id in table 'bannette_tpl'
	public $name = "";		// nom du template
	public $comment = "";	// description du template
	public $tpl = ""; 		// Template
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {			
		$this->id = intval($id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos 
	// ---------------------------------------------------------------
	public function getData() {
		if($this->id) {
			$requete = "SELECT * FROM bannette_tpl WHERE bannettetpl_id='".$this->id."' LIMIT 1 ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);				
				$this->name	= $temp->bannettetpl_name;
				$this->comment	= $temp->bannettetpl_comment;
 				$this->tpl = $temp->bannettetpl_tpl;
			}
		}
	}
	
	public static function render($id, $data) {	
	    global $charset, $base_path;
		$requete = "SELECT * FROM bannette_tpl WHERE bannettetpl_id='".$id."' LIMIT 1 ";
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);
			$data=encoding_normalize::utf8_normalize($data);
			$temp->bannettetpl_tpl=encoding_normalize::utf8_normalize($temp->bannettetpl_tpl);
			
			$template_path = $base_path.'/temp/'.LOCATION.'_bannette_tpl_'.$id;
		    file_put_contents($template_path, $temp->bannettetpl_tpl);
			$H2o = H2o_collection::get_instance($template_path);
			$data_to_return = $H2o->render($data);
			
			if ($charset !="utf-8") {
				$data_to_return = encoding_normalize::utf8_decode($data_to_return);
			}
			return $data_to_return;			
		}
	}

} // fin class 

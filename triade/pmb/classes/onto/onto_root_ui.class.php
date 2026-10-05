<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_root_ui.class.php,v 1.5 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


/**
 * class onto_root_ui
 * 
 */
class onto_root_ui {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	public function __construct(){
		
	}
	
	protected static function obj2array($obj){
		$array = array();
		if(is_object($obj)){
			foreach($obj as $key => $value){
				if(is_object($value)){
					$value = self::obj2array($value);
				}
				$array[$key] = $value;
			}
		}else{
			$array = $obj;
		}
		return $array;
	}
	
} // end of onto_root_ui
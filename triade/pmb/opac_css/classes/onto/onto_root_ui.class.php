<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_root_ui.class.php,v 1.1 2017-01-06 16:10:51 tsamson Exp $

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
	
	protected function utf8_decode($elem){
		if(is_array($elem)){
			foreach ($elem as $key =>$value){
				$elem[$key] = self::utf8_decode($value);
			}
		}else if(is_object($elem)){
			$elem = self::obj2array($elem);
			$elem = self::utf8_decode($elem);
		}else{
			$elem = utf8_decode($elem);
		}
		return $elem;
	}
	
	protected static function utf8_encode($elem){
		if(is_array($elem)){
			foreach ($elem as $key =>$value){
				$elem[$key] = self::utf8_encode($value);
			}
		}else if(is_object($elem)){
			$elem = self::obj2array($elem);
			$elem = self::utf8_encode($elem);
		}else{
			$elem = utf8_encode($elem);
		}
		return $elem;
	}
	
	public static function utf8_normalize($elem,$tranform='encode'){
		global $charset;
		if($charset != "utf-8"){
			if($tranform=='encode'){
				return self::utf8_encode($elem);
			}elseif($tranform=='decode'){
				return self::utf8_decode($elem);
			}else{
				return $elem;
			}
		}else{
			return $elem;
		}
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
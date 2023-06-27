<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_factory.class.php,v 1.3 2018-05-22 10:46:02 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if(!isset($autoloader) || !is_object($autoloader)){
	require_once($class_path."/autoloader.class.php");
	$autoloader = new autoloader();
}

/**
 * Classe ayant pour but de fournir la bonne instance du bon searcher selon le contexte...
 * @author arenou
 *
 */
class searcher_factory {
	
	/**
	 * 
	 * @param string $type Type d'élément cherché
	 * @param string $mode Mode de recherche
	 * @param mixed, les arguments à passer au constructeur !
	 * @return searcher Une instance d'un searcher qui convient!
	 */
	public static function get_searcher($type,$mode)
	{
		global $sphinx_active;
		$classname = "";
		if($mode == 'extended'){
		    if($type == "records"){
		        $classname = "searcher_extended";
		    }else{
		        $classname = "searcher_".$type."_".$mode;
		    }
		}
		
		if($classname == "" && $sphinx_active && $mode != 'tab'){
			$classname = self::get_sphinx_classname($type,$mode);
		}
		if($classname == ""){
			$classname = self::get_native_classname($type,$mode);
		}
		if(!class_exists($classname)){
			return false;
		}
		//la prise en compte de 3 arguments passés au constructeur devrait nous couvrir suffisament...
		$obj = "";
		switch(func_num_args()){
			case 2 :
				$obj = new $classname();
				break;
			case 3 :
				$obj = new $classname(func_get_arg(2));
				break;
			case 4 :
				$obj = new $classname(func_get_arg(2),func_get_arg(3));
				break;
			case 5 :
				$obj =  new $classname(func_get_arg(2),func_get_arg(3),func_get_arg(4));
				break;
		}
		if(is_object($obj)){
			$obj->init_fields_restrict($mode);
			return $obj;
		}
		return false;
	}
	
	private static function get_sphinx_classname($type,$mode)
	{
		if(class_exists('searcher_sphinx_'.$type.'_'.$mode)){
			return 	'searcher_sphinx_'.$type.'_'.$mode;
		}
		if(class_exists('searcher_sphinx_'.$type)){
			return 	'searcher_sphinx_'.$type;
		}
	}
	
	private static function get_native_classname($type,$mode)
	{
		if (($type == 'records') && class_exists('searcher_'.$mode)) {
			return 'searcher_'.$mode;
		}
		if($type == 'authperso'){
			$type = 'searcher_authorities_authpersos';			
			return $type;				
		}
		//typo dans le source déjà en place...
		if($type == 'authorities' && $mode == ''){
			$type = 'autorities';
		}else if($type!='records' && ($mode != 'extended')){
			$type = 'authorities_'.$type;
		}
		if(class_exists('searcher_'.$type.'_'.$mode)){
			return 	'searcher_'.$type.'_'.$mode;
		}
		if(class_exists('searcher_'.$type)){
			return 	'searcher_'.$type;
		}
	}
	
}
<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_factory.class.php,v 1.9.4.1 2025/06/05 08:18:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * Classe ayant pour but de fournir la bonne instance du bon searcher selon le contexte...
 * @author arenou
 *
 */
class searcher_factory {

    /**
     * Nettoyage des caractères cp1252 (Windows)
     * @return string
     */
    protected static function clean_cp1252($user_query) {
        global $charset;
        
        return encoding_normalize::clean_cp1252($user_query, $charset);
    }
    
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
			    $obj = new $classname(static::clean_cp1252(func_get_arg(2)));
				break;
			case 4 :
			    $obj = new $classname(static::clean_cp1252(func_get_arg(2)),func_get_arg(3));
				break;
			case 5 :
			    $obj =  new $classname(static::clean_cp1252(func_get_arg(2)),func_get_arg(3),func_get_arg(4));
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

	private static function get_native_classname($type, $mode)
	{
		if (($type == 'records') && class_exists('searcher_'.$mode)) {
			return 'searcher_'.$mode;
		}
		if($type == 'authperso'){
		    if($mode === 'query'){
		        return 'searcher_authorities_authpersos_query';
		    }
			return 'searcher_authorities_authpersos';
		}
		//typo dans le source déjà en place...
		if ($type == 'authorities' && $mode == ''){
			$type = 'autorities';
		} elseif (!in_array($type, ['ontologies','records', 'bulletins', 'serials']) && ($mode != 'extended')){
			$type = 'authorities_'.$type;
		}
		if(!empty($type) && !empty($mode) && class_exists('searcher_'.$type.'_'.$mode)){
			return 	'searcher_'.$type.'_'.$mode;
		}
		if(!empty($type) && class_exists('searcher_'.$type)){
			return 	'searcher_'.$type;
		}
	}

}
<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_cache.class.php,v 1.8 2023/11/09 10:46:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

final class cms_cache{
	
    public static $hash_cache_cadres = array(
        'headers' => [],
        'contents' => []
    );
    
	/**
	 * @var array() 
	 */
	private static $cms_cache_arrayObject;
	
	/**
	 * @param object $cms_object an storable cms object
	 * @return number the object's index
	 */
	private static function get_index($cms_object){
		$index=0;
		
		switch(get_class($cms_object)){
			case 'cms_articles':
				$index=$cms_object->num_section;
				break;
			case 'cms_editorial_parametres_perso':
				$index=$cms_object->num_type;
				break;
			case 'cms_editorial_publications_states':
				$index=0;
				break;
			case 'cms_logo':
				$index=$cms_object->type.'_'.$cms_object->id;
				break;
			default:
				$index=$cms_object->id;
				break;
		}
		return $index;
	}
	
	protected static function get_cadre_header($my_hash_cadre="", $content_type=""){
	    $query = "SELECT cache_cadre_hash, cache_cadre_header
            FROM cms_cache_cadres
            WHERE cache_cadre_hash='".addslashes($my_hash_cadre)."' AND cache_cadre_type_content='".addslashes($content_type)."'";
	    $result = pmb_mysql_query($query);
	    if($result && pmb_mysql_num_rows($result)){
	        static::$hash_cache_cadres['headers'][] = $my_hash_cadre.$content_type;
	        return pmb_mysql_result($result, 0, 1);
	    }
	    return "";
	}
	
	protected static function get_cadre_content($my_hash_cadre="", $content_type=""){
	    $query = "SELECT cache_cadre_hash, cache_cadre_content
            FROM cms_cache_cadres
            WHERE cache_cadre_hash='".addslashes($my_hash_cadre)."' AND cache_cadre_type_content='".addslashes($content_type)."'";
	    $result = pmb_mysql_query($query);
	    if($result && pmb_mysql_num_rows($result)){
	        static::$hash_cache_cadres['contents'][] = $my_hash_cadre.$content_type;
	        return pmb_mysql_result($result, 0, 1);
	    }
	    return "";
	}
	
	public static function get_cadre($todo, $my_hash_cadre="", $content_type=""){
	    $value = '';
	    switch ($todo) {
	        case "select":
	            $value = static::get_cadre_content($my_hash_cadre, $content_type);
	            break;
	        case "select_header":
	            $value = static::get_cadre_header($my_hash_cadre, $content_type);
	            break;
	    }
	    if(!empty($value)) {
            if($content_type == "object"){
                $value = unserialize($value);
            } elseif($content_type == "array"){
                $value = encoding_normalize::json_decode($value, true);
            }
            return array($todo=>true,"value"=>$value);
	    }
	    return array($todo=>false,"value"=>"");
	}
	
	protected static function insert_cadre_header($my_hash_cadre="", $content_type="", $content=""){
	    if(in_array($my_hash_cadre.$content_type, static::$hash_cache_cadres['headers'])) {
	        return true;
	    }
	    static::$hash_cache_cadres['headers'][] = $my_hash_cadre.$content_type;
	    $query = "INSERT INTO cms_cache_cadres(cache_cadre_hash,cache_cadre_type_content,cache_cadre_header)
			VALUES('".addslashes($my_hash_cadre)."','".addslashes($content_type)."','".addslashes($content)."')
			ON DUPLICATE KEY UPDATE cache_cadre_header='".addslashes($content)."'";
	    $result = pmb_mysql_query($query);
	    if($result) {
	        return true;
	    }
	    return false;
	}
	
	protected static function insert_cadre_content($my_hash_cadre="", $content_type="", $content=""){
	    if(in_array($my_hash_cadre.$content_type, static::$hash_cache_cadres['contents'])) {
	        return true;
	    }
	    static::$hash_cache_cadres['contents'][] = $my_hash_cadre.$content_type;
	    $query = "INSERT INTO cms_cache_cadres(cache_cadre_hash,cache_cadre_type_content,cache_cadre_content)
			VALUES('".addslashes($my_hash_cadre)."','".addslashes($content_type)."','".addslashes($content)."')
			ON DUPLICATE KEY UPDATE cache_cadre_content='".addslashes($content)."'";
	    $result = pmb_mysql_query($query);
	    if($result) {
	        return true;
	    }
	    return false;
	}
	
	public static function insert_cadre($todo, $my_hash_cadre="", $content_type="", $content="") {
	    if($content_type == "object" && $content){
	        $content = serialize($content);
	    } elseif($content_type == "array"){
	        $content = encoding_normalize::json_encode($content);
	    }
	    $inserted = false;
	    switch ($todo) {
	        case "insert":
	            $inserted = static::insert_cadre_content($my_hash_cadre, $content_type, $content);
	            break;
	        case "insert_header":
	            $inserted = static::insert_cadre_header($my_hash_cadre, $content_type, $content);
	            break;
	    }
	    if($inserted){
	        return array($todo=>true,"value"=>"");
	    }
	    return array($todo=>false,"value"=>"");
	}
	
	/**
	 * @param object $cms_object an storable cms object
	 * @return bool true if exists in the array, false otherwise
	 */
	public static function get_at_cms_cache($cms_object){
		if(!isset(self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)])) {
			self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)] = null;
		}
		if(is_null(self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)])){
			return false;
		}else{
			return self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)];
		}
	}
	
	/**
	 * @param object $cms_object an storable cms object
	 */
	public static function set_at_cms_cache($cms_object){
		self::$cms_cache_arrayObject[get_class($cms_object)][self::get_index($cms_object)]=$cms_object;
	}
	
	/*
	 * Private contructor
	 */
	private function __construct() {}
	
	/*
	 * Prevent cloning of instance
	 */
	private function __clone() {
		throw new Exception('Clone is not allowed !');
	}
	
	/*
	 * Set the instance to null
	 */
	private function __destruct() {
		self::$cms_cache_arrayObject=null;
	}
	
	public static function init_hash_cache_cadres() {
	    static::$hash_cache_cadres = array(
	        'headers' => [],
	        'contents' => []
	    );
	}
	
	public static function clean_cache(){
		pmb_mysql_query("TRUNCATE TABLE cms_cache_cadres");
		static::init_hash_cache_cadres();
	}
	
	public static function clean_outdated_cache(){
	    global $cms_cache_ttl;//Variable en seconde
	    
	    $cms_cache_ttl = intval($cms_cache_ttl);
	    $requete="DELETE FROM cms_cache_cadres WHERE DATE_SUB(NOW(), INTERVAL ".$cms_cache_ttl." SECOND) > cache_cadre_create_date";
	    pmb_mysql_query($requete);
	    if(pmb_mysql_affected_rows()) {
	        static::init_hash_cache_cadres();
	    }
	}
}

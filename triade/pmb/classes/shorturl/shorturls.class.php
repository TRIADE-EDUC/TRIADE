<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturls.class.php,v 1.2 2021/04/13 14:34:26 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once("$class_path/shorturl/shorturl_type.class.php");

class shorturls {
	
	public static function proceed($hash)
	{
		$query = "select id_shorturl,shorturl_type from shorturls where shorturl_hash = '".addslashes($hash)."'";
		$result=pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$id = $row->id_shorturl;
			$classname = self::get_class_name($row->shorturl_type);
			$class = new $classname($id);
			$class->proceed();
		}else{
			print "Hash not found";
			exit;
		}	

	}
	
	protected static function get_class_name($type=""){
		if($type && class_exists("shorturl_type_".$type)){
			return "shorturl_type_".$type;
		}	
		throw new Exception("Class not found");
	}
}
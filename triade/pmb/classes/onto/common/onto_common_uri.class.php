<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_uri.class.php,v 1.17 2018-01-22 09:16:28 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class onto_common_uri
 * Génération des URI. La classe s'appuie sur un numéro auto en base de données.
 * L'URI par défaut est prefix+"#"+numero auto. Si pas de préfixe :
 * class_uri+"#"+numero auto.
 * (Le # est à confirmer)
 * 
 * L'URI est stoquée dans la table de données associée au numéro auto. Le numéro
 * auto est utilisé dans les tables PMB.
 * 
 */
class onto_common_uri {

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * dernière URI générée
	 * @access private
	 */
	static private $last_uri;

	/**
	 * Génère une nouvelle URI. Cette méthode est apellée par save() de onto_handler
	 *
	 * @param string class_uri URI de la classe d'objets
	 * @param string uri_prefix Préfixe à employer pour l'URI. Si vide, on prend celui de la classe.
	 * @return void
	 * @static
	 * @access public
	 */
	static public function get_new_uri($class_uri, $uri_prefix="") {
		global $dbh;
		
		if($uri_prefix){
			$class_uri=$uri_prefix;
		}
		$last_uri="";
		$max=1;
		//On cherche le max des id + 1
		$query='SELECT MAX(uri_id)+1 FROM onto_uri';
		$result=pmb_mysql_query($query,$dbh);
		
		if(pmb_mysql_num_rows($result)){
			$max=pmb_mysql_result($result,0,0);
		}
		if(!$max) $max =1;
		
		$query='SELECT 1 FROM onto_uri WHERE uri="'.addslashes($class_uri.$max).'"';
		$result=pmb_mysql_query($query,$dbh);
		if(!pmb_mysql_error($dbh) && !pmb_mysql_num_rows($result)){
			$last_uri=$class_uri.$max;
		}else{
			do{
				$max++;
				$query='SELECT 1 FROM onto_uri WHERE uri="'.addslashes($class_uri.$max).'"';
				$result=pmb_mysql_query($query,$dbh);
			}while (pmb_mysql_num_rows($result));
		}
		
		$last_uri=$class_uri.$max;
		$query='INSERT INTO onto_uri SET uri="'.addslashes($last_uri).'"';
		pmb_mysql_query($query,$dbh);
		
		//On initialise last_uri.
		self::$last_uri=$last_uri;
		return self::$last_uri;
	} // end of member function get_new_uri

	/**
	 * Génère une URI temporaire (basée sur microtime ?)
	 * @param string class_uri URI de la classe d'objets
	 * 
	 * @return void
	 * @static
	 * @access public
	 */
	static public function get_temp_uri($class_uri=""){
		$temp_uri = $class_uri."_temp_".(microtime(true)*10000);
		self::set_new_uri($temp_uri);
		return $temp_uri;
	} // end of member function get_temp_uri

	/**
	 * 
	 *
	 * @param string uri Vérifie si une URI est temporaire.

	 * @return void
	 * @access public
	 */
	static public function is_temp_uri($uri) {
		if(preg_match("/\_temp\_/", $uri)){
			return true;
		}else{
			return false;
		}
	} // end of member function is_temp_uri

	/**
	 * 
	 *
	 * @return void
	 * @access public
	 */
	static public function get_last_uri() {
		return self::$last_uri;
	} // end of member function get_last_uri

	static public function get_name_from_uri($uri,$pmb_name){
		$tmp=array();
		$tmp=preg_split("/\/|\#/", $uri);
		return trim($pmb_name.'_'.strtolower(end($tmp)));
	}
	
	static public function set_new_uri($uri){
		global $dbh;
		$query = "select uri_id from onto_uri where uri ='".addslashes($uri)."'";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			return pmb_mysql_result($result,0,0);
		}
		$query = "insert into onto_uri set uri = '".addslashes($uri)."'";
		$result = pmb_mysql_query($query,$dbh);
		return pmb_mysql_insert_id($dbh);
	}

	static public function get_uri($id_uri){
		global $dbh;
		$uri = '';
		$query = "select uri from onto_uri where uri_id ='".$id_uri."'";
		$result = pmb_mysql_query($query,$dbh);
		if($result && pmb_mysql_num_rows($result)){
			$uri = pmb_mysql_result($result,0,0);
		}
		return $uri;
	} 
	
	static public function get_id($uri){
		global $dbh;
		$id = 0;
		$query = "select uri_id from onto_uri where uri = '".addslashes($uri)."'";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$id = pmb_mysql_result($result,0,0);
		}
		return $id;		
	}
	
	static public function replace_temp_uri($temp_uri, $class_uri, $uri_prefix="") {
		global $dbh;
		
		if($uri_prefix){
			$class_uri=$uri_prefix;
		}
		$last_uri="";
		$max=1;
		//On cherche le max des id + 1
		$query='SELECT MAX(uri_id)+1 FROM onto_uri';
		$result=pmb_mysql_query($query,$dbh);
		
		if(pmb_mysql_num_rows($result)){
			$max=pmb_mysql_result($result,0,0);
		}
		
		$query='SELECT 1 FROM onto_uri WHERE uri="'.addslashes($class_uri.$max).'"';
		$result=pmb_mysql_query($query,$dbh);
		if(!pmb_mysql_error($dbh) && !pmb_mysql_num_rows($result)){
			$last_uri=$class_uri.$max;
		}else{
			do{
				$max++;
				$query='SELECT 1 FROM onto_uri WHERE uri="'.addslashes($class_uri.$max).'"';
				$result=pmb_mysql_query($query,$dbh);
			}while (pmb_mysql_num_rows($result));
		}
		
		$last_uri=$class_uri.$max;
		$query='update onto_uri SET uri="'.addslashes($last_uri).'" where uri="'.$temp_uri.'"';
		pmb_mysql_query($query,$dbh);
		
		//On initialise last_uri.
		self::$last_uri=$last_uri;
		return self::$last_uri;	
	}
	
	/**
	 * Supprime une uri de la table onto_uri
	 * @param string $uri
	 */
	static public function delete_uri($uri) {
		$query = 'delete from onto_uri where uri="'.addslashes($uri).'"';
		pmb_mysql_query($query);
	}
} // end of onto_common_uri

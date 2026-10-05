<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_mysqli.class.php,v 1.2 2023/02/10 13:47:32 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class pmb_mysqli {
	
	/**
	 * 
	 * @var array
	 */
	public static $mysqli_types;
	
	/**
	 * 
	 * @var array
	 */
	public static $mysqli_flags;
	
	/**
	 * 
	 * @return mysqli
	 */
	public static function get_connection($link_identifier = null) {
		global $dbh;
		if ($link_identifier == null) {
			return $dbh;
		}
		return $link_identifier;
	}
	
	/**
	 * 
	 * @param string $server
	 * @param string $username
	 * @param string $password
	 * @param string $dbname
	 * @param string $port
	 * @param string $socket
	 * @return mysqli
	 */
	public static function init_connection($server = null, $username = null, $password = null, $dbname = null, $port = null, $socket = null) {
		
	    /* PHP >= 8.1 : Desactivation du rapport d'erreur defini par defaut a : MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT (auparavant : MYSQLI_REPORT_OFF).*/
	    mysqli_report(MYSQLI_REPORT_OFF);
	    
	    return new mysqli($server, $username, $password, $dbname, $port, $socket);
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function get_mysqli_types() {
		if (!isset(static::$mysqli_types)) {
			static::$mysqli_types = array();
			$constants = get_defined_constants(true);
			foreach ($constants['mysqli'] as $c => $n) {
				if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) {
					static::$mysqli_types[$n] = $m[1];
				}
			}
		}
		return static::$mysqli_types;
	}
	
	/**
	 * 
	 * @return array:
	 */
	public static function get_mysqli_flags() {
		if (!isset(static::$mysqli_flags)) {
			static::$mysqli_flags = array();
			$constants = get_defined_constants(true);
			foreach ($constants['mysqli'] as $c => $n) {
				if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m)) {
					if (!array_key_exists($n, static::$mysqli_flags)) {
						static::$mysqli_flags[$n] = strtolower(str_replace('PRI_KEY','PRIMARY_KEY',$m[1]));
					}
				}
			}
		}
		return static::$mysqli_flags;
	}

} # fin de définition de la classe pmb_mysqli



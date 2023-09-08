<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: api.inc.php,v 1.18 2015-10-05 12:02:10 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Liste de fonctions utiles
// connectFtp($url,$user,$password) renvoie un identifiant de connexion ou rien si erreur de connexion

function connectFtp($url="", $user="", $password="", $chemin, &$msg_) {
	global $msg;

	$conn_id = @ftp_connect($url);
	if ($conn_id) {
		// login with username and password
		$login_result = @ftp_login($conn_id, $user, $password);
		if (!$login_result) {
			$msg_=$msg["sauv_api_connect_failed"];
			return "";
		}
		$chdir_result=@ftp_chdir($conn_id,$chemin);
		if (!$chdir_result) {
			$msg_=$msg["sauv_api_failed_path"];
			return "";
		}
	} else {
		$msg_=$msg["sauv_api_failed_host"];
		return "";
	}
	return $conn_id;
}

function abort($msg_,$logid) {
	global $msg;
	$requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort : ".addslashes($msg_)."') where sauv_log_id=".$logid;
	@pmb_mysql_query($requete);
	echo sprintf($msg["sauv_api_failed_cancel"],$msg_);
	exit();
}

/*
Plus tard pour Dimitri & le planificateur des tâches
function stop($msg_,$logid) {
	global $msg;
	$requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort : ".addslashes($msg_)."') where sauv_log_id=".$logid;
	@pmb_mysql_query($requete);
	return sprintf($msg["sauv_api_failed_cancel"],$msg_);
}
*/
function abort_copy($msg_,$logid) {
	global $msg;
	$requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort Copy : ".addslashes($msg_)."') where sauv_log_id=".$logid;
	@pmb_mysql_query($requete);
	echo sprintf($msg["sauv_api_copy_failed_cancel"],$msg_);
	exit();
}

/*
Plus tard pour Dimitri & le planificateur des tâches
function stop_copy($msg_,$logid) {
	global $msg;
	$requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Abort Copy : ".addslashes($msg_)."') where sauv_log_id=".$logid;
	@pmb_mysql_query($requete);
	return sprintf($msg["sauv_api_copy_failed_cancel"],$msg_);
}
*/
function write_log($msg_,$logid) {
	$requete="update sauv_log set sauv_log_messages=concat(sauv_log_messages,'Log : ".addslashes($msg_)."\n') where sauv_log_id=".$logid;
	@pmb_mysql_query($requete);
}

function create_statement($table) {
	
	global $dbh;
	$requete = "SHOW CREATE TABLE $table";
	$result = pmb_mysql_query($requete, $dbh);
	if ($result) {
		$create = pmb_mysql_fetch_row($result);
		$create[1] = str_replace("\r"," ", $create[1]);
		$create[1] = str_replace("\n"," ", $create[1]);
		$create[1] .= ";";
		return $create[1];
	} else {
		return "";
	}
}

function table_dump($table_name,$fp) {

		global $dbh;

		fwrite($fp,"#".$table_name."\r\n");
		
		fwrite($fp,"drop table if exists ".$table_name.";\r\n");
		
		//Get strucutre
		fwrite($fp,create_statement($table_name)."\n");

		//enumerate tables

		$update_a_faire=0; /* permet de gérer les id auto_increment qui auraient pour valeur 0 */
		//parse the field info first
		$res2=pmb_mysql_query("select * from ${table_name} order by 1 ",$dbh);
		if ($res2) {
			$nf=pmb_mysql_num_fields($res2);
			$nr=pmb_mysql_num_rows($res2);
		}
		$fields = '';
		$values = '';
		if ($nf) {
			for ($b=0;$b<$nf;$b++) {
				$fn=pmb_mysql_field_name($res2,$b);
			    $ft=pmb_mysql_field_type($res2,$b);
				$fs=pmb_mysql_field_len($res2,$b);
				$ff=pmb_mysql_field_flags($res2,$b);
				$is_numeric=false;
				switch(strtolower($ft))
					{
					case "int":
						$is_numeric=true;
						break;
						
					case "blob":
					    $is_numeric=false;
					    break;
					    
					case "real":
						$is_numeric=true;
						break;
	
					case "string":
						$is_numeric=false;
						break;
	
					case "unknown":
						switch(intval($fs))
							{
							case 4:
								// little weakness here...
								// there is no way (thru the PHP/MySQL interface)
								// to tell the difference between a tinyint and a year field type
								$is_numeric=true;
								break;
	
							default:
								$is_numeric=true;
								break;
							}
						break;
	
					case "timestamp":
						// Afin de résoudre le pb des timestamp pas corrects en restauration $is_numeric=true;
						$is_numeric=false;
						break;
					case "date":
						$is_numeric=false;
						break;
	
					case "datetime":
						$is_numeric=false;
						break;
	
					case "time":
						$is_numeric=false;
						break;
						
					case "geometry":
						$is_numeric=false;
						break;
	
					default:
						//future support for field types that are not recognized
						//(hopefully this will work without need for future modification)
						$is_numeric=true;
						//I'm assuming new field types will follow SQL numeric syntax..
						// this is where this support will breakdown
						break;
				}
	
				(string)$fields!="" ? $fields .= ', '.$fn : $fields .= $fn;
				$fna[$b] = $fn;
				$ina[$b] = $is_numeric;
			}
		}
		//parse out the table's data and generate the SQL INSERT statements in order to replicate the data itself...
		if ($nr) {
			for ($c=0;$c<$nr;$c++) {
				$row=pmb_mysql_fetch_row($res2);
				$values = '';
				for ($d=0;$d<$nf;$d++) {
					$data=strval($row[$d]);
					if ($ina[$d]==true) {
						((string)$values!="")? $values.= ', '.floatval($data) : $values.= floatval($data);
					} else {
						((string)$values!="")? $values.=", \"".pmb_mysql_real_escape_string($data)."\"" : $values.="\"".pmb_mysql_real_escape_string($data)."\"";
					}
				}
				fwrite($fp,"insert into $table_name ($fields) values ($values);\r\n");
				if ($update_a_faire==1) {
					$update_a_faire=0;
					fwrite($fp,"update $table_name set ".$cle_update."='0' where ".$cle_update."='1';\r\n");
				}
			}
		}
		if ($res2) pmb_mysql_free_result($res2);
}

function read_infos($filename) {
   	$tInfo=array();
   	$f=fopen($filename,"r") or die("Le fichier n'existe pas !");
   	$line=fgets($f,8192);
   	$line=rtrim($line);
   	while ((!feof($f))&&($line!="#data-section")) {
   		$tLine=explode(" : ",$line);
   		$tInfo[substr($tLine[0],1)]=$tLine[1];
   		$line=fgets($f,8192);
   		$line=rtrim($line);
   	}
   	return $tInfo;    	
}
?>
<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesBackup.class.php,v 1.8 2023/09/22 07:34:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($class_path."/external_services.class.php");
require_once($base_path."/admin/sauvegarde/lib/api.inc.php");
require_once("$class_path/crypt.class.php");

class pmbesBackup extends external_services_api_class {
	
	/* Liste des groupes de tables */
	public function listGroupsTables() {
	    if(!$this->has_user_rights(SAUV_AUTH)) {
	        return array();
	    }
		$list = array();
		$query = "select sauv_table_id, sauv_table_nom, sauv_table_tables from sauv_tables order by sauv_table_nom";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_assoc($result)) {
		    $list[] = array(
				"sauv_table_id" => $row["sauv_table_id"],
				"sauv_table_nom" => encoding_normalize::utf8_normalize($row["sauv_table_nom"]),
				"sauv_table_tables" => encoding_normalize::utf8_normalize($row["sauv_table_tables"]),
			);
		}
		return $list;
	}
	
	/* Liste des tables non intégrées dans les groupes */
	public function listTablesUnsaved() {
	    if(!$this->has_user_rights(SAUV_AUTH)) {
	        return array();
	    }
		//Récupération de la liste des tables dans les groupes
		$requete = "select sauv_table_id, sauv_table_nom, sauv_table_tables from sauv_tables ";
		$resultat = pmb_mysql_query($requete);
		$tTables = "";
		while ($res = pmb_mysql_fetch_object($resultat)) {
			$tTables.= ",".$res -> sauv_table_tables;
		}
				
		//Recherche des tables non intégrées dans les groupes
		$tTablesSelected = explode(",", $tTables);
		$requete = "show tables";
		$resultat = pmb_mysql_query($requete) or die(pmb_mysql_error());
		$unsavedTables = array();
		while (list ($table) = pmb_mysql_fetch_row($resultat)) {
			$as=array_search($table, $tTablesSelected);
			if (( $as!== false)&&($as!==null)) {
				//Do nothing
			} else {
				$unsavedTables[] = $table;
			}
		}
		return $unsavedTables;
	}
	
	/* Lancement d'une sauvegarde */
	public function launchBackup($id_sauvegarde) {
		global $base_path,$PMBuserid, $PMBusername, $msg;
		
		if(!$this->has_user_rights(SAUV_AUTH)) {
		    return array();
		}
		$id_sauvegarde = intval($id_sauvegarde);
		$report=array();
	
		//Recherche des paramètres de la sauvegarde
		$requete="select sauv_sauvegarde_nom, sauv_sauvegarde_file_prefix, sauv_sauvegarde_tables, sauv_sauvegarde_lieux, sauv_sauvegarde_users,sauv_sauvegarde_compress,sauv_sauvegarde_compress_command, sauv_sauvegarde_crypt from sauv_sauvegardes where sauv_sauvegarde_id=".$id_sauvegarde;
		$resultat=pmb_mysql_query($requete);
		$res=pmb_mysql_fetch_object($resultat);
		$liste_users = explode(",",$res->sauv_sauvegarde_users);

		$access_allow=false;
		foreach ($liste_users as $user) {
			if ($user == $PMBuserid) {
				$access_allow=true;
			}
		}
		if (!$access_allow) {
			$report[] = "Error : User ".$PMBusername." don't have permissions";
			$result = array(
				"logid" => "",
				"report" => $report
			);
			return $result;
//			throw new Exception("Error : User ".$PMBusername." don't have permissions");
		}

		//Création du log dans la base de log
		$log_messages="Start time : ".date("H:i",time())."\r\n";
		$report[] = $log_messages;
		$log_file=$res->sauv_sauvegarde_file_prefix."_".date("Y_m_d",time());
		$report[] = $log_file;
		//Recherche si nom de fichier déjà existant
		$n_version=0;
		if (!defined('SAUV_PREFIX')) define( 'SAUV_PREFIX', "" );
			else define( 'SAUV_PREFIX', SAUV_PREFIX."_" );
		$log_file_test=SAUV_PREFIX.$log_file.".sav";
		while (file_exists($base_path."/admin/backup/backups/".$log_file_test)) {
			$n_version++;
			$log_file_test=$log_file."_".$n_version.".sav";
		}
		$log_file=$log_file_test;
					
		$requete="insert into sauv_log (sauv_log_start_date,sauv_log_file,sauv_log_messages,sauv_log_userid) values(now(),'$log_file','$log_messages',$PMBuserid)";
		$res_query = pmb_mysql_query($requete);
		if (!$res_query) {
			$report[] = pmb_mysql_error();
			$result = array(
				"logid" => "",
				"report" => $report
			);
			return $result;
		}
		$logid=pmb_mysql_insert_id();
		
		//Création du fichier d'export
		$path_name = $base_path."/admin/backup/backups/".$log_file;
		$fe=@fopen($path_name,"w+");
		if (!$fe) {
			$report[] = stop("The file $log_file could not be created",$logid);
			$result = array(
				"logid" => $logid,
				"report" => $report
			);
			return $result;
		}
		fwrite($fe,"#Name : ".$res->sauv_sauvegarde_nom."\r\n");
		fwrite($fe,"#".$log_messages);
		fwrite($fe,"#Date : ".date("Y-m-d",time())."\r\n");
		
		//Récupération des tables
		$requete="select sauv_table_tables from sauv_tables where sauv_table_id in (".$res->sauv_sauvegarde_tables.")";
		$resultat=pmb_mysql_query($requete);
		if (!$resultat) {
			$report[] = $requete;
			$report[] = stop("Tables could not be retrived",$logid);
			$result = array(
				"logid" => $logid,
				"report" => $report
			);
			return $result;
		}
		$tables=array();
		while (list($sauv_table_tables)=pmb_mysql_fetch_row($resultat)) {
			$tSauv_table_tables=explode(",",$sauv_table_tables);
			for ($i=0; $i<count($tSauv_table_tables); $i++) {
				$as=array_search($tSauv_table_tables[$i],$tables);
				if (($as!==null)&&($as!==false)) {
					//
				} else {
					$tables[]=$tSauv_table_tables[$i];
				}
			}
		}
	
		//Export SQL		
		$temp_file="temp_".(SAUV_PREFIX!=""?SAUV_PREFIX."_":"").$res->sauv_sauvegarde_file_prefix."_".date("d_m_Y",time()).".sql";
		$temp_path_name = $base_path."/admin/backup/backups/".$temp_file;

		$ftemp=@fopen($temp_path_name,"w+");
		
		if (!$ftemp) {
			$report[] = stop("Temporary file for SQL export could not be created",$logid);
			$result = array(
				"logid" => $logid,
				"report" => $report
			);
			return $result;
		}
			
		//Log de l'entête
		fwrite($fe,"#Groups : ".$res->sauv_sauvegarde_tables."\r\n");
		fwrite($fe,"#Tables : ".implode(",",$tables)."\r\n");
		
		//Ecriture du fichier SQL
		for ($i=0; $i<count($tables); $i++) {
			table_dump($tables[$i],$ftemp);
		}
	
		write_log("SQL OK : SQL export is OK",$logid);
		$report[] = "SQL OK : SQL export is OK";
		fclose($ftemp);
			
		//Compression éventuelle
		fwrite($fe,"#Compress : ".$res->sauv_sauvegarde_compress."\r\n");
		if ($res->sauv_sauvegarde_compress==1) {
			fwrite($fe,"#Compress commands : ".$res->sauv_sauvegarde_compress_command."\r\n");
			$command=explode(":",$res->sauv_sauvegarde_compress_command);
			
			switch ($command[0]) {
				case 'external' :
					$c_command=str_replace("%s",$temp_path_name,$command[1]);
					exec($c_command);
					@unlink($temp_path_name);
					if (!file_exists($temp_path_name)) {
						$report[] = stop("Compression failed",$logid);
						$result = array(
							"logid" => $logid,
							"report" => $report
						);
						return $result;
					}
				break;
				case 'internal' :
					$fz=bzopen($temp_path_name.".bz2","w");

					if (!$fz) {
						$report[] = stop("Compression failed",$logid);
						$result = array(
							"logid" => $logid,
							"report" => $report
						);
						return $result;
					}
					$ftemp=fopen($temp_path_name,"r");
					if (!$ftemp) {
						$report[] = stop("Compression failed",$logid);
						$result = array(
							"logid" => $logid,
							"report" => $report
						);
						return $result;
					}
					$to_crypt=fread($ftemp,filesize($temp_path_name));
					bzwrite($fz,$to_crypt);
					bzclose($fz);
					fclose($ftemp);
					unlink($temp_path_name);
					$temp_path_name=$temp_path_name.".bz2";
				break;
			}
			write_log("Compress OK : Compress is OK",$logid);
			$report[] = "Compress OK : Compress is OK";
		} else {
			$temp_path_name=$base_path."/admin/backup/backups/".$temp_file;
		}
			
		//cryptage ?
		if ($res->sauv_sauvegarde_crypt==1) {
			//Recherche des paramètres de cryptage
			$requete="select sauv_sauvegarde_key1, sauv_sauvegarde_key2 from sauv_sauvegardes where sauv_sauvegarde_id=".$currentSauv;
			$resultat=pmb_mysql_query($requete);
			
			$res=pmb_mysql_fetch_object($resultat);
			
			//Ajout du cryptage
			$fe=@fopen($path_name,"a");
			if (!$fe) {
				$report[] = stop("The file $path_name could not be opened",$logid);
				$result = array(
					"logid" => $logid,
					"report" => $report
				);
				return $result;
			}
			fwrite($fe,"#Crypt : 1\r\n");
			
			$ftemp=@fopen($temp_path_name,"r");
			if (!$ftemp) {
				$report[] = stop("Temporary file for SQL export could not be opened for crypting",$logid);
				$result = array(
					"logid" => $logid,
					"report" => $report
				);
				return $result;
			}
			
			if ($res->sauv_sauvegarde_key1=="") $cle1=$sauvegarde_cle_crypt1; else $cle1=$res->sauv_sauvegarde_key1;
			if ($res->sauv_sauvegarde_key2=="") $cle2=$sauvegarde_cle_crypt2; else $cle2=$res->sauv_sauvegarde_key2;
			
			$cr=new Crypt($cle1,$cle2);
			$to_crypt=fread($ftemp,filesize($temp_path_name));
			fclose($ftemp);
			
			$ftemp=@fopen($temp_path_name,"w+");
			if (!$ftemp) {
				$report[] = stop("Temporary file for SQL export could not be opened for crypting",$logid);
				$result = array(
					"logid" => $logid,
					"report" => $report
				);
				return $result;
			}
			
			fwrite($ftemp,$cr->getCrypt("PMBCrypt"));
			fwrite($ftemp,$cr->getCrypt($to_crypt));
			
			write_log("Crypt OK : Crypting file is OK",$logid);
			$result[] = "Crypt OK : Crypting file is OK";
			
			fclose($ftemp);
//			
		} else {
//			$result[] = $msg["sauv_misc_end_message"];
		}
			
		//Succeed - Executer cette requete si le fichier a bien été crée
		$requete="update sauv_log set sauv_log_succeed=1 where sauv_log_id=".$logid;
		pmb_mysql_query($requete);
	
		$fe=@fopen($path_name,"a");
		$fsql=@fopen($temp_path_name,"rb");
		
		if ((!$fe)||(!$fsql)) {
			$report[] = stop("Could not create final file",$logid);
			$result = array(
				"logid" => $logid,
				"report" => $report
			);
			return $result;
		}
	
		//$to_happend=fread($fsql,filesize($temp_file));
		//fwrite($fe,"#data-section\r\n".$to_happend);
		
		// MaxMan: modified because this error:
		//Fatal error: Allowed memory size of 8388608 bytes exhausted 
		//(tried to allocate 6495315 bytes) in 
		///var/www/pmb/admin/sauvegarde/end_save.php on line 52
		
		fwrite($fe,"#data-section\r\n");
		do {
		   $to_append = fread($fsql, 8192);
		   if (strlen($to_append) == 0) {
		       break;
		   }
		   fwrite($fe,$to_append);
		} while (true);
			
		fclose($fsql);
		fclose($fe);

		unlink($temp_path_name);

		//Log : Backup complet
		write_log("Backup complete",$logid);
		$report[] = "Backup complete";
		
		//Récupération de la taille du fichier
		$tmp_msg = '';
		if ($tmp_size = filesize($path_name)) {
			if ($tmp_size < 1000) {
				$tmp_msg = "Backup size : ".round($tmp_size,3)." bytes";
			} else {
				$tmp_size = $tmp_size / 1024;
				if ($tmp_size < 1000) {
					$tmp_msg = "Backup size : ".round($tmp_size,3)." Ko";
				} else {
					$tmp_size = $tmp_size / 1024;
					$tmp_msg = "Backup size : ".round($tmp_size,3)." Mo";
				}
			}
		}
		if (trim($tmp_msg)) {
			write_log($tmp_msg,$logid);
			$report[] = $tmp_msg;
		}
		
		//Succeed
		$requete="update sauv_log set sauv_log_succeed=1 where sauv_log_id=".$logid;
		pmb_mysql_query($requete);

		//Récupération des lieux
		$requete="select sauv_sauvegarde_lieux from sauv_sauvegardes where sauv_sauvegarde_id=".$id_sauvegarde;
		$resultat=pmb_mysql_query($requete);
		$lieux=pmb_mysql_result($resultat,0,0);

		$tLieux=explode(",",$lieux);
				
		//Pour chaque lieu, transférer le fichier
		for ($i=0; $i<count($tLieux); $i++) {
			$requete="select sauv_lieu_nom,sauv_lieu_url, sauv_lieu_protocol, sauv_lieu_login, sauv_lieu_password, sauv_lieu_host from sauv_lieux where sauv_lieu_id=".$tLieux[$i];
			$resultat=pmb_mysql_query($requete);
			$res=pmb_mysql_fetch_object($resultat);
			$tfilecopy=explode("/",$path_name);
			$filecopy=$tfilecopy[count($tfilecopy)-1];
			switch ($res->sauv_lieu_protocol) {
				//Si protocol = file
				case "file" :
					if (!copy($path_name,$res->sauv_lieu_url."/".$filecopy)) {
						$report[] = stop("Copy : ".$res->sauv_lieu_nom." : Failed",$logid);	
					} else {
						write_log("Copy : ".$res->sauv_lieu_nom." : Succeed",$logid);
						$report[] = "Copy : ".$res->sauv_lieu_nom." : Succeed";
					}
				break;
				//Si protocol = ftp
				case "ftp" :
					$msg_="";
					
					//Connexion + passage dans le répertoire concerné
					$conn_id=connectFtp($res->sauv_lieu_host, $res->sauv_lieu_login, $res->sauv_lieu_password, $res->sauv_lieu_url, $msg_);

					if ($conn_id=="") {
						abort_copy("Copy : ".$res->sauv_lieu_nom." : Failed : ".$msg_,$logid);
					} else {
						//Transfert
						if (!ftp_put($conn_id, $filecopy, $path_name, FTP_BINARY)) {
							$report[] = stop_copy("Copy : ".$res->sauv_lieu_nom." : Failed",$logid);
							$result = array(
								"logid" => $logid,
								"report" => $report
							);
							return $result;
						} else {
							write_log("Copy : ".$res->sauv_lieu_nom." : Succeed",$logid);
							$report[] = "Copy : ".$res->sauv_lieu_nom." : Succeed";
						}
					}

				break;
			}
		}
		$report[] = $msg["sauv_misc_end_message"];
		
		$result = array(
			"logid" => $logid,
			"report" => $report
		);
		return $result;
	}
	
	/* liste des jeux de sauvegardes */
	public function listSetBackup() {
	    if(!$this->has_user_rights(SAUV_AUTH)) {
	        return array();
	    }
		$list = array();
		$query = "select sauv_sauvegarde_id, sauv_sauvegarde_nom, sauv_sauvegarde_file_prefix, sauv_sauvegarde_tables, 
			sauv_sauvegarde_lieux, sauv_sauvegarde_users,sauv_sauvegarde_compress, sauv_sauvegarde_compress_command,
			sauv_sauvegarde_crypt, sauv_sauvegarde_key1, sauv_sauvegarde_key2 from sauv_sauvegardes ";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_assoc($result)) {
		    $list[] = array(
				"sauv_sauvegarde_id" => $row["sauv_sauvegarde_id"],
				"sauv_sauvegarde_nom" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_nom"]),
				"sauv_sauvegarde_file_prefix" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_file_prefix"]),
				"sauv_sauvegarde_tables" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_tables"]),
				"sauv_sauvegarde_lieux" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_lieux"]),
				"sauv_sauvegarde_users" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_users"]),
				"sauv_sauvegarde_compress" => $row["sauv_sauvegarde_compress"],
				"sauv_sauvegarde_compress_command" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_compress_command"]),
				"sauv_sauvegarde_crypt" => $row["sauv_sauvegarde_crypt"],
//				"sauv_sauvegarde_key1" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_key1"]),
//				"sauv_sauvegarde_key2" => encoding_normalize::utf8_normalize($row["sauv_sauvegarde_key2"]),
			);
		}
		return $list;
	}
	
	public function listSauvPerformed() {
	    if(!$this->has_user_rights(SAUV_AUTH)) {
	        return array();
	    }
		$list=array();
		
		$requete="select sauv_log_id,sauv_log_start_date,sauv_log_file,sauv_log_succeed,sauv_log_messages,concat(prenom,' ',nom) as name from sauv_log,users where sauv_log_userid=userid";
//   	 	if (count($this->date_saving)!=0) {
//    			$dates=implode("','",$this->date_saving);
//    			$dates="'".$dates."'";
//  	  		$requete.=" and sauv_log_start_date in (".$dates.")";
//	    	}
    	$requete.=" order by sauv_log_start_date desc";
    	$res=pmb_mysql_query($requete);
    	while ($row = pmb_mysql_fetch_assoc($res)) {
    	    $list[] = array(
				"sauv_log_id" => $row["sauv_log_id"],
				"sauv_log_start_date" => encoding_normalize::utf8_normalize($row["sauv_log_start_date"]),
				"sauv_log_file" => encoding_normalize::utf8_normalize($row["sauv_log_file"]),
				"sauv_log_succeed" => encoding_normalize::utf8_normalize($row["sauv_log_succeed"]),
				"sauv_log_messages" => encoding_normalize::utf8_normalize($row["sauv_log_messages"]),
				"name" => encoding_normalize::utf8_normalize($row["name"]),
			);
		}
		return $list;
	}
	
	public function deleteSauvPerformed($ids_log) {
		global $base_path;
		
		if(!$this->has_user_rights(SAUV_AUTH)) {
		    return array();
		}
		$sauv_log = explode(",",$ids_log);
	
		if (!is_array($sauv_log)) {
			return false;
//		    $msg["sauv_list_unselected_set"]."\");
	    } else {
	    	for ($i=0; $i<count($sauv_log); $i++) {
	    		$sauv_log[$i] = intval($sauv_log[$i]);
	    		$requete="select sauv_log_file from sauv_log where sauv_log_id=".$sauv_log[$i];
	    		$resultat=pmb_mysql_query($requete);
	    		if ($resultat) {
		    		$file_to_del=pmb_mysql_result($resultat,0,0);
		    		@unlink($base_path."/admin/backup/backups/".$file_to_del);
		    		$requete="delete from sauv_log where sauv_log_id=".$sauv_log[$i];
		    		$rs = pmb_mysql_query($requete);
		    		if (!$rs) {
		    			return false;
		    		}
		    	} else {
	    			return false;
	    		}
	    	}
	    	return true;
	    }
	}
}
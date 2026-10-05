<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sauvegarde_list.class.php,v 1.19 2023/04/28 10:07:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;

//Formulaire de gestion des listes de sauvegardes
include ($include_path."/templates/sauvegarde_list.tpl.php");

class sauvegarde_list {
	
	//Données
	public $date_saving; //Liste des dates de sauvegarde
	public $logid; //Liste des fichiers à supprimer ou a restaurer
	public $act; //Action

    public function __construct() {
    	global $date_saving;
    	global $logid;
    	global $act;
    	
    	$this->date_saving=$date_saving;
    	$this->logid=$logid;
    	$this->act=$act;
    }
    
    public function proceed() {
    	global $msg;
    	//Actions possibles :
    	//delete : suppression des jeux cochés
    	//restore : restoration immédiate des jeux cochés
    	//chaine vide : affichage
    	switch ($this->act) {
    		
    		case "delete":
    			if (!is_array($this->logid)) {
    				echo "<script>alert(\"".$msg["sauv_list_unselected_set"]."\"); history.go(-1);</script>";
    			} else {
    				for ($i=0; $i<count($this->logid); $i++) {
    					$requete="select sauv_log_file from sauv_log where sauv_log_id=".$this->logid[$i];
    					$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
    					$file_to_del=pmb_mysql_result($resultat,0,0);
    					@unlink("admin/backup/backups/".$file_to_del);
    					$requete="delete from sauv_log where sauv_log_id=".$this->logid[$i];
    					pmb_mysql_query($requete) or die(pmb_mysql_error());
    				}
    			}
    		break;
    		default:
    			//Do nothing
    		break;
    	}
    	return $this->showForm();
    }
    
    public function read_infos($filename) {
    	$tInfo=array();
    	$f=@fopen($filename,"r");
    	if (!$f) return $tInfo;
    	$line=fgets($f,4096);
    	$line=rtrim($line);
    	while ((!feof($f))&&($line!="#data-section")) {
    		$tLine=explode(" : ",$line);
    		$tInfo[substr($tLine[0],1)]=$tLine[1];
    		$line=fgets($f,4096);
    		$line=rtrim($line);
    	}
    	fclose($f);
    	return $tInfo;    	
    }
    
    public function showForm() {
    	global $form;
    	global $msg;
    	
    	//Récupération des dates présentes dans la base
    	if (!is_array($this->date_saving)) $this->date_saving=array();
    	$date_list="<select name=\"date_saving[]\" multiple>\n";
    	$requete="select sauv_log_start_date from sauv_log group by sauv_log_start_date order by sauv_log_start_date desc";
    	$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
    	while ($res=pmb_mysql_fetch_object($resultat)) {
    		$tDate=explode("-",$res->sauv_log_start_date);
    		$date_list.="<option value=\"".$res->sauv_log_start_date."\"";
    		$as=array_search($res->sauv_log_start_date,$this->date_saving);
    		if (($as!==null)&&($as!==false)) $date_list.=" selected";
    		$date_list.=">".$tDate[2]."/".$tDate[1]."/".$tDate[0]."</option>\n";
    	}
    	$date_list.="</select>";
    	
    	$form=str_replace("!!date_saving!!",$date_list,$form);
    	
    	$requete="select sauv_log_id,sauv_log_start_date,sauv_log_file,sauv_log_succeed,sauv_log_messages,concat(prenom,' ',nom) as name from sauv_log,users where sauv_log_userid=userid";
    	if (count($this->date_saving)!=0) {
    		$dates=implode("','",$this->date_saving);
    		$dates="'".$dates."'";
    		$requete.=" and sauv_log_start_date in (".$dates.")";
    	}
    	$requete.=" order by sauv_log_start_date desc";
    	$resultat=pmb_mysql_query($requete);
    	
		$sty="class='brd center'";
    	
    	$sauvegarde_list="<table class='center' celpadding=0 cellspacing=0>\n";
		$sauvegarde_list.="<th $sty>&nbsp;</th><th $sty>&nbsp;</th>";
    	$sauvegarde_list.="<th $sty colspan='4'>".$msg["sauv_list_th_info_set"]."</th>";
    	$sauvegarde_list.="<th $sty colspan='4'>".$msg["sauv_list_th_info_file"]."</th>";
    	$sauvegarde_list.="<th $sty colspan='3' rowspan='2'>".$msg["sauv_list_th_actions"]."</th>";
    	$sauvegarde_list.="<tr>";
		$sauvegarde_list.="<th $sty>&nbsp;</th><th $sty>&nbsp;</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_filename"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_date"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_final_state"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_user"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_set"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_hour"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_compr"]."</th>";
    	$sauvegarde_list.="<th $sty>".$msg["sauv_list_th_crypt"]."</th>";
   		$sauvegarde_list.="</tr>\n";
    	while ($res=pmb_mysql_fetch_object($resultat)) {
    		$sauvegarde_list.="<tr><td $sty><input type=\"checkbox\" name=\"logid[]\" value=\"".$res->sauv_log_id."\"></td>";
    		$sauvegarde_list.="<td $sty>";
    		if ($res->sauv_log_succeed==1) { 
    			$infos=$this->read_infos("admin/backup/backups/".$res->sauv_log_file);
    			if (count($infos)==0) {
    				$res->sauv_log_succeed=0;
    			}
    		}
    		if ($res->sauv_log_succeed==1) { 
    			$succeed="sauv_succeed.png"; 
    			$succeed_message=$msg["sauv_list_succeed"];
    		} else { 
    			$succeed="sauv_failed.png";
    			//Recherche du message d'erreur
    			$tMessages=explode("\n",$res->sauv_log_messages);
    			$succeed_message="";
    			for ($i=0; $i<count($tMessages); $i++) {
    				if (substr($tMessages[$i],0,5)=="Abort") {
    					$succeed_message=$tMessages[$i];
    					break;
    				}
    			}
    			if ($succeed_message=="") $succeed_message=$msg["sauv_list_special_error"];
    			$infos=array();
    		}
    		$sauvegarde_list.="<img src=\"images/".$succeed."\" width=20 height=20></td>";
    		$sauvegarde_list.="<td $sty>".$res->sauv_log_file."</td>";
    		$sauvegarde_list.="<td $sty>".$res->sauv_log_start_date."</td>";
    		$sauvegarde_list.="<td $sty>".$succeed_message."</td>";
    		$sauvegarde_list.="<td $sty>".$res->name."</td>";
    		if ($res->sauv_log_succeed==1) {
    			$sauvegarde_list.="<td $sty>".$infos["Name"]."</td>";
    			$sauvegarde_list.="<td $sty>".$infos["Start time"]."</td>";
    			$sauvegarde_list.="<td $sty>";
    			if (isset($infos["Compress"]) && $infos["Compress"]=="1") $sauvegarde_list.="<img src=\"images/sauv_compress.png\">"; 
    				else $sauvegarde_list.="&nbsp;";
    			$sauvegarde_list.="</td>";
    			$sauvegarde_list.="<td $sty>";
    			if (isset($infos["Crypt"]) && $infos["Crypt"]=="1") $sauvegarde_list.="<img src=\"images/sauv_crypted.png\">"; 
    				else $sauvegarde_list.="<img src=\"images/sauv_noncrypted.png\">";
    			$sauvegarde_list.="</td>";
    			$sauvegarde_list.="<td $sty><input type=\"button\" value=\"".$msg["sauv_list_download"]."\" class=\"bouton\" onClick=\"document.location='admin/sauvegarde/download.php?logid=".$res->sauv_log_id."'\"></td>";
    			$sauvegarde_list.="<td $sty><input type=\"button\" value=\"".$msg["sauv_list_restaure"]."\" class=\"bouton\" onClick=\"openPopUp('admin/sauvegarde/restaure.php?filename=".rawurlencode($res->sauv_log_file)."&logid=".$res->sauv_log_id."&critical=','restore_win',700,500,-2,-2,'menubar=no,resizable=yes,scrollbars=yes');\"></td>";    			
    		} else {
    			$sauvegarde_list.="<td $sty colspan=4>".$msg["sauv_list_fnodisp"]."</td>";
    			$sauvegarde_list.="<td $sty>&nbsp;</td>";
    			$sauvegarde_list.="<td $sty>&nbsp;</td>";    			
    		}
    		$sauvegarde_list.="<td $sty><input type=\"button\" value=\"".$msg["sauv_list_log"]."\" class=\"bouton\" onClick=\"openPopUp('admin/sauvegarde/show_log.php?logid=".$res->sauv_log_id."','show_log',300,300,-2,-2,'menubar=no,resizable=1,scrollbars=yes');\"></td>";
    		$sauvegarde_list.="</tr>\n";
    	}
    	$sauvegarde_list.="</table>\n";
    	$form=str_replace("!!sauvegarde_list!!",$sauvegarde_list,$form);
    	return $form;
    }
}

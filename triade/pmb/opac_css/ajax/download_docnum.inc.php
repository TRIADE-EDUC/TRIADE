<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: download_docnum.inc.php,v 1.9 2023/10/24 09:57:08 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $select_noti;
//gestion des droits
require_once($class_path."/acces.class.php");
require_once($class_path."/explnum.class.php");

switch($sub){
	case 'gen_list':	
		if (!empty($select_noti)) {
			$id_notices = explode(",",$select_noti);
		} else $id_notices=$_SESSION["cart"];
		doc_num_gen_list($id_notices);
		break;
}
function doc_num_gen_list($id_notices){
	global $msg, $gestion_acces_active,$gestion_acces_empr_notice,$gestion_acces_empr_docnum;
	global $opac_allow_download_docnums,$opac_url_base;
	
	if (!$opac_allow_download_docnums) return;
	
	$explnum_list=array();
	foreach($id_notices as $notice_id){
	    $notice_id = intval($notice_id);
		$query = "SELECT explnum_id from explnum where explnum_notice=$notice_id ";
		$query .= " union ";
		$query .= " select explnum_id from explnum ,bulletins where explnum_bulletin=bulletin_id and num_notice=$notice_id ";
		$result = pmb_mysql_query($query);
		if ($result) {
			while($row = pmb_mysql_fetch_object($result)){
				$explnum_id=$row->explnum_id;
			
				$res = pmb_mysql_query("SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_data, length(explnum_data) as taille,explnum_path, concat(repertoire_path,explnum_path,explnum_nomfichier) as path, repertoire_id FROM explnum left join upload_repertoire on repertoire_id=explnum_repertoire WHERE explnum_id = '$explnum_id' ");
				$ligne = pmb_mysql_fetch_object($res);
					
				$id_for_rigths = $ligne->explnum_notice;
				if($ligne->explnum_bulletin != 0){
					//si bulletin, les droits sont rattachés à la notice du bulletin, à défaut du pério...
					$req = "select bulletin_notice,num_notice from bulletins where bulletin_id =".$ligne->explnum_bulletin;
					$res = pmb_mysql_query($req);
					if(pmb_mysql_num_rows($res)){
						$r = pmb_mysql_fetch_object($res);
						$id_for_rigths = $r->num_notice;
						if(!$id_for_rigths){
							$id_for_rigths = $r->bulletin_notice;
						}
					}
				}
						
				//droits d'acces emprunteur/notice
				if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
					$ac= new acces();
					$dom_2= $ac->setDomain(2);
					$rights= $dom_2->getRights($_SESSION['id_empr_session'],$id_for_rigths);
				}
						
				//Accessibilité des documents numériques aux abonnés en opac
				$req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon ,notice_id FROM notice_statut, explnum, notices WHERE explnum_notice=notice_id AND statut=id_notice_statut  AND explnum_id='$explnum_id' ";
				$res_restriction_abo=pmb_mysql_query($req_restriction_abo);
				if(! pmb_mysql_num_rows($res_restriction_abo) ){// bulletin
					$req_restriction_abo="SELECT explnum_visible_opac, explnum_visible_opac_abon,notice_id
						FROM notice_statut, explnum, bulletins, notices
						WHERE explnum_bulletin = bulletin_id
						AND num_notice = notice_id
						AND statut = id_notice_statut
						AND explnum_id='$explnum_id' ";
					$res_restriction_abo=pmb_mysql_query($req_restriction_abo);
				}			
				$expl_num=pmb_mysql_fetch_array($res_restriction_abo);
			
				//droits d'acces emprunteur/document numérique
				if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
					$ac= new acces();
					$dom_3= $ac->setDomain(3);
					$docnum_rights= $dom_3->getRights($_SESSION['id_empr_session'],$explnum_id);
				}
					
				//Accessibilité (Consultation/Téléchargement) sur le document numérique aux abonnés en opac
				$req_restriction_docnum_abo = "SELECT explnum_download_opac, explnum_download_opac_abon FROM explnum,explnum_statut WHERE explnum_id='".$explnum_id."' AND explnum_docnum_statut=id_explnum_statut ";
					
				$result_docnum=pmb_mysql_query($req_restriction_docnum_abo);
				$docnum_expl_num=pmb_mysql_fetch_array($result_docnum,PMB_MYSQL_ASSOC);
				
				if( ($rights & 16 || (is_null($dom_2) && $expl_num["explnum_visible_opac"] && (!$expl_num["explnum_visible_opac_abon"] || ($expl_num["explnum_visible_opac_abon"] && $_SESSION["user_code"]))))
				&& ($docnum_rights & 8 || (is_null($dom_3) && $docnum_expl_num["explnum_download_opac"] && (!$docnum_expl_num["explnum_download_opac_abon"] || ($docnum_expl_num["explnum_download_opac_abon"] && $_SESSION["user_code"]))))){
					if (($ligne->explnum_data)||($ligne->explnum_path)) {
						$explnum_list[] = $ligne->explnum_id;
					}
				}
			}	
		}
	}
	if (count($explnum_list)) {
		if ($opac_allow_download_docnums == 1) {
			foreach ($explnum_list as $explnum_id) {
				print "<script>
					window.open('".$opac_url_base."doc_num_data.php?explnum_id=".$explnum_id."&force_download=1','_blank','');
					</script>";
			}
		}
		if ($opac_allow_download_docnums == 2) {
			$zip = new ZipArchive();
			$filename=microtime();
			$filename=str_replace(".","",$filename);
			$filename=str_replace(" ","",$filename);
			$filename="temp/pmb_".$filename.".zip";
			$res = $zip->open($filename, ZipArchive::CREATE);
			if ($res) {
				foreach ($explnum_list as $explnum_id) {
					$explnum = new explnum($explnum_id);
					$zip->addFromString(reg_diacrit($explnum->get_file_name()),$explnum->get_file_content());
				}			
				$zip->close();
				
				header("Content-disposition: attachment; filename=\"".basename($filename)."\"");
				header("Content-Type: application/force-download");
				header("Content-Transfer-Encoding: application/zip");
				header("Content-Length: ".filesize($filename));
				header("Pragma: no-cache");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
				header("Expires: 0");
				
				$fp = fopen($filename, 'rb');
				fpassthru($fp);
				fclose($fp) ;
				
				@unlink($filename);
			}
		}
	} else {
		print "<script>
					alert('".$msg["download_docnum_no_doc"]."');
					window.close();
				</script>";
	}
}
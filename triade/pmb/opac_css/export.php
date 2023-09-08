<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.php,v 1.26 2018-02-13 15:08:42 dgoron Exp $

$base_path=".";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once('./includes/templates/common.tpl.php');
require_once('./includes/notice_categories.inc.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");

require_once('./classes/notice_affichage.class.php');
require_once('./classes/notice_affichage.ext.class.php');

require_once($include_path."/mail.inc.php") ;

// pour export panier
require_once("$base_path/admin/convert/start_export.class.php");

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');


if(isset($select_item) && $select_item){
	$cart_ = explode(",",$select_item);
} else $cart_=$_SESSION["cart"];

if (($opac_export_allow=='1') || (($opac_export_allow=='2') && ($_SESSION["user_code"]))) {
	switch ($opac_export_allow_expl) {
		case '1' :
			$keep_expl = 1 ;
			$keep_explnum = 0 ;
			break;
		case '2' :
			$keep_expl = 0 ;
			$keep_explnum = 1 ;
			break;
		case '3' :
			$keep_expl = 1 ;
			$keep_explnum = 1 ;
			break;
		case '0' :
		default :
			$keep_expl = 0 ;
			$keep_explnum = 0 ;
			break;
	}
	if ($action=="export") {
		$exportation="";
		$nb_fiche=0;
		$nb_fiche_total=count($cart_);
		$n_notices=$nb_fiche_total;
		$_SESSION["param_export"]["notice_exporte"]=array();
		for ($z=0; $z<$nb_fiche_total; $z++) {
			$id_externe = 0;
			$is_externe = false; 					
			if (substr($cart_[$z],0,2)!="es"){
				// Exclure de l'export (opac, panier) les fiches interdites de diffusion dans administration, Notices > Origines des notices NG72
				$sql="select 1 from origine_notice,notices where notice_id = '$cart_[$z]' and origine_catalogage = orinot_id and orinot_diffusion='1'";	 
			} else {
				$id_externe = substr($cart_[$z],2);
				$is_externe = true;
				$requete = "SELECT source_id FROM external_count WHERE rid=".$id_externe;
				$myQuery = pmb_mysql_query($requete, $dbh);
				$source_id = pmb_mysql_result($myQuery, 0, 0);				
				$sql="select 1 from entrepot_source_$source_id where recid='".$id_externe."' group by ufield,usubfield,field_order,subfield_order,value";
			}		
			$res=pmb_mysql_query($sql,$dbh);
			if ($ligne=pmb_mysql_fetch_array($res)) {
				$nb_fiche++;
				$export= new start_export(($id_externe ? $id_externe : $cart_[$z]),$typeexport,$is_externe,$keep_expl,$keep_explnum) ;
				$exportation.=$export->output_notice;			
			}	
		}
		if ($nb_fiche>0) {
			$exportation=$export->get_header().$exportation.$export->get_footer();
			header("Content-type: ".$export->get_mime_type());
			header('Content-Disposition: attachment; filename="export.'.$export->get_suffix().'"');
		
			print $exportation;
			
			global $pmb_logs_activate;
			if($pmb_logs_activate){
				global $log, $infos_notice, $infos_expl;
				
				if($_SESSION['user_code']) {
					$res=pmb_mysql_query($log->get_empr_query());
					if($res){
						$empr_carac = pmb_mysql_fetch_array($res);
						$log->add_log('empr',$empr_carac);
					}
				}
				$log->add_log('num_session',session_id());
				$log->add_log('expl',$infos_expl);
				$log->add_log('docs',$infos_notice);
				$log->save();
			}
			
		} else {
				print "<script>alert(\"".$msg['export_aucune_notice']."\"); history.go(-1);</script>";
		}		
	}
}
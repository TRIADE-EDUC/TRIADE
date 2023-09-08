<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio_a2z.inc.php,v 1.10 2019-05-29 12:53:20 ngantier Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
                    
require_once($base_path."/classes/perio_a2z.class.php");

switch($sub){
	case 'get_onglet':
		$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
		ajax_http_send_response( $a2z->get_onglet($onglet_sel) );
	break;
	case 'get_perio':	
		$a2z=new perio_a2z($id,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);	
		if($pmb_logs_activate){
			//Enregistrement du log
			global $log;
				
			if($_SESSION['user_code']) {
				$res=pmb_mysql_query($log->get_empr_query());
				if($res){
					$empr_carac = pmb_mysql_fetch_array($res);
					$log->add_log('empr',$empr_carac);
				}
			}
		
			$log->add_log('num_session',session_id());
			
			$rqt="select notice_id, typdoc, niveau_biblio, index_l, libelle_categorie, name_pclass, indexint_name
				from notices n
				left join notices_categories nc on nc.notcateg_notice=n.notice_id
				left join categories c on nc.num_noeud=c.num_noeud
				left join indexint i on n.indexint=i.indexint_id
				left join pclassement pc on i.num_pclass=pc.id_pclass
				where notice_id='".$id."'";
			$res_noti = pmb_mysql_query($rqt);
			if(($noti=pmb_mysql_fetch_array($res_noti))){
			    $log->add_log('docs',$noti);
			} else {
			    // n'existe pas, ou notice externe
			    $log->add_log('docs',$id);
			}
		
			//Enregistrement vue
			if($opac_opac_view_activate){
				$log->add_log('opac_view', $_SESSION["opac_view"]);
			}
		
			$log->save();
		}
		ajax_http_send_response($a2z->get_perio($id) );
	break;
	case 'reload':	
		$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);	
		ajax_http_send_response( $a2z->get_form(0,0,1) );
	break;
}

?>
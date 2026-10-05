<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_cart.inc.php,v 1.19 2024/03/22 15:31:02 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $include_path, $msg;
global $opac_resa, $opac_resa_planning, $pmb_location_reservation, $opac_resa_popup;
global $pmb_transferts_actif, $transferts_choix_lieu_opac;

require_once($base_path.'/includes/resa_func.inc.php');
require_once($include_path.'/mail.inc.php');
require_once($include_path.'/templates/resa_planning.tpl.php') ;
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/resa.class.php');
require_once($base_path.'/classes/resa_planning.class.php');
require_once($base_path.'/classes/event/events/event_resa_multiple.class.php');
require_once($base_path.'/classes/event/events_handler.class.php');

if($opac_resa && $_SESSION['user_code']) {

	$resa_cart_display='';
	$notices=array();
	$bulletins=array();
	$from_cart=0;
	
	//Récupération des notices
	switch($sub){
		case 'resa_cart' :
		case 'resa_planning_cart' :
			$notices = $_SESSION['cart'];
			$from_cart = 1;
			break;
		case 'resa_cart_checked':
		case 'resa_planning_cart_checked':
			if(isset($notice)) {
				$notices = $notice;
			} else if(isset($resa_notices)) {
				$notices = $resa_notices;
			}
			$from_cart = 1;
			break;
		default:
			print '<script>document.location="./index.php"</script>';
			break;
	}
	$id_empr=$_SESSION['id_empr_session'];

	//resas classiques
	if($opac_resa_planning!=1){

		if ($pmb_transferts_actif=='1' && $transferts_choix_lieu_opac=='1' ) {
		    //ON ENVOIT QUELQUES EVENTS...
		    $evth = events_handler::get_instance();
		    $evt = new event_resa_mutiple("resa_multiple", "show_form");
		    $evt->set_notices($notices);
		    $evth->send($evt);
		    if($evt->get_form() != ''){
		        print $evt->get_form();
		    }else{
    			if($idloc==''){
    				//les transferts sont actifs, avec un choix du lieu de retrait et pas de choix encore fait
    				//=> on affiche les localisations
    				if($pmb_location_reservation) {
    					$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1  and idlocation in (select resa_loc from resa_loc where resa_emprloc=$empr_location) ORDER BY location_libelle ";
    					$req_loc_list = "SELECT expl_location FROM exemplaires, docs_statut WHERE expl_notice IN (".implode(",",$notices).") and  expl_statut=idstatut
    						and transfert_flag=1 and statut_allow_resa=1
    						AND expl_bulletin='0' and expl_location in (select resa_loc from resa_loc where resa_emprloc=$empr_location)";
    				} else {
    					$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1 ORDER BY location_libelle";
    					$req_loc_list = "SELECT expl_location FROM exemplaires, docs_statut WHERE expl_notice IN (".implode(",",$notices).") and  expl_statut=idstatut
    						and transfert_flag=1 and statut_allow_resa=1 AND expl_bulletin='0' ";
    				}
    
    				$loc_list=array();
    				$flag_transferable=0;
    				$res_loc_list = pmb_mysql_query($req_loc_list);
    				if(pmb_mysql_num_rows($res_loc_list)){
    					while ($r = pmb_mysql_fetch_object($res_loc_list)){
    						$loc_list[]=$r->expl_location;
    						// au moins un expl transférable
    						$flag_transferable=1;
    					}
    				}
    				$res = pmb_mysql_query($loc_req);
    				//on parcours la liste des localisations
    				$optionsHtml = '';
    				while ($value = pmb_mysql_fetch_array($res)) {
    				    if(!$flag_transferable){
    				        // il y en a un ici?
    				        $req= "select expl_id from exemplaires, docs_statut where expl_notice IN (".implode(",",$notices).") AND expl_bulletin='0' and expl_location = " . $value[0] . "
    							and expl_statut=idstatut and statut_allow_resa=1 ";
    				        $res_expl = pmb_mysql_query($req);
    				        if(!pmb_mysql_num_rows($res_expl)){
    				            continue;
    				        }
    				    }
    				    if($value[0]==$empr_location) {
    				        $selected=' selected="selected" ';
    				    } else {
    				        $selected='';
    				    }
    				    $optionsHtml .= "<option value='" . $value[0] . "' $selected >" . translation::get_translated_text($value[0], "docs_location", "location_libelle", $value[1]) . "</option>";
    				}
    				if($optionsHtml) {
    				    $tmpHtml = '<form method="post" action="do_resa.php?lvl='.$lvl.'&sub='.$sub.'">';
    				    $tmpHtml .= $msg['reservation_selection_localisation'].'<br />';
    				    $tmpHtml .= '<select name="idloc">';
    				    $tmpHtml .= $optionsHtml;
    				    $tmpHtml .= "</select><input type='hidden' name='listeNotices' value='".implode(",",$notices)."'><br /><br /><input class='bouton' type='submit' value='" . $msg['reservation_bt_choisir_localisation'] . "'></form>";
    				    echo $tmpHtml;
    				} else {
    				    $resa_cart_display = '<table><tr><th colspan="2">'.$msg['empr_menu_resa'].' : </th></tr>';
    				    foreach($notices as $notice_id){
    				        $resa_cart_display.= '<tr>';
    				        $resa = reservation::get_instance_from_empr_and_notice($id_empr, $notice_id);
    				        $resa_cart_display.= '<td>'.$resa->notice.'</td><td><strong>'.$msg['resa_no_expl'].'</strong></td>';
    				        $resa_cart_display.= '</tr>';
    				    }
    				    $resa_cart_display.='</table>';
    				    if(!$opac_resa_popup){
    				        require_once $base_path.'/includes/show_cart.inc.php';
    				    }
    				    print '<br/><br/>'.$resa_cart_display;
    				}
    			}else{
    			    $notices=explode(',',$listeNotices);
    				$resa_cart_display = '<table><tr><th colspan="2">'.$msg['empr_menu_resa'].' : </th></tr>';
    				foreach($notices as $notice_id){
    					$resa_cart_display.= '<tr>';
    					$resa = reservation::get_instance_from_empr_and_notice($id_empr, $notice_id);
    					
    					$event = new event_resa_mutiple('resa_multiple','before_validate');
    					$event->set_empr_id($_SESSION["id_empr_session"]);
    					$event->set_notice($resa->id_notice);
    					$event->set_bulletin($resa->id_bulletin);
    					$evth->send($event);
    					if($event->get_id_loc() !== false && $event->get_id_loc()){
    					    $idloc = $event->get_id_loc();
    					}
    					if($resa->add($idloc)){
    						$resa_cart_display.= '<td>'.$resa->notice.'</td><td>'.$resa->message.'</td>';
    						reservation::alert_mail_users_pmb($resa->id_notice, $resa->id_bulletin, $_SESSION["id_empr_session"]);
    						//On retire la notice du panier ?
    						delete_cart_record($notice_id);
    					}else{
    						$resa_cart_display.= '<td>'.$resa->notice.'</td><td>'.$resa->message.'</td>';
    					}
    					$event = new event_resa_mutiple('resa_multiple','validate_resa');
    					$event->set_empr_id($_SESSION["id_empr_session"]);
    					$event->set_notice($resa->id_notice);
    					$event->set_bulletin($resa->id_bulletin);
    					$event->set_resa_id($resa->id);
    					$evth->send($event);    					
    					$resa_cart_display.= '</tr>';
    				}
    				$resa_cart_display.='</table>';
    				$event = new event_resa_mutiple('resa_multiple','finished');
    				$event->set_empr_id($_SESSION["id_empr_session"]);
    				$evth->send($event);    				
    				if(!$opac_resa_popup){
    					require_once $base_path.'/includes/show_cart.inc.php';
    				}
    				print '<br/><br/>'.$resa_cart_display;
    			}

		    }
		} else {

			$resa_cart_display='<table><tr><th colspan="2">'.$msg['empr_menu_resa'].' : </th></tr>';
			foreach($notices as $notice_id){
				$resa_cart_display.='<tr>';
				$resa = reservation::get_instance_from_empr_and_notice($id_empr, $notice_id);
				if($resa->add($_SESSION['empr_location'])){
					$resa_cart_display.= '<td>'.$resa->notice.'</td><td>'.$resa->message.'</td>';
					reservation::alert_mail_users_pmb($resa->id_notice, $resa->id_bulletin, $_SESSION["id_empr_session"]);
					//On retire la notice du panier ?
					delete_cart_record($notice_id);
				}else{
					$resa_cart_display.= '<td>'.$resa->notice.'</td><td>'.$resa->message.'</td>';
				}
				$resa_cart_display.= '</tr>';
			}
			$resa_cart_display.= '</table>';

			if(!$opac_resa_popup){
				require_once $base_path.'/includes/show_cart.inc.php';
			}
			print '<br/><br/>'.$resa_cart_display;

		}

	//resas planifiees
	} else {

		//recuperation des informations de resa
		$t_resa = array();
		if (is_array($notices) && count($notices)) {

			foreach($notices as $k=>$id_notice){
				$id_notice = intval($id_notice);
				$id_bulletin = 0;
				//On regarde le type de la notice.
				if(($id_notice)) {
					$qn= 'select niveau_biblio from notices where notice_id='.$id_notice;
					$rn = pmb_mysql_query($qn);
					if(pmb_mysql_num_rows($rn)){
						$on = pmb_mysql_fetch_object($rn);
		
						switch ($on->niveau_biblio) {
							//monographie
							case 'm' :
								$t_resa[$k]['id_notice'] = $id_notice;
								$t_resa[$k]['id_bulletin'] = 0;
								$t_resa[$k]['save'] = array($id_notice,0);
								break;
							//bulletin
							case 'b' :
								$qb = 'select bulletin_id from bulletins where num_notice='.$id_notice;
								$rb = pmb_mysql_query($qb);
								if(pmb_mysql_num_rows($rb)){
									$id_bulletin = pmb_mysql_result($rb,0,0);									
									$t_resa[$k]['id_notice'] = $id_notice;
									$t_resa[$k]['id_bulletin'] = $id_bulletin;
									$t_resa[$k]['save'] = array(0,$id_bulletin);
								}
								break;
							//article
							case 'a' :
								$qa = 'select analysis_bulletin from analysis where analysis.analysis_notice='.$id_notice;
								$ra = pmb_mysql_query($qa);
								if(pmb_mysql_num_rows($ra)){
									$id_bulletin = pmb_mysql_result($ra,0,0);
									$oa = pmb_mysql_fetch_object($ra);
									$t_resa[$k]['id_notice'] = $id_notice;
									$t_resa[$k]['id_bulletin'] = $id_bulletin;
									$t_resa[$k]['save'] = array(0,$id_bulletin);
								}
								break;
							default :
								break;
						}
					}
				}
			}
		}
	
		
		//Rien n'est precise, 1ere etape : affichage du formulaire de reservation uniquement
		if(!$step) {
			$step=1;
		}
		
		//2eme etape >> enregistrement des reservations
		if($step==2 && count($t_resa) && is_array($resa_deb) && is_array($resa_fin) && is_array($resa_qte)) {

			foreach($t_resa as $k=>$v) {

				$ck_date_debut = preg_replace("#[^0-9]#",'', $resa_deb[$v['id_notice']]);
				$ck_date_fin = preg_replace("#[^0-9]#",'', $resa_fin[$v['id_notice']]);
				
				if( (strlen($ck_date_debut)==8) &&  (strlen($ck_date_fin)==8) && ($ck_date_debut >= $date_jour) && ($ck_date_debut < $ck_date_fin) ) {
				
					foreach($resa_qte[$v['id_notice']] as $loc=>$qte) {
						if($qte) {
							$r = new resa_planning();
							$r->resa_idempr=$id_empr;
							$r->resa_idnotice=$v['save'][0];
							$r->resa_idbulletin=$v['save'][1];
							$r->resa_date_debut=$resa_deb[$v['id_notice']];
							$r->resa_date_fin=$resa_fin[$v['id_notice']];
							$r->resa_qty = $qte;
							$r->resa_remaining_qty = $qte;
							$r->resa_loc_retrait = $loc;
							$r->save();
							//On retire la notice du panier ?
							delete_cart_record($v['save'][0]);
						}
					}
				}
				
			}
		}
		
		
		//Affichage du formulaire de reservation
		$form = '';
		if (count($t_resa)) {
			
			$form = $form_resa_planning_add_from_cart;
				
			foreach($t_resa as $k=>$v) {
					
				$form = str_replace('<!-- items -->',$form_resa_planning_add_from_cart_item.'<!-- items -->',$form);
					
				//recherche des localisations ou la reservation peut se faire
				$tab_loc_retrait = resa_planning::get_available_locations($id_empr,(($v['id_bulletin'])?0:$v['id_notice']),$v['id_bulletin']);
									
				if(count($tab_loc_retrait)>=1) {
						
					$form_table = $form_resa_planning_add_from_cart_loc_retrait_table;
					$form_rows='';
					
					foreach($tab_loc_retrait as $kloc=>$vloc) {
					
						$form_rows.= $form_resa_planning_add_from_cart_loc_retrait_row;
						
						$form_rows = str_replace('!!location_label!!', htmlentities($vloc['location_libelle'],ENT_QUOTES,$charset),$form_rows);	
						$form_rows = str_replace('!!id_location!!', $vloc['location_id'],$form_rows);
						$form_row_options=''; 
						for($i=0;$i<=$vloc['location_nb'];$i++) {
							$form_row_options.= $form_resa_planning_add_from_cart_loc_retrait_option;
							$form_row_options = str_replace('!!val!!',$i,$form_row_options);
						}
						$form_rows = str_replace('<!-- options -->' , $form_row_options, $form_rows);
					}
					$form_table = str_replace('<!-- rows -->' , $form_rows, $form_table);
					$form = str_replace('!!resa_loc_retrait!!',$form_table,$form);
					$form = str_replace('!!id_notice!!',$v['id_notice'],$form);
					$form = str_replace('!!id_bulletin!!',$v['id_bulletin'],$form);
							
				} else {
					$form = str_replace('!!resa_loc_retrait!!',$form_resa_planning_add_from_cart_loc_retrait_none,$form);
				}

				
				//Affichage notice 
				$opac_notices_depliable = 1 ;
				$liens_opac = array() ;
				$ouvrage_resa = aff_notice($v['id_notice'], 1) ;
					
				$form = str_replace('<!-- items -->',$ouvrage_resa.'<!-- items -->',$form) ;
				
				
				//Affichage des previsions sur le document courant par le lecteur courant
				$q3 = 'SELECT id_resa, resa_idnotice, resa_idbulletin, resa_date_debut, resa_date_fin, ';
				$q3.= 'if(resa_date_fin < sysdate() or resa_date_fin="0000-00-00",1,0) as resa_perimee, resa_validee, resa_confirmee, ';
				$q3.= 'resa_qty, resa_loc_retrait, location_libelle ';
				$q3.= 'FROM resa_planning left join docs_location on resa_loc_retrait=idlocation ';
				$q3.= 'WHERE id_resa not in (select resa_planning_id_resa from resa where resa_idempr='.$id_empr.') ';
				$q3.= 'and resa_idempr='.$id_empr.' and resa_idnotice='.$v['save'][0].' and resa_idbulletin='.$v['save'][1].' ';
				$q3.= 'ORDER by resa_date_debut asc, resa_date_fin asc';
				$r3 = pmb_mysql_query($q3);
				
				if(pmb_mysql_num_rows($r3)) {
					$tableau_resa = '<div class="resa_planning_current" ><h3>'.$msg['resa_planning_current'].'</h3>';
					$tableau_resa.= '<table class="tab_resa_planning">';
					$tableau_resa.='<tr><th>'.$msg['resa_planning_dates'].'</th>
							<th>'.$msg['resa_planning_qty'].'</th><th>'.$msg['resa_planning_loc_retrait'].'</th></tr>';
				
					while ($resa = pmb_mysql_fetch_array($r3)) {
						$id_resa = $resa['id_resa'];
						$resa_idnotice = $resa['resa_idnotice'];
						$resa_idbulletin = $resa['resa_idbulletin'];
						$resa_date_debut = formatdate($resa['resa_date_debut']);
						$resa_date_fin = formatdate($resa['resa_date_fin']);
						$resa_qty =$resa['resa_qty'];
						$resa_loc_retrait = translation::get_translated_text($resa['resa_loc_retrait'], "docs_location", "location_libelle", $resa['location_libelle']);
						$txt_dates = $msg['resa_planning_date_debut'].$resa_date_debut.'<br />';
						$txt_dates.= $msg['resa_planning_date_fin'].$resa_date_fin.'<br />';
						if ($resa['resa_perimee']) {
							$txt_dates.= $msg['resa_planning_overtime'];
						} else {
							$txt_dates.= $msg['resa_planning_attente_validation'] ;
						}
				
						if ($parity++ % 2) {
							$pair_impair = 'even';
						} else {
							$pair_impair = 'odd';
						}
						$tableau_resa.= '<tr class="'.$pair_impair.'">
							<td>'.$txt_dates.'</td><td>'.$resa_qty.'</td>
							<td>'.htmlentities($resa_loc_retrait,ENT_QUOTES,$charset).'</td>
							</tr>';
					}
					$tableau_resa.='</table></div>';
				}
				
				$form = str_replace('<!-- items -->',$tableau_resa.'<!-- items -->',$form) ;
							
			}
			
		}
		$resa_cart_display = $form;
		print '<br/><br/>'.$resa_cart_display;

	}

} else {
	print '<script>document.location="./index.php";</script>';
}

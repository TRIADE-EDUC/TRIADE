<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.16 2017-11-07 15:17:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/resa_planning.class.php");
require_once("$include_path/resa_planning_func.inc.php");
require_once("$include_path/templates/resa_planning.tpl.php");
require_once("$class_path/mono_display.class.php");
require_once("$class_path/serials.class.php");

// gestion des liens en rech resa ou pas
$link = "./circ.php?categ=resa_planning&id_empr=$id_empr&groupID=$groupID&id_notice=!!id!!";
$link_serial = "./circ.php?categ=resa_planning&resa_action=search_resa&id_empr=$id_empr&groupID=$groupID&mode=view_serial&serial_id=!!id!!";
$link_analysis = '';
$link_bulletin = "./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=!!id!!";

switch ($categ) {

	case 'resa_planning' :
		print "<h1>".$msg['resa_menu']." &gt; ".$msg['resa_menu_planning']."</h1>";

		switch($resa_action) {

			case 'search_resa' : //Recherche pour prévision

				if (!aff_entete($id_empr,$layout_begin)) {
					error_message($msg[350], $msg[54], 1 , './circ.php');
					break;
				}
				print $layout_begin;
				
				switch($mode) {
					case 1:
						// recherche catégorie/sujet
						print $menu_search[1];
						include('./circ/resa_planning/subjects/main.inc.php');
						break;
					case 5:
						// recherche par termes
						print $menu_search[6];
						include('./circ/resa_planning/terms/main.inc.php');
						break;
					case 2:
						// recherche éditeur/collection
						print $menu_search[2];
						include('./circ/resa_planning/publishers/main.inc.php');
						break;
					case 3:
						// accès aux paniers
						print $menu_search[3];
						include('./circ/resa_planning/cart.inc.php');
						break;
					case 'view_serial':
						// affichage de la liste des éléments bulletinés pour un périodique
						include('./circ/resa_planning/view_serial.inc.php');
						break;
					case 6:
						// recherches avancees
						print $menu_search[6];
						include('./circ/resa_planning/extended/main.inc.php');
						break;
					default :
						// recherche auteur/titre
						print $menu_search[0];
						$action_form = "./circ.php?categ=resa_planning&mode=0&id_empr=$id_empr&groupID=$groupID" ;
						include('./circ/resa_planning/authors/main.inc.php');
						break;
				}
				break;

			case 'add_resa' : //Ajout d'une prévision depuis une recherche catalogue

				if (!aff_entete($id_empr,$layout_begin)) {
					error_message($msg[350], $msg[54], 1 , './circ.php');
					break;
				}
				print $layout_begin;

				if(!check_record($id_notice,$id_bulletin)) {
					error_message($msg[350], $msg['resa_unknown_record'], 1 , './circ.php?');
					break;
				}
				
				if($id_notice) {
					$display = new mono_display($id_notice, 6, '', 0, '', '', '', 0, 1, 1, 1);
					print ($display->result);
				} else if ($id_bulletin) {
					$bull = new bulletinage($id_bulletin);
					$bull->make_display();
					print $bull->display;
				}
				print "<script type='text/javascript' src='./javascript/tablist.js'></script>\n";

				$form_resa_dates = str_replace('!!resa_date_debut!!', formatdate(today()), $form_resa_dates);
				$form_resa_dates = str_replace('!!resa_date_fin!!', formatdate(today()), $form_resa_dates);
				$form_resa_dates = str_replace('!!resa_deb!!', today(), $form_resa_dates);
				$form_resa_dates = str_replace('!!resa_fin!!', today(), $form_resa_dates);

				$tab_loc_retrait = resa_planning::get_available_locations($id_empr,$id_notice,$id_bulletin);

				if(count($tab_loc_retrait)>=1) {
					$form_loc_retrait = '<table ><tbody><tr><th>'.$msg['resa_planning_loc_retrait'].'</th><th>'.$msg['resa_planning_qty_requested'].'</th></tr>';
					foreach($tab_loc_retrait as $k=>$v) {
						$form_loc_retrait.= '<tr><td style="width:50%">'.htmlentities($v['location_libelle'],ENT_QUOTES,$charset).'</td>';
						$form_loc_retrait.= '<td><select name="location['.$v['location_id'].']">';
						for($i=1;$i<$v['location_nb']*1+1;$i++) {
							$form_loc_retrait.= '<option value='.$i.'>'.$i.'</option>';
						}
						$form_loc_retrait.= '</select></td>';
						$form_loc_retrait.='</tr>';
					}
					$form_loc_retrait.= '</tbody></table>';
				} else {
						error_message($msg[350], $msg['resa_planning_no_item_available'], 1 , "./circ.php?categ=resa_planning&resa_action=search_resa&mode=0&id_empr=$id_empr&groupID=$groupID");
						break;
				}
				$form_resa_dates = str_replace ('!!resa_qty!!', sprintf($msg['resa_planning_qty_requested'],$nb_items),$form_resa_dates);
				$form_resa_dates = str_replace ('!!resa_loc_retrait!!', $form_loc_retrait,$form_resa_dates);
				print $form_resa_dates;

				//Affichage des previsions sur le document courant par le lecteur courant
				print doc_planning_list($id_empr, $id_notice, $id_bulletin);
				break;

			case 'add_resa_suite' :	//Enregistrement prévision depuis fiche

				$empr_cb=0;
				if(!aff_entete($id_empr,$layout_begin,$empr_cb)) {
					error_message($msg[350], $msg[54], 1 );
					break;
				}
				if(!check_record($id_notice,$id_bulletin)) {
					error_message($msg[350], $msg['resa_planning_unknown_record'], 1 );
					break;
				}
				$check_qty=0;
				if(isset($location) && is_array($location)) {
					foreach($location as $k=>$v) {
						$check_qty+= $v*1;
					}
				}
				if($check_qty==0) {
					error_message($msg[350], $msg['resa_planning_alert_qty'], 1 );
					break;
				}
				
				//On vérifie les dates
				$query="SELECT DATEDIFF('$resa_fin', '$resa_deb') AS diff";

				$resultatdate=pmb_mysql_query($query);
						if( pmb_mysql_num_rows($resultatdate) ) {
					$resdate=pmb_mysql_fetch_object($resultatdate);
					if($resdate->diff > 0 ) {
						foreach($location as $resa_loc_retrait=>$resa_qty) {
							$resa_qty+=0;
							if($resa_qty) {
								$r = new resa_planning();
								$r->resa_idempr = $id_empr;
								$r->resa_idnotice = $id_notice;
								$r->resa_idbulletin = $id_bulletin;
								$r->resa_date_debut = $resa_deb;
								$r->resa_date_fin = $resa_fin;
								$r->resa_qty = $resa_qty;
								$r->resa_remaining_qty = $resa_qty;
								$r->resa_loc_retrait = $resa_loc_retrait;
								$r->save();
							}
						}
						print "<script type='text/javascript'>document.location='./circ.php?categ=pret&form_cb=".rawurlencode($empr_cb)."'</script>";

					} else {
						error_message($msg[350], $msg['resa_planning_alert_date'], 1 );
						break;
					}
				}
								break;


			case 'val_resa':	//Validation réservation depuis liste

				for($i=0;$i<count($resa_check);$i++) {

					$key = $resa_check[$i];
					//On vérifie les dates
					$tresa_date_debut = explode('-', extraitdate($resa_date_debut[$key]));
					if (strlen($tresa_date_debut[2])==1) $tresa_date_debut[2] = '0'.$tresa_date_debut[2];
					if (strlen($tresa_date_debut[1])==1) $tresa_date_debut[1] = '0'.$tresa_date_debut[1];
					$r_date_debut = implode('', $tresa_date_debut);

					$tresa_date_fin = explode('-', extraitdate($resa_date_fin[$key]));
					if (strlen($tresa_date_fin[2])==1) $tresa_date_fin[2] = '0'.$tresa_date_fin[2];
					if (strlen($tresa_date_fin[1])==1) $tresa_date_fin[1] = '0'.$tresa_date_fin[1];
					$r_date_fin = implode('', $tresa_date_fin);

					if ( (checkdate($tresa_date_debut[1], $tresa_date_debut[2], $tresa_date_debut[0]))
							&& (checkdate($tresa_date_fin[1], $tresa_date_fin[2], $tresa_date_fin[0]))
							&& (strlen($r_date_debut)==8) && (strlen($r_date_fin)==8)
							&& ($r_date_debut < $r_date_fin) ) {
						$r = new resa_planning($key);
						$r->resa_date_debut=implode('-', $tresa_date_debut);
						$r->resa_date_fin=implode('-', $tresa_date_fin);
						$r->resa_validee='1';
						$r->save();

					}
				}
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;


			case 'raz_val_resa':

				for($i=0;$i<count($resa_check);$i++) {
					$key = $resa_check[$i];
					$rqt_maj = 'update resa_planning set resa_validee=0 where id_resa in ('.$resa_check[$i].')' ;
					if ($id_empr[$resa_check[$i]]) $rqt_maj .= ' and resa_idempr='.$id_empr[$resa_check[$i]];
					pmb_mysql_query($rqt_maj, $dbh);
				}
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;

			case 'suppr_resa':	//Suppression réservation depuis liste

				for($i=0;$i<count($resa_check);$i++) {
					$key = $resa_check[$i];
					resa_planning::delete($key);
				}
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;


			case 'conf_resa':
				if(!$resa_check) {
					$resa_check=array();
				}
				if(count($resa_check)) {
					alert_empr_resa_planning ($resa_check);
				}
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;


			case 'raz_conf_resa':

				for($i=0;$i<count($resa_check);$i++) {
					$key = $resa_check[$i];
					$rqt_maj = 'update resa_planning set resa_confirmee=0 where id_resa in ('.$resa_check[$i].')' ;
					if ($id_empr[$resa_check[$i]]) {
						$rqt_maj .= ' and resa_idempr='.$id_empr[$resa_check[$i]];
					}
					pmb_mysql_query($rqt_maj, $dbh);
				}
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;


			case 'to_resa' :
				if(count($resa_check)) {
					foreach($resa_check as $k=>$id_rp) {
						$rp=new resa_planning($id_rp);
						$rp->to_resa();
					}
				}
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;


			default :
				print planning_list(0, 0, 0, '', '', GESTION_INFO_GESTION) ;
				break;
			}
			break;

			case 'pret' :

		switch ($action) {

			case 'suppr_resa' :	//Suppression réservation depuis fiche lecteur
				resa_planning::delete($id_resa);
				break;

			default :
				break;
		}
		break;

	default :
		break;
}


?>
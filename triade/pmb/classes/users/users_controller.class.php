<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users_controller.class.php,v 1.8 2023/12/21 11:29:49 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/user.class.php");
require_once($class_path."/event/events/event_user.class.php");
require_once($class_path."/mails/mails_configuration.class.php");
require_once('./admin/users/users_func.inc.php');

use Pmb\Common\Helper\MySQL;

class users_controller extends lists_controller {
	
	protected static $model_class_name = 'user';
	
	protected static $list_ui_class_name = 'list_users_ui';
	
	public static function proceed($id=0) {
		global $action, $msg, $synchro_step;
		global $database_window_title, $form_pwd;
		
		$id = intval($id);
		switch($action) {
			case 'pwd':
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "before_pwd");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				static::proceed_pwd($id);
				
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "after_pwd");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				if(!empty($form_pwd)) {
					static::redirect_display_list();
				}
				break;
			case 'modif':
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "before_modif");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				static::proceed_modif($id);
				break;
			case 'update':
		        $url = self::get_url_base()."&action=add";
			    if($id != 0) {
			        $url = self::get_url_base()."&action=modif&id=".$id;
			    }
			    verify_csrf($url);
			    
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "before_update");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				static::proceed_update($id);
				
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "after_update");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				static::set_object_id($id);
				static::redirect_display_list();
				break;
			case 'add':
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "before_add");
				$event->set_user_id($id);
				if( isset($synchro_step) ) {
					$event->set_synchro_step($synchro_step);
				}
				$evt_handler->send($event);
				
				echo window_title($database_window_title.$msg[347].$msg[1003].$msg[1001]);
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_user_form();
				echo form_focus('userform', 'form_login');
				break;
			case 'del':
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "before_del");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				if($id && $id !=1) {
					user::delete($id, $_COOKIE["PhpMyBibli-LOGIN"]);
				}
				
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "after_del");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				static::redirect_display_list();
				break;
			case 'duplicate':
				//Evenement publié
				$evt_handler = events_handler::get_instance();
				$event = new event_user("user", "before_duplicate");
				$event->set_user_id($id);
				$evt_handler->send($event);
				
				static::proceed_duplicate($id);
				break;
			default:
				parent::proceed($id);
				break;
		}
	}
	
	public static function proceed_pwd($id=0) {
		global $database_window_title, $msg;
		global $form_pwd, $form_pwd2, $admin_npass_form;
		global $pmb_url_base;
		
		$requete = "SELECT username FROM users WHERE userid='$id' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$row = $row=pmb_mysql_fetch_row($res);
		$myUser = $row[0];
		
		if(empty($form_pwd)) {
			echo window_title($database_window_title.$msg[2]." $myUser".$msg[1003].$msg[1001]);
			$admin_npass_form = str_replace('!!id!!', $id, $admin_npass_form);
			$admin_npass_form = str_replace('!!myUser!!', $myUser, $admin_npass_form);
			print $admin_npass_form;
			echo form_focus('userform', 'form_pwd');
		} else {
			if($form_pwd==$form_pwd2 && !empty($form_pwd)) {
				$requete = "UPDATE users SET last_updated_dt=curdate(),pwd='".MySQL::password($form_pwd)."', user_digest = '".md5($myUser.":".md5($pmb_url_base).":".$form_pwd)."' WHERE userid=$id ";
				$res = pmb_mysql_query($requete);
			}
		}
	}
	
	public static function proceed_modif($id=0) {
		global $msg;
		
		$id = intval($id);
		$requete = "SELECT username FROM users WHERE userid='$id' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			$usr=pmb_mysql_fetch_object($res);
			
			$param_default = user::get_form($id, 'userform');
			
			echo window_title($msg[1003].$msg[18].$msg[1003].$msg[86].$msg[1003].$usr->username.$msg[1001]);
			
			$model_instance = static::get_model_instance($id);
			print $model_instance->get_user_form($param_default);
			echo form_focus('userform', 'form_nom');
		} else{
			echo sprintf($msg['unknown_user_id'], $id);
		}
	}
	
	public static function proceed_update($id=0) {
		global $msg;
		global $form_admin, $form_catal, $form_circ, $form_auth, $form_edition, $form_edition_forcing;
		global $form_sauv, $form_pref, $form_dsi, $form_acquisition, $form_restrictcirc, $form_thesaurus, $form_transferts;
		global $form_extensions, $form_demandes, $form_fiches, $form_cms, $form_cms_build;
		global $form_catal_modif_cb_expl, $form_acquisition_account_invoice_flg, $form_semantic, $form_concepts;
		global $form_frbr, $form_modelling, $form_animations;
		global $form_actif, $pmb_droits_explr_localises;
		global $form_login, $form_pwd, $form_pwd2, $form_nom, $form_prenom, $user_lang, $form_nb_per_page_search, $form_nb_per_page_select, $form_nb_per_page_gestion, $form_user_email;
		global $form_user_alert_resamail, $pmb_contribution_area_activate, $form_user_alert_contribmail, $demandes_active, $form_user_alert_demandesmail, $form_alert_user_alert_animation_mail;
		global $opac_websubscribe_show, $form_user_alert_subscribemail, $form_user_alert_suggmail, $opac_serialcirc_active, $form_user_alert_serialcircmail;
		global $duplicate_from_userid, $sel_group, $animations_active;
		
		//pour le set_properties_from_form de la classe user
		global $droits; 
		global $form_expl_visibilite;
		
		$droits = 0;
		
		/* le user admin ne peut perdre le droit admin */
		if ($id==1) $form_admin = 1 ;
		/* le user admin ne peut perdre le droit de forçage */
		if ($id==1) $form_edition_forcing = 1 ;
		
		if(!empty($form_admin)) 				$droits = $droits + ADMINISTRATION_AUTH;
		if(!empty($form_catal)) 				$droits = $droits + CATALOGAGE_AUTH;
		if(!empty($form_circ)) 					$droits = $droits + CIRCULATION_AUTH;
		if(!empty($form_auth)) 					$droits = $droits + AUTORITES_AUTH;
		if(!empty($form_edition)) 				$droits = $droits + EDIT_AUTH;
		if(!empty($form_edition_forcing)) 		$droits = $droits + EDIT_FORCING_AUTH;
		if(!empty($form_sauv)) 					$droits = $droits + SAUV_AUTH;
		if(!empty($form_pref))	 				$droits = $droits + PREF_AUTH;
		if(!empty($form_dsi)) 					$droits = $droits + DSI_AUTH;
		if(!empty($form_acquisition))			$droits = $droits + ACQUISITION_AUTH;
		if(!empty($form_restrictcirc))			$droits = $droits + RESTRICTCIRC_AUTH;
		if(!empty($form_thesaurus))				$droits = $droits + THESAURUS_AUTH;
		if(!empty($form_transferts)) 			$droits = $droits + TRANSFERTS_AUTH;
		if(!empty($form_extensions))		 	$droits = $droits + EXTENSIONS_AUTH;
		if(!empty($form_demandes))		 		$droits = $droits + DEMANDES_AUTH;
		if(!empty($form_fiches)) 				$droits = $droits + FICHES_AUTH;
		if(!empty($form_cms)) 					$droits = $droits + CMS_AUTH;
		if(!empty($form_cms_build)) 			$droits = $droits + CMS_BUILD_AUTH;
		if(!empty($form_catal_modif_cb_expl)) 	$droits = $droits + CATAL_MODIF_CB_EXPL_AUTH;
		if(!empty($form_acquisition_account_invoice_flg)) 	$droits = $droits + ACQUISITION_ACCOUNT_INVOICE_AUTH;
		if(!empty($form_semantic)) 				$droits = $droits + SEMANTIC_AUTH;
		if(!empty($form_concepts)) 				$droits = $droits + CONCEPTS_AUTH;
		if(!empty($form_frbr)) 					$droits = $droits + FRBR_AUTH;
		if(!empty($form_modelling)) 			$droits = $droits + MODELLING_AUTH;
		if(!empty($form_animations)) 			$droits = $droits + ANIMATION_AUTH;
		
		// no duplication
		$requete = " SELECT count(1) FROM users WHERE (username='$form_login' AND userid!='$id' )  LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$nbr = pmb_mysql_result($res, 0, 0);
		
		if ($nbr > 0) {
			error_form_message($form_login.$msg["user_login_already_used"]);
		} elseif($form_actif) {
			// visibilité des exemplaires
			if ($pmb_droits_explr_localises) {
				$requete_droits_expl="select idlocation from docs_location order by location_libelle";
				$resultat_droits_expl=pmb_mysql_query($requete_droits_expl);
				$form_expl_visibilite=array();
				$form_expl_visibilitei=array();
				$form_expl_visibilitevm=array();
				$form_expl_visibilitevu=array();
				while ($j=pmb_mysql_fetch_array($resultat_droits_expl)) {
					$temp_global="form_expl_visibilite_".$j["idlocation"];
					global ${$temp_global};
					switch (${$temp_global}) {
						case "explr_invisible":
							$form_expl_visibilitei[] = $j["idlocation"];
							break;
						case "explr_visible_mod":
							$form_expl_visibilitevm[] = $j["idlocation"];
							break;
						case "explr_visible_unmod":
							$form_expl_visibilitevu[] = $j["idlocation"];
							break;
					}
				}
				
				if (!empty($form_expl_visibilitei) && is_array($form_expl_visibilitei)) {
					$form_expl_visibilite[0] = implode(',', $form_expl_visibilitei);
				} else {
					$form_expl_visibilite[0] = "0";
				}
				
				if (!empty($form_expl_visibilitevm) && is_array($form_expl_visibilitevm)) {
					$form_expl_visibilite[1] = implode(',', $form_expl_visibilitevm);
				} else {
					$form_expl_visibilite[1] = "0";
				}
				
				if (!empty($form_expl_visibilitevu) && is_array($form_expl_visibilitevu)) {
					$form_expl_visibilite[2] = implode(',', $form_expl_visibilitevu);
				} else {
					$form_expl_visibilite[2] = "0";
				}
				
				pmb_mysql_free_result($resultat_droits_expl);
			} else {
				$form_expl_visibilite[0]="0";
				$form_expl_visibilite[1]="0";
				$form_expl_visibilite[2]="0";
			} //fin visibilité des exemplaires
			
			// O.K.  if item already exists UPDATE else INSERT
			if(!$id) {
				if(!empty($form_login) && $form_pwd==$form_pwd2) {
					if(!empty($duplicate_from_userid)) {
						$user = new user();
						$user->set_duplicate_from_userid($duplicate_from_userid);
						$user->set_properties_from_form();
						$user->save();
					} else {
						$requete = "INSERT INTO users (userid, deflt_styles, create_dt, last_updated_dt, username, pwd, nom, prenom, rights, user_lang, nb_per_page_search, nb_per_page_select, ";
						$requete.= "nb_per_page_gestion, user_email, user_alert_resamail, user_alert_contribmail, user_alert_demandesmail, user_alert_subscribemail, user_alert_suggmail, user_alert_serialcircmail, explr_invisible, explr_visible_mod, explr_visible_unmod, deflt_notice_replace_keep_categories, user_alert_animation_mail";
						if (isset($sel_group)) {
							$requete.= ", grp_num";
						}
						$requete.= ") VALUES";
						$requete .= "(null,'light',curdate(),curdate()";
						$requete .= ",'$form_login'";
						$requete .= ",'".MySQL::password($form_pwd)."'";
						$requete .= ",'$form_nom'";
						$requete .= ",'$form_prenom'";
						$requete .= ",'$droits'";
						$requete .= ", '$user_lang'";
						$requete .= ", '$form_nb_per_page_search'";
						$requete .= ", '$form_nb_per_page_select'";
						$requete .= ", '$form_nb_per_page_gestion'";
						$requete .= ", '$form_user_email'";
						if (!$form_user_alert_resamail) $form_user_alert_resamail="0" ;
						$requete .= ", '$form_user_alert_resamail'";
						if ((!$pmb_contribution_area_activate) || (!$form_user_alert_contribmail)) $form_user_alert_contribmail="0" ;
						$requete .= ", '$form_user_alert_contribmail'";
						if ((!$demandes_active) || (!$form_user_alert_demandesmail)) $form_user_alert_demandesmail="0" ;
						$requete .= ", '$form_user_alert_demandesmail'";
						if ((!$opac_websubscribe_show) || (!$form_user_alert_subscribemail)) $form_user_alert_subscribemail="0" ;
						$requete .= ", '$form_user_alert_subscribemail'";
						$requete .= ", '$form_user_alert_suggmail'";
						if ((!$opac_serialcirc_active) || (!$form_user_alert_serialcircmail)) $form_user_alert_serialcircmail="0" ;
						$requete .= ", '$form_user_alert_serialcircmail'";
						$requete .= ", '".$form_expl_visibilite[0]."'";
						$requete .= ", '".$form_expl_visibilite[1]."'";
						$requete .= ", '".$form_expl_visibilite[2]."'";
						$requete .= ", '1'";
						if ((!$animations_active) || (!$form_alert_user_alert_animation_mail)) $form_alert_user_alert_animation_mail="0" ;
						$requete .= ", '$form_alert_user_alert_animation_mail'";
						if (isset($sel_group)) {
							$requete.= ", '$sel_group' ";
						}
						$requete.= ") ";
						$res = pmb_mysql_query($requete);
						$id=pmb_mysql_insert_id();
						
						//initialisation de la config SMTP
						mails_configuration::init_domain_from_mail($form_user_email);
						
						if (pmb_mysql_error()) {
							echo pmb_mysql_error();
						} else {
							echo "<script>document.location=\"".static::get_url_base()."&action=modif&id=$id\";</script>";
						}
					}
				}
			} else {
				$requete = "SELECT username,nom,prenom,rights, user_lang, nb_per_page_search, nb_per_page_select, nb_per_page_gestion, explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num  ";
				$requete .= "FROM users WHERE userid='$id' LIMIT 1 ";
				$res = pmb_mysql_query($requete);
				$nbr = pmb_mysql_num_rows($res);
				if($nbr==1) {
					$model_instance = static::get_model_instance($id);
					$model_instance->set_properties_from_form();
					$model_instance->save();
					
					//initialisation de la config SMTP
					mails_configuration::init_domain_from_mail($form_user_email);
				}
			}
		}
	}
	
	public static function proceed_duplicate($id=0) {
		global $database_window_title, $msg;
		
		$id = intval($id);
		$requete = "SELECT username FROM users WHERE userid='$id' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			$param_default = user::get_form($id, 'userform');
			$param_default .= "<input type='hidden' id='duplicate_from_userid' name='duplicate_from_userid' value='".$id."' />";
			
			echo window_title($database_window_title.$msg[347].$msg[1003].$msg[1001]);
			
			$model_instance = static::get_model_instance($id);
			$model_instance->set_duplicate_from_userid($id);
			$model_instance->set_userid(0);
			print $model_instance->get_user_form($param_default);
			echo form_focus('userform', 'form_nom');
		}else{
			echo sprintf($msg['unknown_user_id'], $id);
		}
	}
	
	public static function get_url_base() {
		global $base_path;
		
		return $base_path."/admin.php?categ=users&sub=users";
	}
}
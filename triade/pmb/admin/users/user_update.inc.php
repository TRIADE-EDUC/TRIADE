<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: user_update.inc.php,v 1.47 2019-05-13 13:29:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$droits = 0;

/* le user admin ne peut perdre le droit admin */
if ($id==1) $form_admin = 1 ;
/* le user admin ne peut perdre le droit de forçage */
if ($id==1) $form_edition_forcing = 1 ;

if(isset($form_admin) && $form_admin) 				$droits = $droits + ADMINISTRATION_AUTH;
if(isset($form_catal) && $form_catal) 				$droits = $droits + CATALOGAGE_AUTH;
if(isset($form_circ) && $form_circ) 				$droits = $droits + CIRCULATION_AUTH;
if(isset($form_auth) && $form_auth) 				$droits = $droits + AUTORITES_AUTH;
if(isset($form_edition) && $form_edition) 			$droits = $droits + EDIT_AUTH;
if(isset($form_edition_forcing) && $form_edition_forcing) 	$droits = $droits + EDIT_FORCING_AUTH;
if(isset($form_sauv) && $form_sauv) 				$droits = $droits + SAUV_AUTH;
if(isset($form_pref) && $form_pref) 				$droits = $droits + PREF_AUTH;
if(isset($form_dsi) && $form_dsi) 					$droits = $droits + DSI_AUTH;
if(isset($form_acquisition) && $form_acquisition)	$droits = $droits + ACQUISITION_AUTH;
if(isset($form_restrictcirc) && $form_restrictcirc)	$droits = $droits + RESTRICTCIRC_AUTH;
if(isset($form_thesaurus) && $form_thesaurus)		$droits = $droits + THESAURUS_AUTH;
if(isset($form_transferts) && $form_transferts) 	$droits = $droits + TRANSFERTS_AUTH;
if(isset($form_extensions) && $form_extensions) 	$droits = $droits + EXTENSIONS_AUTH;
if(isset($form_demandes) && $form_demandes) 		$droits = $droits + DEMANDES_AUTH;
if(isset($form_fiches) && $form_fiches) 			$droits = $droits + FICHES_AUTH;
if(isset($form_cms) && $form_cms) 					$droits = $droits + CMS_AUTH;
if(isset($form_cms_build) && $form_cms_build) 		$droits = $droits + CMS_BUILD_AUTH;
if(isset($form_catal_modif_cb_expl) && $form_catal_modif_cb_expl) $droits = $droits + CATAL_MODIF_CB_EXPL_AUTH;
if(isset($form_acquisition_account_invoice_flg) && $form_acquisition_account_invoice_flg) 	$droits = $droits + ACQUISITION_ACCOUNT_INVOICE_AUTH;
if(isset($form_semantic) && $form_semantic) 		$droits = $droits + SEMANTIC_AUTH;
if(isset($form_concepts) && $form_concepts) 		$droits = $droits + CONCEPTS_AUTH;
if(isset($form_frbr) && $form_frbr) 				$droits = $droits + FRBR_AUTH;
if(isset($form_modelling) && $form_modelling) 		$droits = $droits + MODELLING_AUTH;

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
		while ($j=pmb_mysql_fetch_array($resultat_droits_expl)) {
			$temp_global="form_expl_visibilite_".$j["idlocation"];
			global ${$temp_global};
			switch (${$temp_global}) {
				case "explr_invisible":
					$form_expl_visibilitei[] = $j["idlocation"];
				break;
				case "explr_visible_mod":
					$form_expl_visibilitevm[] .= $j["idlocation"];
				break;
				case "explr_visible_unmod":
					$form_expl_visibilitevu[] .= $j["idlocation"];
				break;	
			}	
		}
		
		if (count($form_expl_visibilitei)) 
			$form_expl_visibilite[0]= implode(',',$form_expl_visibilitei);
		else
			$form_expl_visibilite[0]="0";
		
		if (count($form_expl_visibilitevm))	
			$form_expl_visibilite[1]= implode(',',$form_expl_visibilitevm);
		else 
			$form_expl_visibilite[1]="0";

		if (count($form_expl_visibilitevu))
			$form_expl_visibilite[2]= implode(',',$form_expl_visibilitevu);
		else
			$form_expl_visibilite[2]="0";

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
				$requete.= "nb_per_page_gestion, user_email, user_alert_resamail, user_alert_demandesmail, user_alert_subscribemail, user_alert_suggmail, user_alert_serialcircmail, explr_invisible, explr_visible_mod, explr_visible_unmod, deflt_notice_replace_keep_categories";
				if (isset($sel_group)) {
					$requete.= ", grp_num";
				}
				$requete.= ") VALUES";
				$requete .= "(null,'light',curdate(),curdate()";
				$requete .= ",'$form_login'";
				$requete .= ",password('$form_pwd')";
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
				if (isset($sel_group)) {
					$requete.= ", '$sel_group' ";
				}
				$requete.= ") ";
				$res = @pmb_mysql_query($requete);
				$id=pmb_mysql_insert_id();
				echo "<script>document.location=\"./admin.php?categ=users&sub=users&action=modif&id=$id\";</script>";
			}
		}
	} else {
		$requete = "SELECT username,nom,prenom,rights, user_lang, nb_per_page_search, nb_per_page_select, nb_per_page_gestion, explr_invisible, explr_visible_mod, explr_visible_unmod, grp_num  ";
		$requete .= "FROM users WHERE userid='$id' LIMIT 1 ";
		$res = @pmb_mysql_query($requete);
		$nbr = pmb_mysql_num_rows($res);
		
		$requete_param = "SELECT * FROM users WHERE userid='$id' LIMIT 1 ";
		$res_param = pmb_mysql_query($requete_param);
		$field_values = pmb_mysql_fetch_row( $res_param );
		
		if($nbr==1) {
			$row=pmb_mysql_fetch_row($res);
			
			$user = new user($id);
			$user->set_properties_from_form();
			$user->save();
		}
	}
}	

show_users();
echo window_title("$msg[7] $msg[25]");

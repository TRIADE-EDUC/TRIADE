<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_func.inc.php,v 1.100 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions pour la gestion des emprunteurs

require_once("$include_path/templates/empr.tpl.php");
require_once($class_path."/parametres_perso.class.php");
if ($ldap_accessible) require_once($include_path."/ldap_param.inc.php");
require_once("$class_path/opac_view.class.php");
require_once("$class_path/docs_location.class.php");
require_once("$include_path/templates/camera.tpl.php");

// affichage de la liste des langues
function make_empr_lang_combo($lang='') {
	// retourne le combo des langues avec la langue $lang selectionn?e
	// nécessite l'inclusion de XMLlist.class.php (normalement c'est déjà le cas partout
	global $include_path;
	global $msg;
	global $charset;

	// langue par defaut
	if(!$lang) $lang="fr_FR";
	$langues = new XMLlist("$include_path/messages/languages.xml");
	$langues->analyser();
	$clang = $langues->table;
	reset($clang);
	$combo = "<select name='form_empr_lang' id='empr_lang'>";
	foreach ($clang as $cle => $value) {
		// arabe seulement si on est en utf-8
		if (($charset != 'utf-8' and $cle != 'ar') or ($charset == 'utf-8')) {
			if(strcmp($cle, $lang) != 0) $combo .= "<option value='$cle'>$value ($cle)</option>";
				else $combo .= "<option value='$cle' selected>$value ($cle)</option>";
		}
	}
	$combo .= "</select>";
	return $combo;
	}

// affichage de la liste lecteurs pour selection
function list_empr($cb, $empr_list, $nav_bar, $nb_total=0, $where_intitule="") {
	global $empr_list_tmpl,$empr_search_cle_tmpl;
	
	if ($cb != "") {
		if ($nb_total>0) $empr_search_cle_tmpl = str_replace("<!--!!nb_total!!-->", "(".$nb_total.")", $empr_search_cle_tmpl);
		$empr_search_cle_tmpl = str_replace("!!cle!!", $cb, $empr_search_cle_tmpl);
		$empr_search_cle_tmpl = str_replace("!!where_intitule!!", $where_intitule, $empr_search_cle_tmpl);
		$empr_list_tmpl = str_replace("!!empr_search_cle_tmpl!!", $empr_search_cle_tmpl, $empr_list_tmpl);
	} else {
		$empr_list_tmpl = str_replace("!!empr_search_cle_tmpl!!", "", $empr_list_tmpl);
	}

	$empr_list_tmpl = str_replace("!!list!!", $empr_list, $empr_list_tmpl);
	$empr_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $empr_list_tmpl);
		
	print pmb_bidi($empr_list_tmpl);
}

// form de saisie cb emprunteur
function get_cb($title, $message, $title_form, $form_action, $check=0, $cb_initial="", $creation=0) {
	global $empr_cb_tmpl;
	global $empr_cb_tmpl_create;
	global $script1;
	global $script2;
	global $deflt2docs_location, $pmb_lecteurs_localises, $empr_location_id, $param_allloc ;
	
	if ($cb_initial===0) $cb_initial="" ; 
	if ($creation==1) $empr_cb_tmpl = $empr_cb_tmpl_create;
	switch ($check) {
		case '1':
			// script javascript 1 : checke seulement si le champ contient des trucs
			$empr_cb_tmpl = str_replace("!!script!!", $script1, $empr_cb_tmpl);
			break ;
		case '2':
			// script javascript 2 : checke si le champ ne contient que de l'alpha
			$empr_cb_tmpl = str_replace("!!script!!", $script2, $empr_cb_tmpl);
			break ;
		case '0':
		default:
			// aucun test
			$empr_cb_tmpl = str_replace("!!script!!", "", $empr_cb_tmpl);
			break ;
	}
	$empr_cb_tmpl = str_replace("!!titre_formulaire!!", $title_form, $empr_cb_tmpl);
	$empr_cb_tmpl = str_replace("!!form_action!!", $form_action, $empr_cb_tmpl);
	$empr_cb_tmpl = str_replace("!!title!!", $title, $empr_cb_tmpl);
	$empr_cb_tmpl = str_replace("!!message!!", $message, $empr_cb_tmpl);
	$empr_cb_tmpl = str_replace("!!cb_initial!!", (string)$cb_initial, $empr_cb_tmpl);
	
	if ($pmb_lecteurs_localises) {
		if ($empr_location_id) $deflt2docs_location=$empr_location_id;
		elseif ($param_allloc) $deflt2docs_location=0;
		$empr_cb_tmpl = str_replace("!!restrict_location!!", docs_location::gen_combo_box_empr($deflt2docs_location), $empr_cb_tmpl);
	} else 
		$empr_cb_tmpl = str_replace("!!restrict_location!!", "", $empr_cb_tmpl);
	print pmb_bidi($empr_cb_tmpl);
}

// form de saisie cb emprunteur
function get_login_empr_pret($title, $message, $title_form, $form_action, $check=0, $cb_initial="") {
	global $login_empr_pret_tmpl;
	global $script1;
	global $script2;
	
	if ($cb_initial===0) $cb_initial="" ; 
	$login_empr_pret_tmpl = str_replace("!!titre_formulaire!!", $title_form, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!form_action!!", $form_action, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!title!!", $title, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!message!!", $message, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!cb_initial!!", (string)$cb_initial, $login_empr_pret_tmpl);
	
	print pmb_bidi($login_empr_pret_tmpl);
}

// affichage du form emprunteurs (gere modif et creation).
function show_empr_form($form_action, $form_cancel, $link, $id, $cb,$duplicate_empr_from_id="") {
	global $empr_form;
	global $dbh,$msg,$charset;
	global $biblio_email;
	global $aff_list_empr;
	global $deflt2docs_location;
	global $pmb_lecteurs_localises ;
	global $pmb_gestion_abonnement,$pmb_gestion_financiere,$empr_abonnement_default_debit;
	global $empr_prolong_calc_date_adhes_depassee;
	global $database_window_title ;
	global $lang;
	global $pmb_rfid_activate, $pmb_rfid_serveur_url;
	global $pmb_opac_view_activate;
	global $camera_tpl, $empr_pics_folder, $empr_pics_url, $deflt_camera_empr, $photo_tpl;
	global $base_path;
	
	// si $id est fourni, il s'agit d'une modification. on recupere les donnees dans $link
	if($id) {
		// modification
		echo window_title($database_window_title.$msg[55]);
		$entete=$msg[55];		
		if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url) {
			$script_rfid_encode="if(script_rfid_encode()==false) return false;";
		} else 	$script_rfid_encode='';
		$empr_form = str_replace("!!questionrfid!!",   $script_rfid_encode, $empr_form);
		$requete = "SELECT * FROM empr WHERE id_empr='$id' ";
		$res = pmb_mysql_query($requete, $link);
		if($res) {
			$empr = pmb_mysql_fetch_object($res);
		} else {
			error_message( $msg[53], $msg[54], 0);
		}
		if (!$duplicate_empr_from_id) {
			$empr_form = str_replace('!!empr_create_script_call!!','',$empr_form);
			$empr_form = str_replace('!!empr_create_script_loader!!','',$empr_form);
		} else {
			if (file_exists($base_path.'/javascript/empr_create_script.js')) {
				$empr_form = str_replace('!!empr_create_script_call!!','empr_create_script();',$empr_form);
				$empr_form = str_replace('!!empr_create_script_loader!!','<script type="text/javascript" src="javascript/empr_create_script.js"></script>',$empr_form);
			} else {
				$empr_form = str_replace('!!empr_create_script_call!!','',$empr_form);
				$empr_form = str_replace('!!empr_create_script_loader!!','',$empr_form);
			}
		}
	} else {
		// création
		$empr = new stdClass();
		$empr->empr_cb = '';
		$empr->empr_nom = '';
		$empr->empr_prenom = '';
		$empr->empr_adr1 = '';
		$empr->empr_adr2 = '';
		$empr->empr_cp = '';
		$empr->empr_ville = '';
		$empr->empr_pays = '';
		$empr->empr_mail = '';
		$empr->empr_tel1 = '';
		$empr->empr_sms = '';		
		$empr->empr_tel2 = '';
		$empr->empr_prof = '';
		$empr->empr_year = '';
		$empr->empr_lang = '';		
		$empr->empr_login = '';
		$empr->empr_msg = '';
		$empr->empr_statut = '';
		$empr->empr_categ = '';
		$empr->empr_codestat = '';
		$empr->empr_sexe = '';
		$empr->empr_ldap = '';
		$empr->empr_location = '';
		
		$entete=$msg[15];
		$empr_form = str_replace("!!questionrfid!!",  '' , $empr_form);
		if (file_exists($base_path.'/javascript/empr_create_script.js')) {
			$empr_form = str_replace('!!empr_create_script_call!!','empr_create_script();',$empr_form);
			$empr_form = str_replace('!!empr_create_script_loader!!','<script type="text/javascript" src="javascript/empr_create_script.js"></script>',$empr_form);
		} else {
			$empr_form = str_replace('!!empr_create_script_call!!','',$empr_form);
			$empr_form = str_replace('!!empr_create_script_loader!!','',$empr_form);
		}
	}
	if ($duplicate_empr_from_id) {
		$empr_form = str_replace("!!id!!",   "", $empr_form);
		$empr_form = str_replace("!!entete!!", $msg["empr_duplicate"], $empr_form);
	} else {
		 $empr_form = str_replace("!!id!!",   $id, $empr_form);
		 $empr_form = str_replace("!!entete!!", $entete, $empr_form);
	}
	$empr_form = str_replace("!!form_action!!",   $form_action, $empr_form);

	if($empr_pics_folder) {
		if($deflt_camera_empr) {
			$camera_tpl = str_replace("!!upload_folder!!", $empr_pics_folder, $camera_tpl);			
			$empr_form = str_replace("!!camera!!", gen_plus('emr_camera', $msg['empr_photo_capture'], $camera_tpl, 0, "init_camera('" . $empr_pics_folder . "', '" . $empr_pics_url . "', 'f_cb', '!!num_carte!!');"), $empr_form);		
		} else {		
			$photo_tpl = str_replace("!!upload_folder!!", $empr_pics_folder, $photo_tpl);
			$empr_form = str_replace("!!camera!!", gen_plus('emr_camera', $msg['empr_photo_capture'], $photo_tpl, 0, "init_camera('" . $empr_pics_folder . "', '" . $empr_pics_url . "', 'f_cb', '!!num_carte!!');"), $empr_form);		
		}
	} else {
		$empr_form = str_replace("!!camera!!", '', $empr_form);		
	}	
	
	if($empr->empr_cb) { //Si il y a un code lecteur
		if (!$duplicate_empr_from_id) $empr_form = str_replace("!!cb!!",      $empr->empr_cb,      $empr_form);
		else $empr_form = str_replace("!!cb!!",      $cb,      $empr_form);
		
		$date_adhesion = (!$duplicate_empr_from_id ? $empr->empr_date_adhesion : date('Y-m-d'));
			
		$empr_form = str_replace("!!adhesion!!", get_input_date('form_adhesion', 'form_adhesion', $date_adhesion), $empr_form);

		if ($duplicate_empr_from_id) {
			/* AJOUTER ICI LE CALCUL EN FONCTION DE LA CATEGORIE */
			$rqt_empr_categ = "select duree_adhesion from empr_categ where id_categ_empr = ".$empr->empr_categ;
			$res_empr_categ = pmb_mysql_query($rqt_empr_categ, $dbh);
			$empr_categ = pmb_mysql_fetch_object($res_empr_categ);
			//$form_adhesion=preg_replace('/-/', '', $form_adhesion);
			
			$rqt_date = "select date_add('".$date_adhesion."', INTERVAL $empr_categ->duree_adhesion DAY) as date_expiration " ;
			$resultatdate=pmb_mysql_query($rqt_date);
			$resdate=pmb_mysql_fetch_object($resultatdate);
			$empr->empr_date_expiration = $resdate->date_expiration;
		}

		$empr_form = str_replace("!!expiration!!", get_input_date('form_expiration', 'form_expiration', $empr->empr_date_expiration), $empr_form);

		// ajout ici des trucs sur la relance adhésion
		$empr_temp = new emprunteur($id, '', FALSE, 0) ;
		$aff_relance = "";
		if ($empr_temp->adhesion_renouv_proche() || $empr_temp->adhesion_depassee()) {
			if ($empr_temp->adhesion_depassee()) $mess_relance = $msg['empr_date_depassee'];
				else $mess_relance = $msg['empr_date_renouv_proche'];

			$rqt="select duree_adhesion from empr_categ where id_categ_empr='$empr_temp->categ'";
			$res_dur_adhesion = pmb_mysql_query($rqt, $dbh);
			$row = pmb_mysql_fetch_row($res_dur_adhesion);
			$nb_jour_adhesion_categ = $row[0];
			
			if ($empr_prolong_calc_date_adhes_depassee && $empr_temp->adhesion_depassee()) {
				$rqt_date = "select date_add(curdate(),INTERVAL 1 DAY) as nouv_date_debut,
						date_add(curdate(),INTERVAL $nb_jour_adhesion_categ DAY) as nouv_date_fin ";
			} else {
				$rqt_date = "select date_add('$empr_temp->date_expiration',INTERVAL 1 DAY) as nouv_date_debut,
						date_add('$empr_temp->date_expiration',INTERVAL $nb_jour_adhesion_categ DAY) as nouv_date_fin ";
			}
			$resultatdate=pmb_mysql_query($rqt_date) or die ("<br /> $rqt_date ".pmb_mysql_error());
			$resdate=pmb_mysql_fetch_object($resultatdate);

			$nouv_date_debut = $resdate->nouv_date_debut ;
			$nouv_date_fin = $resdate->nouv_date_fin ;

			$nouv_date_debut_formatee = formatdate($nouv_date_debut) ;
			$nouv_date_fin_formatee = formatdate($nouv_date_fin) ;

			// on conserve la date d'adhésion initiale
			$action_prolonger = "dijit.byId('form_expiration').set('value','".$nouv_date_fin."');this.form.is_subscription_extended.value = 1;";

			$action_relance_courrier = "openPopUp('./pdf.php?pdfdoc=lettre_relance_adhesion&id_empr=$id', 'lettre'); return(false) ";

			$aff_relance = "<div class='row'>
						<span class='erreur'>$mess_relance</span><br />
						<input type='hidden' id='is_subscription_extended' name='is_subscription_extended' value='0' />
						<input class='bouton' type='button' value=\"".$msg['prolonger']."\" onClick=\"$action_prolonger\" />&nbsp;
						<input class='bouton' type='button' value=\"".$msg['prolong_courrier']."\" onClick=\"$action_relance_courrier\" />";

			if ($empr_temp->mail && $biblio_email ) {
				$action_relance_mail = "if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_relance_adhesion&id_empr=$id', 'mail'); } return(false) ";
				$aff_relance .= "&nbsp;<input class='bouton' type='button' value=\"".$msg['prolong_mail']."\" onClick=\"$action_relance_mail\" />";
			}

			$aff_relance .= "</div>";
			
			if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement)) {
				$aff_relance.="<div class='row'><input type='radio' name='debit' value='0' id='debit_0' ".(!$empr_abonnement_default_debit ? "checked" : "")." /><label for='debit_0'>".$msg["finance_abt_no_debit"]."</label>&nbsp;<input type='radio' name='debit' value='1' id='debit_1' ".(($empr_abonnement_default_debit == 1) ? "checked" : "")." />";
				$aff_relance.="<label for='debit_1'>".$msg["finance_abt_debit_wo_caution"]."</label>&nbsp;";
				if ($pmb_gestion_abonnement==2) $aff_relance.="<input type='radio' name='debit' value='2' id='debit_2' ".(($empr_abonnement_default_debit == 2) ? "checked" : "")." /><label for='debit_2'>".$msg["finance_abt_debit_wt_caution"]."</label>";
				$aff_relance.="</div>";
			}
		}
		$empr_form = str_replace("!!adhesion_proche_depassee!!", $aff_relance, $empr_form);

		//Liste des types d'abonnement
		$list_type_abt="";
		if (($pmb_gestion_abonnement==2)&&($pmb_gestion_financiere)) {
			$requete="select * from type_abts order by type_abt_libelle ";
			$resultat_abt=pmb_mysql_query($requete);
			
			$user_loc=$deflt2docs_location;
			$t_type_abt=array();
			while ($res_abt=pmb_mysql_fetch_object($resultat_abt)) {
				$locs=explode(",",$res_abt->localisations);
				$as=array_search($user_loc,$locs);
				if ((($as!==false)&&($as!==null))||(!$res_abt->localisations)) {
					$t_type_abt[]=$res_abt;
				}
			}
			if (count($t_type_abt)) {
				$list_type_abt="<div class='row'>\n<label for='type_abt'>".$msg["finance_type_abt"]."</label></div>\n<div class='row'>\n<select name='type_abt' id='type_abt'>\n";
				for ($i=0; $i<count($t_type_abt); $i++) {
					$list_type_abt.="<option value='".$t_type_abt[$i]->id_type_abt."'";
					if ($empr->type_abt==$t_type_abt[$i]->id_type_abt) $list_type_abt.=" selected";
					$list_type_abt.=">".htmlentities($t_type_abt[$i]->type_abt_libelle,ENT_QUOTES,$charset)."</option>\n";
				}
				$list_type_abt.="</select></div>";
			}
		}
		$empr_form = str_replace("!!typ_abonnement!!",$list_type_abt,$empr_form);
	} else { // création de lecteur
		$empr->empr_date_adhesion = today() ;
		$empr_form = str_replace('!!cb!!',$cb,$empr_form);
		$adhesion = "<input type='text' style='width: 10em;' name='form_adhesion' id='form_adhesion' value='".$empr->empr_date_adhesion."'
					data-dojo-type='dijit/form/DateTextBox' required='false' />";
		$empr_form = str_replace("!!adhesion!!", $adhesion, $empr_form);
		$empr_form = str_replace("!!adhesion_proche_depassee!!", "", $empr_form);
		$empr_form = str_replace("!!expiration!!",   "<input type='hidden' name='form_expiration' value=''>",   $empr_form);
		
		//Liste des types d'abonnement
		$list_type_abt="";
		if (($pmb_gestion_abonnement==2)&&($pmb_gestion_financiere)) {
			$requete="select * from type_abts";
			$resultat_abt=pmb_mysql_query($requete);
			
			$user_loc=$deflt2docs_location;
			$t_type_abt=array();
			while ($res_abt=pmb_mysql_fetch_object($resultat_abt)) {
				$locs=explode(",",$res_abt->localisations);
				$as=array_search($user_loc,$locs);
				if ((($as!==false)&&($as!==null))||(!$res_abt->localisations)) {
					$t_type_abt[]=$res_abt;
				}
			}
			if (count($t_type_abt)) {
				$list_type_abt="<div class='row'>\n<label for='type_abt'>".$msg["finance_type_abt"]."</label></div>\n<div class='row'>\n<select name='type_abt' id='type_abt'>\n";
				for ($i=0; $i<count($t_type_abt); $i++) {
					$list_type_abt.="<option value='".$t_type_abt[$i]->id_type_abt."'>".htmlentities($t_type_abt[$i]->type_abt_libelle,ENT_QUOTES,$charset)."</option>\n";
				}
				$list_type_abt.="</select></div>";
			}
		}
		$empr_form = str_replace("!!typ_abonnement!!",$list_type_abt,$empr_form);
	}
		
	$empr_form = str_replace("!!nom!!",      htmlentities($empr->empr_nom   ,ENT_QUOTES, $charset), $empr_form);
	$empr_form = str_replace("!!prenom!!",      htmlentities($empr->empr_prenom   ,ENT_QUOTES, $charset), $empr_form);
	$empr_form = str_replace("!!adr1!!",      htmlentities($empr->empr_adr1   ,ENT_QUOTES, $charset),   $empr_form);
	$empr_form = str_replace("!!adr2!!",      htmlentities($empr->empr_adr2   ,ENT_QUOTES, $charset),   $empr_form);
	$empr_form = str_replace("!!cp!!",      htmlentities($empr->empr_cp   ,ENT_QUOTES, $charset), $empr_form);
	$empr_form = str_replace("!!ville!!",      htmlentities($empr->empr_ville   ,ENT_QUOTES, $charset),   $empr_form);
	$empr_form = str_replace("!!pays!!",      htmlentities($empr->empr_pays   ,ENT_QUOTES, $charset),   $empr_form);
	$empr_form = str_replace("!!mail!!",      htmlentities($empr->empr_mail   ,ENT_QUOTES, $charset),   $empr_form);
	$empr_form = str_replace("!!tel1!!",      htmlentities($empr->empr_tel1   ,ENT_QUOTES, $charset),   $empr_form);
	if(!$empr->empr_sms) $empr_sms_chk=''; else $empr_sms_chk="checked='checked'";
	$empr_form = str_replace('!!sms!!', $empr_sms_chk, $empr_form);
	$empr_form = str_replace("!!tel2!!",      htmlentities($empr->empr_tel2   ,ENT_QUOTES, $charset),   $empr_form);
	$empr_form = str_replace("!!prof!!",      htmlentities($empr->empr_prof   ,ENT_QUOTES, $charset),   $empr_form);
	if ($empr->empr_year != 0) $empr_form = str_replace("!!year!!",      htmlentities($empr->empr_year   ,ENT_QUOTES, $charset),   $empr_form);
		else $empr_form = str_replace("!!year!!", "", $empr_form);
	if (!$empr->empr_lang) $empr->empr_lang=$lang;
	$empr_form = str_replace('!!combo_empr_lang!!', make_empr_lang_combo($empr->empr_lang), $empr_form);

	if (!$duplicate_empr_from_id) { 
		$empr_form = str_replace('!!empr_login!!', $empr->empr_login, $empr_form);
		$empr_form = str_replace("!!empr_msg!!",      htmlentities($empr->empr_msg   ,ENT_QUOTES, $charset),   $empr_form);
	} else {
		$empr_form = str_replace('!!empr_login!!', "", $empr_form);
		$empr_form = str_replace("!!empr_msg!!", "",   $empr_form);
	}
	
	//Si il n'y a pas de statut, categ, codestat on prend celui définit pour l'utilisateur
	if(!$empr->empr_statut){
		global $deflt_empr_statut;
		$empr->empr_statut=$deflt_empr_statut;
	}
	if(!$empr->empr_categ){
		global $deflt_empr_categ;
		$empr->empr_categ=$deflt_empr_categ;
	}
	if(!$empr->empr_codestat){
		global $deflt_empr_codestat;
		$empr->empr_codestat=$deflt_empr_codestat;
	}
	// on récupère le select catégorie
	$requete = "SELECT id_categ_empr, libelle, duree_adhesion FROM empr_categ ORDER BY libelle ";
	$res = pmb_mysql_query($requete, $link);
	$nbr_lignes = pmb_mysql_num_rows($res);
	$categ_content='';
	$empr_grille_categ="<select id='empr_grille_categ' style='display:none;' onChange=\"get_pos(); expandAll(); if (inedit) move_parse_dom(relative); else initIt();\"><option value='0' selected='selected' >".$msg['all_categories_empr']."</option>";
	for($i=0; $i < $nbr_lignes; $i++) {
		$row = pmb_mysql_fetch_row($res);
		$categ_content.= "<option value='$row[0]'";
		if($row[0] == $empr->empr_categ) $categ_content .= " selected='selected'";
		$categ_content .= ">$row[1]</option>";
		$empr_grille_categ.="<option value='$row[0]'>$row[1]</option>";
	}
	$empr_grille_categ.='</select>';
	$empr_form = str_replace("!!categ!!", $categ_content, $empr_form);

	// Ajout des categories et localisations pour edition des grilles
	$empr_form = str_replace("<!-- empr_grille_categ -->", $empr_grille_categ,  $empr_form);
	if ($pmb_lecteurs_localises) {
		$empr_grille_location = docs_location::get_html_select(array(0),array('id'=>0,'msg'=>$msg['all_locations_empr']),array('id'=>'empr_grille_location','class'=>'saisie-20em','style'=>'display:none;', 'onChange'=>'get_pos(); expandAll(); if (inedit) move_parse_dom(relative); else initIt();'));
	} else {
		$empr_grille_location="<input type='hidden' id='empr_grille_location' value='0' />";
	}
	$empr_form = str_replace("<!-- empr_grille_location -->", $empr_grille_location, $empr_form);
	
	$requete = "SELECT id_categ_empr, libelle, duree_adhesion FROM empr_categ ORDER BY libelle ";
	$res = pmb_mysql_query($requete, $link);
	$grille_categ="<option value='0' selected='selected'>".$msg['all_categories_empr']."</value>";
	for($i=0; $i < $nbr_lignes; $i++) {
		$row = pmb_mysql_fetch_row($res);
		$categ_content.= "<option value='$row[0]'";
		if($row[0] == $empr->empr_categ) $categ_content .= " selected='selected'";
		$categ_content .= ">$row[1]</option>";
		$grille_categ.="<option value='$row[0]'>$row[1]</option>";
	}
	$empr_form = str_replace("!!categ!!",      $categ_content,   $empr_form);
	
	// on récupère le select statut
	$requete = "SELECT idstatut, statut_libelle FROM empr_statut ORDER BY statut_libelle ";	
	$res = pmb_mysql_query($requete, $link);
	$nbr_lignes = pmb_mysql_num_rows($res);
	$statut_content = "";
	for($i=0; $i < $nbr_lignes; $i++) {
		$row = pmb_mysql_fetch_row($res);
		$statut_content .= "<option value='$row[0]'";
		if($row[0] == $empr->empr_statut) $statut_content .= " selected='selected'";
		$statut_content .= ">$row[1]</option>";
	}
	$empr_form = str_replace("!!statut!!",      $statut_content,   $empr_form);

	// et le select code stat
	// on récupère le select cod stat
	$requete = "SELECT idcode, libelle FROM empr_codestat ORDER BY libelle ";
	$res = pmb_mysql_query($requete, $link);
	$nbr_lignes = pmb_mysql_num_rows($res);

	$cstat_content = "";
	for($i=0; $i < $nbr_lignes; $i++) {
		$row = pmb_mysql_fetch_row($res);
		$cstat_content .= "<option value='$row[0]'";
		if($row[0] == $empr->empr_codestat) $cstat_content .= " selected='selected'";
		$cstat_content .= ">$row[1]</option>";
	}

	// mise à jour du sexe
	switch($empr->empr_sexe) {
		case 1:
			$empr_form = str_replace("sexe_select_1", 'selected', $empr_form);
			break;
		case 2:
			$empr_form = str_replace("sexe_select_2", 'selected', $empr_form);
			break;
		default:
			$empr_form = str_replace("sexe_select_0", 'selected', $empr_form);
			break;
	}
	$empr_form = preg_replace("/sexe_select_[0-2]/m", '', $empr_form);
	$empr_form = str_replace("!!cstat!!",      $cstat_content,   $empr_form);

	$empr_form = str_replace("!!groupe_ajout!!", get_groups_form($id), $empr_form);

	$empr_form = str_replace('!!cancel!!',$form_cancel,$empr_form);

	// ldap MaxMan
	if ($empr->empr_ldap){
		$form_ldap="checked" ;
	}else{
		$form_ldap="";
	}
		//$empr_form = str_replace('!!empr_password!!', $empr_password, $empr_form);
	$empr_form = str_replace("!!ldap!!",$form_ldap,$empr_form);

	$empr_form = str_replace('!!empr_password!!', '', $empr_form);
	
	if (!$empr->empr_location) $empr->empr_location=$deflt2docs_location ;
	if ($pmb_lecteurs_localises) {
		$loc = "
			<div class='colonne4' id='g2_r1_f0'  movable='yes' title='".htmlentities($msg['empr_location'],ENT_QUOTES,$charset)."'>
				<div class='row'>
					<label for='form_empr_location' class='etiquette'>".$msg['empr_location']."</label>
				</div>
				<div class='row'>
					!!localisation!!
				</div>
			</div>
		";
	
		//$loc = str_replace('!!localisation!!', docs_location::gen_combo_box_empr($empr->empr_location, 0), $loc);
		$loc = str_replace('!!localisation!!', docs_location::get_html_select(array($empr->empr_location),array(),array('id'=>'empr_location_id','name'=>'empr_location_id')), $loc);
	} else {
		$loc = "<input type='hidden' name='empr_location_id' id='empr_location_id' value='".$empr->empr_location."'>" ; 
		$empr_form = str_replace('<!-- !!localisation!! -->', $loc, $empr_form);
	}
	$empr_form = str_replace('<!-- !!localisation!! -->', $loc, $empr_form);
	
	if($pmb_opac_view_activate ){
		$opac_view_tpl = "
			<div class='row' id='g4_r1_f0' movable='yes' title='".htmlentities($msg['empr_form_opac_view'],ENT_QUOTES,$charset)."'>				
					!!opac_view!!
			</div>";
		$opac_view = new opac_view(0,$id);		
		$opac_view_tpl=str_replace("!!opac_view!!",gen_plus("opac_view",$msg["empr_form_opac_view"],$opac_view->do_sel_list(),0),$opac_view_tpl);
	} else {
		$opac_view_tpl = "";
	}
	$empr_form = str_replace('<!-- !!opac_view!! -->', $opac_view_tpl, $empr_form);	
	//Champs persos
	$p_perso=new parametres_perso("empr");
	$perso_=$p_perso->show_editable_fields($id);
	if (isset($perso_["FIELDS"]) && count($perso_["FIELDS"])) $perso = "<div class='row'></div>" ;
		else $perso="";
	$class="colonne2";
	if(isset($perso_["FIELDS"])) {
		for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
			$p=$perso_["FIELDS"][$i];
			$perso.="<div class='$class' id='g6_r0_f".$p["ID"]."' movable='yes' title='".htmlentities($p['TITRE'],ENT_QUOTES,$charset)."' >";
			$perso.="<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."</div>\n";
			$perso.="<div class='row'>";
			$perso.=$p["AFF"]."</div>";
			$perso.="</div>";
			if ($class=="colonne2") $class="colonne_suite"; else $class="colonne2";
		}
	}
	if ($class=="colonne_suite") $perso.="<div class='$class'>&nbsp;</div>";
	$perso.=$perso_["CHECK_SCRIPTS"];
	$empr_form=str_replace("!!champs_perso!!",$perso,$empr_form);
	
	$empr_form = str_replace('!!empr_notice_override!!',get_rights_form($id),$empr_form);
	print pmb_bidi($empr_form);
}

//creation formulaire surcharge des droits d'accès emprunteurs-notices
function get_rights_form($empr_id=0) {
		
	global $dbh,$msg,$charset;
	global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;
	global $gestion_acces_empr_contribution_area, $gestion_acces_empr_contribution_scenario;
	global $gestion_acces_contribution_moderator_empr;
	global $class_path;
	
	$form = '';
	if (!$empr_id) return $form;
	
	if ($gestion_acces_active==1) {
		
		require_once($class_path.'/acces.class.php');
		$ac = new acces();
		
		$acces_list = array(
				2 => $gestion_acces_empr_notice,
				3 => $gestion_acces_empr_docnum,
				4 => $gestion_acces_empr_contribution_area,
				5 => $gestion_acces_empr_contribution_scenario,
				6 => $gestion_acces_contribution_moderator_empr
		);
		
		foreach ($acces_list as $index => $acces_active) {
			if ($acces_active == 1) {
				$dom = $ac->setDomain($index);
				
				//Role utilisateur
				$def_usr_prf=$dom->getComment('user_prf_def_lib');
				$cur_usr_prf=$dom->getUserProfile($empr_id);
		
				//Recuperation des droits generiques du domaine pour avoir les droits utilisateurs globaux
				$global_rights = $dom->getDomainRights(0,0);
				
				//Recuperation profils ressources
				$t_r = array();
				$t_r[0] = $dom->getComment('res_prf_def_lib');	//profile ressource par defaut
				$q_r = $dom->loadUsedResourceProfiles();
				$r_r = pmb_mysql_query($q_r, $dbh);
				if (pmb_mysql_num_rows($r_r)) {
					while(($row = pmb_mysql_fetch_object($r_r))) {
						$t_r[$row->prf_id] = $row->prf_name;
					}
				}
		
				//Recuperation des controles dependants de l'utilisateur
				$t_ctl=$dom->getControls(0);
		
				//recuperation des droits du domaine pour un utilisateur
				$t_rights = $dom->get_user_rights($empr_id, $cur_usr_prf);
				
				$r_form = "
						<div class='row'>
							<label class='etiquette'>".htmlentities($dom->getComment('long_name'), ENT_QUOTES, $charset)."</label>
						</div>";

				if (($global_rights & 512)) {
					$r_form = "
					<label class='etiquette'>".htmlentities($dom->getComment('override'), ENT_QUOTES, $charset)."</label>
					<select id='override_rights[".$index."]' name='override_rights[".$index."]' >
					<option value='0' selected='selected'>".htmlentities($dom->getComment('override_none'), ENT_QUOTES, $charset)."</option>
					<option value='1'>".htmlentities($dom->getComment('override_yes'), ENT_QUOTES, $charset)."</option>
					<option value='2'>".htmlentities($dom->getComment('override_no'), ENT_QUOTES, $charset)."</option>
					</select>";
				}
				$r_form.= "
				<div class='row'>
				<div class='row'><!-- rights_tab --></div>
				</div>";
				
				if (count($t_r)) {
					$h_tab = "<div class='dom_div'><table class='dom_tab'><tr>";
					foreach($t_r as $k=>$v) {
						$h_tab.= "<th class='dom_col'>".htmlentities($v, ENT_QUOTES, $charset)."</th>";
					}
					$h_tab.="</tr><!-- rights_tab --></table></div>";
		
					$c_tab = '<tr>';
					foreach($t_r as $k=>$v) {
		
						$c_tab.= "<td><table style='border:1px solid;'><!-- rows --></table></td>";
						$t_rows = "";
							
						foreach($t_ctl as $k2=>$v2) {
								
							$t_rows.="
							<tr>
							<td style='width:25px;' ><input type='checkbox' name='chk_rights[".$index."][".$k."][".$k2."]' value='1' ";
		
							if ($t_rights[$cur_usr_prf][$k] & (pow(2,$k2-1))) {
								$t_rows.= "checked='checked' ";
							}
							if(($global_rights & 512)==0) $t_rows.="disabled='disabled' ";
							$t_rows.="/></td>
							<td>".htmlentities($v2, ENT_QUOTES, $charset)."</td>
							</tr>";
						}
						$c_tab = str_replace('<!-- rows -->', $t_rows, $c_tab);
					}
					$c_tab.= "</tr>";
		
				}
				$h_tab = str_replace('<!-- rights_tab -->', $c_tab, $h_tab);;
				$r_form=str_replace('<!-- rights_tab -->', $h_tab, $r_form);
					
				$form.= $r_form;
			}
		}
	}
	return $form;
}

function get_groups_form($empr_id=0) {
	global $msg;

	$empr_id += 0;
	$query = "SELECT id_groupe, libelle_groupe, ifnull(empr_id,0) as inscription FROM groupe join empr_groupe on (id_groupe=groupe_id  and empr_id=".$empr_id.")  ORDER BY libelle_groupe";
	$result = pmb_mysql_query($query);
	$groups = array();
	if(pmb_mysql_num_rows($result)) {
		while ($row = pmb_mysql_fetch_object($result)) {
			$groups[] = array('id' => $row->id_groupe, 'name' => $row->libelle_groupe);
		}
	}
	return templates::get_display_elements_completion_field($groups, 'empr_form', 'form_groups', 'group_id', 'groups');
// 	return gen_liste_multiple ($query, "id_groupe", "libelle_groupe", "inscription", "form_groups[]", "", $empr_id, 0, $msg['empr_form_aucungroupe'], 0,$msg['empr_form_nogroupe'], 5) ;
}



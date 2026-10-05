<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_func.inc.php,v 1.122.2.1.2.1 2025/03/21 09:05:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions pour la gestion des emprunteurs
global $class_path, $include_path, $ldap_accessible;

require_once "$include_path/templates/empr.tpl.php";
require_once "$class_path/parametres_perso.class.php";
if ($ldap_accessible) {
	require_once "$include_path/ldap_param.inc.php";
}
require_once "$class_path/opac_view.class.php";
require_once "$class_path/docs_location.class.php";
require_once "$include_path/templates/camera.tpl.php";
require_once "$class_path/emprunteur.class.php";
require_once ($class_path.'/interface/entity/interface_entity_reader_form.class.php');

// affichage de la liste des langues
function make_empr_lang_combo($lang='') {
	// retourne le combo des langues avec la langue $lang selectionnee
	// nécessite l'inclusion de XMLlist.class.php (normalement c'est déjà le cas partout
	global $include_path;
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
	
	if ($cb_initial===0) $cb_initial="" ; 
	$login_empr_pret_tmpl = str_replace("!!titre_formulaire!!", $title_form, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!form_action!!", $form_action, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!title!!", $title, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!message!!", $message, $login_empr_pret_tmpl);
	$login_empr_pret_tmpl = str_replace("!!cb_initial!!", (string)$cb_initial, $login_empr_pret_tmpl);
	
	print pmb_bidi($login_empr_pret_tmpl);
}

function get_empr_content_form($empr, $id, $cb,$duplicate_empr_from_id="") {
	global $msg, $charset;
	global $pmb_form_empr_editables, $empr_content_form, $empr_content_form_newgrid;
	global $lang;
	global $pmb_opac_view_activate;
	global $empr_pics_folder, $empr_pics_url, $deflt_camera_empr, $camera_tpl, $photo_tpl;
	global $biblio_email;
	global $deflt2docs_location, $deflt_type_abts;
	global $pmb_lecteurs_localises ;
	global $pmb_gestion_abonnement,$pmb_gestion_financiere,$empr_abonnement_default_debit;
	global $empr_prolong_calc_date_adhes_depassee;
	
	if($pmb_form_empr_editables == 2) {
		$content_form = $empr_content_form_newgrid;
	} else {
		$content_form = $empr_content_form;
	}
	if($empr_pics_folder) {
		if($deflt_camera_empr) {
			$camera_tpl = str_replace("!!upload_folder!!", $empr_pics_folder, $camera_tpl);
			$content_form = str_replace("!!camera!!", gen_plus('emr_camera', $msg['empr_photo_capture'], $camera_tpl, 0, "init_camera('{$id}');"), $content_form);
		} else {
			$photo_tpl = str_replace("!!upload_folder!!", $empr_pics_folder, $photo_tpl);
			$content_form = str_replace("!!camera!!", gen_plus('emr_camera', $msg['empr_photo_capture'], $photo_tpl, 0, "init_camera('{$id}');"), $content_form);
		}
	} else {
		$content_form = str_replace("!!camera!!", '', $content_form);
	}
	
	if($empr->empr_cb) { //Si il y a un code lecteur
		if (!$duplicate_empr_from_id) $content_form = str_replace("!!cb!!",      $empr->empr_cb,      $content_form);
		else $content_form = str_replace("!!cb!!",      $cb,      $content_form);
		
		$date_adhesion = (!$duplicate_empr_from_id ? $empr->empr_date_adhesion : date('Y-m-d'));
		
		$content_form = str_replace("!!adhesion!!", get_input_date('form_adhesion', 'form_adhesion', $date_adhesion), $content_form);
		
		$content_form = str_replace("!!expiration!!", get_input_date('form_expiration', 'form_expiration', $empr->empr_date_expiration), $content_form);
		
		// ajout ici des trucs sur la relance adhésion
		$empr_temp = new emprunteur($id, '', FALSE, 0) ;
		$aff_relance = "";
		if ($empr_temp->adhesion_renouv_proche() || $empr_temp->adhesion_depassee()) {
			if ($empr_temp->adhesion_depassee()) $mess_relance = $msg['empr_date_depassee'];
			else $mess_relance = $msg['empr_date_renouv_proche'];
			
			$rqt="select duree_adhesion from empr_categ where id_categ_empr='$empr_temp->categ'";
			$res_dur_adhesion = pmb_mysql_query($rqt);
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
			$myDate = strtotime($nouv_date_fin);
			
			$nouv_date_debut_formatee = formatdate($nouv_date_debut) ;
			$nouv_date_fin_formatee = formatdate($nouv_date_fin) ;
			
			// on conserve la date d'adhésion initiale
			if (is_firefox_min_57_version()) {
				$action_prolonger = "dijit.byId('form_expiration').set('value','".$nouv_date_fin."');this.form.is_subscription_extended.value = 1;";
			} else {
				$action_prolonger = "document.getElementById('form_expiration').value='".$nouv_date_fin."';this.form.is_subscription_extended.value = 1;";
			}
			$action_prolonger .= "let myDate = new Date(".$myDate."*1000);";
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
		$content_form = str_replace("!!adhesion_proche_depassee!!", $aff_relance, $content_form);
	} else { // création de lecteur
		$content_form = str_replace('!!cb!!',$cb,$content_form);
		$adhesion = get_input_date('form_adhesion', 'form_adhesion', $empr->empr_date_adhesion, false);
		$content_form = str_replace("!!adhesion!!", $adhesion, $content_form);
		$content_form = str_replace("!!adhesion_proche_depassee!!", "", $content_form);
		$content_form = str_replace("!!expiration!!",   "<input type='hidden' name='form_expiration' value=''>",   $content_form);
		
// 		$empr->type_abt = $deflt_type_abts;
	}
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
				$res_abt->option_disabled = false;
			} else {
				$res_abt->option_disabled = true;
			}
			$t_type_abt[]=$res_abt;
		}
		if (count($t_type_abt)) {
            if(!$empr->empr_cb) { // En création
                $empr->type_abt = $deflt_type_abts;
		    }
			$list_type_abt="<div class='row'>\n<label for='type_abt'>".$msg["finance_type_abt"]."</label></div>\n<div class='row'>\n<select name='type_abt' id='type_abt'>\n";
			for ($i=0; $i<count($t_type_abt); $i++) {
				$list_type_abt.="<option value='".$t_type_abt[$i]->id_type_abt."' ".($empr->type_abt==$t_type_abt[$i]->id_type_abt ? "selected" : "")." ".($t_type_abt[$i]->option_disabled ? "disabled='disabled'" : "")." data-localisations='".$t_type_abt[$i]->localisations."'>".htmlentities($t_type_abt[$i]->type_abt_libelle,ENT_QUOTES,$charset)."</option>\n";
			}
			$list_type_abt.="</select></div>";
		}
	}
	$content_form = str_replace("!!typ_abonnement!!",$list_type_abt,$content_form);
	
	$content_form = str_replace("!!nom!!",      htmlentities($empr->empr_nom   ,ENT_QUOTES, $charset), $content_form);
	$content_form = str_replace("!!prenom!!",      htmlentities($empr->empr_prenom   ,ENT_QUOTES, $charset), $content_form);
	$content_form = str_replace("!!adr1!!",      htmlentities($empr->empr_adr1   ,ENT_QUOTES, $charset),   $content_form);
	$content_form = str_replace("!!adr2!!",      htmlentities($empr->empr_adr2   ,ENT_QUOTES, $charset),   $content_form);
	$content_form = str_replace("!!cp!!",      htmlentities($empr->empr_cp   ,ENT_QUOTES, $charset), $content_form);
	$content_form = str_replace("!!ville!!",      htmlentities($empr->empr_ville   ,ENT_QUOTES, $charset),   $content_form);
	$content_form = str_replace("!!pays!!",      htmlentities($empr->empr_pays   ,ENT_QUOTES, $charset),   $content_form);
	$content_form = str_replace("!!mail!!",      htmlentities($empr->empr_mail   ,ENT_QUOTES, $charset),   $content_form);
	$content_form = str_replace("!!tel1!!",      htmlentities($empr->empr_tel1   ,ENT_QUOTES, $charset),   $content_form);
	if(!$empr->empr_sms) $empr_sms_chk=''; else $empr_sms_chk="checked='checked'";
	$content_form = str_replace('!!sms!!', $empr_sms_chk, $content_form);
	$content_form = str_replace("!!tel2!!",      htmlentities($empr->empr_tel2   ,ENT_QUOTES, $charset),   $content_form);
	$content_form = str_replace("!!prof!!",      htmlentities($empr->empr_prof   ,ENT_QUOTES, $charset),   $content_form);
	if ($empr->empr_year != 0) $content_form = str_replace("!!year!!",      htmlentities($empr->empr_year   ,ENT_QUOTES, $charset),   $content_form);
	else $content_form = str_replace("!!year!!", "", $content_form);
	if (!$empr->empr_lang) $empr->empr_lang=$lang;
	$content_form = str_replace('!!combo_empr_lang!!', make_empr_lang_combo($empr->empr_lang), $content_form);
	
	if (!$duplicate_empr_from_id) {
		$content_form = str_replace('!!empr_login!!', $empr->empr_login, $content_form);
		$content_form = str_replace("!!empr_msg!!",      htmlentities($empr->empr_msg   ,ENT_QUOTES, $charset),   $content_form);
	} else {
		$content_form = str_replace('!!empr_login!!', "", $content_form);
		$content_form = str_replace("!!empr_msg!!", "",   $content_form);
	}
	
	// on récupère le select catégorie
	$requete = "SELECT id_categ_empr, libelle, duree_adhesion FROM empr_categ ORDER BY libelle ";
	$res = pmb_mysql_query($requete);
	$nbr_lignes = pmb_mysql_num_rows($res);
	$categ_content='';
	for($i=0; $i < $nbr_lignes; $i++) {
		$row = pmb_mysql_fetch_row($res);
		$categ_content.= "<option value='$row[0]'";
		if($row[0] == $empr->empr_categ) $categ_content .= " selected='selected'";
		$categ_content .= ">$row[1]</option>";
	}
	$content_form = str_replace("!!categ!!", $categ_content, $content_form);
	
	// et le select code stat
	// on récupère le select cod stat
	$requete = "SELECT idcode, libelle FROM empr_codestat ORDER BY libelle ";
	$res = pmb_mysql_query($requete);
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
			$content_form = str_replace("sexe_select_1", 'selected', $content_form);
			break;
		case 2:
			$content_form = str_replace("sexe_select_2", 'selected', $content_form);
			break;
		default:
			$content_form = str_replace("sexe_select_0", 'selected', $content_form);
			break;
	}
	$content_form = preg_replace("/sexe_select_[0-2]/m", '', $content_form);
	$content_form = str_replace("!!cstat!!",      $cstat_content,   $content_form);
	
	$content_form = str_replace("!!groupe_ajout!!", get_groups_form($id), $content_form);
	
	// ldap MaxMan
	if ($empr->empr_ldap){
		$form_ldap="checked" ;
	}else{
		$form_ldap="";
	}
	//$content_form = str_replace('!!empr_password!!', $empr_password, $content_form);
	$content_form = str_replace("!!ldap!!",$form_ldap,$content_form);
	
	if ($pmb_lecteurs_localises) {
		$empr_content_form_location = "
			<div class='row'>
				<label for='form_empr_location' class='etiquette'>".$msg['empr_location']."</label>
			</div>
			<div class='row'>
				!!localisation!!
			</div>
		";
		if($pmb_form_empr_editables == 2) {
			$loc = "
				<div id='el0Child_8' class='row' movable='yes' title='".htmlentities($msg['empr_location'],ENT_QUOTES,$charset)."'>
					".$empr_content_form_location."
				</div>
			";
		} else {
			$loc = "
				<div class='colonne4' id='g2_r1_f0'  movable='yes' title='".htmlentities($msg['empr_location'],ENT_QUOTES,$charset)."'>
					".$empr_content_form_location."
				</div>
			";
		}
		
		//$loc = str_replace('!!localisation!!', docs_location::gen_combo_box_empr($empr->empr_location, 0), $loc);
		$loc = str_replace('!!localisation!!', docs_location::get_html_select(array($empr->empr_location),array(),array('id'=>'empr_location_id','name'=>'empr_location_id')), $loc);
	} else {
		$loc = "<input type='hidden' name='empr_location_id' id='empr_location_id' value='".$empr->empr_location."'>" ;
		$content_form = str_replace('<!-- !!localisation!! -->', $loc, $content_form);
	}
	$content_form = str_replace('<!-- !!localisation!! -->', $loc, $content_form);
	
	if($pmb_opac_view_activate ){
		if($pmb_form_empr_editables == 2) {
			$opac_view_tpl = "
			<div id='el0Child_12' class='row' movable='yes' title='".htmlentities($msg['empr_form_opac_view'],ENT_QUOTES,$charset)."'>
					!!opac_view!!
			</div>";
		} else {
			$opac_view_tpl = "
			<div class='row' id='g4_r1_f0' movable='yes' title='".htmlentities($msg['empr_form_opac_view'],ENT_QUOTES,$charset)."'>
					!!opac_view!!
			</div>";
		}
		$opac_view = new opac_view(0,$id);
		$opac_view_tpl=str_replace("!!opac_view!!",gen_plus("opac_view",$msg["empr_form_opac_view"],$opac_view->do_sel_list(),0),$opac_view_tpl);
	} else {
		$opac_view_tpl = "";
	}
	$content_form = str_replace('<!-- !!opac_view!! -->', $opac_view_tpl, $content_form);
	//Champs persos
	$p_perso=new parametres_perso("empr");
	$perso_=$p_perso->show_editable_fields($id);
	if (isset($perso_["FIELDS"]) && count($perso_["FIELDS"])) $perso = "<div class='row'></div>" ;
	else $perso="";
	if(isset($perso_["FIELDS"])) {
		for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
			if(($i == count($perso_["FIELDS"])-1) && ($i%2 == 0)) $element_class = 'row';
			else $element_class = 'colonne2';
			$p=$perso_["FIELDS"][$i];
			if($pmb_form_empr_editables == 2) {
				$perso.="<div class='".$element_class."' id='el14Child_".$p["ID"]."' movable='yes' title='".htmlentities($p['TITRE'],ENT_QUOTES,$charset)."' >";
			} else {
				$perso.="<div class='".$element_class."' id='g6_r0_f".$p["ID"]."' movable='yes' title='".htmlentities($p['TITRE'],ENT_QUOTES,$charset)."' >";
			}
			$perso.="<div class='row'><label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."</div>\n";
			$perso.="<div class='row'>";
			$perso.=$p["AFF"]."</div>";
			$perso.="</div>";
		}
	}
	$perso.=$perso_["CHECK_SCRIPTS"];
	$content_form=str_replace("!!champs_perso!!",$perso,$content_form);
	
	$content_form = str_replace('!!empr_notice_override!!',get_rights_form($id),$content_form);
	
	if ($duplicate_empr_from_id) {
		$content_form = str_replace("!!id!!", 0, $content_form);
	} else {
		$content_form = str_replace("!!id!!",   $id, $content_form);
	}
	
	return $content_form;
}

// affichage du form emprunteurs (gere modif et creation).
function show_empr_form($form_action, $form_cancel, $id, $cb,$duplicate_empr_from_id="") {
	global $empr_form_password_constraints;
	global $msg, $base_path;
// 	global $aff_list_empr;
	global $deflt2docs_location;
	global $database_window_title ;
	global $lang;
	
	// si $id est fourni, il s'agit d'une modification. on recupere les donnees dans $link
	if($id) {
		// modification
		echo window_title($database_window_title.$msg[55]);
		$requete = "SELECT * FROM empr WHERE id_empr='$id' ";
		$res = pmb_mysql_query($requete);
		if($res) {
			$empr = pmb_mysql_fetch_object($res);
		} else {
			error_message( $msg[53], $msg[54], 0);
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
	}
	
	if (is_object($empr) && $empr->empr_cb) { //Si il y a un code lecteur
		$date_adhesion = (!$duplicate_empr_from_id ? $empr->empr_date_adhesion : date('Y-m-d'));
			
		if ($duplicate_empr_from_id) {
			/* AJOUTER ICI LE CALCUL EN FONCTION DE LA CATEGORIE */
			$rqt_empr_categ = "select duree_adhesion from empr_categ where id_categ_empr = ".$empr->empr_categ;
			$res_empr_categ = pmb_mysql_query($rqt_empr_categ);
			$empr_categ = pmb_mysql_fetch_object($res_empr_categ);
			//$form_adhesion=preg_replace('/-/', '', $form_adhesion);
			
			$rqt_date = "select date_add('".$date_adhesion."', INTERVAL $empr_categ->duree_adhesion DAY) as date_expiration " ;
			$resultatdate=pmb_mysql_query($rqt_date);
			$resdate=pmb_mysql_fetch_object($resultatdate);
			$empr->empr_date_expiration = $resdate->date_expiration;
		}
	} else { // création de lecteur
		$empr->empr_date_adhesion = today() ;
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

// 	$empr_form = str_replace('!!empr_password!!', '', $empr_form);
	
	if (!$empr->empr_location) $empr->empr_location=$deflt2docs_location ;

	
	$interface_form = new interface_entity_reader_form('empr_form');
	if($id) {
		$interface_form->set_label($empr->empr_nom." ".$empr->empr_prenom);
	}
	
	$interface_form->set_object_id(($duplicate_empr_from_id ? 0 : $id))
	->set_cb($empr->empr_cb)
	->set_num_statut($empr->empr_statut)
	->set_content_form(get_empr_content_form($empr, $id, $cb, $duplicate_empr_from_id))
	->set_table_name('empr')
	->set_field_focus('form_nom')
	->set_url_base($base_path.'/circ.php');
	print $interface_form->get_display();

	//regles de saisie de mot de passe
	$enabled_password_rules = emprunteur::get_json_enabled_password_rules(0, $lang);
	$empr_form_password_constraints = str_replace('!!enabled_password_rules!!', $enabled_password_rules, $empr_form_password_constraints);
	if ($duplicate_empr_from_id) {
	    $empr_form_password_constraints = str_replace("!!id!!", 0, $empr_form_password_constraints);
	} else {
	    $empr_form_password_constraints = str_replace("!!id!!",   $id, $empr_form_password_constraints);
	}
	print pmb_bidi($empr_form_password_constraints);
}

//creation formulaire surcharge des droits d'accès emprunteurs-notices
function get_rights_form($empr_id=0) {
	global $charset;
	global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum;
	global $gestion_acces_empr_contribution_area, $gestion_acces_empr_contribution_scenario;
	global $gestion_acces_contribution_moderator_empr;
	global $gestion_acces_empr_cms_section, $gestion_acces_empr_cms_article;
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
            6 => $gestion_acces_contribution_moderator_empr,
            7 => $gestion_acces_empr_cms_section,
            8 => $gestion_acces_empr_cms_article,
		);
		
		foreach ($acces_list as $index => $acces_active) {
			if ($acces_active == 1) {
				$dom = $ac->setDomain($index);
				
				//Role utilisateur
				$cur_usr_prf=$dom->getUserProfile($empr_id);
				if (!is_array($cur_usr_prf)) {
				    $cur_usr_prf = [$cur_usr_prf];
				}
		
				//Recuperation des droits generiques du domaine pour avoir les droits utilisateurs globaux
				$global_rights = $dom->getDomainRights(0,0);
				
				//Recuperation profils ressources
				$t_r = array();
				$t_r[0] = $dom->getComment('res_prf_def_lib');	//profile ressource par defaut
				$q_r = $dom->loadUsedResourceProfiles();
				$r_r = pmb_mysql_query($q_r);
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
							<td style='width:25px;' ><input type='checkbox' id='chk_rights_".$index."_".$k."_".$k2."' name='chk_rights[".$index."][".$k."][".$k2."]' value='1' ";
							foreach ($cur_usr_prf as $prf) {
							    if ($t_rights[$prf][$k] & (pow(2,$k2-1))) {
    								$t_rows.= "checked='checked' ";
    							}
							}
							if(($global_rights & 512)==0) {
							    $t_rows.="disabled='disabled' /></td>
    							<td>".htmlentities($v2, ENT_QUOTES, $charset)."</td>
    							</tr>";
							} else {
							$t_rows.="/></td>
							<td><label for='chk_rights_".$index."_".$k."_".$k2."'>".htmlentities($v2, ENT_QUOTES, $charset)."</label></td>
							</tr>";
						}
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
	$empr_id = intval($empr_id);
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



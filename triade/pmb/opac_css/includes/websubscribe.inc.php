<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: websubscribe.inc.php,v 1.18 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) die("no access");

define('PBINSC_OK'		,    0);
define('PBINSC_MAIL'	,    1);
define('PBINSC_LOGIN'	,    2);
define('PBINSC_BDD'		,    3);
//define('PBINSC_MAIL'	,    4); //Error : Already defined
define('PBINSC_INVALID'	,    5);
define('PBINSC_INCONNUE',    6);
define('PBINSC_CLE'		,    7);
define('PBINSC_PARAM'	,    8);

require_once($base_path."/includes/templates/websubscribe.tpl.php");
require_once($base_path."/includes/bannette_func.inc.php");
require_once("$class_path/emprunteur.class.php");

function generate_form_inscription() {
	global $subs_form_create, $msg ;
	global $f_nom, $f_prenom, $f_email, $f_login;
	global $f_msg, $f_adr1, $f_adr2, $f_cp, $f_ville, $f_pays, $f_tel1;
	
	$subs_form_create = str_replace ("!!f_nom!!",				stripslashes($f_nom),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_prenom!!",			stripslashes($f_prenom),					$subs_form_create);
	$subs_form_create = str_replace ("!!f_email!!",				stripslashes($f_email),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_login!!",				stripslashes($f_login),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_password!!",			"",											$subs_form_create);
	$subs_form_create = str_replace ("!!f_passwordv!!",			"",											$subs_form_create);
	$subs_form_create = str_replace ("!!f_adr1!!",				stripslashes($f_adr1),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_adr2!!",				stripslashes($f_adr2),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_cp!!",				stripslashes($f_cp),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_ville!!",				stripslashes($f_ville),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_pays!!",				stripslashes($f_pays),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_tel1!!",				stripslashes($f_tel1),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_msg!!",				stripslashes($f_msg),						$subs_form_create);
	$subs_form_create = str_replace ("!!f_loc!!",				docs_location::gen_combo_box_empr ("", 0),	$subs_form_create);
	$subs_form_create = str_replace ("!!others_informations!!",	prepare_post_others_informations(),			$subs_form_create);
	return $subs_form_create;
}

function test_form_fields($fields) {
    $ok = true;
    foreach ($fields as $field_name) {
        global ${$field_name};
        if (${$field_name} != strip_tags(${$field_name})) {
        	${$field_name} = '';
            $ok = false;
        }
    }
    return $ok;
}

function verif_validite_compte() {
	global $dbh, $msg, $opac_default_lang ;
	global $f_nom, $f_prenom, $f_email, $f_login, $f_password ;
	global $f_msg, $f_adr1, $f_adr2, $f_cp, $f_ville, $f_pays, $f_tel1;
	global $f_consent_message;
	global $base_path, $opac_websubscribe_num_carte_auto;
	global $opac_websubscribe_show,$lvl;
	
	$ret=array();
	if (!isset($f_consent_message) || !$f_consent_message) {
		$ret[0] = PBINSC_INVALID;
		$ret[1] = $msg['subs_form_consent_message_mandatory'] . generate_form_inscription();
		return $ret ;
	}
	if (!test_form_fields(['f_nom', 'f_prenom', 'f_email', 'f_login', 'f_password', 'f_msg', 'f_adr1', 'f_adr2', 'f_cp', 'f_ville', 'f_pays', 'f_tel1'])) {
	    $ret[0] = PBINSC_INVALID;
	    $ret[1] = $msg['subs_pb_tags'] . generate_form_inscription();
	    return $ret ;
	}
	$rqt = "select id_empr from empr where empr_mail like '%".$f_email."%' ";
	$res = pmb_mysql_query($rqt,$dbh);
	if (pmb_mysql_num_rows($res)>0) {
		$ret[0]=PBINSC_MAIL;
		$ret[1]=str_replace("!!email!!",urlencode($f_email),$msg['subs_pb_email']);
		return $ret ;
	}

	$rqt = "select id_empr from empr where empr_login ='".$f_login."' ";
	$res = pmb_mysql_query($rqt,$dbh);
	if (pmb_mysql_num_rows($res)>0) {
		$ret[0]=PBINSC_LOGIN;
		$ret[1]=str_replace("!!f_login!!",$f_login,$msg['subs_pb_login']).generate_form_inscription();
		return $ret ;
	}

	//Mise en conformité de l'identifiant
	$converted_login = convert_diacrit(pmb_strtolower($f_login)) ;
	$converted_login = pmb_alphabetic('^a-z0-9\.\_\-\@', '', $converted_login);
	if ($converted_login != $f_login) {
		$f_login = $converted_login;
		$ret[0]=PBINSC_LOGIN;
		$ret[1]=str_replace("!!f_login!!",$f_login,$msg['subs_pb_invalid_login']).generate_form_inscription();
		return $ret ;
	}
	
	// préparation des données:
	// langue:
	if ($_COOKIE['PhpMyBibli-LANG']) $lang=$_COOKIE['PhpMyBibli-LANG'];
	if (!$lang) {
		if ($opac_default_lang) $lang = $opac_default_lang;
		else $lang = "fr_FR";
	}
	
	// paramétrage :
	global $opac_websubscribe_empr_status, $opac_websubscribe_empr_categ, $opac_websubscribe_empr_stat, $opac_websubscribe_valid_limit ;
	$opac_websubscribe_empr_status_array=explode(",",$opac_websubscribe_empr_status);
	
	if (!$opac_websubscribe_empr_categ) {
		$ret[0]=PBINSC_PARAM;
		$ret[1]=$msg['subs_pb_empr_categ'];
		return $ret;
	}
	if (!$opac_websubscribe_empr_stat) {
		$ret[0]=PBINSC_PARAM;
		$ret[1]=$msg['subs_pb_empr_codestat'];
		return $ret;
	}

	// codes-barres emprunteur bidon :
	$pe_emprcb='wwwtmp'.rand(0,100000);
	// durée d'adhésion de la categ web
	$rqt="select duree_adhesion from empr_categ where id_categ_empr='".$opac_websubscribe_empr_categ."' ";
	$res = pmb_mysql_query($rqt,$dbh);
	$obj=pmb_mysql_fetch_object($res);
	$duree_adhesion=$obj->duree_adhesion;
	if(!$duree_adhesion) {
		$duree_adhesion = 365; //Valeur choisie par défaut pour éviter tout problème de paramétrage
	}
	
	global $pmb_lecteurs_localises,$opac_websubscribe_show_location;
	global $opac_websubscribe_empr_location;
	if ($pmb_lecteurs_localises && $opac_websubscribe_show_location) {
		global $empr_location_id;
		$websubscribe_empr_location = ($empr_location_id ? $empr_location_id : $opac_websubscribe_empr_location);
	} else {
		$websubscribe_empr_location = $opac_websubscribe_empr_location;
	}
	// clé de validation :
	$alphanum  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	$cle_validation = substr(str_shuffle($alphanum), 0, 20);
	
	$subscription_action = get_others_informations_from_globals();
	
	$rqt = "insert into empr set "; 
	$rqt.= "id_empr=0, "; 
	$rqt.= "empr_cb ='".$pe_emprcb."', "; 
	$rqt.= "empr_login ='".$f_login."', "; 
	$rqt.= "empr_mail='".$f_email."', "; 
	$rqt.= "empr_nom='".$f_nom."', ";
	$rqt.= "empr_prenom='".$f_prenom."', ";
	$rqt.= "empr_password='".$f_password."', ";
	$rqt.= "empr_creation=sysdate(), ";
	$rqt.= "empr_modif=sysdate(), ";
	$rqt.= "empr_date_adhesion=sysdate(), ";
	$rqt.= "empr_date_expiration=date_add(sysdate(), INTERVAL $duree_adhesion DAY), ";
	$rqt.= "empr_lang='".$lang."', ";
	$rqt.= "empr_statut='".$opac_websubscribe_empr_status_array[0]."', ";
	$rqt.= "empr_location='".$websubscribe_empr_location."', ";
	$rqt.= "empr_categ='".$opac_websubscribe_empr_categ."', ";
	$rqt.= "empr_codestat='".$opac_websubscribe_empr_stat."', ";
	$rqt.= "empr_msg='".$f_msg."', ";
	$rqt.= "empr_adr1='".$f_adr1."', ";
	$rqt.= "empr_adr2='".$f_adr2."', ";
	$rqt.= "empr_cp='".$f_cp."', ";
	$rqt.= "empr_ville='".$f_ville."', ";
	$rqt.= "empr_pays='".$f_pays."', ";
	$rqt.= "empr_tel1='".$f_tel1."', ";
	$rqt.= "cle_validation='".$cle_validation."' ";
	if(count($subscription_action)){
		$rqt.=",empr_subscription_action = '".addslashes(serialize($subscription_action))."'";
	}else{
		$rqt.=",empr_subscription_action = '".addslashes(serialize(array()))."'";
	}
	
	$res = pmb_mysql_query($rqt,$dbh) or die (pmb_mysql_error()."<br /><br />$rqt");
	$id_empr = pmb_mysql_insert_id();
	
	emprunteur::update_digest($f_login,$f_password);
	emprunteur::hash_password($f_login,$f_password);
	
	if ($id_empr) {
		//redefine empr.empr_cb   
		$pe_emprcb='www'.$id_empr;
		
		$opac_websubscribe_num_carte_auto_array=array();
		$opac_websubscribe_num_carte_auto_array=explode(",",$opac_websubscribe_num_carte_auto);		
		if ($opac_websubscribe_num_carte_auto_array[0] == "2" ) {
		
			$long_prefixe = $opac_websubscribe_num_carte_auto_array[1];
			$nb_chiffres = $opac_websubscribe_num_carte_auto_array[2];
			$prefix = $opac_websubscribe_num_carte_auto_array[3];
		
			$rqt =  "SELECT CAST(SUBSTRING(empr_cb,".($long_prefixe+1).") AS UNSIGNED) AS max_cb, SUBSTRING(empr_cb,1,".($long_prefixe*1).") AS prefixdb FROM empr ORDER BY max_cb DESC limit 0,1" ; // modif f cerovetti pour sortir dernier code barre tri par ASCII
			$res = pmb_mysql_query($rqt, $dbh);
			$cb_initial = pmb_mysql_fetch_object($res);
			$pe_emprcb = ($cb_initial->max_cb*1)+1;
			if (!$nb_chiffres) $nb_chiffres=strlen($pe_emprcb);
			if (!$prefix) $prefix = $cb_initial->prefixdb;
		
			$pe_emprcb = $prefix.substr((string)str_pad($pe_emprcb, $nb_chiffres, "0", STR_PAD_LEFT),-$nb_chiffres);
		
		} elseif ($opac_websubscribe_num_carte_auto_array[0] == '3' ) {
		
			$num_carte_auto_filename = $base_path.'/circ/empr/'.trim($opac_websubscribe_num_carte_auto_array[1]).'.inc.php';
			$num_carte_auto_fctname = trim($opac_websubscribe_num_carte_auto_array[1]);
			if (file_exists($num_carte_auto_filename)){
				require_once($num_carte_auto_filename);
				if(function_exists($num_carte_auto_fctname)) {
					$pe_emprcb = $num_carte_auto_fctname();
				}
			}
		}
		$rqt = "UPDATE empr SET empr_cb='$pe_emprcb' WHERE id_empr='$id_empr'";
		$res = pmb_mysql_query($rqt, $dbh) or die (pmb_mysql_error()."<br /><br />$rqt");

		// envoyer le mail de demande de confirmation
		global $opac_biblio_name,$opac_biblio_email,$opac_url_base ;
		$obj = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_mail_obj']) ;
		$corps = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_mail_corps']) ;
		$corps = str_replace("!!empr_first_name!!",$f_prenom,$corps) ;
		$corps = str_replace("!!empr_last_name!!",$f_nom,$corps) ;
		$lien_validation = "<a href='".$opac_url_base."subscribe.php?subsact=validation&login=".urlencode($f_login)."&cle_validation=$cle_validation'>".$opac_url_base."subscribe.php?subsact=validation&login=$f_login&cle_validation=$cle_validation</a>";
		$corps = str_replace("!!lien_validation!!",$lien_validation,$corps) ;
		
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";

		$res_envoi=@mailpmb(trim(stripslashes($f_prenom." ".$f_nom)), stripslashes($f_email),$obj,$corps,$opac_biblio_name, $opac_biblio_email, $headers);
		if (!$res_envoi) {
			$ret[0]=PBINSC_MAIL;
			$ret[1]=str_replace("!!f_email!!",$f_email,$msg['subs_pb_mail']);
			return $ret ;
		}
		$ret[0]=PBINSC_OK;
		$ret[1]=str_replace("!!f_email!!",$f_email,$msg['subs_ok_inscrit']);
		$ret[1]=str_replace("!!nb_h_valid!!",$opac_websubscribe_valid_limit,$ret[1]);
		
		//alerte pour les utilisateurs
		$query_users = "select nom, prenom, user_email from users where user_email like('%@%') and user_alert_subscribemail=1";
		$result_users = @pmb_mysql_query($query_users, $dbh);
		if ($result_users) {
			if (pmb_mysql_num_rows($result_users) > 0) {
				global $pmb_url_base;
				$obj = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_alert_user_mail_obj']) ;
				$obj = str_replace("!!empr_name!!", stripslashes($f_nom),$obj);
				$obj = str_replace("!!empr_first_name!!", stripslashes($f_prenom),$obj);
				$corps = str_replace("!!biblio_name!!",$opac_biblio_name,$msg['subs_alert_user_mail_corps']) ;
				$corps = str_replace("!!empr_name!!", stripslashes($f_nom),$corps);
				$corps = str_replace("!!empr_first_name!!", stripslashes($f_prenom),$corps);
				$empr_link = str_replace("!!pmb_url_base!!",$pmb_url_base,$msg['subs_alert_user_mail_empr_link']) ;
				$empr_link = str_replace("!!empr_cb!!",$pe_emprcb,$empr_link);
				$corps = str_replace("!!empr_link!!", $empr_link,$corps);
				while ($user=@pmb_mysql_fetch_object($result_users)) {
					@mailpmb(trim($user->prenom." ".$user->nom), $user->user_email,$obj,$corps,$opac_biblio_name, $opac_biblio_email, $headers);
				}
			}
		}

		return $ret ;
				
	} else {
		$ret[0]=PBINSC_BDD;
		$ret[1]=$msg['subs_pb_bdd'];
		return $ret ;
	}

}

function verif_validation_compte() {
	global $dbh, $msg;
	global $login, $cle_validation, $form_access_compte ;
	global $opac_websubscribe_empr_status, $opac_websubscribe_valid_limit  ;
	$opac_websubscribe_empr_status_array=explode(",",$opac_websubscribe_empr_status);

	$ret=array();

	$rqt = "select id_empr, if(date_add(empr_creation, INTERVAL $opac_websubscribe_valid_limit HOUR)>=sysdate(),1,0) as not_depasse, empr_password, cle_validation, empr_subscription_action from empr where empr_login ='".$login."' and empr_statut='".$opac_websubscribe_empr_status_array[0]."' "; 
	$res = pmb_mysql_query($rqt,$dbh) or die (pmb_mysql_error()."<br /><br />$rqt");
	if (pmb_mysql_num_rows($res)>0) {
		// trouvé !
		$obj=pmb_mysql_fetch_object($res);
		if ($obj->not_depasse) {
			// validation pas dépassée
			if ($obj->cle_validation==$cle_validation) {
				$subscription_action = unserialize($obj->empr_subscription_action);
				$suite = get_html_subscription_action($subscription_action);
				$rqt = "update empr set cle_validation='', empr_subscription_action= '', empr_statut='".$opac_websubscribe_empr_status_array[1]."' where empr_login='".$login."' ";
				$res = pmb_mysql_query($rqt,$dbh) or die (pmb_mysql_error()."<br /><br />$rqt");
				$ret[0]=PBINSC_OK;				
				if($suite){
					//on connecte avec une mini feinte...
					global $emprlogin;
					$emprlogin = $login;
					global $encrypted_password;
					$encrypted_password = $obj->empr_password;
					$log_ok = connexion_empr();
					if ($log_ok){
						$ret[1] = str_replace("!!form_access_compte!!",$suite,$msg['subs_ok_validation']);
					}else{
						$form_access_compte=str_replace("!!login!!",$login,$form_access_compte) ;
						$form_access_compte=str_replace("!!encrypted_password!!",$obj->empr_password,$form_access_compte) ;
						$ret[1] = str_replace("!!form_access_compte!!",$form_access_compte,$msg['subs_ok_validation']) ;
					}
				}else{
					$form_access_compte=str_replace("!!login!!",$login,$form_access_compte) ;
					$form_access_compte=str_replace("!!encrypted_password!!",$obj->empr_password,$form_access_compte) ;
					$ret[1] = str_replace("!!form_access_compte!!",$form_access_compte,$msg['subs_ok_validation']) ;
				}
				return $ret ;
			} else {
				// login Ok mais clé pas valide
				$rqt = "delete from empr where empr_login='".$login."' ";
				$res = pmb_mysql_query($rqt,$dbh) or die (pmb_mysql_error()."<br /><br />$rqt");
				$ret[0]=PBINSC_CLE;
				$ret[1]=$msg['subs_pb_cle'];
				return $ret ;
			}
		} else {
			// dépassée
			$rqt = "delete from empr where empr_login='".$login."' ";
			$res = pmb_mysql_query($rqt,$dbh) or die (pmb_mysql_error()."<br /><br />$rqt");
			$ret[0]=PBINSC_INVALID;
			$ret[1]=$msg['subs_pb_invalid'];
			return $ret ;
		}			
	}
	// n'existe même pas !
	$ret[0]=PBINSC_INCONNUE;
	$ret[1] = str_replace("!!login!!",$login,$msg['subs_pb_inconnue']) ;
	return $ret ;
}

function get_others_informations_from_globals(){
	global $lvl;
	$subscription_action = array();
	if($lvl){
		$subscription_action['lvl'] = $lvl;
		switch($lvl){
			case "resa" :
				global $id_notice,$id_bulletin;
				$subscription_action['id_notice'] = $id_notice;
				$subscription_action['id_bulletin'] = $id_bulletin;
				break;
			case "bannette_gerer" :
				global $tab,$enregistrer,$bannette_abon;
				$subscription_action['tab'] = $tab;
				$subscription_action['enregistrer'] = $enregistrer;
				$subscription_action['bannette_abon'] = $bannette_abon;
		}	
	}
	return $subscription_action;
}

function prepare_post_others_informations(){
	global $opac_websubscribe_show,$lvl;
	$others_informations = "";
	if($opac_websubscribe_show == 2 && $lvl){
		$others_informations.= "
			<input type='hidden' name='lvl' value='".$lvl."' />";
		switch($lvl){
			case "resa" :
				global $id_notice,$id_bulletin;
				$others_informations.= "
				<input type='hidden' name='id_notice' value='".($id_notice*1)."' />
				<input type='hidden' name='id_bulletin' value='".($id_bulletin*1)."' />";
				break;
			case "resa_cart" :
				break;
			case "bannette_gerer" :
				global $bannette_abon;
				$others_informations.= "
				<input type='hidden' name='enregistrer' value='PUB'/>
				<input type='hidden' name='tab' value='dsi'/>
				<input type='hidden' name='new_connexion' value='1'/>";
				if(is_array($bannette_abon)){
					foreach($bannette_abon as $id=>$value){
						$others_informations.= "
						<input type='hidden' name='bannette_abon[".$id."]' value='1'/>";
					}
				}
				break;
		}
	}
	return $others_informations;
}

function get_html_subscription_action($others_informations){
	global $opac_websubscribe_show;
	global $msg;
	
	$html = "";
	if($opac_websubscribe_show == 2){
		
		switch($others_informations['lvl']){
			case "resa" :
				$html="
				<div>
					<h3>".$msg['websubscribe_resa_action']."</h3>
					<div class='row'>&nbsp;</div> 
					".aff_notice($others_informations['id_notice'],1,1,0,"",0,0,1)."
				</div>";
				break;
			case "bannette_gerer" :
				$id_bannette =0;
				foreach($others_informations['bannette_abon'] as $id=>$v){
					$id_bannette = $id;
				}
				$html = "
				<div>
					<h3>".$msg['websubscribe_bannette_action']."</h3>
				<div class='row'>&nbsp;</div>
				".affiche_public_bannette($id_bannette)."
				</div>";
				break;
		}
	}
	return $html;
	
}

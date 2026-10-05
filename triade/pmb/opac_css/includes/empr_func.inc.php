<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_func.inc.php,v 1.80.2.2.2.1 2025/05/20 14:00:07 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $class_path;

require_once $class_path . "/password/password.class.php";
require_once $class_path . "/emprunteur.class.php";

use Pmb\Security\Library\Auth;

function connexion_empr() {


	global $msg, $opac_duration_session_auth;
	global $time_expired, $erreur_session, $login, $password, $encrypted_password ;
	global $auth_ok, $lang, $code, $emprlogin;
	global $password_key;
	global $first_log, $auth_ok_need_refresh_page;
	global $erreur_connexion;
	global $opac_opac_view_activate, $pmb_opac_view_class, $opac_view_class, $opac_view;
	global $opac_default_style;
	//a positionner si authentification exterieure
	global $ext_auth,$empty_pwd;
	global $base_path,$class_path;
	global $cms_build_activate;
	//a positionner si les vues OPAC sont activées
	global $include_path, $javascript_path;
	global $opac_integrate_anonymous_cart;
	global $allow_opac;
	global $security_mfa_active, $mfa_code, $sub;

	$auth_ok_need_refresh_page=false;
	$erreur_connexion = CONNEXION_ERROR_NONE;

	$auth_instance = Auth::getInstance();
	if ($auth_instance->isInBlackList()) {
	    $erreur_connexion = CONNEXION_ERROR_FORBIDDEN_IP;
	    return 0;
	}

	$log_ok=0;
	if (!$_SESSION["user_code"]) {

        $p_login= (isset($_POST['login']) ? addslashes($_POST['login']) : '');

		$auth_instance = Auth::getInstance($p_login);
	    if (!$auth_instance->isAuthorized() ) {
	        $erreur_connexion = CONNEXION_ERROR_TOO_MANY_ATTEMPTS;
			return 0;
	    }

		if ($time_expired==0) {

			//Si pas de session en cours, vérification du login
			$verif_query = "SELECT id_empr, empr_cb, empr_nom, empr_prenom, empr_password, empr_lang, empr_sms, empr_tel1, empr_mail, empr_date_expiration<sysdate() as isexp, empr_login, empr_ldap,empr_location, cle_validation, allow_opac
					FROM empr
					JOIN empr_statut ON empr_statut=idstatut
					WHERE empr_login='".($emprlogin ? $emprlogin :$p_login)."'";
			$verif_result = pmb_mysql_query($verif_query);
			if(pmb_mysql_num_rows($verif_result)) {
				// récupération des valeurs MySQL du lecteur et injection dans les variables
				while ($verif_line = pmb_mysql_fetch_array($verif_result)) {
					$verif_empr_cb = $verif_line['empr_cb'];
					$verif_empr_login = $verif_line['empr_login'];
					$verif_empr_ldap = $verif_line['empr_ldap'];
					$verif_empr_password = $verif_line['empr_password'];
					$verif_lang = ($verif_line['empr_lang']?$verif_line['empr_lang']:"fr_FR");
					$verif_id_empr = $verif_line['id_empr'];
					$verif_isexp = $verif_line['isexp'];
					$verif_opac = $verif_line['allow_opac'];
					$empr_location = $verif_line['empr_location'];
					$empr_sms = $verif_line['empr_sms'];
					$empr_mail = $verif_line['empr_mail'];
					$empr_tel1 = $verif_line['empr_tel1'];
				}
			} else {
				$verif_empr_cb = '';
				$verif_empr_login = '';
				$verif_empr_ldap = '';
				$verif_empr_password = '';
				$verif_lang = '';
				$verif_id_empr = 0;
				$verif_isexp = '';
				$verif_opac = 0;
				$empr_location = 0;
			}

			$auth_ok = false;
			$passwords_match = true;
			$auth_mfa = false;
			if ($verif_opac) {

				$hash_format = password::get_hash_format($verif_empr_password);

				switch(true) {

					case ($ext_auth) :
						$auth_ok = true;
						break;

					case ($code) :
						$auth_ok = connexion_auto();
						break;

					case ($password_key) :
						$auth_ok = connexion_unique();
						//Si la connexion échoue, c'est que le lien n'est plus valide donc on s'en va
						if(!$auth_ok) {
						    $erreur_connexion = CONNEXION_ERROR_INVALID_LINK;
							return 0;
						}
						break;

					case ($verif_empr_ldap) :  // auth by server ldap
						$auth_ok=auth_ldap($p_login,$password);
						break;

					case ( '' == $verif_empr_login) :
						break;

					case ( 'undefined' == $hash_format ) : //ancien format de mots de passe
						//Retrait temporaire de la verif, a remettre apres la correction
						//du souci de token csrf perdu apres une session
						// verify_csrf();
						if( ($empty_pwd || ($verif_empr_password !== password::gen_previous_hash('', $verif_id_empr))) &&  ($verif_empr_password == password::gen_previous_hash($password, $verif_id_empr)) ) {
							$auth_ok = true;
							$auth_mfa = true;
						} else {
							$passwords_match = false;
						}
						break;

					case ( ('bcrypt' == $hash_format) && empty($encrypted_password) ) : //nouveau format de mots de passe
						// verify_csrf();
					    if (
					    ($empty_pwd || (!is_null($verif_empr_password) && false === password::verify_hash('', $verif_empr_password))) &&
					    (!is_null($password) && !is_null($verif_empr_password) && true === password::verify_hash($password, $verif_empr_password))
					    ) {
					        $auth_ok = true;
					        $auth_mfa = true;
					    } else {
					        $passwords_match = false;
					    }
						break;

					case ( ('bcrypt' == $hash_format) && !empty($encrypted_password) ) : //nouveau format de mots de passe
						// verify_csrf();
						if( ($empty_pwd || (false === password::verify_hash('', $verif_empr_password))) && (true === password::compare_hashes($encrypted_password, $verif_empr_password)) ) {
							$auth_ok = true;
							$auth_mfa = true;
						} else {
							$passwords_match = false;
						}
						break;
				}

			}

			//enregistrement acces
			if ($auth_ok) {
                $auth_instance->logAttempt(true);
            }

            // L'authentification a reussi
            if ($auth_ok) {

                // Le mode mfa est actif
                if ($security_mfa_active && $auth_mfa) {
					$secret_code = emprunteur::get_mfa_secret_code_empr($verif_id_empr);
					$mfa_service = (new Pmb\MFA\Controller\MFAServicesController())->getData('OPAC');
					if(!empty($secret_code)) {
						if($mfa_service->application && empty($mfa_code)) {
							// On contourne les aiguilleurs ajax, voir ajax_authentification
							switch($sub) {
								case "send_mail":
								case "send_sms":
								case "check_totp":
									return $verif_id_empr;
							}

							$automatic_send_code = "";
							$favorite = emprunteur::get_mfa_favorite_empr($verif_id_empr);

							if(!empty($favorite) && $favorite != "app") {
								if(($favorite == "sms" && !empty($empr_sms) && !empty($empr_tel1)) || ($favorite == "mail" && !empty($empr_mail))) {
									$automatic_send_code = $favorite;
								}
							}

							print "
								<script src='" . $javascript_path . '/mfa.js' . "'></script>
								<script>
									show_mfa_frame(`" . addslashes(emprunteur_display::get_display_mfa($verif_id_empr, $mfa_service->sms)) . "`, `" . $automatic_send_code . "`);
								</script>
							";

							$erreur_connexion = CONNEXION_ERROR_WAITING_FOR_OTP_CODE;
							return 0;
						} else if(!empty($mfa_code)) {
							$mfa_otp = (new Pmb\MFA\Controller\MFAOtpController())->getData("OPAC");

							$mfa_totp = new mfa_totp();
							$mfa_totp->set_hash_method($mfa_otp->hashMethod);
							$mfa_totp->set_life_time($mfa_otp->lifetime);
							$mfa_totp->set_length_code($mfa_otp->lengthCode);

							if(!$mfa_totp->check_totp(base32_upper_decode($secret_code), $mfa_code, 2)) {
							    $erreur_connexion = CONNEXION_ERROR_INVALID_OTP_CODE;
								unset($_POST['mfa_code']);
								return 0;
							}
						}
					} else if($mfa_service->required) {
						global $opac_show_login_form_next;

						$opac_show_login_form_next = "./empr.php?tab=mfa&lvl=mfa_initialization";
					}
				}

				$cart_anonymous = array();
				if($opac_integrate_anonymous_cart && isset($_SESSION['cart'])) {
					$cart_anonymous = $_SESSION['cart'];
				}
				//Si mot de passe correct, enregistrement dans la session de l'utilisateur
				startSession("PmbOpac", $verif_empr_login);

				$log_ok=1;
				$login=$verif_empr_login;//Dans le cas de la connexion automatique à l'Opac
				if (isset($_SESSION["cms_build_activate"]) && $_SESSION["cms_build_activate"]) {
				    $cms_build_activate=1;
				}
				if (isset($_SESSION["opac_view"])) {
				    $opac_view=$_SESSION["opac_view"];
				}
				if (isset($_SESSION["build_id_version"]) && $_SESSION["build_id_version"]) {
				    $build_id_version=$_SESSION["build_id_version"];
				} else {
				    $build_id_version='';
				}
				//Récupération de l'environnement précédent
				$requete="select session from opac_sessions where empr_id=".$verif_id_empr;
				$res_session=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($res_session)) {
					$temp_session=unserialize(pmb_mysql_result($res_session,0,0));
					$_SESSION=$temp_session;
				} else {
					$_SESSION=array();
				}
				$_SESSION["cms_build_activate"]=$cms_build_activate;
				$_SESSION["build_id_version"]=$build_id_version;
				if(!$code) {
				    $_SESSION["connexion_empr_auto"]=0;
				}
				$_SESSION["user_code"]=$verif_empr_login;
				$_SESSION["id_empr_session"]=$verif_id_empr;
				$_SESSION["connect_time"]=time();
				$_SESSION["lang"]=$verif_lang;
				$_SESSION["empr_location"]=$empr_location;
				$_SESSION["empr_location_libelle"]= translation::get_translated_text($_SESSION["empr_location"], "docs_location", "location_libelle");

				// change language and charset after login
				$lang=$_SESSION["lang"];
				set_language($lang);
				if(!$verif_isexp){
					recupere_pref_droits($_SESSION["user_code"]);
					$_SESSION["user_expired"] = $verif_isexp;
				} else {
					recupere_pref_droits($_SESSION["user_code"],1);
					$_SESSION["user_expired"] = $verif_isexp;
					if(!empty($msg["empr_expire"])) {
						echo "<script>alert(\"".$msg["empr_expire"]."\");</script>";
					}
					$erreur_connexion = CONNEXION_ERROR_EXPIRED_SUBSCRIPTION;
				}
				if($opac_opac_view_activate){
				    $current_opac_view = $opac_view;
    				$opac_view=$_SESSION["opac_view"]=0;
					$_SESSION['opac_view_query']=0;
					if(!$pmb_opac_view_class) {
					    $pmb_opac_view_class= "opac_view";
					}
					require_once($base_path."/classes/".$pmb_opac_view_class.".class.php");
					$opac_view_class= new $pmb_opac_view_class($_SESSION["opac_view"],$_SESSION["id_empr_session"]);
					if($opac_view_class->id){
						$opac_view_class->set_parameters();
						$opac_view_filter_class=$opac_view_class->opac_filters;
						$opac_view=$_SESSION["opac_view"]=$opac_view_class->id;
					 	if(!$opac_view_class->opac_view_wo_query) {
 							$_SESSION['opac_view_query']=1;
 						}
					}else {
					    $opac_view=$_SESSION["opac_view"]=0;
					}
					$css=$_SESSION["css"]=$opac_default_style;
					if($current_opac_view != $_SESSION["opac_view"]) {
					    $auth_ok_need_refresh_page=true;
					}
				}

				if(count($cart_anonymous)) {
					$_SESSION["cart_anonymous"] = $cart_anonymous;
				}

				if(!$code && !$password_key) {
					$first_log=true;
				}

				// Enregistrement en session de l'authentification externe
				if($ext_auth) {
				    $_SESSION['ext_auth'] = 1;
				} else {
				    $_SESSION['ext_auth'] = 0;
				}

			//  L'authentification a echoue
			} else {

				//On detruit la session creee
			    if (isset($_SESSION["cms_build_activate"]) && $_SESSION["cms_build_activate"]) {
			        $cms_build_activate = 1;
			    }
			    if (!empty($_SESSION["opac_view"])) {
				    $opac_view = $_SESSION["opac_view"];
				}
				if (isset($_SESSION["build_id_version"]) && $_SESSION["build_id_version"]) {
				    $build_id_version=$_SESSION["build_id_version"];
				}

				$ext_auth_context_id = (!empty($_SESSION["ext_auth_context_id"]) ? $_SESSION["ext_auth_context_id"] : 0);
				@session_destroy();
				if( $cms_build_activate || $opac_opac_view_activate || $ext_auth_context_id ) {
					session_start();
					if($cms_build_activate) {
					    $_SESSION["cms_build_activate"]=$cms_build_activate;
					    $_SESSION["build_id_version"]=$build_id_version;
					}
					if($opac_opac_view_activate) {
					    $_SESSION["opac_view"]=$opac_view;
					}
					if($ext_auth_context_id) {
					    $_SESSION["ext_auth_context_id"] = $ext_auth_context_id;
					}
				}

				switch(true) {

				    case ($code) :
				    case ($verif_empr_ldap) :
				    case ( '' == $verif_empr_login && ('' != $login)  && ('' != $password)) :
				        $erreur_session = (isset($empr_erreur_header) ? $empr_erreur_header : '');
				        $erreur_session .= $msg["empr_type_card_number"]."<br />";
				        $erreur_session .= (isset($empr_erreur_footer) ? $empr_erreur_footer : '');
				        $auth_instance->logAttempt(false);
				        $erreur_connexion = CONNEXION_ERROR_INVALID_CREDENTIALS;
				        break;

				    case ( !isset($_POST['login']) && !isset($_POST['password']) ) :
				        break;

				    case (!$passwords_match) :
				        $erreur_session = (isset($empr_erreur_header) ? $empr_erreur_header : '');
				        $erreur_session .= $msg["empr_type_card_number"]."<br />";
				        $erreur_session .= (isset($empr_erreur_footer) ? $empr_erreur_footer : '');
				        $auth_instance->logAttempt(false);
				        $erreur_connexion = CONNEXION_ERROR_INVALID_CREDENTIALS;
				        break;

				    case ('' != $verif_empr_login && $verif_isexp) :
				        //Si l'abonnement a expire
				        if(!empty($msg["empr_expire"])) {
				            echo "<script>alert(\"".$msg["empr_expire"]."\");</script>";
				        }
				        $erreur_connexion = CONNEXION_ERROR_EXPIRED_SUBSCRIPTION;
				        break;

				    case ('' != $verif_empr_login && !$verif_opac) :
				        //Si la connexion a l'opac est interdite
				        echo "<script>alert(\"".$msg["empr_connexion_interdite"]."\");</script>";
				        $erreur_connexion = CONNEXION_ERROR_FORBIDDEN_STATUS;
				        break;

				    default :
				        // Autre cas au cas où...
				        $erreur_session = (isset($empr_erreur_header) ? $empr_erreur_header : '');
				        $erreur_session .= $msg["empr_type_card_number"]."<br />";
				        $erreur_session .= (isset($empr_erreur_footer) ? $empr_erreur_footer : '');
				        $auth_instance->logAttempt(false);
				        $erreur_connexion = CONNEXION_ERROR_INVALID_CREDENTIALS;
				        break;
				}

				$log_ok=0 ;
				$time_expired = 0 ;
			}


		} elseif ($time_expired==1) {  // la session a expiré, on va le lui dire

		    echo "<script>alert(reverse_html_entities(\"".sprintf($msg["session_expired"],round($opac_duration_session_auth/60))."\"));</script>";

		} else { //session anonyme expirée, time_expired=2

		    echo "<script>alert(reverse_html_entities(\"".sprintf($msg["anonymous_session_expired"],round($opac_duration_session_auth/60))."\"));</script>";

		}

	} else {
		//Si session en cours, pas de problème...
		$log_ok=1;
		$login=$_SESSION["user_code"];
		if($_SESSION["user_expired"]){
			recupere_pref_droits($login,1);
		} else 	recupere_pref_droits($login);
		if(!$code)$_SESSION["connexion_empr_auto"]=0;
	}

	// pour visualiser une notice issue de DSI avec une connexion auto
	if(isset($_SESSION["connexion_empr_auto"]) && $_SESSION["connexion_empr_auto"] && $log_ok){
		global $connexion_empr_auto,$tab,$lvl;
		$connexion_empr_auto=1;
		if(!$code){
			if (!$tab) $tab="dsi";
			if (!$lvl) $lvl="bannette";
		}
	}

	if ($auth_ok && !$allow_opac) {
	    // cas de l'adhésion dépassée dont le statut associé (opac_adhesion_expired_status) interdit de se connecter
	    @session_destroy();
	    $erreur_connexion = CONNEXION_ERROR_FORBIDDEN_STATUS;
	    $log_ok = 0;
	}


	return $log_ok;

}

function recupere_pref_droits($login,$limitation_adhesion=0) {
	global $msg ;
	global $id_empr,
		$empr_cb,
		$empr_nom,
		$empr_prenom,
		$empr_adr1,
		$empr_adr2,
		$empr_cp,
		$empr_ville,
		$empr_mail,
		$empr_tel1,
		$empr_tel2,
		$empr_prof,
		$empr_year,
		$empr_categ,
		$empr_codestat,
		$empr_sexe,
		$empr_login,
		$empr_ldap,
		$empr_location,
		$empr_statut;

	global $allow_loan,
		$allow_loan_hist,
		$allow_book,
		$allow_opac,
		$allow_dsi,
		$allow_dsi_priv,
		$allow_sugg,
		$allow_dema,
		$allow_prol,
		$allow_avis,
		$allow_tag,
		$allow_pwd,
		$allow_liste_lecture,
		$allow_self_checkout,
		$allow_self_checkin,
		$allow_serialcirc,
		$allow_scan_request,
		$allow_contribution;
	global $opac_adhesion_expired_status;
	global $allow_pnb;

	if($limitation_adhesion && $opac_adhesion_expired_status){
		$req = "select * from empr_statut where idstatut='".$opac_adhesion_expired_status."'";
		$res = pmb_mysql_query($req);
		$data_expired = pmb_mysql_fetch_array($res);
		$droit_loan= $data_expired['allow_loan'];
		$droit_loan_hist= $data_expired['allow_loan_hist'];
		$droit_book= $data_expired['allow_book'];
		$droit_opac= $data_expired['allow_opac'];
		$droit_dsi= $data_expired['allow_dsi'];
		$droit_dsi_priv= $data_expired['allow_dsi_priv'];
		$droit_sugg= $data_expired['allow_sugg'];
		$droit_dema= $data_expired['allow_dema'];
		$droit_prol= $data_expired['allow_prol'];
		$droit_avis= $data_expired['allow_avis'];
		$droit_tag= $data_expired['allow_tag'];
		$droit_pwd= $data_expired['allow_pwd'];
		$droit_liste_lecture = $data_expired['allow_liste_lecture'];
		$droit_self_checkout = $data_expired['allow_self_checkout'];
		$droit_self_checkin = $data_expired['allow_self_checkin'];
		$droit_serialcirc = $data_expired['allow_serialcirc'];
		$droit_scan_request = $data_expired['allow_scan_request'];
		$droit_contribution = $data_expired['allow_contribution'];
		$droit_pnb = $data_expired['allow_pnb'];
	} else {
		$droit_loan= 1;
		$droit_loan_hist=1;
		$droit_book= 1;
		$droit_opac= 1;
		$droit_dsi= 1;
		$droit_dsi_priv=1;
		$droit_sugg= 1;
		$droit_dema= 1;
		$droit_prol= 1;
		$droit_avis=1 ;
		$droit_tag= 1;
		$droit_pwd= 1;
		$droit_liste_lecture = 1;
		$droit_self_checkout=1;
		$droit_self_checkin=1;
		$droit_serialcirc=1;
		$droit_scan_request=1;
		$droit_contribution=1;
		$droit_pnb = 1;
	}

	$query0 = "select * from empr, empr_statut where empr_login='".$login."' and idstatut=empr_statut ";
	$req0 = pmb_mysql_query($query0);
	$data = pmb_mysql_fetch_array($req0);
	$id_empr = $data['id_empr'];
	$empr_cb = $data['empr_cb'];
	$empr_nom = $data['empr_nom'];
	$empr_prenom= $data['empr_prenom'];
	$empr_adr1= $data['empr_adr1'];
	$empr_adr2= $data['empr_adr2'];
	$empr_cp= $data['empr_cp'];
	$empr_ville= $data['empr_ville'];
	$empr_mail= $data['empr_mail'];
	$empr_tel1= $data['empr_tel1'];
	$empr_tel2= $data['empr_tel2'];
	$empr_prof= $data['empr_prof'];
	$empr_year= $data['empr_year'];
	$empr_categ= $data['empr_categ'];
	$empr_codestat= $data['empr_codestat'];
	$empr_sexe= $data['empr_sexe'];
	$empr_login= $data['empr_login'];
	$empr_ldap= $data['empr_ldap'];
	$empr_location= $data['empr_location'];
	$empr_date_adhesion= $data['empr_date_adhesion'];
	$empr_date_expiration= $data['empr_date_expiration'];
	$empr_statut= $data['empr_statut'];

	// droits de l'utilisateur
	$allow_loan= $data['allow_loan'] & $droit_loan;
	$allow_loan_hist= $data['allow_loan_hist'] & $droit_loan_hist;
	$allow_book= $data['allow_book'] & $droit_book;
	$allow_opac= $data['allow_opac'] & $droit_opac;
	$allow_dsi= $data['allow_dsi'] & $droit_dsi;
	$allow_dsi_priv= $data['allow_dsi_priv'] & $droit_dsi_priv;
	$allow_sugg= $data['allow_sugg'] & $droit_sugg;
	$allow_dema= $data['allow_dema'] & $droit_dema;
	$allow_prol= $data['allow_prol'] & $droit_prol;
	$allow_avis= $data['allow_avis'] & $droit_avis;
	$allow_tag= $data['allow_tag'] & $droit_tag;
	$allow_pwd= $data['allow_pwd'] & $droit_pwd;
	$allow_liste_lecture = $data['allow_liste_lecture'] & $droit_liste_lecture;
	$allow_self_checkout= $data['allow_self_checkout'] & $droit_self_checkout;
	$allow_self_checkin= $data['allow_self_checkin'] & $droit_self_checkin;
	$allow_serialcirc= $data['allow_serialcirc'] & $droit_serialcirc;
	$allow_scan_request= $data['allow_scan_request'] & $droit_scan_request;
	$allow_contribution = $data['allow_contribution'] & $droit_contribution;
	$allow_pnb = $data['allow_pnb'] & $droit_pnb;
}

function connexion_auto_duration(){
	global $opac_connexion_auto_duration;
	global $date_conex;

	$log_ok=1;
	$opac_connexion_auto_duration = intval($opac_connexion_auto_duration);
	if($opac_connexion_auto_duration) {
		$diff = time() - $date_conex;
		$hours = $diff / 3600;
		if($hours > $opac_connexion_auto_duration) {
			$log_ok=0;
		}
	}
	return $log_ok;
}

function connexion_auto(){
	global $opac_connexion_phrase;
	global $date_conex,$emprlogin,$code;

	$log_ok=0;
	if(connexion_auto_duration() && $opac_connexion_phrase && ($code == md5($opac_connexion_phrase.$emprlogin.$date_conex))) {
		$log_ok = 1;
	}
	return $log_ok;
}

function connexion_unique(){
	global $emprlogin,$password_key;

	$log_ok=0;
	$query = "select cle_validation from empr where empr_login='".$emprlogin."'";
	$result = pmb_mysql_query($query);
	if ($result) {
		if (pmb_mysql_num_rows($result)) {
			if ($password_key == pmb_mysql_result($result, 0, "cle_validation")) {
				$log_ok = 1;
				$query = "update empr set cle_validation='' where empr_login='".$emprlogin."'";
				pmb_mysql_query($query);
			}
		}
	}
	return $log_ok;
}

function connexion_registration_confirmation($id) {
	$emprunteur = new emprunteur($id);
	$emprunteur->registration_confirmation_email();
}
function generate_ws_sess_id() {
    if (empty($_SESSION["id_empr_session"])) {
        return 0;
    }
    //Crééons la session
    $session_info = array();
    usleep(1);
    $session_info["sess_id"] = md5(microtime()).$_SESSION["id_empr_session"];
    $session_info["empr_id"] = $_SESSION["id_empr_session"];
    $session_info["login_date"] = time();
    $session_info["lastused_date"] = time();

    //Mettons la dans le cache
    $sql = "REPLACE INTO es_cache_blob SET es_cache_objectref = '".addslashes($session_info["sess_id"])."', es_cache_objecttype = 2, es_cache_objectformat = 'none', es_cache_owner = 'single_cache', es_cache_creationdate = NOW(), es_cache_expirationdate = NOW() + INTERVAL 1200 SECOND, es_cache_content = '".addslashes(serialize($session_info))."'";
    pmb_mysql_query($sql);

    //Creation d'une session OPAC si besoin
    initialize_opac_session($_SESSION["id_empr_session"]);
    $_SESSION["ws_sess_id"] = $session_info["sess_id"];
    return $session_info["sess_id"];
}

function initialize_opac_session($empr_id) {
    $empr_id = intval($empr_id);
    if(!$empr_id) {
        return;
    }
    $q = "SELECT session FROM opac_sessions WHERE empr_id = ".$empr_id;
    $r = pmb_mysql_query($q);
    if (pmb_mysql_num_rows($r)) {
        return;
    }
    $session = [
        'cart' => [],
    ];
    $q = "insert into opac_sessions (empr_id, session, date_rec) values ($empr_id, '".addslashes(serialize($session))."', now() )";
    pmb_mysql_query($q);
}
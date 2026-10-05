<?php
use Pmb\MFA\Controller\MFAOtpController;

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_patterns_readers_ui.class.php,v 1.3 2023/07/13 13:17:02 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_patterns_readers_ui extends list_patterns_ui {
	
	public static $emprunteur_datas;
	public static $temp_mfa_secret_code = "";
	
	public static function get_available_patterns() {
		global $animations_active;
		
		$patterns = [
				'empr_group_empr' => [
						'empr_name',
						'empr_first_name',
						'empr_sexe',
						'empr_cb',
						'empr_login',
						'empr_mail',
						'empr_loans',
						'empr_loans_late',
						'empr_resas',
						'empr_resa_confirme',
						'empr_resa_not_confirme',
						'empr_name_and_adress',
						'empr_dated',
						'empr_datef',
						'empr_nb_days_before_expiration',
						'empr_all_information',
						'empr_auth_opac',
						'empr_auth_opac_subscribe_link',
						'empr_last_loan_date',
						'empr_auth_opac_change_password_link',
				],
				'empr_group_mfa' => [
						'empr_code_totp'
				],
				'empr_group_loc' => [
						'empr_loc_name',
						'empr_loc_adr1',
						'empr_loc_adr2',
						'empr_loc_cp',
						'empr_loc_town',
						'empr_loc_phone',
						'empr_loc_email',
						'empr_loc_website',
				],
				'empr_group_misc' => [
						'empr_day_date',
				]
		];
		
		if ($animations_active) {
			$patterns['animation_group'] = [
					'animation_name',
					'animation_start_date',
					'animation_start_hour',
					'animation_end_date',
					'animation_end_hour',
					'animation_registered_list',
					'animation_location',
					'animation_empr_name',
					'animation_empr_firstname',
					'animation_registration_unsubscribe_link'
			];
		}
		return $patterns;
	}
	
	public static function get_patterns($text='') {
		global $msg, $opac_url_base;
		global $opac_connexion_phrase;
		
		$emprunteur_datas = static::$emprunteur_datas;
		
		$code_totp = '';
		if (strpos($text,"!!empr_code_totp!!") !== false) {
			$mfa_otp = (new MFAOtpController())->getData("OPAC");
			$mfa_totp = new mfa_totp();
			$mfa_totp->set_hash_method($mfa_otp->hashMethod);
			$mfa_totp->set_life_time($mfa_otp->lifetime);
			$mfa_totp->set_length_code($mfa_otp->lengthCode);
			$secret_code = !empty(static::$temp_mfa_secret_code) ? static::$temp_mfa_secret_code : $emprunteur_datas->mfa_secret_code;
			$code_totp = $mfa_totp->get_totp(base32_upper_decode($secret_code));
		}
		$loc_name = '';
		$loc_adr1 = '';
		$loc_adr2 = '';
		$loc_cp = '';
		$loc_town = '';
		$loc_phone = '';
		$loc_email = '';
		$loc_website = '';
		if ($emprunteur_datas->empr_location) {
			$empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=".$emprunteur_datas->empr_location);
			if (pmb_mysql_num_rows($empr_dest_loc)) {
				$empr_loc = pmb_mysql_fetch_object($empr_dest_loc);
				$loc_name = $empr_loc->name;
				$loc_adr1 = $empr_loc->adr1;
				$loc_adr2 = $empr_loc->adr2;
				$loc_cp = $empr_loc->cp;
				$loc_town = $empr_loc->town;
				$loc_phone = $empr_loc->phone;
				$loc_email = $empr_loc->email;
				$loc_website = $empr_loc->website;
			}
		}
		
		switch ($emprunteur_datas->empr_sexe) {
			case "2":
				$empr_civilite = $msg["civilite_madame"];
				break;
			case "1":
				$empr_civilite = $msg["civilite_monsieur"];
				break;
			default:
				$empr_civilite = $msg["civilite_unknown"];
				break;
		}
		
		$dates = time();
		$login = $emprunteur_datas->empr_login;
		$code=md5($opac_connexion_phrase.$login.$dates);
		
		$empr_auth_opac = "<a href='".$opac_url_base."empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac"]."</a>";
		$empr_auth_opac_subscribe_link = "<a href='".$opac_url_base."empr.php?lvl=renewal&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_subscribe_link"]."</a>";
		$empr_auth_opac_change_password_link = "<a href='".$opac_url_base."empr.php?lvl=change_password&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_change_password_link"]."</a>";
		
		$search = array(
				"!!empr_name!!",
				"!!empr_first_name!!",
				"!!empr_sexe!!",
				"!!empr_cb!!",
				"!!empr_login!!",
				"!!empr_mail!!",
				"!!empr_dated!!",
				"!!empr_datef!!",
				"!!empr_nb_days_before_expiration!!",
				"!!empr_auth_opac!!",
				"!!empr_auth_opac_subscribe_link!!",
				"!!empr_auth_opac_change_password_link!!",
				"!!empr_code_totp!!",
				"!!empr_loc_name!!",
				"!!empr_loc_adr1!!",
				"!!empr_loc_adr2!!",
				"!!empr_loc_cp!!",
				"!!empr_loc_town!!",
				"!!empr_loc_phone!!",
				"!!empr_loc_email!!",
				"!!empr_loc_website!!",
				"!!empr_day_date!!",
				"!!code!!",
				"!!login!!",
				"!!date_conex!!",
				"!!empr_last_loan_date!!",
		);

		$replace = array(
				$emprunteur_datas->empr_nom,
				$emprunteur_datas->empr_prenom,
				$empr_civilite,
				$emprunteur_datas->empr_cb,
				$emprunteur_datas->empr_login,
				$emprunteur_datas->empr_mail[0]	,
				$emprunteur_datas->aff_empr_date_adhesion,
				$emprunteur_datas->aff_empr_date_expiration,
				$emprunteur_datas->nb_days_before_expiration,
				$empr_auth_opac,
				$empr_auth_opac_subscribe_link,
				$empr_auth_opac_change_password_link,
				$code_totp,
				$loc_name,
				$loc_adr1,
				$loc_adr2,
				$loc_cp,
				$loc_town,
				$loc_phone,
				$loc_email,
				$loc_website,
				$emprunteur_datas->aff_empr_day_date,
				$code,
				$login,
				$dates,
				$emprunteur_datas->aff_last_loan_date,
		);
		
		if (strpos($text, "!!empr_loans!!") !== false) {
			$search[] = "!!empr_loans!!";
			$replace[] = $emprunteur_datas->m_liste_prets();
		}
		if (strpos($text, "!!empr_loans_late!!") !== false) {
			$search[] = "!!empr_loans_late!!";
			$replace[] = $emprunteur_datas->m_liste_prets(true);
		}
		if (strpos($text, "!!empr_resas!!") !== false) {
			$search[] = "!!empr_resas!!";
			$replace[] = $emprunteur_datas->m_liste_resas();
		}
		if (strpos($text, "!!empr_resa_confirme!!") !== false) {
			$search[] = "!!empr_resa_confirme!!";
			$replace[] = $emprunteur_datas->m_liste_resas_confirme();
		}
		if (strpos($text, "!!empr_resa_not_confirme!!") !== false) {
			$search[] = "!!empr_resa_not_confirme!!";
			$replace[] = $emprunteur_datas->m_liste_resas_not_confirme();
		}
		if (strpos($text, "!!empr_name_and_adress!!") !== false) {
			$search[] = "!!empr_name_and_adress!!";
			$replace[] = nl2br($emprunteur_datas->m_lecteur_adresse());
		}
		if (strpos($text, "!!empr_all_information!!") !== false) {
			$search[] = "!!empr_all_information!!";
			$replace[] = nl2br($emprunteur_datas->m_lecteur_info());
		}
		return array(
				'search' => $search,
				'replace' => $replace
		);
	}
	
	public static function set_emprunteur_datas($emprunteur_datas) {
		static::$emprunteur_datas = $emprunteur_datas;
	}

	public static function set_temp_mfa_secret_code($mfa_secret_code) {
		static::$temp_mfa_secret_code = $mfa_secret_code;
	}
	
}
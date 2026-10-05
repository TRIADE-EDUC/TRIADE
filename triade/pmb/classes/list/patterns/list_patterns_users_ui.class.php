<?php
use Pmb\MFA\Controller\MFAOtpController;
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_patterns_users_ui.class.php,v 1.5 2023/07/06 09:32:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_patterns_users_ui extends list_patterns_ui {
	
	/**
	 *Instance utilisateur
	 * @var user
	 */
	public static $user;
	public static $temp_mfa_secret_code = "";
	
	public static function get_available_patterns() {
		$patterns = [
				'user_group_user' => [
						'user_name',
						'user_first_name',
						'user_login',
						'user_email',
						
				],
				'user_group_mfa' => [
						'user_code_totp'
				],
				'user_group_loc' => [
						'user_loc_name',
						'user_loc_adr1',
						'user_loc_adr2',
						'user_loc_cp',
						'user_loc_town',
						'user_loc_phone',
						'user_loc_email',
						'user_loc_website',
				],
				'user_group_misc' => [
						'user_day_date',
				]
		];
		
		
		
		return $patterns;
	}
	
	public static function get_patterns($text='') {
		
		$user = static::$user;

		$code_totp = '';
		if (strpos($text,"!!user_code_totp!!") !== false) {
			$mfa_otp = (new MFAOtpController())->getData("GESTION");
			$mfa_totp = new mfa_totp();
			$mfa_totp->set_hash_method($mfa_otp->hashMethod);
			$mfa_totp->set_life_time($mfa_otp->lifetime);
			$mfa_totp->set_length_code($mfa_otp->lengthCode);
			$secret_code = !empty(static::$temp_mfa_secret_code) ? static::$temp_mfa_secret_code : $user->get_mfa_secret_code();
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
		if ($user->deflt_docs_location) {
			$result = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=".$user->deflt_docs_location);
			if (pmb_mysql_num_rows($result)) {
				$user_loc = pmb_mysql_fetch_object($result);
				$loc_name = $user_loc->name;
				$loc_adr1 = $user_loc->adr1;
				$loc_adr2 = $user_loc->adr2;
				$loc_cp = $user_loc->cp;
				$loc_town = $user_loc->town;
				$loc_phone = $user_loc->phone;
				$loc_email = $user_loc->email;
				$loc_website = $user_loc->website;
			}
		}
		$search = array(
				"!!user_name!!",
				"!!user_first_name!!",
				"!!user_login!!",
				"!!user_email!!",
				"!!user_code_totp!!",
				"!!user_loc_name!!",
				"!!user_loc_adr1!!",
				"!!user_loc_adr2!!",
				"!!user_loc_cp!!",
				"!!user_loc_town!!",
				"!!user_loc_phone!!",
				"!!user_loc_email!!",
				"!!user_loc_website!!",
				"!!user_day_date!!"
		);
		$replace = array(
				$user->get_nom(),
				$user->get_prenom(),
				$user->get_username(),
				$user->get_user_email(),
				$code_totp,
				$loc_name,
				$loc_adr1,
				$loc_adr2,
				$loc_cp,
				$loc_town,
				$loc_phone,
				$loc_email,
				$loc_website,
				format_date(date('Y-m-d'))
		);
		return array(
				'search' => $search,
				'replace' => $replace
		);
	}
	
	public static function set_user($user) {
		static::$user = $user;
	}

	public static function set_temp_mfa_secret_code($mfa_secret_code) {
		static::$temp_mfa_secret_code = $mfa_secret_code;
	}
	
}
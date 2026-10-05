<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesOPACEmpr.class.php,v 1.78.2.2.2.1 2025/03/04 16:43:11 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
global $msg, $charset;
global $verif_empr_ldap;
global $ldap_server, $ldap_basedn, $ldap_port, $ldap_proto, $ldap_binddn,$ldap_encoding_utf8;
global $ldap_accessible ;
global $opac_empr_password_salt;
global $opac_websubscribe_password_regexp;
global $opac_avis_allow;
global $opac_allow_add_tag;
global $opac_sugg_categ, $opac_sugg_categ_default;
global $pmb_transferts_actif, $transferts_choix_lieu_opac, $pmb_location_reservation;
global $opac_max_cart_items;


require_once $class_path."/external_services.class.php";
require_once $class_path."/external_services_caches.class.php";
require_once $class_path."/password/password.class.php";
require_once $class_path."/emprunteur.class.php";

define("LIST_LOAN_LATE",0);
define("LIST_LOAN_CURRENT",1);
define("LIST_LOAN_PRECEDENT",2);

class pmbesOPACEmpr extends external_services_api_class{

	public function check_auth(&$empr_login, &$empr_password, &$empr_id) {

		global $charset;
		global $base_path,$class_path;
		global $verif_empr_ldap;

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$empr_login = encoding_normalize::utf8_normalize($empr_login);
			$empr_password = encoding_normalize::utf8_normalize($empr_password);
		} else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$empr_login = encoding_normalize::utf8_decode($empr_login);
			$empr_password = encoding_normalize::utf8_decode($empr_password);
		}

		$verif_query = "SELECT id_empr, empr_login, empr_password, empr_date_expiration<sysdate() as isexp, empr_ldap, allow_opac
						FROM empr
						JOIN empr_statut ON empr_statut=idstatut
						WHERE empr_login='".addslashes($empr_login)."'";
		$verif_result = pmb_mysql_query($verif_query);
		if (!$verif_result || !pmb_mysql_num_rows($verif_result)) {
			return false;
		}
		// récupération des valeurs MySQL du lecteur et injection dans les variables
		$verif_line = pmb_mysql_fetch_array($verif_result);
		$empr_id = $verif_line['id_empr'];
		$verif_id_empr = $verif_line['id_empr'];
		$verif_empr_login = $verif_line['empr_login'];
		$verif_empr_password = $verif_line['empr_password'];
		$verif_isexp = $verif_line['isexp'];
		$verif_empr_ldap = $verif_line['empr_ldap'];
		$verif_opac = $verif_line['allow_opac'];

		if(file_exists($base_path."/external_services/pmbesOPACEmpr/external_auth.class.php")){
			require_once($base_path."/external_services/pmbesOPACEmpr/external_auth.class.php");
			$external_auth = new external_auth();
			$check = $external_auth->check_auth($empr_login, $empr_password);
			if($check){
				return true;
			} else if (!$external_auth->normal_auth) {
				return false;
			}
		}

		//Authentification ldap
		if ($verif_empr_ldap) {
			//Authentification par LDAP
			global $ldap_server, $ldap_basedn, $ldap_port, $ldap_proto, $ldap_binddn,$ldap_encoding_utf8;
			define ('LDAP_SERVER',$ldap_server);  //url server ldap
			define ('LDAP_BASEDN',$ldap_basedn);  //search base
			define ('LDAP_PORT'  ,$ldap_port);    //port
			define ('LDAP_PROTO'  ,$ldap_proto);    //protocollo
			define ('LDAP_BINDDN',$ldap_binddn);

			global $ldap_accessible ;
			if (!$ldap_accessible) {
				return false;
			}
			$ret = 0;
			if ($empr_password){
				//Gestion encodage
				if(($ldap_encoding_utf8) && ($charset != "utf-8")){
					$uid = encoding_normalize::utf8_normalize($empr_login);
					$pwd = encoding_normalize::utf8_normalize($empr_password);
				} elseif((!$ldap_encoding_utf8) && ($charset == "utf-8")){
					$uid = encoding_normalize::utf8_decode($empr_login);
					$pwd = encoding_normalize::utf8_decode($empr_password);
				}
				$dn=str_replace('UID',$uid,LDAP_BINDDN);
				$conn=@ldap_connect(LDAP_SERVER,LDAP_PORT);  // must be a valid LDAP server!
				ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, LDAP_PROTO);
				if ($conn) {
					$ret = @ldap_bind($conn, $dn, $pwd);
					ldap_unbind($conn);
				}
			}
			return $ret;

		}

		if(! $this->check_empr_allow_opac($verif_isexp, $verif_opac)) {
			return false;
		}

		//Authentification normale
		$check_password =false;
		$hash_format = password::get_hash_format($verif_empr_password);

		if( 'bcrypt' == $hash_format) {
			$check_password = password::verify_hash($empr_password, $verif_empr_password);
		} else {
			$previous_encrypted_password = password::gen_previous_hash($empr_password, $verif_id_empr);
			if( $verif_empr_password == $previous_encrypted_password ){
				$check_password = true;
			}
		}
		return ( ($check_password) && ($verif_empr_login!=""));
	}

	public function check_auth_md5($empr_login, $empr_password_md5, &$empr_id) {

		//note: cette fonction ne permet pas l'authentification synchronisée sur ldap
		global $charset;
		global $verif_empr_ldap;

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$empr_login = encoding_normalize::utf8_normalize($empr_login);
			$empr_password_md5 = encoding_normalize::utf8_normalize($empr_password_md5);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$empr_login = encoding_normalize::utf8_decode($empr_login);
			$empr_password_md5 = encoding_normalize::utf8_decode($empr_password_md5);
		}

		$verif_query = "SELECT id_empr, empr_password, empr_date_expiration<sysdate() as isexp, empr_login, empr_ldap, empr_location, allow_opac
						FROM empr
						JOIN empr_statut ON empr_statut=idstatut
						WHERE empr_login='".addslashes($empr_login)."'";
		$verif_result = pmb_mysql_query($verif_query);
		if (!$verif_result || !pmb_mysql_num_rows($verif_result)) {
			return 0;
		}

		// récupération des valeurs MySQL du lecteur et injection dans les variables
		$verif_line = pmb_mysql_fetch_array($verif_result);
		$verif_empr_login = $verif_line['empr_login'];
		$verif_empr_ldap = $verif_line['empr_ldap'];
		$verif_empr_password = $verif_line['empr_password'];
		$empr_id = $verif_line['id_empr'];
		$verif_isexp = $verif_line['isexp'];
		$verif_opac = $verif_line['allow_opac'];

		if(! $this->check_empr_allow_opac($verif_isexp, $verif_opac)) {
			return false;
		}

		if ($verif_empr_ldap) {
			return 0;
		} else {
			//Authentification standard
			return ((md5($verif_empr_password)==$empr_password_md5)&&($verif_empr_login!=""));
		}
	}

	/**
	 * Verif login / mot de passe "aes"
	 *
	 * @param string $empr_login : login lecteur
	 * @param string $empr_password : mot de passe chiffré avec AES-256-GCM encodé en Base64
	 * @param string $iv : vecteur d'initialisation encodé en Base64
	 * @param $empr_id : id lecteur
	 *
	 * @return bool
	 *
	 */
	public function check_auth_aes($empr_login, $empr_password, $iv, &$empr_id) {

		global $charset;
		global $opac_empr_password_salt;
		global $base_path,$class_path;
		global $verif_empr_ldap;

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$empr_login = encoding_normalize::utf8_normalize($empr_login);
		} else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$empr_login = encoding_normalize::utf8_decode($empr_login);
		}


		$verif_query = "SELECT id_empr, empr_password, empr_date_expiration<sysdate() as isexp, empr_ldap, allow_opac
						FROM empr
						JOIN empr_statut ON empr_statut=idstatut
						WHERE empr_login='".addslashes($empr_login)."'";
		$verif_result = pmb_mysql_query($verif_query);
		if (!$verif_result || !pmb_mysql_num_rows($verif_result)) {
			return false;
		}
		// récupération des valeurs MySQL du lecteur et injection dans les variables
		$verif_line = pmb_mysql_fetch_array($verif_result);
		$empr_id = $verif_line['id_empr'];
		$verif_id_empr = $verif_line['id_empr'];
		$verif_empr_password = $verif_line['empr_password'];
		$verif_isexp = $verif_line['isexp'];
		$verif_empr_ldap = $verif_line['empr_ldap'];
		$verif_opac = $verif_line['allow_opac'];

		if ($verif_empr_ldap) {
			return false;
		}

		if(! $this->check_empr_allow_opac($verif_isexp, $verif_opac)) {
			return false;
		}

		//Récupération du mot de passe en clair
		$cipher="AES-256-GCM"; 	//Algo chiffrement
		$tag_len = 16;			//Longueur tag

		$bin_key = hex2bin(substr($opac_empr_password_salt.$opac_empr_password_salt, 0, 64));
		$bin_text_tag = base64_decode($empr_password);
		$bin_iv = base64_decode($iv);

		$bin_text = substr($bin_text_tag, 0, strlen($bin_text_tag)-$tag_len);
		$bin_tag = substr($bin_text_tag, - $tag_len);

		$clear_empr_password = openssl_decrypt($bin_text, $cipher, $bin_key, OPENSSL_RAW_DATA, $bin_iv, $bin_tag);
		if(!$clear_empr_password) {
			return false;
		}

		//Authentification externe
		if(is_readable($base_path."/external_services/pmbesOPACEmpr/external_auth.class.php")){
			require_once $base_path."/external_services/pmbesOPACEmpr/external_auth.class.php";
			$external_auth = new external_auth();
			$check = $external_auth->check_auth($empr_login, $clear_empr_password);
			if($check){
				return true;
			} else if (!$external_auth->normal_auth){
				return false;
			}
		}

		//Authentification ldap
		if ($verif_empr_ldap) {
			//Authentification par LDAP
			global $ldap_server, $ldap_basedn, $ldap_port, $ldap_proto, $ldap_binddn,$ldap_encoding_utf8;
			define ('LDAP_SERVER',$ldap_server);  //url server ldap
			define ('LDAP_BASEDN',$ldap_basedn);  //search base
			define ('LDAP_PORT'  ,$ldap_port);    //port
			define ('LDAP_PROTO'  ,$ldap_proto);    //protocollo
			define ('LDAP_BINDDN',$ldap_binddn);

			global $ldap_accessible ;
			if (!$ldap_accessible) {
				return false;
			}
			$ret = 0;
			if ($clear_empr_password){
				//Gestion encodage
				if(($ldap_encoding_utf8) && ($charset != "utf-8")){
					$uid = encoding_normalize::utf8_normalize($empr_login);
					$pwd = encoding_normalize::utf8_normalize($clear_empr_password);
				} elseif((!$ldap_encoding_utf8) && ($charset == "utf-8")){
					$uid = encoding_normalize::utf8_decode($empr_login);
					$pwd = encoding_normalize::utf8_decode($clear_empr_password);
				}
				$dn=str_replace('UID',$uid,LDAP_BINDDN);
				$conn=@ldap_connect(LDAP_SERVER,LDAP_PORT);  // must be a valid LDAP server!
				ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, LDAP_PROTO);
				if ($conn) {
					$ret = @ldap_bind($conn, $dn, $pwd);
					ldap_unbind($conn);
				}
			}
			return $ret;
		}

		//Authentification normale

		//Vérification du mot de passe
		//Nouveau format
		$hash_format = password::get_hash_format($verif_empr_password);
		if( 'bcrypt' == $hash_format) {
			$check_password = password::verify_hash($clear_empr_password, $verif_empr_password);
			if(true === $check_password) {
				return 1;
			}
			return 0;
		}

		//Ancien format
		$check_password = password::gen_previous_hash($clear_empr_password,$verif_id_empr);
		if($check_password == $verif_empr_password) {
			return 1;
		}
		return 0;

	}

	/**
	 * Verif login / token
	 *
	 * @param string $login_scope : portée (mail, cb, login)
	 * @param string $login_date : date au format YmdHis
	 * @param string $login_value : Identifiant
	 * @param string $login_token : token
	 * @param int $empr_id : id lecteur
	 * @return bool
	 *
	 */
	public function check_auth_token($login_scope, $login_date, $login_value, $login_token, &$empr_id) {

	    global $charset;
	    global $opac_empr_password_salt;
	    global $base_path,$class_path;
	    global $verif_empr_ldap;

	    // Verification scope
	    if( !in_array($login_scope, ['mail', 'cb', 'login']) ) {
	        return false;
	    }

	    // Verification date
	    $login_date = intval($login_date);
	    $current_date = date('YmdHis');
	    if( ($current_date - $login_date) > 60) {
	        return false;
	    }

	    if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
	        $login_value = utf8_encode($login_value);
	    } else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
	        $login_value = utf8_decode($login_value);
	    }


	    // Recherche lecteur
	    $search_query = "SELECT id_empr, empr_date_expiration<sysdate() as isexp, allow_opac
			FROM empr JOIN empr_statut ON empr_statut=idstatut ";
	    switch($login_scope) {
	        case 'mail' :
	            $search_query .= "WHERE empr_mail='" . addslashes($login_value) . "' ";
	            break;
	        case 'cb' :
	            $search_query .= "WHERE empr_cb='" . addslashes($login_value) . "' ";
	            break;
	        default :
	        case 'login' :
	            $search_query .= "WHERE empr_login='" . addslashes($login_value) . "' ";
	            break;
 	    }

 	    $search_result = pmb_mysql_query($search_query);
 	    if ( !$search_result || (pmb_mysql_num_rows($search_result) != 1) ) {
 	        return false;
 	    }

 	    // Verification de l'autorisation de se connecter à l'OPAC
 	    $line = pmb_mysql_fetch_array($search_result);
 	    $empr_id = $line['id_empr'];
 	    $isexp = $line['isexp'];
 	    $allow_opac = $line['allow_opac'];
 	    if(! $this->check_empr_allow_opac($isexp, $allow_opac)) {
 	        return false;
 	    }

	    //Verification token
 	    try {
 	      $checked_token = hash_hmac('sha512', $login_scope.$login_date.$login_value, $opac_empr_password_salt, false);
 	    }catch(\Exception $e) {
 	        return false;
 	    }

 	    if($checked_token != $login_token) {
 	        return false;
 	    }

	    return true;

	}



	/**
	 * Verification des droits de connexion a l'opac
	 */
	protected function check_empr_allow_opac($verif_isexp, $verif_opac)
	{
		global $opac_adhesion_expired_status;
		//Si le statut du lecteur n'autorise pas la connexion opac on s'en va
		if(!$verif_opac) {
			return false;
		}
		//Verification du statut d'adhesion expiree si defini
		if($verif_isexp && $opac_adhesion_expired_status) {
			$adhesion_expired_status_query = "select allow_opac from empr_statut where idstatut='".$opac_adhesion_expired_status."'";
			$adhesion_status_result = pmb_mysql_query($adhesion_expired_status_query);
			if(pmb_mysql_num_rows($adhesion_status_result)) {
				$adhesion_status_allow_opac = pmb_mysql_result($adhesion_status_result, 0, 0);
			}
			$allow_opac = $verif_opac & $adhesion_status_allow_opac;
			if(!$allow_opac) {
				return false;
			}
		}
		return true;
	}


	public function retrieve_session_information($session_id, $no_update_session=false) {

		if (!$session_id) {
			throw new Exception('no session');
		}
		//Allons chercher les infos
		$es_cache = new external_services_cache('es_cache_blob', 1200);
		$session_info = $es_cache->decache_single_object($session_id, CACHE_TYPE_OPACEMPRSESSION);
		if ($session_info === false) {
			throw new Exception('no session');
		}
		$session_info = unserialize($session_info);

		//Mettons à jour la date de dernière utilisation de la session si besoin est
		if (!$no_update_session) {
			$session_info["lastused_date"] = time();
			$es_cache->encache_single_object($session_info["sess_id"], CACHE_TYPE_OPACEMPRSESSION, serialize($session_info));
		}
		return $session_info;
	}

	public function login($empr_login, $empr_password) {

		$empr_id = 0;
		if (!$this->check_auth($empr_login, $empr_password, $empr_id))
			return 0;

		//Crééons la session
		$session_info = array();
		usleep(1);
		$session_info["sess_id"] = md5(microtime()).$empr_id;
		$session_info["empr_id"] = $empr_id;
		$session_info["login_date"] = time();
		$session_info["lastused_date"] = time();

		//Mettons la dans le cache
		$es_cache = new external_services_cache('es_cache_blob', 1200);
		$es_cache->encache_single_object($session_info["sess_id"], CACHE_TYPE_OPACEMPRSESSION, serialize($session_info));

		//Creation d'une session OPAC si besoin
		$this->initialize_opac_session($empr_id);
		return $session_info["sess_id"];
	}

	/**
	 * Login / mot de passe "md5"
	 *
	 * @param string $empr_login
	 * @param string $empr_password
	 *
	 * $empr_password = md5(crypt($password.$opac_empr_password_salt.'0', substr($opac_empr_password_salt,0,2)));
	 * avec :
	 * $password = mot de passe en clair
	 * $opac_empr_password_salt = parametre $opac_empr_password_salt
	 */
	public function login_md5($empr_login, $empr_password) {

		$empr_id = 0;
		if (!$this->check_auth_md5($empr_login, $empr_password, $empr_id))
			return 0;

		//Crééons la session
		$session_info = array();
		usleep(1);
		$session_info["sess_id"] = md5(microtime()).$empr_id;
		$session_info["empr_id"] = $empr_id;
		$session_info["login_date"] = time();
		$session_info["lastused_date"] = time();

		//Mettons la dans le cache
		$es_cache = new external_services_cache('es_cache_blob', 1200);
		$es_cache->encache_single_object($session_info["sess_id"], CACHE_TYPE_OPACEMPRSESSION, serialize($session_info));

		//Creation d'une session OPAC si besoin
		$this->initialize_opac_session($empr_id);
		return $session_info["sess_id"];
	}

	/**
	 * Login / mot de passe "aes"
	 *
	 * @param string $empr_login : login lecteur
	 * @param string $empr_password : mot de passe chiffré avec AES-256-GCM encodé en Base64
	 * @param string $iv : vecteur d'initialisation encodé en Base64
	 *
	 */
	public function login_aes($empr_login, $empr_password, $iv) {

		$empr_id = 0;
		if (!$this->check_auth_aes($empr_login, $empr_password, $iv, $empr_id)) {
			return 0;
		}
		//Crééons la session
		$session_info = array();
		usleep(1);
		$session_info["sess_id"] = md5(microtime()).$empr_id;
		$session_info["empr_id"] = $empr_id;
		$session_info["login_date"] = time();
		$session_info["lastused_date"] = time();

		//Mettons la dans le cache
		$es_cache = new external_services_cache('es_cache_blob', 1200);
		$es_cache->encache_single_object($session_info["sess_id"], CACHE_TYPE_OPACEMPRSESSION, serialize($session_info));

		//Creation d'une session OPAC si besoin
		$this->initialize_opac_session($empr_id);
		return $session_info["sess_id"];
	}

	/**
	 * Login / token
	 *
	 * @param string $login_scope : portée (mail, cb, login)
	 * @param string $login_date : date au format YmdHis
	 * @param string $login_value : Identifiant
	 * @param string $login_token : token lecteur
	 *
	 */
	public function login_token($login_scope, $login_date, $login_value, $login_token) {

	    $empr_id = 0;

	    if (!$this->check_auth_token($login_scope, $login_date, $login_value, $login_token, $empr_id)) {
	        return 0;
	    }

	    //Creation session
	    $session_info = array();
	    usleep(1);
	    $session_info["sess_id"] = md5(microtime()).$empr_id;
	    $session_info["empr_id"] = $empr_id;
	    $session_info["login_date"] = time();
	    $session_info["lastused_date"] = time();

	    //Mettons la dans le cache
	    $es_cache = new external_services_cache('es_cache_blob', 1200);
	    $es_cache->encache_single_object($session_info["sess_id"], CACHE_TYPE_OPACEMPRSESSION, serialize($session_info));

	    //Creation d'une session OPAC si besoin
	    $this->initialize_opac_session($empr_id);
	    return $session_info["sess_id"];
	}

	/**
	 * Vérification de la présence d'une session OPAC et création si besoin
	 *
	 * @param int $empr_id : identifiant emprunteur
	 *
	 * return void
	 */
	protected function initialize_opac_session($empr_id) {

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


	public function logout($session_id) {

		if (!$session_id) {
			throw new Exception('no session');
		}
		$es_cache = new external_services_cache('es_cache_blob', 1200);
		$session_info = $es_cache->decache_single_object($session_id, CACHE_TYPE_OPACEMPRSESSION);
		if ($session_info !== false) {
			$es_cache->delete_single_object($session_id, CACHE_TYPE_OPACEMPRSESSION);
		}
	}

	public function get_account_info($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		$empr = new emprunteur($empr_id);

		$result = array();
		$result["id"] = $empr->id;
		$result["cb"] = $empr->cb;
		$result["personal_information"] = array();
		$result["personal_information"]["firstname"] = encoding_normalize::utf8_normalize($empr->prenom);
		$result["personal_information"]["lastname"] = encoding_normalize::utf8_normalize($empr->nom);
		$result["personal_information"]["address_part1"] = encoding_normalize::utf8_normalize($empr->adr1);
		$result["personal_information"]["address_part2"] = encoding_normalize::utf8_normalize($empr->adr2);
		$result["personal_information"]["address_cp"] = encoding_normalize::utf8_normalize($empr->cp);
		$result["personal_information"]["address_city"] = encoding_normalize::utf8_normalize($empr->ville);
		$result["personal_information"]["phone_number1"] = encoding_normalize::utf8_normalize($empr->tel1);
		$result["personal_information"]["phone_number2"] = encoding_normalize::utf8_normalize($empr->tel2);
		$result["personal_information"]["email"] = encoding_normalize::utf8_normalize($empr->mail);
		$result["personal_information"]["birthyear"] = encoding_normalize::utf8_normalize($empr->birth);
		$result["personal_information"]["sex"] = encoding_normalize::utf8_normalize($empr->sexe);
		$result["location_caption"] = encoding_normalize::utf8_normalize($empr->empr_location_l);
		$result["location_id"] = encoding_normalize::utf8_normalize($empr->empr_location);
		$result["adhesion_date"] = encoding_normalize::utf8_normalize($empr->date_adhesion);
		$result["expiration_date"] = encoding_normalize::utf8_normalize($empr->date_expiration);
		return $result;
	}

	public function change_password($session_id, $old_password, $new_password) {

		global $opac_websubscribe_password_regexp;
		global $charset, $lang;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		$empr = new emprunteur($empr_id);

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$old_password = encoding_normalize::utf8_normalize($old_password);
			$new_password = encoding_normalize::utf8_normalize($new_password);
		} elseif ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$old_password = encoding_normalize::utf8_decode($old_password);
			$new_password = encoding_normalize::utf8_decode($new_password);
		}

		if (!$old_password || !$new_password) {
			return 0;
		}

		//Pas de changement? On ne fait rien
		if ($old_password == $new_password) {
			return true;
		}

		// Verification des regles de mot de passe
		$check_password_rules = emprunteur::check_password_rules($empr_id, $new_password, [], $lang);
		if( !$check_password_rules['result'] ) {
			return 0;
		}

		//Vérifions que le mot de passe fourni est le bon
		$password_match = false;
		$hash_format = password::get_hash_format($empr->pwd);
		if( 'bcrypt' == $hash_format ) {
			$password_match = password::verify_hash($old_password, $empr->pwd);
		} elseif( $empr->pwd == password::gen_previous_hash($old_password, $empr_id) ) {
			$password_match = true;
		}
		if (!$password_match) {
			return 0;
		}
		//Changement
		emprunteur::update_digest(addslashes($empr->login), $new_password);
		emprunteur::hash_password(addslashes($empr->login), $new_password);

		return true;
	}

	public function list_loans($session_id, $loan_type) {

		global $msg;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		$empr = new emprunteur($empr_id);

		switch ($loan_type) {
			case LIST_LOAN_LATE:
			case LIST_LOAN_CURRENT:
						$empr->fetch_info_suite();
				$results = array();
				foreach ($empr->prets as $apret) {
					if ($loan_type == LIST_LOAN_LATE && !$apret["pret_retard"])
						continue;
					$expl_object = new exemplaire($apret["cb"]);
					$aresult = array(
						"empr_id" => $empr_id,
						"notice_id" => $expl_object->id_notice,
						"bulletin_id" => $expl_object->id_bulletin,
						"expl_id" => $apret["id"],
						"expl_cb" => encoding_normalize::utf8_normalize($apret["cb"]),
						"expl_support" => encoding_normalize::utf8_normalize($apret["typdoc"]),
						"expl_location_id" => $expl_object->location_id,
						"expl_location_caption" => encoding_normalize::utf8_normalize($apret["location"]),
						"expl_section_id" => $expl_object->section_id,
						"expl_section_caption" => encoding_normalize::utf8_normalize($apret["section"]),
						"expl_libelle" => encoding_normalize::utf8_normalize(strip_tags($apret["libelle"])),
						"loan_startdate" => $apret["date_pret"],
						"loan_returndate" => $apret["date_retour"],
					    'pnb_flag' => $apret['pnb_flag'],
					);
					$results[] = $aresult;
				}
				break;
			case LIST_LOAN_PRECEDENT:
				$sql = "SELECT arc_expl_notice, arc_expl_bulletin, arc_expl_id, tdoc_libelle," ;
				$sql.= "group_concat(distinct date_format(arc_debut, '".$msg["format_date"]."') separator '<br />') as aff_pret_debut, ";
				$sql.= "group_concat(distinct date_format(arc_fin, '".$msg["format_date"]."') separator '<br />') as aff_pret_fin, ";
				$sql.= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if(mention_date, concat(' (',mention_date,')') ,if (date_date, concat(' (',date_format(date_date, '".$msg["format_date"]."'),')') ,'')))) as tit, if(notices_m.notice_id, notices_m.notice_id, notices_s.notice_id) as not_id ";
				$sql.= "FROM (((pret_archive LEFT JOIN notices AS notices_m ON arc_expl_notice = notices_m.notice_id ) ";
				$sql.= "        LEFT JOIN bulletins ON arc_expl_bulletin = bulletins.bulletin_id) ";
				$sql.= "        LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id) ";
				$sql.= "        LEFT JOIN docs_type ON docs_type.idtyp_doc = pret_archive.arc_expl_typdoc, ";
				$sql.= "        empr ";
				$sql.= "WHERE empr.id_empr = arc_id_empr and arc_id_empr='$empr_id' ";
				$sql.= "group by arc_expl_notice, arc_expl_bulletin, tit, not_id ";
				$sql.= "order by arc_debut desc";
				$res = pmb_mysql_query($sql);
				while($row = pmb_mysql_fetch_assoc($res)) {
					$expl_object = new exemplaire('', $row["arc_expl_id"]);
					$expl_libelle="";
					if ($expl_object->id_bulletin) {
						$bulletin_display = new bulletinage_display($expl_object->id_bulletin);
						$expl_libelle = $bulletin_display->header;
					}
					else {
						$notice_display = new mono_display($expl_object->id_notice, 0);
						$expl_libelle = $notice_display->header;
					}
					$aresult = array(
						"empr_id" => $empr_id,
						"notice_id" => $expl_object->id_notice,
						"bulletin_id" => $expl_object->id_bulletin,
						"expl_id" => $row["arc_expl_id"],
						"expl_cb" => encoding_normalize::utf8_normalize($expl_object->cb),
						"expl_support" => encoding_normalize::utf8_normalize($row["tdoc_libelle"]),
						"expl_location_id" => $expl_object->location_id,
						"expl_location_caption" => encoding_normalize::utf8_normalize($expl_object->location),
						"expl_section_id" => $expl_object->section_id,
						"expl_section_caption" => encoding_normalize::utf8_normalize($expl_object->section),
						"expl_libelle" => encoding_normalize::utf8_normalize($expl_libelle),
						"loan_startdate" => $row["aff_pret_debut"],
						"loan_returndate" => $row["aff_pret_fin"]
					);
					$results[] = $aresult;
				}
				break;
		}

		return $results;
	}

	public function list_resas($session_id) {

		global $msg;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$results = array();
		$requete3 = "SELECT id_resa, resa_idempr, resa_idnotice, resa_idbulletin, resa_date, resa_date_fin, resa_cb, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date"]."') as aff_date_fin, resa_loc_retrait, location_libelle FROM resa LEFT JOIN docs_location ON (idlocation = resa_loc_retrait) WHERE resa_idempr=".$empr_id;
		$result3 = @pmb_mysql_query($requete3);
		while ($resa = pmb_mysql_fetch_array($result3)) {
			$id_resa = $resa['id_resa'];
			$resa_idempr = $resa['resa_idempr'];
			$resa_idnotice = $resa['resa_idnotice'];
			$resa_idbulletin = $resa['resa_idbulletin'];
			$resa_retrait_location_id = $resa["resa_loc_retrait"];
			$resa_retrait_location = $resa["location_libelle"];

			if ($resa['resa_cb']) {
				$resa_dateend = $resa['aff_date_fin'];
			}else {
				$resa_dateend = "";
			}

			$rang = recupere_rang($resa_idempr, $resa_idnotice, $resa_idbulletin) ;

			$aresult = array(
				"resa_id" => $id_resa,
				"empr_id" => $empr_id,
				"notice_id" => $resa_idnotice,
				"bulletin_id" => $resa_idbulletin,
				"resa_rank" => $rang,
				"resa_dateend" => $resa_dateend,
				"resa_retrait_location" => encoding_normalize::utf8_normalize($resa_retrait_location),
				"resa_retrait_location_id" => $resa_retrait_location_id
			);
			$results[] = $aresult;
		}

		return $results;
	}

	public function delete_resa($session_id, $resa_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return FALSE;
		}
		$resa_id = intval($resa_id);
		if (!$resa_id) {
			return FALSE;
		}
		// *** Traitement de la suppression d'une résa affectée
		$recup_id_resa = "select id_resa, resa_cb FROM resa WHERE resa_idempr=".$empr_id;
		$recup_id_resa .= " AND id_resa = $resa_id";
		$resrecup_id_resa = pmb_mysql_query($recup_id_resa);
		$obj_recupidresa = pmb_mysql_fetch_object($resrecup_id_resa) ;
		$suppr_id_resa = $obj_recupidresa->id_resa ;

		// récup éventuelle du cb
		$cb_recup = $obj_recupidresa->resa_cb ;
		// archivage resa
		$rqt_arch = "UPDATE resa_archive, resa SET resarc_anulee = 1 WHERE id_resa = '".$suppr_id_resa."' AND resa_arc = resarc_id ";
		pmb_mysql_query($rqt_arch);
		// suppression
		$rqt = "delete from resa where id_resa='".$suppr_id_resa."' ";
		pmb_mysql_query($rqt);

		// réaffectation du doc éventuellement
		if ($cb_recup) {
			if (!affecte_cb ($cb_recup) && $cb_recup) {
				// cb non réaffecté, il faut transférer les infos de la résa dans la table des docs à ranger
				$rqt = "insert into resa_ranger (resa_cb) values ('".$cb_recup."') ";
				pmb_mysql_query($rqt) ;
			}
		};
		return TRUE;
	}

	public function list_suggestions($session_id) {

		global $msg;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$sql = "select *, date_format(date_suggestion, '".$msg["format_date"]."') as aff_date, libelle_categ as sugg_category_caption, libelle_source as sugg_source_caption from suggestions_origine, suggestions LEFT JOIN suggestions_source ON suggestions_source.id_source = suggestions.sugg_source LEFT JOIN suggestions_categ ON suggestions_categ.id_categ = suggestions.num_categ where origine = '".$empr_id."' ";
		$sql .= "and type_origine = '1' ";
		$sql .= "and id_suggestion=num_suggestion order by date_suggestion ";
		$res = pmb_mysql_query($sql);
		if (!$res)
			return array();

		$sug_map = new suggestions_map();

		$results = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
			$sugg_state = $sug_map->getTextComment($row["statut"]);

			$aresult = array(
				"sugg_id" => $row["id_suggestion"],
				"sugg_title" => encoding_normalize::utf8_normalize($row["titre"]),
				"sugg_author" => encoding_normalize::utf8_normalize($row["auteur"]),
				"sugg_editor" => encoding_normalize::utf8_normalize($row["editeur"]),
				"sugg_barcode" => encoding_normalize::utf8_normalize($row["code"]),
				"sugg_price" => encoding_normalize::utf8_normalize($row["prix"]),
				"sugg_url" => encoding_normalize::utf8_normalize($row["url_suggestion"]),
				"sugg_comment" => encoding_normalize::utf8_normalize($row["commentaires"]),
				"sugg_date" => encoding_normalize::utf8_normalize($row["date_publication"]),
				"sugg_source_caption" => encoding_normalize::utf8_normalize($row["sugg_source_caption"]),
				"sugg_source" => encoding_normalize::utf8_normalize($row["sugg_source"]),
				"sugg_category_caption" => encoding_normalize::utf8_normalize($row["sugg_category_caption"]),
				"sugg_category" => encoding_normalize::utf8_normalize($row["num_categ"]),
				"sugg_location" => encoding_normalize::utf8_normalize($row["sugg_location"]),
				"sugg_state" => encoding_normalize::utf8_normalize($row["statut"]),
				"sugg_state_caption" => encoding_normalize::utf8_normalize($sugg_state),
			);
			$results[] = $aresult;
		}
		return $results;
	}

	public function add_review($session_id, $notice_id, $note, $comment, $subject) {

		global $charset;
		global $opac_avis_allow;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$note = encoding_normalize::utf8_normalize($note);
			$comment = encoding_normalize::utf8_normalize($comment);
			$subject = encoding_normalize::utf8_normalize($subject);
		} else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$note = encoding_normalize::utf8_decode($note);
			$comment = encoding_normalize::utf8_decode($comment);
			$subject = encoding_normalize::utf8_decode($subject);
		}

		//Vérifions qu'on peut poster des avis:
		//Valeurs correctes: 1 2 3
		//Valeurs pas correctes: 0
		if ($opac_avis_allow == 0)
			return 0;

		//Vérifions que la notice demandée existe
		$notice_id = intval($notice_id);
		$sql = "SELECT COUNT(1) > 0 FROM notices WHERE notice_id = ".$notice_id;
		$exists = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		if (!$exists)
			return 0;

		//Vérifions que la note est conforme:
		$note = intval($note);
		if (!$note)
			return 0;

		//Ajoutons l'avis:
		//Copié de /opac_css/avis.php
		$sql="insert into avis (num_empr,num_notice,type_object,note,sujet,commentaire) values ('$empr_id','$notice_id','1','$note','".addslashes($subject)."','".addslashes($comment)."')";
		$res = pmb_mysql_query($sql);
		return $res != false ? 1 : 0;
	}

	public function add_tag($session_id, $notice_id, $tag) {

		global $opac_allow_add_tag;
		global $charset;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$tag = encoding_normalize::utf8_normalize($tag);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$tag = encoding_normalize::utf8_decode($tag);
		}

		//Vérifions qu'on peut poster des avis:
		//Valeurs correctes: 1 2
		//Valeurs pas correctes: 0
		if ($opac_allow_add_tag == 0)
			return 0;

		//Vérifions que la notice demandée existe
		$notice_id = intval($notice_id);
		$sql = "SELECT COUNT(1) > 0 FROM notices WHERE notice_id = ".$notice_id;
		$exists = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		if (!$exists)
			return 0;

		//Vérifions que le tag existe
		if (!$tag)
			return 0;

		//Vérifions si le tag n'est pas déjà dans la notice
		$sql="select * from notices where index_l like '%".addslashes($tag)."%' and notice_id=$notice_id";
		$r = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($r)>=1)
			return 0;

		//Si tout va bien, on y va
		$sql="insert into tags (libelle, num_notice,user_code,dateajout) values ('".addslashes($tag)."',$notice_id,'". $empr_id ."',CURRENT_TIMESTAMP())";
		return pmb_mysql_query($sql) != false ? 1 : 0;
	}

	public function list_suggestion_categories($session_id) {

		global $opac_sugg_categ;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		if (!$opac_sugg_categ) {
			return [];
		}
		$results = array();
		$sugg_categs = suggestions_categ::getCategList();
		foreach ($sugg_categs as $categ_id => $categ_caption) {
			$results[] = array(
				"category_id" => $categ_id,
				"category_caption" => encoding_normalize::utf8_normalize($categ_caption)
			);
		}
		return $results;
	}

	public function list_suggestion_sources($session_id) {

		global $opac_sugg_categ;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		if (!$opac_sugg_categ) {
			return array();
		}
		$req = "select * from suggestions_source order by libelle_source";
		$res=pmb_mysql_query($req);
		$results = array();
		while ($row = pmb_mysql_fetch_object($res)){
			$results[] = array(
				"source_id" => $row->id_source,
				"source_caption" => encoding_normalize::utf8_normalize($row->libelle_source)
			);
		}
		return $results;
	}

	public function list_suggestion_sources_and_categories($session_id) {
		return array(
			'sources' => $this->proxy_parent->pmbesOPACEmpr_list_suggestion_sources($session_id),
			'categories' => $this->proxy_parent->pmbesOPACEmpr_list_suggestion_categories($session_id),
		);
	}

	public function list_locations($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		return $this->proxy_parent->pmbesOPACGeneric_list_locations();
	}

	public function add_suggestion($session_id, $title, $author, $editor, $isbn_or_ean, $price, $url, $comment, $sugg_categ, $sugg_location) {

		global $charset;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$title = encoding_normalize::utf8_normalize($title);
			$author = encoding_normalize::utf8_normalize($author);
			$editor = encoding_normalize::utf8_normalize($editor);
			$isbn_or_ean = encoding_normalize::utf8_normalize($isbn_or_ean);
			$price = encoding_normalize::utf8_normalize($price);
			$url = encoding_normalize::utf8_normalize($url);
			$comment = encoding_normalize::utf8_normalize($comment);
			$sugg_categ = encoding_normalize::utf8_normalize($sugg_categ);
			$sugg_location = encoding_normalize::utf8_normalize($sugg_location);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$title = encoding_normalize::utf8_decode($title);
			$author = encoding_normalize::utf8_decode($author);
			$editor = encoding_normalize::utf8_decode($editor);
			$isbn_or_ean = encoding_normalize::utf8_decode($isbn_or_ean);
			$price = encoding_normalize::utf8_decode($price);
			$url = encoding_normalize::utf8_decode($url);
			$comment = encoding_normalize::utf8_decode($comment);
			$sugg_categ = encoding_normalize::utf8_decode($sugg_categ);
			$sugg_location = encoding_normalize::utf8_decode($sugg_location);
		}

		$sug_map = new suggestions_map();
		global $opac_sugg_categ, $opac_sugg_categ_default;

		//copié de /opac_css/empr/make_sugg.inc.php
		//On évite de saisir 2 fois la même suggestion
		if (!suggestions::exists($empr_id, $title, $author, $editor, $isbn_or_ean)) {
			$su = new suggestions();
			$su->titre = $title;
			$su->editeur = $editor;
			$su->auteur = $author;
			$su->code = $isbn_or_ean;
			$price = str_replace(',','.',$price);
			if (is_numeric($price)) $su->prix = $price;
			$su->nb = 1;
			$su->statut = $sug_map->getFirstStateId();
			$su->url_suggestion = $url;
			$su->commentaires = $comment;
			$su->date_creation = today();

			if ($opac_sugg_categ == '1' ) {

				if (!suggestions_categ::exists($sugg_categ) ){
					$num_categ = $opac_sugg_categ_default;
				}
				if (!suggestions_categ::exists($num_categ) ) {
					$num_categ = '1';
				}
				$su->num_categ = $num_categ;
			}
			$su->sugg_location=$sugg_location;
			try {
			    $su->save();
			} catch(Exception $e){
			    return 0;
			}

			$orig = new suggestions_origine($empr_id, $su->id_suggestion);
			$orig->type_origine = 1;
			try {
			    $orig->save();
			} catch(Exception $e){
			    return 0;
			}

			return true;
		}
		return 0;
	}

	public function add_suggestion2($session_id, $suggestion) {

		global $charset;

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		$title = $suggestion['sugg_title'];
		$author = $suggestion['sugg_author'];
		$editor = $suggestion['sugg_editor'];
		$isbn_or_ean = $suggestion['sugg_barcode'];
		$price = $suggestion['sugg_price'];
		$url = $suggestion['sugg_url'];
		$comment = $suggestion['sugg_comment'];
		$sugg_categ = $suggestion['sugg_category'];
		$sugg_source = $suggestion['sugg_source'];
		$sugg_location = $suggestion['sugg_location'];

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$title = encoding_normalize::utf8_normalize($suggestion['sugg_title']);
			$author = encoding_normalize::utf8_normalize($suggestion['sugg_author']);
			$editor = encoding_normalize::utf8_normalize($suggestion['sugg_editor']);
			$isbn_or_ean = encoding_normalize::utf8_normalize($suggestion['sugg_barcode']);
			$price = encoding_normalize::utf8_normalize($suggestion['sugg_price']);
			$url = encoding_normalize::utf8_normalize($suggestion['sugg_url']);
			$comment = encoding_normalize::utf8_normalize($suggestion['sugg_comment']);
			$sugg_categ = encoding_normalize::utf8_normalize($suggestion['sugg_category']);
			$sugg_source = encoding_normalize::utf8_normalize($suggestion['sugg_source']);
			$sugg_location = encoding_normalize::utf8_normalize($suggestion['sugg_location']);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$title = encoding_normalize::utf8_decode($suggestion['sugg_title']);
			$author = encoding_normalize::utf8_decode($suggestion['sugg_author']);
			$editor = encoding_normalize::utf8_decode($suggestion['sugg_editor']);
			$isbn_or_ean = encoding_normalize::utf8_decode($suggestion['sugg_barcode']);
			$price = encoding_normalize::utf8_decode($suggestion['sugg_price']);
			$url = encoding_normalize::utf8_decode($suggestion['sugg_url']);
			$comment = encoding_normalize::utf8_decode($suggestion['sugg_comment']);
			$sugg_categ = encoding_normalize::utf8_decode($suggestion['sugg_category']);
			$sugg_source = encoding_normalize::utf8_decode($suggestion['sugg_source']);
			$sugg_location = encoding_normalize::utf8_decode($suggestion['sugg_location']);
		}



		$sug_map = new suggestions_map();
		global $opac_sugg_categ, $opac_sugg_categ_default;

		//copié de /opac_css/empr/make_sugg.inc.php
		//On évite de saisir 2 fois la même suggestion
		if (!suggestions::exists($empr_id, $title, $author, $editor, $isbn_or_ean)) {
			$su = new suggestions();
			$su->titre = $title;
			$su->editeur = $editor;
			$su->auteur = $author;
			$su->code = $isbn_or_ean;
			$price = str_replace(',','.',$price);
			if (is_numeric($price)) $su->prix = $price;
			$su->nb = 1;
			$su->statut = $sug_map->getFirstStateId();
			$su->url_suggestion = $url;
			$su->commentaires = $comment;
			$su->date_creation = today();
			$su->sugg_src = $sugg_source;

			if ($opac_sugg_categ == '1' ) {

				if (!suggestions_categ::exists($sugg_categ) ){
					$sugg_categ = $opac_sugg_categ_default;
				}
				if (!suggestions_categ::exists($sugg_categ) ) {
					$sugg_categ = '1';
				}
				$su->num_categ = $sugg_categ;
			}
			$su->sugg_location=$sugg_location;
			$su->save();

			$orig = new suggestions_origine($empr_id, $su->id_suggestion);
			$orig->type_origine = 1;
			$orig->save();
			return true;
		}
		return 0;
	}

	public function edit_suggestion($session_id, $suggestion) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		$id = intval($suggestion['sugg_id']);
		if (!$id)
			return FALSE;

		$exists = suggestions_origine::exists($empr_id, $id, 1);
		if (!$exists)
			return FALSE;

		$title = $suggestion['sugg_title'];
		$author = $suggestion['sugg_author'];
		$editor = $suggestion['sugg_editor'];
		$isbn_or_ean = $suggestion['sugg_barcode'];
		$price = $suggestion['sugg_price'];
		$url = $suggestion['sugg_url'];
		$comment = $suggestion['sugg_comment'];
		$sugg_categ = $suggestion['sugg_category'];
		$sugg_source = $suggestion['sugg_source'];
		$sugg_location = $suggestion['sugg_location'];

		global $charset;
		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$title = encoding_normalize::utf8_normalize($suggestion['sugg_title']);
			$author = encoding_normalize::utf8_normalize($suggestion['sugg_author']);
			$editor = encoding_normalize::utf8_normalize($suggestion['sugg_editor']);
			$isbn_or_ean = encoding_normalize::utf8_normalize($suggestion['sugg_barcode']);
			$price = encoding_normalize::utf8_normalize($suggestion['sugg_price']);
			$url = encoding_normalize::utf8_normalize($suggestion['sugg_url']);
			$comment = encoding_normalize::utf8_normalize($suggestion['sugg_comment']);
			$sugg_categ = encoding_normalize::utf8_normalize($suggestion['sugg_category']);
			$sugg_source = encoding_normalize::utf8_normalize($suggestion['sugg_source']);
			$sugg_location = encoding_normalize::utf8_normalize($suggestion['sugg_location']);
		}
		else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$title = encoding_normalize::utf8_decode($suggestion['sugg_title']);
			$author = encoding_normalize::utf8_decode($suggestion['sugg_author']);
			$editor = encoding_normalize::utf8_decode($suggestion['sugg_editor']);
			$isbn_or_ean = encoding_normalize::utf8_decode($suggestion['sugg_barcode']);
			$price = encoding_normalize::utf8_decode($suggestion['sugg_price']);
			$url = encoding_normalize::utf8_decode($suggestion['sugg_url']);
			$comment = encoding_normalize::utf8_decode($suggestion['sugg_comment']);
			$sugg_categ = encoding_normalize::utf8_decode($suggestion['sugg_category']);
			$sugg_source = encoding_normalize::utf8_decode($suggestion['sugg_source']);
			$sugg_location = encoding_normalize::utf8_decode($suggestion['sugg_location']);
		}

		$sug_map = new suggestions_map();
		global $opac_sugg_categ, $opac_sugg_categ_default;

		//copié de /opac_css/empr/make_sugg.inc.php
		//On évite de saisir 2 fois la même suggestion
		$su = new suggestions($id);
		$su->titre = $title;
		$su->editeur = $editor;
		$su->auteur = $author;
		$su->code = $isbn_or_ean;
		$price = str_replace(',','.',$price);
		if (is_numeric($price)) $su->prix = $price;
		$su->nb = 1;
		$su->statut = $sug_map->getFirstStateId();
		$su->url_suggestion = $url;
		$su->commentaires = $comment;
		$su->date_creation = today();
		$su->sugg_src = $sugg_source;

		if ($opac_sugg_categ == '1' ) {

			if (!suggestions_categ::exists($sugg_categ) ){
				$sugg_categ = $opac_sugg_categ_default;
			}
			if (!suggestions_categ::exists($sugg_categ) ) {
				$sugg_categ = '1';
			}
			$su->num_categ = $sugg_categ;
		}
		$su->sugg_location=$sugg_location;
		$su->save();
		return true;
	}

	public function delete_suggestion($session_id, $suggestion_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return FALSE;
		}
		$exists = suggestions_origine::exists($empr_id, $suggestion_id, 1);
		if (!$exists)
			return FALSE;

		$sugg = new suggestions($suggestion_id);
		if (!($sugg->sugg_origine_type == 1) && ($sugg->sugg_origine == $empr_id))
			return FALSE;

		suggestions::delete($suggestion_id);
		return TRUE;
	}

	public function list_resa_locations($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return array();
		}
		$empr = new emprunteur($empr_id);

		global $pmb_transferts_actif, $transferts_choix_lieu_opac, $pmb_location_reservation;
		if (($pmb_transferts_actif!="1")||($transferts_choix_lieu_opac!="1"))
			return array();

		$results=array();
		if($pmb_location_reservation) {
			$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1  and idlocation in (select resa_loc from resa_loc where resa_emprloc=".$empr->empr_location.") ORDER BY location_libelle ";
		} else {
			$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1 ORDER BY location_libelle";
		}
		$res = pmb_mysql_query($loc_req);
		//on parcours la liste des localisations
		while ($value = pmb_mysql_fetch_array($res)) {
			$results[] = array(
				"location_id" => $value[0],
				"location_caption" => encoding_normalize::utf8_normalize($value[1])
			);
		}
		return $results;
	}

	public function can_reserve_notice($session_id, $id_notice, $id_bulletin) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return FALSE;
		}
		$resa= new reservation($empr_id, $id_notice, $id_bulletin);
		return $resa->can_reserve();
	}

	public function add_resa($session_id, $id_notice, $id_bulletin, $location) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id){
			$results = [
					"success" => false,
					"error" => "no_empr_id",
			];
			return $results;
		}
		$results=array();
		$resa= new reservation($empr_id, $id_notice, $id_bulletin);
		$ral = $resa->add($location);
		if($ral == false) {
			$results = [
					"success" => false,
					"error" => $resa->service->error,
					"message" => encoding_normalize::utf8_normalize($resa->service->message),
			];
		} else {
			reservation::alert_mail_users_pmb($id_notice, $id_bulletin, $empr_id);
			$results = [
					"success" => true,
					"error" => "",
					"message" => "",
			];
		}
		return $results;
	}

	public function list_abonnements($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		$results = [];

		$bannette_abon = new bannette_abon(0, $empr_id);
		$tableau_bannette_pub = $bannette_abon->tableau_gerer_bannette("PUB");
		$tableau_bannette_priv = $bannette_abon->tableau_gerer_bannette("PRI");
		$tableau_bannettes = array_merge($tableau_bannette_pub, $tableau_bannette_priv);
		$search = new search();
		foreach ($tableau_bannettes as $abanette) {
			// Construction de l'affichage de l'info bulle de la requette
			$requete="select * from bannette_equation, equations where num_equation=id_equation and num_bannette=".$abanette["id_bannette"];
			$resultat=pmb_mysql_query($requete);
			if (($r=pmb_mysql_fetch_object($resultat))) {
				$equ = new equation ($r->num_equation);
				$search->unserialize_search($equ->requete);
				$recherche = $search->make_human_query();
			}

			$a_abonnement = array(
				'abonnement_id' => $abanette["id_bannette"],
				'abonnement_type' => ($abanette["priv_pub"] == 'PUB' ? "PUBLIC" : 'PRIVATE'),
				'abonnement_title' => encoding_normalize::utf8_normalize($abanette["comment_public"]),
				'abonnement_lastsentdate' => encoding_normalize::utf8_normalize($abanette["aff_date_last_envoi"]),
				'abonnement_notice_count' => $abanette["nb_contenu"],
				'abonnement_equation_human' => encoding_normalize::utf8_normalize($recherche),
				'empr_subscriber' => ($abanette["priv_pub"] == 'PUB' ? ($abanette["abonn"] == 'checked') : true)
			);
			$results[] = $a_abonnement;
		}

		return $results;

	}

	public function list_cart_content($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		$results = [];
		$sql = "SELECT session FROM opac_sessions WHERE empr_id = ".$empr_id;
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$empr_session = unserialize($row["session"]);
			if (isset($empr_session["cart"])) {
				foreach ($empr_session["cart"] as $anotice_id) {
					$results[] = $anotice_id;
				}
			}
		}
		return $results;
	}

	public function empty_cart($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$results = [];
		$sql = "SELECT session FROM opac_sessions WHERE empr_id = ".$empr_id;
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$empr_session = unserialize($row["session"]);
			$empr_session["cart"] = array();
			$new_row_session = serialize($empr_session);
			$sql_update = "UPDATE opac_sessions SET session ='".addslashes($new_row_session)."', date_rec = NOW() WHERE empr_id = ".$empr_id;
			pmb_mysql_query($sql_update);
		}
		return $results;
	}

	public function add_notices_to_cart($session_id, $notice_ids) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		if (!is_array($notice_ids)) {
			return 0;
		}
		//Enlevons ce qui n'est pas entier dans le tableau
		$notice_ids = array_filter($notice_ids, function($id) { return intval($id);});
		if (!count($notice_ids)) {
			return 0;
		}
		$sql = "SELECT session FROM opac_sessions WHERE empr_id = ".$empr_id;
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$empr_session = unserialize($row["session"]);
			if (!isset($empr_session["cart"]))
				$empr_session["cart"] = array();

			$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
			$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage
			//Vérifions que l'emprunteur a bien le droit de toucher les notices
			$notice_ids=$this->filter_tabl_notices($notice_ids);

			foreach ($notice_ids as $anotice_id) {
				$empr_session["cart"][] = $anotice_id;
			}
			$empr_session["cart"] = array_unique($empr_session["cart"]);

			global $opac_max_cart_items;
			$empr_session["cart"] = array_slice($empr_session["cart"], 0, $opac_max_cart_items);

			$new_row_session = serialize($empr_session);
			$sql_update = "UPDATE opac_sessions SET session ='".addslashes($new_row_session)."', date_rec = NOW() WHERE empr_id = ".$empr_id;
			pmb_mysql_query($sql_update);
		}
		return true;
	}

	public function delete_notices_from_cart($session_id, $notice_ids) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return 0;
		}
		if (!is_array($notice_ids)) {
			return 0;
		}
		//Enlevons ce qui n'est pas entier dans le tableau
		$notice_ids = array_filter($notice_ids, function($id) { return intval($id);});
		if (count($notice_ids) == 0) {
			return 0;
		}
		$sql = "SELECT session FROM opac_sessions WHERE empr_id = ".$empr_id;
		$res = pmb_mysql_query($sql);
		if (pmb_mysql_num_rows($res)) {
			$row = pmb_mysql_fetch_assoc($res);
			$empr_session = unserialize($row["session"]);
			if (!isset($empr_session["cart"]))
				$empr_session["cart"] = array();

			$empr_session["cart"] = array_diff($empr_session["cart"], $notice_ids);
			$new_row_session = serialize($empr_session);
			$sql_update = "UPDATE opac_sessions SET session ='".addslashes($new_row_session)."', date_rec = NOW() WHERE empr_id = ".$empr_id;
			pmb_mysql_query($sql_update);
		}
		return true;
	}

	public function list_shelves($session_id, $filter = 0) {
		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		$filter = intval($filter);
		return $this->proxy_parent->pmbesOPACGeneric_list_shelves($empr_id, $filter);
	}

	public function retrieve_shelf_content($session_id, $shelf_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesOPACGeneric_retrieve_shelf_content($shelf_id, $empr_id);
	}

	public function simpleSearch($session_id, $searchType=0,$searchTerm="") {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_simpleSearch($searchType, $searchTerm, -1, $empr_id);
	}

	public function simpleSearchLocalise($session_id, $searchType=0,$searchTerm="",$location=0,$section=0) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_simpleSearchLocalise($searchType, $searchTerm, -1, $empr_id,$location,$section);
	}

	public function get_sort_types($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return array();
		}
		return $this->proxy_parent->pmbesSearch_get_sort_types();
	}

	public function fetchSearchRecords($session_id, $searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset='iso-8859-1') {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecords($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset, true, true);
	}

	public function fetchSearchRecordsSorted($session_id, $searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset='iso-8859-1', $sort_type="") {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsSorted($searchId, $firstRecord, $recordCount, $recordFormat, $recordCharset, true, true, $sort_type);
	}

	public function fetchSearchRecordsArray($session_id, $searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1') {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsArray($searchId, $firstRecord, $recordCount, $recordCharset, true, true);
	}

	public function fetchSearchRecordsArraySorted($session_id, $searchId, $firstRecord, $recordCount, $recordCharset='iso-8859-1', $sort_type="") {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsArraySorted($searchId, $firstRecord, $recordCount, $recordCharset, true, true, $sort_type);
	}

	public function getAdvancedSearchFields($session_id, $fetch_values=false) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		$empr = new emprunteur($empr_id);
		$lang = $empr->empr_lang;

		return $this->proxy_parent->pmbesSearch_getAdvancedSearchFields("opac|search_fields", $lang, $fetch_values);
	}

	public function getAdvancedExternalSearchFields($session_id, $fetch_values=false) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}$empr = new emprunteur($empr_id);
		$lang = $empr->empr_lang;

		return $this->proxy_parent->pmbesSearch_getAdvancedSearchFields("opac|search_fields_unimarc", $lang, $fetch_values);
	}

	public function advancedSearch($session_id, $search_description) {


		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_advancedSearch("opac|search_fields", $search_description, -1, $empr_id);
	}

	public function advancedSearchExternal($session_id, $search_description, $source_ids) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		array_walk($source_ids, function(&$a) {$a = intval($a);}); //Soyons sûr de ne stocker que des entiers dans le tableau.
		$source_ids = array_unique($source_ids);
		if (!$source_ids) {
			return FALSE;
		}

		return $this->proxy_parent->pmbesSearch_advancedSearch("opac|search_fields_unimarc|sources(".implode(',',$source_ids).")", $search_description, -1, 0);
	}

	public function fetch_notice_items($session_id, $notice_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesItems_fetch_notice_items($notice_id, $empr_id);
	}

	public function fetch_item($session_id, $item_cb='', $item_id='') {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesItems_fetch_item($item_cb, $item_id, $empr_id);
	}

	public function listNoticeExplNums($session_id, $notice_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_listNoticeExplNums($notice_id, $empr_id);
	}

	public function listBulletinExplNums($session_id, $bulletinId) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_listBulletinExplNums($bulletinId, $empr_id);
	}

	public function fetchNoticeList($session_id, $noticelist, $recordFormat, $recordCharset, $nbResa=false) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		if (!is_array($noticelist)) {
			return [];
		}
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchNoticeList($noticelist, $recordFormat, $recordCharset, true, true, false, $nbResa);
	}

	public function fetchExternalNoticeList($session_id, $noticelist, $recordFormat, $recordCharset) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesNotices_fetchExternalNoticeList($noticelist, $recordFormat, $recordCharset);
	}

	public function fetchNoticeListFull($session_id, $noticelist, $recordFormat, $recordCharset, $includeLinks, $nbResa=false) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		if (!is_array($noticelist)) {
			return [];
		}
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		if (!$noticelist) {
			return [];
		}
		$results = $this->proxy_parent->pmbesNotices_fetchNoticeListFull($noticelist, $recordFormat, $recordCharset, $includeLinks, $nbResa);

		return $results;
	}

	public function fetchNoticeByExplCb($session_id, $expl_cb, $recordFormat, $recordCharset) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchNoticeByExplCb($empr_id,$expl_cb, $recordFormat, $recordCharset, true, true);
	}

	public function findNoticeBulletinId($session_id,$noticeId) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_findNoticeBulletinId($noticeId);
	}

	public function get_author_information_and_notices($session_id, $author_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesAuthors_get_author_information_and_notices($author_id, $empr_id);
	}

	public function get_collection_information_and_notices($session_id, $collection_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesCollections_get_collection_information_and_notices($collection_id, $empr_id);
	}

	public function get_subcollection_information_and_notices($session_id, $subcollection_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesCollections_get_subcollection_information_and_notices($subcollection_id, $empr_id);
	}

	public function get_publisher_information_and_notices($session_id, $publisher_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesPublishers_get_publisher_information_and_notices($publisher_id, $empr_id);
	}

	public function list_thesauri($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesThesauri_list_thesauri($empr_id);
	}

	public function fetch_thesaurus_node_full($session_id,$node_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesThesauri_fetch_node_full($node_id, $empr_id);
	}

	public function self_checkout($session_id,$expl_cb){

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		return $this->proxy_parent->pmbesSelfServices_self_checkout($expl_cb,$empr_id);
	}

	public function self_checkin($session_id,$expl_cb){

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		return $this->proxy_parent->pmbesSelfServices_self_checkin($expl_cb,$empr_id);
	}

	public function self_renew($session_id,$expl_cb){

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		return $this->proxy_parent->pmbesSelfServices_self_renew($expl_cb,$empr_id, 1);
	}

	public function fetchNoticesCollstates($session_id,$serialIds){

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchNoticesCollstates($serialIds,$empr_id);
	}

	public function fetch_notices_bulletins($session_id,$noticelist){

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetch_notices_bulletins($noticelist,$empr_id);
	}

	public function fetchNoticeListFullWithBullId($session_id, $noticelist, $recordFormat, $recordCharset, $includeLinks=true, $nbResa=false) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		if (!is_array($noticelist)) {
			return [];
		}
		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		if (!$noticelist) {
			return [];
		}
		$results = $this->proxy_parent->pmbesNotices_fetchNoticeListFullWithBullId($noticelist, $recordFormat, $recordCharset, $includeLinks, $nbResa);

		return $results;
	}

	public function fetchNoticesBulletinsList($session_id,$noticelist){

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchNoticesBulletinsList($noticelist,$empr_id);
	}

	public function fetchSearchRecordsFull($session_id, $searchId, $firstRecord, $recordCount,  $recordCharset='iso-8859-1') {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFull($searchId, $firstRecord, $recordCount,  $recordCharset, true, true);
	}

	public function fetchSearchRecordsFullSorted($session_id, $searchId, $firstRecord, $recordCount,  $recordCharset='iso-8859-1', $sort_type="") {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullSorted($searchId, $firstRecord, $recordCount,  $recordCharset, true, true, $sort_type);
	}

	public function fetchSearchRecordsFullWithBullId($session_id, $searchId, $firstRecord, $recordCount,  $recordCharset='iso-8859-1') {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullWithBullId($searchId, $firstRecord, $recordCount,  $recordCharset, true, true);
	}

	public function fetchSearchRecordsFullWithBullIdSorted($session_id, $searchId, $firstRecord, $recordCount,  $recordCharset='iso-8859-1', $sort_type="") {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		return $this->proxy_parent->pmbesSearch_fetchSearchRecordsFullSorted($searchId, $firstRecord, $recordCount,  $recordCharset, true, true, $sort_type);
	}

	public function fetchSerialList($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchSerialList($empr_id);
	}

	public function listExternalSources($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		return $this->proxy_parent->pmbesSearch_listExternalSources($empr_id);
	}

	public function fetchBulletinListFull($session_id,$bulletinlist, $recordFormat, $recordCharset, $nbResa=false) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage

		return $this->proxy_parent->pmbesNotices_fetchBulletinListFull($bulletinlist, $recordFormat, $recordCharset, $nbResa);
	}

	public function getReadingLists($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$sql = "select * from opac_liste_lecture where num_empr='".$empr_id."'";
		$res = pmb_mysql_query($sql);
		$empr = new emprunteur($empr_id);

		$results = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
		    $notices = array();
		    $notices_create_date = array();
		    $query_notices = "select * from opac_liste_lecture_notices where opac_liste_lecture_num=" . $row['id_liste'];
		    $result_notices = pmb_mysql_query($query_notices);
		    if (pmb_mysql_num_rows($result_notices)) {
		        while ($row_notices = pmb_mysql_fetch_object($result_notices)) {
		            $notices[] = $row_notices->opac_liste_lecture_notice_num;
		            $notices_create_date[$row_notices->opac_liste_lecture_notice_num] = $row_notices->opac_liste_lecture_create_date;
		        }
		    }
			$aresult = array(
				'reading_list_id' => $row['id_liste'],
				'reading_list_name' => encoding_normalize::utf8_normalize($row['nom_liste']),
				'reading_list_caption' => encoding_normalize::utf8_normalize($row['description']),
				'reading_list_emprid' => $row['num_empr'],
				'reading_list_empr_caption' => encoding_normalize::utf8_normalize($empr->nom." ".$empr->prenom),
				'reading_list_confidential' => $row['confidential'],
				'reading_list_public' => $row['public'],
				'reading_list_readonly' => $row['read_only'],
			    'reading_list_notice_ids' => explode(',', $notices),
			    'reading_list_notice_create_date' => $notices_create_date,
			);
			$results[] = $aresult;
		}
		return $results;
	}

	public function getPublicReadingLists($session_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$sql = "select opac_liste_lecture.*, empr.empr_prenom, empr.empr_nom from opac_liste_lecture left join empr on empr.id_empr = opac_liste_lecture.num_empr where public=1";
		$res = pmb_mysql_query($sql);

		$results = array();
		while($row = pmb_mysql_fetch_assoc($res)) { $notices = array();
    		$notices_create_date = array();
    		$query_notices = "select * from opac_liste_lecture_notices where opac_liste_lecture_num=" . $row['id_liste'];
    		$result_notices = pmb_mysql_query($query_notices);
    		if (pmb_mysql_num_rows($result_notices)) {
    		    while ($row_notices = pmb_mysql_fetch_object($result_notices)) {
    		        $notices[] = $row_notices->opac_liste_lecture_notice_num;
    		        $notices_create_date[$row_notices->opac_liste_lecture_notice_num] = $row_notices->opac_liste_lecture_create_date;
    		    }
    		}
			$aresult = array(
				'reading_list_id' => $row['id_liste'],
				'reading_list_name' => encoding_normalize::utf8_normalize($row['nom_liste']),
				'reading_list_caption' => encoding_normalize::utf8_normalize($row['description']),
				'reading_list_emprid' => $row['num_empr'],
				'reading_list_empr_caption' => encoding_normalize::utf8_normalize($row['empr_nom']." ".$row['empr_prenom']),
				'reading_list_confidential' => $row['confidential'],
				'reading_list_public' => $row['public'],
			    'reading_list_readonly' => $row['read_only'],
			    'reading_list_notice_ids' => explode(',', $notices),
			    'reading_list_notice_create_date' => $notices_create_date,
			);
			$results[] = $aresult;
		}
		return $results;
	}

	public function addNoticesToReadingList($session_id, $list_id, $notice_ids) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$list_id = intval($list_id);
		if (!$list_id)
			return FALSE;

		if (!is_array($notice_ids))
			$notice_ids = array($notice_ids);

		$this->proxy_parent->isOPAC=true;//Je sauvegarde dans le parent que je viens de l'OPAC pour les droits d'affichage
		$this->proxy_parent->idEmpr=$empr_id;//Je sauvegarde dans le parent l'identifiant du lecteur pour les droits d'affichage
		//Vérifions que l'emprunteur a bien le droit de voir les notices
		$notice_ids=$this->filter_tabl_notices($notice_ids);

		if (!$notice_ids)
			return FALSE;

		//Vérifions que l'utilisateur a bien le droit de modifier la liste
			$sql = "select * from opac_liste_lecture where id_liste = '".$list_id."' and num_empr='".$empr_id."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res))    return FALSE;

		foreach ($notice_ids as $notice_id) {
            $query = "INSERT INTO opac_liste_lecture_notices SET opac_liste_lecture_num=". $list_id . ",opac_liste_lecture_notice_num=" . $notice_id;
            pmb_mysql_query($query);
		}
		return TRUE;
	}

	public function removeNoticesFromReadingList($session_id, $list_id, $notice_ids) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$list_id = intval($list_id);
		if (!$list_id) {
			return FALSE;
		}
		if (!$notice_ids) {
			return FALSE;
		}

		//Vérifions que l'utilisateur a bien le droit de modifier la liste
		$sql = "select * from opac_liste_lecture where id_liste = '".$list_id."' and num_empr='".$empr_id."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res)) {
			return FALSE;
		}

		$query = "DELETE FROM opac_liste_lecture_notices WHERE opac_liste_lecture_num=" . $list_id . "
            AND opac_liste_lecture_notice_num IN(" . implode(',', $notice_ids) . ")";
		pmb_mysql_query($query);

		return TRUE;
	}

	public function emptyReadingList($session_id, $list_id) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}

		$list_id = intval($list_id);
		if (!$list_id) {
			return FALSE;
		}

		//Vérifions que l'utilisateur a bien le droit de modifier la liste
		$sql = "select * from opac_liste_lecture where id_liste = '".$list_id."' and num_empr='".$empr_id."'";
		$res = pmb_mysql_query($sql);
		if (!pmb_mysql_num_rows($res))
			return FALSE;

		//Suppression dans les listes de lecture partagées
		$query = "delete from opac_liste_lecture_notices where opac_liste_lecture_num=" . $list_id;
		pmb_mysql_query($query);
		return TRUE;
	}

	public function listFacets($session_id, $searchId, $fields = array(), $filters = array()) {
		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		return $this->proxy_parent->pmbesSearch_listFacets($searchId, $fields, $filters);
	}

	public function listRecordsFromFacets($session_id, $searchId, $filters = array()) {

		$session_info = $this->retrieve_session_information($session_id);
		$empr_id = $session_info["empr_id"];
		if (!$empr_id) {
			return [];
		}
		return $this->proxy_parent->pmbesSearch_listRecordsFromFacets($searchId, $filters);
	}

	public function checkExternalAuthentication() {
		return array(
			"ext_auth" => password::check_external_authentication()
		);
	}

}
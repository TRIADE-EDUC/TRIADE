<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sessions_tokens.class.php,v 1.1 2016/10/18 07:54:03 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class sessions_tokens {
	
	/**
	 * Type de token (Information sur l'utilisation)
	 * @var string
	 */
	protected $type;
	
	/**
	 * Token
	 * @var string
	 */
	protected $token;
	
	/**
	 * SESSID
	 * @var string
	 */
	protected $SESSID;
	
	/**
	 * Login de l'utilisateur/lecteur de la session
	 * @var string
	 */
	protected $login;
	
	/**
	 * Timestamp d'expiration de la session
	 * @var timestamp
	 */
	protected $expiration;
	
	public function __construct($type) {
		$this->type = $type;
		$this->clean_sessions();
	}
	
	/**
	 * Génère un token à partir d'une méthode et d'un tableau de paramètre
	 * @param callable $callable Méthode de génération
	 * @param array $arguments Arguments
	 */
	public function generate_token($callable, $arguments = array()) {
		$this->token = call_user_func_array($callable, $arguments);
		$this->save();
		return $this->token;
	}
	
	/**
	 * Génère un token à partir du md5 des arguments transmis concaténés
	 * @param array $arguments
	 */
	public function generate_token_from_arguments($arguments) {
		$this->token = md5(implode('', $arguments));
		$this->save();
		return $this->token;
	}
	
	protected function get_token_from_SESSID() {
		$this->token = '';
		if ($this->SESSID) {
			$query = 'select sessions_tokens_token from sessions_tokens where sessions_tokens_SESSID = "'.$this->SESSID.'" and sessions_tokens_type = "'.$this->type.'"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->token = pmb_mysql_result($result, 0, 0);
			}
		}
	}
	
	protected function get_SESSID_from_token() {
		$this->SESSID = '';
		if ($this->token) {
			$query = 'select sessions_tokens_SESSID from sessions_tokens where sessions_tokens_token = "'.$this->token.'" and sessions_tokens_type = "'.$this->type.'"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->SESSID = pmb_mysql_result($result, 0, 0);
			}
		}
	}
	
	/**
	 * Enregistre le token associé à la session
	 */
	public function save() {
		if ($this->SESSID && $this->token) {
			$query = 'insert into sessions_tokens (sessions_tokens_SESSID, sessions_tokens_token, sessions_tokens_type) values ("'.$this->SESSID.'", "'.$this->token.'", "'.$this->type.'")';
			pmb_mysql_query($query);
		}
	}
	
	public function is_valid() {
		if ($this->get_login()) {
			return true;
		}
		return false;
	}
	
	protected function get_session_data() {
		global $opac_duration_session_auth;
		$this->login = '';
		$this->expiration = 0;
		$this->get_SESSID();
		if ($this->SESSID) {
			$query = 'select login, LastOn from sessions where SESSID = "'.$this->SESSID.'"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->login = $row->login;
				$this->expiration = $row->LastOn + $opac_duration_session_auth;
			}
		}
	}
	
	/**
	 * @return string Login de l'utilisateur/ Lecteur associé à la session
	 */
	public function get_login() {
		if (!isset($this->login)) {
			$this->get_session_data();
		}
		return $this->login;
	}
	
	/**
	 * @return timestamp Timestamp de l'expiration de la session
	 */
	public function get_expiration() {
		if (!isset($this->expiration)) {
			$this->get_session_data();
		}
		return $this->expiration;
	}
    
    public function get_token() {
    	if (!isset($this->token)) {
	    	$this->get_token_from_SESSID();
    	}
    	return $this->token;
    }

    public function get_SESSID() {
    	if (!isset($this->SESSID)) {
    		$this->get_SESSID_from_token();
    	}
        return $this->SESSID;
    }

    public function get_type() {
        return $this->type;
    }

    public function set_token($token) {
        $this->token = $token;
        return $this;
    }

    public function set_SESSID($SESSID) {
        $this->SESSID = $SESSID;
        return $this;
    }
    
    /**
     * Nettoie les session OPAC expirées
     */
    protected function clean_sessions() {
		global $opac_duration_session_auth;
		
		// heure courante moins une heure
		$time_out = time() - $opac_duration_session_auth;
	
		// suppression des sessions inactives
		$query = 'delete from sessions where LastOn < '.$time_out.' and SESSNAME = "PmbOpac"';
		pmb_mysql_query($query);
		
		// Suppression des tokens liés à des sessions supprimées
		$query = 'delete from sessions_tokens where sessions_tokens_SESSID not in (select SESSID from sessions)';
		pmb_mysql_query($query);
    }
}
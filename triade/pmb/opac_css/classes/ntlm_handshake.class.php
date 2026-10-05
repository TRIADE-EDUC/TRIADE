<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ntlm_handshake.class.php,v 1.6 2019/07/11 13:01:58 btafforeau Exp $

require_once('log.class.php');

// Test
//
// session_start();
// $nh = new ntlm_handshake();
// $nh->version = 1;
// $auth = $nh->run();
// if ($auth) {
// 	print "You are authenticated<br />";
// } else {
// 	print "You are not authenticated<br />";
// }
// highlight_string(print_r($nh->auth,true));




class ntlm_handshake {
	
	public $targetname = 'testwebsite';
	public $domain = 'testdomain';
	public $computer = 'mycomputer';
	public $dnsdomain = 'testdomain.local';
	public $dnscomputer = 'mycomputer.local';
	public $workstation = '';
	
	public $version = 1;
	public $v2_only = true;
	
	public $headers = array();
	public $auth_header = null;
	public $msg = '';
	public $msg2 = '';
	
	public $fail_msg = '<h1>Authentication Required</h1>';
	
	public $auth = array();
	public $clientblob = '';
	public $clientblobhash = '';
	
	public $ntlm_hosts = array();		//plages d'adresses IP pour lesquelles une authentification NTLM est possible
	public $http_proxies = array();		//proxies http
	public $ntlm_check=true;			//vérification NTLM ?
	public $ntlm_check_ip = false;		//vérification de l'adresse IP
	public $log = false;
		
	
	public function __construct () {
	}
	
	
	public function run() {
		$this->ntlm_prompt();		
		return $this->auth['authenticated'];	
	
	}
	
	
	//définition d'un log.
	public function set_log($log=false, $log_file='', $log_format='text', $log_now=false, $log_reset=true) {
		$this->log = $log;
		if ($this->log) {
			log::$log_file=$log_file;
			log::$log_format=$log_format;
			log::$log_now=$log_now;
			if ($log_reset) log::reset();
		}
	}
	
	
	public function set_ntlm_hosts($ntlm_hosts=array(), $http_proxies=array()) {
		$this->ntlm_hosts = $ntlm_hosts;
		$this->http_proxies = $http_proxies;	
		$this->ntlm_check_ip = true;
	}
	
	
	public function check_ip() {
		
		if ($this->ntlm_check_ip) {
			$remote_addr = $_SERVER['REMOTE_ADDR'];
			if (in_array($remote_addr,$this->http_proxies)) {
				$remote_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			$this->ntlm_check = false;
			foreach($this->ntlm_hosts as $ntlm_host) {
				if(stripos($remote_addr,$ntlm_host)===0) {
					$this->ntlm_check = true;
					break;
				}
			}
		} 		
	}
	
	
	public function ntlm_prompt() {
		
		$this->check_ip();
		if (!$this->ntlm_check) return;	
		
		$this->auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
		if ($this->log) {
			if ($this->auth_header == null) {
				log::print_message('HTTP_AUTHORIZATION non défini.');
			} else {
				log::print_message('HTTP_AUTHORIZATION = ');
				log::print_message($this->auth_header);
			}
		}
		
		if ($this->auth_header == null && function_exists('getallheaders')) {
			$this->headers = getallheaders();
			$this->auth_header = isset($this->headers['Authorization']) ? $this->headers['Authorization'] : null;
		}
		if ($this->log) {
			if ($this->auth_header == null) {
				log::print_message('Apache headers non définis.');
			} else {
				log::print_message('Apache headers = ');
				log::print_message($this->headers);
				log::print_message('auth_header = ');
				log::print_message($this->auth_header);
				log::print_message(bin2hex(base64_decode($this->auth_header)));
			}
		}
		
		if (isset($_SESSION['_ntlm_auth'])) {
			if ($this->log) {
				log::print_message('_ntlm_auth = ');
				log::print_message($_SESSION['_ntlm_auth']);
			}
			$this->auth = $_SESSION['_ntlm_auth'];
			//return ;
		}
		
		if (!$this->auth_header) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: NTLM');
			print $this->fail_msg;
			if ($this->log) {
				log::print_message("Pas de headers.");
				log::print_message($this->fail_msg);
				log::print_message("Envoi header 'HTTP/1.1 401 Unauthorized'");
				log::print_message("Envoi header 'WWW-Authenticate: NTLM'");
			}
			exit;
		}

		if (substr($this->auth_header,0,5) == 'NTLM ') {
			
			$this->msg = base64_decode(substr($this->auth_header, 5));
			
			if (substr($this->msg, 0, 8) != "NTLMSSP\x00") {
				if ($this->log) {
					log::print_message("Header NTLM non reconnus.");
				}
				die();
			}

			if (substr($this->msg, 8, 1) == "\x01") {
				
				if ($this->version==1) {
					$this->msg2 = "NTLMSSP\x00\x02\x00\x00\x00\x00\x00\x00";
					$this->msg2.= "\x00\x28\x00\x00\x00\x01\x82\x00\x00";
					$this->msg2.= "\x00\x02\x02\x02\x00\x00\x00\x00\x00";
					$this->msg2.= "\x00\x00\x00\x00\x00\x00\x00";
					header('HTTP/1.1 401 Unauthorized');
					header('WWW-Authenticate: NTLM '.trim(base64_encode($this->msg2)));
					if ($this->log) {
						log::print_message("Envoi header 'HTTP/1.1 401 Unauthorized'");
						log::print_message("Envoi header 'WWW-Authenticate: NTLM'");
						log::print_message(bin2hex($this->msg2));
					}
				} else if ($this->version==2) {
					$_SESSION['_ntlm_server_challenge'] = $this->ntlm_get_random_bytes(8);
					$this->msg2 = $this->ntlm_get_challenge_msg($_SESSION['_ntlm_server_challenge']);		
					header('HTTP/1.1 401 Unauthorized');
					header('WWW-Authenticate: NTLM '.trim(base64_encode($this->msg2)));
					if ($this->log) {
						log::print_message("Envoi header 'HTTP/1.1 401 Unauthorized'");
						log::print_message("Envoi header 'WWW-Authenticate: NTLM'");
						log::print_message("Envoi challenge NTLM");
						log::print_message(bin2hex($this->msg2));
					}
				}
				exit;
				
			} else if (substr($this->msg, 8, 1) == "\x03") {
				if ($this->version==1) {
					$this->auth = $this->ntlm_parse_response_msg();
				} else if ($this->version==2) {
					$this->auth = $this->ntlm_parse_response_msg($_SESSION['_ntlm_server_challenge']);
					unset($_SESSION['_ntlm_server_challenge']);
					if (!$this->auth['authenticated']) {
						header('HTTP/1.1 401 Unauthorized');
						header('WWW-Authenticate: NTLM');
						print $this->fail_msg;
						print $this->auth['error'];
						if ($this->log) {
							log::print_message("Envoi header 'HTTP/1.1 401 Unauthorized'");
							log::print_message("Envoi header 'WWW-Authenticate: NTLM'");
							log::print_message($this->fail_msg);
							log::print_message($this->auth['error']);
						}
						
						exit;
					}
				}
				$_SESSION['_ntlm_auth'] = $this->auth;
				return ;
			}
		} else {
			if($this->log) {
				log::print_message("Pas de header NTLM.");
			}
		}
	}
	
	
	public function ntlm_utf8_to_utf16le($str) {
		return iconv('UTF-8', 'UTF-16LE', $str);
	}

	
	public function ntlm_md4($s) {
		if (function_exists('mhash')) {
			return mhash(MHASH_MD4, $s);
		}
		return pack('H*', hash('md4', $s));
	}
	
	
	public function ntlm_av_pair($type, $utf16) {
		return pack('v', $type).pack('v', strlen($utf16)).$utf16;
	}
	
	
	public function ntlm_field_value($start, $decode_utf16 = true) {
		$len = (ord(substr($this->msg, $start+1, 1)) * 256) + ord(substr($this->msg, $start, 1));
		$off = (ord(substr($this->msg, $start+5, 1)) * 256) + ord(substr($this->msg, $start+4, 1));
		$result = substr($this->msg, $off, $len);
		if ($decode_utf16) {
			$result = iconv('UTF-16LE', 'UTF-8', $result);
		}
		return $result;
	}
	
	
	public function ntlm_hmac_md5($key) {
		$blocksize = 64;
		if (strlen($key) > $blocksize) {
			$key = pack('H*', md5($key));
		}
		$key = str_pad($key, $blocksize, "\0");
		$ipadk = $key ^ str_repeat("\x36", $blocksize);
		$opadk = $key ^ str_repeat("\x5c", $blocksize);
		return pack('H*', md5($opadk.pack('H*', md5($ipadk.$this->msg))));
	}
	
	
	public function ntlm_get_random_bytes($length) {
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= chr(rand(0, 255));
		}
		return $result;
	}
	
	
	public function ntlm_get_challenge_msg($challenge='') {
		
		$this->domain = $this->ntlm_field_value(16);
		$ws = $this->ntlm_field_value(24);
		$tdata = $this->ntlm_av_pair(2, $this->ntlm_utf8_to_utf16le($this->domain)).$this->ntlm_av_pair(1, $this->ntlm_utf8_to_utf16le($this->computer)).$this->ntlm_av_pair(4, $this->ntlm_utf8_to_utf16le($this->dnsdomain)).$this->ntlm_av_pair(3, $this->ntlm_utf8_to_utf16le($this->dnscomputer))."\0\0\0\0\0\0\0\0";
		$tname = $this->ntlm_utf8_to_utf16le($this->targetname);
		
		$this->msg2 = "NTLMSSP\x00\x02\x00\x00\x00".
		pack('vvV', strlen($tname), strlen($tname), 48). 					// target name len/alloc/offset
		"\x01\x02\x81\x00". 												// flags
		$challenge. 														// challenge
		"\x00\x00\x00\x00\x00\x00\x00\x00". // context
		pack('vvV', strlen($tdata), strlen($tdata), 48 + strlen($tname)).	// target info len/alloc/offset
		$tname.$tdata;
		return $this->msg2;
	}
		
	
	public function ntlm_verify_hash($challenge) {
		
		//	$md4hash = $this->get_ntlm_user_hash($this->user);
		//	if (!$md4hash) {
		//		return false;
		//	}
		//	$ntlmv2hash = ntlm_hmac_md5($md4hash, ntlm_utf8_to_utf16le(strtoupper($this->user).$this->domain));
		//	$blobhash = ntlm_hmac_md5($ntlmv2hash, $challenge.$this->clientblob);
		//
		//	echo  
		//	'domain = '.$this->domain."\r\n".
		//	'user = '.$this->user."\r\n".
		//	'challenge = '.bin2hex($challenge )."\r\n".
		//	'clientblob = '.bin2hex($this->clientblob )."\r\n".
		//	'clientblobhash = '.bin2hex($this->clientblobhash )."\r\n".
		//	'md4hash = '.bin2hex($md4hash )."\r\n".
		//	'ntlmv2hash = '.bin2hex($ntlmv2hash)."\r\n".
		//	'blobhash = '.bin2hex($blobhash)."\r\n";
		//		
		//	return ($blobhash == $this->clientblobhash);
		//return ntlm_md4(ntlm_utf8_to_utf16le('test'));
		return true;
		
	}
	
	
	public function ntlm_parse_response_msg($challenge='') {
		
		if ($this->version==1) {
			
			$this->user = $this->ntlm_field_value(36);
			$this->domain = $this->ntlm_field_value(28);
			$this->workstation = $this->ntlm_field_value(44);
			
		} else if ($this->version==2) {
			
			$this->user = $this->ntlm_field_value(36);
			$this->domain = $this->ntlm_field_value(28);
			$this->workstation = $this->ntlm_field_value(44);
			$ntlmresponse = $this->ntlm_field_value(20, false);
			//$blob = "\x01\x01\x00\x00\x00\x00\x00\x00".$timestamp.$nonce."\x00\x00\x00\x00".$tdata;
			$this->clientblob = substr($ntlmresponse, 16);
			$this->clientblobhash = substr($ntlmresponse, 0, 16);
		
			if (substr($this->clientblob, 0, 8) != "\x01\x01\x00\x00\x00\x00\x00\x00") {
				//return array('authenticated' => true, 'username' => $this->user, 'domain' => $this->domain, 'workstation' => $this->workstation);
				return array('authenticated' => false, 'error' => 'NTLMv2 response required. Please force your client to use NTLMv2.');
			}
			
			if (!$this->ntlm_verify_hash($challenge)) {
				return array('authenticated' => false, 'error' => 'Incorrect username or password.', 'username' => $this->user, 'domain' => $this->domain, 'workstation' => $this->workstation);
			}
		
		}
		return array('authenticated' => true, 'username' => $this->user, 'domain' => $this->domain, 'workstation' => $this->workstation);
	}
	
	
	public function ntlm_unset_auth() {
		unset ($_SESSION['_ntlm_auth']);
	}
	

	public function get_ntlm_user_hash() {
		//$userdb = array('loune'=>'test', 'user1'=>'password');
		//if (!isset($userdb[strtolower($this->user)]))
		//return false;
		//return ntlm_md4(ntlm_utf8_to_utf16le('test'));
		return true;
	}
	
}

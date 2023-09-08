<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: curl.class.php,v 1.20 2019-04-02 13:05:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/* Curl, CurlResponse
#
# Author Sean Huber - shuber@huberry.com
# Date May 2008
#
# A basic CURL wrapper for PHP
#
# See the README for documentation/examples or http://php.net/curl for more information about the libcurl extension for PHP
http://github.com/shuber/curl/tree/master/curl.php4
*/
	
class Curl {
	public $cookie_file;
	public $headers = array();
	public $options = array();
	public $referer = '';
	public $user_agent = '';
 	public $reponsecurl=array();
	# Protected
	public $error = '';
	public $handle;
	public $buffer="";
	
	# Variables qui empechent le dépassement mémoire
	public $limit=0;	
	public $body_overflow;
	public $timeout=0;
	
	public function __construct() {
		global $base_path;
		// initialisation des libellés de réponse
		$this->reponsecurl['N/A'] = "Ikke HTTP";
		$this->reponsecurl['OK']    = "Valid hostname";
		$this->reponsecurl['FEJL']  = "Invalid hostname";
		$this->reponsecurl['Død']   = "No response";
		$this->reponsecurl[100]   = "Continue";
		$this->reponsecurl[101]   = "Switching Protocols";
		$this->reponsecurl[200]   = "OK";
		$this->reponsecurl[201]   = "Created";
		$this->reponsecurl[202]   = "Accepted";
		$this->reponsecurl[203]   = "Non-Authoritative Information";
		$this->reponsecurl[204]   = "No Content";
		$this->reponsecurl[205]   = "Reset Content";
		$this->reponsecurl[206]   = "Partial Content";
		$this->reponsecurl[300]   = "Multiple Choices";
		$this->reponsecurl[301]   = "Moved Permanently";
		$this->reponsecurl[302]   = "Found";
		$this->reponsecurl[303]   = "See Other";
		$this->reponsecurl[304]   = "Not Modified";
		$this->reponsecurl[305]   = "Use Proxy";
		$this->reponsecurl[307]   = "Temporary Redirect";
		$this->reponsecurl[400]   = "Bad Request";
		$this->reponsecurl[401]   = "Unauthorized";
		$this->reponsecurl[402]   = "Payment Required";
		$this->reponsecurl[403]   = "Forbidden";
		$this->reponsecurl[404]   = "Not Found";
		$this->reponsecurl[405]   = "Method Not Allowed";
		$this->reponsecurl[406]   = "Not Acceptable";
		$this->reponsecurl[407]   = "Proxy Authentication Required";
		$this->reponsecurl[408]   = "Request Timeout";
		$this->reponsecurl[409]   = "Conflict";
		$this->reponsecurl[410]   = "Gone";
		$this->reponsecurl[411]   = "Length Required";
		$this->reponsecurl[412]   = "Precondition Failed";
		$this->reponsecurl[413]   = "Request Entity Too Large";
		$this->reponsecurl[414]   = "Request-URI Too Long";
		$this->reponsecurl[415]   = "Unsupported Media Type";
		$this->reponsecurl[416]   = "Requested Range Not Satisfiable";
		$this->reponsecurl[417]   = "Expectation Failed";
		$this->reponsecurl[500]   = "Internal Server Error";
		$this->reponsecurl[501]   = "Not Implemented";
		$this->reponsecurl[502]   = "Bad Gateway";
		$this->reponsecurl[503]   = "Service Unavailable";
		$this->reponsecurl[504]   = "Gateway Timeout";
		$this->reponsecurl[505]   = "HTTP Version Not Supported";
		
		if(isset($base_path) && $base_path){
			$this->cookie_file = $base_path.'/temp/curl_cookie.txt';
		}else{
			$this->cookie_file = realpath('.').'/curl_cookie.txt';
		}
		$this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ?
			$_SERVER['HTTP_USER_AGENT'] :
			'Curl/PHP ' . PHP_VERSION . ' (http://github.com/shuber/curl/)';
	}
	  
	public function delete($url, $vars = array()) {
		return $this->request('DELETE', $url, $vars);
	}
	
	public function error() {
		return $this->error;
	}
	
	public function get($url, $vars = array()) {
		$this->buffer="";
		if (!empty($vars)) {
			$url .= (stripos($url, '?') !== false) ? '&' : '?';
			$url .= http_build_query($vars, '', '&');
		}
		return $this->request('GET', $url);
	}
	
	public function post($url, $vars = array()) {
		return $this->request('POST', $url, $vars);
	}
	
	public function put($url, $vars = array()) {
		return $this->request('PUT', $url, $vars);
	}
	
	public function getBodyOverflow($curl,$contenu) {
		$taille_max  = $this->limit;
		$taille_bloc = strlen($contenu);
		if (strlen($this->body_overflow)+$taille_bloc<$taille_max) $this->body_overflow .= $contenu;	
		return strlen($contenu);
	}
	
	public function saveBodyInFile($curl,$contenu) {
		if(!$this->header_detect) {
			$this->buffer.=$contenu;
			$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
			if (preg_match($pattern,$this->buffer)) {
				$texte = preg_replace($pattern, '', $this->buffer);
				$this->header_detect=1;
			} else {
				$texte = '';
			}
		} else $texte=$contenu;
		if($texte) {
			$fd = fopen($this->save_file_name,"a");
			fwrite($fd,$texte);
			fclose($fd);	
		}	
		return strlen($contenu);
	}
	
	# Protected
	public function request($method, $url, $vars = array()) {
		
		$this->handle = curl_init();
		
		# Set some default CURL options
		if ($this->timeout) {
			curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, $this->timeout);
			curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->timeout);
		}
		curl_setopt($this->handle, CURLOPT_COOKIEFILE, $this->cookie_file);
		curl_setopt($this->handle, CURLOPT_COOKIEJAR, $this->cookie_file);
		@curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->handle, CURLOPT_HEADER, true);
		curl_setopt($this->handle, CURLOPT_POSTFIELDS, (is_array($vars) ? http_build_query($vars, '', '&') : $vars));
		curl_setopt($this->handle, CURLOPT_REFERER, $this->referer);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handle, CURLOPT_URL, str_replace(" ","%20",preg_replace("/#.*$/","",$url)));
		/*On supprime ce qui suit le # car c'est une ancre pour le navigateur et avec on consière la validation fausse alors qu'elle est bonne
		 *On remplace les espaces par %20 pour la même raison
		 */
		curl_setopt($this->handle, CURLOPT_USERAGENT, $this->user_agent);		
		if($this->limit) 
			curl_setopt($this->handle, CURLOPT_WRITEFUNCTION,array(&$this,'getBodyOverflow'));
		
		if(isset($this->save_file_name) && $this->save_file_name){
			$this->header_detect=0;					
			curl_setopt($this->handle, CURLOPT_WRITEFUNCTION,array(&$this,'saveBodyInFile'));
		}	
		configurer_proxy_curl($this->handle,str_replace(" ","%20",preg_replace("/#.*$/","",$url)));			
		
		# Format custom headers for this request and set CURL option
		$headers = array();
		foreach ($this->headers as $key => $value) {
			$headers[] = $key.': '.$value;
		}
		
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
		
		# Determine the request method and set the correct CURL option
		switch ($method) {
			case 'GET':
				curl_setopt($this->handle, CURLOPT_HTTPGET, true);
				break;
			case 'POST':
				curl_setopt($this->handle, CURLOPT_POST, true);
				break;
			default:
				curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
		}
		
		# Set any custom CURL options
		foreach ($this->options as $option => $value) {
			curl_setopt($this->handle, constant('CURLOPT_'.str_replace('CURLOPT_', '', strtoupper($option))), $value);
		}

		$this->body_overflow="";		
		$response = curl_exec($this->handle);
		if($this->limit) $response=$this->body_overflow;
		
		if ($response) {
			$response = new CurlResponse($response);
		} else {
			$this->error = curl_errno($this->handle).' - '.curl_error($this->handle);
		}
		curl_close($this->handle);
		
		return $response;
	}
	
	public function set_option($option, $value) {
		$this->options[$option] = $value;
	}
}
 
class CurlResponse {
	public $body = '';
	public $headers = array();
	
	public function __construct($response) {
		# Extract headers from response
		$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
		preg_match_all($pattern, $response, $matches);
		$headers = explode("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));
		
		# Extract the version and status from the first header
		$version_and_status = array_shift($headers);
		preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
		$this->headers['Http-Version'] = $matches[1];
		$this->headers['Status-Code'] = $matches[2];
		$this->headers['Status'] = $matches[2].' '.$matches[3];
		
		# Convert headers into an associative array
		foreach ($headers as $header) {
			preg_match('#(.*?)\:\s(.*)#', $header, $matches);
			$this->headers[$matches[1]] = $matches[2];
		}
		
		# Remove the headers from the response body
		$this->body = preg_replace($pattern, '', $response);
	}
	
	public function __toString() {
		return $this->body;
	}
 
}
		

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: multicurl.class.php,v 1.3 2021/04/21 07:14:19 jlaurent Exp $

class multicurl {
    
    const MODE_MULTI = 0;
    const MODE_MONO = 1;
    const MODES_AVAILABLE = [
        multicurl::MODE_MULTI,
        multicurl::MODE_MONO,
    ];
    protected $mode = multicurl::MODE_MULTI;

    const MAX_QUERIES = 100;
    protected $max_queries = multicurl::MAX_QUERIES;
    
	protected $multi_handler = null;
	protected $curl_handlers = [];
	protected $requests = [];
	protected $responses = [];
	protected $buffers = [];
	protected $overflows = [];
	
	const TEMP_DIR_DEFAULT = __DIR__.'/../temp/';
	protected $temp_dir = multicurl::TEMP_DIR_DEFAULT;
	
	const CURL_OPTIONS_DEFAULT = [
	    
	    CURLOPT_RETURNTRANSFER     => true,
	    CURLOPT_FOLLOWLOCATION     => true,
	    CURLOPT_MAXREDIRS          => 10,
	    CURLOPT_AUTOREFERER        => true,
	    CURLOPT_HEADER             => true,
	    CURLOPT_HTTPHEADER         => [
 	        'Connection: keep-alive',
	    ],
	    CURLOPT_USERAGENT          => 'Multicurl/1.0',	    
	    CURLOPT_CONNECTTIMEOUT     => 5,
	    CURLOPT_TIMEOUT            => 30,
	    
 	    CURLOPT_SSL_VERIFYPEER     => true,
 	    CURLOPT_SSL_VERIFYSTATUS   => false,
 	    CURLOPT_SSL_VERIFYHOST     => 2,
	    
	    CURLOPT_COOKIEFILE         => multicurl::TEMP_DIR_DEFAULT.'multicurl_cookies.txt',
	    CURLOPT_COOKIEJAR          => multicurl::TEMP_DIR_DEFAULT.'multicurl_cookies.txt',
	    
	    CURLOPT_VERBOSE            => false,
	];
	protected $curl_options = multicurl::CURL_OPTIONS_DEFAULT;
	
	
	const MULTICURL_OPTIONS_DEFAULT = [
		CURLMOPT_MAX_HOST_CONNECTIONS	=> 10,
	];
	protected $multicurl_options = multicurl::MULTICURL_OPTIONS_DEFAULT;
	
	
	const RESPONSE_MAX_SIZE_DEFAULT = 0;
	protected $response_max_size = multicurl::RESPONSE_MAX_SIZE_DEFAULT;
			
	static $external_configure_function = null;
	
    /**
     * 
     * @param int $mode
     */
	public function __construct($mode = multicurl::MODE_MULTI) {
	    $this->set_mode($mode);
	}
	
	
	/**
	 * reset objet
	 */
	public function reset() {

	    $this->mode = multicurl::MODE_MULTI;
	    $this->multi_handler = null;
	    $this->curl_handlers = [];
	    $this->requests = [];
	    $this->responses = [];
	    $this->buffers = [];
	    $this->overflows = [];
	    $this->temp_dir = multicurl::TEMP_DIR_DEFAULT;
	    $this->curl_options = multicurl::CURL_OPTIONS_DEFAULT;
	    $this->response_max_size = multicurl::RESPONSE_MAX_SIZE_DEFAULT;
	    
	}
		
	
	/**
	 * 
	 * @param int $mode
	 * 
	 * @return mixed int | false
	 */
	public function set_mode($mode) {
	    
	    if( !in_array($mode, multicurl::MODES_AVAILABLE) ) {
	        return false;
	    }
	    $this->mode = $mode;
	    return $this->mode;
	}
	
	
	/**
	 *
	 * @return int
	 */
	public function get_mode() {
	    return $this->mode;
	}
 
	
	/**
	 * 
	 * @param string $dir_path
	 * 
	 * @return bool
	 */
	public function set_temp_dir($dir_path) {
	    
	    if(!is_string($dir_path) || is_empty($dir_path)) {
	        return false;
	    }
	    if(!is_dir($dir_path)) {
	        return false;
	    }
	    if(!is_writable($dir_path)) {
	        return false;
	    }
	    $last_char = substr($dir_path, -1);
	    if($last_char != '/')  {
	        $dir_path.= '/';
	    }
	    $this->temp_dir = $dir_path;
	    $this->curl_options[CURLOPT_COOKIEFILE] = $this->temp_dir.'multicurl_cookies.txt';
	    $this->curl_options[CURLOPT_COOKIEJAR] = $this->temp_dir.'multicurl_cookies.txt';
	    return true;
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function get_temp_dir() {
	    return $this->temp_dir;
	}
	
	
	/**
	 * 
	 * @param array $options
	 * 
	 * @return boolean
	 */
	public function set_curl_options($options) {
	    
	    if(!is_array($options)) {
	        return false;
	    }
        foreach($options as $k=>$v) {
            $this->curl_options[$k] = $v;
        }
	    return true;
	}
	
		    
    /**
	 * 
	 * @return array
	 */
	public function get_curl_options() {
	   
	    return $this->curl_options;
	}
	
	
	/**
	 *
	 * @param array $options
	 *
	 * @return boolean
	 */
	public function set_multicurl_options($options) {
		
		if(!is_array($options)) {
			return false;
		}
		foreach($options as $k=>$v) {
			$this->multicurl_options[$k] = $v;
		}
		return true;
	}
	
	
	/**
	 *
	 * @return array
	 */
	public function get_multicurl_options() {
		
		return $this->multicurl_options;
	}
	
	
	/**
	 *
	 * @return boolean
	 */
	public function apply_multicurl_options() {
		
		if(is_null($this->multi_handler)) {
			return false;
		}
		if(empty($this->multicurl_options)) {
			return true;
		}
		foreach($this->multicurl_options as $k=>$v) {
			curl_multi_setopt($this->multi_handler, $k, $v);
		}
		return true;
	}
		
	
	/**
	 * 
	 * @param int $response_max_size
	 * 
	 * @return bool|int
	 */
	public function set_response_max_size($response_max_size) {
	    if(!is_numeric($response_max_size)) {
	        return false;
	    }
	    $this->response_max_size = intval($response_max_size);
	    return $this->response_max_size;
	}
	
	
	/**
	 * 
	 * @return int
	 */
	public function get_response_max_size() {
	    return $this->response_max_size;
	}
	
	
	/**
	 * 
	 * @param string $url
	 * @param array $vars
	 * @param string $file
	 * 
	 * return void
	 */
	public function add_get($url, $vars = [], $options = [], $file = '') {
	    
	    if(!is_array($vars)) {
	        $vars = [];
	    }
	    if (!empty($vars)) {
	        $url .= (stripos($url, '?') !== false) ? '&' : '?';
	        $url .= http_build_query($vars, '', '&');
	    }
	    $options[CURLOPT_HTTPGET] = true;
	    $this->add_request($url, $options, $file);
	}
	
	
	/**
	 * 
	 * @param string $url
	 * @param array|string $vars
	 * @param string $file
	 * 
	 * return void
	 */
	public function add_post($url, $vars = mixed, $options = [], $file = '') {
	    
	   if (is_array($vars) && !empty($vars)) {
	        $vars = http_build_query($vars, '', '&');
	    }
	    if(!is_array($options)) {
	        $options = [];
	    }
	    $options[CURLOPT_POST] = true;
	    $options[CURLOPT_POSTFIELDS] = $vars;
	    $this->add_request($url, $options, $file);
	}
		
	
	/**
	 * 
	 * @param string $url
	 * @param array $options
	 * @param string $file
	 * 
	 * @return int
	 */
	protected function add_request($url, $options = [], $file = '') {
	    
	    $k = count($this->requests);
	     //On supprime ce qui suit le # car c'est une ancre pour le navigateur et avec on considere la validation fausse alors qu'elle est bonne
	     //On remplace les espaces par %20 pour la meme raison
	    $url = str_replace(" ","%20",preg_replace("/#.*$/","",$url));
	    
	    if(empty($file) || !is_string($file)) {
	        $file = '';
	    }
	    if($file) {
	        $file = $this->temp_dir.$file;
	        @unlink($file);
	    }
	    $this->requests[$k] = [
	        'url'      => $url,
	        'options'  => $options,
	        'file'     => $file,
	    ];
	   
	    return $k;
	}
	
	
	/**
	 * 
	 * @param int $k
	 * 
	 * @return array
	 */
	public function get_request($k) {
	    $k = intval($k);
	    if(array_key_exists($k, $this->requests)){
	        return $this->requests[$k];
	    }
	    return [];
	}
	
	
    /**
     * 
     * @return array
     */
	public function get_requests() {
        return $this->requests;
	}
        
	
    /**
     * 
	 * @return void
	 */
	public function run() {
	    
	    switch ($this->mode) {
	        
	        case multicurl::MODE_MONO :
	            
	            foreach($this->requests as $k=>$request) {
	                
	                $this->curl_handlers[$k] = curl_init($request['url']);
	                $this->call_external_configure_function($this->curl_handlers[$k], $request['url']);
	                curl_setopt_array($this->curl_handlers[$k], $this->curl_options);
	                curl_setopt_array($this->curl_handlers[$k], $this->requests[$k]['options']);
	                $this->get_callback_overflow_function($k);
	                $this->get_callback_write_function($k);
	                $this->responses[$k] = $this->parse_response($k, curl_exec($this->curl_handlers[$k]));
	                curl_close($this->curl_handlers[$k]);
	            }
	            break;
	            
	        default :
	        case multicurl::MODE_MULTI :
	        	
	            $this->multi_handler = curl_multi_init();
	            $this->apply_multicurl_options();
	            foreach($this->requests as $k=>$request) {
	                
	                $this->curl_handlers[$k] = curl_init($request['url']);
	                $this->call_external_configure_function($this->curl_handlers[$k], $request['url']);
	                curl_setopt_array($this->curl_handlers[$k], $this->curl_options);
	                curl_setopt_array($this->curl_handlers[$k], $this->requests[$k]['options']);
	                $this->get_callback_overflow_function($k);
	                $this->get_callback_write_function($k);
	                curl_multi_add_handle($this->multi_handler, $this->curl_handlers[$k]);
	            }

//-----------------------------------
	            
	            $still_running = 0;
	            do {
	                $status = curl_multi_exec($this->multi_handler, $still_running);
	                if ($still_running) {
	                    curl_multi_select($this->multi_handler);
	                    
	                }
	                $info = curl_multi_info_read($this->multi_handler);
	                if (false !== $info) {
	                	if($info['result']!==CURLE_OK) {
	                		var_dump($info,  'ERROR= '.$info['result'].' - '.curl_strerror($info['result']));
	                	}
	                }
	            } while ($still_running && $status == CURLM_OK);

	            foreach($this->curl_handlers as $k=>$curl_handler){
	                $this->responses[$k] = $this->parse_response($k, curl_multi_getcontent($curl_handler));
	                curl_multi_remove_handle($this->multi_handler, $curl_handler);
	            }
	            
	            curl_multi_close($this->multi_handler);

 	            break;
	    }
	}
	
	public function output($output) {
		var_dump($output);
	}
	
	/**
	 * 
	 * @param int $k
	 * @param string $response
	 * @return array
	 */
	protected function parse_response ($k, $response) {
	    
	    if($this->response_max_size) {
	        $response = $this->overflows[$k];
	    }
	    $result = [
	        'id'       => 0,
	        'headers'  => [],
	        'body'     => '',
	    ];
	    $result['id'] = $k;

	    # Extract headers from response
	    $pattern = '#HTTP/\d\.{0,2}?\d{0,1}?.*?$.*?\r\n\r\n#ims';
	    $matches = [];
	    preg_match_all($pattern, $response, $matches);
	    $headers = explode("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));
	    
	    # Extract the version and status from the first header
	    $version_and_status = array_shift($headers);
	    preg_match('#HTTP/(\d\.{0,2}?\d{0,1}?)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
	    if(!empty($matches)) {
    	    $result['headers']['Http-Version'] = $matches[1];
    	    $result['headers']['Status-Code'] = $matches[2];
    	    $result['headers']['Status'] = $matches[2].' '.$matches[3];
	    }
	    # Convert headers into an associative array
	    foreach ($headers as $header) {
	        preg_match('#(.*?)\:\s(.*)#', $header, $matches);
	        $result['headers'][$matches[1]] = $matches[2];
	    }
	    
	    # Remove the headers from the response body
	    $result['body'] = preg_replace($pattern, '', $response);
	    
	    return $result;
	}
	
	
	/**
	 *
	 * @param int $k
	 * 
	 * @return array
	 */
	public function get_response($k) {
	    $k = intval($k);
	    if(array_key_exists($k, $this->responses)){
	        return $this->responses[$k];
	    }
	    return [];
	}
	
	/**
	 * 
	 * @return array
	 */
	public function get_responses() {
        return $this->responses;
	}
		
    /**
     * 
     * @param int $k
     * 
     * @return void
     */
	protected function get_callback_write_function($k) {
	    if(!empty($this->requests[$k]['file'])) {
	        $callback = $this->get_write_function($k);
	        curl_setopt($this->curl_handlers[$k], CURLOPT_WRITEFUNCTION, $callback);
	    }
	}
	
	/**
	 *
	 * @param int $k
	 * 
	 * @return Closure
	 */
	protected function get_write_function($k){
	    
	    $this->buffers[$k]['content'] = '';
	    $this->buffers[$k]['header_detected'] = 0;
	    $obj = $this;
	    
	    $write_function = function ($curl_handler, $content) use ($obj, $k){
	        
	        $to_file = '';
	        if(!$obj->buffers[$k]['header_detected']) {
	            $obj->buffers[$k]['content'].= $content;
	            $pattern = '#HTTP/\d\.?\d?.*?$.*?\r\n\r\n#ims';
	            if (preg_match($pattern,$obj->buffers[$k]['content'])) {
	                $to_file = preg_replace($pattern, '', $obj->buffers[$k]['content']);
	                $obj->buffers[$k]['header_detected'] = 1;
	            }
	        } else {
	            $to_file = $content;
	        }
	        
	        if($to_file) {
	            $fd = fopen($obj->requests[$k]['file'],"a");
	            fwrite($fd, $to_file);
	            fclose($fd);
	        }
	        return strlen($content);
	    };
	    
	    return $write_function;
	}
	
	
	/**
	 *
	 * @param int $k
	 * 
	 * return void
	 */
	protected function get_callback_overflow_function($k) {
	    if($this->response_max_size) {
	        $callback = $this->get_overflow_function($k);
	        curl_setopt($this->curl_handlers[$k], CURLOPT_WRITEFUNCTION, $callback);
	    }
	}
	
	/**
	 *
	 * @param int $k
	 * 
	 * @return Closure
	 */
	protected function get_overflow_function($k){
	    
	    $this->overflows[$k] = '';
	    $obj = $this;
	    
	    $overflow_function = function ($curl_handler, $content) use ($obj, $k){
	        
	        $max_size = $obj->response_max_size;
	        $content_size = strlen($content);
	        if ( (strlen($obj->overflows[$k]) + $content_size) < $max_size ) {
	            $obj->overflows[$k].= $content;
	        }
	        return strlen($content);
	    };
	    
	    return $overflow_function;
	}
	
	/**
	 * 
	 * @param string $function
	 * 
	 * @return void
	 */
	public function set_external_configure_function($function) {
	    static::$external_configure_function = $function;
	}
	
	/**
	 *
	 * @return void
	 */
	public function unset_external_configure_function() {
	    static::$external_configure_function = null;
	}
	
	/*
	 * 
	 * @return string
	 */
	public function get_external_configure_function() {
	    return static::$external_configure_function;
	}
	
	protected function call_external_configure_function($handler, $url){
	    if(is_null(static::$external_configure_function)) {
	        return false;
	    }
	    if(!function_exists(static::$external_configure_function)) {
	        return false;
	    }
	    $ext = static::$external_configure_function;
	    $ext($handler, $url);
	    return true;
	}
	
	public function set_debug() {
		$this->curl_options[CURLOPT_VERBOSE] = true;
		$f = fopen($this->temp_dir.'/curl_debug.log', 'a+');
		$this->curl_options[CURLOPT_STDERR]=$f;
	}
}



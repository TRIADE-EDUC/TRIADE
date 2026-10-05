<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_authenticate.class.php,v 1.5 2023/08/17 09:47:55 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path."/shorturl/shorturl_type.class.php";
require_once $class_path."/encoding_normalize.class.php";

class shorturl_type_authenticate extends shorturl_type {

    protected static $default_duration = 300;

    public static function create_hash($type, $action, $context=array() )
    {
        $hash = bin2hex(openssl_random_pseudo_bytes(64));
        $context = encoding_normalize::json_encode($context);
        $q = "select shorturl_hash from shorturls where shorturl_hash='$hash' ";
        $r=pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            $row = pmb_mysql_fetch_object($r);
            $hash = $row->shorturl_hash;
        }else{
            $q = 'insert into shorturls set shorturl_hash="'.addslashes($hash).'", shorturl_type="'.addslashes($type).'", shorturl_action="'.addslashes($action).'", shorturl_context = "'.addslashes($context).'"';
            pmb_mysql_query($q);
        }
        return $hash;
    }


    public static function delete_callback_by_hash($hash)
    {
        if(!is_string($hash)) {
            return;
        }
        $q = "delete from shorturls where shorturl_type='authenticate' and shorturl_action='callback' and shorturl_hash='".addslashes($hash)."' limit 1";
        pmb_mysql_query($q);
    }


    public function generate_hash($action, $context= [])
    {
		if(method_exists($this, $action)){
			$hash = static::create_hash('authenticate', $action, $context);
		}
		return $hash;
	}


	public function generate_callback ($context = ['target'=>'opac'], $renew = false)
	{
	    global $opac_url_base, $pmb_url_base, $database;

	    $url_base = $opac_url_base;
	    if(isset($context['target']) && 'gestion' == $context['target']) {
	        $url_base = $pmb_url_base;
	    }
	    $parsed_url_base = parse_url($url_base);
	    if(isset($parsed_url_base['scheme']) && $parsed_url_base['scheme']) {
	        $tempo_url_base = $parsed_url_base['scheme'].'://';
	    } else {
	        $tempo_url_base = "http://";
	    }
	    if(isset($parsed_url_base['host']) && $parsed_url_base['host']) {
	        $tempo_url_base.= $parsed_url_base['host'];
	    }
	    if(isset($parsed_url_base['port']) && $parsed_url_base['port']) {
	        $tempo_url_base.= ':'.$parsed_url_base['port'];
	    }
	    $context['creation_time'] = time();
	    if( empty($context['duration']) ) {
	       $context['duration'] = static::$default_duration;
	    }
	    $context['url_base'] = $url_base;
	    $context['database'] = $database;
	    $context['method'] = $_SERVER['REQUEST_METHOD'];
	    $context['url'] = $tempo_url_base.$_SERVER['REQUEST_URI'];
	    $context['post'] = $_POST;

	    $hash = '';
	    if($renew) {
	        $hash = $this->update_context($context);
	    } else {
	        $hash = $this->generate_hash('callback', $context);
	    }
	    static::clean_callbacks();
	    return $hash ;
	}


	/**
	 * Genere un callback a partir d'une URL (GET)
	 * @param array $context
	 * @param boolean $renew
	 * @return string
	 */
	public function generate_callback_from_url ($context = ['target'=>'opac'], $renew = false)
	{
	    global $opac_url_base, $pmb_url_base, $database;

	    $url_base = $opac_url_base;
	    if(isset($context['target']) && 'gestion' == $context['target']) {
	        $url_base = $pmb_url_base;
	    }

	    $context['creation_time'] = time();
	    if( empty($context['duration']) ) {
	        $context['duration'] = static::$default_duration;
	    }
	    $context['url_base'] = $url_base;
	    $context['database'] = $database;
	    $context['method'] = 'GET';
	    $context['post'] = '';

	    $hash = '';
	    if($renew) {
	        $hash = $this->update_context($context);
	    } else {
	        $hash = $this->generate_hash('callback', $context);
	    }
	    static::clean_callbacks();
	    return $hash ;
	}

	protected function update_context($context = [])
	{
	    if( empty($this->id) || empty($this->hash)) {
	        return '';
	    }
	    $context = $context = encoding_normalize::json_encode($context);
	    $q = 'update shorturls set shorturl_context = "'.addslashes($context).'" where id_shorturl='.$this->id;
	    pmb_mysql_query($q);
	    return $this->hash;
	}


	public function callback ()
	{
	    global $charset;

	    $context = encoding_normalize::json_decode($this->context, true);
	    $this->delete_callback();

	    $current_time = time();
	    $duration = static::$default_duration;
	    if( !empty($context['duration']) ) {
	        $duration = $context['duration'];
	    }
	    if( $duration && (($current_time - $context['creation_time']) > $duration) ) {
	        throw new Exception('Time elapsed');
	    }
	    session_write_close();
	    pmb_mysql_close();

	    if ('POST' == $context['method']) {
	        $html = "<!DOCTYPE html>
                <html>
                    <head>
                        <meta charset=\"{$charset}\">
                    </head>
                    <body>
                        <form method=\"post\" action=\"{$context['url']}\" id=\"myform\">";

	        foreach($context['post'] as $name=>$value){
	            if(is_array($value)){
	                foreach($value as $key=>$val){
	                    $html.= "<input type=\"hidden\" name=\"{$name}[{$key}]\" value=\"".htmlentities($val,ENT_QUOTES,$charset)."\" />";
	                }
	            } else {
	                $html.= "<input type=\"hidden\" name=\"{$name}\" value=\"".htmlentities($value,ENT_QUOTES,$charset)."\" />";
	            }
	        }
	        $html.= "</form>
                    <script>document.getElementById(\"myform\").submit();</script>
                </body>
            </html>";
	        echo $html;
	        exit();
	    } else {
	        header('Location: ' . $context['url'], 302);
	        exit();
	    }
	}


	public function delete_callback()
	{
 	    $q = "delete from shorturls where shorturl_type='authenticate' and shorturl_action='callback' and id_shorturl=".($this->id);
 	    pmb_mysql_query($q);
	    static::clean_callbacks();
	}


	public static function clean_callbacks()
	{
	    $current_time = time();
	    $shorturl_ids = [];
        $q = "select id_shorturl, shorturl_context from shorturls where shorturl_type='authenticate' and shorturl_action='callback' ";
        $r = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($r)) {
            while($row = pmb_mysql_fetch_assoc($r)) {
                $context = encoding_normalize::json_decode($row['shorturl_context'], true);
                $duration = static::$default_duration;
                if( !empty($context['duration']) ) {
                    $duration = static::$default_duration;
                }
                if( $duration && (($current_time - $context['creation_time']) > $duration) ) {
                    $shorturl_ids[] = $row['id_shorturl'];
                }
            }
        }
        if(count($shorturl_ids)) {
            $q = "delete from shorturls where id_shorturl in (".implode(',', $shorturl_ids).")";
            pmb_mysql_query($q);
        }
	}


	public function get_callback_url()
	{
	    global $_tableau_databases;

	    $context = encoding_normalize::json_decode($this->context, true);
	    return $context['url_base'].'s_authenticate.php?h='.$this->hash.(count($_tableau_databases)>1?'&database='.$context['database']:'');
	}


}
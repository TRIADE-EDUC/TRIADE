<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type.class.php,v 1.3 2022/07/29 12:29:02 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class shorturl_type {
	protected $id;
	protected $hash;
	private $last_access;
	protected $context = array();
	protected $action ='';
	
	public function __construct($id=0)
	{	    
		$this->id = intval($id);
		$this->fetch_datas();
	}
	
	private function fetch_datas()
	{
		if($this->id != 0){
			$query = 'select * from shorturls where id_shorturl = '.$this->id;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->hash = $row->shorturl_hash;
				$this->last_access = $row->shorturl_last_access;
				$this->context = $row->shorturl_context;
				$this->action = $row->shorturl_action;
				$this->type = $row->shorturl_type;
			}
		}
	}
	
	public function proceed()
	{
		if(method_exists($this, $this->action)){
			pmb_mysql_query('update shorturls set shorturl_last_access=now() where id_shorturl = "'.addslashes($this->id).'"');
			$this->{$this->action}();
		}else {
			print 'Action undefined';
			exit;
		}
	}
	public function get_id() {
		return $this->id;
	}

	public function get_hash() {
		return $this->hash;
	}
	
	public function get_last_access() {
		return $this->last_access;
	}
	
	public function get_context() {
		return $this->context;
	}
	public function set_context($context) {
		$this->context = $context;
		return $this;
	}
	public function get_action() {
		return $this->action;
	}
	
	public static function create_hash($type,$action,$context=array()) {
		$hash = md5($type.'///'.$action.'///'.serialize($context));
		$query = "select shorturl_hash from shorturls where shorturl_hash='$hash' ";
		$result=pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$hash = $row->shorturl_hash;
		}else{
			$query = 'insert into shorturls set shorturl_hash="'.addslashes($hash).'", shorturl_type="'.addslashes($type).'", shorturl_action="'.addslashes($action).'", shorturl_context = "'.addslashes(serialize($context)).'"';
			pmb_mysql_query($query);
		}
     		return $hash;
	}
	
	public function get_shorturl($action,$context=array()) {
		global $opac_url_base, $_tableau_databases, $database;
		return $opac_url_base.'s.php?h='.$this->generate_hash($action,$context).(count($_tableau_databases)>1?'&database='.$database:'');
	}
	
	public function get_display_shorturl_in_result($action = '') {
		global $msg;
		
		$rss = (!$action || ($action == 'rss') ? true : false);
		$permalink = (!$action || ($action == 'permalink') ? true : false);
		
		$html = '';
		if ($rss) {
			$html.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"short_url\"><a target='_blank' href='".$this->get_shorturl('rss')."' title='".$msg["short_url_generate"]."'>".$msg["short_url_generate"]."</a></span>";
		}
		if ($permalink) {
			$html.= "
					<script type='text/javascript'>
						require(['dojo/on', 'dojo/topic', 'apps/pmb/sharelink/SharePopup'], function(on, topic, SharePopup){
						window.copy_shorturl_to_clipboard = function() {
								new SharePopup('".$this->get_shorturl('permalink')."');
							}					
						});
					</script>";
			$html.= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"short_url_permalink\"><a href='#' onclick='copy_shorturl_to_clipboard(); return false;' title='".$msg["short_url_permalink"]."'>".$msg["short_url_permalink"]."</a></span>";
		}
		
		return $html;
	}
	
	protected function permalink() {
		global $charset;
		$context = unserialize($this->context);
		if(!isset($context['post'])){
			//redirection simple
			header('Location: '.$context['url']);
		}else{
			//Reconstruction sauvage du formulaire et transmission...
			$html = '
			<html><head></head><body><img src="'.get_url_icon('patience.gif').'"/>
			<form method="post" action="'.$context['url'].'" id="myform">';
			foreach($context['post'] as $name=>$value){
				if(is_array($value)){
					foreach($value as $key=>$val){
						$html.='<input type="hidden" name="'.$name.'['.$key.']" value="'.htmlentities($val,ENT_QUOTES,$charset).'"/>';
					}
				}else{
					$html.='<input type="hidden" name="'.$name.'" value="'.htmlentities($value,ENT_QUOTES,$charset).'"/>';
				}
			}
			$html.='
			</form>
			<script type="text/javascript">
				document.getElementById("myform").submit();
			</script>
			<body></html>';
			print $html;
		}
	}
	
	public function generate_hash($action,$context=array())
	{
		$hash = '';
		switch($action){
			case 'permalink':
 				$context['url'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 				if($_SERVER['REQUEST_METHOD'] == 'POST'){
 					$context['post'] = $_POST;
 				}
				break;
		}
		if(method_exists($this, $action)){
			$hash = self::create_hash('search',$action,$context);
		}
		return $hash;
	}
	
	public static function get_by_hash($hash) 
	{
	    $query = "select id_shorturl,shorturl_type from shorturls where shorturl_hash = '".addslashes($hash)."'";
	    $result=pmb_mysql_query($query);
	    if (pmb_mysql_num_rows($result)) {
	        $row = pmb_mysql_fetch_object($result);
	        $id = $row->id_shorturl;
	        $classname = static::class;
	        $obj = new $classname($id);
	        return $obj;
	    }
	    return false;
	}
	
}


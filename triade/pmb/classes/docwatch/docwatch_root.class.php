<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_root.class.php,v 1.18.4.1 2025/03/17 08:15:42 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/cms/cms_pages.class.php");

/**
 * class docwatch_root
 *
 */
class docwatch_root{

	protected $msg = array();

	protected $parameters;

	/** Aggregations: */

	/** Compositions: */

	/** Fonctions: */

	public function __construct($id=0) {
		$this->load_msg();
	} // end of member function __construct

	protected function load_msg(){
		if (!count($this->msg)) {
			global $lang;
			global $class_path;

			//on regarde la langue par défaut du module
			$default_language = "fr_FR";
			//si elle est différente de celle de l'interface, on l'intègre
			// la langue par défaut donne l'assurance d'avoir tous les messages...
			if($default_language != $lang){
			    $file = $class_path."/docwatch/messages/".$default_language."/".static::class.".xml";
				$this->load_msg_file($file);
			}
			$file = $class_path."/docwatch/messages/".$lang."/".static::class.".xml";
			$this->load_msg_file($file);
		}
	}

	protected function load_msg_file($file){
		global $cache_msg_file;
		if(!$cache_msg_file || !is_array($cache_msg_file)){
			$cache_msg_file=array();
		}
		if(isset($cache_msg_file[$file])){
			$this->msg=$cache_msg_file[$file];
		}elseif(file_exists($file)){
			$messages = new XMLlist($file);
			$messages->analyser();
			$this->msg = array_merge($this->msg, $messages->table);
			$cache_msg_file[$file]=$this->msg;
			return true;
		}else{
			return false;
		}
	}

	public function serialize(){
		return serialize($this->parameters);
	} // end of member function serialize

	public function unserialize($parameters){
		$this->parameters = unserialize($parameters);
	} // end of member function unserialize

	protected function get_form_value_name($name){
		//calcule le hash si pas encore fait...
		return $name;
// 		return $this->get_hash()."_".$name;
	}

	protected function get_value_from_form($name){
		$var_name = $this->get_form_value_name($name);
		global ${$var_name};
		return ${$var_name};
	}

	protected static function charset_normalize($elem,$input_charset){
		global $charset;
		if(is_array($elem)){
			foreach ($elem as $key =>$value){
				$elem[$key] = self::charset_normalize($value,$input_charset);
			}
		}else{
			//PMB dans un autre charset, on converti la chaine...
			$elem = self::clean_cp1252($elem, $input_charset);
			if($charset != $input_charset){
				$elem = iconv($input_charset,$charset . '//IGNORE',$elem);
			}
		}
		return $elem;
	}

	protected static function clean_cp1252($str,$charset){
		$cp1252_map = array();
		switch($charset){
			case "utf-8" :
				$cp1252_map = array(
				"\xe2\x82\xac" => "EUR", /* EURO SIGN */
				"\xe2\x80\x9a" => "\xc2\xab", /* SINGLE LOW-9 QUOTATION MARK */
				"\xc6\x92" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
				"\xe2\x80\9e" => "\xc2\xab", /* DOUBLE LOW-9 QUOTATION MARK */
				"\xe2\x80\xa6" => "...", /* HORIZONTAL ELLIPSIS */
				"\xe2\x80\xa0" => "?", /* DAGGER */
				"\xe2\x80\xa1" => "?", /* DOUBLE DAGGER */
				"\xcb\x86" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\xe2\x80\xb0" => "?", /* PER MILLE SIGN */
				"\xc5\xa0" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
				"\xe2\x80\xb9" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\xc5\x92" => "OE",   /* LATIN CAPITAL LIGATURE OE */
				"\xc5\xbd" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
				"\xe2\x80\x98" => "\x27", /* LEFT SINGLE QUOTATION MARK */
				"\xe2\x80\x99" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
				"\xe2\x80\x9c" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
				"\xe2\x80\x9d" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
				"\xe2\x80\xa2" => "\b7", /* BULLET */
				"\xe2\x80\x93" => "\x20", /* EN DASH */
				"\xe2\x80\x94" => "\x20\x20", /* EM DASH */
				"\xcb\x9c" => "\x7e",   /* SMALL TILDE */
				"\xe2\x84\xa2" => "?", /* TRADE MARK SIGN */
				"\xc5\xa1" => "s",   /* LATIN SMALL LETTER S WITH CARON */
				"\xe2\x80\xba" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\xc5\x93" => "oe",   /* LATIN SMALL LIGATURE OE */
				"\xc5\xbe" => "z",   /* LATIN SMALL LETTER Z WITH CARON */
				"\xc5\xb8" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
				);
				break;
			case "iso8859-1" :
			case "iso-8859-1" :
				$cp1252_map = array(
				"\x80" => "EUR", /* EURO SIGN */
				"\x82" => "\xab", /* SINGLE LOW-9 QUOTATION MARK */
				"\x83" => "\x66",     /* LATIN SMALL LETTER F WITH HOOK */
				"\x84" => "\xab", /* DOUBLE LOW-9 QUOTATION MARK */
				"\x85" => "...", /* HORIZONTAL ELLIPSIS */
				"\x86" => "?", /* DAGGER */
				"\x87" => "?", /* DOUBLE DAGGER */
				"\x88" => "?",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\x89" => "?", /* PER MILLE SIGN */
				"\x8a" => "S",   /* LATIN CAPITAL LETTER S WITH CARON */
				"\x8b" => "\x3c", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\x8c" => "OE",   /* LATIN CAPITAL LIGATURE OE */
				"\x8e" => "Z",   /* LATIN CAPITAL LETTER Z WITH CARON */
				"\x91" => "\x27", /* LEFT SINGLE QUOTATION MARK */
				"\x92" => "\x27", /* RIGHT SINGLE QUOTATION MARK */
				"\x93" => "\x22", /* LEFT DOUBLE QUOTATION MARK */
				"\x94" => "\x22", /* RIGHT DOUBLE QUOTATION MARK */
				"\x95" => "\b7", /* BULLET */
				"\x96" => "\x20", /* EN DASH */
				"\x97" => "\x20\x20", /* EM DASH */
				"\x98" => "\x7e",   /* SMALL TILDE */
				"\x99" => "?", /* TRADE MARK SIGN */
				"\x9a" => "S",   /* LATIN SMALL LETTER S WITH CARON */
				"\x9b" => "\x3e;", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\x9c" => "oe",   /* LATIN SMALL LIGATURE OE */
				"\x9e" => "Z",   /* LATIN SMALL LETTER Z WITH CARON */
				"\x9f" => "Y"    /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
				);
				break;
		}
		return strtr($str, $cp1252_map);
	}


	public static function prefix_var_tree($tree,$prefix){
		for($i=0 ; $i<count($tree) ; $i++){
			$tree[$i]['var'] = $prefix.".".$tree[$i]['var'];
			if(isset($tree[$i]['children']) && $tree[$i]['children']){
				$tree[$i]['children'] = self::prefix_var_tree($tree[$i]['children'],$prefix);
			}
		}
		return $tree;
	}

	protected function save_constructor_link_form($type,$class_name){
		$method = $class_name."_link_".$type."_method";
		$page = $class_name."_link_".$type;
		$var = $class_name."_page_".$type."_var";
		$url = $class_name."_link_".$type."_url";

		global ${$method};
		global ${$page};
		global ${$var};
		global ${$url};

		$this->parameters['links'][$type] = array();
		switch(${$method}) {
			case $page."_select_cms_page":
				$this->parameters['links'][$type] = array(
						'method' => ${$method},
						'page' => (int) ${$page},
						'var'  => ${$var}
				);
				break;
			case $page."_input_url":
				$this->parameters['links'][$type] = array(
						'method' => ${$method},
						'url' => ${$url}
				);
				break;
		}
	}

	public function get_constructor_link_form($type,$class_name=""){
		global $msg,$charset;

		if(!isset($this->parameters['links'][$type]['method'])) $this->parameters['links'][$type]['method'] = '';
		if(!isset($this->parameters['links'][$type]['url'])) $this->parameters['links'][$type]['url'] = '';
		if(!isset($this->parameters['links'][$type]['page'])) $this->parameters['links'][$type]['page'] = '';

		$name = $class_name."_link_".$type;

		$form = "
				<select id='".$name."_method' name='".$name."_method' onChange='".$class_name."_load_".$type."_method_env(this);'>
					<option value=''>".htmlentities($msg['dsi_docwatch_datasource_link_constructor_method'], ENT_QUOTES, $charset)."</option>
					<option value='".$name."_select_cms_page' ".($name."_select_cms_page" == $this->parameters['links'][$type]['method'] ? "selected='selected'" : "").">".htmlentities($msg['dsi_docwatch_datasource_link_select_cms_page'], ENT_QUOTES, $charset)."</option>
					<option value='".$name."_input_url' ".($name."_input_url" == $this->parameters['links'][$type]['method'] ? "selected='selected'" : "").">".htmlentities($msg['dsi_docwatch_datasource_link_input_url'], ENT_QUOTES, $charset)."</option>
				</select>
				<script type='text/javascript'>
					function ".$class_name."_load_".$type."_method_env(obj){
						switch(obj.options[obj.options.selectedIndex].value) {
							case '".$name."_select_cms_page':
								document.getElementById('".$name."_input_url').setAttribute('style','display:none;');
								document.getElementById('".$name."_select_cms_page').setAttribute('style','');
								if(document.getElementById('".$name."_env')) {
									document.getElementById('".$name."_env').setAttribute('style','');
								}
								break;
							case '".$name."_input_url':
								document.getElementById('".$name."_select_cms_page').setAttribute('style','display:none;');
								if(document.getElementById('".$name."_env')) {
									document.getElementById('".$name."_env').setAttribute('style','display:none;');
								}
								document.getElementById('".$name."_input_url').setAttribute('style','');
								break;
							default:
								document.getElementById('".$name."_select_cms_page').setAttribute('style','display:none;');
								if(document.getElementById('".$name."_env')) {
									document.getElementById('".$name."_env').setAttribute('style','display:none;');
								}
								document.getElementById('".$name."_input_url').setAttribute('style','display:none;');
								break;
						}
					}
				</script>
					";

		$form .= "
				<br />
				<div id='".$name."_select_cms_page' name='".$name."_select_cms_page' ".(!$this->parameters['links'][$type]['page'] ? "style='display:none;'" : "").">
					<div class='row'>
 						<label>".htmlentities($msg['dsi_docwatch_datasource_link_constructor_page'],ENT_QUOTES,$charset)."</label>
			 		</div>
			 		<div class='row'>
						<select id='".$name."' name='".$name."' onChange='".$class_name."_load_".$type."_page_env();'>
							<option value='0'>".htmlentities($msg['dsi_docwatch_datasource_link_constructor_page'], ENT_QUOTES, $charset)."</option>";

		$query = "select id_page,page_name from cms_pages order by 2";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){

			while( $row = pmb_mysql_fetch_object($result)){
				$form.= "
							<option value='".$row->id_page."' ".($row->id_page == $this->parameters['links'][$type]['page'] ? "selected='selected'" : "").">".htmlentities($row->page_name,ENT_QUOTES,$charset)."</option>";
			}
		}
		$form.="
						</select>
					</div>
					<script type='text/javascript'>
						function ".$class_name."_load_".$type."_page_env(){
							dijit.byId('".$name."_env').href = './ajax.php?module=dsi&categ=docwatch&sub=sources&elem=".$class_name."&action=get_env&name=".$class_name."_page_".$type."_var"."&pageid='+dojo.byId('".$name."').value;
							dijit.byId('".$name."_env').refresh();
						}
					</script>
				</div>";
		$form.="
				<div id='".$name."_input_url' name='".$name."_input_url' ".(!$this->parameters['links'][$type]['url'] ? "style='display:none;'" : "").">
					<div class='row'>
 						<label>".htmlentities($msg['dsi_docwatch_datasource_link_constructor_url'],ENT_QUOTES,$charset)."</label>
			 		</div>
			 		<div class='row'>
						<input type='text' class='saisie-80em' name='".$name."_url' id='".$name."_url' value='".($this->parameters['links'][$type]['url'] ? $this->parameters['links'][$type]['url'] : "")."' />
					</div>
				</div>";
		$href = "";
		if($this->parameters['links'][$type]['page']){
			$href = "./ajax.php?module=dsi&categ=docwatch&sub=sources&elem=".$class_name."&action=get_env&name=".$class_name."_page_".$type."_var"."&pageid=".$this->parameters['links'][$type]['page']."&var=".$this->parameters['links'][$type]['var'];
		}
		$form.="
				<div id='".$name."_env' dojoType='dojox.layout.ContentPane'".($href!= ""? " preload='true' href='".$href."'":"")."></div>";
		return $form;
	}

	public function get_page_env_select($pageid,$name,$var=""){
		global $msg,$charset;

		$pageid = intval($pageid);
		$page = new cms_page($pageid);
		$form="
		<div class='row'>
			<label for='".$name."'>".htmlentities($msg['dsi_docwatch_datasource_link_constructor_page_var'], ENT_QUOTES, $charset)."</label>
		</div>
		<div class='row'>
			<select name='".$name."' id='".$name."'>";
		foreach($page->vars as $page_var){
			$form.="
				<option value='".$page_var['name']."' ".($page_var['name'] == $var ? "selected='selected'" : "").">".($page_var['comment']!=""? htmlentities($page_var['comment'], ENT_QUOTES, $charset) : htmlentities($page_var['name'], ENT_QUOTES, $charset))."</option>";
		}
		$form.="
			</select>
		</div>";
		return $form;
	}

	protected function get_constructed_link($type,$value,$is_bulletin = false){
		global $pmb_opac_url;
		$link = "";
		switch($type){
			case "notice" :
				if ($this->parameters['links'][$type]['page']) {
					$link = $pmb_opac_url."index.php?lvl=cmspage&pageid=".$this->parameters['links'][$type]['page']."&".$this->parameters['links'][$type]['var']."=".$value;
				} else {
					if (!$is_bulletin) {
						$link = $pmb_opac_url."index.php?lvl=notice_display&id=".$value;
					} else {
						$link = $pmb_opac_url."index.php?lvl=bulletin_display&id=".$value;
					}
				}
				break;
			case "shelve":
				if ($this->parameters['links'][$type]['page']) {
					$link = $pmb_opac_url."index.php?lvl=cmspage&pageid=".$this->parameters['links'][$type]['page']."&".$this->parameters['links'][$type]['var']."=".$value;
				} else {
					$link = $pmb_opac_url."index.php?lvl=etagere_see&id=".$value;
				}
				break;
			case "article":
			case "section" :
			default :
				if ($this->parameters['links'][$type]['page']) {
					$link = $pmb_opac_url."index.php?lvl=cmspage&pageid=".$this->parameters['links'][$type]['page']."&".$this->parameters['links'][$type]['var']."=".$value;
				} else {
					$link = str_replace("!!id!!", $value, $this->parameters['links'][$type]['url']);
				}
				break;
		}
		return $link;
	}

	protected function format_text($text){
		global $charset;
		return htmlentities($text,ENT_QUOTES,$charset);
	}

} // end of docwatch_root

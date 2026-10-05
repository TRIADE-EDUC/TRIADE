<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_datasource_monitoring_website.class.php,v 1.22 2023/08/28 14:04:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/datasources/docwatch_datasource.class.php");
require_once($class_path."/docwatch/selectors/docwatch_selector_monitoring_website.class.php");

/**
 * class docwatch_datasource_monitoring_website
 * 
 */
class docwatch_datasource_monitoring_website extends docwatch_datasource{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/
	
	protected $upload_date;
	
	protected $content;
	
	protected $content_hash;
	
	protected $content_headers;
	/**
	 * @return void
	 * @access public
	 */
	public function __construct($id=0) {
		parent::__construct($id);
	} // end of member function __construct
		
	protected function clean_html($html){
		
		if($this->parameters['xpath_expressions'] == '') {
			preg_match("/\<body.*\>(.*)\<\/body\>/isU", $html, $matches);
			$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $matches[1]);
		} else {
			$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
		}
		switch ($this->parameters['mode_creation_items']) {
			case 'all_change':
			case 'by_change':
				$html = strip_tags($html);
				break;
			case 'all_links':
				preg_match_all("/\<a.*\>(.*)\<\/a\>/isU", strip_tags($html, '<a>'), $link_matches);
				$links = array();
				foreach ($link_matches[0] as $link_match) {
					$links[] = $link_match;
				}
				$html = implode(PHP_EOL, $links);
				break;
		}
		return $html;
	}
	
	protected function get_stored_data() {
		$stored_data = array(
				'is_first' => true,
				'upload_date' => '',
				'content' => '',
				'content_hash' => ''
		);
		$query = "select datasource_monitoring_website_upload_date, datasource_monitoring_website_content, datasource_monitoring_website_content_hash
						from docwatch_datasource_monitoring_website where datasource_monitoring_website_num_datasource = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$stored_data = array(
					'is_first' => false,
					'upload_date' => $row->datasource_monitoring_website_upload_date,
					'content' => $row->datasource_monitoring_website_content,
					'content_hash' => $row->datasource_monitoring_website_content_hash
			);
		}
		return $stored_data;
	}
	
	protected function save_content($first=true) {
		if($first) {
			$query = "insert into docwatch_datasource_monitoring_website set 
					datasource_monitoring_website_num_datasource = '".$this->id."', ";
			$where = "";
		} else {
			$query = "update docwatch_datasource_monitoring_website set ";
			$where = "where datasource_monitoring_website_num_datasource = '".$this->id."' ";
		}
		$query .= " 
				datasource_monitoring_website_upload_date = NOW(),
				datasource_monitoring_website_content = '".addslashes(serialize($this->content))."',
				datasource_monitoring_website_content_hash = '".addslashes($this->content_hash)."'";
		$query .= $where;
		pmb_mysql_query($query);
	}
	
	protected function get_xdiff_change($xdiff_string) {
		$xdiff_change = array();
		preg_match_all("/@@(.*)@@/isU", $xdiff_string, $matches);
		for($i=0; $i<count($matches[0]); $i++) {
			$start = strpos($xdiff_string, $matches[0][$i])+strlen($matches[0][$i]);
			if($i<count($matches[0])-1) {
				$html_extracted = substr($xdiff_string, $start, (strpos($xdiff_string, $matches[0][$i+1]) - $start));
			} else {
				$html_extracted = substr($xdiff_string, $start);
			}
			$has_change = false;
			$lines = explode(PHP_EOL, $html_extracted);
			$change = array();
			foreach ($lines as $line) {
				$line = trim($line);
				if($line != '') {
					if(substr($line, 0, 1) == '-') {
						if(strip_tags(trim(substr($line, 1))) != '') {
// 							$change[] = "<p>- <del style='background-color:#ffcccc'>".trim(substr($line, 1))."</del></p>";
							$has_change = true;
						}
					} elseif(substr($line, 0, 1) == '+') {
						if(strip_tags(trim(substr($line, 1))) != '') {
// 							$change[] = "<p>+ <ins style='background-color:#ccffcc'>".trim(substr($line, 1))."</ins></p>";
							$change[] = "<p>".trim(substr($line, 1))."</p>";
							$has_change = true;
						}
					} else {
						if(strip_tags($line) != '') {
							$change[] = "<p>".$line."</p>";
						}
					}
				}
			}
			if($has_change) {
				$xdiff_change[] = $change;
			}
		}
		return $xdiff_change;
	}
	
	protected function get_content_from_link($link) {
		global $charset;
		
		$content_from_link = array();
		if($link){
			$datas = array();
			$informations = array();
			$loaded=false;
			$aCurl = new Curl();
			$aCurl->set_option('CURLOPT_SSL_VERIFYPEER',false);
			$aCurl->timeout=15;
			$content = $aCurl->get($link);
			$html=$content->body;
			if($html && $content->headers['Status-Code'] == 200){
				$this->content_headers = $content->headers;
				//Uniformisons les retours en minuscules pour la compatibilité sur tous les environnements
				$this->content_headers = array_change_key_case($this->content_headers, CASE_LOWER);
				if(is_array($this->parameters['xpath_expressions']) && count($this->parameters['xpath_expressions'])) {
					$dom = new DOMDocument();
					$old_errors_value = false;
					if(libxml_use_internal_errors(true)){
						$old_errors_value = true;
					}
					$get_encode=array();
					if(preg_match("/<.*?charset[ ]*=[ ]*[\"'](.*?)[\"'].*?>/",$html,$get_encode) && isset($get_encode[1])){
						if(pmb_strtolower($get_encode[1]) == 'utf-8'){
							$html='<?xml encoding="utf-8" ?>'.$html;
						}elseif((pmb_strtolower($get_encode[1]) == 'iso-8859-1') || (pmb_strtolower($get_encode[1]) == 'iso-8859-15')){
							$html='<?xml encoding="iso-8859-1" ?>'.$html;
						}
					}
					$loaded = $dom->loadHTML($html);
					if($loaded) {
						$xpath = new DOMXPath($dom);
						foreach ($this->parameters['xpath_expressions'] as $i=>$xpath_expression) {
							$entries = $xpath->query($xpath_expression);
							if(is_object($entries) && $entries->item(0)) {
    							$html_content = $this->clean_html($dom->saveHTML($entries->item(0)));
    							if($this->parameters['xpath_expressions_for_title'][$i]) {
    								$entries = $xpath->query($this->parameters['xpath_expressions_for_title'][$i]);
    								$html_title = $this->clean_html($dom->saveHTML($entries->item(0)));
    							} else {
    								$html_title = '';
    							}
    							$html_link = '';
    							if($this->parameters['xpath_expressions_for_link'][$i]) {
    								$entries = $xpath->query($this->parameters['xpath_expressions_for_link'][$i]);
    								preg_match("/\<a.*\>(.*)\<\/a\>/isU", strip_tags($dom->saveHTML($entries->item(0)), '<a>'), $link_matches);
    								if($link_matches[0]) {
    									$html_link = $link_matches[0];
    								}
    							}
    							$content_from_link[] = array(
    									'content' => $html_content,
    									'title' => $html_title,
    									'link' => $html_link
    							);
							}
						}
					}
					libxml_use_internal_errors($old_errors_value);
				} else {
					$content_from_link[] = array(
							'content' => $this->clean_html($html),
							'title' => '',
							'link' => ''
					);
				}
			}
		}
		
		//Dom renvoie de l'utf-8. Mais certains caractères windows peuvent être présents dans les pages...
		if($charset != "utf-8"){
			foreach ($content_from_link as $key=>$content) {
				foreach ($content as $key_content=>$value_content) {
					$content_from_link[$key][$key_content] = encoding_normalize::utf8_decode(encoding_normalize::clean_cp1252($value_content,'utf-8'));
				}
			}
		}

		return $content_from_link;
	}
	
	protected function find_root_url($url) {
		
		$tmp = parse_url($url);
		$url = ($tmp["scheme"]?$tmp["scheme"]."://":"").$tmp["host"]."/";
		
		return $url;
	}
	
	protected function get_constructed_remote_link($link, $link_for_construct = '') {
		if(!isset($this->parameters['use_root_url'])) $this->parameters['use_root_url']=0;
		
		if($link_for_construct) {
			if(substr($link_for_construct, 0, 7) == 'http://' || substr($link_for_construct, 0, 8) == 'https://') {
				$link = $link_for_construct;
			} else {
				preg_match("/href=(\"[^\"]+\"|'[^']+'|[^<>\s]+)/i", ' '.$link_for_construct.' ', $matches);
				if($matches[1]) {
					$match_link = str_replace('"', '', $matches[1]);
					if(substr($match_link, 0, 7) == 'http://' || substr($match_link, 0, 8) == 'https://') {
						$link = $match_link;
					} else {
						if($this->parameters['use_root_url']) {
							$link = $this->find_root_url($link);
						}
						if(substr($link, strlen($link)-1) === '/' && substr($match_link, 0, 1) === '/') {
							$link .= substr($match_link, 1);
						} else {
							$link .= $match_link;
						}
					}
				} else {
					$link .= $link_for_construct;
				}
			}
		} else {
			$link .= '#'.strtotime($this->content_headers['date']).rand(0,1000);
		}
		return $link;
	}
	
	protected function get_items_datas($link){
		$items = array();
		$content_from_link = $this->get_content_from_link($link);
		$content_hash_from_link = md5(serialize($content_from_link));
		
		if(is_array($content_from_link) && count($content_from_link)) {
			$datas = array();
			$stored_data = $this->get_stored_data();
			if($content_hash_from_link != $stored_data['content_hash']) {
				$content_from_base = unserialize($stored_data['content']);
				if(is_array($this->parameters['xpath_expressions']) && count($this->parameters['xpath_expressions'])) {
					$hash_from_base = array();
					if(is_array($content_from_base)) {
						foreach ($content_from_base as $content) {
							$hash_from_base[] = md5($content['content']);
						}
					}
					foreach ($content_from_link as $i=>$content) {
						if(!in_array(md5($content['content']), $hash_from_base)) {
							$items[] = array(
									'content' => $content['content'],
									'title' => ($content['title'] ? $content['title'] : $this->get_title()),
									'link' => $this->get_constructed_remote_link($link, $content['link'])
							);
						}
					}
				} else {
					if(!extension_loaded('xdiff')) {
						return false;
					}
					if(md5($content_from_base[0]['content']) != md5($content_from_link[0]['content'])) {
						if(!isset($content_from_base[0]['content'])) $content_from_base[0]['content'] = '';
						if(!isset($content_from_link[0]['content'])) $content_from_link[0]['content'] = '';
						$xdiff_string = xdiff_string_diff($content_from_base[0]['content'] , $content_from_link[0]['content']);
						$xdiff_change = $this->get_xdiff_change($xdiff_string);
						if($this->parameters['mode_creation_items'] == 'by_change') {
							foreach ($xdiff_change as $i=>$change) {
								$items[] = array(
										'content' => implode('', $change),
										'title' => $this->get_title(),
										'link' => $this->get_constructed_remote_link($link)
								);
							}
						} else {
							$item_content = '';
							foreach ($xdiff_change as $change) {
								$item_content .= implode('', $change);
							}
							$items[] = array(
									'content' => $item_content,
									'title' => $this->get_title(),
									'link' => $this->get_constructed_remote_link($link)
							);
						}
					}
				}
				foreach ($items as $item) {
					$data = array();
					$data["type"] = "monitoring_website";
					$data["title"] = $item['title'];
					$data["summary"] = $item['content'];
					$data["content"] = '';
					$data["url"] = $item['link'];
					$data["publication_date"] = date( 'Y-m-d H:i:s', strtotime($this->content_headers['date']));
					$data["logo_url"] = '';
					$data["descriptors"] = "";
					$data["tags"] = '';
					$datas[] = $data;
				}
				$this->content = $content_from_link;
				$this->content_hash = $content_hash_from_link;
				$this->save_content($stored_data['is_first']);
			}
			return $datas;
		}else{
			return false;
		}
	}
	
	public function get_available_selectors(){
		global $msg;
		return array(
				"docwatch_selector_monitoring_website" => $msg['dsi_docwatch_selector_monitoring_website']
		);
	}
	
	protected function get_expression_xpath_content($parameter_name, $i, $expression) {
		global $msg, $charset;
		
		$form = "<input type='text' data-dojo-type='dijit/form/TextBox' id='docwatch_datasource_monitoring_website_".$parameter_name."_".$i."' name='docwatch_datasource_monitoring_website_".$parameter_name."[]' value=\"".htmlentities($expression, ENT_QUOTES, $charset)."\" style='width: 50em;'/>
	 			<button data-dojo-type='dijit/form/Button' type='button'>".$msg['raz']."
	 				<script type='dojo/on' data-dojo-event='click' data-dojo-args='evt'>
	 					require(['dojo/dom'], function(dom){
				            dom.byId('docwatch_datasource_monitoring_website_".$parameter_name."_".$i."').value = '';
				        });
	 				</script>
	 			</button>";
		if($i == 0) {
			$form .= "<button data-dojo-type='dijit/form/Button' type='button'>+
					<script type='dojo/on' data-dojo-event='click' data-dojo-args='evt'>
	 					require(['dojo/dom', 'dojo/dom-construct', 'dojo/dom-attr', 'dojo/on', 'dojo/parser'], function(dom, domConstruct, domAttr, on, parser){
				            var count = dom.byId('".$parameter_name."_count').value;
							var div = domConstruct.create('div', {id : 'monitoring_website_".$parameter_name."_'+count});
							var input = domConstruct.create('input', {type : 'text', 'data-dojo-type' : 'dijit/form/TextBox', id : 'docwatch_datasource_monitoring_website_".$parameter_name."_'+count, name : 'docwatch_datasource_monitoring_website_".$parameter_name."[]', style : 'width: 50em;'});
							domConstruct.place(input, div);
							var button = domConstruct.create('button', {id : 'monitoring_website_".$parameter_name."_'+count+'_button', 'data-dojo-type' : 'dijit/form/Button', type : 'button', innerHTML : ' X '});
							on(button, 'click', function(){
								dom.byId('docwatch_datasource_monitoring_website_".$parameter_name."_'+count).value = '';
							});
							domConstruct.place(button, div);
							domConstruct.place(div, 'add_".$parameter_name."');
							parser.parse('monitoring_website_".$parameter_name."_'+count);
							dom.byId('".$parameter_name."_count').value = count+1;
				        });
	 				</script>
				</button>";
		}
		return $form;
	}
	
	protected function get_xpath_expressions_form($parameter_name) {
		global $msg,$charset;
		
		if(!isset($this->parameters[$parameter_name])) $this->parameters[$parameter_name]='';
		
		$form = "
	 		<div class='row'>
	 			<label>".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_'.$parameter_name],ENT_QUOTES,$charset)."</label>
	 		</div>";
		if(is_array($this->parameters[$parameter_name]) && count($this->parameters[$parameter_name])) {
			foreach ($this->parameters[$parameter_name] as $i=>$expression) {
				$form .= "
			 		<div class='row'>
			 			".$this->get_expression_xpath_content($parameter_name, $i, $expression)."	
					</div>";
			}
			$form .= "<input type='hidden' id='".$parameter_name."_count' name='".$parameter_name."_count' value= '".count($this->parameters[$parameter_name])."' />";
		} else {
			$form .= "
	 			<div class='row'>
		 			".$this->get_expression_xpath_content($parameter_name, 0, '')."	
				</div>
		 		<input type='hidden' id='".$parameter_name."_count' name='".$parameter_name."_count' value= '1' />";
		}
		$form .= "<div id='add_".$parameter_name."'></div>";
		return $form;
	}
	
	protected function get_xdiff_informations(){
		global $msg, $charset;
		
		if(!extension_loaded('xdiff')) {
			return "
			<div class='row'>
				<span><b>".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_xdiff'],ENT_QUOTES,$charset)."</b></span>
			</div>
			<div class='row'>&nbsp;</div>";
		}
		return "";
	}
	
	public function get_form_content(){
		global $msg,$charset;
		$form = parent::get_form_content();
		
		if(!isset($this->parameters['mode_creation_items'])) $this->parameters['mode_creation_items']='';
		if(!isset($this->parameters['use_root_url'])) $this->parameters['use_root_url']=0;
		
		$form .= "
		<div class='row'>&nbsp;</div>
 		<div class='row'>
 			<label>".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_mode_creation_items'],ENT_QUOTES,$charset)."</label>
 		</div>
 		<div class='row'>
 			<select id='docwatch_datasource_monitoring_website_mode_creation_items' name='docwatch_datasource_monitoring_website_mode_creation_items' onchange='monitoring_website_mode_creation_items(this.value);'>
 				<option value='all_change' ".("all_change" == $this->parameters['mode_creation_items'] ? "selected='selected'" : "").">".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_mode_creation_items_all_change'], ENT_QUOTES, $charset)."</option>
				<option value='by_change' ".("by_change" == $this->parameters['mode_creation_items'] ? "selected='selected'" : "").">".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_mode_creation_items_by_change'], ENT_QUOTES, $charset)."</option>
				<option value='all_links' ".("all_links" == $this->parameters['mode_creation_items'] ? "selected='selected'" : "").">".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_mode_creation_items_all_links'], ENT_QUOTES, $charset)."</option>
			</select>
 		</div>
		<div class='row'>&nbsp;</div>
		<div id='docwatch_datasource_monitoring_website_xpath_expressions_content' ".("by_change" == $this->parameters['mode_creation_items'] ? "style='display:none;'" : "").">
			".$this->get_xpath_expressions_form('xpath_expressions')."
			<div class='row'>&nbsp;</div>
			".$this->get_xpath_expressions_form('xpath_expressions_for_title')."
			<div class='row'>&nbsp;</div>
	 		".$this->get_xpath_expressions_form('xpath_expressions_for_link')."
	 		<div class='row'>&nbsp;</div>
	 	</div>
	 	<div id='docwatch_datasource_monitoring_website_xdiff_only' ".("by_change" != $this->parameters['mode_creation_items'] ? "style='display:none;'" : "").">
			<div class='row'>
				<span>".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_xdiff_only'],ENT_QUOTES,$charset)."</span>
			</div>
			<div class='row'>&nbsp;</div>
	 	</div>
	 	".$this->get_xdiff_informations()."
 		<div class='row'>
 			<label>".htmlentities($msg['dsi_docwatch_datasource_monitoring_website_use_root_url'],ENT_QUOTES,$charset)."</label>
 		</div>
 		<div class='row'>
			".$msg['39']."<input ".($this->parameters['use_root_url'] ? "":"checked='checked'")." type='radio' data-dojo-type='dijit/form/RadioButton' name='docwatch_datasource_monitoring_website_use_root_url' value='0' />&nbsp;
			".$msg['40']."<input ".($this->parameters['use_root_url'] ? "checked='checked'":"")." type='radio' data-dojo-type='dijit/form/RadioButton' name='docwatch_datasource_monitoring_website_use_root_url' value='1' /> 
		</div>
		<script type='text/javascript'>
			function monitoring_website_mode_creation_items(value) {
				if(value == 'by_change') {
					document.getElementById('docwatch_datasource_monitoring_website_xpath_expressions_content').setAttribute('style', 'display:none');
					document.getElementById('docwatch_datasource_monitoring_website_xdiff_only').setAttribute('style', 'display:block');
				} else {
					document.getElementById('docwatch_datasource_monitoring_website_xpath_expressions_content').setAttribute('style', 'display:block');
					document.getElementById('docwatch_datasource_monitoring_website_xdiff_only').setAttribute('style', 'display:none');
				}
			}
		</script>
		";
		return $form;
	}
	
	public function set_from_form() {
		global $docwatch_datasource_monitoring_website_mode_creation_items;
		global $docwatch_datasource_monitoring_website_xpath_expressions;
		global $docwatch_datasource_monitoring_website_xpath_expressions_for_title;
		global $docwatch_datasource_monitoring_website_xpath_expressions_for_link;
		global $docwatch_datasource_monitoring_website_use_root_url;
		
		$this->parameters['mode_creation_items'] = stripslashes($docwatch_datasource_monitoring_website_mode_creation_items);
		$this->parameters['xpath_expressions'] = array();
		if(is_array($docwatch_datasource_monitoring_website_xpath_expressions)) {
			foreach ($docwatch_datasource_monitoring_website_xpath_expressions as $xpath_expression) {
				if($xpath_expression) {
					$this->parameters['xpath_expressions'][] = stripslashes($xpath_expression);
				}
			}
		}
		$this->parameters['xpath_expressions_for_title'] = array();
		if(is_array($docwatch_datasource_monitoring_website_xpath_expressions_for_title)) {
			foreach ($docwatch_datasource_monitoring_website_xpath_expressions_for_title as $xpath_expression_for_title) {
				if($xpath_expression_for_title) {
					$this->parameters['xpath_expressions_for_title'][] = stripslashes($xpath_expression_for_title);
				}
			}
		}
		$this->parameters['xpath_expressions_for_link'] = array();
		if(is_array($docwatch_datasource_monitoring_website_xpath_expressions_for_link)) {
			foreach ($docwatch_datasource_monitoring_website_xpath_expressions_for_link as $xpath_expression_for_link) {
				if($xpath_expression_for_link) {
					$this->parameters['xpath_expressions_for_link'][] = stripslashes($xpath_expression_for_link);
				}
			}
		}
		$this->parameters['use_root_url'] = $docwatch_datasource_monitoring_website_use_root_url;
		parent::set_from_form();
	}

} // end of docwatch_datasource_monitoring_website


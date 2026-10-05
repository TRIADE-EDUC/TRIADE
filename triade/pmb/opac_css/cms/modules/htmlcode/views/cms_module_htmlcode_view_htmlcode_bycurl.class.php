<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_htmlcode_view_htmlcode_bycurl.class.php,v 1.6 2023/08/28 14:01:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/curl.class.php');

class cms_module_htmlcode_view_htmlcode_bycurl extends cms_module_common_view{
	protected $cadre_parent;

	public function __construct($id=0){
		parent::__construct($id+0);
	}

	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_htmlcode_bycurl_url'>".$this->format_text($this->msg['cms_module_htmlcode_view_htmlcode_bycurl_url'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='cms_module_common_view_htmlcode_bycurl_url' name='cms_module_common_view_htmlcode_bycurl_url' value='".$this->format_text(stripslashes($this->parameters['html_bycurl_url']))."' />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_htmlcode_bycurl_url'>".$this->format_text($this->msg['cms_module_htmlcode_view_htmlcode_bycurl_utf8'])."</label>
				</div>
				<div class='colonne-suite'>";

		$form.= "<input type='checkbox' id='cms_module_htmlcode_view_htmlcode_bycurl_utf8' name='cms_module_htmlcode_view_htmlcode_bycurl_utf8' value='1' ".(($this->parameters['html_bycurl_utf8'])?"checked='checked'":'')."/>";

		$form.= "
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_htmlcode_bycurl_auth'>".$this->format_text($this->msg['cms_module_htmlcode_view_htmlcode_bycurl_auth'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='cms_module_htmlcode_view_htmlcode_bycurl_auth' name='cms_module_htmlcode_view_htmlcode_bycurl_auth' value='".$this->format_text(stripslashes($this->parameters['html_bycurl_auth']))."' />
				</div>
			</div>";
		return $form;
	}

	public function save_form(){
		global $cms_module_common_view_htmlcode_bycurl_url;
		global $cms_module_htmlcode_view_htmlcode_bycurl_auth;
		global $cms_module_htmlcode_view_htmlcode_bycurl_utf8;
		$this->parameters['html_bycurl_url'] = $cms_module_common_view_htmlcode_bycurl_url;
		$this->parameters['html_bycurl_auth'] = $cms_module_htmlcode_view_htmlcode_bycurl_auth;
		if($cms_module_htmlcode_view_htmlcode_bycurl_utf8) {
			$this->parameters['html_bycurl_utf8']=1;
		} else {
			$this->parameters['html_bycurl_utf8']=0;
		}
		return parent::save_form();
	}

	public function render($datas){

		global $charset;
		$ch = new Curl();
		if($this->parameters['html_bycurl_auth']) {
			$ch->options['CURLOPT_USERPWD'] = $this->parameters['html_bycurl_auth'];
		}
		$rcurl = $ch->get($this->parameters['html_bycurl_url']);
		if ($rcurl->headers['Status-Code']!='200') {
			$response = '';
		} else {
			$response = $rcurl->body;
		}
		if($response) {
			if($charset == 'utf-8' && !$this->parameters['html_bycurl_utf8']) {
				$response = encoding_normalize::utf8_normalize($response);
			} else if($charset != 'utf-8' && $this->parameters['html_bycurl_utf8']) {
				$response = encoding_normalize::utf8_decode($response);
			}
		}
		return $response;
	}
}
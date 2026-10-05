<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_rss.class.php,v 1.3 2021/10/13 10:34:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/selectors/docwatch_selector.class.php");

/**
 * class docwatch_selector_notice
 * 
 */
class docwatch_selector_rss extends docwatch_selector{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * @return void
	 * @access public
	 */
	
	public function get_value(){
		if($this->parameters['rss_link']){
			if(empty($this->parameters['curl_timeout'])){
				$this->parameters['curl_timeout']= "";
			}
			$this->value = array(
					'rss_link' => $this->parameters['rss_link'],
					'curl_timeout' => $this->parameters['curl_timeout']
			);
			return $this->value;
		}
		
	}
	
	public function get_form(){
		global $msg,$charset;
		
		if(empty($this->parameters['rss_link'])){
			$this->parameters['rss_link']= "";
		}
		if(empty($this->parameters['curl_timeout'])){
			$this->parameters['curl_timeout']= "";
		}
		$form ="
		<div class='row'>
				<label>".htmlentities($msg['dsi_docwatch_selector_rss'],ENT_QUOTES,$charset)."</label>
				<input type='text' name='docwatch_selector_rss_url_link' value='".htmlentities($this->parameters['rss_link'],ENT_QUOTES,$charset)."'/>			
		</div>
		<div class='row'>
				<label>".$msg['dsi_docwatch_selector_rss_curl_timeout']."</label>
				<input type='text' class='saisie-10em' name='docwatch_selector_rss_curl_timeout' value='".htmlentities($this->parameters['curl_timeout'],ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg['dsi_docwatch_selector_rss_curl_timeout_sup_1']."</label>
		</div>
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_rss_url_link, $docwatch_selector_rss_curl_timeout;
		$this->parameters['rss_link'] = $docwatch_selector_rss_url_link;
		$this->parameters['curl_timeout'] = stripslashes($docwatch_selector_rss_curl_timeout);
	}
} // end of docwatch_selector_rss


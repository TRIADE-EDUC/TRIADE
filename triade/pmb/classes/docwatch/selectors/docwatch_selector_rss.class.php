<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_rss.class.php,v 1.2 2017-08-23 07:29:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
			$this->value = $this->parameters['rss_link'];
			return $this->value;
		}
		
	}
	
	public function get_form(){
		global $msg,$charset;
		
		if(!isset($this->parameters['rss_link']) || !$this->parameters['rss_link']){
			$this->parameters['rss_link']= "";
		}
		$form ="
		<div class='row'>
				<label>".htmlentities($msg['dsi_docwatch_selector_rss'],ENT_QUOTES,$charset)."</label>
				<input type='text' name='docwatch_selector_rss_url_link' value='".htmlentities($this->parameters['rss_link'],ENT_QUOTES,$charset)."'/>			
		</div>
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_rss_url_link;
		$this->parameters['rss_link'] = $docwatch_selector_rss_url_link;
	}
} // end of docwatch_selector_notice


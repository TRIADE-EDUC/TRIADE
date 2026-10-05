<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_monitoring_website.class.php,v 1.2 2017/08/23 07:29:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/selectors/docwatch_selector.class.php");

/**
 * class docwatch_selector_monitoring_website
 * 
 */
class docwatch_selector_monitoring_website extends docwatch_selector{

	/** Aggregations: */

	/** Compositions: */

	 /*** Attributes: ***/

	/**
	 * @return void
	 * @access public
	 */
	
	public function get_value(){
		if($this->parameters['site_link']){
			$this->value = $this->parameters['site_link'];
			return $this->value;
		}
		
	}
	
	public function get_form(){
		global $msg,$charset;
		
		if(!isset($this->parameters['site_link']) || !$this->parameters['site_link']){
			$this->parameters['site_link']= "";
		}
		$form ="
		<div class='row'>
				<label>".htmlentities($msg['dsi_docwatch_selector_monitoring_website'],ENT_QUOTES,$charset)."</label>
				<input type='text' name='docwatch_selector_monitoring_website_link' value='".htmlentities($this->parameters['site_link'],ENT_QUOTES,$charset)."'/>			
		</div>
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_monitoring_website_link;
		$this->parameters['site_link'] = stripslashes($docwatch_selector_monitoring_website_link);
	}
} // end of docwatch_selector_monitoring_website


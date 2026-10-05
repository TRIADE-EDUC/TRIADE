<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_explnum.class.php,v 1.3 2022/10/21 13:51:23 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path.'/event/event.class.php');

class event_explnum extends event {
	
	/**
	 * Exemplaire numérique
	 * @var explnum
	 */
	protected $explnum;
	
	protected $integrationUrl;
	
	/**
	 * 
	 * @return explnum
	 */
	public function get_explnum() {
		return $this->explnum;
	}
	
	/**
	 * 
	 * @param int $explnum
	 */
	public function set_explnum($explnum) {
		$this->explnum = $explnum;
		return $this;
	}
	
	public function get_contenu_vignette() {
	    return $this->explnum;
	}
	
	public function setIntegrationUrl($url) {
	    return $this->integrationUrl = $url;
	}
	public function getIntegrationUrl() {
	    return $this->integrationUrl;
	}
}
<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: event_explnum.class.php,v 1.4 2022/11/03 09:37:34 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path.'/event/event.class.php');

class event_explnum extends event {
	
	/**
	 * Exemplaire numérique
	 * @var explnum
	 */
	protected $explnum;
	
	protected $contenu_vignette;
	
	/**
	 * 
	 * @return explnum
	 */
	public function get_explnum() {
		return $this->explnum;
	}
	
	public function get_contenu_vignette() {
	    return $this->contenu_vignette ;
	}
	
	public function set_contenu_vignette($contenu_vignette) {
		$this->contenu_vignette = $contenu_vignette;
		return $this;
	}
	
	/**
	 * 
	 * @param int $explnum
	 */
	public function set_explnum($explnum) {
		$this->explnum = $explnum;
		return $this;
	}
}
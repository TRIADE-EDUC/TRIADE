<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_bulletinage_explnum_controller.class.php,v 1.3 2023/10/24 09:57:08 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/entities/entities_analysis_controller.class.php");

class entities_bulletinage_explnum_controller extends entities_bulletinage_controller {
		
	protected $bulletin_id;
	
	/**
	 * 8 = droits de modification
	 */
	protected function get_acces_m() {
		global $PMBuserid;
		$acces_m=1;
		$acces_j = $this->dom_1->getJoin($PMBuserid, 8, 'bulletin_notice');
		$q = "select count(1) from bulletins $acces_j where bulletin_id=".$this->bulletin_id;
		$r = pmb_mysql_query($q);
		if(pmb_mysql_result($r,0,0)==0) {
			$acces_m=0;
			if(!$this->id) {
				$this->error_message = 'mod_bull_error';
			} else {
				$this->error_message = 'mod_enum_error';
			}
		}
		return $acces_m;
	}
	
	public function proceed_explnum_form() {
		$this->action_link = $this->url_base."&action=explnum_update";
		if($this->id) {
			$this->delete_link = $this->url_base."&action=explnum_delete&bul_id=".intval($this->bulletin_id)."&explnum_id=".intval($this->id);
		} else {
			$this->delete_link = "";
		}
		// affichage des infos du bulletinage pour rappel
		$bulletinage = new bulletinage_display($this->bulletin_id);
		print "<div class='row'><h2>".$bulletinage->display."</h2></div>";
		
		$explnum = new explnum($this->id, 0, $this->bulletin_id);
		print $explnum->explnum_form($this->action_link,$this->get_permalink(), $this->delete_link);
	}
	
	protected function get_permalink($id=0) {
	    if(!$id) {
	        $id = $this->bulletin_id;
	    }
		return $this->url_base."&action=view&bul_id=" . intval($id);
	}
	
	public function set_bulletin_id($bulletin_id=0) {
	    $this->bulletin_id = (int) $bulletin_id;
	}
}

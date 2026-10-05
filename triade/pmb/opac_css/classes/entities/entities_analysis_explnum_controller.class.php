<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_analysis_explnum_controller.class.php,v 1.3 2023/10/24 09:57:08 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/entities/entities_analysis_controller.class.php");

class entities_analysis_explnum_controller extends entities_analysis_controller {
		
	protected $analysis_id;
	
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
				$this->error_message = 'mod_depo_error';
			} else {
				$this->error_message = 'mod_enum_error';
			}
		}
		return $acces_m;
	}
	
	public function proceed_explnum_form() {
		$this->action_link = $this->url_base."&sub=analysis&action=explnum_update&bul_id=".intval($this->bulletin_id);
		if($this->id) {
			$this->delete_link = $this->url_base."&sub=bulletinage&action=explnum_delete&bul_id=".intval($this->bulletin_id)."&explnum_id=".intval($this->id);
		} else {
			$this->delete_link = "";
		}
		$myAnalysis = new analysis(intval($this->analysis_id), intval($this->bulletin_id));
		print "<div class='row'><div class='perio-barre'>".$this->get_link_parent()."<h3>".$myAnalysis->tit1."</h3></div></div><br />";
		
		$explnum = new explnum($this->id,$this->analysis_id);
		print $explnum->explnum_form($this->action_link,$this->get_permalink(), $this->delete_link);
	}
	
	public function set_analysis_id($analysis_id=0) {
	    $this->analysis_id = (int) $analysis_id;
	}
}

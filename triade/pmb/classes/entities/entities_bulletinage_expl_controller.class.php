<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_bulletinage_expl_controller.class.php,v 1.1 2023/04/07 09:12:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/entities/entities_bulletinage_controller.class.php");

class entities_bulletinage_expl_controller extends entities_bulletinage_controller {
		
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
				$this->error_message = 'mod_expl_error';
			}
		}
		return $acces_m;
	}
	
	public function proceed_expl_form() {

	}
	
	protected function is_expl_deletable() {
		global $msg;
		
		$sql_circ = pmb_mysql_query("select 1 from serialcirc_expl where num_serialcirc_expl_id ='".$this->id."' ") ;
		if (pmb_mysql_num_rows($sql_circ)) {
			error_message($msg[416], $msg["serialcirc_expl_no_del"], 1, bulletinage::get_permalink($this->bulletin_id));
			return false;
		}else{
			$query = "select 1 from pret where pret_idexpl='".$this->id."' ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				// gestion erreur prêt en cours
				error_message($msg[416], $msg['impossible_expl_del_pret'], 1, bulletinage::get_permalink($this->bulletin_id));
				return false;
			} else {
				return true;
			}
		}
	}
	
	public function proceed_expl_delete() {
		global $msg;
		
		print "<div class=\"row\"><div class=\"msg-perio\">".$msg['catalog_notices_suppression']."</div></div>";
		
		$sql_circ = pmb_mysql_query("select 1 from serialcirc_expl where num_serialcirc_expl_id ='".$this->id."' ") ;
		if (pmb_mysql_num_rows($sql_circ)) {
			error_message($msg[416], $msg["serialcirc_expl_no_del"], 1, bulletinage::get_permalink($this->bulletin_id));
		}else{
			$query = "select 1 from pret where pret_idexpl='".$this->id."' ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				// gestion erreur prêt en cours
				error_message($msg[416], $msg['impossible_expl_del_pret'], 1, bulletinage::get_permalink($this->bulletin_id));
			} else {
				exemplaire::del_expl($this->id);
				
				print $this->get_redirection_form();
			}
		}
	}
	
	protected function get_permalink($id=0) {
		if(!$id) $id = $this->bulletin_id;
		return bulletinage::get_permalink($id);
	}
	
	public function set_bulletin_id($bulletin_id=0) {
	    $this->bulletin_id = (int) $bulletin_id;
	}
}

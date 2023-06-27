<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_notices_equation.class.php,v 1.3 2015-04-03 11:16:24 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/selectors/docwatch_selector_notices.class.php");
require_once($class_path."/equation.class.php");

/**
 * class docwatch_selector_equation
 * 
 */
class docwatch_selector_notices_equation extends docwatch_selector_notices {

	/*
	 * On récupère via le formulaire l'identifiant d'une équation de DSI
	 * $this->parameters['equation']
	 */
	
	public function get_value(){
		global $dbh;
		if(!count($this->value)){
			if($this->parameters['equation']){
				$equ = new equation ($this->parameters['equation']) ;
				$search = new search() ;
				$search->unserialize_search($equ->requete) ;
				$table = $search->make_search() ;
				$result = pmb_mysql_query("select * from ".$table,$dbh);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$this->value[] = $row->notice_id;
					}
				}
			}
		}
		return $this->value;
	}
	
} // end of docwatch_selector_equation
<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_notices_dsi.class.php,v 1.4 2022/01/18 07:36:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/selectors/docwatch_selector_notices.class.php");


/**
 * class docwatch_selector_dsi
 * 
 */
class docwatch_selector_notices_dsi extends docwatch_selector_notices{
	
	/*
	 * On récupère via le formulaire un tableau de bannettes
	* $this->parameters['sdis']
	*/
	
	public function get_value(){
		if(!count($this->value)){
			if(count($this->parameters['sdis'])){
				$query = "select distinct num_notice from bannette_contenu where num_bannette in (".implode(",",$this->parameters['sdis']).")";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)){
					while($row = pmb_mysql_fetch_object($result)){
						$this->value[] = $row->num_notice;
					}
				}
			}
		}
		return $this->value;
	}
} // end of docwatch_selector_dsi

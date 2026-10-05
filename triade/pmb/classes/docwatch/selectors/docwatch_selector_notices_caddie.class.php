<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_notices_caddie.class.php,v 1.10 2022/01/18 07:36:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/docwatch/selectors/docwatch_selector_notices.class.php");
require_once($class_path."/caddie.class.php");

/**
 * class docwatch_selector_caddie
 * 
 */
class docwatch_selector_notices_caddie extends docwatch_selector_notices {
	
	/*
	 * On récupère via le formulaire un tableau de panier de notices
	 * $this->parameters['caddies']
	 */
	
	public function get_value(){
		if(!count($this->value) && count($this->parameters['caddies'])){
			//partons du principe qu'on a des caddie...
			$query ="select distinct object_id from caddie_content where caddie_id in (".implode(",",$this->parameters['caddies']).")";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row=pmb_mysql_fetch_object($result)){
					$this->value[] =$row->object_id;
				}			
			}				
		}	
		return $this->value;
	}
	
	public function get_form(){
		global $msg,$charset;
		$form ="
		<div class='row'>
			<div class='colonne3'>
				<label>".htmlentities($msg['dsi_docwatch_selector_notices_caddie_select'],ENT_QUOTES,$charset)."</label>
			</div> 
			<div class='colonne_suite'>".caddie::get_cart_list_multiple_selector('NOTI', 'docwatch_selector_notices_caddie_select[]', $this->parameters['caddies'])."
			</div>
		</div>		
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_notices_caddie_select;
		$this->parameters['caddies'] = $docwatch_selector_notices_caddie_select;
	}
} // end of docwatch_selector_caddie

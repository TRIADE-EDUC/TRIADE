<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_selector_notices_caddie.class.php,v 1.8 2017-08-23 07:29:05 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/docwatch/selectors/docwatch_selector_notices.class.php");

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
		global $dbh;
		if(!count($this->value) && count($this->parameters['caddies'])){
			//partons du principe qu'on a des caddie...
			$query ="select distinct object_id from caddie_content where caddie_id in (".implode(",",$this->parameters['caddies']).")";
			$result = pmb_mysql_query($query,$dbh);
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
			<div class='colonne_suite'>".$this->gen_select()."
			</div>
		</div>		
		";
		return $form;
	}
	
	public function set_from_form(){
		global $docwatch_selector_notices_caddie_select;
		$this->parameters['caddies'] = $docwatch_selector_notices_caddie_select;
	}
	
	
	protected function gen_select(){
		global $dbh,$charset;
		
		if(!isset($this->parameters['caddies']) || !$this->parameters['caddies']){
			$this->parameters['caddies']= array();
		}
		$query ="select idcaddie, name from caddie where type='NOTI' order by name";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$select ="
				<select name='docwatch_selector_notices_caddie_select[]' multiple='yes'>";
			while($row = pmb_mysql_fetch_object($result)){
				$select.="
					<option value='".htmlentities($row->idcaddie,ENT_QUOTES,$charset)."' ".(in_array($row->idcaddie,$this->parameters['caddies']) ? "selected='selected'" : "").">".htmlentities($row->name,ENT_QUOTES,$charset)."</option>";
			}
			$select.="
				</select>";
		}else{
			$select = $msg["dsi_docwatch_selector_notices_caddie_select_no_caddie"];
		}
		
		return $select;
	}
	
} // end of docwatch_selector_caddie

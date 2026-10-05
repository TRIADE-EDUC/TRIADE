<?php
// +-------------------------------------------------+
//  2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_authorities.class.php,v 1.1 2011/12/20 13:12:44 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/import_authorities.tpl.php");

class import_authorities {
	
	public function __construct(){
		
	}
	
	public function show_form(){
		global $msg,$charset;
		global $authorites_import_form;
		
		$form =  $authorites_import_form;
		
		return $form;
	}
	
	public function proceed(){
		
	}
}

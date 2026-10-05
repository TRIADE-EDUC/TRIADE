<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_mailtpl_img_ui.class.php,v 1.1 2022/06/10 12:14:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/mailtpl/list_configuration_mailtpl_files_ui.class.php");

class list_configuration_mailtpl_img_ui extends list_configuration_mailtpl_files_ui {
	
	protected $files_type = 'img';
	
}
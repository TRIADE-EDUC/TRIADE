<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_configuration_domains_controller.class.php,v 1.1 2023/01/19 14:18:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mails_configuration_domains_controller extends mails_configuration_controller {
	
	protected static $list_ui_class_name = 'list_mails_configuration_domains_ui';
	
}
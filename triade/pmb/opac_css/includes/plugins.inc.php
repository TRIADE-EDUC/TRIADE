<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: plugins.inc.php,v 1.1 2016/09/06 09:52:07 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Inclusion/initialisation du système de plugins
require_once $class_path.'/plugins.class.php';
$plugins = plugins::get_instance();

//Inclusion/initialisation du système d'évenements !
require_once $class_path.'/event/events_handler.class.php';
$evth = events_handler::get_instance();
$evth->discover();
$requires = $evth->get_requires();
for($i=0 ; $i<count($requires) ; $i++){
	require_once $requires[$i];
}

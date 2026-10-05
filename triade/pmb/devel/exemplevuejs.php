<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: exemplevuejs.php,v 1.1 2020/05/14 14:09:12 gneveu Exp $

// définition du minimum nécessaire 

use Pmb\Common\Views\VueJsView;

$base_path="..";                            
$base_auth = "";  
$base_title = "\$msg[7]"; 
$base_use_dojo = 1;   
require_once ("$base_path/includes/init.inc.php");  


$test = new VueJsView("exemple/exemple",['lastname' => "Renou" , 'firstname' => "Arnaud"]);
print $test->render();
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint_see.inc.php,v 1.79 2018-12-05 09:11:55 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/authorities/page/authority_page_indexint.class.php");

$id += 0;
if($id) {
	$authority_page = new authority_page_indexint($id);
	$authority_page->proceed('indexint');
} else {
    $indexint = new indexint(0);
    print "
	<div id='aut_details'>\n
		<h3><span>$msg[detail_indexint]</span></h3>
		<div id='aut_details_container'>
    		" .	$indexint->child_list() . "
    	</div>
    </div>";
    
}
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.inc.php,v 1.1 2012/03/15 09:30:49 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if($cms_build_activate || $_SESSION["cms_build_activate"]){ 
	if(!$pageid ){
		print "
		<div id='new_page' draggable='no' dragtype='opacdrop' oncontextmenu='' recept='no' recepttype='opacdrop' downlight='' highlight='' style='background: none repeat scroll 0% 0% rgb(221, 221, 221); border: 1px dashed red; visibility: visible; display: block;'>
		<br/>
		Nouvelle page
		<br/>
		</div>
		";
	}	
}

?>
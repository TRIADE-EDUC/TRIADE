<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme_see.inc.php,v 1.38 2024/03/22 15:31:02 qvarin Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".inc.php" )) die ( "no access" );
	
// affichage du detail pour un titre uniforme
require_once($class_path."/authorities/page/authority_page_titre_uniforme.class.php");

$id = intval($id);
if ($id) {
	$authority_page = new authority_page_titre_uniforme($id);
	$authority_page->proceed('works');
}
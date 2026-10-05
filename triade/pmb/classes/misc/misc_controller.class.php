<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_controller.class.php,v 1.1 2021/11/25 14:30:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class misc_controller extends lists_controller {
	
	public static function proceed($id=0) {
		// on récupére la liste des tables
		print "<div class='div-contenu'><div class='row tableListe'>";
		parent::proceed($id);
		print "</div></div>";
	}
}
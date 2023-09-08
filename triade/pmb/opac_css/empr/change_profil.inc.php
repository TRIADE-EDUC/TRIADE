<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: change_profil.inc.php,v 1.2 2019-03-14 10:40:00 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch ($action) {
	case "save" :
		print "<span class='alerte'>".$msg['sauv_misc_running']."</span>";
		$emprunteur_datas = emprunteur_display::get_emprunteur_datas($id_empr);
		$emprunteur_datas->set_from_form();
		$emprunteur_datas->save();
		print '<script type="text/javascript">window.location = "./empr.php";</script>';
		break;
	case "get_form" :
	default :
		print emprunteur_display::get_display_profil($id_empr);
		break;
}
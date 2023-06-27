<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_coll.inc.php,v 1.28 2018-12-28 13:15:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/list/records/list_records_bulletins_collstate_edition_ui.class.php");
$list_records_bulletins_collstate_edition_ui = new list_records_bulletins_collstate_edition_ui(array('niveau_biblio' => 's', 'niveau_hierar' => '1'), array(), array());
print $list_records_bulletins_collstate_edition_ui->get_display_list();
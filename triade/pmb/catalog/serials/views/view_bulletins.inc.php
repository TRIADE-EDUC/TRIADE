<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_bulletins.inc.php,v 1.38 2022/01/18 10:20:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//DG - 12/07/2020 - Attention à la globalisation de variables - fichier inclus dans le contexte d'une fonction
global $msg, $nb_per_page_a_search;
global $pmb_collstate_advanced;
global $serial_id, $bull_date_start, $bull_date_end;

//LIST UI
$list_bulletins_ui = list_bulletins_ui::get_instance(array('serial_id' => $serial_id));

$bulletins = $list_bulletins_ui->get_display_list();

$pages_display = $list_bulletins_ui->get_bulletins_pagination();
?>

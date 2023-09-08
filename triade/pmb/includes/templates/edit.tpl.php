<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.tpl.php,v 1.38 2019-05-27 12:13:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $edit_menu, $msg, $pmb_short_loan_management, $pmb_pnb_param_login, $pmb_resa_planning,  $pmb_gestion_financiere_caisses, $pmb_transferts_actif, $transferts_validation_actif, $pmb_logs_activate, $edit_layout, $current_module, $edit_layout_end;

// $edit_menu : menu page Editions
$edit_menu = "
<div id='menu'>
<h3 onclick='menuHide(this,event)'>$msg[1130]</h3>
<ul>
<li><a href='./edit.php?categ=procs'>$msg[1131]</a></li>
<li><a href='./edit.php?categ=state'>".$msg['editions_state']."</a></li>
</ul>
<h3 onclick='menuHide(this,event)'>$msg[1110]</h3>
<ul>
<li><a href='./edit.php?categ=expl&sub=encours'>$msg[1111]</a></li>
<li><a href='./edit.php?categ=expl&sub=retard'>$msg[1112]</a></li>
<li><a href='./edit.php?categ=expl&sub=retard_par_date'>".$msg['edit_expl_retard_par_date']."</a></li>
<li><a href='./edit.php?categ=expl&sub=ppargroupe'>$msg[1114]</a></li>
<li><a href='./edit.php?categ=expl&sub=rpargroupe'>".$msg["menu_retards_groupe"]."</a></li>";

if ($pmb_short_loan_management==1) {
$edit_menu.= "
</ul>
<h3 onclick='menuHide(this,event)'>".$msg['short_loans']."</h3>
<ul>
<li><a href='./edit.php?categ=expl&sub=short_loans'>".$msg['short_loans']."</a></li>
<li><a href='./edit.php?categ=expl&sub=unreturned_short_loans'>".$msg['unreturned_short_loans']."</a></li>
<li><a href='./edit.php?categ=expl&sub=overdue_short_loans'>".$msg['overdue_short_loans']."</a></li>
";	
}
if($pmb_pnb_param_login) {
	$edit_menu.= "
	</ul>
	<h3 onclick='menuHide(this,event)'>" . $msg['edit_menu_pnb'] . "</h3>
	<ul>
		<li><a href='./edit.php?categ=pnb&sub=orders'>".$msg["edit_menu_pnb_orders"]."</a></li>
		";
}
// <li><a href='./edit.php?categ=pnb&sub=loans'>".$msg["1111"]."</a></li>
// <li><a href='./edit.php?categ=pnb&sub=group_loans'>".$msg["1114"]."</a></li>
 
$edit_menu.= "
</ul>
<h3 onclick='menuHide(this,event)'>$msg[350]</h3>
<ul>
<li><a href='./edit.php?categ=notices&sub=resa'>".$msg['edit_resa_menu']."</a></li>
<li><a href='./edit.php?categ=notices&sub=resa_a_traiter'>".$msg['edit_resa_menu_a_traiter']."</a></li>
".($pmb_resa_planning ? "<li><a href='./edit.php?categ=notices&sub=resa_planning'>".$msg['edit_resa_planning_menu']."</a></li>" : "")."
</ul>
<h3 onclick='menuHide(this,event)'>$msg[1120]</h3>
<ul>
<li><a href='./edit.php?categ=empr&sub=encours'>$msg[1121]</a></li>
<li><a href='./edit.php?categ=empr&sub=limite'>".$msg['edit_menu_empr_abo_limite']."</a></li>
<li><a href='./edit.php?categ=empr&sub=depasse'>".$msg['edit_menu_empr_abo_depasse']."</a></li>
<li><a href='./edit.php?categ=empr&sub=categ_change'>".$msg['edit_menu_empr_categ_change']."</a></li>
".($pmb_gestion_financiere_caisses ? "<li><a href='./edit.php?categ=empr&sub=cashdesk'>".$msg['cashdesk_edition']."</a></li>" : "")."
</ul>
<h3 onclick='menuHide(this,event)'>$msg[1150]</h3>
<ul>
<li><a href='./edit.php?categ=serials&sub=collect'>$msg[1151]</a></li>
<!-- <li><a href='./edit.php?categ=serials&sub=manquant'>$msg[1154]</a></li> -->
<li><a href='./edit.php?categ=serials&sub=circ_state'>".$msg["serial_circ_state_edit"]."</a></li>
<li><a href='./edit.php?categ=serials&sub=simple_circ'>".$msg["serial_simple_circ_edit"]."</a></li>
</ul>
<h3 onclick='menuHide(this,event)'>$msg[1140]</h3>
<ul>
<li><a href='./edit.php?categ=cbgen&sub=libre'>$msg[1141]</a></li>
</ul>
<h3 onclick='menuHide(this,event)'>".$msg['sticks_sheet']."</h3>
<ul>
<li><a href='./edit.php?categ=sticks_sheet&sub=models'>".$msg['sticks_sheet_models']."</a></li>
</ul>

<h3 onclick='menuHide(this,event)'>".$msg["edit_tpl_menu"]."</h3>
<ul>
<li><a href='./edit.php?categ=tpl&sub=notice'>".$msg["edit_notice_tpl_menu"]."</a></li>
<li><a href='./edit.php?categ=tpl&sub=serialcirc'>".$msg["edit_serialcirc_tpl_menu"]."</a></li>
<li><a href='./edit.php?categ=tpl&sub=bannette'>".$msg["edit_bannette_tpl_menu"]."</a></li>
</ul>";

if ($pmb_transferts_actif=="1") {
	$edit_menu .= "
		<h3 onclick='menuHide(this,event)'>".$msg['transferts_edition_titre']."</h3>
		<ul>";
	if ($transferts_validation_actif=="1")
		$edit_menu .= "
			<li><a href='./edit.php?categ=transferts&sub=validation'>".$msg['transferts_edition_validation']."</a></li>";
	$edit_menu .= "
		<li><a href='./edit.php?categ=transferts&sub=envoi'>".$msg['transferts_edition_envoi']."</a></li>
		<li><a href='./edit.php?categ=transferts&sub=reception'>".$msg['transferts_edition_reception']."</a></li>
		<li><a href='./edit.php?categ=transferts&sub=retours'>".$msg['transferts_edition_retours']."</a></li>
		</ul>
		";
}

$edit_menu .= "<h3 onclick='menuHide(this,event)'>".$msg['opac_admin_menu']."</h3>
<ul>";
if($pmb_logs_activate){
	$edit_menu .= "<li><a href='./edit.php?categ=stat_opac'>".$msg['stat_opac_menu']."</a></li>";
}
$edit_menu .= "<li><a href='./edit.php?categ=opac&sub=campaigns'>".$msg['campaigns']."</a></li>
</ul>";
$plugins = plugins::get_instance();
$edit_menu .= $plugins->get_menu('edit')."</div>";

// $edit_layout : layout page edition
$edit_layout = "
<div id='conteneur' class='$current_module'>
$edit_menu
<div id='contenu'>";

// $edit_layout_end : layout page edition (fin)
$edit_layout_end = "</div></div>";

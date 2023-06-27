<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.tpl.php,v 1.13 2019-05-27 16:19:33 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $id_notice, $id_bulletin, $id_empr, $groupID, $layout_begin, $msg, $menu_search_commun, $menu_search, $form_resa_dates;

if(!isset($id_notice)) $id_notice = 0;
if(!isset($id_bulletin)) $id_bulletin = 0;
if(!isset($id_empr)) $id_empr = 0;
if(!isset($groupID)) $groupID = 0;

// en-tête et pied de page
$layout_begin = "<div class='row'>".sprintf($msg['resa_planning_for_empr'],"<a href='./circ.php?categ=pret&form_cb=!!cb_lecteur!!&groupID=$groupID'>!!nom_lecteur!!</a>")."</div>";

$menu_search_commun = "
	<br />
	<div class='hmenu'>
		<span".ongletSelect("categ=resa_planning&resa_action=search_resa&mode=0").">
			<a href='./circ.php?categ=resa_planning&resa_action=search_resa&mode=0&id_empr=$id_empr&groupID=$groupID'>$msg[354]</a>
		</span>
		<span".ongletSelect("categ=resa_planning&resa_action=search_resa&mode=1").">
			<a href='./circ.php?categ=resa_planning&resa_action=search_resa&mode=1&id_empr=$id_empr&groupID=$groupID'>$msg[355]</a>
		</span>
		<span".ongletSelect("categ=resa_planning&resa_action=search_resa&mode=5").">
			<a href='./circ.php?categ=resa_planning&resa_action=search_resa&mode=5&id_empr=$id_empr&groupID=$groupID'>".$msg['search_by_terms']."</a>
		</span>
		<span".ongletSelect("categ=resa_planning&resa_action=search_resa&mode=2").">
			<a href='./circ.php?categ=resa_planning&resa_action=search_resa&mode=2&id_empr=$id_empr&groupID=$groupID'>$msg[356]</a>
		</span>
		<span".ongletSelect("categ=resa_planning&resa_action=search_resa&mode=3").">
			<a href='./circ.php?categ=resa_planning&resa_action=search_resa&mode=3&id_empr=$id_empr&groupID=$groupID'>".$msg['search_by_panier']."</a>
		</span>
		<span".ongletSelect("categ=resa_planning&resa_action=search_resa&mode=6").">
			<a href='./circ.php?categ=resa_planning&resa_action=search_resa&mode=6&id_empr=$id_empr&groupID=$groupID'>".$msg['search_extended']."</a>
		</span>
	</div>";

$menu_search[0] = $menu_search_commun;
$menu_search[1] = $menu_search_commun;
$menu_search[2] = $menu_search_commun;
$menu_search[3] = $menu_search_commun;
$menu_search[4] = $menu_search_commun;
$menu_search[6] = $menu_search_commun;

$form_resa_dates = "
<script type='text/javascript'>
	function test_form(form) {
		var t_sel=form.getElementsByTagName('select');
		var resa_qty = 0;
		for(var i=0;i<t_sel.length;i++) {
			resa_qty = resa_qty + t_sel[i].value*1;
		}
		if(resa_qty==0 || isNaN(resa_qty)) {
			alert(\"".$msg['resa_planning_alert_qty']."\");
			return false;
		}
		if(form.resa_deb.value >= form.resa_fin.value){
			alert(\"".$msg['resa_planning_alert_date']."\");
			return false;
	    }
		return true;
	}
</script>
<h3>".$msg['resa_planning_dates']."</h3>
<form action='./circ.php?categ=resa_planning&resa_action=add_resa_suite&id_empr=".$id_empr."&groupID=&id_notice=".$id_notice."&id_bulletin=".$id_bulletin."' method='post' name='dates_resa'>
<div class='form-contenu'>
		<div class='row' >
			<label >".$msg['resa_planning_date_debut']."</label>&nbsp;
			<input type='hidden' name='resa_deb' value='!!resa_deb!!' />
			<input type='button' class='bouton' name='resa_date_debut' value='!!resa_date_debut!!' onclick=\"openPopUp('./select.php?what=calendrier&caller=dates_resa&date_caller=!!resa_deb!!&param1=resa_deb&param2=resa_date_debut&auto_submit=NO&date_anterieure=YES', 'calendar')\" />
			&nbsp;
			<label>".$msg['resa_planning_date_fin']."</label>&nbsp;
			<input type='hidden' name='resa_fin' value='!!resa_fin!!'  />
			<input type='button' class='bouton' name='resa_date_fin' value='!!resa_date_fin!!' onclick=\"openPopUp('./select.php?what=calendrier&caller=dates_resa&date_caller=!!resa_fin!!&param1=resa_fin&param2=resa_date_fin&auto_submit=NO&date_anterieure=YES', 'calendar')\" />
		</div>
		!!resa_loc_retrait!!
		<div class='row' >
		</div>
	</div>
	<div class='row' >
		<input type='submit' name='ok' value='".$msg[77]."' class='bouton' onClick='return test_form(this.form);' />
	</div>
</form>";
		

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.tpl.php,v 1.15 2021/05/03 07:59:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $id_notice, $id_bulletin, $id_empr, $groupID, $layout_begin, $msg, $form_resa_dates;

if(!isset($id_notice)) $id_notice = 0;
if(!isset($id_bulletin)) $id_bulletin = 0;
if(!isset($id_empr)) $id_empr = 0;
if(!isset($groupID)) $groupID = 0;

// en-tête et pied de page
$layout_begin = "<div class='row'>".sprintf($msg['resa_planning_for_empr'],"<a href='./circ.php?categ=pret&form_cb=!!cb_lecteur!!&groupID=$groupID'>!!nom_lecteur!!</a>")."</div>";

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
			<input type='date' name='resa_deb' value='!!resa_deb!!' />
			&nbsp;
			<label>".$msg['resa_planning_date_fin']."</label>&nbsp;
			<input type='date' name='resa_fin' value='!!resa_fin!!'  />
		</div>
		!!resa_loc_retrait!!
		<div class='row' >
		</div>
	</div>
	<div class='row' >
		<input type='submit' name='ok' value='".$msg[77]."' class='bouton' onClick='return test_form(this.form);' />
	</div>
</form>";
		

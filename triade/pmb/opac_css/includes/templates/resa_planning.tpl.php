<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.tpl.php,v 1.13 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $form_resa_planning_add, $form_resa_planning_confirm, $form_resa_planning_add_from_cart, $form_resa_planning_add_from_cart_item, $form_resa_planning_add_from_cart_loc_retrait_table, $msg;
global $form_resa_planning_add_from_cart_loc_retrait_row, $form_resa_planning_add_from_cart_loc_retrait_option, $form_resa_planning_add_from_cart_loc_retrait_none;
global $opac_rgaa_active;

// templates pour le formulaire de pose de reservation planifiee
$form_resa_planning_add = "";
if($opac_rgaa_active) {
    $form_resa_planning_add .= '<h1><span>'.$msg['resa_planning_add'].'</span></h1>';
} else {
    $form_resa_planning_add .= '<h3><span>'.$msg['resa_planning_add'].'</span></h3>';
}
$form_resa_planning_add.= '
<script>
	function test_form(form) {
		var t_sel=form.getElementsByTagName("select");
		var resa_qty = 0;
		for(var i=0;i<t_sel.length;i++) {
			resa_qty = resa_qty + t_sel[i].value*1;
		}
		if(resa_qty==0 || isNaN(resa_qty)) {
			alert("'.$msg['resa_planning_alert_qty'].'");
			return false;
		}
		if(form.resa_deb.value >= form.resa_fin.value){
			alert("'.$msg['resa_planning_alert_date'].'");
			return false;
	    }
		return true;
	}
</script>
<h3>'.$msg['resa_date_planning'].'</h3>
<form action="./do_resa.php" method="post" name="dates_resa">
	<div>
		<label>'.$msg['resa_planning_date_debut'].'</label>
		&nbsp;
		<input type="hidden" name="resa_deb" value="'.date('Y-m-d').'" />
		<input type="button" name="resa_deb_bt" id="resa_deb_bt" value="'.date($msg['date_format']).'"
				onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_deb&param2=resa_deb_bt&auto_submit=NO&date_anterieure=NO\', \'resa_deb\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
		<img src="'.get_url_icon('calendar.jpg').'"
				onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_deb&param2=resa_deb_bt&auto_submit=NO&date_anterieure=NO\', \'resa_deb\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
		&nbsp;
		<label>'.$msg['resa_planning_date_fin'].'</label>
		&nbsp;
		<input type="hidden" name="resa_fin" value="'.date('Y-m-d').'" />
		<input type="button" name="resa_fin_bt" id="resa_fin_bt" value="'.date($msg['date_format']).'"
				onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_fin&param2=resa_fin_bt&auto_submit=NO&date_anterieure=NO\', \'resa_fin\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
		<img src="'.get_url_icon('calendar.jpg').'"
				onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_fin&param2=resa_fin_bt&auto_submit=NO&date_anterieure=NO\', \'resa_fin\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
		&nbsp;
		<input type="hidden" name="id_notice" value="!!id_notice!!" />
		<input type="hidden" name="id_bulletin" value="!!id_bulletin!!" />
		<input type="hidden" name="lvl" value="resa_planning" />
		<input type="hidden" name="connectmode" value="popup" />
	</div>
	<div>
		!!resa_loc_retrait!!
	</div>
	<input type="submit" value="'.$msg[11].'" class="bouton" onClick="return test_form(this.form);" />
</form>';

$form_resa_planning_confirm = "
<br />
<span class='alerte'>".$msg['added_resa']."<br />".
$msg['resa_date_debut']."!!date_deb!!&nbsp;".$msg['resa_date_fin']."!!date_fin!!</span>";


//templates pose resa planifiee a partir d'un panier
if ($opac_rgaa_active) {
    $resa_planning_title = '<h1>'.$msg['resa_planning_add_from_cart'].'</h1>';
} else {
    $resa_planning_title = '<h3>'.$msg['resa_planning_add_from_cart'].'</h3>';
}


$form_resa_planning_add_from_cart = $resa_planning_title . '
<form action="./do_resa.php?lvl=resa_cart&sub=resa_planning_cart_checked&step=2" method="post" name="dates_resa">
	<!-- items -->
	<input type="submit" value="'.$msg[11].'" class="bouton" />
</form>';

$form_resa_planning_add_from_cart_item = '
<div>&nbsp;
	<h3>'.$msg['resa_date_planning'].'</h3>
	<div>
	<label>'.$msg['resa_planning_date_debut'].'</label>
	&nbsp;
	<input type="hidden" name="resa_deb[!!id_notice!!]" value="'.date('Y-m-d').'" />
	<input type="button" name="resa_deb_bt[!!id_notice!!]" id="resa_deb_bt[!!id_notice!!]" value="'.date($msg['date_format']).'"
			onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_deb[!!id_notice!!]&param2=resa_deb_bt[!!id_notice!!]&auto_submit=NO&date_anterieure=NO\', \'resa_deb[!!id_notice!!]\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
			<img src="'.get_url_icon('calendar.jpg').'"
			onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_deb[!!id_notice!!]&param2=resa_deb_bt[!!id_notice!!]&auto_submit=NO&date_anterieure=NO\', \'resa_deb[!!id_notice!!]\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
			&nbsp;
	<label>'.$msg['resa_planning_date_fin'].'</label>
	&nbsp;
	<input type="hidden" name="resa_fin[!!id_notice!!]" value="'.date('Y-m-d').'" />
	<input type="button" name="resa_fin_bt[!!id_notice!!]" id="resa_fin_bt[!!id_notice!!]" value="'.date($msg['date_format']).'"
			onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_fin[!!id_notice!!]&param2=resa_fin_bt[!!id_notice!!]&auto_submit=NO&date_anterieure=NO\', \'resa_fin[!!id_notice!!]\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
			<img src="'.get_url_icon('calendar.jpg').'"
			onclick="window.open(\'./select.php?what=calendrier&caller=dates_resa&date_caller=&param1=resa_fin[!!id_notice!!]&param2=resa_fin_bt[!!id_notice!!]&auto_submit=NO&date_anterieure=NO\', \'resa_fin[!!id_notice!!]\', \'width=250,height=300,toolbar=no,dependent=yes,resizable=yes\')" />
			&nbsp;
		<input type="hidden" name="resa_notices[]" value="!!id_notice!!" />
	</div>
	<div>
	!!resa_loc_retrait!!
	</div>
</div>';

$form_resa_planning_add_from_cart_loc_retrait_table = '
<table>
<tbody>
	<tr>
		<th>'.$msg['resa_planning_loc_retrait'].'</th>
		<th>'.$msg['resa_planning_qty_requested'].'</th>
	</tr>
	<!-- rows -->
</tbody>
</table>';


$form_resa_planning_add_from_cart_loc_retrait_row = '
<tr>
	<td style="width:50%">!!location_label!!</td>
	<td>
		<select name="resa_qte[!!id_notice!!][!!id_location!!]">
		<!-- options -->
		</select>
	</td>
</tr>';

$form_resa_planning_add_from_cart_loc_retrait_option = '<option value="!!val!!">!!val!!</option>';

$form_resa_planning_add_from_cart_loc_retrait_none = $msg['resa_planning_no_item_available'];

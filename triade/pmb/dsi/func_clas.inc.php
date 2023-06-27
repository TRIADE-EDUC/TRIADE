<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_clas.inc.php,v 1.9 2017-02-24 09:40:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function dsi_list_classements() {
	global $dbh, $msg;
	global $dsi_list_tmpl;
	global $form_cb;
	
	// tableau des classements de bannettes
	$requete = "SELECT id_classement, nom_classement, classement_opac_name, type_classement FROM classements where (type_classement='' or type_classement='BAN')
			ORDER BY classement_order, nom_classement";
	$res = @pmb_mysql_query($requete, $dbh);	
	$parity = 0;
	if($nb=pmb_mysql_num_rows($res)){
		$tpl_bannettes=$dsi_list_tmpl;
		$list='
				<tr>
					<th></th>
					<th>'.$msg['103'].'</th>
					<th>'.$msg['dsi_clas_form_nom_opac'].'</th>				
				</tr>
				';
		while(($clas=pmb_mysql_fetch_object($res))) {
			if ($parity % 2) $pair_impair = "even";	else $pair_impair = "odd";
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./dsi.php?categ=options&sub=classements&id_classement=$clas->id_classement&suite=acces';\" ";
			$list .= "
				<tr class='$pair_impair'>
					<td>
						<input class='bouton_small' type='button' onclick=\"document.location='./dsi.php?categ=options&sub=classements&id_classement=$clas->id_classement&suite=up'\" value='-'>
						<input class='bouton_small' type='button' onclick=\"document.location='./dsi.php?categ=options&sub=classements&id_classement=$clas->id_classement&suite=down'\" value='+'>
					</td>				
					<td $tr_javascript style='cursor: pointer'>
						<strong>$clas->nom_classement</strong>
					</td>				
					<td $tr_javascript style='cursor: pointer'>
						$clas->classement_opac_name
					</td>
				</tr>";
			$parity++;
		}			
		$tpl_bannettes = str_replace("!!list!!", $list, $tpl_bannettes);
		$tpl_bannettes = str_replace("!!nav_bar!!", '', $tpl_bannettes);
		$tpl_bannettes = str_replace("!!message_trouve!!", $msg['dsi_clas_type_class_BAN']." ($nb)", $tpl_bannettes);
	}
	// tableau des classements des équations
	$requete = "SELECT id_classement, nom_classement, type_classement FROM classements where type_classement='EQU' ORDER BY nom_classement";
	$res = @pmb_mysql_query($requete, $dbh);
	$parity = 0;
	$tpl_equations = '';
	if($nb=pmb_mysql_num_rows($res)){
		$tpl_equations=$dsi_list_tmpl;
		$list='
				<tr>
					<th>'.$msg['103'].'</th>		
				</tr>';
		while(($clas=pmb_mysql_fetch_object($res))) {
			if ($parity % 2) $pair_impair = "even";	else $pair_impair = "odd";
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./dsi.php?categ=options&sub=classements&id_classement=$clas->id_classement&suite=acces';\" ";
			$list .= "
				<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
					<td>
						<strong>$clas->nom_classement</strong>
					</td>
				</tr>";
			$parity++;
		}	
		$tpl_equations = str_replace("!!list!!", $list, $tpl_equations);
		$tpl_equations = str_replace("!!nav_bar!!", '', $tpl_equations);
		$tpl_equations = str_replace("!!message_trouve!!", $msg['dsi_clas_type_class_EQU']." ($nb)", $tpl_equations);
	}
		
	$ajout = "<br /><input type='button' class='bouton' value='$msg[dsi_clas_ajouter]' onclick=\"document.location='./dsi.php?categ=options&sub=classements&suite=add'\" />" ;
	return $tpl_bannettes.$tpl_equations.$ajout;		
}
	
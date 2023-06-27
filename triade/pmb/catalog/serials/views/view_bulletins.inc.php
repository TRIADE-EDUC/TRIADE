<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_bulletins.inc.php,v 1.30 2019-06-05 13:13:19 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage des bulletinages associés
// on récupère le nombre de lignes qui vont bien
$bulletins = "
		<script src='javascript/ajax.js'></script>		
		<script type='text/javascript' src='./javascript/bulletin_list.js'></script>
		<script type='text/javascript'>
 			var msg_select_all = '".$msg["notice_expl_check_all"]."';
			var msg_unselect_all = '".$msg["notice_expl_uncheck_all"]."';
 			var msg_have_select_bulletin = '".$msg["bulletin_have_select"]."';
		</script>
		<form action='".$base_url."' method='post' name='filter_form'>
			<input type='hidden' name='location' value='".$location."'/>
			<table>";

$date_debut = "<input type='text' name='bull_date_start' id='bull_date_start' value='".$bull_date_start."' style='width: 8em;' data-dojo-type='dijit/form/DateTextBox' onchange='document.filter_form.submit();' />";
$date_fin = "<input type='text' name='bull_date_end' id='bull_date_end' value='".$bull_date_end."' style='width: 8em;' data-dojo-type='dijit/form/DateTextBox' onchange='document.filter_form.submit();' />";

$bulletins .= "
		<tr>
			<th></th>			
			<th>".$msg[4025]."</th>
			<th>".$msg[4026]."</th>
			<th>".$msg['bulletin_mention_periode']."</th>
			<th>".$msg['bulletin_mention_titre_court']."</th>
			<th>".$msg['bul_articles']."</th>
			<th>".$msg['bul_docnum']."</th>
			<th>".$msg['bul_exemplaires']."</th>";
			
if ($pmb_collstate_advanced) {
	$bulletins .= "<th>".$msg['bul_collstate']."</th>";
}

$bulletins .= "
		</tr>
		<tr>
			<th class='align_left'>
				<input type='checkbox' name='check_all_bulletins_".$serial_id."' value='1' title='".$msg["notice_expl_check_all"]."' 
					onClick=\"check_all_bulletins(this, document.getElementById('bulletins_to_check_".$serial_id."').value);\">
				<input id='bulletins_to_check_".$serial_id."' type='hidden' value='!!bulletins_to_check!!' name='bulletins_to_check_".$serial_id."'>
				<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".$msg[400]."' onClick=\"
					if(check_if_bulletins_checked(document.getElementById('bulletins_to_check_".$serial_id."').value,'cart'))
					openPopUp('./cart.php?object_type=BULL&item=' + get_bulletins_checked(document.getElementById('bulletins_to_check_".$serial_id."').value),
					'cart')\">
			</th>
			<th>
				<input type='text' autfield='f_bull_date_id' completion='bull_num' autocomplete='off' id='bull_num_deb_".$serial_id."' class='saisie-10em' name='aff_bulletins_restrict_numero' onchange='this.form.submit();' value='".htmlentities($aff_bulletins_restrict_numero,ENT_QUOTES, $charset)."'/>
				<input type='hidden' name='f_bull_date_id' id='f_bull_date_id'>
			</th>		
			<th>".$msg["search_bull_start"]." ".$date_debut." ".$msg["search_bull_end"]." ".$date_fin."</th>
			<th><input type='text' class='saisie-10em' name='aff_bulletins_restrict_periode' onchange='this.form.submit();' value='".htmlentities($aff_bulletins_restrict_periode,ENT_QUOTES, $charset)."'/></th>
			<th></th><th></th><th></th><th></th>";
if ($pmb_collstate_advanced) {
	$bulletins .= "<th></th>";
}
$bulletins .="
		</tr>";

$bulletins_to_check=array();
// ici : affichage par page des bulletinages associés
// on lance la vraie requette

$clause = ($location?"and (expl_bulletin=bulletin_id and expl_location='$location') ":"")." ".($serial_id ? "and bulletin_notice='$serial_id'" : "")." ".$clause;
$myQuery = pmb_mysql_query("SELECT distinct bulletin_id FROM bulletins ".($location?",exemplaires ":"").(trim($clause) ? " WHERE 1 ".$clause : "")." $filter_date ORDER BY date_date DESC, bulletin_numero*1 DESC, bulletin_id DESC LIMIT $debut,$nb_per_page_a_search", $dbh);
if((pmb_mysql_num_rows($myQuery))) {
	$parity=1;
	while(($bul = pmb_mysql_fetch_object($myQuery))) {
		$collstates = array();
		if ($pmb_collstate_advanced) {
			$query = "SELECT collstate_bulletins_num_collstate, state_collections FROM collstate_bulletins JOIN collections_state ON collections_state.collstate_id = collstate_bulletins.collstate_bulletins_num_collstate WHERE collstate_bulletins_num_bulletin = '".$bul->bulletin_id."'";
			$result = pmb_mysql_query($query);			
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$collstates[$row->collstate_bulletins_num_collstate] = $row->state_collections;
				}
			}
		}
		$bulletin = new bulletinage($bul->bulletin_id,0,'',$location,false);
		if ($parity % 2) {
			$pair_impair = "even";
		}
		else {
			$pair_impair = "odd";
		}
		$parity += 1;				
		
        $href_start="<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$bulletin->bulletin_id."' style='display:block;'>";        
        $tr_surbrillance = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
		$bulletins .= "<tr class='".$pair_impair."' ".$tr_surbrillance." style='cursor: pointer'><td>";
		$bulletins .= "<input type='checkbox' name='checkbox_bulletin[".$bulletin->bulletin_id."]' id='checkbox_bulletin[".$bulletin->bulletin_id."]' value='1'>";
		$drag="<span id=\"BULL_drag_".$bulletin->bulletin_id."\" dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext=\"".htmlentities($bulletin->bulletin_numero,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".get_url_icon('notice_drag.png')."\"/></span>";
		$bulletins .= "$drag</td><td>".$href_start;
		$bulletins .= $bulletin->bulletin_numero;
		$bulletins .= "</a></td><td>".$href_start;
		$bulletins .= $bulletin->aff_date_date;
		$bulletins .= "</a></td><td>".$href_start;
		$bulletins .= htmlentities($bulletin->mention_date, ENT_QUOTES, $charset);
		$bulletins .= "</a></td><td>".$href_start;
		$bulletins .= htmlentities($bulletin->bulletin_titre, ENT_QUOTES, $charset);
		$bulletins .= "</a></td><td class='center'>" ;
		if ($bulletin->nb_analysis) {
			$bulletins .= $bulletin->nb_analysis."&nbsp;<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title='".$msg[400]."' onClick=\"openPopUp('./cart.php?object_type=BULL&item=".$bulletin->bulletin_id."&what=DEP', 'cart')\">"; 
		}
		else {
			$bulletins .= "&nbsp;";
		}
		$bulletins .= "</td><td class='center'>".$href_start;
		if (is_array($bulletin->nbexplnum) && sizeof($bulletin->nbexplnum)) {
			$bulletins .= $bulletin->nbexplnum; 
		}else {
			$bulletins .= "&nbsp;";
		}
		$bulletins .= "</a></td><td class='center'>".$href_start;
		if (is_array($bulletin->expl) && sizeof($bulletin->expl)) {
			$bulletins .= sizeof($bulletin->expl); 
		} else {
			$bulletins .= "&nbsp;";
		}
		$bulletins .= "</a></td>";
		if ($pmb_collstate_advanced) {
			$bulletins .= "<td>";
			$collstate_list = "";
			foreach($collstates as $id => $collstate) {
				if($collstate_list) {
					$collstate_list.= "<br/>";
				}
				$collstate_list .="<a href='./catalog.php?categ=serials&sub=collstate_bulletins_list&id=".$id."&serial_id=".$serial_id."&bulletin_id=0'>".$collstate."</a>";
				
			}			
			$bulletins .= $collstate_list."</td>";
		}
		
		$bulletins .= "</tr>";
		$bulletins_to_check[]=$bulletin->bulletin_id;
	}
	$bulletins .= "</table></form>";
} else {
	$bulletins .= "</table><br />";
   	if ($aff_bulletins_restrict_periode || $aff_bulletins_restrict_date || $aff_bulletins_restrict_numero) $bulletins .= $msg['perio_restrict_no_bulletin'];
   	else $bulletins .= $msg[4024] ;
}
$bulletins .= "<script type='text/javascript'>ajax_parse_dom();</script>";
$bulletins = str_replace('!!bulletins_to_check!!', implode(',',$bulletins_to_check), $bulletins);
// barre de navigation par page
$pages_display = aff_pagination ($base_url."&location=$location&bull_date_start=$bull_date_start&bull_date_end=$bull_date_end", $nbr_lignes, $nb_per_page_a_search, $page, 10, false, true);
?>

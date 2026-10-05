<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bulletin.inc.php,v 1.14 2019/08/29 10:05:39 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $caller, $param1, $param2, $no_display, $bt_ajouter, $idperio, $base_url;

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=bulletin&caller=$caller&param1=$param1&param2=$param2&no_display=$no_display&bt_ajouter=$bt_ajouter&idperio=$idperio";

$selector_bulletin = new selector_bulletin(stripslashes($user_input));
$selector_bulletin->proceed();

function show_results ($user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $msg;
	global $no_display ;
	global $charset;
	global $idperio;
	
	if(!$idperio){
		$requete = "SELECT bulletin_notice FROM bulletins where bulletin_id=".$no_display." ";
		$res = @pmb_mysql_query($requete);
		if(!($bull=pmb_mysql_fetch_object($res))) {
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
			exit;
		}	
		$bulletin_notice = $bull->bulletin_notice;
	} else if(!$no_display && $idperio){
		$bulletin_notice = $idperio;
		$no_display = 0;
	}
	
	// on récupére le nombre de lignes qui vont bien	
	if($user_input=="") {
		$requete = "SELECT COUNT(1) FROM bulletins where bulletin_id!='".$no_display."' and bulletin_notice =$bulletin_notice ";
	}  else {
		$requete = "SELECT COUNT(1) FROM bulletins where bulletin_numero like '%".str_replace("*","%",$user_input)."%' and bulletin_id!='".$no_display."' and bulletin_notice =$bulletin_notice ";		
	}	
	$res = pmb_mysql_query($requete);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);

	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;

	if ($nbr_lignes) {
		// on lance la vraie requête
		if($user_input=="") $requete = "SELECT bulletin_numero,mention_date, date_date,bulletin_titre,bulletin_id FROM bulletins where bulletin_id!=".$no_display." and bulletin_notice =$bulletin_notice ORDER BY date_date LIMIT $debut,$nb_per_page ";
		else $requete = "SELECT bulletin_numero,mention_date,bulletin_titre,bulletin_id FROM bulletins where bulletin_numero like '%".str_replace("*","%",$user_input)."%' and bulletin_id!=".$no_display." and bulletin_notice =$bulletin_notice ORDER BY date_date LIMIT $debut,$nb_per_page ";
		
		$res = @pmb_mysql_query($requete);
		print "<table><tr>";
		while(($bull=pmb_mysql_fetch_object($res))) {
			$notice_entry = $bull->bulletin_titre."&nbsp;".$bull->mention_date;
			print "
				<tr>
					<td>
						<a href='#' onclick=\"set_parent('$caller', '$bull->bulletin_id', '".htmlentities(addslashes($bull->bulletin_numero),ENT_QUOTES,$charset)." (".htmlentities(addslashes($bull->bulletin_titre),ENT_QUOTES,$charset).")' )\">".htmlentities($bull->bulletin_numero,ENT_QUOTES,$charset)."</a></td>
					<td>$notice_entry</td>";
			print "</tr>";
			}
		print "</table>";
	
		// constitution des liens
		$nbepages = ceil($nbr_lignes/$nb_per_page);
		$suivante = $page+1;
		$precedente = $page-1;

		// affichage du lien précédent si nécéssaire
		print '<hr /><div class="center">';
		if($precedente > 0)
		print "<a href='$base_url&page=$precedente&nbr_lignes=$nbr_lignes&user_input=".rawurlencode(stripslashes($user_input))."'><img src='".get_url_icon('left.gif')."' style='border:0px; margin:3px 3px' title='$msg[48]' alt='[$msg[48]]' class='align_middle' /></a>";
		for($i = 1; $i <= $nbepages; $i++) {
			if($i==$page)
				print "<b>$i/$nbepages</b>";
		}
		if($suivante<=$nbepages)
			print "<a href='$base_url&page=$suivante&nbr_lignes=$nbr_lignes&user_input=".rawurlencode(stripslashes($user_input))."'><img src='".get_url_icon('right.gif')."' style='border:0px; margin:3px 3px' title='$msg[49]' alt='[$msg[49]]' class='align_middle' /></a>";
		}
		print '</div>';
}

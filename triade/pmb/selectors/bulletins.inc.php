<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bulletins.inc.php,v 1.7 2019/06/06 13:42:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=bulletins&caller=$caller&param1=$param1&param2=$param2&no_display=$no_display";

// classe pour la gestion du sélecteur
require("./selectors/classes/selector_bulletins.class.php");

$selector_bulletins = new selector_bulletins(stripslashes($user_input));
$selector_bulletins->proceed();

function show_results ($user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $msg;
	global $no_display ;
	global $charset;

	// on récupére le nombre de lignes qui vont bien	
	if($user_input=="") {
		$requete = "SELECT COUNT(1) FROM bulletins where bulletin_id!='".$no_display."' ";
	}  else {
		$requete = "SELECT COUNT(1) FROM bulletins , notices where bulletin_notice=notice_id and (bulletin_numero like '%".str_replace("*","%",$user_input)."%' or tit1 like '%".str_replace("*","%",$user_input)."%' ) and bulletin_id!='".$no_display."' ";		
	}	
	$res = pmb_mysql_query($requete);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);

	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;

	if ($nbr_lignes) {
		// on lance la vraie requête
		if($user_input=="") $requete = "SELECT  tit1, mention_date, date_date,bulletin_titre,bulletin_id , if(bulletin_titre is not null and bulletin_titre!='',concat(bulletin_titre,' - ',bulletin_numero),bulletin_numero) as bulletin_numero FROM bulletins, notices where bulletin_notice=notice_id and bulletin_id!=".$no_display." ORDER BY tit1, date_date LIMIT $debut,$nb_per_page ";
		else $requete = "SELECT tit1, mention_date,bulletin_titre,bulletin_id, if(bulletin_titre is not null and bulletin_titre!='',concat(bulletin_titre,' - ',bulletin_numero),bulletin_numero) as bulletin_numero  FROM bulletins, notices where bulletin_notice=notice_id and (bulletin_numero like '%".str_replace("*","%",$user_input)."%' or tit1 like '%".str_replace("*","%",$user_input)."%' ) and bulletin_id!=".$no_display." ORDER BY tit1, date_date LIMIT $debut,$nb_per_page ";
		$res = @pmb_mysql_query($requete);
		print "<table><tr>";
		while(($bull=pmb_mysql_fetch_object($res))) {
			$notice_entry = $bull->bulletin_titre."&nbsp;".$bull->mention_date;
			print "
				<tr>
					<td>
						<a href='#' onclick=\"set_parent('$caller', '$bull->bulletin_id', '".htmlentities(addslashes($bull->tit1.' / '.$bull->bulletin_numero),ENT_QUOTES,$charset)."' )\">".htmlentities( $bull->tit1.' / '.$bull->bulletin_numero,ENT_QUOTES,$charset)."</a></td>
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

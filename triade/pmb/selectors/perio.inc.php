<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio.inc.php,v 1.14 2020/10/29 08:44:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $caller, $param1, $param2, $no_display, $bt_ajouter ,$user_input;
global $niveau_biblio, $modele_id, $serial_id;

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=perio&caller=$caller&param1=$param1&param2=$param2&no_display=$no_display&bt_ajouter=$bt_ajouter";
if(!empty($niveau_biblio)) {
	$base_url .= "&niveau_biblio=".$niveau_biblio;
}
if(!empty($modele_id)) {
	$base_url .= "&modele_id=".$modele_id;
}
if(!empty($serial_id)) {
	$base_url .= "&serial_id=".$serial_id;
}
$selector_perio = new selector_perio(stripslashes($user_input));
$selector_perio->proceed();

function show_results ($user_input, $nbr_lignes=0, $page=0, $id = 0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $msg;
	global $no_display ;
	global $charset;
	global $niveau_biblio, $modele_id, $serial_id;
	
	// on récupére le nombre de lignes qui vont bien
	if($user_input=="") {
		$requete = "SELECT COUNT(1) FROM notices where notice_id!='".$no_display."' and niveau_biblio='s' and niveau_hierar='1' ";
	} else {
		$aq=new analyse_query($user_input);
		if ($aq->error) {
			error_message($msg["searcher_syntax_error"],sprintf($msg["searcher_syntax_error_desc"],$aq->current_car,$aq->input_html,$aq->error_message));
			exit;
		}
		$members=$aq->get_query_members("notices","index_wew","index_sew","notice_id");
		$requete = "select count(notice_id) from notices where (".$members["where"]." or code like '".addslashes($user_input)."') and notice_id!='".$no_display."' and niveau_biblio='s' and niveau_hierar='1'";
	}
	
	$res = pmb_mysql_query($requete);
	$nbr_lignes = @pmb_mysql_result($res, 0, 0);

	if(!$page) $page=1;
	$debut =($page-1)*$nb_per_page;

	if($nbr_lignes) {
		// on lance la vraie requête

		if($user_input=="") {
			$requete = "SELECT notice_id, tit1, code FROM notices where notice_id!='".$no_display."' and niveau_biblio='s' and niveau_hierar='1' ORDER BY tit1, code LIMIT $debut,$nb_per_page ";
		} else {
		    $requete = "select notice_id, tit1, code, ".$members["select"]." as pert from notices where (".$members["where"]." or code like '".addslashes($user_input)."') and notice_id!='".$no_display."' and niveau_biblio='s' and niveau_hierar='1' group by notice_id order by pert desc, index_serie, tnvol, index_sew, code limit $debut,$nb_per_page";
		}

		$res = @pmb_mysql_query($requete);
		if($niveau_biblio && $modele_id && $serial_id){
		    while(($notice=pmb_mysql_fetch_object($res))) {
		        $location="./catalog.php?categ=serials&sub=modele&act=copy&modele_id=$modele_id&serial_id=$serial_id&new_serial_id=".$notice->notice_id;
		        $mono_display = new mono_display($notice->notice_id, 0, '', 0, '', '', '',0, 0, 0, 0,"", 0, false, true);
		        print "
				<div class='row'>
					<div class='left'>
						<a href='#' onclick=\"copier_modele('$location')\">".$mono_display->header_texte."</a>
					</div>
					<div class='right'>
					".htmlentities($mono_display->notice->code,ENT_QUOTES,$charset)."
					</div>
				</div>";
		    }
		} else {
		    print "<table><tr>";
		    while(($notice=pmb_mysql_fetch_object($res))) {
		        print "
				<tr>
					<td>
						<a href='#' onclick=\"set_parent('$caller', '$notice->notice_id', '".htmlentities(addslashes($notice->tit1),ENT_QUOTES,$charset)." ($notice->code)')\">".htmlentities($notice->tit1,ENT_QUOTES,$charset)."</a></td>
					<td>$notice->code</td>";
		        print "</tr>";
		    }
		    print "</table>";
		}
		pmb_mysql_free_result($res);
	}
		// affichage de la pagination
		
		print "<div class='row'>&nbsp;<hr /></div><div class='center'>";
		$url_base = $base_url."&user_input=".rawurlencode($user_input);
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		print $nav_bar;
		print "</div>";
}
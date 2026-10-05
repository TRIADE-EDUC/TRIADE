<?php
use Pmb\Common\Library\Navbar\Navbar;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.inc.php,v 1.44.10.2 2025/01/29 07:27:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $acces_j, $gestion_acces_active, $gestion_acces_user_notice, $class_path, $PMBuserid, $ex_query, $EAN, $isbn, $code, $rqt_bulletin;
global $where_typedoc, $typdoc_query, $nb_results, $msg, $pmb_allow_external_search, $nb_per_page_search, $limit_page, $page, $begin_result_liste;
global $link, $link_expl, $link_explnum, $end_result_liste, $link_serial, $link_analysis, $link_bulletin, $include_path;

// accès à une notice par code-barre, ISBN, ou numéro commercial ou  par CB exemplaire

//droits d'acces lecture notice
$acces_j='';
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
} 


// on commence par voir ce que la saisie utilisateur est ($ex_query)
$ex_query_original = $ex_query;
$ex_query = clean_string($ex_query);

$EAN = '';
$isbn = '';
$code = '';
$rqt_bulletin = 0;

$where_typedoc = "";
if(isEAN($ex_query)) {
	// la saisie est un EAN -> on tente de le formater en ISBN
	$EAN=$ex_query;
	$isbn = EANtoISBN($ex_query);
	// si échec, on prend l'EAN comme il vient
	if(!$isbn) 
		$code = str_replace("*","%",$ex_query);
	else {
		$code=$isbn;
		$code10=formatISBN($code,10);
	}
} else {
	if(isISBN($ex_query)) {
		// si la saisie est un ISBN
		$isbn = formatISBN($ex_query);
		// si échec, ISBN erroné on le prend sous cette forme
		if(!$isbn) 
			$code = str_replace("*","%",$ex_query);
		else {
			$code10=$isbn ;
			$code=formatISBN($code10,13);
		}
	} else {
		// ce n'est rien de tout ça, on prend la saisie telle quelle
		$code = str_replace("*","%",$ex_query);
		// filtrer par typdoc_query si selectionné
		if(!empty($typdoc_query) && !empty($typdoc_query[0])) $where_typedoc=" and typdoc in ('".implode("','", $typdoc_query)."')";
	}
}

if(empty($nb_results)) {
	
	// on compte
	if ($EAN && $isbn) {
		
		// cas des EAN purs : constitution de la requête
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code','$EAN'".($code10?",'$code10'":"").")) ";
		$myQuery = pmb_mysql_query($requete);
		
	} elseif ($isbn) {
		
		// recherche d'un isbn
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= " WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code'".($code10?",'$code10'":"").")) ";
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)==0) {
			// rien trouvé en monographie
			// cas où un exemplaire de bulletin correspond à un ISBN
			$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
			$requete.= $acces_j;
			$requete.= "left join bulletins on bulletin_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
			$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$ex_query' OR bulletin_numero like '$ex_query' OR bulletin_cb like '$ex_query' OR notices.code like '$ex_query') ";
			$requete.= "GROUP BY bulletin_id ";
			$rqt_bulletin=1;
		}
	} elseif ($code) {
		
		// recherche d'un exemplaire
		// note : le code est recherché aussi dans le champ code des notices
		// (cas des code-barres disques qui échappent à l'EAN)
		//
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR notices.code like '$code' OR exemplaires.expl_cb like '$ex_query_original' OR notices.code like '$ex_query_original') $where_typedoc ";
				
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)==0) {
		    // rien trouvé en monographie, vérifions les notices de bulletin
		    $requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
		    $requete.= $acces_j;
		    $requete.= "left join bulletins on num_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
		    $requete.= "WHERE niveau_biblio='b' AND (exemplaires.expl_cb like '$code' OR bulletin_numero like '$code' OR bulletin_cb like '$code' OR notices.code like '$code' OR exemplaires.expl_cb like '$ex_query_original' OR bulletin_numero like '$ex_query_original' OR bulletin_cb like '$ex_query_original' OR notices.code like '$ex_query_original')  $where_typedoc ";
		    $requete.= "GROUP BY bulletin_id ";
		    $myQuery = pmb_mysql_query($requete);
		}
		if(pmb_mysql_num_rows($myQuery)==0) {
		    // rien trouvé en monographie et en notice de bulletin, un pério ?
			$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
			$requete.= $acces_j;
			$requete.= "left join bulletins on bulletin_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
			$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$code' OR bulletin_numero like '$code' OR bulletin_cb like '$code' OR notices.code like '$code' OR exemplaires.expl_cb like '$ex_query_original' OR bulletin_numero like '$ex_query_original' OR bulletin_cb like '$ex_query_original' OR notices.code like '$ex_query_original')  $where_typedoc ";
			$requete.= "GROUP BY bulletin_id ";
			$rqt_bulletin=1;
		}
		
	} else {
		
		error_message($msg[235], $msg[307]." $ex_query".($pmb_allow_external_search?"<br /><a href='./catalog.php?categ=search&mode=7&external_type=simple&from_mode=0&code=".rawurlencode($ex_query)."' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>":""), 1, "./catalog.php?categ=search&mode=0");
		die();
		
	}
	
	$nb_results= pmb_mysql_num_rows($myQuery);
	$limit_page= " limit $nb_per_page_search "; 
	$page=0;

} else {
    if($page) {
        $start_page = $nb_per_page_search * ($page-1);
    } else {
        $start_page = 0;
    }
    $limit_page= " limit ".$start_page.", $nb_per_page_search ";
 
	// echo "EAN : $EAN<br /> isbn : $isbn<br />code : $code<br />";
	
	if ($EAN && $isbn) {
		// cas des EAN purs : constitution de la requête
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code','$EAN'".($code10?",'$code10'":"").")) ";
		$requete.= $limit_page;
		$myQuery = pmb_mysql_query($requete);
		
	} elseif ($isbn) {
		// recherche d'un isbn
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code'".($code10?",'$code10'":"").")) ";
		$requete.= $limit_page;
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)==0) {
			// rien trouvé en monographie
			// cas où un exemplaire de bulletin correspond à un ISBN
			$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
			$requete.= $acces_j;
			$requete.= "left join bulletins on bulletin_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
			$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$ex_query' OR bulletin_numero like '$ex_query' OR bulletin_cb like '$ex_query' OR notices.code like '$ex_query') ";
			$requete.= "GROUP BY bulletin_id ".$limit_page;
			$rqt_bulletin=1;
		}
	} elseif ($code) {
		// recherche d'un exemplaire
		// note : le code est recherché aussi dans le champ code des notices
		// (cas des code-barres disques qui échappent à l'EAN)
		//
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR notices.code like '$code') $where_typedoc ";
		$requete.= $limit_page;
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)==0) {
		    // rien trouvé en monographie, vérifions les notices de bulletin
		    $requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
		    $requete.= $acces_j;
		    $requete.= "left join bulletins on num_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
		    $requete.= "WHERE niveau_biblio='b' AND (exemplaires.expl_cb like '$code' OR bulletin_numero like '$code' OR bulletin_cb like '$code' OR notices.code like '$code')  $where_typedoc ";
		    $requete.= "GROUP BY bulletin_id ".$limit_page;
		    $myQuery = pmb_mysql_query($requete);
		}
		if(pmb_mysql_num_rows($myQuery)==0) {
			// rien trouvé en monographie et en notice de bulletin, un pério ?
			$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
			$requete.= $acces_j;
			$requete.= "left join bulletins on bulletin_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
			$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$code' OR bulletin_numero like '$code' OR bulletin_cb like '$code' OR notices.code like '$code')  $where_typedoc ";
			$requete.= "GROUP BY bulletin_id ".$limit_page;
			$rqt_bulletin=1;
		}
	} else {
		error_message($msg[235], $msg[307]." $ex_query".($pmb_allow_external_search?"<br /><a href='./catalog.php?categ=search&mode=7&external_type=simple&from_mode=0&code=".rawurlencode($ex_query)."' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>":""), 1, "./catalog.php?categ=search&mode=0");
		die();
	}
}

if ($rqt_bulletin!=1) {
	if(pmb_mysql_num_rows($myQuery)) {
		if(pmb_mysql_num_rows($myQuery) > 1  || $page) {
			// la recherche fournit plusieurs résultats !!!
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print sprintf("<div class='othersearchinfo'><b>".$msg[940]."</b>&nbsp;$ex_query =&gt; ".$msg["searcher_results"]."</div>",$nb_results);			
			print $begin_result_liste;
			$nb=0;
			while($notice = pmb_mysql_fetch_object($myQuery)) {
				if($notice->niveau_biblio != 's' && $notice->niveau_biblio != 'a') {
					// notice de monographie (les autres n'ont pas de code ni d'exemplaire !!! ;-)
					$link = notice::get_pattern_link();
					$link_expl = exemplaire::get_pattern_link(); 
					$link_explnum = explnum::get_pattern_link();   
					$display = new mono_display($notice, 6, $link, 1, $link_expl, '', $link_explnum,1, 0,1,1);
					print pmb_bidi($display->result);
				}
				if (++$nb >= $nb_per_page_search) break;
			}
			print $end_result_liste;
		} else {
			$notice = pmb_mysql_fetch_object($myQuery);
			print "<div class=\"row\"><div class=\"msg-perio\">".$msg['recherche_encours']."</div></div>";
			// un seul résultat : je balance le user direct sur la notice concernée
			print "<script type=\"text/javascript\">";
			print "document.location = \"".notice::get_permalink($notice->notice_id)."\"";
			print "</script>";
		}
	} else {
		error_message($msg[235], $msg[307]." $ex_query".($pmb_allow_external_search?"<br /><a href='./catalog.php?categ=search&mode=7&external_type=simple&from_mode=0&code=".rawurlencode($ex_query)."' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>":""),1, "./catalog.php?categ=search&mode=0");
	}
} else {
	require_once ("$class_path/serials.class.php") ;
	
	// C'est un pério !
	$res = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($res)) {
		if (pmb_mysql_num_rows($res) ==1) {
			$row = pmb_mysql_fetch_object($res);
			print "<div class=\"row\"><div class=\"msg-perio\">".$msg['recherche_encours']."</div></div>";
			print "<script type=\"text/javascript\">";
			print "document.location = \"".bulletinage::get_permalink($row->bulletin_id)."\"";
			print "</script>";
		}else{
			print $begin_result_liste;
			while(($n=pmb_mysql_fetch_object($res))) {
				$link_serial = serial::get_pattern_link();
				$link_analysis = "";
				$link_bulletin = "";
				require_once ("$include_path/bull_info.inc.php") ;
				
				$n->isbd = show_bulletinage_info($n->bulletin_id);
				print pmb_bidi($n->isbd) ;
			}	
			print $end_result_liste;
		}
	} else {
		error_message($msg[235], $msg[307]." $ex_query".($pmb_allow_external_search?"<br /><a href='./catalog.php?categ=search&mode=7&external_type=simple&from_mode=0&code=".rawurlencode($ex_query)."' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>":""), 1, "./catalog.php?categ=search&mode=0");   
	}
}

//Gestion de la pagination
if (!empty($nb_results)) {
	
	print "
	<form name='search_form' action='./catalog.php?categ=search&mode=0' method='post' style='display:none'>
		<input type='hidden' name='page' value='$page'/>
		<input type='hidden' name='nb_results' value='$nb_results'/>
		<input type='hidden' name='ex_query' value='$ex_query'/>
		<input type='hidden' name='typdoc_query' value=''/>
		<input type='hidden' name='statut_query' value=''/>
	</form>";
	
	if (!$page) {
	    $current_page = 1 ;
	} else {
	    $current_page = intval($page);
	}
	$navbar = new Navbar($current_page, $nb_results, $nb_per_page_search);
	$navbar->setHiddenFormName('search_form');
	print "<div id='results_pager' class='center'>".$navbar->render()."</div>";
    	
}  
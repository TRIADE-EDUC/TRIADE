<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.inc.php,v 1.17 2019/04/09 09:56:16 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
// accès à une notice par code-barre, ISBN, ou numéro commercial ou par CB exemplaire

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

if ($EAN && $isbn) {

	// cas des EAN purs : constitution de la requête
	$requete = "SELECT distinct notices.* FROM notices ";
	$requete.= $acces_j;
	$requete.= "join exemplaires on notices.notice_id=exemplaires.expl_notice ";
	$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code','$EAN'".($code10?",'$code10'":"").")) limit 10";
	$myQuery = pmb_mysql_query($requete);

} elseif ($isbn) {

	// recherche d'un isbn
	$requete = "SELECT distinct notices.* FROM notices ";
	$requete.= $acces_j;
	$requete.= "join exemplaires on notices.notice_id=exemplaires.expl_notice ";
	$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code'".($code10?",'$code10'":"").")) limit 10";
	$myQuery = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($myQuery)==0) {
		// rien trouvé en monographie
		// cas où un exemplaire de bulletin correspond à un ISBN
		$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
		$requete.= $acces_j;
		$requete.= "join bulletins on bulletin_notice=notice_id join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
		$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$ex_query' OR bulletin_numero like '$ex_query' OR bulletin_cb like '$ex_query' OR notices.code like '$ex_query') ";
		$requete.= "GROUP BY bulletin_id limit 10";
		$rqt_bulletin=1;
	}

} elseif ($code) {

	// recherche d'un exemplaire
	// note : le code est recherché aussi dans le champ code des notices
	// (cas des code-barres disques qui échappent à l'EAN)
	$requete = "SELECT distinct notices.* FROM notices ";
	$requete.= $acces_j;
	$requete.= "join exemplaires on notices.notice_id=exemplaires.expl_notice ";
	$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR notices.code like '$code') limit 10";
	$myQuery = pmb_mysql_query($requete);
	if(pmb_mysql_num_rows($myQuery)==0) {
		// rien trouvé en monographie
		$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
		$requete.= $acces_j;
		$requete.= "join bulletins on bulletin_notice=notice_id join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
		$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$code' OR bulletin_numero like '$code' OR bulletin_cb like '$code' OR notices.code like '$code') ";
		$requete.= "GROUP BY bulletin_id limit 10";
		$rqt_bulletin=1;
	}

} else {

	error_message($msg[235], $msg[307]." $ex_query", 1, "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=0");
	die();

}
if ($rqt_bulletin!=1) {
	if(pmb_mysql_num_rows($myQuery)) {
		if(pmb_mysql_num_rows($myQuery) > 1) {
			// la recherche fournit plusieurs résultats !!!
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print $begin_result_liste;
			while($notice = pmb_mysql_fetch_object($myQuery)) {
				if($notice->niveau_biblio != 's' && $notice->niveau_biblio != 'a') {
					// notice de monographie (les autres n'ont pas de code ni d'exemplaire !!! ;-)
					$link = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_notice=!!id!!";
					$display = new mono_display($notice, 6, $link, 1, '', '', '', 1);
					print $display->result;
				}
			}
			print $end_result_liste;
		} else {
			$notice = pmb_mysql_fetch_object($myQuery);
			// un seul résultat
			print "<script type=\"text/javascript\">";
			print "document.location = \"./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_notice=".$notice->notice_id."\"";
			print "</script>";
		}
	} else {
		error_message($msg[235], $msg[307]." $ex_query", 1, "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=0");
	}
} else {
	// C'est un périodique
	$res = @pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($res)) {
		if(pmb_mysql_num_rows($res) > 1) {
			print $begin_result_liste;
			while (($n=pmb_mysql_fetch_object($res))) {
				$link_bulletin = "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=".$n->bulletin_id;
				require_once ("$class_path/serials.class.php") ;
				require_once ("$include_path/bull_info.inc.php") ;
				$n->isbd = show_bulletinage_info_resa($n->bulletin_id, $link_bulletin);
				print $n->isbd ;
			}
			print $end_result_liste;
		} else {
			$n=pmb_mysql_fetch_object($res);
			// un seul résultat
			print "<script type=\"text/javascript\">";
			print "document.location = \"./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&id_bulletin=".$n->bulletin_id."\"";
			print "</script>";
		}
	} else {
		error_message($msg[235], $msg[307]." $ex_query", 1, "./circ.php?categ=resa&id_empr=$id_empr&groupID=$groupID&mode=0");
	}
}

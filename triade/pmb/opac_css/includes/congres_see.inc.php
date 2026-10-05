<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: congres_see.inc.php,v 1.39 2023/08/02 06:21:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $class_path, $msg;
global $id;

// affichage du detail pour un auteur
require_once($class_path."/authorities/page/authority_page_congres.class.php");
require_once($base_path.'/includes/templates/author.tpl.php');
require_once($base_path.'/classes/author.class.php');
require_once("$class_path/aut_link.class.php");

print "<div id='aut_details'>\n";

$id = intval($id);
if($id) {
	$rqt_auteurs = "select author_id as aut from authors where author_see='$id' and author_id!=0 ";
	$rqt_auteurs .= "union select author_see as aut from authors where author_id='$id' and author_see!=0 " ;
	$res_auteurs = pmb_mysql_query($rqt_auteurs);
	$clause_auteurs = " in ('$id' ";
	while(($id_aut=pmb_mysql_fetch_object($res_auteurs))) {
		$clause_auteurs .= ", '".$id_aut->aut."' ";
		$rqt_auteursuite = "select author_id as aut from authors where author_see='$id_aut->aut' and author_id!=0 ";
		$res_auteursuite = pmb_mysql_query($rqt_auteursuite);
		while(($id_autsuite=pmb_mysql_fetch_object($res_auteursuite))) $clause_auteurs .= ", '".$id_autsuite->aut."' ";
	}
	$clause_auteurs .= " ) " ;

	// affichage des informations sur l'auteur
	$ourAuteur = new auteur($id);
	if($ourAuteur->type == 72) {
		// Congrès
		print common::format_title($msg["congres_see_title"]." ".$renvoi);
	} else if($ourAuteur->type == 71) {
		// Collectivités
		print common::format_title($msg["collectivite_see_title"]." ".$renvoi);
	} else {
		print common::format_title($msg["author_see_title"]." ".$renvoi);
	}
	print "<div id='aut_details_container'>\n";
	print "	<div id='aut_see' class='aut_see'>\n
			<img src='".get_url_icon("home.gif")."' alt='' style='border:0px'></a> &gt;\n";

	print pmb_bidi($ourAuteur->print_congres_titre());

	$ourAuteur->get_similar_name($ourAuteur->type);
	print $ourAuteur->print_similar_name();

	print $ourAuteur->author_comment;

	// récupération des formes rejetées pour affichage
	$requete = "select distinct author_id as aut from authors where author_id $clause_auteurs and author_id!=$id " ;
	$res = pmb_mysql_query($requete);
	while (($obj=pmb_mysql_fetch_object($res))) {
		$objRenvoi = new auteur($obj->aut);
		if (pmb_strlen($renvoi)) {
		    $renvoi .= ', ('.$objRenvoi->get_isbd().")";
		} else {
		    $renvoi = $objRenvoi->isbd_entry;
		}
	}

	if (pmb_strlen($renvoi)) print pmb_bidi("<span class='number_results'>$renvoi</span><br />\n");

	$aut_link= new aut_link(AUT_TABLE_AUTHORS,$id);
	print pmb_bidi($aut_link->get_display());

	print "</div><!-- fermeture #aut_see -->\n";
	
	$authority_page = new authority_page_congres($id);
	
	//LISTE DE NOTICES ASSOCIEES
	//composition du contexte, puis envoi des données au template Django
	$context = array();
	//$authority = new authority(0, $id, AUT_TABLE_AUTHORS);
	$authority = authorities_collection::get_authority('authority', 0, ['num_object' => $id, 'type_object' => AUT_TABLE_AUTHORS]);
	$authority->set_recordslist($authority_page->get_recordslist());
	print $authority->render($context);

} else {
	print common::format_title($msg["author_see_title"]." ".$renvoi);
	print "<div id='aut_details_container'>\n";
}

print "	</div><!-- fermeture du div aut_details_container -->\n";
print
"	</div><!-- fermeture du div aut_details -->\n";

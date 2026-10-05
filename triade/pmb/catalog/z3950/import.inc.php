<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: import.inc.php,v 1.49 2023/10/11 12:18:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $action, $source, $msg, $pmb_indexation_lang;
global $id_notice, $znotices_id, $last_query_id, $f_ex_cb;

if(!isset($f_ex_cb)) $f_ex_cb = '';

// définition du minimum nécéssaire 
require_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once("$class_path/iso2709.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/editor.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/origine_notice.class.php");
require_once("$class_path/audit.class.php");
require_once("notice.inc.php");
require_once("$class_path/expl.class.php");
require_once("$include_path/templates/expl.tpl.php");
require_once("$class_path/z3950_notice.class.php");
require_once("$class_path/serials.class.php");
require_once("$class_path/notice.class.php");

$id_notice = intval($id_notice);
if (!$id_notice) {
	print "<h1>$msg[z3950_integr_catal]</h1>";
} else {
	print "<h1>$msg[notice_z3950_remplace_catal]</h1>";
}
$znotices_id = intval($znotices_id);
$last_query_id = intval($last_query_id);
$resultat=pmb_mysql_query("select znotices_id, znotices_bib_id, isbd, isbn, titre, auteur, z_marc from z_notices where znotices_id='$znotices_id' AND znotices_query_id='$last_query_id'");

$test_resultat=0;
$integration_OK="";
$integrationexpl_OK="";

while (($ligne=pmb_mysql_fetch_array($resultat))) {
	//$id_notice=$ligne["znotices_id"];	
	$znotices_id=$ligne["znotices_id"];
	
	/* récupération du format des notices retournées par la bib */
	$znotices_bib_id=$ligne["znotices_bib_id"];
	$rqt_bib_id=pmb_mysql_query("select format from z_bib where bib_id='$znotices_bib_id'");
	while (($ligne_format=pmb_mysql_fetch_array($rqt_bib_id))) {
		$format=$ligne_format["format"];
	}

	$resultat_titre=$ligne["titre"];
	$resultat_auteur=$ligne["auteur"];
	$resultat_isbd=$ligne["isbd"];
	$test_resultat++;
	$lien = $resultat_titre." / ".$resultat_auteur;
	print pmb_bidi(zshow_isbd($resultat_isbd, $lien));

	if ($action != "integrerexpl") { 
	    if (!empty($source) && $source == 'form') {
			$notice = new z3950_notice ('form');
		} else {
			// avant affichage du formulaire : détecter si notice déjà présente pour proposer MAJ
			$isbn_verif = traite_code_isbn($ligne['isbn']) ;
			$suite_rqt="";
			if (isISBN($isbn_verif)) {
				if (strlen($isbn_verif)==13)
					$suite_rqt=" or code='".formatISBN($isbn_verif,13)."' ";
				else $suite_rqt="or code='".formatISBN($isbn_verif,10)."' ";
			}
			if ($isbn_verif) {
				$requete = "SELECT notice_id FROM notices WHERE code='$isbn_verif' ".$suite_rqt;
				$myQuery = pmb_mysql_query($requete);
				$temp_nb_notice = pmb_mysql_num_rows($myQuery) ;
				if ($temp_nb_notice) {
				    $not_id = pmb_mysql_result($myQuery, 0 ,0) ;
				} else {
				    $not_id=0 ;
				}
			}
			// if ($not_id) METTRE ICI TRAITEMENT DU CHOIX DU DOUBLON echo "<script> alert('Existe déjà'); </script>" ;
			$notice = new z3950_notice ($format, $ligne['z_marc'],0 , true);
			//Si pas d'origine renseignée en 801, on reprend le nom de la source
			if (!count($notice->origine_notice)) {
				$requete = "SELECT bib_nom FROM z_bib WHERE bib_id=".$ligne['znotices_bib_id'];
				$myQuery = pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($myQuery)) {
					$myRow = pmb_mysql_fetch_object($myQuery) ;
					$notice->origine_notice['nom'] = $myRow->bib_nom;
				}
			}
		}
	}

	$integration_OK="PASFAIT";
	$integrationexpl_OK="PASFAIT";
	switch ($action) {
		case "integrer" :
			if (!$id_notice) {
				$res_integration = $notice->insert_in_database();
			} else {
				$res_integration = $notice->update_in_database($id_notice);
			}
			$new_notice=$res_integration[0];
			$num_notice=$res_integration[1];
			if (($new_notice==0) && ($num_notice==0)) $integration_OK="ECHEC"; 
			if (($new_notice==0) && ($num_notice!=0)) $integration_OK="EXISTAIT"; 
			if (($new_notice==1) && ($num_notice!=0)) $integration_OK="OK"; 
			if (($new_notice==2) && ($num_notice!=0)) $integration_OK="UPDATE_OK"; 
			if (($new_notice==1) && ($num_notice==0)) $integration_OK="NEWRATEE"; 
			break;

		case "integrerexpl" :
			if ($notice_nbr == 0) {
				$integration_OK = "ECHEC";
			} else {
				$integration_OK = "OK";
				$num_notice = $notice_nbr;
				$formlocid="f_ex_section".$f_ex_location ;
				$f_ex_section=${$formlocid};
				$res_integrationexpl = create_expl($f_ex_cb, $num_notice, $f_ex_typdoc, $f_ex_cote, $f_ex_section, $f_ex_statut, $f_ex_location, $f_ex_cstat, $f_ex_note, $f_ex_prix, $f_ex_owner, $f_ex_comment );
				$new_expl=$res_integrationexpl[0];
				$num_expl=$res_integrationexpl[1];
				if (($new_expl==0) && ($num_expl==0)) $integrationexpl_OK="ECHEC"; 
				if (($new_expl==0) && ($num_expl!=0)) $integrationexpl_OK="EXISTAIT"; 
				if (($new_expl==1) && ($num_expl!=0)) $integrationexpl_OK="OK"; 
				if (($new_expl==1) && ($num_expl==0)) $integrationexpl_OK="NEWRATEE"; 
			}
			break;
		}
		/* ----------------------------------- */

	$msg['z3950_integr_expl_ok']       = str_replace ("!!f_ex_cb!!", $f_ex_cb, $msg['z3950_integr_expl_ok']      );
	$msg['z3950_integr_expl_existait'] = str_replace ("!!f_ex_cb!!", $f_ex_cb, $msg['z3950_integr_expl_existait']);
	$msg['z3950_integr_expl_newrate']  = str_replace ("!!f_ex_cb!!", $f_ex_cb, $msg['z3950_integr_expl_newrate'] );
	$msg['z3950_integr_expl_echec']    = str_replace ("!!f_ex_cb!!", $f_ex_cb, $msg['z3950_integr_expl_echec']   );

	switch ($integrationexpl_OK) {
		case "OK" :
			print "<hr /><strong>$msg[z3950_integr_expl_ok]</strong>&nbsp;<a id='liensuite' href=\"javascript:top.document.location='./catalog.php?categ=edit_expl&id=$num_notice&cb=$f_ex_cb'\">$msg[z3950_integr_expl_levoir]</a>";
			print "<script type='text/javascript'>document.getElementById('liensuite').focus();</script>" ;
			break;
		case "EXISTAIT" :
			print "<hr /><strong>$msg[z3950_integr_expl_existait]</strong>&nbsp;<a id='liensuite' href=\"javascript:top.document.location='./catalog.php?categ=edit_expl&id=$num_notice&cb=$f_ex_cb'\">$msg[z3950_integr_expl_levoir]</a>";
			print "<script type='text/javascript'>document.getElementById('liensuite').focus();</script>" ;
			break;
		case "NEWRATE" :
			print "<hr /><strong>$msg[z3950_integr_expl_newrate]</strong>";
			break;
		case "ECHEC" :
			print "<hr /><strong>$msg[z3950_integr_expl_echec]</strong>";
			break;
	}

	switch($notice->bibliographic_level.$notice->hierarchic_level){
		case "a2" :
			$url_view = analysis::get_permalink($num_notice, $notice->bull_id);
			break;
		case "s1" :
			$url_view = serial::get_permalink($num_notice);
			break;
		default :
			$url_view = notice::get_permalink($num_notice);
			break;
	}
	switch ($integration_OK) {
		case "OK" :
			print "<hr />
					<span class='msg-perio'>".$msg['z3950_integr_not_ok']."</span>
					&nbsp;<a id='liensuite' href=\"javascript:top.document.location='$url_view'\">$msg[z3950_integr_not_lavoir]</a>";
			print "<script type='text/javascript'>document.getElementById('liensuite').focus();</script>" ;
			break;
		case "UPDATE_OK" :
			print "<hr />
					<span class='msg-perio'>".$msg['z3950_update_not_ok']."</span>
					&nbsp;<a id='liensuite' href=\"javascript:top.document.location='".$url_view."'\">$msg[z3950_integr_not_lavoir]</a>";
			print "<script type='text/javascript'>document.getElementById('liensuite').focus();</script>" ;
			break;
		case "EXISTAIT" :
			if ($action=="integrer") {
				print "<hr />
					<span class='msg-perio'>".$msg['z3950_integr_not_existait']."</span>
					&nbsp;<a id='liensuite' href=\"javascript:top.document.location='".$url_view."'\">$msg[z3950_integr_not_lavoir]</a>";
				print "<script type='text/javascript'>document.getElementById('liensuite').focus();</script>" ;
			}
			break;
		case "NEWRATE" :
			if ($action=="integrer") print "<hr />
					<span class='msg-perio'>".$msg['z3950_integr_not_newrate']."</span>";
			break;
		case "ECHEC" :
			if ($action=="integrer") print "<hr />
					<span class='msg-perio'>".$msg['z3950_integr_not_echec']."</span>";
			break;
	}

	if ($integration_OK == "PASFAIT") {
	    $entity_locking = new entity_locking($id_notice, TYPE_NOTICE);
	    if(!$entity_locking->is_locked()){
	        echo $notice->get_form ("./catalog.php?categ=z3950&".
	            "znotices_id=$znotices_id&last_query_id=$last_query_id&action=integrer&source=form&".
	            "tri1=$tri1&tri2=$tri2", $id_notice, 'link');
	    }else{
	        echo $entity_locking->get_locked_form();
	    }
		
	}
	if (($integration_OK == "OK") | ($integration_OK == "EXISTAIT") | ($integration_OK == "UPDATE_OK")) {
		print "<hr />
					<span class='right'><a id='liensuite' href='./catalog.php?categ=z3950&action=display&last_query_id=".$last_query_id."&tri1=auteur&tri2=auteur'>".$msg['z3950_retour_a_resultats']."</a></span>";
		print "<script type='text/javascript'>document.getElementById('liensuite').focus();</script>" ;
					
		//$nex = new exemplaire('', 0, $num_notice);
		//$nex->zexpl_form ('./catalog.php?categ=z3950&znotices_id='.$znotices_id.'&last_query_id='.$last_query_id.'&action=integrerexpl&notice_nbr='.$num_notice.'&tri1='.$tri1.'&tri2='.$tri2);
	}
	
} /* fin while */

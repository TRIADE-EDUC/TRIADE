<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4.4.1 2025/03/13 16:40:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $charset, $sub;
global $id_empr, $cb_doc;
global $biblio_name, $biblio_adr1, $biblio_town, $biblio_phone, $biblio_email;

require_once('./circ/print_pret/func.inc.php');

$empr=print_get_info_empr($id_empr);
//global $biblio_name, $biblio_logo, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_state, $biblio_country, $biblio_phone, $biblio_email, $biblio_website ;

$xml_bibli="<text style=\"header\">".htmlspecialchars($biblio_name,ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\">".htmlspecialchars($biblio_adr1,ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\">".htmlspecialchars($biblio_town,ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\">".htmlspecialchars($biblio_phone,ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\">".htmlspecialchars($biblio_email,ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\"></text>";
$xml_bibli.="<text style=\"p1\">".htmlspecialchars($msg['fpdf_edite']." ".formatdate(date("Y-m-d",time())),ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\"></text>";
$xml_bibli.="<text style=\"t1\">Emprunteur: </text>";
$xml_bibli.="<text style=\"p1\">".htmlspecialchars($empr->nom." ". $empr->prenom,ENT_QUOTES,$charset)."</text>";
$xml_bibli.="<text style=\"p1\"></text>";

function print_expl($cb_doc) {
	global $msg, $charset;
	$expl=print_get_info_expl($cb_doc);
	$xml_expl ="<text style=\"p1\"></text>";

	$titre=substr($expl->tit,0,25);

	$xml_expl.="<text style=\"t1_s\">".htmlspecialchars($titre,ENT_QUOTES,$charset)."</text>";
	$xml_expl.="<text style=\"t1\" x=\"360\">".htmlspecialchars($expl->aff_pret_retour,ENT_QUOTES,$charset)."</text>";
	$xml_expl.="<text style=\"p1\">".htmlspecialchars($expl->expl_cb.". ".$msg['fpdf_date_pret']." ".$expl->aff_pret_date,ENT_QUOTES,$charset)."</text>";
	$xml_expl.="<text style=\"ps1\">".htmlspecialchars($expl->location_libelle." / ".$expl->section_libelle." / ".$expl->expl_cote,ENT_QUOTES,$charset)."</text>";

	return $xml_expl;
}
//En fonction de $sub, inclure les fichiers correspondants
switch($sub):
	case 'one':
		$xml_bibli.="<text style=\"t1\">".htmlspecialchars($msg["ticket_de_pret"],ENT_QUOTES,$charset)."</text>";
		$xml_bibli.=print_expl($cb_doc);
		$xml_bibli.="<text style=\"t1\"></text>";
	break;
	case 'all':


		$xml_bibli.="<text style=\"t1\">".htmlspecialchars("Liste des prêts:",ENT_QUOTES,$charset)."</text>";
		$query = "select expl_cb from pret,exemplaires  where pret_idempr=$id_empr and expl_id=pret_idexpl ";
		$result = pmb_mysql_query($query);
		while (($r= pmb_mysql_fetch_array($result))) 	$xml_bibli.=print_expl( $r['expl_cb']);
		$xml_bibli.="<text style=\"t1\"></text>";
	break;
	default:
		ajax_http_send_error('400',"commande inconnue");
	break;
endswitch;

$xml=file_get_contents ( $base_path . "/includes/printer/ticket_pret.xml");

$xml=str_replace("!!document-contents!!",$xml_bibli , $xml);
ajax_http_send_response($xml,"text/xml");

?>

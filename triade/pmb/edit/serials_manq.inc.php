<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serials_manq.inc.php,v 1.18 2021/03/25 08:52:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $msg;
global $user_query;

// inclusion du template de gestion des périodiques
require_once("$include_path/templates/serials.tpl.php");
require_once("./catalog/serials/serial_func.inc.php");

if (!$user_query) $user_query ="*" ;
$user_query = str_replace("*","%",$user_query ); 


$serial_edit_access = str_replace('!!message!!', $msg[1914], $serial_edit_access);
$serial_edit_access = str_replace('!!etat!!', 'manquant', $serial_edit_access);

print $serial_edit_access;

// comptage du nombre de résultats
$count_query = pmb_mysql_query("SELECT COUNT(notice_id) FROM notices WHERE index_sew like '".$user_query."' AND niveau_biblio='s' AND niveau_hierar='1'");
$nbr_lignes = pmb_mysql_result($count_query, 0, 0);


if($nbr_lignes) {
	$myQuery = pmb_mysql_query(" SELECT notices.notice_id, notices.tit1, notices.ed1_id, bulletins.bulletin_id, bulletins.mention_date, bulletins.date_date, bulletins.bulletin_numero, exemplaires.expl_id, publishers.ed_name, publishers.ed_pays
		FROM notices
		JOIN publishers ON notices.ed1_id = publishers.ed_id
		LEFT JOIN bulletins ON bulletins.bulletin_notice = notices.notice_id
		LEFT JOIN exemplaires ON bulletins.bulletin_id = exemplaires.expl_bulletin
		WHERE niveau_biblio = 's' AND niveau_hierar = '1'
		and ISNULL( expl_id )
		and index_sew like '".$user_query."' ORDER BY tit1, notice_id, bulletins.date_date, bulletins.bulletin_numero, bulletin_id ");

	// Variable permettant de conserver les valeurs precedentes pour calcul du nombre
	$wnotice_id=0;
	$wbulletin_id=0;
	$wtit1="";
	$wed_name="";
	$wed_pays="";
	$wmention_date="";
	$wbulletin_numero="";
	$cpt_notice = 1;

	while($serial=pmb_mysql_fetch_object($myQuery)) {
		$tr_javascript=" onmousedown=\"document.location='".serial::get_permalink($wnotice_id)."';\" ";
		if ($wnotice_id != $serial->notice_id or $wbulletin_id != $serial->bulletin_id) {
			if ($wnotice_id != 0) {
				if ($wnotice_id == $serial->notice_id) {
				# affichage du titre seulement lors de la premiere notice
					if ($cpt_notice == 1){
						$serial_list .= "<tr $tr_javascript style='cursor: pointer'>";
						$serial_list .= "<td><strong>$wtit1</strong></td>
							<td>$wed_name</td>
							<td>$wed_pays</td>";
					} else {
						$serial_list .= "<tr $tr_javascript style='cursor: pointer'>";
						$serial_list .= "<td></td>
						<td></td>
						<td></td>";
					}
					$cpt_notice=0;
				} else {
					$cpt_notice=1;
					$serial_list .= "<tr $tr_javascript style='cursor: pointer'>";
					$serial_list .= "		<td></td>
							<td></td>
							<td></td>";
				}
				$serial_list .= "<td>$wmention_date</td>
						<td>$wbulletin_numero</td>
					</tr>";
			}
			$wnotice_id=$serial->notice_id;
			$wtit1=$serial->tit1;
			$wed_name=$serial->ed_name;
			$wed_pays=$serial->ed_pays;

			$wbulletin_id=$serial->bulletin_id;
			$wmention_date=$serial->mention_date;
			$wbulletin_numero=$serial->bulletin_numero;
		}
	}

	// Affichage dernier element
			if ($cpt_notice == 1){
				$serial_list .= "<tr $tr_javascript style='cursor: pointer'>";
				$serial_list .= "<td><strong>$wtit1</strong></td>
					<td>$wed_name</td>
					<td>$wed_pays</td>";
				} else {
					$serial_list .= "<tr $tr_javascript style='cursor: pointer'>";
					$serial_list .= "<td></td>
					<td></td>
					<td></td>";
				}
				$serial_list .= "<td>$wmention_date</td>
						<td>$wbulletin_numero</td>
					</tr>";

	// affichage du résultat
	list_serial($user_query, $serial_list, '');

} else {
	// la requête n'a produit aucun résultat
	error_message($msg[46], str_replace('!!user_query!!', $user_query, $msg[1153]), 1, './edit.php?categ=serials&sub=collect');
}

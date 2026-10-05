<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning_func.inc.php,v 1.47 2022/08/01 06:44:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path;
require_once($include_path."/mail.inc.php") ;

// defines pour flag affichage info de gestion
if (!defined('NO_INFO_GESTION')) define ('NO_INFO_GESTION', 0); // 0 >> aucune info de gestion : liste simple
if (!defined('GESTION_INFO_GESTION')) define ('GESTION_INFO_GESTION', 1); // pour traitement des prévisions
if (!defined('LECTEUR_INFO_GESTION')) define ('LECTEUR_INFO_GESTION', 2); // pour affichage en fiche lecteur
if (!defined('EDIT_INFO_GESTION')) define ('EDIT_INFO_GESTION', 3); // pour affichage en édition

function planning_list($idnotice=0, $idbulletin=0, $idempr=0, $order='', $where='', $info_gestion=NO_INFO_GESTION, $url_gestion='', $ancre='') {
	global $msg,$charset;
	global $montrerquoi, $f_loc_empr, $f_loc_ret ;
	global $pmb_lecteurs_localises, $deflt2docs_location;
	global $pmb_location_resa_planning,$deflt_docs_location, $deflt_resas_location;
	global $tdoc;

	if($info_gestion == GESTION_INFO_GESTION) {
	    $list_resa_planning_circ_ui = new list_resa_planning_circ_ui();
	    return $list_resa_planning_circ_ui->get_display_list();
	}

	$q_loc = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
	$r_loc = pmb_mysql_query($q_loc);

	//Tableau + selecteur de localisations emprunteurs / nb de previsions
	$tab_loc_empr = array();
	$sel_loc_empr = '';
	if(!isset($f_loc_empr)) {
		$f_loc_empr=$deflt2docs_location;
	}
	if($pmb_lecteurs_localises) {
		$sel_loc_empr = '<select name="f_loc_empr" onchange="document.check_resa_planning.submit();">';
		$sel_loc_empr.='<option value="0"'.((!$f_loc_empr)?' selected="selected"':'').'>'.$msg['all_location'].'</option>';
		if(pmb_mysql_num_rows($r_loc)) {
			pmb_mysql_data_seek($r_loc,0);
			while($o=pmb_mysql_fetch_object($r_loc)) {
				$tab_loc_empr[$o->idlocation] = htmlentities($o->location_libelle,ENT_QUOTES,$charset);
				$sel_loc_empr.= '<option value="'.$o->idlocation.'"'.(($f_loc_empr==$o->idlocation)?' selected="selected"':'').'>'.$tab_loc_empr[$o->idlocation].'</option>';
			}
		}
		$sel_loc_empr.= '</select>';
	}

	//Tableau + selecteur de localisation de retrait / nb de previsions
	$tab_loc_ret = array();
	$sel_loc_ret = '';
	if(!isset($f_loc_ret)) {
		if($deflt_resas_location) {
			$f_loc_ret=$deflt_resas_location;
		} else {
			$f_loc_ret=$deflt_docs_location;
		}
	}
	if($pmb_location_resa_planning) {
		$sel_loc_ret = '<select name="f_loc_ret" onchange="document.check_resa_planning.submit();">';
		$sel_loc_ret.='<option value="0"'.((!$f_loc_ret)?' selected="selected"':'').'>'.$msg['all_location'].'</option>';
		if(pmb_mysql_num_rows($r_loc)) {
			pmb_mysql_data_seek($r_loc,0);
			while($o=pmb_mysql_fetch_object($r_loc)) {
				$tab_loc_ret[$o->idlocation] = htmlentities($o->location_libelle,ENT_QUOTES,$charset);
				$sel_loc_ret.= '<option value="'.$o->idlocation.'"'.(($f_loc_ret==$o->idlocation)?' selected="selected"':'').'>'.$tab_loc_ret[$o->idlocation].'</option>';
			}
		}
		$sel_loc_ret.= '</select>';
	}

	$aff_final = "<script type='text/javascript'>
		var ajax_func_to_call=new http_request();
		var f_caller='';
		var param1='';
		var param2='';
		var id;
		function func_callback(p_caller,p_id,p_date,p_param1,p_param2) {
			f_caller = p_caller;
			param1 = p_param1;
			param2 = p_param2;
			id = p_id;
			var url_func = './ajax.php?module=circ&categ=resa_planning&sub=update_resa_planning&id='+p_id+'&date='+p_date+'&param1='+p_param1;
			ajax_func_to_call.request(url_func,0,'',1,func_callback_ret,0,0);
		}

		function func_callback_ret() {
			if (param1 == '1') document.forms[f_caller].elements['resa_date_debut['+id+']'].value = ajax_func_to_call.get_text();
			if (param1 == '2') document.forms[f_caller].elements['resa_date_debut['+id+']'].value = ajax_func_to_call.get_text();
			document.forms[f_caller].elements[param2].value = ajax_func_to_call.get_text();
		}
	</script>";

	$clause = '';
	switch ($info_gestion) {

		case NO_INFO_GESTION:
		default:
			$clause .= " AND resa_planning.resa_remaining_qty!=0 ";
			break;
	}

	if (!$order) {
		$order="empr_nom, empr_prenom, tit, resa_idnotice, resa_date " ;
	}
	$nb_prev=0;
	$q = "select resa_planning.id_resa, resa_planning.resa_idnotice, resa_planning.resa_idbulletin, resa_planning.resa_date, resa_planning.resa_date_debut, resa_planning.resa_date_fin, resa_planning.resa_validee, resa_planning.resa_confirmee, resa_planning.resa_idempr, resa_planning.resa_qty, resa_planning.resa_remaining_qty, resa_planning.resa_loc_retrait, ";
	$q.= "trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')), if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')), ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
	$q.= "concat(empr_nom,', ',empr_prenom) as empr_nom_prenom, id_empr, empr_cb, empr_location, ";
	$q.= "if(resa_planning.resa_date_fin>=sysdate() or resa_planning.resa_date_fin='0000-00-00',0,1) as perimee, ";
	$q.= "date_format(resa_planning.resa_date_debut, '".$msg['format_date']."') as aff_resa_date_debut, ";
	$q.= "if(resa_planning.resa_date_fin='0000-00-00', '', date_format(resa_planning.resa_date_fin, '".$msg['format_date']."')) as aff_resa_date_fin, ";
	$q.= "date_format(resa_planning.resa_date, '".$msg['format_date']."') as aff_resa_date, " ;
	$q.= "ifnull(notices_m.typdoc,notices_s.typdoc) as typdoc ";
	$q.= "FROM resa_planning ";
	$q.= "LEFT JOIN notices as notices_m on resa_idnotice = notices_m.notice_id ";
	$q.= "LEFT JOIN series as series_m on notices_m.tparent_id = series_m.serie_id ";
	$q.= "LEFT JOIN bulletins on resa_idbulletin = bulletins.bulletin_id ";
	$q.= "LEFT JOIN notices as notices_s on bulletin_notice = notices_s.notice_id ";
	if ($montrerquoi!='toresa') {
		$q.= "LEFT JOIN series as series_s on notices_s.tparent_id = series_s.serie_id, ";
	} else {
		$q.= "LEFT JOIN series as series_s on notices_s.tparent_id = series_s.serie_id ";
		$q.= "JOIN resa on resa.resa_planning_id_resa = resa_planning.id_resa, ";
	}
	$q.= "empr ";
	$q.= "WHERE resa_planning.resa_idempr = id_empr ";
	if ($clause) $q.= $clause;

	if ($idnotice) $q.= "and notices_m.notice_id = '".$idnotice."' ";
	if ($idbulletin) $q.= "and bulletin_id = '".$idbulletin."' ";
	if ($idempr) $q.= "and id_empr = '".$idempr."' ";
	$q.= "order by ".$order;

	$r = pmb_mysql_query($q) or die("Erreur SQL !=$q");
	$nb_prev = pmb_mysql_num_rows($r);

	if (!$nb_prev) {
		switch ($info_gestion) {
			case NO_INFO_GESTION:
			default:
				return '';
		}
		return $aff_final ;
	}

	//Entete Tableau
	$aff_final .= "	<script type='text/javascript' src='./javascript/sorttable.js'></script>
				<table width='100%' class='sortable'>
					<tr>";

	$aff_final .= '<th>'.htmlentities($msg['empr_nom_prenom'], ENT_QUOTES, $charset).'</th>
			'.($pmb_lecteurs_localises ? '<th>'.htmlentities($msg['resa_planning_loc_empr'], ENT_QUOTES, $charset).'</th>' :'');

	$aff_final .= '<th>'.htmlentities($msg['374'], ENT_QUOTES, $charset).'</th>
		<th>'.htmlentities($msg['resa_planning_date_debut'], ENT_QUOTES, $charset).'</th>
		<th>'.htmlentities($msg['resa_planning_date_fin'], ENT_QUOTES, $charset).'</th>';

	$aff_final .= '</tr>';
	$odd_even=0;

	//Contenu tableau
	while ($data = pmb_mysql_fetch_object($r)) {

		if ($odd_even==0) {
			$aff_final .= "<tr class='odd' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='odd'\">";
			$odd_even=1;
		} else if ($odd_even==1) {
			$aff_final .= "<tr class='even' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='even'\">";
			$odd_even=0;
		}

		//Nom lecteur
		if (SESSrights & CIRCULATION_AUTH) {
		    $aff_final .= "<td><a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($data->empr_cb)."\">".htmlentities($data->empr_nom_prenom,  ENT_QUOTES, $charset)."</a></td>";
		} else {
		    $aff_final .= '<td>'.htmlentities($data->empr_nom_prenom, ENT_QUOTES, $charset).'</td>';
		}
		//Localisation lecteur
		if ($pmb_lecteurs_localises) {
		    $aff_final.= '<td>'.$tab_loc_empr[$data->empr_location].'</td>';
		}

		//Date prevision
		$aff_final.= '<td style="text-align:center;">'.$data->aff_resa_date.'</td>';
		$aff_final.= "<td style='text-align:center;'>".$data->aff_resa_date_debut.'</td>';
		$aff_final.= "<td style='text-align:center;'>".$data->aff_resa_date_fin." </td>";
		$aff_final.= "</tr>";
	}
	$aff_final.= "</table>";
	$aff_final.= "<div class='row'></div>";
	return $aff_final ;
}

function resa_planning_loc_retrait($id_resa) {
	global $transferts_choix_lieu_opac, $transferts_site_fixe;

	$res_trans = 0;

	switch ($transferts_choix_lieu_opac) {
		case "2":
			//retrait de la resa sur lieu fixé
			$res_trans = $transferts_site_fixe;
		break;
		case "3":
			//retrait de la resa sur lieu exemplaire
			//==>on fait rien !
		break;
		case "1":
		default:
			//retrait de la resa sur lieu lecteur
			//on recupere la localisation de l'emprunteur
			$rqt = "SELECT empr_location FROM resa_planning INNER JOIN empr ON resa_idempr = id_empr WHERE id_resa='".$id_resa."'";
			$res = pmb_mysql_query($rqt);
			$res_trans = pmb_mysql_result($res,0) ;
		break;

	}
	return $res_trans;
}

//Generation entete réservation avec verification numero lecteur
function aff_entete($id_empr,&$layout_begin='',&$empr_cb=0) {
	global $charset;
	$exists=false;
	$id_empr = intval($id_empr);
	if($id_empr) {
		// récupération nom emprunteur
		$q = "SELECT empr_nom, empr_prenom, empr_cb FROM empr WHERE id_empr=$id_empr LIMIT 1";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
 			$o = pmb_mysql_fetch_object($r);
 			$name = $o->empr_prenom;
 			if ($name) {
 			    $name .= ' '.$o->empr_nom;
 			} else {
 			    $name = $o->empr_nom;
 			}
 			$layout_begin = str_replace('!!nom_lecteur!!', htmlentities($name,ENT_QUOTES,$charset), $layout_begin);
 			$layout_begin = str_replace('!!cb_lecteur!!', htmlentities($o->empr_cb,ENT_QUOTES,$charset), $layout_begin);
 			$empr_cb=$o->empr_cb;
			$exists=true;
		}
	}
	return $exists;
}


function check_record($id_notice,$id_bulletin) {
	$id_notice = intval($id_notice);
	$id_bulletin = intval($id_bulletin);
	$exists=false;
	if($id_notice || $id_bulletin) {
		$q = "SELECT expl_id FROM exemplaires where expl_notice=$id_notice and expl_bulletin=$id_bulletin LIMIT 1";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			$exists=true;
		}
	}
	return $exists;
}


//Affichage des réservations planifiées sur le document courant par le lecteur courant
function doc_planning_list($id_empr, $id_notice, $id_bulletin) {
	global $msg;

	$query = "SELECT id_resa, resa_idempr, resa_idnotice, resa_date, resa_date_debut, resa_date_fin, resa_validee, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee ";
	$query.= "FROM resa_planning ";
	$query.= "WHERE resa_idempr='".$id_empr."' and resa_idnotice='".$id_notice."' ";
	$result = pmb_mysql_query($query);

	$message_resa = '';
	if (pmb_mysql_num_rows($result)) {
		$message_resa .= '<br /><b>'.$msg['resa_planning_enc'].'</b>';
		while ($resa = pmb_mysql_fetch_array($result)) {
			$resa_date_debut = $resa['resa_date_debut'];
			$resa_date_fin = $resa['resa_date_fin'];
			$message_resa.= "<blockquote><b>".$msg['resa_planning_date_debut']."</b> ".formatdate($resa_date_debut)."&nbsp;<b>".$msg['resa_planning_date_fin']."</b> ".formatdate($resa_date_fin)."&nbsp;" ;
			if (!$resa['perimee']) {
				if ($resa['resa_validee']) {
					$message_resa.= " ".$msg['resa_validee'] ;
				} else {
					$message_resa.= " ".$msg['resa_attente_validation']." " ;
				}
			} else {
				$message_resa.= " ".$msg['resa_overtime']." " ;
			}
			$message_resa.= "</blockquote>" ;
		}
	}
	return $message_resa;

}

function alert_empr_resa_planning($id_resa=0, $id_empr_concerne=0) {
	global $msg;
	global $pdflettreresa_priorite_email_manuel;

	if ($pdflettreresa_priorite_email_manuel==3) return ;

	if(!count($id_resa)) return;
	$tmp_id_resa =implode(",",$id_resa);

	$query = "select distinct ";
	$query .= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
	$query .= "date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, ";
	$query .= "date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, ";
	$query .= "id_empr, empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_sms, id_resa, ";
	$query .= "trim(concat(ifnull(notices_m.niveau_biblio,''), ifnull(notices_s.niveau_biblio,''))) as niveau_biblio, ";
	$query .= "trim(concat(ifnull(notices_m.notice_id,''), ifnull(notices_s.notice_id,''))) as id_notice ";
	$query .= "from (((resa_planning LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), empr ";
	$query .= "where id_resa in (".$tmp_id_resa.") and resa_validee=1 and resa_idempr=id_empr";
	if ($id_empr_concerne) $query .= "and id_empr=$id_empr_concerne ";

	$result = pmb_mysql_query($query);
	if (pmb_mysql_num_rows($result)) {
		while ($o=pmb_mysql_fetch_object($result)) {
			if (($pdflettreresa_priorite_email_manuel==1 || $pdflettreresa_priorite_email_manuel==2) && $o->empr_mail) {
				$mail_reader_resa_planning = new mail_reader_resa_planning();
				$mail_reader_resa_planning->set_mail_to_id($o->id_empr);
				$mail_reader_resa_planning->set_empr($o);
				$res_envoi = $mail_reader_resa_planning->send_mail();
				if (!$res_envoi || $pdflettreresa_priorite_email_manuel==2) {
					print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=lettre_resa_planning&id_resa=$tmp_id_resa', 'lettre_confirm_resa".$tmp_id_resa."', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');</script>";
				}
			} elseif ($pdflettreresa_priorite_email_manuel!=3) {
				print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=lettre_resa_planning&id_resa=$tmp_id_resa', 'lettre_confirm_resa".$tmp_id_resa."', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');</script>";
			}
			$rqt_maj = "update resa_planning set resa_confirmee=1 where id_resa in (".$tmp_id_resa.") and resa_validee=1 " ;
			pmb_mysql_query($rqt_maj);
		}
	}
}
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning_func.inc.php,v 1.39 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path."/mail.inc.php") ;
require_once($class_path."/mail/reader/resa/mail_reader_resa_planning.class.php");

// defines pour flag affichage info de gestion
if (!defined('NO_INFO_GESTION')) define ('NO_INFO_GESTION', 0); // 0 >> aucune info de gestion : liste simple
if (!defined('GESTION_INFO_GESTION')) define ('GESTION_INFO_GESTION', 1); // pour traitement des prévisions
if (!defined('LECTEUR_INFO_GESTION')) define ('LECTEUR_INFO_GESTION', 2); // pour affichage en fiche lecteur
if (!defined('EDIT_INFO_GESTION')) define ('EDIT_INFO_GESTION', 3); // pour affichage en édition

function planning_list($idnotice=0, $idbulletin=0, $idempr=0, $order='', $where='', $info_gestion=NO_INFO_GESTION, $url_gestion='', $ancre='') {

	global $dbh,$msg,$charset;
	global $montrerquoi, $f_loc_empr, $f_loc_ret ;
	global $current_module ;
	global $pdflettreresa_priorite_email_manuel;
	global $pmb_lecteurs_localises, $deflt2docs_location;
	global $pmb_location_resa_planning, $pmb_location_reservation,$deflt_docs_location, $deflt_resas_location;
	global $tdoc;


	$q_loc = 'select idlocation, location_libelle FROM docs_location order by location_libelle';
	$r_loc = pmb_mysql_query($q_loc,$dbh);

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

	switch ($info_gestion) {
		case GESTION_INFO_GESTION:
			$url_gestion = "./circ.php?categ=resa_planning";
			break;
		case EDIT_INFO_GESTION:
			$url_gestion = "./edit.php?categ=notices&sub=resa_planning";
			break;
	}

	$clause = '';
	switch ($info_gestion) {

		case GESTION_INFO_GESTION :
		case EDIT_INFO_GESTION:

			if (!$montrerquoi) {
				$montrerquoi='all';
			}

			$aff_final .= "<form class='form-".$current_module."' name='check_resa_planning' action='".$url_gestion."' method='post' ><div class='left' >" ;
			$aff_final .= "<input type='hidden' name='resa_action' value='' />";

			//Selecteur previsions validees/confirmees
			$aff_final .= "<span class='usercheckbox'><input type='radio' name='montrerquoi' value='all' id='all' onclick='this.form.submit();' ";
			if ($montrerquoi=='all') {
				$aff_final .= "checked='checked'" ;
				$clause = "and resa_planning.resa_remaining_qty!=0 ";
			}
			$aff_final .= " /><label for='all'>".htmlentities($msg['resa_planning_show_all'], ENT_QUOTES, $charset)."</label></span>&nbsp;";

			$aff_final .= "<span class='usercheckbox'><input type='radio' name='montrerquoi' value='validees' id='validees' onclick='this.form.submit();' ";
			if ($montrerquoi=='validees') {
				$aff_final .= "checked='checked'" ;
				$clause = "and resa_planning.resa_validee='1' and resa_planning.resa_remaining_qty!=0 ";
			}
			$aff_final .= " /><label for='validees'>".htmlentities($msg['resa_planning_show_validees'], ENT_QUOTES, $charset)."</label></span>&nbsp;";

			$aff_final .= "<span class='usercheckbox'><input type='radio' name='montrerquoi' value='invalidees' id='invalidees' onclick='this.form.submit();' ";
			if ($montrerquoi=='invalidees') {
				$aff_final .= "checked='checked'" ;
				$clause = "and resa_planning.resa_validee='0' and resa_planning.resa_remaining_qty!=0 ";
			}
			$aff_final.= " /><label for='invalidees'>".htmlentities($msg['resa_planning_show_invalidees'], ENT_QUOTES, $charset)."</label></span>&nbsp;";

			$aff_final.= "<span class='usercheckbox'><input type='radio' name='montrerquoi' value='valid_noconf' id='valid_noconf' onclick='this.form.submit();' ";
			if ($montrerquoi=='valid_noconf') {
				$aff_final .= "checked='checked'" ;
				$clause = "and resa_planning.resa_validee='1' and resa_planning.resa_confirmee='0' and resa_planning.resa_remaining_qty!=0 ";
			}
			$aff_final .= " /><label for='valid_noconf'>".htmlentities($msg['resa_planning_show_non_confirmees'], ENT_QUOTES, $charset)."</label></span>&nbsp;";

			$aff_final.= "<span class='usercheckbox'><input type='radio' name='montrerquoi' value='toresa' id='toresa' onclick='this.form.submit();' ";
			if ($montrerquoi=='toresa') {
				$aff_final .= "checked='checked'" ;
				$clause = "and resa_planning.resa_remaining_qty=0 ";
			}
			$aff_final.= " /><label for='toresa'>".htmlentities($msg['resa_planning_show_toresa'], ENT_QUOTES, $charset)."</label></span></div>";


			//Selecteur de localisation des emprunteurs
			if($pmb_lecteurs_localises) {
				$aff_final .= '<div class="row">&nbsp;</div><div class="row"><div class="colonne10">'.$msg['resa_planning_loc_empr'].'</div>';
				$aff_final .= '<div class="colonne_suite">'.$sel_loc_empr.'</div><div class="row">&nbsp;</div>' ;
				if ($f_loc_empr) {
					$clause .= " AND empr_location='".$f_loc_empr."' ";
				}

			}

			//Selecteur de localisation de retrait
			if($pmb_location_resa_planning) {
				$aff_final .= '<div class="row"><div class="colonne10">'.$msg['resa_planning_loc_retrait'].'</div>';
				$aff_final .= '<div class="colonne_suite">'.$sel_loc_ret.'</div></div><div class="row">&nbsp;</div>' ;
				if ($f_loc_ret) {
					$clause .= " AND resa_planning.resa_loc_retrait='".$f_loc_ret."' ";
				}
			}

			break;


		case LECTEUR_INFO_GESTION:
			$aff_final .= "<form class='form-".$current_module."' name='check_resa_planning' action='' method='post' >" ;
			$clause .= " AND resa_planning.resa_remaining_qty!=0 ";
			break;


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

	$r = pmb_mysql_query($q,$dbh) or die("Erreur SQL !=$q");
	$nb_prev = pmb_mysql_num_rows($r);

	if (!$nb_prev) {
		switch ($info_gestion) {
			case GESTION_INFO_GESTION :
			case EDIT_INFO_GESTION :
				$aff_final .= "</form>" ;
				break;
			case LECTEUR_INFO_GESTION :
				return '';
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

	switch ($info_gestion) {
		case GESTION_INFO_GESTION:
		case EDIT_INFO_GESTION:
			$aff_final .= '<th>'.htmlentities($msg['233'], ENT_QUOTES, $charset).'</th>
				<th>'.htmlentities($msg['empr_nom_prenom'], ENT_QUOTES, $charset).'</th>
				'.($pmb_lecteurs_localises ? '<th>'.htmlentities($msg['resa_planning_loc_empr'], ENT_QUOTES, $charset).'</th>' :'');
			break;


		case LECTEUR_INFO_GESTION:
			$aff_final .= '<th>'.htmlentities($msg['233'], ENT_QUOTES, $charset).'</th>';
			break;


		case NO_INFO_GESTION:
		default:
			$aff_final .= '<th>'.htmlentities($msg['empr_nom_prenom'], ENT_QUOTES, $charset).'</th>
			'.($pmb_lecteurs_localises ? '<th>'.htmlentities($msg['resa_planning_loc_empr'], ENT_QUOTES, $charset).'</th>' :'');
			break;
	}

	$aff_final .= '<th>'.htmlentities($msg['374'], ENT_QUOTES, $charset).'</th>
		<th>'.htmlentities($msg['resa_planning_date_debut'], ENT_QUOTES, $charset).'</th>
		<th>'.htmlentities($msg['resa_planning_date_fin'], ENT_QUOTES, $charset).'</th>';

	switch ($info_gestion) {

		case GESTION_INFO_GESTION :
			$aff_final.= '<th class="sorttable_nosort">'.htmlentities($msg['resa_planning_tab_qty'], ENT_QUOTES, $charset).'</th>
 				<th class="sorttable_nosort">'.htmlentities($msg['resa_validee'], ENT_QUOTES, $charset).'</th>
				<th class="sorttable_nosort">'.htmlentities($msg['resa_confirmee'], ENT_QUOTES, $charset).'</th>';
			if ($pmb_location_resa_planning=='1') {
				$aff_final .= '<th>'.$msg['resa_planning_loc_retrait'].'</th>';
			}
			$aff_final.= '<th class="sorttable_nosort">'.htmlentities($msg['resa_selectionner'], ENT_QUOTES, $charset).'</th>' ;
			break;


		case EDIT_INFO_GESTION :
			$aff_final.= '<th class="sorttable_nosort">'.htmlentities($msg['resa_planning_tab_qty'], ENT_QUOTES, $charset).'</th>
 				<th class="sorttable_nosort">'.htmlentities($msg['resa_validee'], ENT_QUOTES, $charset).'</th>
				<th class="sorttable_nosort">'.htmlentities($msg['resa_confirmee'], ENT_QUOTES, $charset).'</th>';
			if ($pmb_location_resa_planning=='1') {
				$aff_final .= '<th>'.$msg['resa_planning_loc_retrait'].'</th>';
			}
			break;


		case LECTEUR_INFO_GESTION :
			$aff_final.= '<th class="sorttable_nosort">'.htmlentities($msg['resa_planning_tab_qty'], ENT_QUOTES, $charset).'</th>
 				<th class="sorttable_nosort">'.htmlentities($msg['resa_validee'], ENT_QUOTES, $charset).'</th>
				<th class="sorttable_nosort">'.htmlentities($msg['resa_confirmee'], ENT_QUOTES, $charset).'</th>';
			if ($pmb_location_resa_planning=='1') {
				$aff_final .= '<th>'.$msg['resa_planning_loc_retrait'].'</th>';
			}
			$aff_final.= '<th class="sorttable_nosort">' . $msg["resa_suppr_th"] . '</th>' ;
			break;

		case NO_INFO_GESTION:
		default:
			break;
	}
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

		$link = '';
		$type_doc_aff = "alt='".htmlentities($tdoc->table[$data->typdoc],ENT_QUOTES, $charset)."' title='".htmlentities($tdoc->table[$data->typdoc],ENT_QUOTES, $charset)."' ";

		switch ($info_gestion) {
			case GESTION_INFO_GESTION:
			case EDIT_INFO_GESTION:
				//Titre document
				if (SESSrights & CATALOGAGE_AUTH) {
					if ($data->resa_idnotice) {
						$link = "<a href='./catalog.php?categ=isbd&id=".$data->resa_idnotice."' $type_doc_aff>".htmlentities($data->tit, ENT_QUOTES, $charset).'</a>';
					} else if($data->resa_idbulletin) {
						$link = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$data->resa_idbulletin."' $type_doc_aff>".htmlentities($data->tit, ENT_QUOTES, $charset)."</a>";
					} else {
						$link = htmlentities($data->tit, ENT_QUOTES, $charset);
					}
				} else {
					$link = htmlentities($data->tit, ENT_QUOTES, $charset);
				}
				$aff_final.= '<td><b>'.$link.'</b></td>';
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
				break;


			case LECTEUR_INFO_GESTION:
				//Titre document
				if (SESSrights & CATALOGAGE_AUTH) {
					if ($data->resa_idnotice) {
						$link = "<a href='./catalog.php?categ=isbd&id=".$data->resa_idnotice."' $type_doc_aff>".htmlentities($data->tit, ENT_QUOTES, $charset)."</a>";
					} else if ($data->resa_idbulletin) {
						$link = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$data->resa_idbulletin."' $type_doc_aff>".htmlentities($data->tit, ENT_QUOTES, $charset)."</a>";
					} else {
						$link = htmlentities($data->tit, ENT_QUOTES, $charset);
					}
				} else {
					$link = htmlentities($data->tit, ENT_QUOTES, $charset);
				}
				$aff_final.= '<td><b>'.$link.'</b></td>';
				break;


			case NO_INFO_GESTION:
			default:
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
				break;
		}

		//Date prevision
		$aff_final.= '<td style="text-align:center;">'.$data->aff_resa_date.'</td>';

		switch ($info_gestion) {
			case GESTION_INFO_GESTION:
			case LECTEUR_INFO_GESTION:
				//id emprunteur
				$aff_final.= "<input type='hidden' id='id_empr[".$data->id_resa."]' name='id_empr[".$data->id_resa."]' value='".$data->id_empr."' />";
				//Date debut/fin prevision
				if($data->resa_validee) {
					$aff_final.= "<td class='sorttable_mmdd' style='text-align:center;'>".$data->aff_resa_date_debut.'</td>';
					$aff_final.= "<td class='sorttable_mmdd' style='text-align:center;'>".$data->aff_resa_date_fin." </td>";
					$aff_final.= "<td style='text-align:center;'>";
					if ($montrerquoi!='toresa') {
						$aff_final.= $data->resa_remaining_qty."/";
					}
					$aff_final.= $data->resa_qty."</td>";
					$aff_final.= "<td style='text-align:center;'><strong>X</strong></td>";
				} else {
					$aff_final .= "<td style='text-align:center;'>";
					$aff_final .= "<input type='hidden' id='resa_date_debut[".$data->id_resa."]' name='resa_date_debut[".$data->id_resa."]' value='".$data->aff_resa_date_debut."' />";
					$resa_date_debut = str_replace("-", "", $data->resa_date_debut);
					$aff_final .= "<input type='hidden' id='form_resa_date_debut_".$data->id_resa."' name='form_resa_date_debut_".$data->id_resa."' value='".$resa_date_debut."' />";
					$aff_final .= "<input type='button' class='bouton' sorttable_customkey='".$data->aff_resa_date_debut."' onclick=\"openPopUp('./select.php?what=calendrier&caller=check_resa_planning&date_caller=".$resa_date_debut."&param1=form_resa_date_debut_".$data->id_resa."&param2=form_resa_date_debut_lib_".$data->id_resa."&auto_submit=NO&date_anterieure=YES&func_to_call=func_callback&id=".$data->id_resa."&sub_param1=1', 'calendar'); \" value='".$data->aff_resa_date_debut."' name='form_resa_date_debut_lib_".$data->id_resa."'>";
					$aff_final .= '</td>';

					$aff_final .= "<td style='text-align:center;'>";
					$aff_final .= "<input type='hidden' id='resa_date_fin[".$data->id_resa."]' name='resa_date_fin[".$data->id_resa."]' value='".$data->aff_resa_date_fin."' />";
					$resa_date_fin = str_replace("-", "", $data->resa_date_fin);
					$aff_final .= "<input type='hidden' id='form_resa_date_fin_".$data->id_resa."' name='form_resa_date_fin_".$data->id_resa."' value='".$resa_date_fin."' />";
					$aff_final .= "<input type='button' class='bouton' sorttable_customkey='".$data->aff_resa_date_fin."' onclick=\"openPopUp('./select.php?what=calendrier&caller=check_resa_planning&date_caller=".$resa_date_fin."&param1=form_resa_date_fin_".$data->id_resa."&param2=form_resa_date_fin_lib_".$data->id_resa."&auto_submit=NO&date_anterieure=YES&func_to_call=func_callback&id=".$data->id_resa."&sub_param1=2', 'calendar')\" value='".$data->aff_resa_date_fin."' name='form_resa_date_fin_lib_".$data->id_resa."'>";
					$aff_final .= '</td>';
					$aff_final.= "<td style='text-align:center;'>";
					if ($montrerquoi!='toresa') {
						$aff_final.= $data->resa_remaining_qty."/";
					}
					$aff_final.= $data->resa_qty."</td>";
					$aff_final.= '<td></td>';
				}
				//Resa confirmee
				if($data->resa_confirmee) {
					$aff_final.= "<td style='text-align:center;'><strong>X</strong></td>";
				} else {
					$aff_final.= '<td></td>';
				}
				//Localisation retrait
				if ($pmb_location_resa_planning=="1") {
					$aff_final .= '<td>'.$tab_loc_ret[$data->resa_loc_retrait].'</td>';
				}
				break;


			case EDIT_INFO_GESTION:
				$aff_final.= "<td class='sorttable_mmdd' style='text-align:center;'>".$data->aff_resa_date_debut.'</td>';
				$aff_final.= "<td class='sorttable_mmdd' style='text-align:center;'>".$data->aff_resa_date_fin." </td>";
				$aff_final.= "<td style='text-align:center;'>";
				if ($montrerquoi!='toresa') {
					$aff_final.= $data->resa_remaining_qty."/";
				}
				$aff_final.= $data->resa_qty."</td>";
				if($data->resa_validee) {
					$aff_final.= "<td style='text-align:center;'><strong>X</strong></td>";
				} else {
					$aff_final.= '<td></td>';
				}
				if($data->resa_confirmee) {
					$aff_final.= "<td style='text-align:center;'><strong>X</strong></td>";
				} else {
					$aff_final.= '<td></td>';
				}
				//Localisation retrait
				if ($pmb_location_resa_planning=='1') {
					$aff_final .= '<td>'.$tab_loc_ret[$data->resa_loc_retrait].'</td>';
				}

				break;


			case NO_INFO_GESTION:
			default:
				$aff_final.= "<td style='text-align:center;'>".$data->aff_resa_date_debut.'</td>';
				$aff_final.= "<td style='text-align:center;'>".$data->aff_resa_date_fin." </td>";
				break;
		}

		switch ($info_gestion) {
			case GESTION_INFO_GESTION:
				$aff_final .= "\n<td style='text-align:center;'><input type='checkbox' id='resa_check[".$data->id_resa."]' name='resa_check[]' value='".$data->id_resa."' /></td>" ;
				break;

			case LECTEUR_INFO_GESTION:
				$aff_final .= "\n<td style='text-align:center;'><input type='button' id='resa_supp' name='resa_supp' class='bouton' value='X' onclick=\"document.location='./circ.php?categ=pret&sub=suppr_resa_planning_from_fiche&action=suppr_resa&id_resa=".$data->id_resa."&id_empr=".$idempr."';\" /></td>" ;
				break;

			case EDIT_INFO_GESTION:
				break;

			case NO_INFO_GESTION:
			default:
				break;
		}
		$aff_final.= "</tr>";
	}
	$aff_final.= "</table>";
	$aff_final.= "<div class='row'></div>";


	//Affichage boutons
	switch ($info_gestion) {
		case GESTION_INFO_GESTION:
			$aff_final .= "	<div class='right'>
						<input type='button' id='bt_chk' class='bouton' value='".$msg['resa_tout_cocher']."' onClick=\"checkAll('check_resa_planning', 'resa_check', check); return false;\" />
					</div>
					<div class='row'>&nbsp;</div>
					<div class='left' >
						<input type='button' class='bouton' value='".$msg['resa_planning_bt_val']."' onclick=\"this.form.resa_action.value='val_resa'; this.form.submit();\"/>&nbsp;
						<input type='button' class='bouton' value='".$msg['resa_planning_bt_raz_val']."' onclick=\"this.form.resa_action.value='raz_val_resa'; this.form.submit();\"/>&nbsp;
						<input type='button' class='bouton' value='".$msg['resa_planning_bt_conf']."' onclick=\"this.form.resa_action.value='conf_resa'; this.form.submit();\"/>&nbsp;
						<input type='button' class='bouton' value='".$msg['resa_planning_bt_raz_conf']."' onclick=\"this.form.resa_action.value='raz_conf_resa'; this.form.submit();\"/>&nbsp;
						<input type='button' class='bouton' value='".$msg['resa_planning_bt_to_resa']."' onclick=\"this.form.resa_action.value='to_resa'; this.form.submit();\"/>&nbsp;
					</div>

					<div class='right' >
						<input type='button' class='bouton' value='".$msg['resa_valider_suppression']."'  onclick=\"if(confirm('".$msg['resa_valider_suppression_confirm']."')){this.form.resa_action.value='suppr_resa'; this.form.submit();}\" />
					</div>
					<div class='row'></div>
				</form>" ;
			$aff_final.= "
				<script type='text/javascript'>
					var check = true;

					//Coche et décoche les éléments de la liste
					function checkAll(the_form, the_objet, do_check) {

						var elts = document.forms[the_form].elements[the_objet+'[]'] ;
						var elts_cnt  = (typeof(elts.length) != 'undefined')
					              ? elts.length
					              : 0;

						if (elts_cnt) {
							for (var i = 0; i < elts_cnt; i++) {
								elts[i].checked = do_check;
							}
						} else {
							elts.checked = do_check;
						}
						if (check == true) {
							check = false;
							document.getElementById('bt_chk').value = '".$msg['acquisition_sug_uncheckAll']."';
						} else {
							check = true;
							document.getElementById('bt_chk').value = '".$msg['acquisition_sug_checkAll']."';
						}
						return true;
					}

				</script>";
			break;
		case LECTEUR_INFO_GESTION:
		case EDIT_INFO_GESTION:
			$aff_final.= '</form>' ;
			break;

		case NO_INFO_GESTION:
		default:
			break;
	}

	return $aff_final ;
}

function resa_planning_loc_retrait($id_resa) {
	global $dbh,$msg,$charset;
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
			$res = pmb_mysql_query($rqt,$dbh);
			$res_trans = pmb_mysql_result($res,0) ;
		break;

	}

	return $res_trans;

}


//Generation entete réservation avec verification numero lecteur
function aff_entete($id_empr,&$layout_begin='',&$empr_cb=0) {
	global $dbh,$msg,$charset;
	$exists=false;
	$id_empr+=0;
	if($id_empr) {
		// récupération nom emprunteur
		$q = "SELECT empr_nom, empr_prenom, empr_cb FROM empr WHERE id_empr=$id_empr LIMIT 1";
		$r = pmb_mysql_query($q, $dbh);
		if(pmb_mysql_num_rows($r)) {
 			$o = pmb_mysql_fetch_object($r);
 			$name = $o->empr_prenom;
 			$name ? $name .= ' '.$o->empr_nom : $name = $o->empr_nom;
 			$layout_begin = str_replace('!!nom_lecteur!!', htmlentities($name,ENT_QUOTES,$charset), $layout_begin);
 			$layout_begin = str_replace('!!cb_lecteur!!', htmlentities($o->empr_cb,ENT_QUOTES,$charset), $layout_begin);
 			$empr_cb=$o->empr_cb;
			$exists=true;
		}
	}
	return $exists;
}


function check_record($id_notice,$id_bulletin) {
	global $dbh,$msg;
	$id_notice+=0;
	$id_bulletin+=0;
	$exists=false;
	if($id_notice || $id_bulletin) {
		$q = "SELECT expl_id FROM exemplaires where expl_notice=$id_notice and expl_bulletin=$id_bulletin LIMIT 1";
		$r = pmb_mysql_query($q, $dbh);
		if(pmb_mysql_num_rows($r)) {
			$exists=true;
		}
	}
	return $exists;
}


//Affichage des réservations planifiées sur le document courant par le lecteur courant
function doc_planning_list($id_empr, $id_notice, $id_bulletin) {

	global $msg, $dbh;

	$requete3 = "SELECT id_resa, resa_idempr, resa_idnotice, resa_date, resa_date_debut, resa_date_fin, resa_validee, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin ";
	$requete3.= "FROM resa_planning ";
	$requete3.= "WHERE resa_idempr='".$id_empr."' and resa_idnotice='".$id_notice."' ";
	$result3 = pmb_mysql_query($requete3, $dbh);

	if (pmb_mysql_num_rows($result3)) $message_resa = '<br /><b>'.$msg['resa_planning_enc'].'</b>';
	while ($resa = pmb_mysql_fetch_array($result3)) {
		$id_resa = $resa['id_resa'];
		$resa_idempr = $resa['resa_idempr'];
		$resa_idnotice = $resa['resa_idnotice'];
		$resa_date = $resa['resa_date'];
		$resa_date_debut = $resa['resa_date_debut'];
		$resa_date_fin = $resa['resa_date_fin'];
		$resa_validee = $resa['resa_validee'];
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

	return $message_resa;

}

function alert_empr_resa_planning($id_resa=0, $id_empr_concerne=0) {

	global $dbh;
	global $msg, $charset;
	global $PMBuserid, $PMBuseremail, $PMBuseremailbcc ;
	global $pdflettreresa_priorite_email ;
	global $pdflettreresa_before_list , $pdflettreresa_madame_monsieur, $pdflettreresa_after_list, $pdflettreresa_fdp;
	global $biblio_name, $biblio_email ;
	global $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_phone ;
	global $pdflettreresa_priorite_email_manuel;

	if ($pdflettreresa_priorite_email_manuel==3) return ;

	if(!count($id_resa)) return;
	$tmp_id_resa =implode(",",$id_resa);

	$query = "select distinct ";
	$query .= "trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
	$query .= "date_format(resa_date_fin, '".$msg["format_date"]."') as aff_resa_date_fin, ";
	$query .= "date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, ";
	$query .= "empr_prenom, empr_nom, empr_cb, empr_mail, empr_tel1, empr_sms, id_resa, ";
	$query .= "trim(concat(ifnull(notices_m.niveau_biblio,''), ifnull(notices_s.niveau_biblio,''))) as niveau_biblio, ";
	$query .= "trim(concat(ifnull(notices_m.notice_id,''), ifnull(notices_s.notice_id,''))) as id_notice ";
	$query .= "from (((resa_planning LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), empr ";
	$query .= "where id_resa in (".$tmp_id_resa.") and resa_validee=1 and resa_idempr=id_empr";
	if ($id_empr_concerne) $query .= "and id_empr=$id_empr_concerne ";

	$result = pmb_mysql_query($query, $dbh);
	if (pmb_mysql_num_rows($result)) {
		while ($o=pmb_mysql_fetch_object($result)) {
			if (($pdflettreresa_priorite_email_manuel==1 || $pdflettreresa_priorite_email_manuel==2) && $o->empr_mail) {
				$to = $o->empr_prenom." ".$o->empr_nom." <".$o->empr_mail.">";
				
				$mail_reader_resa_planning = new mail_reader_resa_planning();
				$res_envoi = $mail_reader_resa_planning->send_mail($o);
				
				if (!$res_envoi || $pdflettreresa_priorite_email_manuel==2) {
					print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=lettre_resa_planning&id_resa=$tmp_id_resa', 'lettre_confirm_resa".$tmp_id_resa."', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');</script>";
				}
			} elseif ($pdflettreresa_priorite_email_manuel!=3) {
				print "<script type='text/javascript'>openPopUp('./pdf.php?pdfdoc=lettre_resa_planning&id_resa=$tmp_id_resa', 'lettre_confirm_resa".$tmp_id_resa."', 600, 500, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes');</script>";
			}
			$rqt_maj = "update resa_planning set resa_confirmee=1 where id_resa in (".$tmp_id_resa.") and resa_validee=1 " ;
			pmb_mysql_query($rqt_maj, $dbh);
		}
	}
}


?>

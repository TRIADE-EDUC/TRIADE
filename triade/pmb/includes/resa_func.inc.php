<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_func.inc.php,v 1.164.4.3 2025/04/17 12:27:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/quotas.class.php");
require_once("$class_path/transfert.class.php");
require_once("$include_path/templates/resa.tpl.php");
require_once("$class_path/resa.class.php");
require_once("$class_path/resa_situation.class.php");
require_once("$class_path/mono_display.class.php");
require_once("$class_path/serial_display.class.php");

// defines pour flag affichage info de gestion
if (!defined('NO_INFO_GESTION')) define ('NO_INFO_GESTION', 0); // 0 >> aucune info de gestion : liste simple, attention utilisée un peu partout !
if (!defined('GESTION_INFO_GESTION')) define ('GESTION_INFO_GESTION', 1); // pour traitement des résa
if (!defined('LECTEUR_INFO_GESTION')) define ('LECTEUR_INFO_GESTION', 2); // pour affichage en fiche lecteur

function resa_list_get_column_title($resa_idnotice=0, $resa_idbulletin=0, $typdoc='') {
	global $charset;
	global $tdoc;
	
	$link = '';
	if(!empty($tdoc->table[$typdoc])) {
		$type_doc_aff= "alt='".htmlentities($tdoc->table[$typdoc],ENT_QUOTES, $charset)."' title='".htmlentities($tdoc->table[$typdoc],ENT_QUOTES, $charset)."' ";
	} else {
		$type_doc_aff= "";
	}
	if (SESSrights & CATALOGAGE_AUTH) {
		if ($resa_idnotice) {
			$mono_display = new mono_display($resa_idnotice);
			$link = "<a href='./catalog.php?categ=isbd&id=".$resa_idnotice."' $type_doc_aff>".$mono_display->header."</a>";
		} elseif ($resa_idbulletin) {
			$bulletinage_display = new bulletinage_display($resa_idbulletin);
			$link = "<a href='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=" . intval($resa_idbulletin) . "' $type_doc_aff>".$bulletinage_display->header."</a>";
		}
	} else {
		$link = reservation::get_notice_title($resa_idnotice, $resa_idbulletin);
	}
	return $link;
}

function resa_list ($idnotice=0, $idbulletin=0, $idempr=0, $order="", $where = "", $info_gestion=NO_INFO_GESTION, $url_gestion="",$ancre="") {

	global $msg;
	global $montrerquoi ;
	global $current_module ;
	global $pdflettreresa_priorite_email_manuel;
	global $pmb_lecteurs_localises, $empr_location_id ;
	global $pmb_transferts_actif,$f_loc, $transferts_choix_lieu_opac;
	global $resa_liste_jscript_GESTION_INFO_GESTION, $ajout_resa_jscript_choix_loc_retrait,$deflt_resas_location;
	global $transferts_site_fixe, $pmb_location_reservation;
	global $pmb_resa_planning;
	global $has_resa_available; // utilisé au niveau de la fiche lecteur
	
	$aff_final='';

	$sql_loc_resa_from="";
	$sql_suite="";
	$sql_loc_resa="";
	
	if (!$montrerquoi) $montrerquoi='all' ;
	if (!$order) $order="notices_m.index_sew, resa_idnotice, resa_idbulletin, resa_date" ;
	if ($pmb_lecteurs_localises && !$idempr){
		if ($f_loc=="")	$f_loc = $deflt_resas_location;
	}
	if ($pmb_transferts_actif=="1" && $f_loc && !$idempr) {
		switch ($transferts_choix_lieu_opac) {
			case "1":
				//retrait de la resa sur lieu choisi par le lecteur
				$sql_suite .= " AND resa_loc_retrait='".$f_loc."' ";
			break;
			case "2":
				//retrait de la resa sur lieu fixé
				if ($f_loc!=$transferts_site_fixe)
					$sql_suite.= " AND 0";
			break;
			case "3":
				//retrait de la resa sur lieu exemplaire
				// On affiche les résa que peut satisfaire la loc
				// respecter les droits de réservation du lecteur
				if($pmb_location_reservation) {
					$sql_loc_resa.=" and empr_location=resa_emprloc and resa_loc='".$f_loc."' ";
					$sql_loc_resa_from=", resa_loc ";
				}
			break;
			default:
				//retrait de la resa sur lieu lecteur
				$sql_suite .= " AND empr_location='".$f_loc."' ";
			break;
		}
	}elseif($pmb_location_reservation && $f_loc && !$idempr) {
		$sql_loc_resa.=" and empr_location=resa_emprloc and resa_loc='".$f_loc."' ";
		$sql_loc_resa_from=", resa_loc ";
	}

	$sql ="SELECT resa_idnotice, resa_idbulletin, resa_date, resa_date_debut, resa_date_fin, resa_cb, resa_confirmee, resa_idempr, ifnull(expl_cote,'') as expl_cote, empr_nom, empr_prenom, empr_cb, idlocation, location_libelle, resa_loc_retrait, resa_planning_id_resa,";
	$sql.=" trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')), ";
	$sql.=" if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')), ";
	$sql.="	ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, id_resa, ";
	$sql.=" ifnull(notices_m.typdoc,notices_s.typdoc) as typdoc, ";
	$sql.=" IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee,";
	$sql.=" if(resa_date_debut='0000-00-00', '', date_format(resa_date_debut, '".$msg["format_date"]."')) as aff_resa_date_debut,";
	$sql.=" if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin,";
	$sql.=" date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date " ;
	$sql.=" FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ";
	$sql.=" LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id ) ";
	$sql.=" LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
	$sql.=" LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id ";
	$sql.=" LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id ) ";
	$sql.=" LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb), ";
	$sql.=" empr, docs_location $sql_loc_resa_from ";
	$sql.=" WHERE resa_idempr = id_empr AND idlocation = empr_location ";
	$sql.=$sql_suite;

	if ($idempr)
		$sql.=" AND id_empr='$idempr'";

	if ($montrerquoi=='validees')
		$sql .= " AND resa_cb<>''";

	if ($montrerquoi=='invalidees')
		$sql .= " AND resa_cb=''";

	if ($montrerquoi=='valid_noconf') {
		$sql .= " AND resa_cb!=''";
		$sql .= " AND resa_confirmee=0";
	}
	if ($where)
		$sql.=" AND ".$where ;

	$sql.=" $sql_loc_resa ";
	$sql.=" ORDER BY ".$order ;

	if ($idnotice || $idbulletin) {
		$sql="SELECT resa_idnotice, resa_idbulletin, resa_date, resa_date_debut, resa_date_fin, resa_cb, resa_confirmee, resa_idempr, ifnull(expl_cote,'') as expl_cote, empr_nom, empr_prenom, empr_cb, idlocation, location_libelle, resa_loc_retrait, ";
		$sql.=" trim(concat(if(series_m.serie_name <>'', if(notices_m.tnvol <>'', concat(series_m.serie_name,', ',notices_m.tnvol,'. '), concat(series_m.serie_name,'. ')), if(notices_m.tnvol <>'', concat(notices_m.tnvol,'. '),'')), ";
		$sql.=" if(series_s.serie_name <>'', if(notices_s.tnvol <>'', concat(series_s.serie_name,', ',notices_s.tnvol,'. '), series_s.serie_name), if(notices_s.tnvol <>'', concat(notices_s.tnvol,'. '),'')), ";
		$sql.="	ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, id_resa, ";
		$sql.=" ifnull(notices_m.typdoc,notices_s.typdoc) as typdoc, ";
		$sql.=" IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee,";
		$sql.=" if(resa_date_debut='0000-00-00', '', date_format(resa_date_debut, '".$msg["format_date"]."')) as aff_resa_date_debut,";
		$sql.=" if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin,";
		$sql.=" date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date " ;
		$sql.=" FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ";
		$sql.=" LEFT JOIN series AS series_m ON notices_m.tparent_id = series_m.serie_id ) ";
		$sql.=" LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
		$sql.=" LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id ";
		$sql.=" LEFT JOIN series AS series_s ON notices_s.tparent_id = series_s.serie_id) ";
		$sql.=" LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb), ";
		$sql.=" empr, docs_location  ";
		$sql.=" WHERE resa_idempr = id_empr AND idlocation = empr_location  AND resa_idnotice = '$idnotice' AND resa_idbulletin='$idbulletin' ";
		$sql.=" ORDER BY  resa_date" ;
		$f_loc=0;
	}

	$req = pmb_mysql_query($sql);
	switch ($info_gestion) {
		case GESTION_INFO_GESTION:
			$aff_final .=
				"<script type=\"text/javascript\">
					function confirm_delete() {
						result = confirm('".$msg["resa_confirm_suppr"]."');
		       			if(result)
		           			return 1;
					}
				</script>
				<form class='form-$current_module' name='check_resa' action='$url_gestion' method='post'><div class='left'>" ;

			//les boutons radios
			$aff_final .= "<span class='usercheckbox'><input type='radio' name='montrerquoi' value='all' id='all' onclick='this.form.submit();' ";
			if ($montrerquoi=='all') $aff_final .= "checked" ;
			$aff_final .= "><label for='all'>".$msg['resa_show_all']."</label></span>&nbsp;<span class='usercheckbox'><input type='radio' name='montrerquoi' value='validees' id='validees' onclick='this.form.submit();' ";
			if ($montrerquoi=='validees') $aff_final .= "checked" ;
			$aff_final .= "><label for='validees'>".$msg['resa_show_validees']."</label></span>&nbsp;<span class='usercheckbox'><input type='radio' name='montrerquoi' value='invalidees' id='invalidees' onclick='this.form.submit();' ";
			if ($montrerquoi=='invalidees') $aff_final .= "checked" ;
			$aff_final .= "><label for='invalidees'>".$msg['resa_show_invalidees']."</label></span>&nbsp;<span class='usercheckbox'><input type='radio' name='montrerquoi' value='valid_noconf' id='valid_noconf' onclick='this.form.submit();' ";
			if ($montrerquoi=='valid_noconf') $aff_final .= "checked" ;
			$aff_final .= "><label for='valid_noconf'>".$msg['resa_show_non_confirmees']."</label></span>";

			if ($pmb_transferts_actif=="1" || $pmb_location_reservation) {
				//la liste de sélection de la localisation
				$aff_final .= "<br />".$msg["transferts_circ_resa_lib_localisation"];
				$aff_final .= "<select name='f_loc' onchange='document.check_resa.submit();'>";
				$res = pmb_mysql_query("SELECT idlocation, location_libelle FROM docs_location order by location_libelle");
				$aff_final .= "<option value='0'>".$msg["all_location"]."</option>";
				//on parcours la liste des options
				while ($value = pmb_mysql_fetch_array($res)) {
					//debut de l'option
					$aff_final .= "<option value='".$value[0]."'";
					if ($value[0]==$f_loc)
						//c'est l'option par défaut
						$aff_final .= " selected";

					//fin de l'option
					$aff_final .= ">".$value[1]."</option>";
				}
				$aff_final .= "</select>";
			}
			//le lien pour l'edition
			if (SESSrights & EDIT_AUTH) $lien_edit_resa_traiter = "<a href='./edit.php?categ=notices&sub=resa'>".$msg['1100']." : ".$msg['edit_resa_menu']."</a> / <a href='./edit.php?categ=notices&sub=resa_a_traiter'>".$msg['1100']." : ".$msg['edit_resa_menu_a_traiter']."</a>" ;

			$aff_final .= "</div><div class='right'>".$lien_edit_resa_traiter."</div>";
			$aff_final .= "<div class='row'>&nbsp;</div>" ;

			jscript_checkbox() ;
			break;
		case LECTEUR_INFO_GESTION:
			if (($pmb_transferts_actif=="1")&&($transferts_choix_lieu_opac=="1")) {
				$aff_final .= $ajout_resa_jscript_choix_loc_retrait;
			}			
			$aff_final .= "
				<script type=\"text/javascript\">
					function check_all_resa_confirme(e, form) {	
						if (!e) var e = window.event;
						if (e.stopPropagation) {
							e.stopPropagation();
						} else { 
							e.cancelBubble = true;
						}					
						var elts = document.getElementsByName('ids_resa[]'); 
						for (var i=0; i<elts.length; i++) {
	  						if(ids_resa_checked == 0){
	  							elts[i].checked = true;
	  						}else {
	  							elts[i].checked = false;
	  						}
	 					}	
	 					ids_resa_checked = 1-ids_resa_checked;
					}
					ids_resa_checked = 0;
				</script>
				<form class='form-$current_module' name='resa_list' action='./circ.php?categ=pret&sub=do_pret_resa&id_empr=".$idempr."' method='post'>";			
			break;
		default:
		case NO_INFO_GESTION:
			break;
	}
	if (!pmb_mysql_num_rows($req)) {
		switch ($info_gestion) {
			case GESTION_INFO_GESTION:
				$aff_final .= "</form>" ;
				break;
			case LECTEUR_INFO_GESTION:
			    $aff_final .= "</form>" ;
				break;
			default:
			case NO_INFO_GESTION:
				break;
		}
		return $aff_final ;
	}

	$aff_final .= "
	<script type='text/javascript' src='./javascript/sorttable.js'></script>
	<table  class='sortable' width='100%'>";

	$aff_final .= "<tr>" ;
	if (!$idnotice && !$idbulletin) $aff_final .= "<th>".$msg['233']."</th>" ;
	$aff_final .= "<th>".$msg['296']."</th>" ;
	if (!$idempr) {
		$aff_final .= "<th>".$msg['empr_nom_prenom']."</th>";
		if ($pmb_lecteurs_localises) $aff_final .= "<th>".$msg['empr_location']."</th>";
	}

	$aff_final .= 	"<th>".$msg['366']."</th>".
	 				"<th>".$msg['374']."</th>".
	 				"<th class='sorttable_alphadate'>".$msg['resa_condition']."</th>";
	if ($pmb_resa_planning) {
		$aff_final .=  	"<th>".$msg['resa_date_debut_td']."</th>";
	}
	$aff_final .=  	"<th>".$msg['resa_date_fin_td']."</th>";


	switch ($info_gestion) {
		case GESTION_INFO_GESTION:
			$aff_final .= "<th>" . $msg["resa_validee"] . "</th>";
			$aff_final .= "<th>" . $msg["resa_confirmee"] . "</th>";
			if ($pmb_transferts_actif=="1")
				$aff_final .= "<th>" . $msg["resa_loc_retrait"] . "</th>";
			$aff_final .= "<th>" . $msg["resa_selectionner"] . "</th>" ;
			if ($pmb_transferts_actif=="1") {
				$aff_final .= "<th>&nbsp;</th>";
			}
			break;
		case LECTEUR_INFO_GESTION:
			$aff_final .= "<th>" . $msg["resa_confirmee"] . "</th>";
			$aff_final .= "<th><input type='button' style='!!resa_confirmee_button!!' name='bloc_all' value='".$msg['resa_tout_cocher']."' class='bouton' title='".$msg['resa_tout_cocher']."' onClick='check_all_resa_confirme(event, this.form)'/></th>";
			if ($pmb_transferts_actif=="1")
				$aff_final .= "<th>" . $msg["resa_loc_retrait"] . "</th>";
			$aff_final .= "<th class='sorttable_nosort'>" . $msg["resa_suppr_th"] . "</th>" ;
			break;
		default:
		case NO_INFO_GESTION:
			break;
		}

	$aff_final .= "</tr>";
	$odd_even=0;
	$lien_deja_affiche = false;
	$has_resa_available = false;
	$flag_resa_confirme = false;
	//on parcours la liste des réservations
	while ($data = pmb_mysql_fetch_array($req)) {
		$resa_idnotice = $data['resa_idnotice'];
		$resa_idbulletin = $data['resa_idbulletin'];
		$resa_idempr = $data['resa_idempr'] ;

		$no_aff=0;
		if(!($idnotice || $idbulletin))
		if($f_loc &&!$idempr && $data['resa_cb'] && $data['resa_confirmee']){
			// Dans la liste des résa à traiter, on n'affiche pas la résa qui a été affecté par un autre site
			$query = "SELECT expl_location FROM exemplaires WHERE expl_cb='".$data['resa_cb']."' ";
			$res = @pmb_mysql_query($query);
			if(($data_expl = pmb_mysql_fetch_array($res))){
				if($data_expl['expl_location']!=$f_loc) {
					$no_aff=1;
					continue;
				}
			}
		}
		if($idempr)$f_loc=0;
		$rank = recupere_rang($resa_idempr, $resa_idnotice, $resa_idbulletin,$f_loc) ;
		$resa=new reservation($resa_idempr,$resa_idnotice, $resa_idbulletin);
		if($idempr) {
			$resa->set_on_empr_fiche(true);
		}
		$resa->get_resa_cb();
		
		$resa_situation = new resa_situation($resa->id);
		$resa_situation->set_resa($resa)
				->set_resa_cb($data['resa_cb'])
				->set_idlocation($data['idlocation'])
				->set_my_home_location($deflt_resas_location)
				->set_rank($rank)
				->set_no_aff($no_aff)
				->set_lien_deja_affiche($lien_deja_affiche);

		$situation = $resa_situation->get_display($info_gestion);
		
		$no_aff = $resa_situation->get_no_aff();
		$lien_deja_affiche = $resa_situation->get_lien_deja_affiche();


		if(!$no_aff || ($idnotice || $idbulletin)) {
			// on affiche les résultats
			$ancre_aff="";
			if($ancre==$data['id_resa'])	$ancre_aff=" id='ancre_resa' ";
			if ($odd_even==0) {
				$aff_final .= "\n<tr $ancre_aff class='odd' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='odd'\">";
				$odd_even=1;
			} else if ($odd_even==1) {
				$aff_final .= "\n<tr $ancre_aff class='even' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='even'\">";
				$odd_even=0;
			}

			if (!$idnotice && !$idbulletin) $aff_final .= "<td><b>".resa_list_get_column_title($resa_idnotice, $resa_idbulletin, $data['typdoc'])."</b></td>";
			$aff_final .= "<td class='center'>".$data['expl_cote']."</td>";
			if (!$idempr) {
				if (SESSrights & CIRCULATION_AUTH) $aff_final .= "<td><a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($data['empr_cb'])."\">".$data['empr_nom'].", ".$data['empr_prenom']."</a></td>";
				else $aff_final .= "<td>".$data['empr_nom'].", ".$data['empr_prenom']."</td>";
				if ($pmb_lecteurs_localises) $aff_final .= "<td>".$data['location_libelle']."</td>";
			}
			$aff_final .= "<td class='center'>".$rank."</td>";
			$aff_final .= "<td class='center'>".$data['aff_resa_date']."</td>";
			$aff_final .= "<td class='center'>".$situation."</td>";
			if ($pmb_resa_planning) {
				$aff_final .= "<td class='center'>".$data['aff_resa_date_debut']."</td>";
			}
			$aff_final .= "<td class='center'>".$data['aff_resa_date_fin']."</td>";

			// gestion du formulaire de validation/suppression
			switch ($info_gestion) {
				case GESTION_INFO_GESTION:
					$aff_final .= "\n<td class='center'>";
					if ($data['resa_cb']) $aff_final .= "<span style='color:red'><b>X</b></span>" ;
					else $aff_final .= "&nbsp;" ;
					$aff_final .= "</td>\n" ;
					$aff_final .= "\n<td class='center'>";
					if ($data['resa_confirmee']) $aff_final .= "<span style='color:red'><b>X</b></span>" ;
					else $aff_final .= "&nbsp;" ;
					$aff_final .= "</td>";
					if ($pmb_transferts_actif=="1") {
						$loc_retrait = resa_loc_retrait($data["id_resa"]);
						$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$loc_retrait."'";
						$libloc = @pmb_mysql_result(pmb_mysql_query($rqt),0);
						$aff_final .= "<td>".$libloc."</td>";
					}
					$aff_final .= "<td class='center'><input type='checkbox' name='suppr_id_resa[]' value='".$data['id_resa']."' id='suppr_resa' /></td>" ;
					if ($pmb_transferts_actif=="1") {
						if ($resa_situation->lien_transfert) {
							if($resa->transfert_resa_dispo($f_loc)){
								$img= get_url_icon("peb_in.png");
							}else {
								$img= get_url_icon("peb_out.png");
							}
							$aff_final .= "<td><a href='#' onclick=\"choisiExpl(this);return(false);\" id_resa=\"".$data['id_resa']."\" idnotice=\"$resa_idnotice\" idbul=\"$resa_idbulletin\" loc=\"$f_loc\" alt=\"".$msg["transferts_circ_resa_lib_choix_expl"]."\" title=\"".$msg["transferts_circ_resa_lib_choix_expl"]."\">".
											"<img src='$img'></a></td>";
						}else
							$aff_final .= "<td>&nbsp;</td>";
					}
					break;
				case LECTEUR_INFO_GESTION:
					$aff_final .= "\n<td class='center'>";
					if ($data['resa_confirmee']) {
						$aff_final .= "<span style='color:red;'><b>X</b></span></td><td class='center'><input type='checkbox' name='ids_resa[]' value='".$data['id_resa']."'>" ; 
						$flag_resa_confirme = true;
					}else $aff_final .= "</td><td>&nbsp;";
					$aff_final .= "</td>" ;
					if ($pmb_transferts_actif=="1") {
						if (($transferts_choix_lieu_opac=="1")&&($data['aff_resa_date_fin']=="")) {
							//choix du lieu de retrait
							$rqt = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle";
							$res_loc = pmb_mysql_query($rqt);
							$liste_loc = "";
							while($value = pmb_mysql_fetch_object($res_loc)) {
								$liste_loc .= "<option value='".$value->idlocation."'";
								if ($value->idlocation==$data['resa_loc_retrait'])
									$liste_loc .= " selected";
								$liste_loc .= ">" . $value->location_libelle . "</option>";
							}
							$aff_final .= str_replace("!!liste_loc!!",$liste_loc,"<td><select onchange=\"chgLocRetrait(" . $data['id_resa'] . ", this.options[this.selectedIndex].value)\">!!liste_loc!!</select></td>");
						} else {
							//on affiche le lieu de retrait
							$loc_retrait = resa_loc_retrait($data['id_resa']);
							$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$loc_retrait."'";
							$res = pmb_mysql_query($rqt);
							if (pmb_mysql_num_rows($res))
								$libloc = pmb_mysql_result($res,0);
							$aff_final .= "<td>".$libloc."</td>";
						}
					}
					$aff_final .= "\n<td class='center'>";
					$aff_final .= "<input type='button' class='bouton' name='suppr_resa' value='".$msg['raz']."' id='suppr_resa' ";
					$aff_final .= "onClick=\"document.location='./circ.php?categ=pret&sub=suppr_resa_from_fiche&action=suppr_resa&suppr_id_resa[]=".$data['id_resa']."&id_empr=$idempr'\" />" ;
					$aff_final .= "</td>";
					break;
				default:
				case NO_INFO_GESTION:
					break;
				}
			$aff_final .= "</tr>\n";
		}
		if($ancre==$data['id_resa'])	$aff_final.= "<a name='ancre_resa'></a>";
	} //fin du while

	$aff_final .= "</table>";
	if($ancre){
		$aff_final.= "
		<script language=javascript>
			document.location='#ancre_resa';
		</script>
		";
	}
	switch ($info_gestion) {
		case GESTION_INFO_GESTION:
			$aff_final .= "<table style='background:none;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;'>
					<tr><td style='background:none;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;text-align:left;'>";
			if ($pdflettreresa_priorite_email_manuel!=3) $aff_final .= "<input type='hidden' name='impression_confirmation' value='0' /><input type='button' class='bouton' value='".$msg['resa_impression_confirmation']."' onclick=\"this.form.action.value='suppr_resa'; this.form.impression_confirmation.value=1; this.form.submit();\"/></td>";
			$aff_final .= "<input type='hidden' name='action' value='' />";
			$aff_final .= "<td style='background:none;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;text-align:left;'><input type='button' class='bouton' value='".$msg['resa_valider_suppression']."'  onclick=\"if(confirm_delete()){this.form.action.value='suppr_resa'; this.form.submit();}\" />";

			$aff_final .= "</td><td style='background:none;border-right:0px;border-left:0px;border-bottom:0px;border-top:0px;text-align:right;'>";
			$aff_final .= "<input type='button' class='bouton' onClick=\"setCheckboxes('check_resa', 'suppr_id_resa', true); return false;\" value='".$msg['resa_tout_cocher']."' />";
			$aff_final .= "</td></tr></table></form>" ;
			break;
		case LECTEUR_INFO_GESTION:
			if($flag_resa_confirme) {			
				$aff_final.= "<input type='submit' class='bouton' onClick=\"\" value='".$msg['empr_do_pret_resa']."' /> ";
				$aff_final = str_replace('!!resa_confirmee_button!!','',$aff_final);
			}else {
				$aff_final = str_replace('!!resa_confirmee_button!!','display:none',$aff_final);				
			}
			$aff_final.= "</form>";
			break;
		default:
		case NO_INFO_GESTION:
			break;
	}

	pmb_mysql_free_result($req);

	return $aff_final ;
}

// cette fonction va retourner un tableau des résa pas traitées
function resa_list_resa_a_traiter () {
	/* Traitement :
		chercher toutes les réservations non traitées (resa_cb ="")
		construire le tableau avec le titre de l'ouvrage, le nom du réservataire et son rang
	*/
	global $msg;
	global $pmb_lecteurs_localises;
	global $deflt_resas_location;
	global $pmb_location_reservation;
	global $pmb_transferts_actif,$transferts_choix_lieu_opac,$transferts_site_fixe;
	global $f_loc,$f_dispo_loc;

	$tableau_final=array();

	$order="tit, resa_idnotice, resa_idbulletin, resa_date" ;
	
	$sql_loc_resa_from = '';
	$sql_suite = '';
	$sql_loc_resa = '';
	if ($pmb_lecteurs_localises){
		if ($f_loc=="")	$f_loc = $deflt_resas_location;
	}
	if ($pmb_transferts_actif=="1" && $f_loc) {
		switch ($transferts_choix_lieu_opac) {
			case "1":
				//retrait de la resa sur lieu choisi par le lecteur
				$sql_suite .= " AND resa_loc_retrait='".$f_loc."' ";
				break;
			case "2":
				//retrait de la resa sur lieu fixé
				if ($f_loc!=$transferts_site_fixe)
					$sql_suite.= " AND 0";
				break;
			case "3":
				//retrait de la resa sur lieu exemplaire
				// On affiche les résa que peut satisfaire la loc
				// respecter les droits de réservation du lecteur
				if($pmb_location_reservation) {
					$sql_loc_resa.=" and empr_location=resa_emprloc and resa_loc='".$f_loc."' ";
					$sql_loc_resa_from=", resa_loc ";
				}
				break;
			default:
				//retrait de la resa sur lieu lecteur
				$sql_suite .= " AND empr_location='".$f_loc."' ";
				break;
		}
	}elseif($pmb_location_reservation && $f_loc) {
		$sql_loc_resa.=" and empr_location=resa_emprloc and resa_loc='".$f_loc."' ";
		$sql_loc_resa_from=", resa_loc ";
	}

	$sql="SELECT resa_idnotice, resa_idbulletin, resa_date, resa_date_fin, resa_cb, resa_idempr, empr_nom, empr_prenom, empr_cb, empr_location, ";
	$sql.=" trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, id_resa, ";
	$sql.=" IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_debut, '".$msg["format_date"]."') as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin, date_format(resa_date, '".$msg["format_date"]."') as aff_resa_date " ;
	$sql.=" FROM ((((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) ";
	$sql.=" LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) ";
	$sql.=" LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id) ";
	$sql.=" LEFT JOIN exemplaires ON resa_cb = exemplaires.expl_cb), empr $sql_loc_resa_from ";
	$sql.=" where resa_idempr=id_empr and (resa_cb='' or resa_cb is null) ";
	$sql.=$sql_suite;
	$sql.=" group by resa_idnotice, resa_idbulletin, resa_idempr ";
	$sql.=" order by ".$order ;
	
	$req = pmb_mysql_query($sql) or die("Erreur SQL !<br />".$sql."<br />".pmb_mysql_error());

	if (!pmb_mysql_num_rows($req)) return $tableau_final;

	while ($data = pmb_mysql_fetch_array($req)) {
		if($pmb_lecteurs_localises){
			$requete = "SELECT location_libelle as empr_loc_libelle FROM docs_location WHERE idlocation= '".$data['empr_location']."' ";
			$result = @pmb_mysql_query($requete);
			$res_empr = pmb_mysql_fetch_object($result);
		}

		$resa_idnotice = $data['resa_idnotice'];
		$resa_idbulletin = $data['resa_idbulletin'];

		$resa=new reservation(0, $resa_idnotice, $resa_idbulletin);
		
		// on compte le nombre total d'exemplaires prêtables pour la notice
		$total_ex = $resa->get_number_expl_lendable();

		// on compte le nombre d'exemplaires sortis
		$total_sortis = $resa->get_number_expl_out();

		// on en déduit le nombre d'exemplaires disponibles
		$total_dispo = $total_ex - $total_sortis ;

		// on a au moins UN dispo :
		if ($total_dispo > 0) {
			$available = true;
			$rank = recupere_rang($data['resa_idempr'], $resa_idnotice, $resa_idbulletin) ;
			if($rank>$total_dispo)	$available = false;

			if ($pmb_transferts_actif == "1") {
				$dest_loc = resa_loc_retrait($data['id_resa']);

				if ($dest_loc!=0) {
					$total_ex = $resa->get_number_expl_lendable($dest_loc);
					if ($total_ex==0) {
						//on a pas d'exemplaires sur le site de retrait
 						//on regarde si on en ailleurs
 						$total_ex = $resa->get_number_expl_lendable($dest_loc, true);
 						if ($total_ex!=0) {
 							//on en a au moins un ailleurs!
 							//on regarde si un des exemplaires n'est pas en transfert pour cette resa !
 							$query = "SELECT id_transfert FROM transferts WHERE etat_transfert=0 AND origine=4 AND origine_comp=".$data['id_resa']." limit 1";
 							$tresult = pmb_mysql_query($query);
 							if (pmb_mysql_num_rows($tresult)) {
 								//on a un transfert en cours
 								$available = false;
							} elseif($total_ex>=$rank)	{
 								if(!$resa->transfert_resa_dispo($dest_loc)){
 									//non disponible dans une autre localisation
 									$available = false;
 								}
 							}
 						}
					}
				}
			}
			// un exemplaire est disponible pour cette resa
			if ($available) {
				$resa_tit = "";
				if ($resa_idnotice) {
					$mono_display = new mono_display($resa_idnotice);
					$resa_tit = $mono_display->header;
				} elseif ($resa_idbulletin) {
					$bulletinage_display = new bulletinage_display($resa_idbulletin);
					$resa_tit = $bulletinage_display->header;
				}
				$rank = recupere_rang($data['resa_idempr'], $resa_idnotice, $resa_idbulletin) ;
				if ($pmb_transferts_actif=="1") {
					$loc_retrait = resa_loc_retrait($data["id_resa"]);
					$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation='".$loc_retrait."'";
					$libloc_retrait = @pmb_mysql_result(pmb_mysql_query($rqt),0);
				} else {
					$libloc_retrait = "";
				}
				$tableau_final[] = array(
						'resa_tit' => $resa_tit,
						'resa_idnotice' => $resa_idnotice,
						'resa_idbulletin' => $resa_idbulletin,
						'resa_idempr' => $data['resa_idempr'],
						'resa_empr' => $data['empr_nom']." ".$data['empr_prenom'],
						'resa_empr_loc_libelle' => $res_empr->empr_loc_libelle,
						'rank' => $rank,
						'loc_retrait_libelle' => $libloc_retrait) ;
			}
		}
	} // fin while

	pmb_mysql_free_result($req);
	return $tableau_final ;
}

// permet de savoir si un CB expl est déjà affecté à une résa
function verif_cb_utilise ($cb) {
	$rqt = "select id_resa from resa where resa_cb='".addslashes($cb)."' ";
	$res = pmb_mysql_query($rqt) ;
	$nb=pmb_mysql_num_rows($res) ;
	if (!$nb) return 0 ;
	$obj=pmb_mysql_fetch_object($res) ;
	return $obj->id_resa ;
}

// Ancien prototype générant une erreur sur une version PHP:
// function get_loc_resa_transfert ($cb,&$id_resa=0) {
// Cette fonction ne semble plus utilisée
function get_loc_resa_transfert ($cb,&$id_resa) {
	global $pmb_utiliser_calendrier, $deflt2docs_location;

	// chercher s'il s'agit d'une notice ou d'un bulletin
	$rqt = "SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_cb='".$cb."' ";
	$res = pmb_mysql_query($rqt) ;
	$nb=pmb_mysql_num_rows($res) ;
	if (!$nb) return 0 ;

	$obj=pmb_mysql_fetch_object($res) ;
	$id_resa = intval($id_resa);
	if ($id_resa==0) {
		// chercher le premier (par ordre de rang, donc de date de début de résa, non validé
		$rqt = 	"SELECT id_resa, resa_idempr,resa_loc_retrait
				FROM resa
				WHERE resa_idnotice='".$obj->expl_notice."'
					AND resa_idbulletin='".$obj->expl_bulletin."'
					AND resa_cb=''
					AND resa_date_fin='0000-00-00'
				ORDER BY resa_date ";
	} else {
		//on sait de qu'elle resa on parle .....
		$rqt = 	"SELECT id_resa, resa_idempr,resa_loc_retrait FROM resa WHERE id_resa='".$id_resa."'";
	}
	$res = pmb_mysql_query($rqt) ;
	if (!pmb_mysql_num_rows($res)) {
	    return 0 ;
	}
	$obj_resa=pmb_mysql_fetch_object($res) ;
	$loc_retait=resa_loc_retrait($obj_resa->id_resa);
	$id_resa= $obj_resa->id_resa ;
	if ($loc_retait!=$deflt2docs_location) {
	    return $loc_retait;
	}
	$nb_days = reservation::get_time($obj_resa->resa_idempr,$obj->expl_notice,$obj->expl_bulletin) ;
	$rqt_date = "select date_add(sysdate(), INTERVAL '$nb_days' DAY) as date_fin ";
	$resultatdate = pmb_mysql_query($rqt_date);
	$res = pmb_mysql_fetch_object($resultatdate) ;
	$date_fin = $res->date_fin ;
	if ($pmb_utiliser_calendrier) {
		$rqt_date = "select date_ouverture from ouvertures where ouvert=1 and num_location=$deflt2docs_location and to_days(date_ouverture)>=to_days('$date_fin') order by date_ouverture ";
		$resultatdate=pmb_mysql_query($rqt_date);
		$res=pmb_mysql_fetch_object($resultatdate) ;
		if ($res->date_ouverture) {
		    $date_fin=$res->date_ouverture ;
		}
	}

	// mettre resa_cb à jour pour cette resa
	$rqt = "update resa set resa_cb='".$cb."' " ;
	$rqt .= ", resa_date_debut=sysdate() " ;
	$rqt .= ", resa_date_fin='$date_fin' and resa_loc_retrait='$deflt2docs_location' ";
	$rqt .= " where id_resa='".$obj_resa->id_resa."' ";
	$res = pmb_mysql_query($rqt);

	$id_resa= $obj_resa->id_resa ;
	return $loc_retait;
}

function affecte_cb ($cb,$id_resa=0) {
	global $pmb_utiliser_calendrier, $pmb_location_reservation,$pmb_transferts_actif,$transferts_choix_lieu_opac,$deflt_docs_location;
	global $pmb_resa_planning;

	// chercher s'il s'agit d'une notice ou d'un bulletin
	$rqt = "SELECT expl_notice, expl_bulletin FROM exemplaires WHERE expl_cb='".$cb."' ";
	$res = pmb_mysql_query($rqt) ;
	$nb=pmb_mysql_num_rows($res) ;
	if (!$nb) return 0 ;

	$obj=pmb_mysql_fetch_object($res) ;
	$id_resa = intval($id_resa);
	if ($id_resa==0) {
		$where = '';
		$from = '';
		if ($pmb_transferts_actif=="1") {
			switch ($transferts_choix_lieu_opac) {
				case "1":
					//retrait de la resa sur lieu choisi par le lecteur
					$where= " AND resa_loc_retrait=" . $deflt_docs_location;
				break;
				case "2":
					//retrait de la resa sur lieu fixé
					$where= " AND resa_loc_retrait=" . $deflt_docs_location;
				break;
				case "3":
					//retrait de la resa sur lieu exemplaire
					if(!$pmb_location_reservation) {
						$from= " ,exemplaires ";
						$where= " AND expl_cb='$cb' and expl_location=" . $deflt_docs_location;
					} else {
						$where= " and expl_location=" . $deflt_docs_location;
					}
				break;
				default:
					//retrait de la resa sur lieu lecteur
					if(!$pmb_location_reservation) {
						$from= " ,empr ";
					}
					//Résa sur le lieu du lecteur, uniquement si résa de rang le plus faible
					$where= " AND resa_idempr=id_empr and empr_location=" . $deflt_docs_location;
				break;
			} //switch $transferts_choix_lieu_opac
		}
		$from_loc_resa = '';
		$sql_loc_resa = '';
		if($pmb_location_reservation) {
			$from_loc_resa= " ,empr, resa_loc, exemplaires ";
			$sql_loc_resa=" and resa_idempr=id_empr and empr_location=resa_emprloc and resa_loc='$deflt_docs_location' ";
			$sql_loc_resa.=" and expl_location=resa_loc AND expl_cb='$cb' ";
			$rqt_min_resa_date = "SELECT MIN(resa_date) AS madateresa FROM resa, empr, resa_loc WHERE resa_idnotice='".$obj->expl_notice."' AND resa_idbulletin='".$obj->expl_bulletin."' AND resa_cb=''";
			$rqt_min_resa_date .= " and resa_idempr=id_empr and empr_location=resa_emprloc and resa_loc='$deflt_docs_location'";
		} else {
		    $rqt_min_resa_date = "SELECT MIN(resa_date) AS madateresa FROM resa WHERE resa_idnotice='".$obj->expl_notice."' AND resa_idbulletin='".$obj->expl_bulletin."' AND resa_cb=''";
		}
		$where.= " AND id_resa IN
					(
						SELECT id_resa
						FROM resa, (".$rqt_min_resa_date.") AS resa_bis
						WHERE resa_date=madateresa AND resa_idnotice='".$obj->expl_notice."' AND resa_idbulletin='".$obj->expl_bulletin."'
					)";
		// chercher le premier (par ordre de rang, donc de date de début de résa, non validé
		$rqt = 	"SELECT id_resa, resa_idempr, resa_loc_retrait, resa_date_fin, resa_planning_id_resa
						FROM resa $from $from_loc_resa
				WHERE resa_idnotice='".$obj->expl_notice."'
					AND resa_idbulletin='".$obj->expl_bulletin."'
					AND resa_cb=''
					$where
					$sql_loc_resa
				ORDER BY resa_date ";
	} else {
		//on sait de quelle resa on parle ...
		$rqt = 	"SELECT id_resa, resa_idempr,resa_loc_retrait, resa_date_fin, resa_planning_id_resa FROM resa WHERE id_resa='".$id_resa."'";
	}
	$res = pmb_mysql_query($rqt) ;
	if (!pmb_mysql_num_rows($res)) {
	    return 0 ;
	}
	$obj_resa=pmb_mysql_fetch_object($res) ;
	/*
	$rqt_loc_retrait="";
	if($pmb_transferts_actif) {
		$rqt = "SELECT empr_location FROM resa INNER JOIN empr ON resa_idempr = id_empr WHERE id_resa=".$obj_resa->id_resa;
		$res = pmb_mysql_query($rqt);
		$empr_location = pmb_mysql_result($res,0) ;
		if($obj_resa->resa_loc_retrait==$deflt_docs_location || $empr_location==$deflt_docs_location) {
			$rqt_loc_retrait.= ", resa_loc_retrait='$deflt2docs_location' " ;
		} else return 0 ;
	}
		*/

	if($obj_resa->resa_date_fin=='0000-00-00' || $obj_resa->resa_planning_id_resa==0) {
		$nb_days = reservation::get_time($obj_resa->resa_idempr,$obj->expl_notice,$obj->expl_bulletin) ;
		$rqt_date = "select date_add(sysdate(), INTERVAL '$nb_days' DAY) as date_fin ";
		$resultatdate = pmb_mysql_query($rqt_date);
		$res = pmb_mysql_fetch_object($resultatdate) ;
		$date_fin = $res->date_fin ;
	} else {
		$date_fin = $obj_resa->resa_date_fin;
	}

	if ($pmb_utiliser_calendrier) {
		$rqt_date = "select date_ouverture from ouvertures where ouvert=1 and num_location=$deflt_docs_location and to_days(date_ouverture)>=to_days('$date_fin') order by date_ouverture ";
		$resultatdate=pmb_mysql_query($rqt_date);
		$res=pmb_mysql_fetch_object($resultatdate) ;
		if ($res->date_ouverture) $date_fin=$res->date_ouverture ;
	}

	// mettre resa_cb à jour pour cette resa
	$rqt = "update resa set resa_cb='".$cb."' " ;
	if ((!$pmb_resa_planning) || ($obj_resa->resa_planning_id_resa==0)) {
		$rqt .= ", resa_date_debut=sysdate() " ;
	}
	$rqt .= ", resa_date_fin='$date_fin', resa_loc_retrait='$deflt_docs_location' ";
	$rqt .= " where id_resa='".$obj_resa->id_resa."' ";
	$res = pmb_mysql_query($rqt);

	$rqt = "delete from resa_ranger where resa_cb='".$cb."' ";
	$res = pmb_mysql_query($rqt);
	return $obj_resa->id_resa ;
}

function resa_transfert($id_resa,$cb) {
	global $transferts_choix_lieu_opac, $transferts_site_fixe;

	$res_trans = 0;

	//pour la gestion des transferts
	$trans = new transfert();

	//les informations de l'exemplaire
	$rqt = "SELECT expl_location FROM exemplaires WHERE expl_cb='" . $cb ."'";
	$res = pmb_mysql_query($rqt);
	$expl_loc = pmb_mysql_result($res,0) ;

	switch ($transferts_choix_lieu_opac) {
		case "1":
			//retrait de la resa sur lieu choisi par le lecteur
			$rqt = "SELECT resa_loc_retrait FROM resa WHERE id_resa='".$id_resa."'";
			$res = pmb_mysql_query($rqt);
			$loc_retrait = pmb_mysql_result($res,0);

			if ($loc_retrait != $expl_loc) {
				//l'exemplaire n'est pas sur le bon site
				//on genere un transfert du site de l'exemplaire vers le site de retrait
				$trans->transfert_pour_resa($cb, $loc_retrait, $id_resa);
				$res_trans = $loc_retrait;
			}

			break;

		case "2":
			//retrait de la resa sur lieu fixé
			if ($transferts_site_fixe != $expl_loc) {
				//l'exemplaire n'est pas sur le bon site
				//on genere un transfert du site de l'exemplaire vers le site fixé
				$trans->transfert_pour_resa($cb, $transferts_site_fixe, $id_resa);
				$res_trans = $transferts_site_fixe;
			}
			break;

		case "3":
			//retrait de la resa sur lieu exemplaire
			//==>on fait rien !
		break;
		default:
			//retrait de la resa sur lieu lecteur
			//on recupere la localisation de l'emprunteur
			$rqt = "SELECT empr_location FROM resa INNER JOIN empr ON resa_idempr = id_empr WHERE id_resa=".$id_resa;
			$res = pmb_mysql_query($rqt);
			$empr_location = pmb_mysql_result($res,0) ;

			if ($empr_location != $expl_loc) {
				//l'exemplaire n'est pas sur le bon site
				//on genere un transfert du site de l'exemplaire vers le site du lecteur
				$trans->transfert_pour_resa($cb, $empr_location, $id_resa);
				$res_trans = $empr_location;
			}
		break;

	}

	return $res_trans;
}

function resa_loc_retrait($id_resa) {
	global $transferts_choix_lieu_opac, $transferts_site_fixe;

	$res_trans = 0;

	switch ($transferts_choix_lieu_opac) {
		case "1":
			//retrait de la resa sur lieu choisi par le lecteur
			$rqt = "SELECT resa_loc_retrait FROM resa WHERE id_resa='".$id_resa."'";
			$res = pmb_mysql_query($rqt);
			$res_trans = pmb_mysql_result($res,0);
		break;
		case "2":
			//retrait de la resa sur lieu fixé
			$res_trans = $transferts_site_fixe;
		break;
		case "3":
			//retrait de la resa sur lieu exemplaire
			//==>on fait rien !
		break;
		default:
			//retrait de la resa sur lieu lecteur
			//on recupere la localisation de l'emprunteur
			$rqt = "SELECT empr_location FROM resa INNER JOIN empr ON resa_idempr = id_empr WHERE id_resa='".$id_resa."'";
			$res = pmb_mysql_query($rqt);
			$res_trans = pmb_mysql_result($res,0) ;
		break;

	}

	return $res_trans;

}

function desaffecte_cb ($cb,$id_resa=0) {
    $id_resa = intval($id_resa);
	if ($id_resa!=0)
		$rqt = "UPDATE resa SET resa_cb='', resa_date_debut='0000-00-00', resa_date_fin='0000-00-00' WHERE resa_cb='".$cb."' AND id_resa='".$id_resa."'";
	else
		$rqt = "UPDATE resa SET resa_cb='', resa_date_debut='0000-00-00', resa_date_fin='0000-00-00' WHERE resa_cb='".$cb."' ";
	pmb_mysql_query($rqt) ;
	return pmb_mysql_affected_rows() ;
}

//   calcul du rang d'un emprunteur sur une réservation
function recupere_rang($id_empr, $id_notice, $id_bulletin,$loc=0) {
	$rank = 1;
	$query = reservation::get_query_rank($id_notice, $id_bulletin, $loc);
	$result = pmb_mysql_query($query);
	while($resa=pmb_mysql_fetch_object($result)) {
		if($resa->resa_idempr == $id_empr) break;
		$rank++;
	}
	return $rank;
}

function recupere_rangs($id_notice, $id_bulletin,$loc=0) {
	$ranks = array();
	$query = reservation::get_query_rank($id_notice, $id_bulletin, $loc);
	$result = pmb_mysql_query($query);
	$rank = 1;
	while($resa=pmb_mysql_fetch_object($result)) {
		$ranks[$resa->id_resa] = $rank;
		$rank++;
	}
	return $ranks;
}

// retourne un tableau constitué des exemplaires disponibles pour une résa donnée
function expl_dispo ($no_notice=0, $no_bulletin=0) {
	global $pmb_lecteurs_localises, $pmb_location_reservation,$deflt_docs_location;

	$tableau = array();
	if($pmb_location_reservation) {
		$sql_loc_resa=" and exemplaires.expl_location=resa_emprloc and resa_loc='".$deflt_docs_location."' ";
		$sql_loc_resa_from=", resa_loc ";
	} else {
		$sql_loc_resa="";
		$sql_loc_resa_from="";
	}
	if ($pmb_lecteurs_localises) {
		$sql_localisation=", case  when exemplaires.expl_location = $deflt_docs_location then 1 else 0 END as loc_ici ";
		$sql_order_localisation=" loc_ici desc, ";
	} else {
		$sql_localisation="";
		$sql_order_localisation="";
	}
	// on récupère les données des exemplaires
	$requete = "SELECT expl_id, expl_cb, expl_cote, expl_notice, expl_bulletin, pret_retour, idlocation, location_libelle, section_libelle, statut_libelle, tdoc_libelle $sql_localisation ";
	$requete .= " FROM docs_location, docs_section, docs_statut, docs_type $sql_loc_resa_from , ";
	$requete .= " exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl";
	$requete .= " WHERE expl_notice='$no_notice' and expl_bulletin='$no_bulletin' ";
	$requete .= " AND docs_statut.statut_allow_resa=1";
	$requete .= " AND exemplaires.expl_location=docs_location.idlocation";
	$requete .= " AND exemplaires.expl_section=docs_section.idsection ";
	$requete .= " AND exemplaires.expl_statut=docs_statut.idstatut ";
	$requete .= " AND exemplaires.expl_typdoc=docs_type.idtyp_doc $sql_loc_resa";
	$requete .= " order by $sql_order_localisation location_libelle, section_libelle, expl_cote ";

	$result = pmb_mysql_query($requete);

	if ($result) {
		while($expl = pmb_mysql_fetch_object($result)) {
			if(!$expl->pret_retour && !verif_cb_utilise($expl->expl_cb))
				if (!$pmb_lecteurs_localises) $expl->loc_ici =1;
				$tableau[] = array (
					'expl_id' => $expl->expl_id,
					'expl_cb' => $expl->expl_cb,
					'expl_notice' => $expl->expl_notice,
					'expl_bulletin' => $expl->expl_bulletin,
					'expl_cote' => $expl->expl_cote,
					'idlocation' => $expl->idlocation,
					'location' => $expl->location_libelle,
					'section' => $expl->section_libelle,
					'statut' => $expl->statut_libelle ,
					'support' => $expl->tdoc_libelle ,
					'loc_ici' => $expl->loc_ici ) ;
		}
	}
	return $tableau ;
}

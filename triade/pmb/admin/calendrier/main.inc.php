<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.27 2023/08/02 07:36:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $sub, $action, $current_module, $msg, $faire, $base_url, $base_url_mois;
global $book_location_id, $loc, $date_deb, $date_fin, $commentaire, $duplicate_locs, $deflt2docs_location, $admin_calendrier_form;
global $page, $annee, $annee_mois, $date;

include "./admin/calendrier/calendrier_func.inc.php" ;
require_once($class_path."/docs_location.class.php");

if (($faire=="ouvrir" || $faire=="fermer" || $faire=="initialization") && $loc!="") {
	$date_deb = detectFormatDate($date_deb);
	$date_fin = detectFormatDate($date_fin);
	if ($faire=="ouvrir") {
		$ouverture=1 ;
	} else {
		$ouverture=0 ;
	}
	//Date de début + date de fin
	if($date_deb != "0000-00-00" && $date_fin != "0000-00-00") {
		$rqt_date = "select if(TO_DAYS('".$date_fin."')>=TO_DAYS('".$date_deb."'),1,0) as OK";
		$resultatdate=pmb_mysql_query($rqt_date);
		$res=pmb_mysql_fetch_object($resultatdate) ;
		$date_courante = $date_deb ;
		while ($res->OK) {
			$rqt_date = "select dayofweek('".$date_courante."') as jour";
			$resultatdate=pmb_mysql_query($rqt_date);
			$res=pmb_mysql_fetch_object($resultatdate) ;
			$jour = "j".$res->jour ;
			// OK : traitement
			if (!empty(${$jour})) {
				calendar_open_or_close_date($date_courante, $ouverture, $commentaire);
			}
			$rqt_date = "select if(to_days(date_add('".$date_courante."', INTERVAL 1 DAY))<=TO_DAYS('".$date_fin."'),1,0) as OK, date_add('".$date_courante."', INTERVAL 1 DAY) as date_courante, dayofweek(date_add('".$date_courante."', INTERVAL 1 DAY)) as jour";
			$resultatdate=pmb_mysql_query($rqt_date);
			$res=pmb_mysql_fetch_object($resultatdate) ;
			$date_courante=$res->date_courante ;
		}
	} elseif($date_deb != "0000-00-00") {
		//Date précise
		$date_courante = $date_deb;
		calendar_open_or_close_date($date_courante, $ouverture, $commentaire);
	}
}

if ($faire=="commentaire" && $annee_mois && ($loc!="")) {
	for ($i=1; $i<=31; $i++) {
		$i_2 = substr("0".$i, -2) ;
		$var_jour_comment = "comment_".$i_2 ;
		$commentaire = ${$var_jour_comment} ;
		if ($commentaire) {
			$date_courante = $annee_mois."-".$i_2;
			$rqt_date = "update ouvertures set commentaire='$commentaire' where date_ouverture='$date_courante' and num_location=$loc";
			$resultatdate=pmb_mysql_query($rqt_date);
			if (!pmb_mysql_affected_rows()) {
				$rqt_date = "insert into ouvertures set ouvert=0, date_ouverture='$date_courante', commentaire='$commentaire', num_location=$loc ";
				$resultatdate=pmb_mysql_query($rqt_date);
				if (!pmb_mysql_affected_rows()) die ("insert into ouvertures failed") ;
			}
			if (is_array($duplicate_locs)) {
				foreach ($duplicate_locs as $duplicate_loc) {
					$rqt_date = "update ouvertures set commentaire='$commentaire' where date_ouverture='$date_courante' and num_location=$duplicate_loc";
					$resultatdate=pmb_mysql_query($rqt_date);
					if (!pmb_mysql_affected_rows()) {
						$rqt_date = "insert into ouvertures set ouvert=0, date_ouverture='$date_courante', commentaire='$commentaire', num_location=$duplicate_loc ";
						$resultatdate=pmb_mysql_query($rqt_date);
						if (!pmb_mysql_affected_rows()) die ("insert into ouvertures failed") ;
					}
				}
			}
		}
	}
}

if (($action=="O" || $action=="F") && $date && $loc!="") {
	$rqt_date = "update ouvertures set ouvert=if(ouvert=0, 1, 0) where date_ouverture='$date' and num_location=$loc ";
	$resultatdate=pmb_mysql_query($rqt_date);
	if (!pmb_mysql_affected_rows()) {
		$rqt_date = "insert into ouvertures set ouvert=if('".$action."'='O', 1, 0), date_ouverture='$date', commentaire='', num_location=$loc ";
		$resultatdate=pmb_mysql_query($rqt_date);
		if (!pmb_mysql_affected_rows()) die ("insert into ouvertures failes") ;
	}
}

if (!$book_location_id) {
	if ( empty($loc) )
		$loc = $deflt2docs_location;
} else {
	$loc = $book_location_id;
}

switch ($sub) {
	case "edition":
		$params['link_on_day'] = "" ;
		echo pmb_bidi(admin_calendar_gestion($date, 0, "", "", 1, $loc));
		break;
	case "consulter":
	default:
		$params['link_on_day'] = $base_url ;

		print "<form method='post' action='$page?categ=$categ&loc=$loc' class='form-$current_module'>
			<div class='form-".$current_module."'>
				<div class='row'>
					<label class='etiquette'>$msg[empr_location] :</label>
					&nbsp;
					".docs_location::gen_combo_box($loc)."
					&nbsp;
					<input class='bouton' type='submit' value='".$msg['actualiser']."' />
				</div>
			</div>
			</form>";
		$result=pmb_mysql_query("select location_libelle,name from docs_location where idlocation=$loc");
		if(pmb_mysql_num_rows($result) == 1) {
			$admin_calendrier_form=str_replace('!!biblio_name!!',pmb_mysql_result($result,0,"name"),$admin_calendrier_form);
			$admin_calendrier_form=str_replace('!!localisation!!',$msg['empr_location'].' : '.pmb_mysql_result($result,0,"location_libelle"),$admin_calendrier_form);
			$admin_calendrier_form=str_replace('!!book_location_id!!',$loc,$admin_calendrier_form);
		}
		else {
			$admin_calendrier_form=str_replace('!!biblio_name!!','',$admin_calendrier_form);
			$admin_calendrier_form=str_replace('!!localisation!!',' ',$admin_calendrier_form);
			$admin_calendrier_form=str_replace('!!book_location_id!!','',$admin_calendrier_form);
		}

		$liste_input_loc_ids=array();
		$result=pmb_mysql_query("select idlocation, location_libelle from docs_location where idlocation not in($loc)");
		$duplicate_form = '';
		while ($row = pmb_mysql_fetch_object($result)) {
			$duplicate_form .= "<input id='dup_".$row->idlocation."'type='checkbox' name='duplicate_locs[]' value='".$row->idlocation."' /><label class='etiquette' for='dup_".$row->idlocation."'>".$row->location_libelle."</label>";
			$liste_input_loc_ids[] = "dup_".$row->idlocation;
		}
		if (trim($duplicate_form)) {
			$duplicate_form .= "&nbsp;<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_input_loc_ids)."\",1);'>
				<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_input_loc_ids)."\",0);'>";
		}
		$admin_calendrier_form = str_replace("!!duplicate_location!!", $duplicate_form, $admin_calendrier_form);
		echo pmb_bidi($admin_calendrier_form) ;
		if ( empty($annee) ) {
			if ( empty($date) ) {
				$rqt_date = "select date_format(CURDATE(),'%Y') as annee ";
				$resultatdate=pmb_mysql_query($rqt_date);
				$resdate=pmb_mysql_fetch_object($resultatdate);
				$annee = $resdate->annee ;
			} else {
			    $annee = substr($date, 0,4);
			}
		}
		$gg = '<img src="'.get_url_icon('gg.gif').'" style="border:0px" title="'.$msg["calendrier_annee_prececente"].'">';
		$dd = '<img src="'.get_url_icon('dd.gif').'" style="border:0px" title="'.$msg["calendrier_annee_suivante"].'">';

		echo "<div class='colonne3'><A href='".$base_url."&annee=".($annee-1)."&loc=".$loc."' >".$gg."</A></div><div class='colonne3 align_right'><A href='".$base_url."&annee=".($annee+1)."&loc=".$loc."'>".$dd."</A></div>\n";

		echo "<div id='calendrier_tab' style='width:99%'>" ;
		for ($i=1; $i<=12; $i++) {
			$mois = substr("0".$i, -2);
			$date = $annee.$mois."01" ;
			if ($i==1 || $i==3 || $i==5 || $i==7 || $i==9 || $i==11 ) echo "<div class='row' style='padding-top: 10px'><div class='colonne3'>";
				else echo "<div class='colonne3' style='padding-left: 10px'>";
			echo pmb_bidi(admin_calendar_gestion($date, 0, $base_url, $base_url_mois,0, $loc));
			echo "</div>\n";
			if ($i==2 || $i==4 || $i==6 || $i==8 || $i==10 || $i==12 ) echo "</div>";
			}
		echo "</div>\n";
		break;
	}

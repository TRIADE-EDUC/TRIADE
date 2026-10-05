<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// © 2006 mental works / www.mental-works.com contact@mental-works.com
// 	complètement repris et corrigé par PMB Services 
// +-------------------------------------------------+
// $Id: tags.inc.php,v 1.24 2021/04/22 11:40:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $quoifaire, $page, $msg, $nb_per_page, $gestion_acces_active, $gestion_acces_user_notice, $class_path, $PMBuserid;
global $valid_id_tags, $pmb_keyword_sep, $begin_result_liste;

require_once "$class_path/notice.class.php";
require_once "$class_path/serials.class.php";

if (!isset($quoifaire)) $quoifaire = '';
if (!isset($page)) $page = 0;

// gestion des tags laisses par les lecteurs sur les notices
if (empty($nb_per_page)) {
    $nb_per_page = 10;
}

//droits d'acces utilisateur/notice (modification)
$acces_jm = '';

if ($gestion_acces_active == 1 && $gestion_acces_user_notice == 1) {
	require_once "$class_path/acces.class.php";
	$ac = new acces();
	$dom_1 = $ac->setDomain(1);
	$acces_jm = $dom_1->getJoin($PMBuserid, 8, 'num_notice');
}

//action = VALIDER le tag
switch ($quoifaire) {
	case 'valider':
	    $nb_valid_id_tags = count($valid_id_tags);
	    for ($i = 0; $i < $nb_valid_id_tags; $i++) {
			$acces_m = 1;
			if (!empty($acces_jm)) {
				$q = "select count(1) from tags $acces_jm where id_tag=".$valid_id_tags[$i];
				$r = pmb_mysql_query($q);
				if (pmb_mysql_result($r, 0, 0) == 0) {
					$acces_m = 0;
				}
			}
			if (!empty($acces_m)) {
				$requete = "select libelle, num_notice, index_l from tags left join notices on notice_id=num_notice where id_tag=".$valid_id_tags[$i];
				$r = pmb_mysql_query($requete) or die(pmb_mysql_error()." <br />$requete");
				if (pmb_mysql_num_rows($r)) {
					$loc = pmb_mysql_fetch_object($r);
					$tab_index_l = array();
					$tab_index_l = explode($pmb_keyword_sep, trim($loc->index_l));
					$tab_index_l[] = stripslashes($loc->libelle);
					$nb_tab_index_l = count($tab_index_l);
					for ($j = 0; $j < $nb_tab_index_l; $j++) {
						if (empty($tab_index_l[$j])) {
							unset($tab_index_l[$j]);
						}
					}
					$index_l = addslashes(implode($pmb_keyword_sep, $tab_index_l));
					$index_matieres = " ".strip_empty_words($index_l)." ";
					
					$requete = "update notices set index_l='$index_l',index_matieres='$index_matieres' where notice_id='$loc->num_notice'";
					pmb_mysql_query($requete) or die(pmb_mysql_error()." <br />$requete");
					indexation_stack::push($loc->num_notice, TYPE_NOTICE);
					$requete = "delete from tags where id_tag='".$valid_id_tags[$i]."'";
					pmb_mysql_query($requete) or die(pmb_mysql_error()." <br />$requete");
				}
			}
		}	
		break;
	case 'supprimer':
	    $nb_valid_id_tags = count($valid_id_tags);
	    for ($i = 0; $i < $nb_valid_id_tags; $i++) {
			$acces_m = 1;
			if (!empty($acces_jm)) {
				$q = "select count(1) from tags $acces_jm where id_tag=".$valid_id_tags[$i];
				$r = pmb_mysql_query($q);
				if (pmb_mysql_result($r, 0, 0) == 0) {
					$acces_m = 0;
				}
			}
			if (!empty($acces_m)) {
				$rqt = "delete from tags where id_tag='".$valid_id_tags[$i]."' ";
				pmb_mysql_query($rqt);
			}
		}	
		break;
	default:
		break;
}

echo "<form class='form-catalog' method='post' id='validation_tags' name='validation_tags' >
		<h3>".$msg['tag_titre_form']."</h3>
		<div class='form-contenu'>";

//variables
if (empty($page)) {
    $page = 1;
}
$debut = ($page - 1) * $nb_per_page;
$url_base = "./catalog.php?categ=tags";

//requete d'affichage des notices et des tags
$requete = "select 1 from tags ";
$r = pmb_mysql_query($requete) or die (pmb_mysql_error()." <br /><br />$requete");
$nbr_lignes = pmb_mysql_num_rows($r);

$requete = "select id_tag,libelle,num_notice,user_code,dateajout,index_l,DATE_FORMAT(dateajout,'".$msg['format_date']."') as ladate, 
			empr_login, empr_nom, empr_prenom,
			niveau_biblio, niveau_biblio, notice_id
			from tags left join empr on empr_login=user_code 
		  			left join notices on notice_id=num_notice
		  order by index_serie, tnvol, index_sew ,dateAjout desc 
		  limit $debut,$nb_per_page  ";

$r = pmb_mysql_query($requete) or die (pmb_mysql_error()." <br /><br />$requete");

if (pmb_mysql_num_rows($r)) {

	$link = notice::get_pattern_link();
	$link_expl = exemplaire::get_pattern_link();
	$link_explnum = explnum::get_pattern_link();
	$link_serial = serial::get_pattern_link();
	$link_analysis = analysis::get_pattern_link();
	$link_bulletin = bulletinage::get_pattern_link();
	$link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
	
	//affichage des notices
	print $begin_result_liste;
	$res_final = "";
	$notice_id = 0;
	while ($loc = pmb_mysql_fetch_object($r)) {
		if ($notice_id != $loc->notice_id) {
		    if (!empty($notice_id)) {
		        $res_final .=  "</ul><br />";
		    }
			$notice_id = $loc->notice_id;
			$deb = 1;
			if ($loc->niveau_biblio != 's' && $loc->niveau_biblio != 'a') {
				// notice de monographie
				$display = new mono_display($loc->notice_id, 6, $link, 1, $link_expl, '', $link_explnum, 1, 0, 1, 1);
				$res_final .= pmb_bidi($display->result);
			} else {
				// on a affaire à un périodique
				$serial = new serial_display($loc->notice_id, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_serial, 0, 0, 1, 1, true, 1);
				$res_final .= pmb_bidi($serial->result);
			}
			$res_final .=  "<ul>" ;
		} else {
		    $deb = 0 ;
		}
		$res_final .=  "
			<div class='row'>
				<input type='checkbox' name='valid_id_tags[]' id='valid_id_tags[]' value='$loc->id_tag' />
				<span style='color:#00BB00'>$loc->libelle</span>, $loc->ladate $loc->empr_prenom $loc->empr_nom
			</div>";
		if (!empty($loc->index_l)) {
		    $res_final .=  "<div class='row'>".$msg['tag_deja_tags']." <b>$loc->index_l</b></div>";
		}
	}
	$res_final .=  "</ul><br />";
	print $res_final;
}

print aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true);
echo "</div>";
echo "
		<div class='row'>
			<div class='left'>
				<input type='hidden' name='quoifaire' value='' />
				<input type='button' class='bouton' name='selectionner' value='".$msg['tag_bt_selectionner']."' onClick=\"setCheckboxes('validation_tags', 'valid_id_tags', true); return false;\" />&nbsp;
				<input type='button' class='bouton' name='valider' value='".$msg['tag_bt_valider']."' onclick='this.form.quoifaire.value=\"valider\"; this.form.submit()' />
			</div>
			<div class='right'>
				<input type='button' class='bouton' name='supprimer' value='".$msg['tag_bt_supprimer']."' onclick='this.form.quoifaire.value=\"supprimer\"; this.form.submit()' />
			</div>
		</div>
		<div class='row'></div>
	</form>";
jscript_checkbox() ;



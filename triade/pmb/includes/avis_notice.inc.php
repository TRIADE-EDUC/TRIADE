<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_notice.inc.php,v 1.23.4.1 2025/04/03 14:14:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path;

require_once "$include_path/interpreter/bbcode.inc.php";
require_once "$class_path/acces.class.php";
require_once "$class_path/avis_records.class.php";
require_once "$include_path/templates/avis.tpl.php";

function avis_notice($id, $avis_quoifaire, $valid_id_avis) {
	global $msg, $charset;
	global $avis_tpl_form1;
	global $opac_avis_allow;
	global $pmb_javascript_office_editor;

	if (empty($opac_avis_allow)) {
	    return;
	}
	$id = intval($id);
	if (!empty($avis_quoifaire)) {
		switch ($avis_quoifaire) {
			case 'valider':
			    if (!empty($valid_id_avis) && is_countable($valid_id_avis)) {
    			    $nb_valid_id_avis = count($valid_id_avis);
    			    for ($i = 0; $i < $nb_valid_id_avis; $i++) {
    					if (avis_records::check_records_edit_rights($valid_id_avis[$i])) {
    						avis_records::validate($valid_id_avis[$i]);
    					}
    				}
			    }
				break;
			case 'invalider':
			    if (!empty($valid_id_avis) && is_countable($valid_id_avis)) {
    			    $nb_valid_id_avis = count($valid_id_avis);
    			    for ($i = 0; $i < $nb_valid_id_avis; $i++) {
    					if (avis_records::check_records_edit_rights($valid_id_avis[$i])) {
    						avis_records::unvalidate($valid_id_avis[$i]);
    					}
    				}
			    }
				break;
			case 'supprimer':
			    if (!empty($valid_id_avis) && is_countable($valid_id_avis)) {
    			    $nb_valid_id_avis = count($valid_id_avis);
    			    for ($i = 0; $i < $nb_valid_id_avis; $i++) {
    					if (avis_records::check_records_edit_rights($valid_id_avis[$i])) {
    						avis_records::delete($valid_id_avis[$i]);
    					}
    				}
			    }
				break;
			case 'ajouter':
				global $avis_note, $avis_sujet, $avis_commentaire;
				if (empty($avis_note)) {
				    $avis_note = "NULL";
				}
				if ($charset != "utf-8") {
				    $avis_commentaire = cp1252Toiso88591($avis_commentaire);
				}
				$sql = "insert into avis (num_empr,num_notice,type_object,note,sujet,commentaire) values ('0','$id', '1', '$avis_note','$avis_sujet','$avis_commentaire')";
				pmb_mysql_query($sql);
				break;
			default:
			    break;
		}
	}
	$aff = "";
	$req_avis = "select id_avis,note,sujet,commentaire,DATE_FORMAT(dateajout,'".$msg['format_date']."') as ladate,empr_login,empr_nom, empr_prenom, valide
		from avis left join empr on id_empr=num_empr where avis_private = 0 and num_notice='$id' order by avis_rank, dateajout desc";
	$r = pmb_mysql_query($req_avis);
	$nb_avis = 0;
	$nb_avis = pmb_mysql_num_rows($r);
	$aff = "
		<script type='text/javascript'>
            pmb_include('./javascript/tablist.js');
            pmb_include('./javascript/dyn_form.js');
            pmb_include('./javascript/http_request.js');
            pmb_include('./javascript/bbcode.js');
            pmb_include('./javascript/avis_drop.js');
            
			function setCheckboxes(the_form, the_objet, do_check) {
				var elts = document.forms[the_form].elements[the_objet+'[]'] ;
				var elts_cnt  = (typeof(elts.length) != 'undefined')
		                  ? elts.length
		                  : 0;
				if (elts_cnt) {
					for (var i = 0; i < elts_cnt; i++) {
						elts[i].checked = do_check;
						} // end for
					} else {
						elts.checked = do_check;
						} // end if... else
				return true;
			}
		</script>

		<form class='form-catalog' method='post' id='validation_avis_$id' name='validation_avis_$id' >";
	$i = 0;
	
	while($loc = pmb_mysql_fetch_object($r)) {
		if (!empty($pmb_javascript_office_editor)) {
			$office_editor_cmd = " if (typeof(tinyMCE) != 'undefined') tinyMCE_execCommand('mceAddControl', true, 'avis_desc_$loc->id_avis'); ";
		} else {
			$office_editor_cmd = "";
		}
		$avis_notice = "<div id='avis_$loc->id_avis' onclick=\" make_form('$loc->id_avis');$office_editor_cmd\">";
		$avis_notice .= avis_records::get_display_review($loc);
		$avis_notice .= "</div><div id='update_$loc->id_avis'></div><br />";
	
		//Drag pour tri
		$id_elt = $loc->id_avis;
		$drag_avis= "<div id=\"drag_$id_elt\" handler=\"handle_$id_elt\" dragtype='avisdrop' draggable='yes' recepttype='avisdrop' id_avis='$id_elt'
			recept='yes' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='".htmlentities($loc->sujet, ENT_QUOTES, $charset)."' downlight=\"avis_downlight\" highlight=\"avis_highlight\"
			order='$i' style='' >

			<span id=\"handle_$id_elt\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>";

		$aff.= $drag_avis.$avis_notice."</div>";
		$i++;

	}
	$avis_tpl_form = $avis_tpl_form1;
	$avis_tpl_form = str_replace("!!notice_id!!", $id, $avis_tpl_form);
	$add_avis_onclick = "show_add_avis($id);";
	$aff .= "$avis_tpl_form
		<div class='row'>
			<div class='left'>
				<input type='hidden' name='avis_quoifaire' value='' />
				<input type='button' class='bouton' name='selectionner' value='".$msg['avis_bt_selectionner']."' onClick=\"setCheckboxes('validation_avis_$id', 'valid_id_avis', true); return false;\" />&nbsp;
				<input type='button' class='bouton' name='valider' value='".$msg['avis_bt_valider']."' onclick='this.form.avis_quoifaire.value=\"valider\"; this.form.submit()' />&nbsp;
				<input type='button' class='bouton' name='invalider' value='".$msg['avis_bt_invalider']."' onclick='this.form.avis_quoifaire.value=\"invalider\"; this.form.submit()' />&nbsp;
				<input type='button' class='bouton' name='ajouter' value='".$msg['avis_bt_ajouter']."' onclick='$add_avis_onclick' />&nbsp;
			</div>
			<div class='right'>
				<input type='button' class='bouton' name='supprimer' value='".$msg['avis_bt_supprimer']."' onclick='this.form.avis_quoifaire.value=\"supprimer\"; this.form.submit()' />&nbsp;
			</div>
		</div>
		<div class='row'></div>
	</form>";
	if (!empty($avis_quoifaire)) {
	    $deplier = 1;
	} else {
	    $deplier = 0;
	}
	$aff = gen_plus("plus_avis_notice_$id",$msg["avis_notice_titre"]." ($nb_avis)", $aff, $deplier, '', "recalc_recept();");
	return $aff;
}
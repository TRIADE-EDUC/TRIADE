<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: integre.inc.php,v 1.30.4.1 2025/03/05 07:56:33 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $z3950_import_modele, $base_path, $msg, $notice_id, $page, $force, $action, $item, $infos, $signature, $pmb_notice_controle_doublons;
global $charset, $class_path, $current_module, $force_url, $nb_per_page_search, $maxAffiche, $enCours, $records, $notice_display, $url_view;
global $retour, $ret, $notice_id_info, $integrate, $form, $javascript_path;

//Recherche de la fonction auxiliaire d'intégration
if ($z3950_import_modele) {
	if (file_exists($base_path."/catalog/z3950/".$z3950_import_modele)) {
		require_once($base_path."/catalog/z3950/".$z3950_import_modele);
	} else {
		error_message("", sprintf($msg["admin_error_file_import_modele_z3950"],$z3950_import_modele), 1, "./admin.php?categ=param&form_type_param=z3950&form_sstype_param=import_modele#justmodified");
		exit;
	}
} else require_once($base_path."/catalog/z3950/func_other.inc.php");

if (!isset($notice_id)) $notice_id = 0;
if (!isset($page)) $page = 0;
if (!isset($force)) $force = 0;

require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');
switch ($action) {
	case "record":
		if($item) {
			$infos = entrepot_to_unimarc($item);
		}
		//on regarde si la signature existe déjà..;
		$signature = "";
		if(!$force){
			if($pmb_notice_controle_doublons != 0){
				$sign = new notice_doublon(true);
				$signature = $sign->gen_signature();
				$requete="select signature, niveau_biblio ,niveau_hierar ,notice_id from notices where signature='$signature'";
				$result = pmb_mysql_query($requete);
				if ($dbls=pmb_mysql_num_rows($result)) {
					//affichage de l'erreur, en passant tous les param postes (serialise) pour l'eventuel forcage
					$tab=new stdClass();
					$tab->POST = $_POST;
					$tab->GET = $_GET;
					$force_url= htmlentities(serialize($tab), ENT_QUOTES,$charset);
					require_once("$class_path/mono_display.class.php");

					print "
					<script type='text/javascript' src='./javascript/tablist.js'></script>
					".return_error_message('', $msg["gen_signature_erreur_similaire"])."
					<div class='row'>
						<form class='form-$current_module' name='dummy'  method='post' action='./catalog.php?categ=search&mode=7&sub=integre&action=record&item=$item&force=1'>
							<input type='hidden' name='forcage' value='1' />
							<input type='hidden' name='signature' value='$signature' />
							<input type='hidden' name='force_url' value='$force_url' />
							<input type='hidden' name='serialized_search' value='".htmlentities($sc->serialize_search(),ENT_QUOTES,$charset)."'/>
							<input type='button' name='ok' class='bouton' value='".$msg["connecteurs_back_to_list"]."' onClick='document.forms.dummy.action = \"./catalog.php?categ=search&mode=7&sub=launch\";document.forms.dummy.submit(); ' />
							<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES,$charset)." ' />
						</form>
					</div>
					";
					if($dbls<$nb_per_page_search){
						$maxAffiche=$dbls;
						echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$dbls,$dbls)."</strong></div>";
					}else{
						$maxAffiche=$nb_per_page_search;
						echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$maxAffiche,$dbls)."</strong></div>";
					}
					$enCours=1;
					while($enCours<=$maxAffiche){
						$r=pmb_mysql_fetch_object($result);
						$records = array($r->notice_id);
						$elements_records_list_ui = new elements_records_list_ui($records, count($records), false);
						$notice_display = $elements_records_list_ui->get_elements_list();

						echo "
						<div class='row'>
						$notice_display
				 	    </div>
						<script type='text/javascript'>
							document.getElementById('el".$r->notice_id."Child').setAttribute('startOpen','Yes');
							document.forms['dummy'].elements['ok'].focus();
						</script>";
						$enCours++;
					}
					exit();
				}
			}
		}else{
			$tab = unserialize(stripslashes($force_url));

			if ( !isset($tab->GET) || !is_array($tab->GET)) {
			    $tab->GET = [];
			}
			foreach($tab->GET as $key => $val){
				add_sl($val);
				$GLOBALS[$key] = $val;
			}

			if ( !isset($tab->POST) || !is_array($tab->POST)) {
			    $tab->POST = [];
			}
			foreach($tab->POST as $key => $val){
				add_sl($val);
				$GLOBALS[$key] = $val;
			}
		}

		//on intègre...
		$z=new z3950_notice("form");
		//on reporte la signature de la notice calculée ou non...
		$z->signature = $signature;
		if($infos['notice']) $z->notice = $infos['notice'];
		if($infos['source_id']) $z->source_id = $infos['source_id'];

		if ($notice_id) {
			$ret = $z->update_in_database($notice_id);
		} else {
			$ret = $z->insert_in_database();
		}

		//on conserve la trace de l'origine de la notice...
		$id_notice = intval($ret[1]);
		$rqt = "select recid from external_count where rid = '".addslashes($item)."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)) {
		    $recid = pmb_mysql_result($res,0,0);
		}
		if($id_notice && $recid) {
		    $req= "insert into notices_externes set num_notice = '".$id_notice."', recid = '".addslashes($recid)."'";
    		pmb_mysql_query($req);
		}
		if ($ret[0]) {
			if($z->bull_id && $z->perio_id){
				$notice_display=new serial_display($ret[1],6);
			} else $notice_display=new mono_display($ret[1],6);
			$retour = "
			<script src='javascript/tablist.js'></script>
			<br /><div class='erreur'></div>
			<div class='row'>
				<div class='colonne10'>
					<img src='".get_url_icon('tick.gif')."' class='align_left'>
				</div>
				<div class='colonne80'>
					<strong>".($notice_id ? $msg["notice_connecteur_remplaced_ok"] : $msg["z3950_integr_not_ok"])."</strong>
					".$notice_display->result."
				</div>
			</div>";
			if($z->bull_id && $z->perio_id) $url_view = analysis::get_permalink($ret[1], $z->bull_id);
			else $url_view = notice::get_permalink($ret[1]);
			$retour .= "
				<div class='row'>
				<form class='form-$current_module' name='dummy' method=\"post\" action=\"catalog.php?categ=search&mode=7&sub=launch\">
					<input type='hidden' name='serialized_search' value='".htmlentities($sc->serialize_search(),ENT_QUOTES,$charset)."'/>
					<input type='submit' name='ok' class='bouton' value='".$msg["connecteurs_back_to_list"]."' />&nbsp;
					<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"document.location='$url_view'\"/>
				</form>
				<script type='text/javascript'>
					document.forms['dummy'].elements['ok'].focus();
				</script>
				</div>
			";
			print $retour;
		} else if ($ret[1]){
			if($z->bull_id && $z->perio_id){
				$notice_display=new serial_display($ret[1],6);
			} else $notice_display=new mono_display($ret[1],6);
			$retour = "
			<script src='javascript/tablist.js'></script>
			<br /><div class='erreur'>$msg[540]</div>
			<div class='row'>
				<div class='colonne10'>
					<img src='".get_url_icon('error.gif')."' class='align_left'>
				</div>
				<div class='colonne80'>
					<strong>".($msg["z3950_integr_not_existait"])."</strong><br /><br />
					".$notice_display->result."
				</div>
			</div>";
			if($z->bull_id && $z->perio_id) $url_view = analysis::get_permalink($ret[1], $z->bull_id);
			else $url_view = notice::get_permalink($ret[1]);
			$retour .= "
			<div class='row'>
			<form class='form-$current_module' name='dummy' method=\"post\" action=\"catalog.php?categ=search&mode=7&sub=launch\">
				<input type='hidden' name='serialized_search' value='".htmlentities($sc->serialize_search(),ENT_QUOTES,$charset)."'/>
				<input type='hidden' name='page' value='".htmlentities($page,ENT_QUOTES,$charset)."'/>
				<input type='submit' name='ok' class='bouton' value='".$msg["connecteurs_back_to_list"]."' />&nbsp;
				<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"document.location='$url_view'\"/>
			</form>
			<script type='text/javascript'>
				document.forms['dummy'].elements['ok'].focus();
			</script>
			</div>
			";
			print $retour;
		}
		else {
			$retour = "<script src='javascript/tablist.js'></script>";
			$retour .= form_error_message($msg["connecteurs_cant_integrate_title"], ($ret[1]?$msg["z3950_integr_not_existait"]:$msg["z3950_integr_not_newrate"]), $msg["connecteurs_back_to_list"], "catalog.php?categ=search&mode=7&sub=launch",array("serialized_search"=>$sc->serialize_search()));
			print $retour;
		}
		break;
	default:
		if ($notice_id)
			$notice_id_info = "&notice_id=".$notice_id;
		else
			$notice_id_info = "";

			//Construction de la notice UNIMARC
		$infos=entrepot_to_unimarc($item);
		if ($infos['notice']) {
			//regardons si on ne l'a pas déjà traité
			$rqt = "select recid from external_count where rid = '$item'";
			$res = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res)) {
			    $recid = pmb_mysql_result($res,0,0);
			}
			$req = "select num_notice from notices_externes where recid like '".addslashes($recid)."'";
			$res = pmb_mysql_query($req);
			if(pmb_mysql_num_rows($res)){
			    $id_notice = pmb_mysql_result($res,0,0);
			    if($id_notice) {
    				$integrate = true;
    				$requete = "SELECT * FROM notices where notice_id = '".$id_notice."'";
    				$result = pmb_mysql_query($requete);
    				if(pmb_mysql_num_rows($result)){
    					$notice = pmb_mysql_fetch_object($result);
    					$records = array($notice->notice_id);
    					$elements_records_list_ui = new elements_records_list_ui($records, count($records), false);
    					$notice_display = $elements_records_list_ui->get_elements_list();
    					$notice_id = $id_notice;
    				} else {
    					$notice_id = 0;
    				}
			    } else {
			        //Suppression de la ligne SQL avec le num_notice à 0
			        pmb_mysql_query("DELETE FROM notices_externes WHERE recid LIKE '".addslashes($recid)."' AND num_notice = 0");
			        $integrate = false;
			        $notice_id = 0;
			    }
			} else {
				$integrate = false;
			}

			if($integrate == false || $force == 1) {
				$z=new z3950_notice("unimarc",$infos['notice'],$infos['source_id']);
				$z->libelle_form = $notice_id ? $msg['notice_connecteur_remplace_catal'] : '';
				$entity_locking = new entity_locking($notice_id, TYPE_NOTICE);
				if($z->bibliographic_level == "a" && $z->hierarchic_level=="2"){
				    if(!$entity_locking->is_locked()){
				    	$form=$z->get_form("catalog.php?categ=search&mode=7&sub=integre&action=record".$notice_id_info."&item=$item".($force ? "&force=".$force : ''),0,'button',true);
				    }else{
				        $form = $entity_locking->get_locked_form();
				    }
				} else{
					if(!$entity_locking->is_locked()){
						$form=$z->get_form("catalog.php?categ=search&mode=7&sub=integre&action=record".$notice_id_info."&item=$item".($force ? "&force=".$force : ''),0,'button');
					}else{
					    $form = $entity_locking->get_locked_form();
					}
				}
				if ($notice_id) {
					$form=str_replace("<!--!!form_title!!-->","<h3>".sprintf($msg["notice_replace_external_action"],$notice_id, $item)."</h3>",$form);
				}
				else
					$form=str_replace("<!--!!form_title!!-->","<h3>".sprintf($msg["connecteurs_integrate"],$item)."</h3>",$form);
				$form=str_replace("<!--form_suite-->","<input type='hidden' name='serialized_search' value='".htmlentities($sc->serialize_search(),ENT_QUOTES,$charset)."'/><input type='hidden' name='page' value='".htmlentities($page,ENT_QUOTES,$charset)."'/>",$form);
				print $form;
			}else{
				$tab=new stdClass();
				$tab->POST = $_POST;
				$tab->GET = $_GET;
				$force_url= htmlentities(serialize($tab), ENT_QUOTES,$charset);

				if ($notice_id)
					$notice_id_info = "&notice_id=".$notice_id;
				else
					$notice_id_info = "";

				print "<br />";
				print return_error_message('', $msg['external_notice_already_integrate']);
				print "<div class='row'>$notice_display</div>
					<script src='$javascript_path/tablist.js'></script>
					<div class='row'>
						<form class='form-$current_module' name='dummy' method='post' action='./catalog.php?categ=search&mode=7&sub=integre".$notice_id_info."&item=$item&force=1'>
							<input type='hidden' name='serialized_search' value='".htmlentities($sc->serialize_search(),ENT_QUOTES,$charset)."'/>
							<input type='button' name='ok' class='bouton' value=\" ".$msg['external_integrate_back']." \" onClick='history.go(-1);'>
							<input type='submit' name='force_button' class='bouton' value=\" ".$msg['external_force_integration']." \">
						</form>
						<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>
					</div>
				</div>";
			}
		} else {
			error_message_history($msg["connecteurs_unable_to_convert_title"], $msg["connecteurs_unable_to_convert"], 1);
		}
		break;
}
?>

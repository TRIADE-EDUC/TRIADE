<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: integre_notices.inc.php,v 1.20 2023/09/06 07:01:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $z3950_import_modele, $base_path, $msg, $current_module, $serialized_search, $charset, $page, $external_notice_to_integer;
global $infos, $biblio_notice, $pmb_notice_controle_doublons, $signature, $nb_per_page_search, $maxAffiche, $notice_display, $enCours, $recid;
global $retour, $notice_id, $url_view;

require_once($class_path."/notice_doublon.class.php");
require_once($class_path."/serials.class.php");
require_once($class_path.'/elements_list/elements_records_list_ui.class.php');

//Recherche de la fonction auxiliaire d'integration
if ($z3950_import_modele) {
	if (file_exists($base_path."/catalog/z3950/".$z3950_import_modele)) {
		require_once($base_path."/catalog/z3950/".$z3950_import_modele);
	} else {
		error_message("", sprintf($msg["admin_error_file_import_modele_z3950"],$z3950_import_modele), 1, "./admin.php?categ=param&form_type_param=z3950&form_sstype_param=import_modele#justmodified");
		exit;
	}
} else require_once($base_path."/catalog/z3950/func_other.inc.php");


print "<form class='form-$current_module' name='back' method=\"post\" action=\"catalog.php?categ=search&mode=7&sub=launch\">
	<input type='hidden' name='serialized_search' value='".htmlentities(stripslashes($serialized_search),ENT_QUOTES,$charset)."'/>
	<input type='submit' name='ok' class='bouton' value='".$msg["connecteurs_back_to_list"]."' />&nbsp;
</form>
<script type='text/javascript'>
	function force_integer(ext_id){
		var ajax = new http_request();
		ajax.request('".$base_path."/ajax.php?module=catalog&categ=force_integer&item='+ext_id,true,'&serialized_search=".$sc->serialize_search()."&page=".$page."',true,integer_callback);
	}
	
	function integer_callback(response){
		data = eval('('+response+')');
		var div = document.createElement('div');
		div.setAttribute('id','notice_externe_'+data.id);
		div.innerHTML = data.html;
		document.getElementById('notice_externe_'+data.id).parentNode.replaceChild(div,document.getElementById('notice_externe_'+data.id));
	}
</script>
";
if (is_array($external_notice_to_integer) && count($external_notice_to_integer) ) {
	foreach($external_notice_to_integer as $external_notice){
		//Construction de la notice UNIMARC
		$infos=entrepot_to_unimarc($external_notice);
		$biblio_notice ="";
		if ($infos['notice']) {
			$z=new z3950_notice("unimarc",$infos['notice'],$infos['source_id']);
			if($z->bibliographic_level == "a" && $z->hierarchic_level == "2"){
				$biblio_notice = "art";
			}
			if($pmb_notice_controle_doublons != 0){
				$sign = new notice_doublon(true,$infos['source_id']);
				$signature = $sign->gen_signature($external_notice);
				$requete="select signature, niveau_biblio ,niveau_hierar ,notice_id from notices where signature='$signature'";
				$result = pmb_mysql_query($requete);
				if ($dbls=pmb_mysql_num_rows($result)) {
					//affichage de l'erreur, en passant tous les param postes (serialise) pour l'eventuel forcage 	
					require_once("$class_path/mono_display.class.php");
					print "
						<div id='notice_externe_".$external_notice."'>
						<script type='text/javascript' src='./javascript/tablist.js'></script>
						".return_error_message('', $msg["gen_signature_erreur_similaire"])."
						<div class='row'>
							<input type='button' class='bouton' onclick='force_integer(".$external_notice.")' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES,$charset)." '/>
						</div>";
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
						<script type='text/javascript'>document.getElementById('el".$r->notice_id."Child').setAttribute('startOpen','Yes');</script>
						</div>";
						$enCours++;						
					}
					continue;
				}
			}
			$z->signature = $signature;
			if($infos['notice']) $z->notice = $infos['notice'];
			if($infos['source_id']) $z->source_id = $infos['source_id'];
			$z->var_to_post();
			$ret=$z->insert_in_database();
			$id_notice = intval($ret[1]);
			$rqt = "select recid from external_count where rid = '".addslashes($external_notice)."'";
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
				} else {
				    $notice_display=new mono_display($ret[1],6);
				}
				$retour = "
				<script src='javascript/tablist.js'></script>
				<br /><div class='erreur'></div>
				<div class='row'>
					<div class='colonne10'>
						<img src='".get_url_icon('tick.gif')."' class='align_left'>
					</div>
					<div class='colonne80'>
						<strong>".(isset($notice_id) ? $msg["notice_connecteur_remplaced_ok"] : $msg["z3950_integr_not_ok"])."</strong>
						".$notice_display->result."
					</div>
				</div>";
				if($z->bull_id && $z->perio_id) $url_view = analysis::get_permalink($ret[1], $z->bull_id);
				else $url_view = notice::get_permalink($ret[1]);
				$retour .= "
					<div class='row'>
						<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"window.open('".$url_view."');\"/>
					</div>";
				print $retour;
			} else if ($ret[1]){
				if($z->bull_id && $z->perio_id){
					$notice_display=new serial_display($ret[1],6);
				} else {
				    $notice_display=new mono_display($ret[1],6);
				}
				$retour = "
				<script src='javascript/tablist.js'></script>
				<br /><div class='erreur'>$msg[540]</div>
				<div class='row'>
					<div class='colonne10'>
						<img src='".get_url_icon('tick.gif')."' class='align_left'>
					</div>
					<div class='colonne80'>
						<strong>".($msg["z3950_integr_not_existait"])."</strong><br /><br />
						".$notice_display->result."
					</div>
				</div>";
				if($z->bull_id && $z->perio_id) {
				    $url_view = analysis::get_permalink($ret[1], $z->bull_id);
				} else {
				    $url_view = notice::get_permalink($ret[1]);
				}
				$retour .= "
				<div class='row'>
					<input type='button' name='cancel' class='bouton' value='".$msg["z3950_integr_not_lavoir"]."' onClick=\"window.open('".$url_view."');\"/>
				</div>
				<script type='text/javascript'>
					document.forms['dummy'].elements['ok'].focus();
				</script>
				</div>
				";
				print $retour;
			} else {
				$retour = "<script src='javascript/tablist.js'></script>";
				$retour .= form_error_message($msg["connecteurs_cant_integrate_title"], ($ret[1]?$msg["z3950_integr_not_existait"]:$msg["z3950_integr_not_newrate"]), $msg["connecteurs_back_to_list"], "catalog.php?categ=search&mode=7&sub=launch",array("serialized_search"=>$sc->serialize_search()));
				print $retour;
			}		
			
		}
	}
}
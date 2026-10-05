<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reset.inc.php,v 1.19 2021/07/30 14:43:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $sub, $msg, $database_window_title;
global $deflt_docs_location, $deflt_docs_statut;
global $f_ex_location, $f_ex_statut, $f_reset_resa, $form_cb_expl, $transferts_reset_OK;

require_once ("$include_path/expl_info.inc.php");
require_once($class_path."/mono_display_expl.class.php");

// Titre de la fenêtre
echo window_title($database_window_title.$msg['transferts_circ_menu_reset'].$msg[1003].$msg[1001]);

//creation de l'objet transfert
$obj_transfert = new transfert();
$form=do_cb_expl('', $msg[661], $msg['transferts_circ_reset_exemplaire'], "./circ.php?categ=trans&sub=".$sub);

if(!$f_ex_location)$f_ex_location=$deflt_docs_location;
if(!$f_ex_statut)$f_ex_statut=$deflt_docs_statut;

$formlocid="f_ex_section".$f_ex_location;
if(!isset(${$formlocid})) $expl_section = '';
else $expl_section = ${$formlocid};

$checked_reset_resa = '';
if($f_reset_resa){
	$checked_reset_resa = " checked='checked' ";
}	

$form_suite="
	<script type='text/javascript'>
		function calcule_section(selectBox) {
			for (var i=0; i<selectBox.options.length; i++) {
				var id=selectBox.options[i].value;
			    var list=document.getElementById(\"docloc_section\"+id);
			    if(list)list.style.display=\"none\";
			}	
			var id=selectBox.options[selectBox.selectedIndex].value;
			var list=document.getElementById(\"docloc_section\"+id);
			if(list) list.style.display=\"block\";
		}
	</script>
	<div class='row'>
		<label class='f_ex_location' for='form_cb_expl'>$msg[298]</label>
	</div>
	<div class='row'>
		".gen_liste ("select distinct idlocation, location_libelle from docs_location order by location_libelle", "idlocation", "location_libelle", 'f_ex_location', "calcule_section(this);", $f_ex_location, "", "","","",0)."
	</div>	
	<div class='row'>
		<label class='etiquette' for='f_ex_section'>$msg[295]</label>
	</div>
	<div class='row'>";
$expl = new exemplaire();
$form_suite.=$expl->do_selector(true,$expl_section);
$form_suite.="	</div>
	<div class='row'>
		<label class='etiquette' for='f_ex_statut'>$msg[297]</label>
	</div>
	<div class='row'>
		".do_selector('docs_statut', 'f_ex_statut',$f_ex_statut)."
	</div>
	<div class='row'>
		<label class='etiquette' for='f_ex_statut'>".$msg['transfert_reset_resa']."</label><input name='f_reset_resa' type='checkbox' ".$checked_reset_resa." value='1'>
	</div><hr />";

print str_replace('<!-- !!before!! -->', $form_suite,	$form);

//si cb
if ($form_cb_expl != "") {
	
	$query = "select * from exemplaires where expl_cb='".$form_cb_expl."' ";	
	$result = pmb_mysql_query($query);
	$expl_info = pmb_mysql_fetch_object($result);
	if($expl_info->expl_id) {
		// Reset des transferts en cours
		$rqt = "UPDATE transferts,transferts_demande, exemplaires set etat_transfert=1, etat_demande=7							
				WHERE id_transfert=num_transfert and num_expl=expl_id  and etat_transfert=0 AND expl_cb='".$form_cb_expl."' " ;
		pmb_mysql_query( $rqt );
		
		//on met à jour la localisation de expl avec celle de l'utilisateur
		$rqt = "UPDATE exemplaires 
				SET expl_location=".$f_ex_location.", transfert_location_origine =".$f_ex_location.",  
				expl_statut=".$f_ex_statut.", transfert_statut_origine =".$f_ex_statut." 
				WHERE expl_cb='".$form_cb_expl."' " ;
		pmb_mysql_query( $rqt );
		if ($expl_section) {
			$rqt = "UPDATE exemplaires SET expl_section=".$expl_section.", transfert_section_origine =".$expl_section." WHERE expl_cb='".$form_cb_expl."'";
			pmb_mysql_query( $rqt );
		}
				
		$rqt = "DELETE FROM transferts_source WHERE trans_source_numexpl=".$expl_info->expl_id ;
		pmb_mysql_query( $rqt );		
		$rqt = "insert transferts_source SET trans_source_numloc=".$f_ex_location." , trans_source_numexpl=".$expl_info->expl_id;
		pmb_mysql_query( $rqt );

		// Suppression doc à ranger
		$rqt = "delete from resa_ranger where resa_cb='".$form_cb_expl."' ";
		pmb_mysql_query($rqt) ;

		// Suppression doc à traiter
		$rqt = "UPDATE exemplaires set expl_retloc=0 where expl_cb='".$form_cb_expl."' limit 1 ";
		pmb_mysql_query($rqt);
		
		if($f_reset_resa){
			// Suppression resa
			$rqt = "delete from resa where resa_cb='".$form_cb_expl."' " ;
			pmb_mysql_query($rqt) ;				
		}
		// le reset est fait

		$expl = new mono_display_expl($form_cb_expl,0 ,0);
		$aff=str_replace("!!cb_expl!!",  $expl->header,$transferts_reset_OK);
		echo str_replace("!!new_location!!", $obj_transfert->new_location_libelle,$aff);
		
		$stuff = get_expl_info($expl_info->expl_id);
		$stuff = check_pret($stuff);
		print print_info($stuff,1,1,0);
	}else{
		// cb inconnu
		print "<strong>".$form_cb_expl." : ".$msg[367]."</strong>";
	}	
} 




?>
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.8.8.1 2024/09/03 13:33:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $empr_grille_categ, $empr_grille_location, $sub, $empr_grille_format;
global $pmb_lecteurs_localises, $backbones;

$empr_grille_categ = intval($empr_grille_categ);
$empr_grille_location = intval($empr_grille_location);
$backbones = intval($backbones);

//champs persos
$nb_cp=0;
$q_cp = "select name, idchamp from empr_custom order by ordre ";
$r_cp = pmb_mysql_query($q_cp);
if (!pmb_mysql_errno()) {
	$nb_cp = pmb_mysql_num_rows($r_cp);
}

$empr_grille_default="
<formpage relative='yes'>
  <etirable id='g0' visible='yes' />
  <etirable id='g1' visible='yes' />
  <etirable id='g2' visible='yes' />
  <etirable id='g3' visible='yes' />
  <etirable id='g4' visible='yes' />
  <etirable id='g5' visible='yes' />";
if ($nb_cp) {
  	$empr_grille_default.= "	
  <etirable id='g6' visible='yes' />";
}
$empr_grille_default.= "  
  <etirable id='g7' visible='yes' />
		
  <movable id='g0_r0_f0' visible='yes' parent='g0' width='33%'/>
  <movable id='g0_r0_f1' visible='yes' parent='g0' width='33%'/>
  <movable id='g0_r0_f2' visible='yes' parent='g0'/>
  <movable id='g0_r0_f3' visible='yes' parent='g0'/>
  <movable id='g0_r1_f0' visible='yes' parent='g0' width='50%'/>
  <movable id='g0_r1_f1' visible='yes' parent='g0' width='10%'/>
  <movable id='g0_r1_f2' visible='yes' parent='g0'/>
  <movable id='g0_r2_f0' visible='yes' parent='g0' width='50%'/>
  <movable id='g0_r2_f1' visible='yes' parent='g0'/>
  <movable id='g0_r3_f0' visible='yes' parent='g0' width='25%'/>
  <movable id='g0_r3_f1' visible='yes' parent='g0' width='25%'/>
  <movable id='g0_r3_f2' visible='yes' parent='g0'/>
  <movable id='g1_r0_f0' visible='yes' parent='g1' width='25%'/>
  <movable id='g1_r0_f1' visible='yes' parent='g1' width='25%'/>
  <movable id='g1_r0_f2' visible='yes' parent='g1'/>
  <movable id='g2_r0_f0' visible='yes' parent='g2' width='25%'/>
  <movable id='g2_r0_f1' visible='yes' parent='g2' width='25%'/>
  <movable id='g2_r0_f2' visible='yes' parent='g2'/>
  <movable id='g2_r1_f0' visible='yes' parent='g2'/>
  <movable id='g3_r0_f0' visible='yes' parent='g3' width='25%'/>
  <movable id='g3_r0_f1' visible='yes' parent='g3' width='25%'/>
  <movable id='g3_r0_f2' visible='yes' parent='g3'/>
  <movable id='g4_r0_f0' visible='yes' parent='g4' width='25%'/>
  <movable id='g4_r0_f1' visible='yes' parent='g4' width='25%'/>
  <movable id='g4_r0_f2' visible='yes' parent='g4'/>
  <movable id='g4_r0_f3' visible='yes' parent='g4'/>
  <movable id='g4_r1_f0' visible='yes' parent='g4'/>
  <movable id='g5_r0_f0' visible='yes' parent='g5'/>";
if ($nb_cp) {
	while ($champ=pmb_mysql_fetch_object($r_cp))
		$empr_grille_default.="  <movable id='g6_r0_f".$champ->idchamp."' visible='yes' parent='g6'/>\n";
}
$empr_grille_default.= "  
</formpage>";

switch ($sub) {
	
	case 'get_default_empr_grille' :
		ajax_http_send_response($empr_grille_default,"text/xml");
		break;
		
	case 'get_empr_grille' :
		//format pour la categorie et la localisation demandee ? 
		$q = "select empr_grille_format from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='".$empr_grille_location."' ";
		$r = pmb_mysql_query($q);
		if (!pmb_mysql_error() && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format pour la categorie demandee et toutes les localisations ?
		$q="select empr_grille_format from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='0' ";
		$r = pmb_mysql_query($q);
		if (!pmb_mysql_error() && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format pour la localisation demandee et toutes les categories ?
		$q="select empr_grille_format from empr_grilles where empr_grille_categ='0' and empr_grille_location='".$empr_grille_location."' ";
		$r = pmb_mysql_query($q);
		if (!pmb_mysql_error() && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format pour toutes les localisations et toutes les categories
		$q="select empr_grille_format from empr_grilles where empr_grille_categ='0' and empr_grille_location='0' ";
		$r = pmb_mysql_query($q);
		if (!pmb_mysql_error() && pmb_mysql_num_rows($r)) {
			$row=pmb_mysql_fetch_object($r);
			ajax_http_send_response($row->empr_grille_format,"text/xml");
			break;
		}
		//format par defaut
		ajax_http_send_response($empr_grille_default,"text/xml");
		break;
	
	case 'set_empr_grille' :
	    //Applique-t-on la modification aux autres grilles ?
	    if ($backbones) {
	        if(empty($empr_grille_categ) && empty($empr_grille_location)) {
	            pmb_mysql_query("delete from empr_grilles");
	        } elseif(empty($empr_grille_categ)) {
	            pmb_mysql_query("delete from empr_grilles where empr_grille_location='".$empr_grille_location."'");
	            //Lecteurs non localises, on efface les traces du 0 car on transmet la localisation identifiant = 1
	            if (!$pmb_lecteurs_localises) {
	                pmb_mysql_query("delete from empr_grilles where empr_grille_location=0");
	            }
	        } elseif(empty($empr_grille_location)) {
	            pmb_mysql_query("delete from empr_grilles where empr_grille_categ='".$empr_grille_categ."'");
	        } else {
	            pmb_mysql_query("delete from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='".$empr_grille_location."'");
	        }
	    } else {
	        pmb_mysql_query("delete from empr_grilles where empr_grille_categ='".$empr_grille_categ."' and empr_grille_location='".$empr_grille_location."'");
	    }
		$query = "insert into empr_grilles set empr_grille_categ='".$empr_grille_categ."', empr_grille_location='".$empr_grille_location."', empr_grille_format='".$empr_grille_format."'";
		pmb_mysql_query($query);
		break;
		
	default :
		break;
}		

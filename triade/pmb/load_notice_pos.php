<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: load_notice_pos.php,v 1.38 2022/01/05 08:50:28 dgoron Exp $

$base_path=".";
$base_noheader=1;
$base_nobody=1;


$base_auth = "CATALOGAGE_AUTH";
require_once("includes/init.inc.php");

header("Content-Type: text/xml");
print "<?xml version='1.0' encoding='iso-8859-1'?>\n";

global $grille_typdoc, $grille_niveau_biblio, $grille_location;
global $pmb_map_activate, $pmb_nomenclature_activate, $pmb_use_uniform_title, $deflt_docs_location;

if (!isset($grille_typdoc) || !$grille_typdoc) $grille_typdoc='a' ;
if (!isset($grille_niveau_biblio) || !$grille_niveau_biblio) $grille_niveau_biblio='m' ;

$requete = "select name from notices_custom order by ordre ";
$res = pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete");

if ($grille_niveau_biblio=='m') {
		$grille_standard="<formpage relative='yes'>
		  <etirable id='el0Child' visible='yes' order='1' />
		  <etirable id='el1Child' visible='yes' order='2' />
		  <etirable id='el2Child' visible='yes' order='3' />
		  <etirable id='el3Child' visible='yes' order='4' />
		  <etirable id='el4Child' visible='yes' order='5' />";
		  if ($pmb_map_activate) $grille_standard.="<etirable id='el14Child' visible='yes' order='6' />";
		  if ($pmb_nomenclature_activate) $grille_standard.="<etirable id='el15Child' visible='yes' order='7' />";
		$grille_standard.="<etirable id='el5Child' visible='yes' order='8' />
		  <etirable id='el6Child' visible='yes' order='9' />";
		  if ($pmb_use_uniform_title) $grille_standard.="<etirable id='el230Child' visible='yes' order='10' />";
		$grille_standard.="<etirable id='el7Child' visible='yes' order='11' />
		  <etirable id='el8Child' visible='yes' order='12' />".
		  (pmb_mysql_num_rows($res)?"<etirable id='el9Child' visible='yes' order='13' />":"")."
		  <etirable id='elonglet0Child' visible='yes' order='14' />
		  <etirable id='el11Child' visible='yes' order='15' />
		  <etirable id='el10Child' visible='yes' order='16' />
		  <movable id='el0Child_0' visible='yes' parent='el0Child'/>
		  <movable id='el0Child_1' visible='yes' parent='el0Child'/>
		  <movable id='el0Child_2' visible='yes' parent='el0Child'/>
		  <movable id='el0Child_3' visible='yes' parent='el0Child'/>
		  <movable id='el0Child_4' visible='yes' parent='el0Child'/>
		  <movable id='el1Child_0' visible='yes' parent='el1Child'/>
		  <movable id='el1Child_2' visible='yes' parent='el1Child'/>
		  <movable id='el1Child_3' visible='yes' parent='el1Child'/>
		  <movable id='el2Child_0' visible='yes' parent='el2Child'/>
		  <movable id='el2Child_1' visible='yes' parent='el2Child'/>
		  <movable id='el2Child_3' visible='yes' parent='el2Child'/>
		  <movable id='el2Child_4' visible='yes' parent='el2Child'/>
		  <movable id='el2Child_7' visible='yes' parent='el2Child'/>
		  <movable id='el3Child_0' visible='yes' parent='el3Child'/>
		  <movable id='el4Child_0' visible='yes' parent='el4Child'/>
		  <movable id='el4Child_1' visible='yes' parent='el4Child'/>
		  <movable id='el4Child_2' visible='yes' parent='el4Child'/>
		  <movable id='el4Child_3' visible='yes' parent='el4Child'/>
		  <movable id='el4Child_4' visible='yes' parent='el4Child'/>
		  <movable id='el5Child_0' visible='yes' parent='el5Child'/>
		  <movable id='el5Child_1' visible='yes' parent='el5Child'/>
		  <movable id='el5Child_2' visible='yes' parent='el5Child'/>
		  <movable id='el6Child_0' visible='yes' parent='el6Child'/>
		  <movable id='el6Child_1' visible='yes' parent='el6Child'/>
		  <movable id='el6Child_2' visible='yes' parent='el6Child'/>";
	if ($pmb_use_uniform_title)	$grille_standard.="<movable id='el230Child_0' visible='yes' parent='el230Child'/>";
		$grille_standard.="<movable id='el7Child_0' visible='yes' parent='el7Child'/>
		  <movable id='el7Child_1' visible='yes' parent='el7Child'/>
		  <movable id='el8Child_0' visible='yes' parent='el8Child'/>
		  <movable id='el8Child_1' visible='yes' parent='el8Child'/>";
		while ($champ=pmb_mysql_fetch_object($res)) 
			$grille_standard.="  <movable id='move_".$champ->name."' visible='yes' parent='el9Child'/>\n";
		$grille_standard.="
		  <movable id='el11Child_0' visible='yes' parent='el11Child'/>
		  <movable id='el10Child_4' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_0' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_7' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_1' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_2' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_11' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_6' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_3' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_9' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_10' visible='yes' parent='el10Child'/>
		</formpage>";
	}

if ($grille_niveau_biblio=='s') {
		$grille_standard="		<formpage relative='yes'>
		  <etirable id='el0Child' visible='yes' order='1' />
		  <etirable id='el1Child' visible='yes' order='2' />
		  <etirable id='el2Child' visible='yes' order='3' />
		  <etirable id='el30Child' visible='yes' order='4' />
		  <etirable id='el5Child' visible='yes' order='5' />
		  <etirable id='el6Child' visible='yes' order='6' />
		  <etirable id='el7Child' visible='yes' order='7' />
		  <etirable id='el8Child' visible='yes' order='8' />".
		  (pmb_mysql_num_rows($res)?"<etirable id='el9Child' visible='yes' order='9' />":"")."
		  <etirable id='elonglet0Child' visible='yes' order='10' />
		  <etirable id='el11Child' visible='yes' order='11' />
		  <etirable id='el10Child' visible='yes' order='12' />";
		  if ($pmb_map_activate) $grille_standard.="<etirable id='el14Child' visible='no' order='13' />";
		$grille_standard.="
		  <movable id='el0Child_0' visible='yes' parent='el0Child'/>
		  <movable id='el0Child_1' visible='yes' parent='el0Child'/>
		  <movable id='el0Child_2' visible='yes' parent='el0Child'/>
		  <movable id='el1Child_0' visible='yes' parent='el1Child'/>
		  <movable id='el1Child_2' visible='yes' parent='el1Child'/>
		  <movable id='el1Child_3' visible='yes' parent='el1Child'/>
		  <movable id='el2Child_0' visible='yes' parent='el2Child'/>
		  <movable id='el2Child_4' visible='yes' parent='el2Child'/>
		  <movable id='el2Child_7' visible='yes' parent='el2Child'/>
		  <movable id='el30Child_0' visible='yes' parent='el30Child'/>
		  <movable id='el5Child_0' visible='yes' parent='el5Child'/>
		  <movable id='el5Child_1' visible='yes' parent='el5Child'/>
		  <movable id='el5Child_2' visible='yes' parent='el5Child'/>
		  <movable id='el6Child_0' visible='yes' parent='el6Child'/>
		  <movable id='el6Child_1' visible='yes' parent='el6Child'/>
		  <movable id='el6Child_2' visible='yes' parent='el6Child'/>
		  <movable id='el7Child_0' visible='yes' parent='el7Child'/>
		  <movable id='el7Child_1' visible='yes' parent='el7Child'/>
		  <movable id='el11Child_0' visible='yes' parent='el11Child'/>
		  <movable id='el8Child_0' visible='yes' parent='el8Child'/>
		  <movable id='el8Child_1' visible='yes' parent='el8Child'/>";
		while ($champ=pmb_mysql_fetch_object($res)) 
			$grille_standard.="  <movable id='move_".$champ->name."' visible='yes' parent='el9Child'/>\n";
		$grille_standard.="  <movable id='el10Child_5' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_0' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_7' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_1' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_2' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_11' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_6' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_3' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_4' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_8' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_9' visible='yes' parent='el10Child'/>
		  <movable id='el11Child_0' visible='yes' parent='el11Child'/>
		  <movable id='el10Child_10' visible='yes' parent='el10Child'/>
		</formpage>";
	}

if ($grille_niveau_biblio=='a') {
		$grille_standard="		<formpage relative='yes'>
		  <etirable id='el0Child' visible='yes' order='1' />
		  <etirable id='el1Child' visible='yes' order='2' />
		  <etirable id='el2Child' visible='yes' order='3' />
		  <etirable id='el5Child' visible='yes' order='4' />
		  <etirable id='el6Child' visible='yes' order='5' />";
		if ($pmb_use_uniform_title) $grille_standard.="<etirable id='el230Child' visible='yes' order='6' />";
		  if ($pmb_map_activate) $grille_standard.="<etirable id='el14Child' visible='yes' order='7' />";
		$grille_standard.="
		  <etirable id='el7Child' visible='yes' order='8' />
		  <etirable id='el8Child' visible='yes' order='9' />".
		  (pmb_mysql_num_rows($res)?"<etirable id='el9Child' visible='yes' order='10' />":"")."
		  <etirable id='elonglet0Child' visible='yes' order='11' />		
		  <etirable id='el11Child' visible='yes' order='12' />
		  <etirable id='el10Child' visible='yes' order='13' />";
		$grille_standard.="
		<movable id='el0Child_0' visible='yes' parent='el0Child'/>
		<movable id='el0Child_1' visible='yes' parent='el0Child'/>
		<movable id='el0Child_2' visible='yes' parent='el0Child'/>
		<movable id='el1Child_0' visible='yes' parent='el1Child'/>
		<movable id='el1Child_2' visible='yes' parent='el1Child'/>
		<movable id='el1Child_3' visible='yes' parent='el1Child'/>
		<movable id='el2Child_0' visible='yes' parent='el2Child'/>
		<movable id='el5Child_0' visible='yes' parent='el5Child'/>
		<movable id='el5Child_1' visible='yes' parent='el5Child'/>
		<movable id='el5Child_2' visible='yes' parent='el5Child'/>
		<movable id='el6Child_0' visible='yes' parent='el6Child'/>
		<movable id='el6Child_1' visible='yes' parent='el6Child'/>
		<movable id='el6Child_2' visible='yes' parent='el6Child'/>
		<movable id='el7Child_0' visible='yes' parent='el7Child'/>
		<movable id='el7Child_1' visible='yes' parent='el7Child'/>
		<movable id='el8Child_0' visible='yes' parent='el8Child'/>
		<movable id='el8Child_1' visible='yes' parent='el8Child'/>";
		while ($champ=pmb_mysql_fetch_object($res)) 
			$grille_standard.="  <movable id='move_".$champ->name."' visible='yes' parent='el9Child'/>\n";
		$grille_standard.="  <movable id='el10Child_5' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_0' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_7' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_1' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_2' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_11' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_6' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_4' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_9' visible='yes' parent='el10Child'/>
		  <movable id='el11Child_0' visible='yes' parent='el11Child'/>";
		if ($pmb_use_uniform_title)	$grille_standard.="<movable id='el230Child_0' visible='yes' parent='el230Child'/>";
		$grille_standard.="				
		</formpage>";
	}

if ($grille_niveau_biblio=='b') {
		$grille_standard="		<formpage relative='yes'>
		  <etirable id='el3Child' visible='yes' order='3' />
		  <etirable id='el5Child' visible='yes' order='4' />
		  <etirable id='el6Child' visible='yes' order='5' />
		  <etirable id='el41Child' visible='yes' order='5' />
		  <etirable id='el7Child' visible='yes' order='6' />
		  <etirable id='el8Child' visible='yes' order='7' />".
		  (pmb_mysql_num_rows($res)?"<etirable id='el9Child' visible='yes' order='8' />":"")."
		  <etirable id='el11Child' visible='yes' order='9' />
		  <etirable id='el10Child' visible='yes' order='10' />";
		  if ($pmb_map_activate) $grille_standard.="<etirable id='el14Child' visible='no' order='11' />";
		$grille_standard.="
		<movable id='el3Child_0' visible='yes' parent='el3Child'/>
		<movable id='el5Child_0' visible='yes' parent='el5Child'/>
		<movable id='el5Child_1' visible='yes' parent='el5Child'/>
		<movable id='el5Child_2' visible='yes' parent='el5Child'/>
		<movable id='el6Child_0' visible='yes' parent='el6Child'/>
		<movable id='el6Child_1' visible='yes' parent='el6Child'/>
		<movable id='el6Child_2' visible='yes' parent='el6Child'/>
		<movable id='el41Child_0' visible='yes' parent='el41Child'/>
		<movable id='el41Child_1' visible='yes' parent='el41Child'/>
		<movable id='el41Child_2' visible='yes' parent='el41Child'/>
		<movable id='el41Child_3' visible='yes' parent='el41Child'/>
		<movable id='el41Child_4' visible='yes' parent='el41Child'/>
		<movable id='el7Child_0' visible='yes' parent='el7Child'/>
		<movable id='el7Child_1' visible='yes' parent='el7Child'/>
		<movable id='el8Child_0' visible='yes' parent='el8Child'/>
		<movable id='el8Child_1' visible='yes' parent='el8Child'/>";
		while ($champ=pmb_mysql_fetch_object($res)) 
			$grille_standard.="  <movable id='move_".$champ->name."' visible='yes' parent='el9Child'/>\n";
		$grille_standard.="  <movable id='el10Child_5' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_0' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_7' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_1' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_2' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_11' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_6' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_4' visible='yes' parent='el10Child'/>
		  <movable id='el10Child_9' visible='yes' parent='el10Child'/>
		  <movable id='el11Child_0' visible='yes' parent='el11Child'/>
		  <movable id='el10Child_10' visible='yes' parent='el10Child'/>
		</formpage>";
	}

$requete = "select grille_typdoc, grille_niveau_biblio, grille_localisation, descr_format from grilles where grille_niveau_biblio='$grille_niveau_biblio' and grille_typdoc='$grille_typdoc' ";
$res = pmb_mysql_query($requete) or die(pmb_mysql_error()."<br />$requete");
if (!isset($grille_location) || !$grille_location) $grille_location=$deflt_docs_location;
$descr_format = '';
while ($grille=pmb_mysql_fetch_object($res)) {
	if (($grille->grille_localisation==$grille_location)&&($grille->descr_format)) {
		$descr_format=$grille->descr_format;
		break;
	} else if (($grille->grille_localisation==0)&&($grille->descr_format)) {
		$descr_format=$grille->descr_format;
	}
}
if ($descr_format) {
	print $descr_format;
} else print $grille_standard;

		

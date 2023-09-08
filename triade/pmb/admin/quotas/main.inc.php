<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.15 2019-06-07 12:25:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($quota)) $quota = '';
if(!isset($elements)) $elements = '';

require_once($class_path."/quotas.class.php");

//Parse des quotas possibles
if ($sub) $qt=new quota($sub); else quota::parse_quotas();

//Génération dynamique du menu
$admin_menu_quotas = "
<h1>".$msg["admin_quotas"]." <span> !!menu_sous_rub!!</span></h1>\n
<div class=\"hmenu\">";
$menu_sous_rub = '';
for ($i=0; $i<count($_quotas_types_); $i++) {	
	if($pmb_pret_restriction_prolongation!=2 && $_quotas_types_[$i]['NAME']=='PROLONG_NMBR_QUOTA' ) continue;
	if($pmb_pret_restriction_prolongation!=2 && $_quotas_types_[$i]['NAME']=='PROLONG_TIME_QUOTA') continue;
	if(!$pmb_short_loan_management && (($_quotas_types_[$i]['NAME']=='SHORT_LOAN_TIME_QUOTA')||($_quotas_types_[$i]['NAME']=='SHORT_LOAN_NMBR_QUOTA'))) continue;
	$admin_menu_quotas.="<span".ongletSelect("categ=quotas&sub=".$_quotas_types_[$i]["ID"])."><a href='./admin.php?categ=quotas&sub=".$_quotas_types_[$i]["ID"]."'>".$_quotas_types_[$i]["SHORT_COMMENT"]."</a></span>\n";
	if ($sub==$_quotas_types_[$i]["ID"]) {
		$menu_sous_rub=$_quotas_types_[$i]["SHORT_COMMENT"];
		if ($elements) $menu_sous_rub.=" > ".$qt->get_title_by_elements_id($elements);
	}
}
$admin_menu_quotas.="
</div>
";
$admin_menu_quotas=str_replace("!!menu_sous_rub!!",$menu_sous_rub,$admin_menu_quotas);
$admin_layout = str_replace('!!menu_contextuel!!', $admin_menu_quotas, $admin_layout);

print $admin_layout;

if ($quota) 
	include("./admin/quotas/quota_test.inc.php");
else {

	switch ($sub) {
		case "":
			break;
		default:
			if (!$elements)
				include("./admin/quotas/quotas_list.inc.php");
			else
				include("./admin/quotas/quota_table.inc.php");
			break;
	}
}
?>
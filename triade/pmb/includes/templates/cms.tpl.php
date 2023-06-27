<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.tpl.php,v 1.11 2019-05-27 09:46:09 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $include_path,$msg,$charset;
global $cms_active,$cms_layout,$current_module,$cms_layout_end;


require_once "$include_path/templates/cms/cms.tpl.php";

$cms_menu = "
	<div id='menu'>";
if(SESSrights & CMS_BUILD_AUTH) {
	$cms_menu .= "
		<h3 onclick='menuHide(this,event)'>".htmlentities($msg["cms_menu_build"],ENT_QUOTES,$charset)."</h3>
		<ul>
			".($cms_active ? "<li><a href='./cms.php?categ=build&sub=block'>".htmlentities($msg["cms_menu_build_block"],ENT_QUOTES,$charset)."</a></li>" : "")."
			".($cms_active ? "<li><a href='./cms.php?categ=pages&sub=list'>".htmlentities($msg["cms_menu_pages"],ENT_QUOTES,$charset)."</a></li>" : "")."
			<li><a href='./cms.php?categ=frbr_pages&sub=list'>".htmlentities($msg["frbr_pages_menu"],ENT_QUOTES,$charset)."</a></li>
		</ul>";
}
if($cms_active) {		
	$cms_menu .= "<h3 onclick='menuHide(this,event)'>".htmlentities($msg["cms_menu_editorial"],ENT_QUOTES,$charset)."</h3>
			<ul>
				<li><a href='./cms.php?categ=editorial&sub=list'>".htmlentities($msg["cms_menu_editorial_gest"],ENT_QUOTES,$charset)."</a></li>
				<li><a href='./cms.php?categ=section&sub=edit&id=new'>".htmlentities($msg["cms_new_section_form_title"],ENT_QUOTES,$charset)."</a></li>
				<li><a href='./cms.php?categ=article&sub=edit&id=new'>".htmlentities($msg["cms_new_article_form_title"],ENT_QUOTES,$charset)."</a></li>
				<li><a href='./cms.php?categ=collection&sub='>".htmlentities($msg["cms_collections_form_title"],ENT_QUOTES,$charset)."</a></li>
			</ul>";
}
if($cms_active && (SESSrights & CMS_BUILD_AUTH)) {	
	$cms_menu .= "
		<h3 onclick='menuHide(this,event)'>".htmlentities($msg["cms_manage_module_menu"],ENT_QUOTES,$charset)."</h3>
		<ul>
			!!cms_managed_modules!!
		</ul>";
}
$plugins = plugins::get_instance();
$cms_menu.= $plugins->get_menu("cms")."
	</div>
";

$cms_layout = "<div id='conteneur' class='$current_module'>
	$cms_menu
	<div id='contenu'>
	!!menu_contextuel!!
";

$cms_layout_end = "
		</div>
	</div>
";

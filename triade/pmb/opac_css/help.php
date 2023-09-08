<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: help.php,v 1.20 2018-08-24 08:44:59 plmrozowski Exp $

$base_path="./";
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

print "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" />
	<meta name=\"author\" content=\"PMB Group\" />";
if ($charset=='utf-8') {
	print utf8_encode("	<meta name=\"keywords\" content=\"OPAC, web, libray, opensource, catalog, catalogue, bibliothèque, médiathèque, pmb, phpmybibli\" />");
} else {
	print "	<meta name=\"keywords\" content=\"OPAC, web, libray, opensource, catalog, catalogue, bibliothèque, médiathèque, pmb, phpmybibli\" />";
}
print "	<meta name=\"description\" content=\"Recherches simples dans l'OPAC de PMB\" />
	<meta name=\"robots\" content=\"all\" />
	<title>pmb : opac</title>
	<script>
	function div_show(name) {
		var z=document.getElementById(name);
		if (z.style.display==\"none\") {
			z.style.display=\"block\"; }
		else { z.style.display=\"none\"; }
		}
	</script>
	".link_styles($css)."
</head>

<body onload=\"window.defaultStatus='pmb : opac';\" id=\"help_popup\" class='popup'>
<div id='help-container'>
<p class='align_right' style=\"margin-top:4px;\"><a name='top' ></a><a href='#' onclick=\"self.close();return false\" title=\"".$msg['search_close']."\"><img src=\"".get_url_icon('close.gif')."\" alt=\"".$msg['search_close']."\" align=\"absmiddle\" style='border:0px'></a></p>

";

if (file_exists("includes/messages/".$lang."/doc_".$whatis."_subst.txt")) {
	$aide = file_get_contents("includes/messages/".$lang."/doc_".$whatis."_subst.txt");
} elseif (file_exists("includes/messages/".$lang."/doc_".$whatis.".txt")) {
	$aide = file_get_contents("includes/messages/".$lang."/doc_".$whatis.".txt");
}
if ($charset=='utf-8') {
	print utf8_encode($aide);
} else {
	print $aide;
}

print "
<p class='align_right'><a href='#top' title=\"".$msg['search_up']."\"><img src=\"images/up.gif\" alt=\"".$msg['search_up']."\" align=\"absmiddle\" style='border:0px'></a></p>
</div>
<script>self.focus();</script>";
print "</body></html>"
?>
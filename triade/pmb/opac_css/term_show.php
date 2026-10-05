<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: term_show.php,v 1.35.4.1 2025/04/07 14:53:18 dgoron Exp $
$base_path=".";                            
$base_auth = ""; 

require_once ("$base_path/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

global $class_path, $id_thes, $term, $jscript_term, $charset;

require_once("$class_path/term_show.class.php"); 

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

$id_thes = intval($id_thes);

//Récupération des paramètres du formulaire appellant
$base_query = "history=".rawurlencode(stripslashes($term))."&history_thes=".rawurlencode(stripslashes($id_thes));

// RSS
require_once($base_path."/includes/includes_rss.inc.php");
$short_header= str_replace("!!liens_rss!!","",$short_header);

//ajout de classe
$short_header= str_replace("<body>","<body class='searchTerm'>",$short_header);

echo $short_header;
if (!isset($_POST["jscript_term"]) && !isset($_GET["jscript_term"])) {
    echo $jscript_term;
}


function parent_link($categ_id,$categ_see) {
	global $charset;
	global $base_path;
	global $opac_show_empty_categ;
	global $css;
	global $msg;
	
	if ($categ_see) $categ=$categ_see; else $categ=$categ_id;
	//$tcateg =  new category($categ);
	if ($opac_show_empty_categ) 
		$visible=true;
	else
		$visible=false;
		
	if (category::has_notices($categ)) {
		$link="<a href='#' onClick=\"parent.parent.document.term_search_form.action='".$base_path."/index.php?lvl=categ_see&id=$categ&rec_history=1'; parent.parent.document.term_search_form.submit(); return false;\" title='".htmlentities($msg["categ_see_alt"],ENT_QUOTES,$charset)."'><img src='".get_url_icon('search.gif')."' style='border:0px' ></a>";
		$visible=true;	
	}
	$r=array("VISIBLE"=>$visible,"LINK"=>$link);
	return $r;
}

if ($term!="") {
	$ts=new term_show(stripslashes($term),"term_show.php",$base_query,"parent_link", 0, $id_thes);
	echo $ts->show_notice();
	echo "<script>
	parent.parent.document.term_search_form.term_click.value='".htmlentities($term,ENT_QUOTES,$charset)."';
	</script>
	";
}

print $short_footer;

?>

<?php
// +-------------------------------------------------
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: term_search.php,v 1.21 2019-05-09 10:35:37 ngantier Exp $
//
// Recherche des termes correspondants à la saisie

$base_path=".";                            
$base_auth = ""; 
$base_title="Recherche par termes";

require_once ("$base_path/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

require_once ("$class_path/term_search.class.php");

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

// RSS
require_once($base_path."/includes/includes_rss.inc.php");
$short_header= str_replace("!!liens_rss!!",genere_link_rss(),$short_header);

//ajout de classe
$short_header= str_replace("<body>","<body class='searchTerm'>",$short_header);

echo $short_header;

//Récupération des paramètres du formulaire appellant
$base_query = "";

$page+= 0;
$id_thes+= 0;
//Page en cours d'affichage
$n_per_page=$opac_term_search_n_per_page;

$ts=new term_search("user_input","f_user_input",$n_per_page,$base_query,"term_show.php","term_search.php", 0, $id_thes);
echo "<table style='width:80%'><tr><td>";
echo $ts->show_list_of_terms();
echo "</td></tr></table>";
echo "<script>
parent.parent.document.term_search_form.page_search.value='".$page."';
</script>
";

print $short_footer;
?>

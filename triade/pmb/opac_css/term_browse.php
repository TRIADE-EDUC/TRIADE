<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: term_browse.php,v 1.21 2020/08/11 15:08:18 dgoron Exp $
//
// Frames pour naviguer par terme

$base_path=".";                            
$base_auth = ""; 
require_once ($base_path.'/includes/init.inc.php');

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

global $id_thes, $page_search, $search_term, $term_click;
global $opac_term_search_height_bottom;

$id_thes = intval($id_thes);
$page_search = intval($page_search);
?>
<frameset rows="<?php echo $opac_term_search_height_bottom;?>,*">
	<frame name="term_search" src="term_search.php?user_input=<?php echo rawurlencode(stripslashes($search_term)); ?>&f_user_input=<?php echo rawurlencode(stripslashes($search_term)); if ($page_search) echo "&page=$page_search"; echo '&id_thes='.$id_thes; ?>">
	<frame name="term_show" src="<?php echo "term_show.php?term=".rawurlencode(stripslashes($term_click)); echo '&id_thes='.$id_thes;?>">
</frameset>

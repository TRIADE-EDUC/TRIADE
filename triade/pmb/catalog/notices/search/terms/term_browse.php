<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: term_browse.php,v 1.11 2019/06/07 08:05:39 btafforeau Exp $
//
// Frames pour naviguer par terme

global $base_path, $base_auth, $base_nobody, $base_query, $search_term, $page_search, $term_click, $id_thes;

$base_path="../../../..";                            
$base_auth = ""; 
$base_nobody=1;

require_once ("$base_path/includes/init.inc.php");  

$base_query = "";
?>
<frameset rows="120,*">
	<frame scrolling=auto name="term_search" src="term_search.php?user_input=<?php echo rawurlencode(stripslashes($search_term)); ?>&f_user_input=<?php echo rawurlencode(stripslashes($search_term));?>&page_search=<?php echo $page_search; ?>&term_click=<?php echo rawurlencode(stripslashes($term_click)); ?>&id_thes=<?php echo $id_thes; ?>">
	<frame name="term_show" src="term_show.php?term=<?php print rawurlencode(stripslashes($term_click)); ?>&first=1">
</frameset>

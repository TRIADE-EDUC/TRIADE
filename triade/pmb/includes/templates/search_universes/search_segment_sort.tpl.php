<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_sort.tpl.php,v 1.4 2020/08/04 11:51:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $charset, $search_segment_sort_form, $msg;

$search_segment_sort_form = "
<hr/>

<div class='row'>
	<label class='etiquette' for='segment_sort'>".$msg['search_segment_sort']."</label>
</div>
<div class='row' id='sort_fields'>
    !!segment_sort_select_fields!!
    !!segment_sort_fields_javascript!!
</div>
";
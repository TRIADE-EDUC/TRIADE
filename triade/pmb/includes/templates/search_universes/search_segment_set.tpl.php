<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_set.tpl.php,v 1.2 2019-05-27 09:16:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $charset, $search_segment_set_form, $msg;

$search_segment_set_form = "
<hr/>
<div class='row'>
    <label class='etiquette'>".htmlentities($msg['search_segment_set'],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
    <input type='hidden' name='segment_set_data_set' id='segment_set_data_set' value='!!segment_set_data_set!!'/> 
    <span id='segment_set_human_query'>!!segment_set_human_query!!</span>
    <img src='".get_url_icon('b_edit.png')."' id='edit_segment_set' data-pmb-evt='{\"class\":\"SegmentForm\", \"type\":\"click\", \"method\":\"loadSetDialog\", \"parameters\":{\"module\":\"selectors\", \"what\" : \"!!segment_type!!\", \"action\" : \"advanced_search\", \"entity_id\" : \"!!segment_id!!\", \"entity_type\" : \"segment\"}}' title=\"".htmlentities($msg['search_segment_add_set'],ENT_QUOTES,$charset)."\" />   
</div>
";

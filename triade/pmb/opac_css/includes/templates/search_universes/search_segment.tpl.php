<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.tpl.php,v 1.9 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $charset, $base_path;
global $search_segment_form;
global $search_segment_parent_universe;
global $search_segment_rebound_form;

$search_segment_parent_universe = "
	<div id='segment_universe_description' class='segment_universe_description'>
		<a href='" . $base_path . "/index.php?lvl=search_universe&id=!!segment_universe_id!!'><h4>".htmlentities($msg['search_universe_new_search'],ENT_QUOTES,$charset)."!!segment_universe_label!!</h4></a>
		<p>!!segment_universe_description!!</p>
         <form id='search_universe_input' name='search_universe_input' action='".$base_path."/index.php?lvl=search_universe&id=!!segment_universe_id!!' method='post' onSubmit=\"if (search_universe_input.user_query.value.length == 0) { search_universe_input.user_query.value='*'; return true; }\">
            <input type='text' name='user_query' id='user_query' class='text_query' value='' size='65' />
            <input type='hidden' name='universe_id' id='universe_id' value='!!segment_universe_id!!'/>
            <input type='submit' name='search_input' value='".$msg["142"]."' class='bouton'/>
        </form>
	</div>
";

$search_segment_form = "
    <div id='segment_form_container'>
        <h3 class='segment_title'>!!segment_label!!</h3>
        <div class='row' id='segment_description'>
            <p>!!segment_description!!</p>
        </div>    
        <div class='row' id='universe_query'>
            !!universe_query!!
        </div>
    </div>
";

$search_segment_rebound_form = "
    <div id='autolevel1_rebound_form'>
        <h3 class='autolevel1_title'>".htmlentities($msg['autolevel1_search'],ENT_QUOTES,$charset)."</h3>
        <div class='row'>    
            <a href='index.php?lvl=search_universe&id=!!universe_id!!'><i class='fa fa-arrow-left' aria-hidden='true'></i> ".htmlentities($msg['search_segment_back_to_universe'],ENT_QUOTES,$charset)."</a>
            <br/>
            <a href='index.php?lvl=search_segment&id=!!segment_id!!'><i class='fa fa-arrow-left' aria-hidden='true'></i> ".htmlentities($msg['search_segment_new_search'],ENT_QUOTES,$charset)."</a>
        </div>
    </div>
";
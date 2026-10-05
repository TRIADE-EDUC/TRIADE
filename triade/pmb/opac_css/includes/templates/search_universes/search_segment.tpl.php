<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.tpl.php,v 1.22 2023/11/21 14:50:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $charset, $base_path;
global $search_segment_form;
global $search_segment_parent_universe;
global $search_segment_rebound_form;
global $search_segment_universe_associate_list;
global $search_segment_universe_associate_form_row;

$search_segment_parent_universe = "
	<div id='segment_universe_description' class='segment_universe_description'>
		<a href='" . $base_path . "/index.php?lvl=search_universe&id=!!segment_universe_id!!'><h4>".htmlentities($msg['search_universe_new_search'],ENT_QUOTES,$charset)."!!segment_universe_label!!</h4></a>
		<p>!!segment_universe_description!!</p>
         <form id='search_universe_input' name='search_universe_input' action='".$base_path."/index.php?lvl=search_universe&id=!!segment_universe_id!!!!get_parameters!!' method='post' onSubmit=\"if (search_universe_input.user_query.value.length == 0) { search_universe_input.user_query.value='*'; return true; }\">
            <input type='text' name='user_query' id='user_query' class='text_query' value='' size='65' title='".htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset)."' />
            <input type='hidden' name='universe_id' id='universe_id' value='!!segment_universe_id!!'/>
            <input type='hidden' name='last_query' id='last_query' value='!!last_query!!'/>
            <input type='submit' name='search_input' value='".$msg["142"]."' class='bouton'/>
        </form>
	</div>
";

$search_segment_form = "
    <div id='segment_form_container'>
        <a href='" . $base_path . "/index.php?lvl=search_universe&id=!!segment_universe_id!!' class='universe_title_link'>
            <h3 id='universe_title' class='universe_title'>
                <img class='universe-return' src='".get_url_icon('arrow_left.png')."'/>
                !!universe_label!!
            </h3>
        </a>
        <h4 id='segment_title' class='segment_title'>!!segment_label!!</h4>
        <input type='hidden' name='last_query' id='last_query' value='!!last_query!!'/>
        <div class='row' id='segment_description'>
            <p>!!segment_description!!</p>
        </div>
        !!search_segment!!
        !!search_segment_result!!
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

$search_segment_universe_associate_list = "
    <div id='content_pane_body' style='display:none;'>
        <div id='search_universe_associate_list'>
            <ul class='search_universe_associate_list'>
                !!universes_associated_form!!
            </ul>
        </div>
        <script>
            function search_in_universe(e, id_universe) {
                var hidden_form = document.getElementById('form_hidden_universe_'+id_universe);
                if (hidden_form) {
                    event.preventDefault();
                    hidden_form.submit();
                }
            }
        </script>
    </div>
";

$search_segment_universe_associate_form_row = "
	<li class='search_universe_associate_list_row' data-universe-id='!!universe_associate_id!!'>
		<a class='search_universe_segments_cell' href='./index.php?lvl=search_universe&id=!!universe_associate_id!!' onclick='search_in_universe(event, !!universe_associate_id!!)'>
			<p class='search_segment_label'>!!universe_associate_label!!</p>
		</a>
        !!search_form_hidden!!
	</li>
";
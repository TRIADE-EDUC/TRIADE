<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universe.tpl.php,v 1.15 2019-05-29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $base_path, $msg;
global $search_universe_form;
global $search_universe_segment_list;
global $search_universe_segments_form_row;
global $search_universe_segment_logo;

$search_universe_form = "
    <div id='search_universe_container'>
        <h3 class='search_universe_title'>!!universe_label!!</h3>
    	<p class='universe_description'>!!universe_description!!</p>
        <div class='row'>
             <form id='search_universe_input' name='search_universe_input' action='".$base_path."/ajax.php?module=ajax&categ=search_universes&sub=search_universe&action=simple_search&id=!!universe_id!!' method='post' onSubmit=\"if (search_universe_input.user_query.value.length == 0) { search_universe_input.user_query.value='*'; return true; }\">
                <input type='text' name='user_query' placeholder='".$msg["autolevel1_search"]."'  id='user_query' class='text_query' value='' size='65' />
                <input type='hidden' name='universe_id' id='universe_id' value='!!universe_id!!'/>
                <input type='hidden' name='search_index' id='search_index' value='!!search_index!!'/>
                <input type='hidden' name='last_query' id='last_query' value='!!last_query!!'/>
                <input type='hidden' name='default_segment' id='default_segment' value='!!default_segment!!'/>
                <input type='submit' name='search_input' value='".$msg["142"]."' class='bouton'/>
            </form>
        </div>   
        <div class='row universe_page'>
            !!universe_segment_list!!
        </div>
        <div id='result_container' class='row'>
    
        </div>
        <script type='text/javascript'>
            require(['apps/pmb/search_universe/SearchUniverseController', 'dojo/ready'], function(SearchUniverseController, ready){
                ready(function(){
                    new SearchUniverseController();
                });
            });
        </script>
    </div>
";

$search_universe_segment_list = 
		"
      <div id='search_universe_segments_list'>
        <ul class='search_universe_segments'>
            !!universe_segments_form!!                
        </ul>
      </div>
"; 

$search_universe_segments_form_row = "
	<li class='search_universe_segments_row' !!segment_selected!! data-segment-id='!!segment_id!!' data-universe-id='!!universe_id!!'>
        <input type='hidden' value='' class='simple_search_mc' name='search_universe_simple_search_!!segment_id!!' id='search_universe_simple_search_!!segment_id!!' />
		<a class='search_universe_segments_cell' href='./index.php?lvl=search_segment&action=segment_results&id=!!segment_id!!'>
			<p class='search_segment_label'>!!segment_label!!</p>
			!!segment_logo!!
			<p class='search_segment_description'>!!segment_description!!</p>
            <p class='segment_nb_results'></p>
		</a>
	</li>
";

$search_universe_segment_logo = "<img src='!!segment_logo!!' class='search_segment_logo' alt='!!segment_logo!!'/>";
<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universe.tpl.php,v 1.26 2023/08/28 12:03:34 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $base_path, $msg;
global $search_universe_form;
global $search_universe_segment_list;
global $search_universe_segments_form_row;
global $search_universe_segment_logo;
global $search_universe_dialog_universe_associated;
global $search_form_hidden, $opac_rgaa_active;

$search_universe_form = "
    <div id='search_universe_container'>
        <h3 id='universe_title' class='search_universe_title'>!!universe_label!!</h3>
    	<p class='universe_description'>!!universe_description!!</p>
        !!search_universe_tabs!!
        <div class='row universe_page' id='search_universe_list_segments'>
            !!universe_segment_list!!
        </div>
        <div id='result_container' class='row'>
    
        </div>
        <script>
            require(['apps/pmb/search_universe/SearchUniverseController', 'dojo/ready'], function(SearchUniverseController, ready){
                ready(function(){
                    new SearchUniverseController();
                });
            });
        </script>
    </div>
";

$title_segment_list = "";
if ($opac_rgaa_active) {
    $title_segment_list = "<h2 class='search_universe_segments_list'>{$msg['search_segments']}</h2>";
}

$search_universe_segment_list = "
<div id='search_universe_segments_list'>
    {$title_segment_list}
    <ul class='search_universe_segments'>
        !!universe_segments_form!!
    </ul>
</div>
<script>
    require(['dijit/Dialog', 'dojo/domReady!'], function(Dialog) {

        var contentPaneBody = document.getElementById('content_pane_body');
        var dialog = null;
        if (contentPaneBody) {
            dialog = new Dialog({
                title: '".$msg["search_segment_universe_associate_list"]."',
                content: contentPaneBody.innerHTML
            });
        }

        showUniverseAssociated = function (event) {
            event.preventDefault();
            var listUniverseAssociated = contentPaneBody.querySelectorAll('li');
            if (listUniverseAssociated.length > 1) {
                if (dialog) {
                    dialog.show().then(function() {
                        // On evite que la div soit masque par le css
                        var nodeUnderlay = document.getElementById(dialog.id+'_underlay');
                        if (nodeUnderlay) {
                            nodeUnderlay.innerHTML = '&nbsp;';
                        }
                    });
                    dialog.containerNode.focus();
                }
            } else {
                if (listUniverseAssociated[0]) {
                    var form = listUniverseAssociated[0].querySelector('form');
                    form.submit();
                }
            }
        }
    });
    
</script>
"; 

$search_universe_segments_form_row = "
	<li class='search_universe_segments_row !!segment_selected!!' data-segment-id='!!segment_id!!' data-universe-id='!!universe_id!!' data-segment-dynamic-field='!!segment_dynamic_field!!'>
        <input type='hidden' value='' class='simple_search_mc' name='search_universe_simple_search_!!segment_id!!' id='search_universe_simple_search_!!segment_id!!' />
		<a class='search_universe_segments_cell' href='!!segment_url!!'>
			<p class='search_segment_label'>!!segment_label!!</p>
			!!segment_logo!!
			<p class='search_segment_description'>!!segment_description!!</p>
            <p class='segment_nb_results'></p>
            !!button_dialog_universe_associated!!
		</a>
	</li>
";

$search_universe_dialog_universe_associated = "
    <img src='".get_url_icon('related_searches.png')."' alt='".$msg["search_segment_universe_associate_list"]."' title='".$msg["search_segment_universe_associate_list"]."' onclick='showUniverseAssociated(event)' />
";

$search_universe_segment_logo = "<img src='!!segment_logo!!' class='search_segment_logo' alt='!!segment_logo!!'/>";

$search_form_hidden = "
    <form id='form_hidden_universe_!!universe_id!!' name='search_universe_input' style='margin: 0px;' action='".$base_path."!!url!!' method='post'>
        <input type='hidden' name='user_query' value='!!user_query!!'/>
        <input type='hidden' name='user_rmc' value='!!user_rmc!!'/>
    </form>
";
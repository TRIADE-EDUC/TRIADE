<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universe.tpl.php,v 1.20 2023/11/02 13:27:04 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $search_universe_content_form;
global $search_universe_form_definition;
global $search_universe_opac_views;
global $search_universe_segment;
global $search_universe_segments_form;
global $search_universe_tree_interface, $msg;

$search_universe_content_form ="
!!universe_id_field!!
<div class='row'>
	<label class='etiquette' for='universe_label'>".$msg['search_universe_label']."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='universe_label' id='universe_label' value='!!universe_label!!' data-translation-fieldname='universe_label' />
</div>
<div class='row'>
	<label class='etiquette' for='universe_description'>".$msg['search_universe_description']."</label>
</div>
<div class='row'>
	<textarea name='universe_description' id='universe_description' rows='5' data-translation-fieldname='universe_description'>!!universe_description!!</textarea>
</div>
<div class='row'>
	<input type='checkbox' name='universe_rmc_enabled' id='universe_rmc_enabled' value='1' !!universe_rmc_enabled!! />
	<label class='etiquette' for='universe_rmc_enabled'>{$msg['search_universe_rmc_enabled']}</label> <i class='fa fa-info-circle' title='{$msg['search_universe_rmc_explication']}'></i>
</div>
<div class='row'>
	<input type='checkbox' name='universe_perio_enabled' id='universe_perio_enabled' value='1' !!universe_perio_enabled!! />
	<label class='etiquette' for='universe_perio_enabled'>{$msg['search_universe_perio_enabled']}</label>
</div>
<div class='row'>
	<input type='checkbox' name='universe_autocomplete' id='universe_autocomplete' value='1' !!universe_autocomplete!! />
	<label class='etiquette' for='universe_autocomplete'>{$msg['search_universe_autocomplete']}</label>
</div>
!!universe_permalink_field!!
!!universe_opac_views!!
!!universe_segments_form!!

<script type='text/javascript'>        
	require(['apps/search_universes/UniverseForm'], function(UniverseForm) {
        new UniverseForm({
            id : '!!universe_id!!',
            type : 'universe',
            className : 'UniverseForm',
            formName : 'search_universe_form'
        });	
    });
</script>
";

$search_universe_form_definition = "
<div class='row'>
	<h3>
		!!area_title!!
	</h3>
</div>
<div data-dojo-id='availableEntities' data-dojo-type='dojo/store/Memory' data-dojo-props='data:!!available_entities_data!!'></div>
<div data-dojo-id='graphStore' data-dojo-type='apps/contribution_area/GraphStore' data-dojo-props='area_id:!!id!!,data:!!graph_data_store!!'></div>
		<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true,region:\"left\"' style='height:100%;width:200px;'>	
		<div data-dojo-props='region:\"center\", splitter:true' style='width:auto;height:50%;' data-dojo-type='apps/contribution_area/FormsList'></div>
		<div  data-dojo-props='region:\"top\",splitter:true' style='width:auto;height:50%' data-dojo-type='apps/contribution_area/ScenariosList'></div>
	</div>	
	<div data-dojo-type='apps/contribution_area/Graph' data-dojo-props='splitter:true,region:\"center\"' style='height:100%;width:auto;overflow:scroll;'></div>
</div>";

$search_universe_opac_views = "		
<hr/>
<div class='row'>
	<label class='etiquette'>".$msg['search_universe_opac_views']."</label>
</div>
<div class='row'>
	!!opac_views_selector!!
</div>
";

$search_universe_segment = "
<tr class=\"!!even_odd!!\" 
    style=\"cursor: pointer; text-align:center;\"
    segmentId='!!segment_id!!'
    onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!even_odd!!'\">

    <td>!!segment_label!!</td>
    <td>!!segment_type!!</td>
    <td>!!segment_logo!!</td>
</tr>
";

$search_universe_segments_form = "
	<hr/>
	<div class='row'>
		<label class='etiquette'>".$msg['search_universe_segments']."</label>
	</div>
	<div class='row'>
		<table class='universe_segments_table'>
            <thead>
                <tr>
                    <th>".$msg['search_segment_label']."</th>
                    <th>".$msg['search_segment_type']."</th>
                    <th>".$msg['search_segment_logo']."</th>
                </tr>
            </thead>
            <tbody>
			!!universe_segments!!
            </tbody>
		</table>
		<input type='button' class='bouton' id='add_segment' name='add_segment' Value='".$msg['ajouter']."' data-pmb-evt='{\"class\":\"UniverseForm\", \"type\":\"click\", \"method\":\"addSegment\", \"parameters\":{\"entity_id\" : \"!!universe_id!!\", \"entity_type\" : \"universe\"}}'/>
	</div>
	<div class='row'>
	</div>
";

$search_universe_tree_interface = "
	<div data-dojo-type='dijit/layout/TabContainer' id='frbrTabContainer' data-dojo-props='splitter:true,region:\"top\"' style='width:auto;height:50%'>
		<div data-dojo-type='apps/search_universes/TreeContainer' data-dojo-props='splitter:true, data:!!parameters!!' style='height: 800px;' title='".$msg['frbr_page_data']."'>
		</div>
	</div>	
";

/**
 * <div data-dojo-type='apps/frbr/cataloging/DatanodesUI' data-dojo-props='splitter:true, direction: \"vertical\",startExpanded: true, region:\"leading\"' style='width:20%;'></div>
		<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true, region:\"center\"' style='height:100%;width:auto;'>
			<div data-dojo-type='dijit/layout/TabContainer' data-dojo-props='splitter:true, region:\"center\"' style='width:auto;height:95%'>
				<div data-dojo-type='apps/frbr/cataloging/GraphUI' title='".$msg['frbr_cataloging_graph_title']."' data-dojo-props='splitter:true' ></div>
				<div data-dojo-type='apps/frbr/cataloging/SearchUI' data-dojo-props='id:\"frbr_search_pane\"' title='".$msg['frbr_cataloging_search_title']."'></div>
				<div data-dojo-type='apps/frbr/cataloging/AddUI' title='".$msg['create']."'></div>
			</div>
	
			<div data-dojo-type='apps/frbr/cataloging/ItemsListUI' style='width:auto;height:50%' data-dojo-props='direction: \"horizontal\", startExpanded: false, splitter:true, region:\"bottom\"' title='".$msg['frbr_cataloging_itemslist_title']."'></div>
		</div>
	
 */
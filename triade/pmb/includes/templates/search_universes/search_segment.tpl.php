<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment.tpl.php,v 1.18.2.1.2.1 2025/05/21 15:25:11 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $search_segment_content_form;
global $search_segment_form_definition;
global $search_segment_form_external_sources;
global $search_segment_form_categ_external_sources;
global $search_segment_form_categ_external_sources_line;
global $search_segment_filter_form;
global $search_segment_universes_form;
global $search_segment_universe_row;
global $charset, $msg;

$search_segment_content_form = "
!!segment_id_field!!
<div class='row'>
	<label class='etiquette' for='segment_label'>".$msg['search_segment_label']."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='segment_label' id='segment_label' value='!!segment_label!!' data-translation-fieldname='segment_label' />
</div>
<div class='row'> 
</div>
<div class='row'>
	<label class='etiquette' for='segment_icon'>".$msg['search_segment_logo']."</label>
</div>
<div class='row'>
	<div><img src='!!segment_logo!!' alt='!!segment_label!!' width='50px' height='50px'/></div>
	<div><input type='text' class='saisie-50em' name='segment_logo' id='segment_logo' value='!!segment_logo!!' /></div>
</div>
<div class='row'> 
</div>
<div class='row'>
	<label class='etiquette' for='segment_description'>".$msg['search_segment_description']."</label>
</div>
<div class='row'>
	<textarea name='segment_description' id='segment_description' rows='5' data-translation-fieldname='segment_description'>!!segment_description!!</textarea>
</div>
<!-- <div class='row'>
	<input type='checkbox' name='segment_rmc_enabled' id='segment_rmc_enabled' value='1' !!segment_rmc_enabled!! />
	<label class='etiquette' for='segment_rmc_enabled'>{$msg['search_segment_rmc_enabled']}</label>
</div> -->
!!segment_permalink_field!!
<div class='row'> 
</div>
<div class='row'>
	<label class='etiquette' for='segment_type'>".$msg['search_segment_type']."</label>
</div>
<div class='row'>
	<select name='segment_type' id='segment_type' !!segment_type_readonly!!>
		!!segment_type!!
    </select>
    !!segment_additional!!
</div>
<br/>
<div class='row'>
    <label class='etiquette' for='segment_default'>".htmlentities($msg['search_segment_default'],ENT_QUOTES, $charset)."</label>
    <input type='checkbox' id='segment_default' name='segment_default' value='1' !!checked!!/>
</div>
<div class='row'>
    <label class='etiquette' for='hide_segment_search'>".htmlentities($msg['search_segment_hide_search'],ENT_QUOTES, $charset)."</label>
    <input type='checkbox' id='hide_segment_search' name='hide_segment_search' value='1' !!hide_segment_checked!!/>
</div>
!!segment_filter_form!!				
<div class='row' id='segment_sort_form'>
	!!segment_sort_form!!
</div>					
<div class='row' id='segment_search_perso_form'>
	!!segment_search_perso_form!!			
</div>
<div class='row' id='segment_search_universes'>
	!!segment_search_universes!!			
</div>
<div class='row' id='segment_facets_form'>
	!!segment_facets_form!!			
</div>
<input type='hidden' name='segment_universe_id' id='segment_universe_id' value='!!segment_universe_id!!' />
<script type='text/javascript'>        
	require(['apps/search_universes/SegmentForm'], function(SegmentForm) {
        new SegmentForm({
            id : '!!segment_id!!',
            type : 'segment',
            className : 'SegmentForm',
            formName : 'search_segment_form'
        });	
    });
</script>
";

$search_segment_filter_form = "
<div class='row'> 
</div>
<div class='row' id='segment_filter_form'>
	!!segment_set_form!!
</div>";

$search_segment_form_definition = "
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

$search_segment_form_external_sources = "
<hr>
<div class='row'>
    <label class='etiquette' for='segment_type'>".$msg['connecteurs_source_label']."</label>
</div>
<div class='row'>
    <label class='erreur'>".$msg['connecteurs_source_advise']."</label>
</div>
<div class='row'>
    !!segment_external_sources!!
</div>";

$search_segment_form_categ_external_sources = "
  <fieldset>
    <legend><h4>!!categ_title!!</h4></legend>
    <div>!!sources_list!!</div>
  </fieldset><br>
";

$search_segment_form_categ_external_sources_line = "
  <input type='checkbox' name='sources[]' value='!!source_id!!' id='sources_!!source_id!!' !!is_checked!!><label for='sources_!!source_id!!'>!!source_name!!</label> 
";

$search_segment_universes_form = "
    <h3>".$msg['search_segment_title_universe']."</h3>
    <div class='row'>
        <table>
            <tr>
                <th></th>
                <th>".$msg['search_universe_label']."</th>
            </tr>
            !!universe_list!!
        </table>
    </div>
";

$search_segment_universe_row = "
    <tr>
        <td><input type='checkbox' !!checked!! name='search_universes_associate[]' value='!!universe_value!!'></td>
        <td>!!name_universe!!</td>
    </tr>
";



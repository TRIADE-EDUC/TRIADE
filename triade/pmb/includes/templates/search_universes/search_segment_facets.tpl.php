<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_facets.tpl.php,v 1.7 2019/05/27 09:16:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $charset, $msg;
global $segment_facets_list_form;
global $segment_facets_list_form_line;

$segment_facets_list_form=
"
<hr/>
<h3>".htmlentities($msg['title_tab_facette'],ENT_QUOTES,$charset)."</h3>
<div class='row'>
	<table>        
		<tr>
			<th></th>
			<th>".htmlentities($msg['intitule_vue_facette'],ENT_QUOTES,$charset)."</th>
		</tr>
		!!facets_list!!
	</table>
	<div class='row'>
		<input class='bouton' type='button' value='".htmlentities($msg['lib_nelle_facette_form'],ENT_QUOTES,$charset)."'  data-pmb-evt='{\"class\":\"SegmentForm\", \"type\":\"click\", \"method\":\"loadFacetDialog\", \"parameters\":{\"sub\" : \"facet\", \"entity_id\" : \"0\", \"entity_type\" : \"facet\", \"segment_id\" : \"!!segment_id!!\", \"segment_type\" : \"!!segment_type!!\" }}'/>		
	</div>
</div>
";

$segment_facets_list_form_line = "
<tr class='!!facet_class!!'>
	<td>
        <input type='checkbox' name='!!facet_type!!' value='!!facet_id!!'  !!facet_checked!! />
	</td>
	<td>
        <label data-entity-link='!!facet_link!!'>!!facet_name!!</label>
    </td>
</tr>    
    
";

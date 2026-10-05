<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_indexation.tpl.php,v 1.3 2022/11/10 15:35:40 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl;

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['list_line_indexation']='
<tr>
	<td>
		<a href="!!list_line_href!!">!!list_line_libelle!!</a>
	</td>
</tr>
';

$ontology_tpl['list_indexation'] = '
<div class="row">
	<script type="javascript" src="./javascript/sorttable.js"></script>
	<table class="sortable">
		<tr>
			<th>!!list_header!!</th>
		</tr>
		!!list_content!!
	</table>
	!!list_pagination!!
</div>	
';

$ontology_tpl['indexation_form'] = '
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites">
	<div class="left">
		<h3>!!onto_form_title!!</h3>
	</div>
	<div id="form-contenu">
		<div class="row">&nbsp;</div>
		<div id="zone-container">
			 <table>
            <tr>
                <th>'.$msg['onto_common_assertion_predicate'].'</th>
                <th>'.$msg['parperso_field_pond'].'</th>
            </tr>
           !!onto_form_content!!
        </table>
		</div>
	</div>
	<div class="row">&nbsp;</div>
	<div class="left">
		!!onto_form_history!!
		&nbsp;
		!!onto_form_submit!!
	</div>
	<div class="row"></div>
</form>
!!onto_form_scripts!!
';

$ontology_tpl['indexation_form_item_row'] = '
<tr>
    <td>
        <label for=!!name!!>!!libelle!!</label>
    </td>
    <td>
        <input type="text" name="!!name!!_pound" value="!!pound!!" />
        <input type="hidden" name="!!name!!_index_uri" value="!!index_uri!!" />
        <input type="hidden" name="!!name!!_field" value="!!field!!" />
        <input type="hidden" name="!!name!!_subfield" value="!!subfield!!" />
        <input type="hidden" name="!!name!!_useProperty" value="!!useProperty!!" />
    </td>
</tr>';
$ontology_tpl['indexation_form_item_row_entity'] = '
<tr>
    <td>
        <label for=!!name!!>!!libelle!!</label>
    </td>
    <td>
        <table>
            <tr>
                <th>'.$msg['onto_common_assertion_predicate'].'</th>
                <th>'.$msg['parperso_field_pond'].'</th>
            </tr>
            !!useProperty!!
        </table>
   </td>
</tr>';
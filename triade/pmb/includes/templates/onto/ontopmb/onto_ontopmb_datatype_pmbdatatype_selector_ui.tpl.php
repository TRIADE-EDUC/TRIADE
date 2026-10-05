<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_pmbdatatype_selector_ui.tpl.php,v 1.1 2015/08/10 23:16:25 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id;

$ontology_tpl['onto_ontopmb_datatype_pmbdatatype_ui'] = '
<select id="!!onto_row_id!!_0_value" name="!!onto_row_id!![0][value]">
	!!options!!
</select>
<input type="hidden" value="http://pmbservices.fr/ontology_description#pmbdatatype" name="!!onto_row_id!![0][type]" id="!!onto_row_id!!_0_type"/>';
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_isbd_ui.tpl.php,v 1.1 2022/11/21 13:55:55 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id;

$ontology_tpl['onto_ontopmb_datatype_isbd_ui'] = '
<textarea cols="80" rows="4" wrap="virtual" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value" class="!!editor_class!!" >!!onto_row_content_text_value!!</textarea>
<input type="hidden" value="!!onto_row_content_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<script type="text/javascript">
    pmbDojo.aceManager.initEditor("!!onto_row_id!!_!!onto_row_order!!_value");
</script>';
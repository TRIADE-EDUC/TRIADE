<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_pmbflag_ui.tpl.php,v 1.1 2022/09/13 13:48:15 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id;


$ontology_tpl['form_row_content_input_add_flag']='
<input class="bouton_small" id="!!onto_row_id!!_add" type="button" value="'.$msg['ontology_p_add_button'].'" onclick="onto_add_flag(\'!!onto_row_id!!\',0);">
';

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_pmbname_ui.tpl.php,v 1.2 2022/11/22 10:13:15 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id,$charset;


$ontology_tpl['form_row_content_existing_pmbname']='
<span><b><i>!!onto_row_content_small_text_value!!</i></b></span>
<input type="hidden" readonly="yes" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
';

$ontology_tpl['form_row_content_pmbname']='
<input type="text" class="saisie-80em" onkeyup="check_pmbname(this.value)" value="!!onto_row_content_small_text_value!!" name="!!onto_row_id!![!!onto_row_order!!][value]" id="!!onto_row_id!!_!!onto_row_order!!_value"/>
<input type="hidden" value="!!onto_row_content_small_text_range!!" name="!!onto_row_id!![!!onto_row_order!!][type]" id="!!onto_row_id!!_!!onto_row_order!!_type"/>
<script type="text/javascript">
    var checked_pmbname = "";
    function check_pmbname(pmbname){
        if(checked_pmbname != pmbname ){
            checked_pmbname = pmbname;
            fetch("./ajax.php?module=modelling&ontology_id='.$ontology_id.'&categ=check_pmbname&pmbname="+pmbname).then(function(response) {
                return response.json();
            }).then(function(data) {
                if( data.state == "ko"){
                    alert(pmbDojo.messages.getMessage("onto","onto_ontopmb_pmbname_exists"))
                }
            });
        }
    }
</script>
';
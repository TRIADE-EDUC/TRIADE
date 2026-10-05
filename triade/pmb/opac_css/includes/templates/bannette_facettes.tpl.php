<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_facettes.tpl.php,v 1.2 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $tpl_facette_elt_ajax, $tpl_facette_elt, $dsi_facette_tpl, $msg, $charset;

$tpl_facette_elt_ajax="
	<div class='row'>
		<label for='list_crit_!!i_field!!'>".htmlentities($msg['list_crit_form_facette'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		<select id='list_crit_!!i_field!!' name='list_crit_!!i_field!!' onchange=\"load_subfields('!!i_field!!',0);\" >
			!!liste1!!
		</select>
		<div id='liste2_!!i_field!!' ></div>
        <div id='bannette_facettes_options' class='row'>
    		<div class='row'>
    			<label>".htmlentities($msg['order_sort_facette'],ENT_QUOTES,$charset)."</label>
    		</div>
    		<div class='row'>
    			<input type='radio' id='order_sort_asc_!!i_field!!' name='order_sort_!!i_field!!' value='0' !!order_sort_asc_checked!!/>
    			<label for='order_sort_asc_!!i_field!!'>".htmlentities($msg['intit_gest_tri3'],ENT_QUOTES,$charset)."</label>
    			<input type='radio' id='order_sort_desc_!!i_field!!' name='order_sort_!!i_field!!' value='1' !!order_sort_desc_checked!!/>
    			<label for='order_sort_desc_!!i_field!!'>".htmlentities($msg['intit_gest_tri4'],ENT_QUOTES,$charset)."</label>
    		</div>
    		<div class='row'>
    			<label>".htmlentities($msg['datatype_sort_facette'],ENT_QUOTES,$charset)."</label>
    		</div>
    		<div class='row'>
    			<input type='radio' id='datatype_sort_alpha_!!i_field!!' name='datatype_sort_!!i_field!!' value='alpha' !!datatype_sort_alpha_checked!!/>
    			<label for='datatype_sort_alpha_!!i_field!!'>".htmlentities($msg['datatype_sort_alpha'],ENT_QUOTES,$charset)."</label>
    			<input type='radio' id='datatype_sort_num_!!i_field!!' name='datatype_sort_!!i_field!!' value='num' !!datatype_sort_num_checked!!/>
    			<label for='datatype_sort_num_!!i_field!!'>".htmlentities($msg['datatype_sort_num'],ENT_QUOTES,$charset)."</label>
    			<input type='radio' id='datatype_sort_date_!!i_field!!' name='datatype_sort_!!i_field!!' value='date' !!datatype_sort_date_checked!!/>
    			<label for='datatype_sort_date_!!i_field!!'>".htmlentities($msg['datatype_sort_date'],ENT_QUOTES,$charset)."</label>
    		</div>
        </div>
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"fonction_raz_facette('i_full_field_!!i_field!!');\" />
	</div>
	<script>load_subfields('!!i_field!!','!!ss_crit!!')</script>	

";

$tpl_facette_elt="
<div id='i_full_field_!!i_field!!'>
	$tpl_facette_elt_ajax
</div>	

";
$dsi_facette_tpl = "
<script src='javascript/ajax.js'></script>

<script>
	
	function add_facette(i_field){
		var i_field=document.getElementById('max_facette').value;
		
		var xhr_object=  new http_request();					
		xhr_object.request('./ajax.php?module=dsi&categ=bannettes&id_bannette=!!id_bannette!!&sub=facettes&suite=add_facette&i_field='+i_field, false,\"\",true,back_add_facette);		
		
	}	
	
	function back_add_facette(response){
		
		var i_field=document.getElementById('max_facette').value;
		
		var new_div = document.createElement('div');
		new_div.setAttribute('id','i_full_field_'+i_field);
		
		new_div.innerHTML =response;
		document.getElementById('add_facette').appendChild (new_div);
		
		document.getElementById('max_facette').value++;
	}
	
	function load_subfields(i_field,id_ss_champs){
	
		var lst = document.getElementById('list_crit_'+i_field);
		var id = lst.value;
		var id_subfields = id_ss_champs;
		var xhr_object=  new http_request();	
		var url='./ajax.php?module=dsi&categ=bannettes&id_bannette=!!id_bannette!!&sub=facettes&suite=ss_crit&i_field='+i_field+'&crit_id='+id+'&ss_crit_id='+id_ss_champs;			
		xhr_object.request(url);
					
		var div = document.getElementById('liste2_'+i_field);
		div.innerHTML = xhr_object.get_text() ;
	}
	
	function fonction_raz_facette(i_field) {
		document.getElementById(i_field).innerHTML='';
	}	
</script>
<input type='hidden' id='max_facette' name='max_facette' value='!!max_facette!!' />
<input type='button' class='bouton' value='+' onClick=\"add_facette();\"/>

<div id='add_facette'>!!facettes!!</div>

";

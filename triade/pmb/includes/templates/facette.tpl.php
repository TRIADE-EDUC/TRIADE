<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $tpl_form_facette, $pmb_opac_view_activate, $msg, $current_module, $charset;

$tpl_form_facette= "
<script type='text/javascript' src='./javascript/http_request.js'></script>
<script type='text/javascript'>
	function check_form_facette(){
		if(document.getElementById('label_facette').value == ''){
			alert('".$msg['label_alert_form_facette']."');
			return false;
		} else return true;
	}
	
	function confirm_delete() {
	    if(confirm('!!name_del_facette!!')) {
	        document.location='./admin.php?categ=opac&sub=!!sub!!&type=!!type!!&action=delete&id=!!id!!';
		}
	}
	
	function load_subfields(id_ss_champs){
		var xhr_object=  new http_request();					
		xhr_object.request('./ajax.php?module=admin&categ=opac&sub=lst_!!sub!!&type=!!type!!',true,\"list_crit=\"+document.getElementById('list_crit').value+\"&sub_field=\"+id_ss_champs,'true',cback,0,0)
	}
	
	function cback(response){						
		var div = document.getElementById('liste2');
		div.innerHTML = response;
	}
</script>
<form class='form-$current_module' id='facette_form' name='facette_form' method='post' action='./admin.php?categ=opac&sub=!!sub!!&type=!!type!!&action=save&id=!!id!!' onSubmit='return check_form_facette()'>
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label for='label_facette'>".htmlentities($msg['intitule_facette'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input id='label_facette' type='text' name='label_facette' value='!!label!!' data-translation-fieldname='facette_name'/>
		</div>
		<div class='row'>
			<label for='list_crit'>".htmlentities($msg['list_crit_form_facette'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			!!liste1!!
		</div>
		<div id='liste2' class='row'></div>
		<div class='row'>
			<label>".htmlentities($msg['crit_sort_facette'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='radio' id='type_sort_nb_results' name='type_sort' value='0' !!type_sort_nb_results_checked!!/>
			<label for='type_sort_nb_results'>".htmlentities($msg['intit_gest_tri1'],ENT_QUOTES,$charset)."</label>
			<input type='radio' id='type_sort_value' name='type_sort' value='1' !!type_sort_value_checked!!/>
			<label for='type_sort_value'>".htmlentities($msg['intit_gest_tri2'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<label>".htmlentities($msg['order_sort_facette'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='radio' id='order_sort_asc' name='order_sort' value='0' !!order_sort_asc_checked!!/>
			<label for='order_sort_asc'>".htmlentities($msg['intit_gest_tri3'],ENT_QUOTES,$charset)."</label>
			<input type='radio' id='order_sort_desc' name='order_sort' value='1' !!order_sort_desc_checked!!/>
			<label for='order_sort_desc'>".htmlentities($msg['intit_gest_tri4'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<label>".htmlentities($msg['datatype_sort_facette'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type='radio' id='datatype_sort_alpha' name='datatype_sort' value='alpha' !!datatype_sort_alpha_checked!!/>
			<label for='datatype_sort_alpha'>".htmlentities($msg['datatype_sort_alpha'],ENT_QUOTES,$charset)."</label>
			<input type='radio' id='datatype_sort_num' name='datatype_sort' value='num' !!datatype_sort_num_checked!!/>
			<label for='datatype_sort_num'>".htmlentities($msg['datatype_sort_num'],ENT_QUOTES,$charset)."</label>
			<input type='radio' id='datatype_sort_date' name='datatype_sort' value='date' !!datatype_sort_date_checked!!/>
			<label for='datatype_sort_date'>".htmlentities($msg['datatype_sort_date'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<label for='list_nb'>".htmlentities($msg['list_nbMax_form_facette'],ENT_QUOTES,$charset)."</label></br>
		</div>
		<div class='row'>	
			<input type='text' size='5' id='list_nb' name='list_nb' value='!!val_nb!!'>
		</div>
		<div class='row'>
			<label for='limit_plus'>".htmlentities($msg['facette_limit_plus_form'],ENT_QUOTES,$charset)."</label></br>
		</div>
		<div class='row'>	
			<input type='text' size='5' id='list_nb' name='limit_plus' value='!!limit_plus!!'>
		</div>		
		<div class='row'>
			<label for=visible_gestion>".htmlentities($msg['facettes_admin_check_visible_gestion'],ENT_QUOTES,$charset)."</label>
			<input type='checkbox' id=visible_gestion name='visible_gestion' value='1' !!visible_gestion_checked!!>
		</div>
		<div class='row'>
			<label for=visible>".htmlentities($msg['facettes_admin_check_visible_opac'],ENT_QUOTES,$charset)."</label>
			<input type='checkbox' id=visible name='visible' value='1' !!visible_checked!!>
		</div>";

if($pmb_opac_view_activate) {
	$tpl_form_facette .= "
		<div class='row'>
			<label for='opac_views'>".htmlentities($msg['admin_opac_facette_opac_views'],ENT_QUOTES,$charset)."</label></br>
		</div>
		<div class='row'>	
			!!list_opac_views!!
		</div>";
}		
$tpl_form_facette .= "</br />
	</div>
	<div class='left'>
		<input class='bouton' type='submit' value='!!val_submit_form!!'/>
		<input class='bouton' type='button' id='btexit' value='".htmlentities($msg['submitStopFacette'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=!!sub!!&type=!!type!!'\"/>
	</div>
	<div class='right'>
			!!val_submit_suppr!!
	</div>
	<div class='row'></div>
</form>";

// $tpl_vue_facettes=
// "
// <hr/>
// <h3>".htmlentities($msg['title_tab_facette'],ENT_QUOTES,$charset)."</h3>
// <div class='row'>
// 	<table>
// 		<tr>
// 			<th>".htmlentities($msg['facette_order'],ENT_QUOTES,$charset)."</th>
// 			<th>".htmlentities($msg['intitule_vue_facette'],ENT_QUOTES,$charset)."</th>
// 			<th>".htmlentities($msg['critP_vue_facette'],ENT_QUOTES,$charset)."</th>
// 			<th>".htmlentities($msg['ssCrit_vue_facette'],ENT_QUOTES,$charset)."</th>
// 			<th>".htmlentities($msg['nbRslt_vue_facette'],ENT_QUOTES,$charset)."</th>
// 			<th>".htmlentities($msg['sort_view_facette'],ENT_QUOTES,$charset)."</th>
// 			<th>".htmlentities($msg['visible_facette'],ENT_QUOTES,$charset)."</th>
// 		</tr>
// 		!!lst_facette!!
// 	</table>
// 	<div class='row'>
// 		<input class='bouton' type='button' value='".htmlentities($msg['lib_nelle_facette_form'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=edit'\"/>	
// 		<input class='bouton' type='button' value='".htmlentities($msg['facette_order_bt'],ENT_QUOTES,$charset)."' onClick=\"document.location='./admin.php?categ=opac&sub=facette_search_opac&section=facette&action=order'\"/>
// 	</div>
// </div>
// ";
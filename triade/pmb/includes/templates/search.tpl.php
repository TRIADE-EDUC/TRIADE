<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.tpl.php,v 1.50 2018-07-13 06:58:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

if(!isset($mode)) $mode = '';
if(!isset($opac_view_id)) $opac_view_id = 0;
if(!isset($id_empr)) $id_empr = 0;
if(!isset($priv_pro)) $priv_pro = '';
if(!isset($categ)) $categ = '';
if(!isset($sub)) $sub = '';
if(!isset($pmb_extended_search_auto)) $pmb_extended_search_auto = 0;

//Template du formulaire de recherches avancées
$search_form="
<script src=\"javascript/ajax.js\"></script>
<script>var operators_to_enable = new Array();</script>
<form class='form-$current_module' name='search_form' id='search_form' action='!!url!!' method='post' onsubmit=\"enable_operators();valid_form_extented_search();\" >
	<h3>!!search_form_title!!<!--!!precise_h3!!--></h3>
	<div class='form-contenu'>
		<!--!!before_form!!--> 
		<div class='row'>!!limit_search!!";
if(!isset($limited_search) || !$limited_search){
	$search_form .= "
			<label class='etiquette' for='add_field'>".htmlentities($msg["search_add_field"], ENT_QUOTES, $charset)."</label> !!field_list!! ";
	if(!$pmb_extended_search_auto){	
		$search_form .="	<input type='button' class='bouton' value='".htmlentities($msg["925"], ENT_QUOTES, $charset)."' onClick=\"if (this.form.add_field.value!='') { this.form.action='!!url!!'; this.form.target=''; this.form.submit();} else { alert('".htmlentities($msg["multi_select_champ"], ENT_QUOTES, $charset)."'); }\"/>";
	}
}
$search_form .=" </div>
 <br />
		<div class='row'>
			!!already_selected_fields!!
		</div>
	</div>";
if($mode==8)$search_form.="<!--!!limitation_affichage!!-->";		

if($sub=="opac_view" && $action =="add") {
	$search_form.="
	<div class='row'>
			<input type='submit' class='bouton' value='".htmlentities($msg["142"], ENT_QUOTES, $charset)."' id='search_form_submit'/>
			".(($categ=='consult') ? "" : "<input type='button' class='bouton' value='".htmlentities($msg["opac_view_search_save"], ENT_QUOTES, $charset)."' onClick=\"this.form.launch_search.value=1; this.form.action='!!memo_url!!'; this.form.page.value=''; !!target_js!! this.form.submit()\"/>")."
	</div>";

} else if(!empty($no_search)) {
    $search_form.="
	<div class='row'>
	   <input type='button' class='bouton' value='".htmlentities($msg["77"], ENT_QUOTES, $charset)."' id='save_search' data-pmb-evt='{\"class\" : \"".$class_name."\", \"type\" : \"click\", \"method\" : \"".$method."\", \"parameters\" : {\"formId\" : \"search_form\", \"entity_type\" : \"".$entity_type."\", \"entity_id\" : \"".$entity_id."\"}}'/>
	</div>";
} else if( $mode!=7 && $mode!=8 && $current_module !="circ" ) {
	$search_form.="
	<div class='row'>
			".(($current_module=='admin')&&($categ=='opac')?"":"<input type='submit' class='bouton' value='".htmlentities($msg["142"], ENT_QUOTES, $charset)."' id='search_form_submit'/>")."
			".(($categ=='consult' || $opac_view_id) ? "" : "<input type='button' class='bouton' id='save_predefined_search' value='".htmlentities($msg["search_perso_save"], ENT_QUOTES, $charset)."' onClick=\"enable_operators();this.form.launch_search.value=1; this.form.action='!!memo_url!!'; this.form.page.value=''; !!target_js!! this.form.submit()\"/>")."
	</div>";
}

if($mode==7 || $mode==8 || $current_module=="circ") $search_form.=	"
	<div class='row'>
		<div class='left'>
			<input type='submit' class='bouton' value='".htmlentities($msg["142"], ENT_QUOTES, $charset)."' id='search_form_submit'/>
			".($current_module=="circ" && $categ=='consult' ? "" : "<input type='button' class='bouton' id='save_predefined_search' value='".htmlentities($msg["search_perso_save"], ENT_QUOTES, $charset)."' onClick=\"enable_operators();this.form.launch_search.value=1; this.form.action='!!memo_url!!'; this.form.page.value=''; !!target_js!! this.form.submit()\"/>")."
		</div>
	</div>";
		
$search_form.="
	<input type='hidden' name='delete_field' value=''/>
	<input type='hidden' name='launch_search' value=''/>
	<input type='hidden' name='page' value='!!page!!'/>
	<input type='hidden' name='id_equation' value='!!id_equation!!'/>
	<input type='hidden' name='id_search_persopac' value='!!id_search_persopac!!'/>
	<input type='hidden' name='opac_view_id' value='!!opac_view_id!!'/>
	<input type='hidden' name='priv_pro' value='$priv_pro'/>
	<input type='hidden' name='id_empr' value='$id_empr'/>
	<input type='hidden' name='id_connector_set' value='!!id_connector_set!!'/>
	<input type='hidden' name='search_type' value='!!search_type!!'/>
</form>
<script>ajax_parse_dom();	
	function valid_form_extented_search(){
		document.search_form.launch_search.value=1;
		document.search_form.action='!!result_url!!';
		document.search_form.page.value='';
		!!target_js!!
		active_autocomplete();
		//document.search_form.submit();
	}
</script>

";

//<input type='submit' class='bouton' value='".$msg["142"]."' id='search_form_submit' onClick=\"this.form.launch_search.value=1; this.form.action='!!result_url!!'; this.form.page.value=''; !!target_js!! \"/>
?>
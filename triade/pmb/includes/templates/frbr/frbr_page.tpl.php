<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_page.tpl.php,v 1.9 2019/05/27 09:21:02 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $frbr_page_form_tpl, $pmb_opac_view_activate, $frbr_page_tree_tpl, $frbr_page_place_tpl, $msg, $charset, $current_module;

$frbr_page_form_tpl="
<script type='text/javascript'>

	function test_form(form){
		if((form.page_name.value.length == 0) )		{
			alert('".$msg["frbr_object_name_mandatory"]."');
			return false;
		}
		return true;
	}

	function load_entity_parameters(type) {
		var request = new http_request();
		var url = './ajax.php?module=cms&categ=frbr_pages&sub=parameters_form&type='+type;
		request.request(url);
		document.getElementById('parameters_form').innerHTML = request.get_text();		
	}
	
	function confirm_delete() {
		if(confirm(\"".$msg['frbr_object_confirm_delete']."\")) {
    		document.location='./cms.php?categ=frbr_pages&sub=del&id=!!id!!';
			return true;
		}
		return false;
	}
       
</script>
<form class='form-".$current_module."' id='frbr_page_form' name='frbr_page_form'  method='post' action=\"./cms.php?categ=frbr_pages&sub=save&id=!!id!!\" >
	<h3>!!title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='page_name'>".$msg['frbr_page_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='page_name' id='page_name' value='!!name!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='page_comment'>".$msg['frbr_page_comment']."</label>
		</div>
		<div class='row'>
			<textarea name='page_comment' id='page_comment' cols='55' rows='5'>!!comment!!</textarea>
		</div>
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label class='etiquette' for='page_entity'>".$msg['frbr_page_entity']."</label>
		</div>
		<div class='row'>
			!!entities_selector!!
		</div>
		<div id='parameters_form'>			
			!!parameters_form!!
		</div>";

if($pmb_opac_view_activate) {
	$frbr_page_form_tpl .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<label for='opac_views'>".htmlentities($msg['frbr_page_opac_views'],ENT_QUOTES,$charset)."</label></br>
		</div>
		<div class='row'>
			!!opac_views_selector!!
		</div>";
}
					
$frbr_page_form_tpl .= "<div class='row'>
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['frbr_object_form_cancel']."'  onclick=\"document.location='./cms.php?categ=frbr_pages&sub=list'\"  />
			<input type='submit' class='bouton' value='".$msg['frbr_object_form_save']."' onclick=\"if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['frbr_page_form'].elements['page_name'].focus();
</script>
";

$frbr_page_tree_tpl = "
	<div data-dojo-type='apps/frbr/TreeContainer' data-dojo-props='splitter:true, data:!!parameters!!' style='height: 700px;' title='".$msg['frbr_page_data']."'>
	</div>		
";

$frbr_page_place_tpl = "
	<div data-dojo-type='apps/frbr/PlaceContainer' data-dojo-props='splitter:true' style='height: 800px;' title='".$msg['frbr_page_place']."'>
	</div>
";
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: make_sugg.inc.php,v 1.33 2023/12/20 13:31:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $msg, $charset, $id_sug;
global $opac_show_help, $opac_rgaa_active;

require_once($base_path.'/classes/suggestions_categ.class.php');
require_once($base_path.'/classes/docs_location.class.php');
require_once($base_path.'/classes/suggestions.class.php');

$tooltip = str_replace("\\n","<br />",$msg["empr_sugg_ko"]);
$sug_form = "<div id='make_sugg'>";
$sug_form .= common::format_title($msg['empr_make_sugg']);
if($opac_show_help) {
    $sug_form .= "<div class='row'>$tooltip</div>";
}
if(!isset($id_sug)) $id_sug = 0;
$sugg = new suggestions($id_sug);

$sug_form.= "
<script>
	var my_timeout;
	function confirm_suppr() {
		phrase = \"".$msg['empr_confirm_suppr_sugg']."\";
		result = confirm(phrase);
		if(result)
			return true;		
		return false;
	}
	
	function input_field_change() {	
		if (my_timeout) clearTimeout(my_timeout);
		my_timeout = setTimeout('get_records_found();', 1000);
	}

	function get_records_found() {
		var tit = document.getElementById('tit').value;
		var code = document.getElementById('code').value;
		
		if((tit.length < 3) && (code.length < 3)) {
			records_found('');
			return;
		}
		var xhr_object = new http_request();
		xhr_object.request('./ajax.php?module=ajax&categ=sugg&sub=get_doublons&tit=' + tit + '&code=' + code, 0, '', 1, records_found);
	}
	
	function records_found(response) {
		dojo.forEach(dijit.findWidgets(dojo.byId('records_found')), function(w) {
			w.destroyRecursive(true);
		});
		
		document.getElementById('records_found').innerHTML = response;
		
		if(typeof(dojo) == 'object'){
	  		dojo.parser.parse(document.getElementById('records_found'));
	  	}	
	}	
</script>

<div id='make_sugg-container'>
<form action=\"empr.php\" method=\"post\" name=\"empr_sugg\" enctype='multipart/form-data'>
	<input type='hidden' name='id_sug' id='id_sug' value='$sugg->id_suggestion' />
    <input type=\"hidden\" name=\"lvl\" />";

$btn_valid = "<input type='button' class='bouton' name='ok' value='&nbsp;".addslashes($msg['empr_bt_valid_sugg'])."&nbsp;' onClick='this.form.lvl.value=\"valid_sugg\";this.form.submit()'/>";
if($sugg){
    $btn_del = "<input type='button' class='bouton' name='del' value='&nbsp;".htmlentities($msg['empr_suppr_sugg'], ENT_QUOTES, $charset)."&nbsp;' onClick=\"if(confirm_suppr()) {this.form.lvl.value='suppr_sugg'; this.form.submit();}\"/>";
} else {
    $btn_del = "";
}
if($opac_rgaa_active) {
    $sug_form .= "<div class='make_sugg-form-container'>";
    $sug_form .= $sugg->get_content_form();
    $sug_form .= "</div>";
    $sug_form .= "
    <div id='records_found'></div>
	<div class='make_sugg-form-buttons align_right'>
		$btn_valid
		$btn_del
	</div>";
} else {
    $sug_form .= "<table style='width:60%; padding:5px' role='presentation'>";
    $sug_form .= $sugg->get_content_form();
    $sug_form .= "
		<tr>
			<td colspan=2>
				<div id='records_found'></div>
			</td>
		</tr>
		<tr>
			<td colspan=2 class='align_right'>
				$btn_valid
				$btn_del
			</td>
		</tr>
	</table>
    ";
}
$sug_form.= "
</form>
</div></div>
";

print $sug_form;
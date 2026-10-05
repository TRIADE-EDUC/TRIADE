<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_tpl.tpl.php,v 1.16 2023/07/21 12:56:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// Affichage de la liste des templates de notices

global $notice_tpl_show_loc_btn, $msg, $current_module, $notice_tpl_form_code, $notice_tpl_eval, $notice_tpl_form_import;

$notice_tpl_show_loc_btn = "<input value='".$msg["notice_tpl_show_loc_btn"]."' id='show_loc_btn' class='bouton' type='button' onclick='notice_tpl_load_locations();'>
<script type='text/javascript'>
function notice_tpl_load_locations(){
	var show_loc_div = document.getElementById('show_loc_div');
	if(show_loc_div){
		show_loc_div.removeChild(document.getElementById('show_loc_btn'));
		var req = new XMLHttpRequest();
		req.open('GET', './ajax.php?module=ajax&categ=notice_tpl&action=get_locations&id_notice_tpl=!!id!!', true);
		req.onreadystatechange = function (aEvt) {
			if (req.readyState == 4) {
				if(req.status == 200){
					show_loc_div.innerHTML = req.responseText;
				}
			}
		}
		req.send(null);
	}
}
</script>";

$notice_tpl_form_code ="
<div class='row'>
	<textarea class='saisie-80em' id='code_!!loc!!_!!typenotice!!_!!typedoc!!' name='code_!!loc!!_!!typenotice!!_!!typedoc!!' cols='62' rows='30' wrap='virtual'>!!code!!</textarea>
	<input type='hidden' name='code_list[]' value='code_!!loc!!_!!typenotice!!_!!typedoc!!' />
	<script type='text/javascript'>
	 	pmbDojo.aceManager.initEditor('code_!!loc!!_!!typenotice!!_!!typedoc!!');
	</script>
</div>
";

$notice_tpl_eval="
<h3>".$msg["notice_tpl_eval"]."</h3>
<div class='row'>&nbsp;</div>
!!tpl!!
<div class='row'>&nbsp;</div>
<input type='button' class='bouton' value='$msg[654]' onClick=\"history.go(-1);\" />
";

$notice_tpl_form_import="
<form class='form-$current_module' ENCTYPE='multipart/form-data' name='fileform' method='post' action='!!action!!' >
<h3>".$msg['notice_tpl_title_form_import']."</h3>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='req_file'>".$msg['notice_tpl_file_import']."</label>
		</div>
	<div class='row'>
		<INPUT NAME='f_fichier' 'saisie-80em' TYPE='file' size='60'>
		</div>
	</div>
<input type='submit' class='bouton' value=' ".$msg['notice_tpl_bt_import']." ' />
</form>
";


<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.tpl.php,v 1.31 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $msg, $base_path;
global $avis_tpl_post_add;
global $avis_tpl_post_add_pb;
global $avis_tpl_form_script;
global $avis_tpl_form;

$avis_tpl_post_add= "
	<div class='center'><br /><br />".$msg["avis_msg_validation"]."
	<br /><br /><a href='#' onclick='window.close()'>".$msg["avis_fermer"]."</a>";

$avis_tpl_post_add_pb="<div class='center'><br /><br />".$msg["avis_msg_pb"];

$avis_tpl_form_script="
	<script src='./includes/javascript/bbcode.js'></script>
	<script>
		msg_avis_validation_en_cours='".$msg["avis_validation_en_cours"]."';
	</script>
";

$avis_tpl_form = "
	$avis_tpl_form_script
	<div id='avis_!!id!!_!!object_type!!_!!object_id!!' class='avis_form_edit' style='display: none;'>
		!!note!!
		<div class='avis_form_edit_content'>
			<label>".$msg["avis_sujet"]."</label><br />
			<input type='text' name='sujet' id='avis_!!id!!_sujet_!!object_type!!_!!object_id!!' size='50' value='!!sujet!!'/>
		</div>
		<div class='avis_form_edit_content'>
			<label>".$msg["avis_avis"]."</label><br />
			<span class='avis_html_editor'>
				<input value='".$msg["bbcode_button_label_b"]."' name='B' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[b]','[/b]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_i"]."' name='I' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[i]','[/i]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_u"]."' name='U' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[u]','[/u]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_http"]."' name='Url' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[url]','[/url]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_img"]."' name='Img' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[img]','[/img]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_code"]."' name='Code' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[code]','[/code]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_quote"]."' name='Quote' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[quote]','[/quote]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_red"]."' name='Red' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[red]','[/red]')\" type='button' class='bouton'>
				<input value='".$msg["bbcode_button_label_list"]."' name='List' onclick=\"insert_text('avis_!!id!!_commentaire_!!object_type!!_!!object_id!!','[li]','[/li]')\" type='button' class='bouton'>
			</span>
		</div>
		<div class='avis_form_edit_content'>
			<textarea name='commentaire' id='avis_!!id!!_commentaire_!!object_type!!_!!object_id!!' cols='60' rows='4'>!!commentaire!!</textarea>
		</div>
		<div class='avis_form_edit_content'>";
if(!empty($_SESSION['id_empr_session'])) {
	$avis_tpl_form .= "
		<label>".$msg["avis_private"]."</label>
		<input type='checkbox' name='private' id='avis_!!id!!_private_!!object_type!!_!!object_id!!' onchange='display_listes_lecture(!!id!!, !!object_id!!);' !!private!! />";
} else {
	$avis_tpl_form .= "
		<input type='hidden' name='private' id='avis_!!id!!_private_!!object_type!!_!!object_id!!' value='0' />";
}
$avis_tpl_form .= "
		</div>
		<div class='avis_form_edit_content' id='avis_!!id!!_display_listes_lecture_!!object_type!!_!!object_id!!'>
			<label>".$msg["avis_liste_lecture"]."</label><br />
			!!listes_lecture!!
		</div>
		<div class='avis_form_edit_buttons'>
            <br/>
			!!button_send!!
			!!button_save!!
			!!button_delete!!
		</div>
		<script>
			if(document.getElementById('avis_!!id!!_private_!!object_type!!_!!object_id!!').checked) {
				document.getElementById('avis_!!id!!_display_listes_lecture_!!object_type!!_!!object_id!!').style.display='block';
			} else {
				document.getElementById('avis_!!id!!_display_listes_lecture_!!object_type!!_!!object_id!!').style.display='none';
			}
		</script>
	</div>
";

// si paramétrage formulaire particulier
if (file_exists($base_path.'/includes/templates/avis_subst.tpl.php')) require_once($base_path.'/includes/templates/avis_subst.tpl.php');


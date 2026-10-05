<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis.tpl.php,v 1.19 2023/09/04 14:31:19 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $avis_tpl_form1, $pmb_avis_note_display_mode, $pmb_javascript_office_editor, $id, $avis_tpl_form1_script, $avis_detail_note_msg, $base_path, $msg;

if(!isset($id)) $id = 0;

if ($pmb_javascript_office_editor) {
$avis_tpl_form1_script="
	$pmb_javascript_office_editor
	<script type='text/javascript'>
        pmb_include('./javascript/tinyMCE_interface.js');
        pmb_include('./javascript/bbcode.js');
	<!--
		function show_add_avis(notice_id) {
			var div_add_avis=document.getElementById('add_avis_'+notice_id);
			if(div_add_avis.style.display  == 'block'){
				div_add_avis.style.display  = 'none';
			}else{
				div_add_avis.style.display  = 'block';
				if (typeof(tinyMCE) != 'undefined') {
					if (!tinyMCE_getInstance('edit_commentaire_!!notice_id!!') ) tinyMCE_execCommand('mceAddControl', false, 'edit_commentaire_!!notice_id!!');
				}
			}
		}
	-->
	</script>
";
} else
$avis_tpl_form1_script="
	<script type='text/javascript'>
        pmb_include('./javascript/bbcode.js');
	<!--
		function show_add_avis(notice_id) {
			var div_add_avis=document.getElementById('add_avis_'+notice_id);
			if(div_add_avis.style.display  == 'block'){
				div_add_avis.style.display  = 'none';
			}else{
				div_add_avis.style.display  = 'block';
			}
		}
	-->
	</script>
";

if($pmb_avis_note_display_mode==2)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<select id='avis_note_!!notice_id!!' name='avis_note'>
				<option value='0'>".$msg["avis_detail_note_0"]."</option>
				<option value='1'>".$msg["avis_detail_note_1"]."</option>
				<option value='2'>".$msg["avis_detail_note_2"]."</option>
				<option value='3' selected='selected'>".$msg["avis_detail_note_3"]."</option>
				<option value='4'>".$msg["avis_detail_note_4"]."</option>
				<option value='5'>".$msg["avis_detail_note_5"]."</option>
			</select>
		</div>
	";
else if($pmb_avis_note_display_mode==4)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				<span class='echelle_avis_text'>".$msg["avis_note_1"]."</span>
					<span class='echelle_avis_stars'>
						<span class='echelle_avis_star'>
							<input type='radio' name='avis_note' id='note_1_!!notice_id!!' value='1' /><label for='note_1_!!notice_id!!'></label>
							<input type='radio' name='avis_note' id='note_2_!!notice_id!!' value='2' /><label for='note_2_!!notice_id!!'></label>
							<input type='radio' name='avis_note' id='note_3_!!notice_id!!' value='3' checked /><label for='note_3_!!notice_id!!'></label>
							<input type='radio' name='avis_note' id='note_4_!!notice_id!!' value='4' /><label for='note_4_!!notice_id!!'></label>
							<input type='radio' name='avis_note' id='note_5_!!notice_id!!' value='5' /><label for='note_5_!!notice_id!!'></label>
						</span>
					</span>
				<span class='echelle_avis_text'>".$msg["avis_note_5"]."</span>
			</span>
		</div>
	";
else if($pmb_avis_note_display_mode==5)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				<span class='echelle_avis_stars'>
					<span class='echelle_avis_star'>
						<input type='radio' name='avis_note' id='note_1_!!notice_id!!' value='1' title='".$msg["avis_detail_note_1"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_1_!!notice_id!!'></label>
						<input type='radio' name='avis_note' id='note_2_!!notice_id!!' value='2' title='".$msg["avis_detail_note_2"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_2_!!notice_id!!'></label>
						<input type='radio' name='avis_note' id='note_3_!!notice_id!!' value='3' title='".$msg["avis_detail_note_3"]."' onClick=\"avis_checked(!!notice_id!!);\" checked /><label for='note_3_!!notice_id!!'></label>
						<input type='radio' name='avis_note' id='note_4_!!notice_id!!' value='4' title='".$msg["avis_detail_note_4"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_4_!!notice_id!!'></label>
						<input type='radio' name='avis_note' id='note_5_!!notice_id!!' value='5' title='".$msg["avis_detail_note_5"]."' onClick=\"avis_checked(!!notice_id!!);\" /><label for='note_5_!!notice_id!!'></label>
					</span>
				</span>
				&nbsp;&nbsp;<span id='avis_detail_note_!!notice_id!!'>".$msg["avis_detail_note_3"]."</span>
			</span>
		</div>

		<script type='text/javascript'>
			function avis_checked(notice_id) {
				var avis_checked = document.getElementsByName('avis_note');
				for(var i=0; i < avis_checked.length; i++) {
					if(avis_checked[i].checked) {
						document.getElementById('avis_detail_note_'+notice_id).innerHTML = (avis_checked[i].title);
					}
				}
			}
		</script>
	";
else if($pmb_avis_note_display_mode!=0)
	$avis_detail_note_msg="
		<div class='row'><label>".$msg["avis_appreciation"]."</label>
			<span class='echelle_avis'>
				$msg[avis_note_1]
				<input type='radio' name='avis_note' id='note_1_!!notice_id!!' value='1' />
				<input type='radio' name='avis_note' id='note_2_!!notice_id!!' value='2' />
				<input type='radio' name='avis_note' id='note_3_!!notice_id!!' value='3' checked />
				<input type='radio' name='avis_note' id='note_4_!!notice_id!!' value='4' />
				<input type='radio' name='avis_note' id='note_5_!!notice_id!!' value='5' />
				$msg[avis_note_5]
			</span>
		</div>
	";
else
	$avis_detail_note_msg="
		<input type='hidden' name='avis_note' value='3'>
	";


if ($pmb_javascript_office_editor) {
	$avis_tpl_form1 = "
		$avis_tpl_form1_script
		<div id='add_avis_!!notice_id!!' style='display: none;'>
			$avis_detail_note_msg
			<div class='row'><label>".$msg["avis_sujet"]."</label><br />
				<input type='text' name='avis_sujet' id='edit_sujet_!!notice_id!!' size='50'/>
			</div>
			<div class='row'>
				<label>".$msg["avis_comm"]."</label><br />
			</div>
			<div class='row'>
				<textarea name='avis_commentaire' id='edit_commentaire_!!notice_id!!' cols='120' rows='20'></textarea>
			</div>
			<div class='row'>
				<input type='button' class='bouton_small'  onclick='this.form.avis_quoifaire.value=\"ajouter\"; this.form.submit()'  value='".$msg["avis_save"]."'>
				<input type='button' class='bouton_small' name='mceToggleEditor' onclick=\"if (typeof(tinyMCE) != 'undefined') tinyMCE_execCommand('mceToggleEditor',false,'edit_commentaire_!!notice_id!!'); return false;\"  value='Edition'>
				<input type='button' class='bouton_small' name='exit_avis_$id' id='exit_avis_$id' value='".$msg["avis_exit"]."' onclick=\"document.getElementById('add_avis_!!notice_id!!').style.display  = 'none';\" />
			</div>
		</div>

	";
}
else {
	$avis_tpl_form1 = "
		$avis_tpl_form1_script
		<div id='add_avis_!!notice_id!!' style='display: none;'>
			$avis_detail_note_msg
			<div class='row'><label>".$msg["avis_sujet"]."</label><br />
				<input type='text' name='avis_sujet' id='edit_sujet_!!notice_id!!' size='50'/>
			</div>
			<div class='row'><label>".$msg["avis_comm"]."</label><br />
				<input value=' B ' name='B' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[b]','[/b]')\" type='button' class='bouton_small'>
				<input value=' I ' name='I' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[i]','[/i]')\" type='button' class='bouton_small'>
				<input value=' U ' name='U' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[u]','[/u]')\" type='button' class='bouton_small'>
				<input value='http://' name='Url' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[url]','[/url]')\" type='button' class='bouton_small'>
				<input value='Img' name='Img' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[img]','[/img]')\" type='button' class='bouton_small'>
				<input value='Code' name='Code' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[code]','[/code]')\" type='button' class='bouton_small'>
				<input value='Quote' name='Quote' onclick=\"insert_text('edit_commentaire_!!notice_id!!','[quote]','[/quote]')\" type='button' class='bouton_small'>
			</div>
			<div class='row'>
				<textarea name='avis_commentaire' id='edit_commentaire_!!notice_id!!' cols='60' rows='4'></textarea>
			</div>
			<div class='row'>
				<input type='button' class='bouton_small'  onclick='this.form.avis_quoifaire.value=\"ajouter\"; this.form.submit()'  value='".$msg["avis_save"]."'>
				<input type='button' class='bouton_small' name='exit_avis_$id' id='exit_avis_$id' value='".$msg["avis_exit"]."' onclick=\"document.getElementById('add_avis_!!notice_id!!').style.display  = 'none';\" />
			</div>
		</div>
	";
}
// si paramétrage formulaire particulier
if (file_exists($base_path.'/includes/templates/avis_subst.tpl.php')) require_once($base_path.'/includes/templates/avis_subst.tpl.php');


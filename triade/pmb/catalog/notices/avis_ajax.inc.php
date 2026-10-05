<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: avis_ajax.inc.php,v 1.18 2022/01/04 08:41:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quoifaire, $id;

require_once ($class_path."/avis_records.class.php");

switch($quoifaire){
	case 'show_form':
		show_form($id);
		break;
	case 'update_avis':
		update_avis($id);
		break;
}

function show_form($id){
	global $msg, $charset,$pmb_javascript_office_editor;

	$id = intval($id);
	$req = "select sujet, commentaire from avis where id_avis='".$id."'";
	$res = pmb_mysql_query($req);
	while(($avis = pmb_mysql_fetch_object($res))){
		$sujet = $avis->sujet;
		$desc = $avis->commentaire;
		if($charset != "utf-8") $desc=cp1252Toiso88591($desc);
	}
	if ($pmb_javascript_office_editor) {
		$office_editor_cmd_quit="if (typeof(tinyMCE) != 'undefined') tinyMCE_execCommand('mceRemoveControl', true, 'avis_desc_".$id."');";
		$display = "
		<div class='row'>
			<label class='etiquette'>$msg[avis_sujet]</label> <br />
			<input type='text' size='50' name='field_sujet_$id' id='field_sujet_$id' value='".htmlentities($sujet,ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>
			<label class='etiquette' >$msg[avis_comm]</label><br />

			<textarea id='avis_desc_$id' name='avis_commentaire' cols='120' rows='20'>".htmlentities($desc,ENT_QUOTES,$charset)."</textarea>
		</div>
		<input type='button' class='bouton_small' name='save_avis_$id' id='save_avis_$id' value='$msg[avis_save]' />
		<input type='button' class='bouton_small' name='mceToggleEditor' onclick=\"if (typeof(tinyMCE) != 'undefined') tinyMCE_execCommand('mceToggleEditor',false,'avis_desc_".$id."'); return false;\"  value='Edition'>
		<input type='button' class='bouton_small' name='exit_avis_$id' id='exit_avis_$id' value='$msg[avis_exit]' onclick=\"$office_editor_cmd_quit avis_exit('$id')\" />
		";

	} else{
		$display = "
		<div class='row'>
			<label class='etiquette'>$msg[avis_sujet]</label>
			<input type='text' class='saisie-20em' name='field_sujet_$id' id='field_sujet_$id' value='".htmlentities($sujet,ENT_QUOTES,$charset)."' />
		</div>
		<div class='row'>
			<label class='etiquette' >$msg[avis_comm]</label>
			<div style='padding-top: 4px;'>
				<input value=' B ' name='B' onclick=\"insert_text('avis_desc_$id','[b]','[/b]')\" type='button' class='bouton_small'>
				<input value=' I ' name='I' onclick=\"insert_text('avis_desc_$id','[i]','[/i]')\" type='button' class='bouton_small'>
				<input value=' U ' name='U' onclick=\"insert_text('avis_desc_$id','[u]','[/u]')\" type='button' class='bouton_small'>
				<input value='http://' name='Url' onclick=\"insert_text('avis_desc_$id','[url]','[/url]')\" type='button' class='bouton_small'>
				<input value='Img' name='Img' onclick=\"insert_text('avis_desc_$id','[img]','[/img]')\" type='button' class='bouton_small'>
				<input value='Code' name='Code' onclick=\"insert_text('avis_desc_$id','[code]','[/code]')\" type='button' class='bouton_small'>
				<input value='Quote' name='Quote' onclick=\"insert_text('avis_desc_$id','[quote]','[/quote]')\" type='button' class='bouton_small'>
			</div>
			<textarea style='vertical-align:top' id='avis_desc_$id' name='avis_desc_$id' cols='60' rows='8'>".htmlentities($desc,ENT_QUOTES,$charset)."</textarea>
		</div>
		<input type='button' class='bouton_small' name='save_avis_$id' id='save_avis_$id' value='$msg[avis_save]' />
		<input type='button' class='bouton_small' name='exit_avis_$id' id='exit_avis_$id' value='$msg[avis_exit]' onclick=\"avis_exit('$id')\" />
		";
	}
	print $display;
}

function update_avis($id){
	global $desc, $sujet, $msg, $charset;
	
	header('Content-Type: text/html;charset='.$charset);

	$id = intval($id);
	$req = "update avis set sujet='".$sujet."', commentaire='".$desc."' where id_avis='".$id."'";
	pmb_mysql_query($req);

	$query = "select id_avis,note,sujet,commentaire,DATE_FORMAT(dateajout,'".$msg['format_date']."') as ladate,empr_login,empr_nom, empr_prenom, valide
		from avis 
		left join empr on id_empr=num_empr 
		where id_avis='".$id."'";
	$result = pmb_mysql_query($query);
	$row = pmb_mysql_fetch_object($result);
	print avis_records::get_display_review($row);
}
?>
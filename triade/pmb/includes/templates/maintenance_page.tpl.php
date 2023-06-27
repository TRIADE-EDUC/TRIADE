<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: maintenance_page.tpl.php,v 1.6 2019-05-27 11:55:20 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $maintenance_page_form, $maintenance_page_default_content, $msg;

$maintenance_page_form = '<form class="form-admin" id="admin_opac_maintenance_form" name="admin_opac_maintenance_form" method="post" action="admin.php?categ=opac&sub=maintenance&action=save">
	<div class="form-contenu">
		<div class="row">
			<label class="etiquette" for="code">'.$msg['admin_opac_maintenance_activate'].'</label>
		</div>
		<div class="row">
			<input type="checkbox" id="maintenance_page_activate" name="maintenance_page_activate" class="switch" value="1" !!maintenance_page_activate_checked!!>
			<label for="maintenance_page_activate">&nbsp;</label>
		</div>
		<div class="row">
			<label class="etiquette" for="code">'.$msg['admin_opac_maintenance_content_title'].'</label>
		</div>
		<div class="row">
			<input type="text" name="maintenance_page_content_title" value="!!maintenance_page_content_title!!"/>
		</div>
		<div class="row">
			<label class="etiquette" for="code">'.$msg['admin_opac_maintenance_content'].'</label>
		</div>
		<div class="row">
			<textarea id="maintenance_page_content" name="maintenance_page_content_body" cols="120" rows="40">!!maintenance_page_content_body!!</textarea>
		</div>
		<div class="row">
			<label class="etiquette" for="code">'.$msg['admin_opac_maintenance_content_style'].'</label>
		</div>
		<div class="row">
			<textarea id="maintenance_page_content_style" name="maintenance_page_content_style" cols="120" rows="20">!!maintenance_page_content_style!!</textarea>
		</div>
	</div>
	<div class="row">
		<div class="left">
			<input class="bouton" value="'.$msg['77'].'" type="submit">
		</div>
	</div>
<div class="row"></div>
</form>';

$maintenance_page_default_content = '<div id="main" style="background-color:#ffffff; width:60%; margin:0 auto; margin-top:150px; padding:30px 30px 20px;; box-sizing:border-box;">
	<div style="text-align:center;"><img src="'.get_url_icon('maintenance.png').'" alt="Logo maintenance"/></div>
	<div class="paragraphe_informations" style="background-color:#f1f1f1; padding:30px 15px 30px; margin-top:30px; border-radius:5px;">
		<h1 style="text-align:center; font-family:Helvetica,Arial, sans serif; text-transform:uppercase; font-size:150%; margin:0;">Le portail est en cours de maintenance</h1>
		<h2 style="text-align:center; font-family:Helvetica,Arial, sans serif; font-size:100%; font-weight:normal; margin-bottom:0px;">D&eacute;sol&eacute; pour la g&ecirc;ne occasionn&eacute;e, nous serons bient&ocirc;t de retour</h2>
	</div>
</div>';
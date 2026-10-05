<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: maintenance_page.tpl.php,v 1.8 2023/07/04 09:58:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $maintenance_page_default_content;

$maintenance_page_default_content = '<div id="main" style="background-color:#ffffff; width:60%; margin:0 auto; margin-top:150px; padding:30px 30px 20px;; box-sizing:border-box;">
	<div style="text-align:center;"><img src="'.get_url_icon('maintenance.png').'" alt="Logo maintenance"/></div>
	<div class="paragraphe_informations" style="background-color:#f1f1f1; padding:30px 15px 30px; margin-top:30px; border-radius:5px;">
		<h1 style="text-align:center; font-family:Helvetica,Arial, sans serif; text-transform:uppercase; font-size:150%; margin:0;">Le portail est en cours de maintenance</h1>
		<h2 style="text-align:center; font-family:Helvetica,Arial, sans serif; font-size:100%; font-weight:normal; margin-bottom:0px;">D&eacute;sol&eacute; pour la g&ecirc;ne occasionn&eacute;e, nous serons bient&ocirc;t de retour</h2>
	</div>
</div>';
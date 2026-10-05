<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: template.tpl.php,v 1.3 2021/02/01 13:24:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $template_content_form, $msg;

$template_content_form = "
<script type='text/javascript' src='./javascript/tabform.js'></script>
<!--	nom	-->
<div class='row'>
	<label class='etiquette' for='name'>".$msg["template_name"]."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
</div>
<!-- 	Commentaire -->
<div class='row'>
	<label class='etiquette' for='comment'>".$msg["template_description"]."</label>
</div>
<div class='row'>
	<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
</div>
!!content_form!!
";


<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_logo.tpl.php,v 1.1.20.1 2025/03/13 11:54:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;

if (!empty($msg)) {
    $docwatch_logo_form_tpl ="
    	<div class='row'>
    		<div class='colonne'>
    			<div class='row'>
    				<label for='docwatch_form_logo'>".$msg['dsi_docwatch_form_logo']."</label>
    			</div> 
    			<div class='row'>
    				<div id='docwatch_logo_vign'>
    					<img id='docwatch_logo_vign_img' src='./docwatch_vign.php?type=!!type!!&id=!!id!!&mode=vign' class='docwatch_log_vign'/>
    				</div>
    				<div>
    					<span>".$msg['dsi_docwatch_form_logo_new']."</span>&nbsp;
    					!!field!!
    				</div>
    			</div>
    		</div>
    	</div>";
}

$docwatch_logo_form_exist_obj_tpl="
	<iframe src='./ajax.php?module=dsi&categ=docwatch&sub=watches&action=edit_logo&id=!!id!!' class='docwatch_logo_frame' >
	</iframe>";

$docwatch_logo_form_new_obj_tpl="
	<input type='file' id='docwatch_logo_file' name='docwatch_logo_file' />
	<script type='text/javascript'>
		!!js!!
	</script>";

if (!empty($msg)) {
    $docwatch_logo_field_tpl ="
    
    <form method='post' name='docwatch_logo_form' id='docwatch_logo_form' action='' enctype='multipart/form-data'>
    	<input type='file' id='docwatch_logo_file' name='docwatch_logo_file' />
    	<input type='submit' value='".$msg['dsi_docwatch_form_logo_add']."' />
    	<input type='button' value='".$msg['dsi_docwatch_form_logo_delete']."' onclick='document.getElementById(\"docwatch_logo_delete\").value=1;document.forms.docwatch_logo_form.submit();'/>
    	<input type='hidden' value='0' name='docwatch_logo_delete' id='docwatch_logo_delete' />
    </form>
    <script type='text/javascript'>
    	!!js!!
    </script>";
}
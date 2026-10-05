<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontologies.tpl.php,v 1.5 2019/05/27 09:07:44 ngantier Exp $


if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $javascript_path,$ontologies_list,$ontologies_list_item,$ontology_form, $msg, $charset, $current_module;
$ontologies_list = "
	<table>
		<tr>
			<th>".htmlentities($msg['ontologies_label'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['ontologies_description'],ENT_QUOTES,$charset)."</th>
			<th>".htmlentities($msg['ontologies_action'],ENT_QUOTES,$charset)."</th>		
		</tr>
		!!items!!
	</table>
	<input class='bouton' type='button' value='".$msg['ontologies_add']."' onClick=\"document.location='./modelling.php?categ=ontologies&sub=general&act=add'\" />";

$ontologies_list_item = "
		<tr !!tr_javascript!!>
			<td>!!label!!</td>
			<td>!!description!!</td>
		<td><input type='button' class='bouton' value='".$msg['download']."' onclick='window.open(\"./ontologie.php?ontologie_id=!!id!!\");return false'/></td>
		</tr>";

$ontology_form ="
<form class='form-$current_module' name='ontologie' method=post action=\"./modelling.php?categ=ontologies&sub=general&act=save\">
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>		
		<div class='row>
			<label for='ontologie_name'>".htmlentities($msg['ontologies_label'],ENT_QUOTES,$charset)."</label>		
		</row>
		<div class='row'>
			<input type='text' name='ontology_name' value='!!name!!'/>
		</div>
		<div class='row>
			<label for='ontology_description'>".htmlentities($msg['ontologies_description'],ENT_QUOTES,$charset)."</label>		
		</row>
		<div class='row'>
			<textarea name='ontology_description'>!!description!!</textarea>
		</div>
		!!onto_upload_directory!!
	</div>
	<!-- Boutons -->
	<div class='row'>&nbsp;</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='ontology_id' value='!!id!!'/>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"history.go(-1);\" />&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return (this.form.ontology_name.value !='');\" />
		</div>
	<div class='right'>
		!!delete_btn!!
	</div>
	<div class='row'>&nbsp;</div>
</form>";
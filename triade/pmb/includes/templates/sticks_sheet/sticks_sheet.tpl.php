<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet.tpl.php,v 1.6 2021/03/15 09:02:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $sticks_sheet_content_form, $msg, $base_path, $charset, $stick_sheet_stick_select_button, $stick_sheet_stick_select_button_script;

$sticks_sheet_content_form = "
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_label'], ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_label' name='sticks_sheet_label' class='saisie-30em' value='!!label!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_page_format'], ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<select id='sticks_sheet_page_format' name='sticks_sheet_page_format' >
			!!page_format!!
		</select>
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_page_orientation'], ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite'>
		<select id='sticks_sheet_page_orientation' name='sticks_sheet_page_orientation' >
			!!page_orientation!!
		</select>
	</div>
</div>
<input type='hidden' id='sticks_sheet_unit' name='sticks_sheet_unit' value='!!unit!!' />
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_nbr_x_sticks'], ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_nbr_x_sticks' name='sticks_sheet_nbr_x_sticks' class='saisie-5em' style='text-align:right;' value='!!nbr_x_sticks!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_nbr_y_sticks'], ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_nbr_y_sticks' name='sticks_sheet_nbr_y_sticks' class='saisie-5em' style='text-align:right;' value='!!nbr_y_sticks!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_stick_width']." (!!unit!!)", ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_stick_width' name='sticks_sheet_stick_width' class='saisie-5em' style='text-align:right;' value='!!stick_width!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_stick_height']." (!!unit!!)", ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_stick_height' name='sticks_sheet_stick_height' class='saisie-5em' style='text-align:right;' value='!!stick_height!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_left_margin']." (!!unit!!)", ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_left_margin' name='sticks_sheet_left_margin' class='saisie-5em' style='text-align:right;' value='!!left_margin!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_top_margin']." (!!unit!!)", ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_top_margin' name='sticks_sheet_top_margin' class='saisie-5em' style='text-align:right;' value='!!top_margin!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_x_sticks_spacing']." (!!unit!!)", ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_x_sticks_spacing' name='sticks_sheet_x_sticks_spacing' class='saisie-5em' style='text-align:right;' value='!!x_sticks_spacing!!' />
	</div>
</div>
<div class='row'>
	<div class='colonne25'>".htmlentities($msg['sticks_sheet_y_sticks_spacing']." (!!unit!!)", ENT_QUOTES, $charset)."</div>
	<div class='colonne_suite' >
		<input type='text' id='sticks_sheet_y_sticks_spacing' name='sticks_sheet_y_sticks_spacing' class='saisie-5em' style='text-align:right;' value='!!y_sticks_spacing!!' />
	</div>
</div>
!!cote_coords!!
!!image_coords!!";

$stick_sheet_stick_select_button = '<input class="bouton" type="button" onclick="openSticksSheetDialog(\'!!source!!\', \'!!sticksSheetSelected!!\');" value="!!button_label!!">';

$stick_sheet_stick_select_button_script = '
		<script type="text/javascript">
			require(["apps/pmb/PMBDialog", "dijit/registry", "apps/sticks_sheet/sticks_sheet", "dojo/request/xhr"], function(Dialog, registry, sticksSheet, xhr){
				window.openSticksSheetDialog = function(source, sticksSheetSelected) {
					if(!registry.byId("sticks_sheets_stick_select_dialog")){
			        	var myDijit = new Dialog({title: "!!dialog_title!!", executeScripts:true, id:"sticks_sheets_stick_select_dialog"});
				        xhr("'.$base_path.'/ajax.php?module=ajax&categ=sticks_sheets&action=get_data", {
				        	handleAs: "json"
						}).then(function (data){
					        myDijit.set("content", "<div id=\"sticks_sheet_container\"></div>");
				        	new sticksSheet({id : "sticks_sheet_widget", data : data, source : source, sticksSheetSelected: sticksSheetSelected}, "sticks_sheet_container");
				        	myDijit.resize();
				        });
					}else{
						var myDijit = registry.byId("sticks_sheets_stick_select_dialog");
				        var sticksSheetWidget = registry.byId("sticks_sheet_widget");
				        sticksSheetWidget.setSource(source);
				        sticksSheetWidget.setSticksSheetSelected(sticksSheetSelected);
				        sticksSheetWidget.updateSticksSheet();
					}
					myDijit.show();
				}
			});
		</script>';
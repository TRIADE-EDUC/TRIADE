<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence.tpl.php,v 1.9 2019-05-27 10:43:47 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $what, $admin_explnum_licence_content_form, $msg, $charset, $admin_explnum_licence_list, $current_module, $admin_explnum_licence_list_row;
global $admin_explnum_licence_settings_menu, $admin_explnum_licence_profile_list, $admin_explnum_licence_profile_list_row, $admin_explnum_licence_right_list, $admin_explnum_licence_right_list_row, $explnum_licence_selector, $explnum_licence_selector_script, $base_path, $explnum_licence_profiles_form_list_item, $explnum_licence_profile_details, $explnum_licence_pdf_container_template, $explnum_licence_right_details, $explnum_licence_info_picto, $explnum_licence_script_dialog, $explnum_licence_profile_quotation;

if (!isset($what)) {
	$what = 'profiles';
}

//statuts de contribution
$admin_explnum_licence_content_form = "
<div class='row'>
	<label class='etiquette' for='explnum_licence_label'>".htmlentities($msg["docnum_statut_libelle"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_label' name='explnum_licence_label' value='!!explnum_licence_label!!' data-translation-fieldname='explnum_licence_label'>
</div>
<div class='row'>
	<label class='etiquette' for='explnum_licence_uri'>".htmlentities($msg["explnum_licence_uri"], ENT_QUOTES, $charset)."</label>
</div>
<div class='row'>
	<input type='text' class='saisie-50em' id='explnum_licence_uri' name='explnum_licence_uri' value='!!explnum_licence_uri!!' data-translation-fieldname='explnum_licence_uri'>
</div>";

$admin_explnum_licence_list = '
		<script type="text/javascript">
			document.title="'.$msg['admin_menu_noti_licence'].'";
			window.status="'.$msg['admin_menu_noti_licence'].'";
		</script>
		<form class="form-'.$current_module.'" id="explnum_licence_form" name="explnum_licence_form" method="post" action="#">
			<input name="action" type="hidden">
			<table>	
				<tbody>
					<tr>
						<th>'.$msg["docnum_statut_libelle"].'</th>			
						<th>'.$msg["explnum_licence_uri"].'</th>
						<th></th>
					</tr>
					!!admin_explnum_licence_list_rows!!
				</tbody>
			</table>
		</form>';

$admin_explnum_licence_list_row = '
					<tr class="!!odd_even!!" onmouseover="this.className=\'surbrillance\'" onmouseout="this.className=\'!!odd_even!!\'">
						<td onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=edit&id=!!id!!\';" style="cursor: pointer">!!explnum_licence_libelle!!</td>
						<td onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=edit&id=!!id!!\';" style="cursor: pointer">!!explnum_licence_uri!!</td>
						<td><input type="button" class="bouton" onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id=!!id!!\';" value="'.$msg['explnum_licence_settings'].'" /></td>
					</tr>';

$admin_explnum_licence_settings_menu = '
		<div class="hmenu">
			<span '.(($what == 'profiles') ? 'class="selected"' : '').'>
				<a title="'.$msg['explnum_licence_profiles'].'" href="./admin.php?categ=docnum&sub=licence&action=settings&id=!!id!!&what=profiles">'.$msg['explnum_licence_profiles'].'</a>
			</span>
			<span '.(($what == 'rights') ? 'class="selected"' : '').'>
				<a title="'.$msg['explnum_licence_rights'].'" href="./admin.php?categ=docnum&sub=licence&action=settings&id=!!id!!&what=rights">'.$msg['explnum_licence_rights'].'</a>
			</span>
		</div>';

$admin_explnum_licence_profile_list = '
		<script type="text/javascript">
			document.title="'.$msg['explnum_licence_profiles'].'";
			window.status="'.$msg['explnum_licence_profiles'].'";
		</script>
		<form class="form-'.$current_module.'" id="explnum_licence_form" name="explnum_licence_form" method="post" action="#">
			<input name="action" type="hidden">
			<table>
				<tbody>
					<tr>
						<th>'.$msg["docnum_statut_libelle"].'</th>
						<th>'.$msg["explnum_licence_uri"].'</th>
					</tr>
					!!admin_explnum_licence_profile_list_rows!!
				</tbody>
			</table>
		</form>';

$admin_explnum_licence_profile_list_row = '
		<tr class="!!odd_even!!" onmouseover="this.className=\'surbrillance\'" onmouseout="this.className=\'!!odd_even!!\'">
			<td onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id=!!id!!&what=profiles&profileaction=edit&profileid=!!profileid!!\';" style="cursor: pointer">!!explnum_licence_profile_libelle!!</td>
			<td onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id=!!id!!&what=profiles&profileaction=edit&profileid=!!profileid!!\';"  style="cursor: pointer">!!explnum_licence_profile_uri!!</td>
		</tr>';

$admin_explnum_licence_right_list = '
		<script type="text/javascript">
			document.title="'.$msg['explnum_licence_rights'].'";
			window.status="'.$msg['explnum_licence_rights'].'";
		</script>
		<form class="form-'.$current_module.'" id="explnum_licence_form" name="explnum_licence_form" method="post" action="#">
			<input name="action" type="hidden">
			<table>
				<tbody>
					<tr>
						<th>'.$msg["docnum_statut_libelle"].'</th>
					</tr>
					!!admin_explnum_licence_right_list_rows!!
				</tbody>
			</table>
		</form>';

$admin_explnum_licence_right_list_row = '
		<tr class="!!odd_even!!" onmouseover="this.className=\'surbrillance\'" onmouseout="this.className=\'!!odd_even!!\'">
			<td onclick="document.location=\'./admin.php?categ=docnum&sub=licence&action=settings&id=!!id!!&what=rights&rightaction=edit&rightid=!!rightid!!\';" style="cursor: pointer">!!explnum_licence_right_libelle!!</td>
		</tr>';

$explnum_licence_selector = '
		<select name="explnum_licence[]" id="explnum_licence_selector_!!selector_index!!" class="explnum_licence_selector">
			<option value="0">'.$msg['explnum_licence_empty_selector'].'</option>
			!!explnum_licence_selector_options!!
		</select>
		<div id="explnum_licence_profiles_!!selector_index!!">
			!!explnum_licence_profiles!!
		</div>
		<br/>';
		
$explnum_licence_selector_script = '
		<script type="text/javascript">
			require(["dojo/on", "dojo/dom", "dojo/query", "dojo/_base/lang", "dojo/dom-construct", "dojo/request"], function(on, dom, query, lang, domConstruct, request) {
				
		
				var init = function() {
					var setEvent = function(node){
						on(node, "change", lang.hitch(this, function(changedElement) {
								var selector = changedElement.target;
								var selectorIndex = selector.id.slice(selector.id.lastIndexOf("_")+1);
								var containerDivId = "explnum_licence_profiles_"+selectorIndex;
								domConstruct.empty(containerDivId);
								if (selector.value * 1) {
									request.post("'.$base_path.'/ajax.php?module=catalog&categ=explnum&quoifaire=get_licence_profiles", {
										data: {
											id: (selector.value*1),
											selectorIndex: selectorIndex*1
										},
										handleAs: "text"
									}).then(function(data) {
										domConstruct.place(data, containerDivId);
									});
								}
						}));
					}
					query(".explnum_licence_selector").forEach(function(node){
						setEvent(node);
					});
					on(dom.byId("add_licence_selector"), "click", function(){
						var selector = lang.clone(dom.byId("explnum_licence_selector_0"));
						var profiles_container = lang.clone(dom.byId("explnum_licence_profiles_0"));
						var nb_selectors = query(".explnum_licence_selector").length;
						selector.value = "0";
						selector.id = "explnum_licence_selector_"+nb_selectors;
						domConstruct.empty(profiles_container);
						profiles_container.id = "explnum_licence_profiles_"+nb_selectors;
						domConstruct.place(selector, dom.byId("explnum_licence_profiles_0").parentNode);
						domConstruct.place(profiles_container, dom.byId("explnum_licence_profiles_0").parentNode);
						domConstruct.create("br", {}, dom.byId("explnum_licence_profiles_0").parentNode);
						setEvent(selector);
					});
					
				}
				init();
			});
		</script>';

$explnum_licence_profiles_form_list_item = '
		<span>
			<input type="radio" name="explnum_licence_profiles[!!explnum_licence_profile_selector_index!!]" !!explnum_licence_profile_selected!! value="!!explnum_licence_profile_id!!" id="explnum_licence_profile_!!explnum_licence_profile_selector_index!!_!!explnum_licence_profile_id!!"/>	
			<label for="explnum_licence_profile_!!explnum_licence_profile_selector_index!!_!!explnum_licence_profile_id!!">
				<img style="max-height:30px;" src="!!explnum_licence_profile_logo_url!!" alt="!!explnum_licence_profile_label!!" title="!!explnum_licence_profile_label!!"/>
			</label>
		</span>';

$explnum_licence_profile_details = "
			<h2>!!explnum_licence_label!!</h2>
			<a target='_blank' href='!!explnum_licence_uri!!'>!!explnum_licence_uri!!</a>
			
			<h3>!!explnum_licence_profile_label!!</h3>
		
			!!explnum_licence_profile_image!!<br/><br/>	
			<a target='_blank' href='!!explnum_licence_profile_uri!!'>!!explnum_licence_profile_uri!!</a>
	
			<p>!!explnum_licence_profile_explanation!!</p>
			!!explnum_licence_rights_details!!
		";


$explnum_licence_pdf_container_template = "
		<page backtop='10mm' backbottom='10mm' backleft='10mm' backright='10mm' style='text-align:center;'>
			!!explnum_licence_profiles_details!!
		</page>";

$explnum_licence_right_details = "
		!!explnum_licence_right_image!!
		<h5>!!explnum_licence_right_label!!</h5>
		<p>!!explnum_licence_right_explanation!!</p>
		";

$explnum_licence_info_picto = '
	<i style="cursor:pointer;" data-parsed="" class="fa fa-info-circle" data-explnum-id="!!explnum_id!!" data-content-type="tooltip"></i>
	<i style="cursor:pointer;" data-parsed="" class="fa fa-file-pdf-o" data-is-pdf="true" data-explnum-id="!!explnum_id!!"></i>
	<i style="cursor:pointer;" data-parsed="" class="fa fa-quote-left" data-explnum-id="!!explnum_id!!" data-content-type="quotation"></i>';

$explnum_licence_script_dialog = '			
<script type="text/javascript">
	require(["dojo/dom", 
		"dojo/query", 
		"dojo/ready", 
		"dojo/on", 
		"dojo/request", 
		"dijit/registry", 
		"dijit/Tooltip", 
		"dojo/dom-attr", 
		"dojo/_base/lang"], function (dom, query, ready, on, request, registry, Tooltip, domAttr, lang) {
    ready(function () {
        var queryResult = query("i[data-explnum-id][data-parsed=\"\"]");
        if (queryResult.length) {
            queryResult.forEach(function (inode) {
                if (!inode.getAttribute("data-is-pdf")) {
                    on(inode, "mouseover", function (e) {
                        if (!domAttr.get(this, "id")) { 
							request.post("'.$base_path.'/ajax.php?module=catalog&categ=explnum&quoifaire=get_licence_" + domAttr.get(this, "data-content-type"), {
	                            data: {
	                                id: domAttr.get(this, "data-explnum-id")
	                            },
	                            handleAs: "text"
	                        }).then(lang.hitch(this, function (data) {
	                                var date = new Date();
	                                domAttr.set(this, "id", "tooltip_explnum_"+date.getTime());
	                                var tooltip = new Tooltip({
	                                    title: "",
	                                    connectId: domAttr.get(this, "id"),
	                        			label: data,
	                                    style: {
	                                        textAlign: "center"
	                                    }
	                                });
 									tooltip.startup();
 									tooltip.open(this);
 									tooltip.close(this);
 									tooltip.open(this);
	                        }));
						}
                    });
                }else{
                    on(inode, "click", function(e){
                        window.open("'.$base_path.'/ajax.php?module=catalog&categ=explnum&quoifaire=get_licence_as_pdf&id="+this.getAttribute("data-explnum-id"), "_blank");
                    });
                }
               	domAttr.set(inode, "data-parsed", "parsed");
            });
        }
    });
});

	function profile_quotation_copy_to_clipboard(copyButton){
		var copyText = copyButton.previousElementSibling;
		copyText.select();
		document.execCommand("copy");
	}
</script>';

$explnum_licence_profile_quotation = '
<input type="text" value="!!profile_quotation!!" size="50"/>
<i style="cursor:pointer;" class="fa fa-copy" onclick="profile_quotation_copy_to_clipboard(this);"></i>';
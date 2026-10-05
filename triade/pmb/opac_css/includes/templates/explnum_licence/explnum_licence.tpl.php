<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence.tpl.php,v 1.6 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $base_path;
global $explnum_licence_profile_details;
global $explnum_licence_pdf_container_template;
global $explnum_licence_right_details;
global $explnum_licence_info_picto;
global $explnum_licence_script_dialog;
global $explnum_licence_profile_quotation;

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
	<span style="cursor:pointer;" data-parsed="" data-explnum-id="!!explnum_id!!" data-content-type="tooltip">
		<i class="fa fa-info-circle"></i>
	</span>
	<span style="cursor:pointer;" data-parsed="" data-is-pdf="true" data-explnum-id="!!explnum_id!!">
		<i class="fa fa-file-pdf-o"></i>
	</span>
	<span style="cursor:pointer;" data-parsed="" data-explnum-id="!!explnum_id!!" data-content-type="quotation">
		<i class="fa fa-quote-left"></i>
	</span>';

$explnum_licence_script_dialog = '			
<script>
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
        var queryResult = query("span[data-explnum-id][data-parsed=\"\"]");
        if (queryResult.length) {
            queryResult.forEach(function (inode) {
                if (!inode.getAttribute("data-is-pdf")) {
                    on(inode, "mouseover", function (e) {
                        if (!domAttr.get(this, "id")) { 
							request.post("'.$base_path.'/ajax.php?module=ajax&categ=explnum&sub=get_licence_" + domAttr.get(this, "data-content-type"), {
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
                        window.open("'.$base_path.'/ajax.php?module=ajax&categ=explnum&sub=get_licence_as_pdf&id="+this.getAttribute("data-explnum-id"), "_blank");
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
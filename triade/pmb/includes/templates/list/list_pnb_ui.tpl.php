<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_pnb_ui.tpl.php,v 1.4 2018/06/29 09:47:33 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$list_pnb_ui_script_case_a_cocher = "
<script language='javascript'>
    
    require(['dojo/query',
            'dojo/on',
            'dojo/ready',
            'dojo/dom',
            'dojo/_base/lang',
            'dojo/dom-construct',
            'dojo/_base/xhr'], function(query, on, ready, dom, lang, domConstruct, xhr){
        ready(function(){
            var checkAll = dom.byId('check_all_command_lines');
            var createContactButton = function(){
                if(!query('div[class=\"align_right\"]').length){
                    var linkContainer = domConstruct.create('div', {class: 'align_right'}, 'pnb_ui_list', 'before');
                    var contactLink = domConstruct.create('a', {href: '#', innerHTML: pmbDojo.messages.getMessage('edition_pnb', 'pnb_contact_link_title')}, linkContainer, 'last');
                    on(contactLink, 'mousedown', function(e){
                        var checkboxes = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][data-pnb]:checked:enabled'));
                        if(checkboxes.length){
                            var spinner = domConstruct.create('i', {class: 'fa fa-2x fa-circle-o-notch fa-spin', ariaHidden:true}, contactLink);
                            var commandsIds = [];
                            checkboxes.forEach(function(checkbox){
                                commandsIds.push(checkbox.value);
                            });
                            xhr.post({
                                url:'./ajax.php?module=edit&categ=pnb&sub=orders&action=mailto',
                                sync: true,
                                postData: 'commands_ids='+commandsIds.join(','),
                                handleAs:'json',
                                load: function(data){
                                    if(data){
                                        var body = '';
                                        for(var key in data){
                                            if((key != 'GLN') && (key != 'address')){
                                                body+= '\\nReference commande: '+data[key].orderId+'\\n'+pmbDojo.messages.getMessage('edition_pnb', 'pnb_contact_command_create_date')+': '+data[key].orderCreateDate+'\\n'+pmbDojo.messages.getMessage('edition_pnb', 'pnb_contact_command_line_id')+': '+data[key].orderLineId+'\\n'+pmbDojo.messages.getMessage('edition_pnb', 'pnb_contact_gln')+': '+data['GLN']+'\\n\\n';
                                            }
                                        }
                                        domConstruct.destroy(spinner);
                                        contactLink.href = 'mailto:'+data['address']+'?subject='+encodeURI(pmbDojo.messages.getMessage('edition_pnb', 'pnb_contact_mail_title'))+'&body='+encodeURI(body);
                                        contactLink.click();
                                        contactLink.href = '#';
                                    }
                                }
                            });
                        }else{
                            alert(pmbDojo.messages.getMessage('edition_pnb', 'pnb_please_select_command'));
                        }
                        e.preventDefault();
                        return false;
                    });
                }
            }();
            var checkboxes = query('input[type=\"checkbox\"][data-pnb]');
            on(checkAll, 'click', lang.hitch(this, function(){
                var checked = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][data-pnb]:checked:enabled'));
                var notChecked = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][data-pnb]:checked:disabled'));
                if(checked.length < checkboxes.length){
                    checkboxes.forEach(function(checkbox){
                        checkbox.checked = true;
                    });
                }else{
                    checkboxes.forEach(function(checkbox){
                        checkbox.checked = false;
                    });
                }
    
           }));
        });
    });
    
	function check(cac) {
		cac.checked=!cac.checked;
	}
    
	function verifChk(formToCheck,valAction) {
		nb = formToCheck.elements.length;
		res = false;
		for (var i=0;i<nb;i++) {
			var e = formToCheck.elements[i];
			if ((e.type == 'checkbox')&&(e.name.substr(0,4)=='sel_'))
				if (e.checked == true) {
					res = true;
					break;
				}
		}
		if (res==true) {
			formToCheck.action.value = valAction;
			formToCheck.submit();
		} else {
			alert('".$msg["transferts_circ_pas_de_selection"]."');
		}
	}
</script>
";

$pnb_ui_search_filters_form_tpl = "		
	<script type=\"text/javascript\" src='./javascript/pnb.js'></script>		
	<input type='checkbox' name='alert_end_offers' id='alert_end_offers' value='1' !!alert_end_offers_checked!! /> <label for='alert_end_offers'>".$msg['pnb_edit_end_offers_filter']."</label>		
	<input type='checkbox' name='alert_staturation_offers' id='alert_staturation_offers' value='1' !!alert_staturation_offers_checked!!! /> <label for='alert_staturation_offers'>".$msg['pnb_edit_staturation_offers_filter']."</label>	
";

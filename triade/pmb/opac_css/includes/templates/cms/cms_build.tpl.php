<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_build.tpl.php,v 1.8 2023/11/30 10:43:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $cms_edit_css, $msg, $cms_objet_type_selection, $cms_edit_objet, $cms_build_cadres_tpl, $cms_build_cadre_tpl_item, $cms_build_pages_tpl, $opac_url_base, $cms_build_pages_tpl_item;
global $cms_build_pages_ajax_tpl, $cms_build_modules_tpl, $cms_build_block_tpl;

function cms_gen_objet_css($name){
	$objet_css="
	<input dojoType='dijit.form.NumberSpinner' value='' smallDelta='1' constraints='{min:-2000,max:2000,places:0}' id='$name' name='$name' style= 'width:80px'
		intermediateChanges='true'
	 	onchange =\"cms_change_css(document.getElementById('cms_edit_form').getAttribute('cms_edit_id'));\"
	/>
	<select id='".$name."_def' name='".$name."_def' onchange =\"cms_change_css_format_number(this,'$name');cms_change_css(document.getElementById('cms_edit_form').getAttribute('cms_edit_id'));\">				
		<option value='auto'>auto</option>
		<option value='px'>px</option>
		<option value='%'>%</option>
		<option value='inherit'>inherit</option>
	</select>
	";

	return $objet_css;
}

$cms_edit_css="
	<script>
		
		function cms_change_css_format_number(obj,id_number){
			obj_number=document.getElementById(id_number);
			obj_number_field=document.getElementById('widget_'+id_number);
			switch(obj.options[obj.selectedIndex].value){
				case 'auto':
					obj_number_field.style.display='none';
				break;
				case 'px': 
					obj_number_field.style.display='block';
				break;
				case '%': 
					obj_number_field.style.display='block';
				break;
				case 'inherit': 
					obj_number_field.style.display='none';
				break;
			}
		}
	
	    dojo.require('dijit.form.NumberSpinner');
	  
	</script>
	<div id='cms_edit_form' cms_edit_id='' >
		<h3>
			".$msg["cms_edit_css"]."
		</h3>
		<div class='row'>
			<span id='cms_edit_title_obj'>
		</span></div>
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_position"]."&nbsp;</label>
		</div>
		<div class='row'>
			<select id='cms_position' name='cms_position' onchange =\"cms_change_css(document.getElementById('cms_edit_form').getAttribute('cms_edit_id'));\">				
				<option value='relative'>relative</option>
				<option value='absolute'>absolute</option>
				<option value='static'>static</option>
			</select>
		</div>					
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_left"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_left")."			
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_top"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_top")."			
		</div>			
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_zindex"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_zIndex")."			
		</div>				
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_visibility"]."&nbsp;</label>
		</div>
		<div class='row'>			
			<select id='cms_visibility' name='cms_visibility' onchange =\"cms_change_css(document.getElementById('cms_edit_form').getAttribute('cms_edit_id'));\">				
				<option value='hidden'>hidden</option>
				<option value='visible'>visible</option>
			</select>
		</div>				
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_height"]."&nbsp;</label>			
		</div>
		<div class='row'>			
			".cms_gen_objet_css("cms_height")."			
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_width"]."&nbsp;</label>
		</div>
		<div class='row'>					
			".cms_gen_objet_css("cms_width")."	
		</div>		
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_float"]."&nbsp;</label>
		</div>
		<div class='row'>					
			<select id='cms_float' name='cms_float' onchange =\"cms_change_css(document.getElementById('cms_edit_form').getAttribute('cms_edit_id'));\">				
				<option value='left'>left</option>
				<option value='right'>right</option>
				<option value='none'>none</option>
				<option value='inherit'>inherit</option>
			</select>
		</div>			
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_margin_top"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_margin_top")."
		</div>
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_padding_top"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_padding_top")."
		</div>	
		<div class='row'>		
			<label class='etiquette'>".$msg["cms_edit_form_margin_right"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_margin_right")."
		</div>			
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_padding_right"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_padding_right")."
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_margin_bottom"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_margin_bottom")."
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_padding_bottom"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_padding_bottom")."
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_margin_left"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_margin_left")."
		</div>					
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_padding_left"]."&nbsp;</label>
		</div>
		<div class='row'>
			".cms_gen_objet_css("cms_padding_left")."
		</div>	
		<div class='row'>
			<label class='etiquette'>".$msg["cms_edit_form_display"]."&nbsp;</label>
		</div>
		<div class='row'>			
			<select id='cms_display' name='cms_display' onchange =\"cms_change_css(document.getElementById('cms_edit_form').getAttribute('cms_edit_id'));\">				
				<option value='block'>block</option>
				<option value='none'>none</option>
			</select>
		</div>	
		
		<div class='row'>		
			<input type='button' class='bouton' name='cms_edit_form_save' id='cms_edit_form_save' value='".$msg["cms_edit_form_save"]."' onclick='' />			
		</div>	
	</div>
";

$cms_objet_type_selection="
	<h3>
		".$msg["cms_edit_objet_selection"]."
	</h3>
	<div class='row'>
	
		<table style='border:0px; width:100%; border-spacing: 0px'>
		<tr>
			<td>"
				.$msg["cms_dragable_type"]."		
			</td>		
			<td>
				<input  type='radio' id='cms_dragable_cadre' name='cms_dragable_type' value='cadre' onclick=\"cms_drag_activate_form();\" ><label for='cms_dragable_cadre'>".$msg["cms_dragable_zone"]."</label>
			</td>	
			<td>		
				<input  type='radio' id='cms_dragable_object' name='cms_dragable_type' value='object'  checked='checked' onclick=\"cms_drag_activate_form();\" ><label for='cms_dragable_object'>".$msg["cms_dragable_cadre"]."</label>
			</td>
		</tr>	
		<tr>
			<td>
				".$msg["cms_receptable_type"]."		
			</td>		
			<td>
				<input  type='radio' id='cms_receptable_conteneur' name='cms_receptable_type' value='conteneur' onclick=\"cms_drag_activate_form();\" ><label for='cms_receptable_conteneur'>".$msg["cms_receptable_conteneur"]."</label>
			</td>	
			<td>		
				<input  type='radio' id='cms_receptable_cadre' name='cms_receptable_type'  value='cadre' checked='checked' onclick=\"cms_drag_activate_form();\" ><label for='cms_receptable_cadre'>".$msg["cms_receptable_zone"]."</label>
			</td>
		</tr>	
		</table>
	</div>	
	<div class='row'>		
		<input type='button'  class='bouton'  id='cms_drag_activate_button' active='' value='".$msg["cms_activer_drag_drop"]."'  onclick=\"cms_drag_activate_form(); return false;\">
	</div>	
	";
				
$cms_edit_objet="	
	<script>
	
		function cms_opac_loaded(){
			document.getElementById('cms_drag_activate_button').setAttribute('active','1')
			cms_drag_activate_form();
		}
		function cms_drag_activate_form(){
			if(document.getElementById('cms_drag_activate_button').getAttribute('active') ){
				cms_drag_activate(0,0,0);
				document.getElementById('cms_drag_activate_button').style.backgroundColor ='';
				document.getElementById('cms_drag_activate_button').setAttribute('active','')
				document.getElementById('cms_drag_activate_button').value='".$msg["cms_activer_drag_drop"]."';
				return;
			} else{
				document.getElementById('cms_drag_activate_button').style.backgroundColor ='#00FF00';
				document.getElementById('cms_drag_activate_button').setAttribute('active','1')
				document.getElementById('cms_drag_activate_button').value='".$msg["cms_reset_drag_drop"]."';
			}
			
			var radioButtons=document.getElementsByName('cms_dragable_type');
			var cms_dragable_type=0;
			for (var i=0; i < radioButtons.length; i ++) {	           
	            if (radioButtons[i].checked) {
                    cms_dragable_type=radioButtons[i].value;
                }
	        } 
	        
			var radioButtons=document.getElementsByName('cms_receptable_type');
			var cms_receptable_type=0;
			for (var i=0; i < radioButtons.length; i ++) {	           
	            if (radioButtons[i].checked) {
                    cms_receptable_type=radioButtons[i].value;
                }
	        } 	      
	        
			cms_drag_activate(1,cms_dragable_type,cms_receptable_type);
		}
	</script>

			

";	
$cms_build_cadres_tpl="
	<script>
		var cms_cadre_portail_list=new Array();
		!!cms_cadre_portail_list!!
	</script>
	<table>
		!!items!!
	</table>
";			           

$cms_build_cadre_tpl_item="
<tr class='!!odd_even!!' style='cursor: pointer;' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\"
 		onclick=\"cms_show_obj('!!cadre_object!!_!!id_cadre!!');return false; \" >
	<td>		<a onclick=\"cms_build_load_module('!!cadre_object!!','get_form',!!id_cadre!!);\" href='#' > 
				<img class='icon' width='16' height='16' title='".$msg["cms_build_edit_bt"]."' alt='".$msg["cms_build_page_add_bt"]."' src='".get_url_icon('b_edit.png')."'  >
			</a>	
			!!cadre_name!!
	</td>			
</tr>
";	


$cms_build_pages_tpl="

<script>
    dojo.require('dijit.form.Button');
    dojo.require('dijit.Dialog');    
    dojo.require('dojo.parser');
    dojo.require('dojox.layout.ContentPane');
    dojo.require('dojox.widget.Dialog');
    dojo.require('dojox.widget.DialogSimple');
    
    function cms_build_page_edit_add(id){
    
    	if(!dijit.byId('cms_build_dialog')){
	        //creates a new dialog
	        try {
	        	var myDijit = new dojox.widget.DialogSimple({title: 'Referent', id:'cms_build_dialog'});    
			}catch(e){
				if(typeof console != 'undefined') {
					console.log(e);
				}
			};
	         
	        //appends the dialog to an existing DOM node
	        dojo.byId('att').appendChild(myDijit.domNode);
	        //the dialog is hidden until called
		}    
        //get the dialog
        var dialogDijit = dijit.byId('cms_build_dialog');        
        var path = './ajax.php?module=cms&categ=pages&sub=edit&id='+id
        dialogDijit.attr('href', path);
        dialogDijit.show();
	}

	 
	function cms_build_page_add(page){	
		var frame=document.getElementById('opac_frame')
		frame.setAttribute('src', '".$opac_url_base."index.php?cms_build_activate=1&lvl=cmspage&pageid='+page);
	
	}
	
	</script>
	<div id='cms_build_pages_list'>
		!!items!!
	</div>
	<input type='button' class='bouton' value='".$msg["cms_build_page_add_bt"]."' onclick=\"cms_build_page_edit_add('!!id!!'); return false;\">
";

$cms_build_pages_tpl_item="
<a href='#' onclick=\"cms_build_page_edit_add('!!id!!');\"><img class='icon' width='16' height='16' title='".$msg["cms_build_edit_bt"]."' alt='".$msg["cms_build_page_add_bt"]."' src='".get_url_icon('b_edit.png')."'> </a> 	
<a href='#' onclick=\"cms_build_page_add('!!id!!');\">!!name!!</a> <br/>
";	

$cms_build_pages_ajax_tpl="
	!!items!!
";
$cms_build_modules_tpl="
  <script>
        function cms_build_load_module(module,action,id){
            if(!module.match('cms_module_')){
                 module = 'cms_module_'+module;
            }  
                
	    	if(!dijit.byId('cms_build_dialog')){
		        //creates a new dialog
		        try {
		        	var myDijit = new dojox.widget.DialogSimple({title: 'Referent', id:'cms_build_dialog'});    
				}catch(e){
					if(typeof console != 'undefined') {
						console.log(e);
					}
				};
		         
		        //appends the dialog to an existing DOM node
		        dojo.byId('att').appendChild(myDijit.domNode);
		        //the dialog is hidden until called
			}    
	        //get the dialog
	        var dialogDijit = dijit.byId('cms_build_dialog');        
	        var path = './ajax.php?module=cms&categ=module&elem='+module+'&action='+action+'&id='+id;
	        path+='&callback=window.parent.cms_build_save_module';
	        path+='&cancel_callback=window.parent.cms_build_cancel_module';
	        path+='&cms_build_info=' + parent.frames['opac_frame'].document.getElementById('cms_build_info').value;
	        dialogDijit.attr('href', path);
	        dialogDijit.show();            
        }
      	
        function cms_build_cancel_module(data){
        	dijit.byId('cms_build_dialog').hide(); 	
        }		        
        
        function cms_build_save_module(data){
        	dijit.byId('cms_build_dialog').hide(); 		
			cms_build_new_cadre(data.dom_id, data.name+' ( id='+data.id+' )<br/>'+data['object']);
         }
	</script>
	!!items!!
";		
	           

$cms_build_block_tpl="
<script src='./javascript/cms/cms_build.js'></script>
<script src='./javascript/cms/cms_drag_n_drop.js'></script>
<script src='./javascript/cms/cms_drop.js'></script>
<script src='./javascript/cms/cms_pages.js'></script>

<script>
	dojo.require('dojo.parser');
	dojo.require('dijit.layout.BorderContainer');
	dojo.require('dijit.layout.TabContainer');
	dojo.require('dijit.layout.AccordionContainer');
	dojo.require('dijit.layout.ContentPane');       
</script>
	
<div dojoType='dijit.layout.BorderContainer' design='sidebar' gutters='true' style='width: 100%; height: 800px;'>
	
	<div dojoType='dijit.layout.ContentPane'  region='center' >
		<IFRAME name='opac_frame' id='opac_frame' src='".$opac_url_base."index.php?cms_build_activate=1' style='background-color:#FFFFFF;width:100%;height:710px;border:0px solid #000'></IFRAME>	
	</div>
	<div dojoType='dijit.layout.ContentPane' region='left' splitter='true' style='width:300px;' >
			
		  <div dojoType='dijit.layout.TabContainer' >			  
		        <div dojoType='dijit.layout.ContentPane' title='".$msg["cms_build_objet_content"]."' selected='true'>			           
		           		   
		           <div dojoType= 'dijit.layout.AccordionContainer' >		        
				       	
				        <div dojoType= 'dijit.layout.AccordionPane' title='".$msg["cms_build_objet_def"]."' selected='true'>
				        	$cms_objet_type_selection
				        	$cms_edit_objet
				        	<div class='row'>
								<h3>".$msg["cms_edit_sel_objet_list"]."</h3>
								</div>		
								<div class='row' id='cms_edit_sel_objet_list'>
									<table id='cms_edit_sel_objet_list_table' style='border:0px; width:100%; border-spacing: 0px'>
									</table>
								</div>		
								<div class='row'>
									<h3>".$msg["cms_edit_sel_portail_list"]."</h3>
								</div>		
								<div class='row' id='cms_edit_sel_portail_list'>	
									!!cadre_portail_list!!	
								</div>
								<div class='row'>
									<h3>".$msg["cms_edit_sel_cadre_list"]."</h3>
								</div>
								<div class='row' id='cms_edit_sel_cadre_list'>
									<table id='cms_edit_sel_cadre_list_table' style='border:0px; width:100%; border-spacing: 0px'>
									</table>
								</div>			
								<div class='row'>
							</div>
				        </div>						
				        <div dojoType= 'dijit.layout.AccordionPane' title='".$msg["cms_build_modules"]."'>	
				        	!!cms_objet_modules!!						          
				        </div>		
				        <div dojoType= 'dijit.layout.AccordionPane' title='".$msg["cms_build_pages"]."'>
				        	!!cms_objet_pages!!				          
				        </div>			
		      		</div>
		        </div>
		        <div dojoType='dijit.layout.ContentPane' title='".$msg["cms_build_css_content"]."'>
		           $cms_edit_css
		        </div>
		    </div>		
	</div>
	<div dojoType='dijit.layout.ContentPane' region='bottom' >	
		<input type='button' class='bouton' value='".$msg["cms_memoriser_drag_drop"]."' onclick=\"cms_drag_record(); return false;\">
	</div>
</div>	

<script>
	var cms_contener_list=new Array();
	var cms_zone_list=new Array();
	var cms_objet_list=new Array();
	!!cms_objet_list_declaration!!
	cms_build_init();	
</script>
";			   
	         
			           
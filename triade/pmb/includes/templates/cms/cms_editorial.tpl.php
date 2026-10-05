<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial.tpl.php,v 1.49.6.1 2024/07/01 14:39:04 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $cms_editorial_form_editables;
global $PMBuserid, $current_module, $cms_editorial_form_tpl, $msg;
global $cms_editorial_form_del_button_tpl, $cms_editorial_form_dupli_button_tpl, $cms_editorial_form_audit_button_tpl, $cms_editorial_parent_field, $cms_editorial_title_field, $charset, $cms_dojo_plugins_editor, $cms_editorial_resume_field, $cms_editorial_resume_field_no_dojo, $cms_editorial_contenu_field, $cms_editorial_contenu_field_no_dojo, $cms_editorial_desc_field, $cms_editorial_first_desc, $cms_editorial_other_desc, $cms_editorial_publication_state_field, $cms_editorial_dates_field, $cms_editorial_type_field, $cms_editorial_obj_id_field, $cms_editorial_permalink_field;

$cms_editorial_form_tpl = "
	<script type='text/javascript'>
		require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
		     ready(function(){
				//instanciation faite CmsEditorialTypeContentForm
 		     	//new FormEdit('cms', '!!type!!');
		     });
		});
	</script>
	<form name='!!cms_editorial_form_name!!' class='cms_editorial_form' action='./cms.php?categ=!!type!!&sub=save' id='!!cms_editorial_form_id!!' method='post' !!cms_editorial_form_attr!!>
		<div id='cms_editorial_content_saved'></div>
		<div class='row'>
			<div class='left'>
				<h3>!!form_title!!</h3>
			</div>
			<div class='right'>";
				if ($current_module != 'ajax' && $PMBuserid==1 && $cms_editorial_form_editables==1){
					$cms_editorial_form_tpl .="<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' id=\"bt_inedit\"/>";
				}
				if ($current_module != 'ajax' && $cms_editorial_form_editables==1) {
					$cms_editorial_form_tpl.="<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" id=\"bt_origin_format\"/>";
				}
		$cms_editorial_form_tpl .= "
			</div>
		</div>
		<div class='form-contenu'>
			<div id='zone-container'>
				<input type='hidden' name='cms_editorial_form_obj_id' id='cms_editorial_form_obj_id' value='!!cms_editorial_form_obj_id!!' />
				!!fields!!
				<div class='row'>&nbsp;</div>
				<div id='el9Child' etirable='yes' data-zone-ajax='yes' dojoType='apps/cms/CmsEditorialTypeContentForm' href='!!type_href!!' data-dojo-props='type : \"!!type!!\", activated_grid : ".$cms_editorial_form_editables.", activated_tinymce : !!activated_tinymce!!' label=\"".htmlentities($msg['cms_editorial_form_type'], ENT_QUOTES, $charset)."\">
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='left'>
				<input type='hidden' name='cms_editorial_form_delete' value='0' />
				<input type='hidden' name='cms_editorial_form_duplicate' value='0' />
				<input type='submit' class='bouton' value='".$msg['cms_editorial_form_save']."' onclick='unload_off();load_textareas();document.forms[\"!!cms_editorial_form_name!!\"].cms_editorial_form_delete.value=0;'/>
				!!cms_editorial_form_dupli!!
				!!cms_editorial_form_audit!!
				
				!!cms_editorial_suite!!
			</div>
			<div class='right'>
				!!cms_editorial_form_suppr!!
			</div>
		</div>
		<div class='row'>&nbsp;</div>
	</form>
	<script type='text/javascript'>
		function unload_tinymce() {
				if (typeof(tinyMCE) != 'undefined') {
					if (tinyMCE_getInstance('cms_editorial_form_resume')) {
						tinyMCE_execCommand('mceToggleEditor',true,'cms_editorial_form_resume');
						tinyMCE_execCommand('mceRemoveControl',true,'cms_editorial_form_resume');
					}
					if (tinyMCE_getInstance('cms_editorial_form_contenu')) {	
						tinyMCE_execCommand('mceToggleEditor',true,'cms_editorial_form_contenu');
						tinyMCE_execCommand('mceRemoveControl',true,'cms_editorial_form_contenu');
					}	
				}
		}
						
		function load_textareas(){
			if(dijit.byId('cms_editorial_form_resume_form')){
				if(document.forms['!!cms_editorial_form_name!!'].cms_editorial_form_resume) document.forms['!!cms_editorial_form_name!!'].cms_editorial_form_resume.value = dijit.byId('cms_editorial_form_resume_form').get('value');
				if(document.forms['!!cms_editorial_form_name!!'].cms_editorial_form_contenu) document.forms['!!cms_editorial_form_name!!'].cms_editorial_form_contenu.value = dijit.byId('cms_editorial_form_contenu_form').get('value');
			} else {
				unload_tinymce();
			}
			if(typeof(check_form) == 'function' && !check_form()){
				return false;
			}
		}
	</script>".jscript_unload_question();

$cms_editorial_form_del_button_tpl ="
	<input type='submit' class='bouton' onclick='unload_off();document.forms[\"!!cms_editorial_form_name!!\"].cms_editorial_form_delete.value=1;document.forms[\"!!cms_editorial_form_name!!\"].action=\"$base_path/cms.php?categ=!!type!!&sub=del\";unload_tinymce();' value='".$msg['cms_editorial_form_delete']."'/>
";

$cms_editorial_form_dupli_button_tpl = "
	<input type='submit' class='bouton' onclick='unload_off();document.forms[\"!!cms_editorial_form_name!!\"].cms_editorial_form_duplicate.value=1;document.forms[\"!!cms_editorial_form_name!!\"].cms_editorial_form_delete.value=0;unload_tinymce();' value='".$msg['cms_editorial_form_duplicate']."'/>
";

$cms_editorial_form_audit_button_tpl = "
	<input type='button' class='bouton' onclick='openPopUp(\"./audit.php?type_obj=!!cms_editorial_form_audit_type!!&object_id=!!cms_editorial_form_obj_id!!\", \"audit_popup\")' value='".$msg['audit_button']."'/>
";

$cms_editorial_parent_field = "
	<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_parent'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_parent'>".$msg['cms_editorial_form_parent']."</label>
		</div>
		<div class='row'>
			<select name='cms_editorial_form_parent' id='cms_editorial_form_parent'>
				!!cms_editorial_form_parent_options!!
			</select> 
		</div>
	</div>";


$cms_editorial_title_field = "
	<div id='el0Child_4' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_title'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_title'>".$msg['cms_editorial_form_title']."</label>
		</div>
		<div class='row'>
			<input type='text' name='cms_editorial_form_title' id='cms_editorial_form_title' value=\"!!cms_editorial_form_title!!\" class='saisie-80em'/>
		</div>
	</div>";

$cms_dojo_plugins_editor=
		" data-dojo-props=\"extraPlugins:[
			{name: 'pastefromword', width: '400px', height: '200px'},
			{name: 'insertTable', command: 'insertTable'},
		    {name: 'modifyTable', command: 'modifyTable'},
		    {name: 'insertTableRowBefore', command: 'insertTableRowBefore'},
		    {name: 'insertTableRowAfter', command: 'insertTableRowAfter'},
		    {name: 'insertTableColumnBefore', command: 'insertTableColumnBefore'},
		    {name: 'insertTableColumnAfter', command: 'insertTableColumnAfter'},
		    {name: 'deleteTableRow', command: 'deleteTableRow'},
		    {name: 'deleteTableColumn', command: 'deleteTableColumn'},
		    {name: 'colorTableCell', command: 'colorTableCell'},
		    {name: 'tableContextMenu', command: 'tableContextMenu'},
		    {name: 'resizeTableColumn', command: 'resizeTableColumn'},
			{name: 'fontName', plainText: true}, 
			{name: 'fontSize', plainText: true}, 
			{name: 'formatBlock', plainText: true},
			'foreColor','hiliteColor',
			'createLink','insertanchor', 'unlink', 'insertImage',
			'fullscreen',
			'viewsource'
			
		]\"	";

$cms_editorial_resume_field = "
	<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_resume'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label id='cms_editorial_form_resume_label' for='cms_editorial_form_resume'>".$msg['cms_editorial_form_resume']."</label>
		</div> 
		<div class='row'>
			<input type='hidden' name='cms_editorial_form_resume' value=''/>
			<textarea data-dojo-type='dijit/Editor' $cms_dojo_plugins_editor id='cms_editorial_form_resume_form' rows='5' class='saisie-80em' wrap='virtual'>!!cms_editorial_form_resume!!</textarea>
		</div>
        !!cms_editorial_resume_field_javascript!!
	</div>";
$cms_editorial_resume_field_no_dojo = "
	<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_resume'], ENT_QUOTES, $charset)."\">		
		<div class='row'>
			<label id='cms_editorial_form_resume_label' for='cms_editorial_form_resume'>".$msg['cms_editorial_form_resume']."</label>
		</div>
		<div class='row'>
			<textarea id='cms_editorial_form_resume' name='cms_editorial_form_resume'>!!cms_editorial_form_resume!!</textarea>
		</div>
        !!cms_editorial_resume_field_javascript!!
	</div>";

$cms_editorial_field_javascript = "
    <script type='text/javascript'>
        function addEventOnField() {
            let node = document.getElementById('!!field_id!!');
            if (node) {
                node.addEventListener('keydown', () => {
                    console.log(checkTextSize(node.value));
                });
            } else {
                setTimeout(function() {
                    let frame = document.getElementById('!!field_id!!_form_iframe');
                    let elt = frame.contentWindow.document.getElementById('dijitEditorBody');
					if(elt) {
	                    elt.addEventListener('keydown', () => {
	                        let limitNode = document.getElementById('!!field_id!!_size_limit');
	                        if (checkTextSize(elt.innerHTML) >= 16777215) {
	                            if (!limitNode) {
	                                let em = document.createElement('em');
	                                em.id = '!!field_id!!_size_limit';
	                                em.appendChild(document.createTextNode(' (" . $msg['cms_editorial_form_size_limit'] . ")'));
	                                document.getElementById('!!field_id!!_label').appendChild(em);
	                            }
	                        } else {
	                            if (limitNode) {
	                                limitNode.remove();
	                            }
	                        }
	                    });
					}
                }, 2000);
            }
        }

        function checkTextSize(string) {
            let escaped_string = encodeURI(string);
            let count = 0;
            if (escaped_string.indexOf('%') != -1) {
                count = escaped_string.split('%').length - 1;
                count = (count == 0 ? 1 : count);
                count = count + (escaped_string.length - (count * 3));
            } else {
                count = escaped_string.length;
            }
            return count;
        }

        addEventOnField();
    </script>
";

$cms_editorial_contenu_field = "
	<div id='el0Child_6' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_contenu'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label id='cms_editorial_form_contenu_label' for='cms_editorial_form_contenu'>".$msg['cms_editorial_form_contenu']."</label>
		</div> 
		<div class='row'>
			<input type='hidden' name='cms_editorial_form_contenu' value=''/>
			<div data-dojo-type='dijit/Editor' $cms_dojo_plugins_editor id='cms_editorial_form_contenu_form' rows='5' class='saisie-80em' wrap='virtual'>!!cms_editorial_form_contenu!!</div>
		</div>
	</div>";
$cms_editorial_contenu_field_no_dojo = "
	<div id='el0Child_6' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_contenu'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label id='cms_editorial_form_contenu_label' for='cms_editorial_form_contenu'>".$msg['cms_editorial_form_contenu']."</label>
		</div>
		<div class='row'>
			<textarea id='cms_editorial_form_contenu' name='cms_editorial_form_contenu'>!!cms_editorial_form_contenu!!</textarea>
		</div>
	</div>";


$cms_editorial_desc_field = "
	<div id='el0Child_7' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_desc'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_desc'>".$msg['cms_editorial_form_desc']."</label>
		</div>
		<div class='row'>
			!!cms_categs!!
			<div id='addcateg'></div>
		</div>
		<link href='./javascript/dojo/dojox/editor/plugins/resources/editorPlugins.css' type='text/css' rel='stylesheet' />
		<link href='./javascript/dojo/dojox/editor/plugins/resources/css/InsertEntity.css' type='text/css' rel='stylesheet' />
		<link href='./javascript/dojo/dojox/editor/plugins/resources/css/PasteFromWord.css' type='text/css' rel='stylesheet' />
		<link href='./javascript/dojo/dojox/editor/plugins/resources/css/InsertAnchor.css' type='text/css' rel='stylesheet' />
		<link href='./javascript/dojo/dojox/editor/plugins/resources/css/LocalImage.css' type='text/css' rel='stylesheet' />
		<link href='./javascript/dojo/dojox/form/resources/FileUploader.css' type='text/css' rel='stylesheet' />
		<script type='text/javascript'>
			dojo.require('dijit.Editor');
			dojo.require('dijit._editor.plugins.LinkDialog');
			dojo.require('dijit._editor.plugins.FontChoice');
			dojo.require('dijit._editor.plugins.TextColor');
			dojo.require('dijit._editor.plugins.FullScreen');
			dojo.require('dijit._editor.plugins.ViewSource');
			dojo.require('dojox.editor.plugins.InsertEntity');
			dojo.require('dojox.editor.plugins.TablePlugins');
			dojo.require('dojox.editor.plugins.ResizeTableColumn');
			dojo.require('dojox.editor.plugins.PasteFromWord');
			dojo.require('dojox.editor.plugins.InsertAnchor');
			dojo.require('dojox.editor.plugins.Blockquote');
			dojo.require('dojox.editor.plugins.LocalImage');
			function add_categ() {
		        template = document.getElementById('addcateg');
		        categ=document.createElement('div');
		        categ.className='row';
		
		        suffixe = eval('document.!!cms_editorial_form_name!!.max_categ.value')
		        nom_id = 'f_categ'+suffixe
		        f_categ = document.createElement('input');
		        f_categ.setAttribute('name',nom_id);
		        f_categ.setAttribute('id',nom_id);
		        f_categ.setAttribute('type','text');
		        f_categ.className='saisie-80emr';
		        f_categ.setAttribute('value','');
				f_categ.setAttribute('completion','categories_mul');
		        f_categ.setAttribute('autfield','f_categ_id'+suffixe);
		 
		        del_f_categ = document.createElement('input');
		        del_f_categ.setAttribute('id','del_f_categ'+suffixe);
		        del_f_categ.onclick=fonction_raz_categ;
		        del_f_categ.setAttribute('type','button');
		        del_f_categ.className='bouton';
		        del_f_categ.setAttribute('readonly','');
		        del_f_categ.setAttribute('value','$msg[raz]');
		
		        f_categ_id = document.createElement('input');
		        f_categ_id.name='f_categ_id'+suffixe;
		        f_categ_id.setAttribute('type','hidden');
		        f_categ_id.setAttribute('id','f_categ_id'+suffixe);
		        f_categ_id.setAttribute('value','');
		
		        categ.appendChild(f_categ);
		        space=document.createTextNode(' ');
		        categ.appendChild(space);
		        categ.appendChild(del_f_categ);
		        categ.appendChild(f_categ_id);
		
		        template.appendChild(categ);
		
// 		        tab_categ_order = document.getElementById('tab_categ_order');
// 				if (tab_categ_order.value != '') tab_categ_order.value += ','+suffixe;
		        document.!!cms_editorial_form_name!!.max_categ.value=suffixe*1+1*1 ;
		        ajax_pack_element(f_categ);
		    }
		    function fonction_selecteur_categ() {
		        name=this.getAttribute('id').substring(4);
		        name_id = name.substr(0,7)+'_id'+name.substr(7);
		        openPopUp('./select.php?what=categorie&caller=!!cms_editorial_form_name!!&p1='+name_id+'&p2='+name+'&dyn=1', 'selector_category');
		    }
		    function fonction_raz_categ() {
		        name=this.getAttribute('id').substring(4);
		        name_id = name.substr(0,7)+'_id'+name.substr(7);
		        document.getElementById(name_id).value=0;
		        document.getElementById(name).value='';
		    }    
		</script>
	</div>";

$cms_editorial_first_desc = "
    <div class='row'>
     	<input type='hidden' name='max_categ' value=\"!!max_categ!!\" />
        <input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />

        <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=categorie&caller='+this.form.name+'&p1=f_categ_id!!icateg!!&p2=f_categ!!icateg!!&dyn=1&parent=0&deb_rech=', 'selector_category')\" />
        <input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
		<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
        <input type='button' class='bouton' value='+' onClick=\"add_categ();\"/>
    </div>";
$cms_editorial_other_desc = "
    <div class='row'>
        <input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />

        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
        <input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
    </div>";

$cms_editorial_publication_state_field = "
	<div id='el0Child_8' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_publication_state'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_publication_state'>".$msg['cms_editorial_form_publication_state']."</label>
		</div> 
		<div class='row'>
			<select name='cms_editorial_form_publication_state' id='cms_editorial_form_publication_state'>
				!!cms_editorial_form_publications_states_options!!
			</select>
		</div>
	</div>";

$cms_editorial_dates_field = "
	<div id='el0Child_9' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_start_date']." / ".$msg['cms_editorial_form_end_date'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<div class='row'>
				<label for='cms_editorial_form_start_date'>".$msg['cms_editorial_form_start_date']."</label>
			</div> 
			<div class='row'>
				<input type='date' name='cms_editorial_form_start_date_value' id='cms_editorial_form_start_date_value'  value='!!cms_editorial_form_start_date_value!!' />
			</div>
		</div>
		<div class='row'>
			<div class='row'>
				<label for='cms_editorial_form_end_date'>".$msg['cms_editorial_form_end_date']."</label>
			</div> 
			<div class='row'>
				<input type='date' name='cms_editorial_form_end_date_value' id='cms_editorial_form_end_date_value'  value='!!cms_editorial_form_end_date_value!!' />
			</div>
		</div>
	</div>
";

$cms_editorial_type_field = "
	<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_type'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_type'>".$msg['cms_editorial_form_type']."</label>
		</div> 
		<div class='row'>
			<select name='cms_editorial_form_type' id='cms_editorial_form_type' backbone='yes' ".(!$cms_editorial_form_editables ? "onchange='cms_editorial_load_type_form(this.value, this);'" : "").">
				!!cms_editorial_form_type_options!!
			</select>
			<input type='hidden' id='cms_editorial_form_type_sel_index' name='cms_editorial_form_type_sel_index' value='' />
		</div>
		<div class='row'>&nbsp;</div>
		<script type='text/javascript'>
			if (dojo.byId('cms_editorial_form_obj_id').value != 0) {
				if (document.getElementById('cms_editorial_form_type_sel_index')) {
					document.getElementById('cms_editorial_form_type_sel_index').value = document.getElementById('cms_editorial_form_type').selectedIndex;
				}
			}
			dojo.require('dojox.layout.ContentPane');
			function cms_editorial_load_type_form(id, elem){
				if (dojo.byId('cms_editorial_form_obj_id').value != 0) {
					if (confirm(\"".$msg['cms_editorial_form_change_type_confirm']."\")) {
						document.getElementById('cms_editorial_form_type_sel_index').value = elem.selectedIndex;
						var content = dijit.byId('el9Child');
						content.href='./ajax.php?module=cms&categ=get_type_form&elem=!!type!!&type_id='+id+'&id='+dojo.byId('cms_editorial_form_obj_id').value;
						content.refresh();
					} else {
						elem.selectedIndex = document.getElementById('cms_editorial_form_type_sel_index').value;
					}	
				} else {
					var content = dijit.byId('el9Child');
					content.href='./ajax.php?module=cms&categ=get_type_form&elem=!!type!!&type_id='+id+'&id='+dojo.byId('cms_editorial_form_obj_id').value;
					content.refresh();
				}
				return true;
			}
		</script>
	</div>
";

$cms_editorial_obj_id_field = "
	<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_obj_id'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_obj_id'>".$msg['cms_editorial_form_obj_id']."</label>
			!!cms_editorial_form_obj_id!!
		</div>
		<div class='row'>&nbsp;</div>
	</div>
";


$cms_editorial_permalink_field = "
	<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg['cms_editorial_form_permalink'], ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label for='cms_editorial_form_permalink'>".$msg['cms_editorial_form_permalink']." : </label>
			!!cms_editorial_form_permalink!!
		</div>
		<div class='row'>&nbsp;</div>
	</div>";
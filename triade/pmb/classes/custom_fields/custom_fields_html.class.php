<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_html.class.php,v 1.3.2.1.2.1 2025/02/12 09:43:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_html extends custom_fields {
    
    public static function val($field, $value) {
        global $pmb_perso_sep;
        
        $value=format_output($field, $value);
        if (!$value) {
            $value=array();
        }
        return array("ishtml" => true, "value"=>implode($pmb_perso_sep,$value), "withoutHTML" =>implode($pmb_perso_sep,$value));
    }
    
    public static function aff($field,&$check_scripts) {
        global $msg;
        global $pmb_editorial_dojo_editor,$pmb_javascript_office_editor;
        global $cms_dojo_plugins_editor;
        global $categ,$current;
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        $afield_name = $field["ID"];
        $ret = "";
        
        $count = 0;
        if (!$values) {
            $values = array("");
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= get_js_function_dnd('html', $field['NAME']);
            if($pmb_javascript_office_editor && $options['HTMLEDITOR'][0]['value'])	{
                $use_editor_html = "1";
            } else {
                $use_editor_html = "0";
            }
            $ret.='<span style="vertical-align:top"><input class="bouton" type="button" value="+" onclick="add_custom_html_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\','.$options['HEIGHT'][0]['value'].','.$options['WIDTH'][0]['value'].','.$use_editor_html.'); "></span>';
        }
        foreach ($values as $avalues) {
            $display_temp = '';
            if($pmb_editorial_dojo_editor){
                $display_temp.="<input type='hidden' name='".$field['NAME']."[$count]' id='".$field['NAME']."_$count' data-form-name='".$field['NAME']."_' value=''/>";
                $display_temp.="<div data-dojo-type='dijit/Editor' $cms_dojo_plugins_editor	id='".$field['NAME']."_".$count."' class='saisie-80em' height='".$options['HEIGHT'][0]['value']."px' wrap='wrap' style='display:inline-block;width:".$options['WIDTH'][0]['value']."px'>".$avalues."</div>";
            } else {
				$display_temp .= "<div style='display: inline-block; width: 97%;'>";
                $display_temp .= "<textarea id='".$field['NAME']."_".$count."' name='".$field['NAME']."[]' data-form-name='".$field['NAME']."_' width='".$options['WIDTH'][0]['value']."px' height='".$options['HEIGHT'][0]['value']."px' wrap='wrap'>".$avalues."</textarea>";
                $display_temp .= "</div>";
				
				if ($pmb_javascript_office_editor && $options['HTMLEDITOR'][0]['value']) {
                    $mcename=$field['NAME']."_".$count;
                    $display_temp.= "<script type='text/javascript'>\n";
                    if (((($categ=="create_form")||($categ=="modif"))&&($current=="catalog.php"))
                        || ($categ == "get_type_form" && $current=="ajax.php")) {
                            $display_temp.="document.body.addEventListener('moveend', function(e) {";
                        }
                        $timeout = 1;
                        //Traitement plus long dans le contenu éditorial
                        if($categ == "get_type_form" && $current=="ajax.php") {
                            $timeout = 1500;
                        }
                        $display_temp.="
					if(typeof(tinyMCE)!= 'undefined') {
						setTimeout(function(){
							tinyMCE_execCommand('mceAddControl', true, '$mcename');
						},".$timeout.");
					}\n";
                        if (((($categ=="create_form")||($categ=="modif"))&&($current=="catalog.php"))
                            || ($categ == "get_type_form" && $current=="ajax.php")) {
                                $display_temp.="},true);
    				  document.body.addEventListener('movestart',function(e) {
    				  		if(typeof(tinyMCE)!= 'undefined') {
    					  		if (tinyMCE_getInstance('$mcename')) {
    								tinyMCE_execCommand('mceToggleEditor',true,'$mcename');
    								tinyMCE_execCommand('mceRemoveControl',true,'$mcename');
    							}
    						}
    				  },true);
    				  ";
                            }
							$display_temp.="
								// Evenement pour rafraichir le contenu de l'editeur et éviter les bugs d'affichage lors du drag & drop
								document.body.addEventListener('dragged_customfield_html_$mcename', function() {
									tinyMCE_execCommand('mceRemoveControl', true, '$mcename');
									tinyMCE_execCommand('mceAddControl', true, '$mcename');
								});
							";
                            $display_temp.="	</script>";
                }
            }
            if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
                $ret.=get_block_dnd('html', $field['NAME'], $count, $display_temp);
            } else {
                $ret.=$display_temp."<br / >";
            }
            $ret.="<br />";
            $count++;
        }
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.='<input id="customfield_text_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.(count($values)).'" />';
            $ret .= '<div id="spaceformorecustomfieldtext_'.$afield_name.'"></div>';
            $ret.=get_custom_dnd_on_add();
            if($pmb_editorial_dojo_editor){
                $ret.="<script>
			function add_custom_html_(field_id, field_name, field_height, field_width,use_html_editor) {
				document.getElementById('customfield_text_'+field_id).value = document.getElementById('customfield_text_'+field_id).value * 1 + 1;
				var count = document.getElementById('customfield_text_'+field_id).value;
                    
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfieldtext_'+field_id, 'customfield_html_'+field_name, (count-1));
                    
				var hid = document.createElement('input');
				hid.setAttribute('type','hidden');
				hid.setAttribute('name',field_name+'['+(count-1)+']');
				hid.setAttribute('id',field_name+'_'+(count-1));
				hid.setAttribute('value','');
                    
				var f_aut0 = document.createElement('div');
				f_aut0.setAttribute('data-dojo-type','dijit/Editor');
				f_aut0.setAttribute('class','saisie-80em');
				f_aut0.setAttribute('wrap','wrap');
				f_aut0.setAttribute('style','display:inline-block; width:'+field_width+'px');
                    
				var space=document.createElement('br');
                    
				document.getElementById(node_dnd_id).appendChild(hid);
				document.getElementById(node_dnd_id).appendChild(f_aut0);
				new dijit.Editor({id : field_name+'_'+(count-1), height : field_height+'px', extraPlugins:[
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
                    
				]}, f_aut0).startup();
				document.getElementById(node_dnd_id).appendChild(space);
				document.getElementById(node_dnd_id).appendChild(space);
                document.getElementById('customfield_text_'+field_id).value = count++;
			}
			</script>";
                $caller = get_form_name();
                $check_scripts.= "
			var i = 0;
			while(document.forms['".$caller."'].elements['".$field['NAME']."['+i+']']){
				if(dijit.byId('".$field['NAME']."_'+i).get('value') && (dijit.byId('".$field['NAME']."_'+i).get('value') != '<br _moz_editor_bogus_node=\"TRUE\" />') && (dijit.byId('".$field['NAME']."_'+i).get('value') != '<br />')) {
					document.forms['".$caller."'].elements['".$field['NAME']."['+i+']'].value = dijit.byId('".$field['NAME']."_'+i).get('value');
				}
				i++;
			}";
            } else {
                $ret.= get_custom_dnd_on_add();
                $ret.="<script>
			function add_html_editor(field_name,ind) {
				if(typeof(tinyMCE)!= 'undefined') {
					tinyMCE_execCommand('mceAddControl', true, field_name+'_'+ind);
				}
			}
			function add_custom_html_(field_id, field_name, field_height, field_width, use_html_editor) {
				document.getElementById('customfield_text_'+field_id).value = document.getElementById('customfield_text_'+field_id).value * 1 + 1;
				var count = document.getElementById('customfield_text_'+field_id).value;
                    
				var node_dnd_id = get_custom_dnd_on_add('spaceformorecustomfieldtext_'+field_id, 'customfield_html_'+field_name, (count-1));
                var ind = field_name+'_'+(count-1)

				var f_aut0 = document.createElement('textarea');
				f_aut0.setAttribute('name',field_name+'[]');
				f_aut0.setAttribute('id', ind);
				f_aut0.setAttribute('wrap','wrap');
				f_aut0.setAttribute('width',field_width+'px');
				f_aut0.setAttribute('height',field_height+'px');

				var parentDiv = document.createElement('div');
				parentDiv.setAttribute('style','display: inline-block; width: 97%;');
				parentDiv.appendChild(f_aut0);

				// Evenement pour rafraichir le contenu de l'editeur et éviter les bugs d'affichage lors du drag & drop
				document.body.addEventListener('dragged_customfield_html_' + ind, function() {
					tinyMCE_execCommand('mceRemoveControl', true, ind);
					tinyMCE_execCommand('mceAddControl', true, ind);
				});
                    
				document.getElementById(node_dnd_id).appendChild(parentDiv);
				document.getElementById(node_dnd_id).appendChild(document.createElement('br'));
				document.getElementById(node_dnd_id).appendChild(document.createElement('br'));
				if (use_html_editor) {
					add_html_editor(field_name,(count-1));
				}
                document.getElementById('customfield_text_'+field_id).value = count++;
			}
			</script>";
                $check_scripts.= "
				var elts_cnt = 0;
				if (document.getElementsByName('".$field['NAME']."[]').length) {
					elts_cnt = document.getElementsByName('".$field['NAME']."[]').length;
				}
				if (elts_cnt) {
					if(typeof(tinyMCE)!= 'undefined') {
						for (var i = 0; i < elts_cnt; i++) {
							tinyMCE_execCommand('mceToggleEditor',true,'".$field['NAME']."_'+i);
							tinyMCE_execCommand('mceRemoveControl',true,'".$field['NAME']."_'+i);
						}
					}
				}
			";
            }
        } else {
            if($pmb_editorial_dojo_editor){
                $caller = get_form_name();
                $check_scripts.= "
			var i = 0;
			while(document.forms['".$caller."'].elements['".$field['NAME']."['+i+']']){
				if(dijit.byId('".$field['NAME']."_'+i).get('value') && (dijit.byId('".$field['NAME']."_'+i).get('value') != '<br _moz_editor_bogus_node=\"TRUE\" />') && (dijit.byId('".$field['NAME']."_'+i).get('value') != '<br />')) {
				    document.forms['".$caller."'].elements['".$field['NAME']."['+i+']'].value = dijit.byId('".$field['NAME']."_'+i).get('value');
				}
				i++;
			}";
            } else {
                $check_scripts.= "
				var elts_cnt = 0;
				if (document.getElementsByName('".$field['NAME']."[]').length) {
					elts_cnt = document.getElementsByName('".$field['NAME']."[]').length;
				}
				if (elts_cnt) {
					if(typeof(tinyMCE)!= 'undefined') {
						for (var i = 0; i < elts_cnt; i++) {
							tinyMCE_execCommand('mceToggleEditor',true,'".$field['NAME']."_'+i);
							tinyMCE_execCommand('mceRemoveControl',true,'".$field['NAME']."_'+i);
						}
					}
				}
			";
            }
        }
        if ($field['MANDATORY']==1) {
            $caller = get_form_name();
            $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
        }
        return $ret;
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $field['NAME'];
    }
}
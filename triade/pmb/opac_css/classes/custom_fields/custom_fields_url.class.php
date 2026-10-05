<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: custom_fields_url.class.php,v 1.4.2.1.2.1 2025/02/12 09:59:54 dgoron Exp $

use Pmb\Common\Library\CSRF\CollectionCSRF;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class custom_fields_url extends custom_fields {
	
    protected static function get_chk_values($name) {
        global ${$name};
        $val = ${$name};
        $value = array();
        if (!empty($val['link']) && is_array($val['link'])) {
            $nb_vals = count($val['link']);
            for ($i = 0; $i < $nb_vals; $i++) {
                if ($val['link'][$i] != "") {
                    $linktarget = '|0';
                    if (!empty($val['linktarget'][$i])) {
                        $linktarget = '|1';
                    }
                    $value[] = $val['link'][$i]."|".$val['linkname'][$i].$linktarget;
                }
            }
        }
        return $value;
    }
    
    public static function val($field, $value) {
        global $charset,$pmb_perso_sep;
        $cut = $field['OPTIONS'][0]['MAXSIZE'][0]['value'];
        $values=format_output($field,$value);
        $ret = "";
        $without = "";
        $details = array();
        for ($i=0;$i<count($values);$i++){
            $val = explode("|",$values[$i]);
            if (isset($val[1]) && $val[1])$lib = $val[1];
            else $lib = ($cut && strlen($val[0]) > $cut ? substr($val[0],0,$cut)."[...]" : $val[0] );
            if( $ret != "") $ret.= $pmb_perso_sep;
            $target = '_blank';
            if (isset($val[2]) && ($val[2] == 0)) {
                $target = '_self';
            }
            $ret .= "<a href='".$val[0]."' target='".$target."'>".htmlentities($lib, ENT_QUOTES, $charset)."</a>";
            if( $without != "") $without.= $pmb_perso_sep;
            $without .= $val[0];
            $details[] = array('url' => $val[0], 'label' => $lib, 'target' => $target);
        }
        return array("ishtml" => true, "value"=>$ret, "withoutHTML" =>$without, "details" => $details);
    }
    
    public static function aff($field,&$check_scripts) {
        global $charset;
        global $msg;
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        $afield_name = $field["ID"];
        $ret = "";
        $count = 0;
        if (!$values) {
            $linktarget_default_checked = (isset($options['LINKTARGET'][0]['value']) && $options['LINKTARGET'][0]['value'] ? 1 : 0);
            $values = array("||".$linktarget_default_checked);
        }
        if(isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret .= get_js_function_dnd('url', $field['NAME']);
            $ret.="<input class='bouton' type='button' value='+' onclick=\"add_custom_url_('$afield_name', '".addslashes($field['NAME'])."', '".addslashes($options['SIZE'][0]['value'])."')\">";
        }
        foreach ($values as $avalues) {
            $avalues = explode("|",$avalues);
            $ret.="<div id='".$field['NAME']."_check_$count' style='display:inline'></div>";
            $ret.= $msg['persofield_url_link']."<input id='".$field['NAME']."_link".$count."' type='text' class='saisie-30em' name='".$field['NAME']."[link][".$count."]' data-form-name='".$field['NAME']."_link' onchange='cp_chklnk_".$field["NAME"]."(".$count.",this);' value='".htmlentities($avalues[0],ENT_QUOTES,$charset)."'>";
            $ret.=" <input class=\"bouton\" type='button' value='".$msg['persofield_url_check']."' onclick='cp_chklnk_".$field["NAME"]."(".$count.",this);'>";
            //$ret.="<br />";
            $ret.="&nbsp;".$msg['persofield_url_linklabel']."<input id='".$field['NAME']."_linkname".$count."' type='text' class='saisie-15em' size='".$options['SIZE'][0]['value']."' name='".$field['NAME']."[linkname][".$count."]' data-form-name='".$field['NAME']."_linkname' value='".htmlentities($avalues[1],ENT_QUOTES,$charset)."'>";
            $target_checked = 'checked="checked"';
            if (isset($avalues[2]) && ($avalues[2] == 0)) {
                $target_checked = '';
            }
            $ret.="&nbsp;<input id='".$field['NAME']."_linktarget".$count."' type='checkbox' name='".$field['NAME']."[linktarget][".$count."]' data-form-name='".$field['NAME']."_linktarget' value='1' ".$target_checked."><label for='".$field['NAME']."_linktarget".$count."'>&nbsp;".$msg['persofield_url_linktarget']."</label>&nbsp;";
            if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value'] && !$count)
                $ret.="<input class='bouton' type='button' value='+' onclick=\"add_custom_url_('$afield_name', '".addslashes($field['NAME'])."', '".addslashes($options['SIZE'][0]['value'])."')\">";
                $ret.="<br />";
                $count++;
        }
        $collectionCSRF = new CollectionCSRF();
        $ret.= "
	<script>
        const tabTokens_".$field['NAME']." = " . json_encode($collectionCSRF->getArrayTokens()) . ";
		function cp_chklnk_".$field["NAME"]."(indice,element){
			var link = element.form.elements['".$field['NAME']."[link]['+indice+']'];
			if(link.value != ''){
				var wait = document.createElement('img');
				wait.setAttribute('src','".get_url_icon('patience.gif')."');
				wait.setAttribute('align','top');
				while(document.getElementById('".$field['NAME']."_check_'+indice).firstChild){
					document.getElementById('".$field['NAME']."_check_'+indice).removeChild(document.getElementById('".$field['NAME']."_check_'+indice).firstChild);
				}
				document.getElementById('".$field['NAME']."_check_'+indice).appendChild(wait);
                var csrf_token = tabTokens_".$field['NAME']."[0];
                tabTokens_".$field['NAME'].".splice(0, 1);
				var testlink = encodeURIComponent(link.value);
	 			var check = new http_request();
				if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&timeout=".$options['TIMEOUT'][0]['value']."&link='+testlink+'&csrf_token='+csrf_token)){
					alert(check.get_text());
				}else{
					var result = check.get_text();
					var type_status=result.substr(0,1);
					var img = document.createElement('img');
					var src='';
			    	if(type_status == '2' || type_status == '3'){
						if((link.value.substr(0,7) != 'http://') && (link.value.substr(0,8) != 'https://')) link.value = 'http://'+link.value;
						//impec, on print un petit message de confirmation
						src = '".get_url_icon('tick.gif')."';
					}else{
						//problème...
						src = '".get_url_icon('error.png')."';
						img.setAttribute('style','height:1.5em;');
					}
					img.setAttribute('src',src);
					img.setAttribute('align','top');
					while(document.getElementById('".$field['NAME']."_check_'+indice).firstChild){
						document.getElementById('".$field['NAME']."_check_'+indice).removeChild(document.getElementById('".$field['NAME']."_check_'+indice).firstChild);
					}
					document.getElementById('".$field['NAME']."_check_'+indice).appendChild(img);
				}
			}
		}
	</script>";
        if (isset($options['REPEATABLE'][0]['value']) && $options['REPEATABLE'][0]['value']) {
            $ret.='<input id="customfield_text_'.$afield_name.'" type="hidden" name="customfield_text_'.$afield_name.'" value="'.($count).'">';
            //$ret.='<input class="bouton" type="button" value="+" onclick="add_custom_text_(\''.$afield_name.'\', \''.addslashes($field['NAME']).'\', \''.addslashes($options['SIZE'][0]['value']).'\', \''.addslashes($options['MAXSIZE'][0]['value']).'\')">';
            $ret .= '<div id="spaceformorecustomfieldtext_'.$afield_name.'"></div>';
            $ret.=get_custom_dnd_on_add();
            $ret.="<script>
			function add_custom_url_(field_id, field_name, field_size) {
				cpt = document.getElementById('customfield_text_'+field_id).value;
				var check = document.createElement('div');
				check.setAttribute('id',field_name+'_check_'+cpt);
				check.setAttribute('style','display:inline');
				var link_label = document.createTextNode('".$msg['persofield_url_link']."');
				var chklnk = document.createElement('input');
				chklnk.setAttribute('type','button');
				chklnk.setAttribute('value','".$msg['persofield_url_check']."');
				chklnk.setAttribute('class','bouton');
				chklnk.setAttribute('onclick','cp_chklnk_'+field_name+'('+cpt+',this);');
				document.getElementById('customfield_text_'+field_id).value = cpt*1 +1;
				var link = document.createElement('input');
		        link.setAttribute('name',field_name+'[link][]');
		        link.setAttribute('id',field_name+'_link'+cpt);
		        link.setAttribute('type','text');
				link.setAttribute('class','saisie-30em');
		        link.setAttribute('size',field_size);
		        link.setAttribute('value','');
				link.setAttribute('onchange','cp_chklnk_'+field_name+'('+cpt+',this);');
				var lib_label = document.createTextNode('".$msg['persofield_url_linklabel']."');
				var lib = document.createElement('input');
		        lib.setAttribute('name',field_name+'[linkname][]');
		        lib.setAttribute('id',field_name+'_linkname'+cpt);
		        lib.setAttribute('type','text');
				lib.setAttribute('class','saisie-15em');
		        lib.setAttribute('size',field_size);
		        lib.setAttribute('value','');
				var target = document.createElement('input');
				target.setAttribute('name',field_name+'[linktarget][]');
		        target.setAttribute('id',field_name+'_linktarget'+cpt);
		        target.setAttribute('type','checkbox');
		        target.setAttribute('value','1');
		        target.setAttribute('checked','checked');
				var targetlabel = document.createElement('label');
				targetlabel.setAttribute('for',field_name+'_linktarget'+cpt);
				targetlabel.innerHTML = ' ".$msg['persofield_url_linktarget']."';
		        space=document.createElement('br');
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(check);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(link_label);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(link);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(chklnk);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(lib_label);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(lib);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(document.createTextNode(' '));
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(target);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(targetlabel);
				document.getElementById('spaceformorecustomfieldtext_'+field_id).appendChild(space);
			}
		</script>";
        }
        if ($field['MANDATORY']==1) {
            $caller = get_form_name();
            $check_scripts.="if (document.forms[\"".$caller."\"].elements[\"".$field['NAME']."[]\"].value==\"\") return cancel_submit(\"".sprintf($msg["parperso_field_is_needed"],$field['ALIAS'])."\");\n";
        }
        return $ret;
    }
    
    public static function aff_search($field,&$check_scripts,$varname) {
        global $charset;
        
        $options=$field['OPTIONS'][0];
        $values=$field['VALUES'];
        if(!isset($values[0])) {
            $values[0] = '';
        }
        $ret="<input id=\"".$varname."\" type=\"text\" size=\"".$options['SIZE'][0]['value']."\" name=\"".$varname."[]\" value=\"".htmlentities($values[0],ENT_QUOTES,$charset)."\">";
        return $ret;
    }
    
    public static function get_rgaa_label($field, $varname) {
        return $field['NAME']."_linkname0";
    }
}
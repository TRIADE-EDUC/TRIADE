<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailtpl.tpl.php,v 1.25 2023/09/02 07:27:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $mailtpl_attachments_form_tpl, $msg, $mailtpl_form_resavars, $mailtpl_form_selvars, $mailtpl_form_sel_img, $mailtpl_js_content_form;

$mailtpl_form_resavars = "
	<select name='resavars_id' id='resavars_id'>
		<option value=!!new_date!!>".$msg["scan_request_date"]."</option>
		<option value=!!expl_title!!>".$msg["233"]."</option>
		<option value=!!record_permalink!!>".$msg["cms_editorial_form_permalink"]."</option>
	</select>
	<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_selvars_insert"]." \" onClick=\"insert_vars(document.getElementById('resavars_id'), document.getElementById('f_message')); return false; \" />
		";

$mailtpl_form_selvars="
<select name='selvars_id' id='selvars_id'>
    !!options_selvars!!
</select>
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_selvars_insert"]." \" onClick=\"insert_vars(document.getElementById('selvars_id'), document.getElementById('f_message')); return false; \" />
<script type='text/javascript'>

	function insert_vars(theselector,dest){	
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;

		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+selvars+end_text;
		}else{
			tinyMCE_execCommand('mceInsertContent',false,selvars);
		}
	}
	
	
</script>
";

$mailtpl_form_sel_img="
!!select_file!!
<input type='button' class='bouton' value=\" ".$msg["admin_mailtpl_form_sel_img_insert"]." \" onClick=\"insert_img(document.getElementById('select_file'), document.getElementById('f_message')); return false; \" />
<script type='text/javascript'>
	function insert_img(theselector,dest){	
		var href='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				href=theselector.options[i].value ;
				break;
			}
		}
		if(!href) return ;
		
		var sel_img='<img src=\"'+href+'\">';
		if(typeof(tinyMCE)== 'undefined'){			
			var start = dest.selectionStart;		   
		    var start_text = dest.value.substring(0, start);
		    var end_text = dest.value.substring(start);
		    dest.value = start_text+sel_img+end_text;
		}else{
			tinyMCE_execCommand('InsertHTML',false,sel_img);
		}
	}

</script>
";

$mailtpl_js_content_form="	
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_mailtpl_name_error"]."');
			return false;
		}
        if (typeof(tinyMCE) != 'undefined') {
            if (tinyMCE_getInstance('f_message')) {
                tinyMCE_execCommand('mceToggleEditor',true,'f_message');
                tinyMCE_execCommand('mceRemoveControl',true,'f_message');
            }
        }
		return true;
	}
</script>
";
		
$mailtpl_attachments_form_tpl="
<div class='row'>
	<label class='etiquette' >".$msg["empr_mailing_form_message_piece_jointe"]." (".ini_get('upload_max_filesize').")</label>
</div>
<div id='add_pieces'>
	<input type='hidden' id='nb_piece' value='1'/>
	<div class='row' id='piece_1'>
		<input type='file' id='pieces_jointes_mailing_1' name='pieces_jointes_mailing[]' class='saisie-80em' size='60'/><input class='bouton' type='button' value='X' onclick='document.getElementById(\"pieces_jointes_mailing_1\").value=\"\"'/>
		<input class='bouton' type='button' value='+' onClick=\"add_pieces_jointes_mailing();\"/>
	</div>
</div>
<script type='text/javascript'>
	function add_pieces_jointes_mailing(){
		var nb_piece=document.getElementById('nb_piece').value;
		nb_piece= (nb_piece*1) + 1;
		
		var template = document.getElementById('add_pieces');
		
		var divpiece=document.createElement('div');
   		divpiece.className='row';
   		divpiece.setAttribute('id','piece_'+nb_piece);
   		template.appendChild(divpiece);
   		document.getElementById('nb_piece').value=nb_piece;
   		
   		var inputfile=document.createElement('input');
   		inputfile.setAttribute('type','file');
   		inputfile.setAttribute('name','pieces_jointes_mailing[]');
   		inputfile.setAttribute('id','pieces_jointes_mailing_'+nb_piece);
   		inputfile.setAttribute('class','saisie-80em');
   		inputfile.setAttribute('size','60');
   		divpiece.appendChild(inputfile);
   		
   		var inputfile=document.createElement('input');
   		inputfile.setAttribute('type','button');
   		inputfile.setAttribute('value','X');
   		inputfile.setAttribute('onclick','del_pieces_jointes_mailing('+nb_piece+');');
   		inputfile.setAttribute('class','bouton');
   		divpiece.appendChild(inputfile);
	}
	
	function del_pieces_jointes_mailing(nb_piece){
		var parent = document.getElementById('add_pieces');
		var child = document.getElementById('piece_'+nb_piece);
		parent.removeChild(child);
		
		var nb_piece=document.getElementById('nb_piece').value;
		nb_piece= (nb_piece*1) - 1;
		document.getElementById('nb_piece').value=nb_piece;
		
	}
</script>";

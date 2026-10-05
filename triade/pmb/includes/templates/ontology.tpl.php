<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ontology.tpl.php,v 1.5 2019/05/27 12:54:49 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl, $msg;

$ontology_tpl['list'] = "
<br />
<div class='row'>
	<!-- nb_results -->
</div>
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<div class='row'>
	<table class='sortable'>
	<thead>
		<tr><th>!!list_header!!</th></tr>
	</thead>
	<tbody>
		<!-- rows -->
	</tbody>
	</table>
</div>
<!-- pagination -->
<!-- add_button -->
";

$ontology_tpl['even_row'] = "
<tr title='!!row_title!!' class='even' style='cursor:pointer' !!edit_url!! onmouseout=\"this.className='even';\" onmouseover=\"this.className='surbrillance';\" ><td>!!row_content!!</td></tr>
";

$ontology_tpl['odd_row'] = "
<tr title='!!row_title!!' class='odd' style='cursor:pointer' !!edit_url!! onmouseout=\"this.className='odd';\" onmouseover=\"this.className='surbrillance';\" ><td>!!row_content!!</td></tr>
";

$ontology_tpl['edit_js'] = " onmousedown=\"document.location='!!edit_url!!';\" ";

$ontology_tpl['nb_results'] = "
<h3>!!nb_found!!".$msg[173]." !!cle!!</h3>
";

$ontology_tpl['add_button'] = "
<div class='row'><div class='left'>
	<input type='button' class='bouton' onclick=\"document.location='!!add_url!!';\" value='!!add_msg!!' />
</div></div> 
";

$ontology_tpl['pagination'] = "
<div class='row'><!-- pagination --></div>
";

$ontology_tpl['form'] = "
<br />
<form class='form-$current_module' name='ontology_object_form' id='ontology_object_form' method='post' action='!!action!!' onSubmit='return false;' >
<h3><label id='form_title'>!!form_title!!</label></h3>
<div class='form-contenu'>
	<!-- fields -->
</div>
	<!-- buttons -->
<div class='row'></div>
</form>
";

$ontology_tpl['buttons'] = "
<div class='row'>
	<div class='left'>
		<!-- cancel_button --><!-- rec_button -->
	</div>
	<div class='right'>
		<!-- del_button -->
	</div>
</div>
";

$ontology_tpl['cancel_button'] = "
<input type='button' class='bouton' onclick=\"unload_off();document.location='!!cancel_url!!';\" value='".$msg[76]."' />
";

$ontology_tpl['rec_button'] = "
<input type='button' class='bouton' onclick=\"\" value='".$msg[77]."' />
";

$ontology_tpl['del_button'] = "
<input type='button' class='bouton' onclick=\"\" value='".$msg[63]."' />
";

$ontology_tpl['object_uri'] = "
<div class='row'>
	<input class='saisie-80em' id='object_uri' name='object_uri' type='text' value='!!object_uri!!' />
	<input id='old_object_uri' name='old_object_uri' type='hidden' value='!!raw_object_uri!!' />
</div>
";

$ontology_tpl['label'] = "
<div class='row'>
	<label class='etiquette' !!for_id!! >!!label!!</label>
</div>
";

$ontology_tpl['p_div'] = "
<div id='div_ontology_!!fname!!_!!lang!!' >
	<!-- p_content -->
</div>
";


$ontology_tpl['small_text'] = "
<div class='row'>
	<input type='text' id='!!fname!!_!!lang!!_!!index!!' name='!!fname!![!!lang!!][!!index!!]' class='saisie-80em' value='!!value!!' /><!-- p_del_button --><!-- p_add_button --><!-- lang -->
</div>
";

$ontology_tpl['text'] = "
<div class='row'>
	<textarea id='!!fname!!_!!lang!!_!!index!!' name='!!fname!![!!lang!!][!!index!!]' wrap='virtual' rows='4' cols='80'>!!value!!</textarea><!-- p_del_button --><!-- p_add_button --><!-- lang -->
</div>
";

$ontology_tpl['p_add_button'] = "
<input type='button' class='bouton_small' onclick=\"ontology_add_!!fname!!_!!lang!!(!!index!!);\" value='".$msg['ontology_p_add_button']."' />
<input type='hidden' id='nb_!!fname!!_!!lang!!' value='!!nb!!' />
";

$ontology_tpl['p_del_button'] = "
<input type='button' class='bouton_small' onclick=\"ontology_del_!!fname!!_!!lang!!(!!index!!);\" value='".$msg['ontology_p_del_button']."' />
";

$ontology_tpl['p_script'] = " 
<script type='text/javascript'>

function ontology_del_!!fname!!_!!lang!!(index) {

	try {
		var x = document.getElementById('!!fname!!_!!lang!!_'+index);
		x.value='';
	} catch(e){}
}

function ontology_add_!!fname!!_!!lang!!(index) {

	try {
	
		var tpl = document.getElementById('div_ontology_!!fname!!_!!lang!!');
		var nb = document.getElementById('nb_!!fname!!_!!lang!!');
		if(nb) {
			
			var x = document.getElementById('!!fname!!_!!lang!!_'+index);
			var new_index = nb.value*1;
			
			var r = document.createElement('div');
			r.className='row';
			
			switch (x.type) {
			
				case 'text' :
				
					var i = document.createElement('input');
					i.setAttribute('type','text');
					i.setAttribute('name','!!fname!![!!lang!!]['+(new_index)+']');
					i.setAttribute('id','!!fname!!_!!lang!!_'+(new_index));
					i.setAttribute('value','');
					i.className='saisie-80em'; 
			
					var sp = document.createTextNode(' ');
					
					var bt = document.createElement('input');
					bt.setAttribute('type','button');
					bt.setAttribute('value', '".$msg['ontology_p_del_button']."');
					bt.onclick=function(){ontology_del_!!fname!!_!!lang!!(new_index);};
					bt.className='bouton_small'; 
		
					r.appendChild(i);
					r.appendChild(bt);
					r.appendChild(sp);
					
					break;
					
				case 'textarea' :
				
					var i = document.createElement('textarea');
					i.setAttribute('name','!!fname!![!!lang!!]['+(new_index)+']');
					i.setAttribute('id','!!fname!!_!!lang!!_'+(new_index));
					i.setAttribute('type','textarea');
					i.setAttribute('cols',80);
					i.setAttribute('rows',4);
					i.setAttribute('wrap','virtual');
					i.setAttribute('value','');
					
					var sp = document.createTextNode(' ');
					
					var bt = document.createElement('input');
					bt.setAttribute('type','button');
					bt.setAttribute('value', '".$msg['ontology_p_del_button']."');
					bt.onclick=function(){ontology_del_!!fname!!_!!lang!!(new_index);};
					bt.className='bouton_small'; 
		
					r.appendChild(i);
					r.appendChild(bt);
					r.appendChild(sp);
					
					break;
			}

			tpl.appendChild(r);
			nb.value=(new_index*1)+1;
			
		}
			
	} catch(e) {}
}

</script>
";

$ontology_tpl['object_p_div'] = "
<div id='div_ontology_!!fname!!' >
<!-- p_content -->
</div>
";

$ontology_tpl['object'] = "
<div class='row'>
<input type='text' id='!!fname!!_!!index!!' name='!!fname!![!!index!!]' class='saisie-80emr' value='!!value!!' aut_field='f_!!fname!!_!!index!!' completion='!!range!!' /><!-- p_sel_button --><!-- p_del_button --><!-- p_add_button -->
<input type='hidden' id='f_!!fname!!_!!index!!' name='f_!!fname!![!!index!!]' value='!!raw_value!!' />
</div>
";

$ontology_tpl['object_p_add_button'] = "
<input type='button' class='bouton_small' onclick=\"ontology_add_!!fname!!();\" value='".$msg['ontology_p_add_button']."' />
<input type='hidden' id='nb_!!fname!!' value='!!nb!!' />
";

$ontology_tpl['object_p_del_button'] = "
<input type='button' class='bouton_small' onclick=\"ontology_del_!!fname!!(!!index!!);\" value='".$msg['ontology_p_del_button']."' />
";

$ontology_tpl['object_p_sel_button'] = "
<input type='button' class='bouton_small' onclick=\"ontology_sel_!!fname!!(!!index!!);\" value='".$msg['ontology_p_sel_button']."' />
";

$ontology_tpl['object_p_script'] = "
<script type='text/javascript'>

//Effacer
function ontology_del_!!fname!!(index) {

	try {
		var x = document.getElementById('!!fname!!_'+index);
		x.value='';
		var y = document.getElementById('f_!!fname!!_'+index);
		y.value='';
	} catch(e){}
}


//Ajouter
function ontology_add_!!fname!!() {
	
	try {
	
		var tpl = document.getElementById('div_ontology_!!fname!!');
		var nb = document.getElementById('nb_!!fname!!');
		if(nb) {
				
			var new_index = nb.value*1;
				
			var r = document.createElement('div');
			r.className='row';
				
			var i = document.createElement('input');
			i.setAttribute('type','text');
			i.setAttribute('name','!!fname!!['+(new_index)+']');
			i.setAttribute('id','!!fname!!_'+(new_index));
			i.setAttribute('completion','!!raw_range!!');
			i.setAttribute('aut_field','f_!!fname!!_'+(new_index));
			i.setAttribute('value','');
			i.className='saisie-80emr';
			
			var hi = document.createElement('input');
			hi.setAttribute('type','hidden');
			hi.setAttribute('name','f_!!fname!!['+(new_index)+']');
			hi.setAttribute('id','f_!!fname!!_'+(new_index));
			hi.setAttribute('value','');
			
			var sp = document.createTextNode(' ');
				
			var bt_del = document.createElement('input');
			bt_del.setAttribute('type','button');
			bt_del.setAttribute('value', '".$msg['ontology_p_del_button']."');
			bt_del.onclick=function(){ontology_del_!!fname!!(new_index);};
			bt_del.className='bouton_small';
			
			var sp1 = document.createTextNode(' ');
				
			/*var bt_sel = document.createElement('input');
			bt_sel.setAttribute('type','button');
			bt_sel.setAttribute('value', '".$msg['ontology_p_sel_button']."');
			bt_sel.onclick=function(){ontology_sel_!!fname!!(new_index);};
			bt_sel.className='bouton_small';*/
			
			r.appendChild(i);
			r.appendChild(sp);
			r.appendChild(bt_del);
			r.appendChild(sp1);
			//r.appendChild(bt_sel);
			//r.appendChild(sp1);
			r.appendChild(hi);
			
			tpl.appendChild(r);
			nb.value=(new_index*1)+1;
		}
		
	} catch(e) {}
}

//Ouverture du popup de recherche
function ontology_sel_!!fname!!(index) {

	try {
		var objs = '!!raw_range!!';
		var code = 'f_!!fname!!_'+index;
		var label = '!!fname!!_'+index;
		var cpt = 'nb_!!fname!!_';
		
		var deb_rech=document.getElementById(label).value;
		openPopUp(\"select.php?what=ontology&caller=ontology_object_form&objs=\"+objs+\"&code=\"+code+\"&label=\"+label+\"&cpt=\"+cpt+\"&dyn=1&deb_rech=\"+".pmb_escape()."(deb_rech), 'select_object', 500, 400, 0, 0, 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes');
		return false;
	
	} catch(e){}
}

</script>
";

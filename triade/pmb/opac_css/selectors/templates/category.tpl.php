<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.tpl.php,v 1.5 2017-11-07 15:54:20 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $dyn, $infield;
global $msg;
global $p1, $p2;
global $add_field, $field_id, $field_name_id, $max_field;

// templates du sélecteur catégories

//	----------------------------------
// $categ_browser : template du browser de catégories
//	----------------------------------
$categ_browser = "
<div class='row'>
	!!browser_top!!
	</div>
<div class='row'>
	<div class='left'>!!browser_header!!</div>";
$categ_browser .= "</div>
<div class='row'>
		<table style='border:0px'>
			!!browser_content!!
		</table>
</div>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

// permet de passer dans l'url de selection des noms de champs autres
if ($dyn==4) {
/* $dyn = 4 pour les vedettes composées
 */
	$jscript_ ="
<script type='text/javascript'>
	function set_parent_w(f_caller, id_value, libelle_value,w,callback,id_thesaurus){
		w.opener.document.forms[f_caller].elements['$p1'].value=id_value;
		w.opener.document.forms[f_caller].elements['$p2'].value=reverse_html_entities(libelle_value);
		
		if(callback)
			w.opener[callback]('$infield');
		closeCurrentEnv();
	}
</script>";
} else if ($dyn==3) {
/* pour $dyn=3, renseigner les champs suivants: (passé dans l'url)
 *
* $max_field : nombre de champs existant
* $field_id : id de la clé
* $field_name_id : id  du champ text
* $add_field : nom de la fonction permettant de rajouter un champ
*
*/
	$jscript_ ="
<script type='text/javascript'>
	function set_parent_w(f_caller, id_value, libelle_value,w,callback,id_thesaurus){
		var i=0;
		if(!(typeof w.opener.$add_field == 'function')) {
			w.opener.document.getElementById('$field_id').value = id_value;
			w.opener.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
			closeCurrentEnv();
			return;
		}
		var n_element=w.opener.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		var multiple=1;
		
		//Vérification que l'élément n'est pas déjà sélectionnée
		for (var i=0; i<n_element; i++) {
			if (w.opener.document.getElementById('$field_id'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
		if (flag) {
			for (var i=0; i<n_element; i++) {
					
				if ((w.opener.document.getElementById('$field_id'+i).value==0)||(w.opener.document.getElementById('$field_id'+i).value=='')) break;
			}
		
			if (i==n_element && (typeof w.opener.$add_field == 'function')) w.opener.$add_field();
			w.opener.document.getElementById('$field_id'+i).value = id_value;
			w.opener.document.getElementById('$field_name_id'+i).value = reverse_html_entities(libelle_value);
		}	
	}
</script>";
}else
$jscript_ = "
<script type='text/javascript'>
<!--
function set_parent_w(f_caller, id_value, libelle_value,w,callback,id_thesaurus){
	dyn='$dyn';
	if(dyn==2) { // Pour les liens entre autorités
		n_aut_link=w.opener.document.forms[f_caller].elements['max_aut_link'].value;
		flag = 1;	
		//Vérification que l'autorité n'est pas déjà sélectionnée
		for (i=0; i<n_aut_link; i++) {
			if (w.opener.document.getElementById('f_aut_link_id'+i).value==id_value && w.opener.document.getElementById('f_aut_link_table'+i).value==$p1) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}	
		if (flag) {
			for (i=0; i<n_aut_link; i++) {
				if ((w.opener.document.getElementById('f_aut_link_id'+i).value==0)||(w.opener.document.getElementById('f_aut_link_id'+i).value=='')) break;
			}	
			if (i==n_aut_link) w.opener.add_aut_link();
			
			var selObj = w.opener.document.getElementById('f_aut_link_table_list');
			var selIndex=selObj.selectedIndex;
			w.opener.document.getElementById('f_aut_link_table'+i).value= selObj.options[selIndex].value;
			
			w.opener.document.getElementById('f_aut_link_id'+i).value = id_value;
			w.opener.document.getElementById('f_aut_link_libelle'+i).value = reverse_html_entities('['+selObj.options[selIndex].text+']'+libelle_value);			
			
		}			
	} else if (dyn) {
		n_categ=w.opener.document.forms[f_caller].elements['max_categ'].value;
		flag = 1;
	
		//Vérification que la catégorie n'est pas déjà sélectionnée
		for (i=0; i<n_categ; i++) {
			if (w.opener.document.getElementById('f_categ_id'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
	
		if (flag) {
			for (i=0; i<n_categ; i++) {
				if ((w.opener.document.getElementById('f_categ_id'+i).value==0)||(w.opener.document.getElementById('f_categ_id'+i).value=='')) break;
			}
	
			if (i==n_categ) w.opener.add_categ();
			w.opener.document.getElementById('f_categ_id'+i).value = id_value;
			w.opener.document.getElementById('f_categ'+i).value = reverse_html_entities(libelle_value);
		}
		if(callback)
			w.opener[callback]('$infield');
	} else {
		var p1 = '$p1';
		var p2 = '$p2';
		//on enlève le dernier _X
		var tmp_p1 = p1.split('_');
		var tmp_p1_length = tmp_p1.length;
		tmp_p1.pop();
		var p1bis = tmp_p1.join('_');
		
		var tmp_p2 = p2.split('_');
		var tmp_p2_length = tmp_p2.length;
		tmp_p2.pop();
		var p2bis = tmp_p2.join('_');
		
		var max_aut = w.opener.document.getElementById(p1bis.replace('id','max_aut'));
		if(max_aut && (p1bis.replace('id','max_aut').substr(-7)=='max_aut')){
			var trouve=false;
			var trouve_id=false;
			for(i_aut=0;i_aut<=max_aut.value;i_aut++){
				if(w.opener.document.getElementById(p1bis+'_'+i_aut).value==0){
					w.opener.document.getElementById(p1bis+'_'+i_aut).value=id_value;
					w.opener.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
					trouve=true;
					break;
				}else if(w.opener.document.getElementById(p1bis+'_'+i_aut).value==id_value){
					trouve_id=true;
				}
			}
			if(!trouve && !trouve_id){
				w.opener.add_line(p1bis.replace('_id',''));
				w.opener.document.getElementById(p1bis+'_'+i_aut).value=id_value;
				w.opener.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
			}
			var theselector = w.opener.document.getElementById(p1.replace('field','fieldvar').replace('_id','')+'[id_thesaurus][]');
			if(theselector){
				for (var i=1 ; i< theselector.options.length ; i++){
					if (theselector.options[i].value == id_thesaurus){
						theselector.options[i].selected = true;
						break;
					}
				}
			}
			if(callback)
				w.opener[callback](p1bis.replace('_id','')+'_'+i_aut);
		}else {
			w.opener.document.getElementById('$p1').value=id_value;
			w.opener.document.getElementById('$p2').value=reverse_html_entities(libelle_value);
			var theselector = w.opener.document.getElementById(p1.replace('field','fieldvar').replace('_id','')+'[id_thesaurus][]');
			if(theselector){
				for (var i=1 ; i< theselector.options.length ; i++){
					if (theselector.options[i].value == id_thesaurus){
						theselector.options[i].selected = true;
						break;
					}
				}
			}
			if(callback)
				w.opener[callback]('$infield');
			//closeCurrentEnv();
		}		
	}
}
-->
</script>
";

$jscript = $jscript_."
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value,callback,id_thesaurus)
{
	set_parent_w(f_caller, id_value, libelle_value,parent,callback,id_thesaurus);
}
-->
</script>
";

$jscript_term = $jscript_."
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value,callback,id_thesaurus)
{
	set_parent_w(f_caller, id_value, libelle_value,parent.parent,callback,id_thesaurus);
}
-->
</script>
";
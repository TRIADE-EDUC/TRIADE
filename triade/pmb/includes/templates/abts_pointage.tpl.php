<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_pointage.tpl.php,v 1.23 2023/12/20 08:26:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $pointage_form, $msg, $current_module, $abts_gestion_retard_form_filter, $abts_gestion_retard_fournisseur_first, $abts_gestion_retard_fournisseur_suite, $abts_gestion_retard_form, $charset, $abts_gestion_retard_perio, $abts_gestion_retard_bulletin, $abts_gestion_retard_bulletin_relance;

$pointage_form = "
<script type='text/javascript' src='./javascript/tablist.js'></script>
<h1>".$msg["4000"]." : ".$msg["pointage_libelle_form"]."</h1>
!!abts_pointage_list!!
!!alerte_fin_abonnement!!
!!alerte_abonnement_depasse!!
<div class='left'>
		!!imprimer!!
		!!imprime_abts_depasse!!
		!!gestion_retard!!
	</div>
";			

$abts_gestion_retard_form_filter = "

<script type='text/javascript' src='./javascript/tablist.js'></script>
<script type='text/javascript' src='./javascript/sorttable.js'></script>
<h1>".$msg["4000"]." : ".$msg["abts_gestion_retard"]."</h3>
<form class='form-$current_module' id='abts_gestion_retard_filter' name='abts_gestion_retard_filter' method='post' action='./catalog.php?categ=serials&sub=abts_retard'>

<script>
	function fonction_selecteur_fourn() {
		name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		openPopUp('./select.php?what=fournisseur&caller=abts_gestion_retard_filter&param1='+name_id+'&param2='+name+'&dyn=1', 'selector');
	}
	function fonction_raz_fourn() {
		name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		document.getElementById(name_id).value=0;
		document.getElementById(name).value='';
	}
	function add_fourn() {
		template = document.getElementById('addfourn');
		fourn=document.createElement('div');
		fourn.className='row';

		suffixe = eval('document.abts_gestion_retard_filter.max_fourn.value')
		nom_id = 'f_fourn'+suffixe
		f_fourn = document.createElement('input');
		f_fourn.setAttribute('name',nom_id);
		f_fourn.setAttribute('id',nom_id);
		f_fourn.setAttribute('type','text');
		f_fourn.className='saisie-30emr';
		f_fourn.setAttribute('readonly','');
		f_fourn.setAttribute('value','');

		del_f_fourn = document.createElement('input');
		del_f_fourn.setAttribute('id','del_f_fourn'+suffixe);
		del_f_fourn.onclick=fonction_raz_fourn;
		del_f_fourn.setAttribute('type','button');
		del_f_fourn.className='bouton_small';
		del_f_fourn.setAttribute('readonly','');
		del_f_fourn.setAttribute('value','X');
		
		sel_f_fourn = document.createElement('input');
        sel_f_fourn.setAttribute('id','sel_f_fourn'+suffixe);
        sel_f_fourn.setAttribute('type','button');
        sel_f_fourn.className='bouton_small';
        sel_f_fourn.setAttribute('readonly','');
        sel_f_fourn.setAttribute('value','...');
        sel_f_fourn.onclick=fonction_selecteur_fourn;
		
		f_fourn_id = document.createElement('input');
		f_fourn_id.name='f_fourn_id'+suffixe;
		f_fourn_id.setAttribute('type','hidden');
		f_fourn_id.setAttribute('id','f_fourn_id'+suffixe);
		f_fourn_id.setAttribute('value','');
		
		fourn.appendChild(f_fourn);
		space=document.createTextNode(' ');
		fourn.appendChild(space);
		space=document.createTextNode(' ');
		fourn.appendChild(space);
		fourn.appendChild(sel_f_fourn);
		fourn.appendChild(space);
		fourn.appendChild(del_f_fourn);
		fourn.appendChild(f_fourn_id);

		template.appendChild(fourn);
		
		document.abts_gestion_retard_filter.max_fourn.value=suffixe*1+1*1 ;
	}
</script>

	<h3>".$msg["abts_gestion_retard_form"]."</h3>
	<div class='form-contenu'>
		
		<div class='colonne3'>				
			<label for='form_pointage' class='etiquette'>".$msg["abts_gestion_retard_localisation"]."</label>				
			<div class='row'>
				!!location_filter!!
			</div>								
		</div>
		<div class='colonne3'>				
			<label for='form_pointage' class='etiquette'>".$msg["abts_gestion_retard_abts_state"]."</label>				
			<div class='row'>
				<select name='filter' id='filter' onchange=''>
					<option value='0' !!abts_state_selected_0!!>".$msg["abts_gestion_retard_abts_state_all"]."</option>
					<option value='1' !!abts_state_selected_1!!>".$msg["abts_gestion_retard_abts_state_actif"]."</option>
					<option value='2' !!abts_state_selected_2!!>".$msg["abts_gestion_retard_abts_state_old"]."</option>
				</select>
			</div>								
		</div>
		<div class='colonne3'>				
			<label for='form_pointage' class='etiquette'>".$msg["abts_gestion_retard_fournisseur"]."</label>	
			<input name='max_fourn' value='!!max_fourn!!' type='hidden'>			
			<div class='row'>
	
				!!fournisseurs_repetables!!
				<div id='addfourn'/>
       			</div>
			</div>								
		</div>
		
		<div class='row'></div>
	</div>
	<div class='left'>
		<input type=\"submit\" class='bouton' value='".$msg["actualiser"]."' />&nbsp;	
	</div>			
	<div class='row'></div>

";	


$abts_gestion_retard_fournisseur_first = "
    <div class='row'>
        <input type='text' class='saisie-30emr' id='f_fourn!!ifourn!!' name='f_fourn!!ifourn!!' value=\"!!fourn_libelle!!\" completion=\"categories_mul\" autfield=\"f_fourn_id!!ifourn!!\" />
        <input type='button' class='bouton_small' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=fournisseur&caller=abts_gestion_retard_filter&param1=f_fourn_id!!ifourn!!&param2=f_fourn!!ifourn!!&dyn=1', 'selector'); \" type='text'>
        <input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.f_fourn!!ifourn!!.value=''; this.form.f_fourn_id!!ifourn!!.value='0'; \" />
        <input type='hidden' name='f_fourn_id!!ifourn!!' id='f_fourn_id!!ifourn!!' value='!!fourn_id!!' />
        <input type='button' class='bouton_small' value='+' onClick=\"add_fourn();\"/>
    </div>
";
$abts_gestion_retard_fournisseur_suite = "
    <div class='row'>
        <input type='text' class='saisie-30emr' id='f_fourn!!ifourn!!' name='f_fourn!!ifourn!!' value=\"!!fourn_libelle!!\" completion=\"categories_mul\" autfield=\"f_fourn_id!!ifourn!!\" />
        <input type='button' class='bouton_small' value='$msg[parcourir]' onclick=\"openPopUp('./select.php?what=fournisseur&caller=abts_gestion_retard_filter&param1=f_fourn_id!!ifourn!!&param2=f_fourn!!ifourn!!&dyn=1', 'selector'); \" type='text'>
        <input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.f_fourn!!ifourn!!.value=''; this.form.f_fourn_id!!ifourn!!.value='0'; \" />
        <input type='hidden' name='f_fourn_id!!ifourn!!' id='f_fourn_id!!ifourn!!' value='!!fourn_id!!' />
    </div>
";


$abts_gestion_retard_form = "
<script src='./javascript/dynamic_element.js' type='text/javascript'></script>
<script>
	tab_perio_bulletins=new Array();
	!!tab_perio!!
	nb_perios=!!nb_perios!!;
	nb_bulletins=!!nb_bulletins!!;

	function gestion_retard_sel_all(checked){
		if(!nb_bulletins) return;
		for(var i=1; i<nb_bulletins+1;i++){
			var id= 'bulletin_'+i;			
			if (checked==false) {
				document.getElementById(id).checked=false;
			} else {
				document.getElementById(id).checked=true;
			}
		}
		for(var i=0; i< nb_perios;i++){
			if (checked==false) {
				document.getElementById('sel_perio_'+i).checked=false;
			} else {
				document.getElementById('sel_perio_'+i).checked=true;
			}
		}	
	}
	
	function gestion_retard_sel(num_perio,checked){
		var start=tab_perio_bulletins[num_perio][0];
		var nb=tab_perio_bulletins[num_perio][1];
		for(var i=start; i< start+nb;i++){
			var id= 'bulletin_'+i;			
			if (checked==false) {
				document.getElementById(id).checked=false;
			} else {
				document.getElementById(id).checked=true;
			}
		}
	}
	
	function gestion_retard_get_sel(){
		if(!nb_bulletins) return;
		var sel_relance='';
		for(var i=1; i< nb_bulletins+1;i++){
			var id= document.getElementById('bulletin_'+i);		
			if (id.checked==true) {
				sel_relance+=','+id.getAttribute('rel_id');		
			}	
		}
		if(sel_relance){
			var url='./print_relance.php?action=print_prepare&sel_relance='+sel_relance;
			openPopUp(url,'print_rel');
		} else {
			alert ('".addslashes($msg["abts_gestion_retard_no_sel"])."');
		}
		
	}	
	
	function gestion_retard_view_histo(id,nb_rel){
		for(var i=1;i<=nb_rel;i++){
			var id_rel=document.getElementById(id+'_'+i);
			if(id_rel) {
				if(id_rel.style.display == 'none') {
					id_rel.style.display='table-row';
				} else {
					id_rel.style.display='none';
				}
			}		
		}		
	}
	
</script>
	<div class='row'></div>		
	!!perio_list!!
	<input type='hidden' name='tab_bulletins' value='!!tab_bulletins!!' >
	<input type='hidden' id='action' name='action' value='' >
	<div class='row'></div>			
	<div class='row'>
		<input type=\"button\" class='bouton' value='".htmlentities($msg["abts_gestion_retard_sel"],ENT_QUOTES, $charset)."' onClick=\"gestion_retard_sel_all(1);\"/>&nbsp;
		<input type=\"button\" class='bouton' value='".htmlentities($msg["abts_gestion_retard_desel"],ENT_QUOTES, $charset)."' onClick=\"gestion_retard_sel_all(0);\"/>&nbsp;
		<input type=\"button\" class='bouton' value='".htmlentities($msg["abts_gestion_retard_relancer"],ENT_QUOTES, $charset)."' onClick=\"gestion_retard_get_sel();\"/>&nbsp;	
	</div>	
			
	<div class='colonne2'>	
		<label for='form_pointage' class='etiquette'>".htmlentities($msg["abts_gestion_retard_comment"],ENT_QUOTES, $charset)."</label>	<br />					
		<textarea  id='comment' name='comment' cols='50' rows='6' wrap='virtual'></textarea>
	</div>
	<div class='colonne_suite' >	
		<input type='submit' class='bouton' value='".htmlentities($msg["abts_gestion_retard_comment_gestion"],ENT_QUOTES, $charset)."' onClick=\"document.getElementById('action').value='comment_gestion';\" />&nbsp;<br />		
		<input type='submit' class='bouton' value='".htmlentities($msg["abts_gestion_retard_comment_opac"],ENT_QUOTES, $charset)."' onClick=\"document.getElementById('action').value='comment_opac';\"  />
	</div>
	<div class='row'></div>
</form>

<script type='text/javascript'>parse_dynamic_elts();dynamic_text_no_control=1;</script>
";	

$abts_gestion_retard_perio = "	
<div id='perio_retard!!num_perio!!' class='notice-parent'>
    ".get_expandBase_button('perio_retard!!num_perio!!')."
	<span class='notice-heada'>
    	<small>
    		<span  class='statutnot1'  style='margin-right: 3px;'>
    			<img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
    		</span>
    	</small>
    	<input type='checkbox' id='sel_perio_!!i_perio!!' name='perio' onClick=\"gestion_retard_sel(!!num_perio!!,this.checked);\" value='1' class='checkbox' /><a href='./catalog.php?categ=serials&sub=view&serial_id=!!num_perio!!'>!!perio_header!!</a>
    </span>
    <br />
</div>
<div id='perio_retard!!num_perio!!Child' class='notice-child' style='margin-bottom:6px;display:none;'>
	<table width='100%' class='sortable'>
		<tr>
			<th>
				
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_date"]."
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_numero"]."
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_abt"]."
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_comment_gestion"]."
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_comment_opac"]."
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_nb_relance"]."
			</th>
			<th>
				".$msg["abts_gestion_retard_bull_date_relance"]."
			</th>
		</tr>
		!!liste_retard!!
	</table>
</div>
";
$abts_gestion_retard_bulletin="
<tr  class='!!tr_class!!' >			
	<td>
		<input type='checkbox' name='bulletin[]' id='bulletin_!!bulletin_number!!' value='!!bulletin_serialise!!' rel_id='!!rel_id!!' class='checkbox' />
	</td>		
	<td>
		!!date!!
	</td>	
	<td>
		!!numero!!
	</td>
	<td>
		!!abonnement!!
	</td>
	<td style='cursor: pointer;'>
		<span dynamics='catalog,comment_gestion' dynamics_params='text' id='gestion_!!rel_id!!'>!!comment_gestion!!</span>
	</td>
	<td style='cursor: pointer;'>
		<span dynamics='catalog,comment_opac' dynamics_params='text' id='opac_!!rel_id!!'>!!comment_opac!!</span>
	</td>
	<td>
		!!nb_relance!!
	</td>
	<td>
		!!date_relance!!
	</td>	
</tr>
";	

$abts_gestion_retard_bulletin_relance="
<tr  class='!!tr_class!!' id='!!relnew_num!!_!!nb_relance!!' style='display:none'>			
	<td>
	</td>		
	<td>
	</td>	
	<td>
	</td>
	<td>
	</td>
	<td>
		!!comment_gestion!!
	</td>
	<td>
		!!comment_opac!!
	</td>
	<td>
		!!nb_relance!!
	</td>
	<td>
		!!date_relance!!
	</td>	
</tr>
";	
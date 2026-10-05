<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: z3950_form.tpl.php,v 1.110.4.1 2025/03/11 11:02:31 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $nb_onglets, $ptab, $msg, $base_path, $charset, $form_notice, $current_module, $zone_article_form, $bt_undo, $zone_categ_form;
global $deflt_notice_catalog_categories_auto;
// template pour le form de catalogage

// nombre de parties du form
$nb_onglets = 8;

//	----------------------------------------------------
// 	  $ptab[0] : contenu de l'onglet 0 (zone de titre)

$ptab[0] = "
<!-- onglet 0 -->
<div id='el0Parent' class='parent'>
	<h3>
	<img src='".get_url_icon('minus.gif')."' class='img_plus' class='align_bottom' name='imEx' id='el0Img' title='$msg[236]' onClick=\"expandBase('el0', true); return false;\" />
	$msg[712]
	</h3>
</div>

<div id='el0Child' class='child' >
	<!--	Titre	-->
	<div class='row'>
		<label for='f_title_0' class='etiquette'>$msg[237]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_title_0' name='f_title_0' value=\"!!title_0!!\" />
	</div>

	<!--	Titre propre d'un auteur différent	-->
	<div class='row'>
		<label for='f_title_1' class='etiquette'>$msg[238]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_title_1' name='f_title_1' value=\"!!title_1!!\" />
	</div>

	<!--	Titre parallèle	-->
	<div class='row'>
		<label for='f_title_2' class='etiquette'>$msg[239]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_title_2' name='f_title_2' value=\"!!title_2!!\" />
	</div>

	<!--	Complément du titre	-->
	<div class='row'>
		<label for='f_title_3' class='etiquette'>$msg[240]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_title_3' name='f_title_3' value=\"!!title_3!!\" />
	</div>
	<div class='row'>
		<!--	Partie de	-->
		<div class='colonne2'>
			<label for='f_serie' class='etiquette'>$msg[241]</label>
            <div class='row'>
    			<input type=\"radio\" name=\"serie_type\" value=\"use_existing\" !!serie_type_use_existing!! id=\"serie_type_use_existing\" /><label for=\"serie_type_use_existing\">".$msg['notice_integre_serie_use_existing']."</label>
                <blockquote>
    				<input type='text' class='saisie-30emr' id='f_serie' name='f_serie' value=\"!!serie!!\" completion='serie' autfield='f_serie_id' autocomplete='off' />
    				<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('$base_path/select.php?what=serie&caller=notice&param1=f_serie_id&param2=f_serie', 'selector')\" />
    				<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_serie.value='';\" />
    				<input type='hidden' name='f_serie_id' id='f_serie_id' value='!!serie_id!!' />
                </blockquote>
    			<input type='radio' name=\"serie_type\" value=\"insert_new\" !!serie_type_insert_new!! id=\"serie_type_insert_new\" /><label for=\"serie_type_insert_new\">".$msg['notice_integre_serie_new']."</label>
    	        <blockquote>
                    <div class='row'>
    			        <div class='colonne' style='margin-right:5px'>
    						<label for='f_serie_new' class='etiquette'>".$msg['233']."</label>
    						<div class='row'>
    							<input type='text' class='saisie-50em' id='f_serie_new' name='f_serie_new' value=\"!!serie_new_name!!\" />
    						</div>
    				    </div>
    				</div>
                </blockquote>
            </div>
		</div>
		<!--	Partie de	-->
		<div class='colonne2'>
			<label for='f_nbr_in_serie' class='etiquette'>$msg[242]</label>
			<div class='row'>
				<input type='text' class='saisie-10em' id='f_nbr_in_serie' name='f_nbr_in_serie' maxlength='255' value=\"!!nbr_in_serie!!\" />
			</div>
		</div>
	</div>
</div>
";

//	----------------------------------------------------
//	Mention de responsabilité
// 	  $ptab[1] : contenu de l'onglet 1 (mention de responsabilité)
//	----------------------------------------------------

$ptab[1] = "
<script>
    function fonction_selecteur_auteur() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUp('./select.php?what=auteur&caller=notice&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), 'selector');
    }
    function fonction_raz_auteur() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        document.getElementById(name_id).value=0;
        document.getElementById(name).value='';
    }
    function fonction_selecteur_fonction() {
        name=this.getAttribute('id').substring(4);
		n = name.substr(3,1);
		name_code = 'f_f'+n+'_code'+name.substr(4);
        name = 'f_existing_'+name.substr(2);
        openPopUp('./select.php?what=function&caller=notice&param1='+name_code+'&param2='+name+'&dyn=1', 'selector');
    }
    function fonction_raz_fonction() {
        name=this.getAttribute('id').substring(4);
		n = name.substr(3,1);
		name_code = 'f_f'+n+'_code'+name.substr(4);
        name = 'f_existing_'+name.substr(2);
        document.getElementById(name_code).value=0;
        document.getElementById(name).value='';
    }
    function add_aut(n) {
        template = document.getElementById('addaut'+n);
        aut=document.createElement('div');
        aut.className='row';

        // auteur
        colonne=document.createElement('div');
        colonne.className='colonne2';
        row=document.createElement('div');
        row.className='row';
        suffixe = eval('document.notice.max_aut'+n+'.value')
        nom_id = 'f_aut'+n+suffixe
        f_aut0 = document.createElement('input');
        f_aut0.setAttribute('name',nom_id);
        f_aut0.setAttribute('id',nom_id);
        f_aut0.setAttribute('type','text');
        f_aut0.className='saisie-30emr';
        f_aut0.setAttribute('value','');
        f_aut0.setAttribute('completion','authors');
        f_aut0.setAttribute('autfield','f_aut'+n+'_id'+suffixe);

        sel_f_aut0 = document.createElement('input');
        sel_f_aut0.setAttribute('id','sel_f_aut'+n+suffixe);
        sel_f_aut0.setAttribute('type','button');
        sel_f_aut0.className='bouton';
        sel_f_aut0.setAttribute('readonly','');
        sel_f_aut0.setAttribute('value','".$msg['parcourir']."');
        sel_f_aut0.onclick=fonction_selecteur_auteur;

        del_f_aut0 = document.createElement('input');
        del_f_aut0.setAttribute('id','del_f_aut'+n+suffixe);
        del_f_aut0.onclick=fonction_raz_auteur;
        del_f_aut0.setAttribute('type','button');
        del_f_aut0.className='bouton';
        del_f_aut0.setAttribute('readonly','');
        del_f_aut0.setAttribute('value','".$msg['raz']."');

        f_aut0_id = document.createElement('input');
        f_aut0_id.name='f_aut'+n+'_id'+suffixe;
        f_aut0_id.setAttribute('type','hidden');
        f_aut0_id.setAttribute('id','f_aut'+n+'_id'+suffixe);
        f_aut0_id.setAttribute('value','');

        f_aut0_type = document.createElement('input');
        f_aut0_type.name='author'+n+'_type_'+suffixe;
        f_aut0_type.setAttribute('type','hidden');
        f_aut0_type.setAttribute('value','use_existing');

        //f_aut0_content.appendChild(f_aut0);
		row.appendChild(f_aut0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(sel_f_aut0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(del_f_aut0);
        row.appendChild(f_aut0_id);
        row.appendChild(f_aut0_type);
        colonne.appendChild(row);
        aut.appendChild(colonne);

        // fonction
        colonne=document.createElement('div');
        colonne.className='colonne_suite';
        row=document.createElement('div');
        row.className='row';
        suffixe = eval('document.notice.max_aut'+n+'.value')
        nom_id = 'f_existing_f'+n+suffixe
        f_f0 = document.createElement('input');
        f_f0.setAttribute('name',nom_id);
        f_f0.setAttribute('id',nom_id);
        f_f0.setAttribute('type','text');
        f_f0.className='saisie-15emr';
        f_f0.setAttribute('value','');
		f_f0.setAttribute('completion','fonction');
        f_f0.setAttribute('autfield','f_f'+n+'_code'+suffixe);

        sel_f_f0 = document.createElement('input');
        sel_f_f0.setAttribute('id','sel_f_f'+n+suffixe);
        sel_f_f0.setAttribute('type','button');
        sel_f_f0.className='bouton';
        sel_f_f0.setAttribute('readonly','');
        sel_f_f0.setAttribute('value','".$msg['parcourir']."');
        sel_f_f0.onclick=fonction_selecteur_fonction;

        del_f_f0 = document.createElement('input');
        del_f_f0.setAttribute('id','del_f_f'+n+suffixe);
        del_f_f0.onclick=fonction_raz_fonction;
        del_f_f0.setAttribute('type','button');
        del_f_f0.className='bouton';
        del_f_f0.setAttribute('readonly','readonly');
        del_f_f0.setAttribute('value','".$msg['raz']."');

        f_f0_code = document.createElement('input');
        f_f0_code.name='f_f'+n+'_code'+suffixe;
        f_f0_code.setAttribute('type','hidden');
        f_f0_code.setAttribute('id','f_f'+n+'_code'+suffixe);
        f_f0_code.setAttribute('value','');

        row.appendChild(f_f0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(sel_f_f0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(del_f_f0);
        row.appendChild(f_f0_code);
        colonne.appendChild(row);

        aut.appendChild(colonne);
        template.appendChild(aut);
        eval('document.notice.max_aut'+n+'.value=suffixe*1+1*1');
        ajax_pack_element(f_aut0);
		ajax_pack_element(f_f0);
    }

    function changeAuthorType(indice){
    	var select_name = 'f_author_type_'+indice;
    	var date_label =  'l_author_date_'+indice;
    	var lieu_label = 'l_author_lieu_'+indice;
    	var pays_label = 'l_author_pays_'+indice;
    	var ville_label = 'l_author_ville_'+indice;
    	var subdivision_label = 'l_author_subdivision_'+indice;
    	var numero_label = 'l_author_numero_'+indice;
    	var web_label = 'l_author_web_'+indice;
    	var date_div =  'div_author_date_'+indice;
    	var lieu_div = 'div_author_lieu_'+indice;
    	var pays_div = 'div_author_pays_'+indice;
    	var ville_div = 'div_author_ville_'+indice;
    	var subdivision_div = 'div_author_subdivision_'+indice;
    	var numero_div = 'div_author_numero_'+indice;
    	var web_div = 'div_author_web_'+indice;
    	if(document.getElementById(select_name).value == '70'){
    		document.getElementById(date_label).classname = 'colonne2';
    		document.getElementById(date_div).classname = 'colonne2';
    		document.getElementById(lieu_label).style.display = 'none';
    		document.getElementById(pays_label).style.display = 'none';
    		document.getElementById(ville_label).style.display = 'none';
    		document.getElementById(subdivision_label).style.display = 'none';
    		document.getElementById(numero_label).style.display = 'none';
    		document.getElementById(web_label).style.display = 'none';
    		document.getElementById(lieu_div).style.display = 'none';
    		document.getElementById(pays_div).style.display = 'none';
    		document.getElementById(ville_div).style.display = 'none';
    		document.getElementById(subdivision_div).style.display = 'none';
    		document.getElementById(numero_div).style.display = 'none';
    		document.getElementById(web_div).style.display = 'none';
    	} else {
    		document.getElementById(date_label).classname = 'colonne4';
    		document.getElementById(date_div).classname = 'colonne4';
    		document.getElementById(lieu_label).style.display = '';
    		document.getElementById(pays_label).style.display = '';
    		document.getElementById(ville_label).style.display = '';
    		document.getElementById(subdivision_label).style.display = '';
    		document.getElementById(numero_label).style.display = '';
    		document.getElementById(web_label).style.display = '';
    		document.getElementById(lieu_div).style.display = '';
    		document.getElementById(pays_div).style.display = '';
    		document.getElementById(ville_div).style.display = '';
    		document.getElementById(subdivision_div).style.display = '';
    		document.getElementById(numero_div).style.display = '';
    		document.getElementById(web_div).style.display = '';
    	}
    }

    function check_link(id) {
		w=window.open(document.getElementById(id).value);
		w.focus();
	}
</script>

<!-- onglet 1 -->
<div id='el1Parent' class='parent'>
	<h3>
    ".get_expandBase_button('el1', '243')."
	$msg[243]
	</h3>
</div>
<div id='el1Child' class='child' >
	<!--	Auteur principal	-->
	<label for='f_aut0' class='etiquette'>$msg[244]</label><br />
	<input type=\"radio\" id=\"author0_type_use_existing\" !!author0_type_use_existing!! value=\"use_existing\" name=\"author0_type\" /><label for=\"author0_type_use_existing\">".$msg['notice_integre_author_use_existing']."</label>
	<blockquote>
	    <div class='row'>
	        <div id='el1Child_0a' class='colonne2'>
	            <label for='f_author_name_0_existing' class='etiquette'>$msg[244]</label>
	            <div class='row' >
					<input type='text' completion='authors' autfield='f_aut0_existing_id' id='f_author_name_0_existing' class='saisie-30emr' name='f_author_name_0_existing' value=\"!!f_author_name_0_existing!!\" />
	                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=notice&param1=f_aut0_existing_id&param2=f_author_name_0_existing&deb_rech='+".pmb_escape()."(this.form.f_author_name_0_existing.value), 'selector')\" />
	              	<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_author_name_0_existing.value=''; this.form.f_aut0_existing_id.value='0'; \" />
	               	<input type='hidden' name='f_aut0_existing_id' id='f_aut0_existing_id' value=\"!!f_aut0_existing_id!!\" />
	               	<input type='hidden' name='f_auth_number_to_compare_0' id='f_auth_number_to_compare_0' value=\"!!authority_number_0!!\" />
	            </div>
			</div>
	        <!--    Fonction    -->
	        <div id='el1Child_1a' class='colonne_suite'>
	            <label for='f_existing_f0' class='etiquette'>$msg[245]</label>
	            <div class='row'>
			        <input type='text' class='saisie-15emr' id='f_existing_f0' name='f_existing_f0' completion=\"fonction\" autfield=\"f_existing_f0_code\" value=\"!!author_function_label_0!!\" />
	                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_existing_f0_code&p2=f_existing_f0', 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_existing_f0.value=''; this.form.f_existing_f0_code.value='0'; \" />
	                <input type='hidden' name='f_existing_f0_code' id='f_existing_f0_code' value=\"!!author_function_0!!\" />
	            </div>
	        </div>
		</div>
	</blockquote>
	<br clear=\"all\" />
	<input type=\"radio\" id=\"author0_type_insert_new\" !!author0_type_insert_new!! value=\"insert_new\" name=\"author0_type\" /><label for=\"author0_type_insert_new\">".$msg['notice_integre_author_new']."</label>
	<blockquote>
		<div class='row'>
			<div class='colonne2'>
				<select name='f_author_type_0' id='f_author_type_0' onChange='changeAuthorType(0);'>
					<option value='70' !!author_type_70_0!!>$msg[203]</option>
					<option value='71' !!author_type_71_0!!>$msg[204]</option>
					<option value='72' !!author_type_72_0!!>".$msg['congres_libelle']."</option>
				</select>
			</div>
			<div class='colonne_suite'>
				<label for='f_author_function_label_0' class='etiquette'>$msg[245]</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne2'>
				<input type='text' class='saisie-20em' name='f_author_name_0' value=\"!!author_name_0!!\" />
				<input type='text' class='saisie-15em' name='f_author_rejete_0' value=\"!!author_rejete_0!!\" />
				<input type='text' class='saisie-15em' name='f_authority_number_0' value=\"!!authority_number_0!!\" />
			</div>
			<div class='colonne_suite'>
				<input type='text' class='saisie-15emr' id='f_author_function_label_0' name='f_author_function_label_0' completion=\"fonction\" autfield=\"f_author_function_0\" value=\"!!author_function_label_0!!\" />
				<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_author_function_0&p2=f_author_function_label_0', 'selector')\" />
				<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_author_function_label_0.value=''; this.form.f_author_function_0.value=''; \" />
				<input type='hidden' name='f_author_function_0' id='f_author_function_0' value=\"!!author_function_0!!\" />
			</div>
		</div>
		<div class='row'>
			<div class='colonne4' id='l_author_date_0'>
				<label for='f_author_date_0' class='etiquette'>".$msg[713]."</label>
			</div>
			<div class='colonne4' id='l_author_lieu_0'  style='display:!!display_0!!'>
				<label for='f_author_lieu_0' class='etiquette'>".$msg['congres_lieu_libelle']."</label>
			</div>
			<div class='colonne_suite' id='l_author_pays_0'  style='display:!!display_0!!'>
				<label for='f_author_pays_0' class='etiquette'>".$msg['congres_pays_libelle']."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne4' id='div_author_date_0'>
				<input type='text' class='saisie-20em' name='f_author_date_0' id='f_author_date_0' value=\"!!author_date_0!!\" />
			</div>
			<div class='colonne4' id='div_author_lieu_0' style='display:!!display_0!!'>
				<input type='text' class='saisie-20em' name='f_author_lieu_0' id='f_author_lieu_0' value=\"!!author_lieu_0!!\" />
			</div>
			<div class='colonne_suite' id='div_author_pays_0' style='display:!!display_0!!'>
				<input type='text' class='saisie-20em' name='f_author_pays_0' id='f_author_pays_0' value=\"!!author_pays_0!!\" />
			</div>
		</div>
		<div class='row'>
			<div class='colonne4' id='l_author_ville_0' style='display:!!display_0!!'>
				<label for='f_author_ville_0' class='etiquette'>".$msg['congres_ville_libelle']."</label>
			</div>
			<div class='colonne4' id='l_author_subdivision_0'  style='display:!!display_0!!'>
				<label for='f_author_subdivision_0' class='etiquette'>".$msg['congres_subdivision_libelle']."</label>
			</div>
			<div class='colonne_suite' id='l_author_numero_0'  style='display:!!display_0!!'>
				<label for='f_author_numero_0' class='etiquette'>".$msg['congres_numero_libelle']."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne4' id='div_author_ville_0' style='display:!!display_0!!'>
				<input type='text' class='saisie-20em' name='f_author_ville_0' id='f_author_ville_0' value=\"!!author_ville_0!!\" />
			</div>
			<div class='colonne4' id='div_author_subdivision_0' style='display:!!display_0!!'>
				<input type='text' class='saisie-20em' name='f_author_subdivision_0' id='f_author_subdivision_0' value=\"!!author_subdivision_0!!\" />
			</div>
			<div class='colonne_suite' id='div_author_numero_0' style='display:!!display_0!!'>
				<input type='text' class='saisie-20em' name='f_author_numero_0' id='f_author_numero_0' value=\"!!author_numero_0!!\" />
			</div>
		</div>
		<div class='row'>
			<div class='colonne' id='l_author_web_0' style='display:!!display_0!!'>
				<label for='f_author_web_0' class='etiquette'>".$msg[147]."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne' id='div_author_web_0' style='display:!!display_0!!'>
				<input type='text' class='saisie-80em' name='f_author_web_0' id='f_author_web_0' value=\"!!author_web_0!!\" maxlength='255' />
				<input class='bouton' type='button' onClick=\"check_link('f_author_web_0')\" title='".$msg['CheckLink']."' value='".$msg['CheckButton']."' />
			</div>
		</div>
		<div class='row'>
			<div class='colonne'>
				<label for='f_author_comment_0' class='etiquette'>".$msg['author_comment']."</label>
			</div>
		</div>
		<div class='row'>
			<div class='colonne'>
				<textarea class='saisie-80em' id='f_author_comment_0' name='f_author_comment_0' cols='62' rows='4' wrap='virtual'>!!author_comment_0!!</textarea>
			</div>
		</div>
	</blockquote>
	<br clear=\"all\" /><br />

	<!--	autres auteurs	-->
	<div class='row'>
		<div class='row'>
			<label for='f_aut1' class='etiquette'>$msg[246]</label>
			<input type='hidden' name='max_aut1' value=\"!!max_aut1!!\" />
		</div>
		!!autres_auteurs!!
	</div>
	<blockquote>
		<label for=\"add_auth1_button\">".$msg['notice_integre_author_add_other']."</label>
		<div class=\"row\">
	        <div id='el1Child_2b_first' class='colonne2'>
	            <div class='row'>
	               	<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut1_id!!iaut_added1!!' id='f_aut1!!iaut_added1!!' name='f_aut1!!iaut_added1!!' value=\"\" />
					<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=notice&param1=f_aut1_id!!iaut_added1!!&param2=f_aut1!!iaut_added1!!&deb_rech='+".pmb_escape()."(this.form.f_aut1!!iaut_added1!!.value), 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_aut1!!iaut_added1!!.value=''; this.form.f_aut1_id!!iaut_added1!!.value='0'; \" />
	                <input type='hidden' name='f_aut1_id!!iaut_added1!!' id='f_aut1_id!!iaut_added1!!' value=\"0\" />
	                <input type=\"hidden\" name=\"author1_type_!!iaut_added1!!\" value=\"use_existing\" />
	            </div>
	        </div>
	    	<!--    Fonction    -->
	        <div  id='el1Child_2b_others' class='colonne_suite'>
	            <div class='row'>
	                <input type='text' class='saisie-15emr' id='f_existing_f1!!iaut_added1!!' name='f_existing_f1!!iaut_added1!!' completion='fonction' autfield='f_f1_code!!iaut_added1!!' value=\"\" />
	                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_f1_code!!iaut_added1!!&p2=f_existing_f1!!iaut_added1!!', 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_existing_f1!!iaut_added1!!.value=''; this.form.f_f1_code!!iaut_added1!!.value='0'; \" />
	                <input type='hidden' name='f_f1_code!!iaut_added1!!' id='f_f1_code!!iaut_added1!!' value=\"0\" />
	                <input class=\"bouton\" id=\"add_auth1_button\" onClick=\"add_aut(1);\" type=\"button\" value=\"+\" />
	             </div>
	         </div>
		</div>
    	<div id='addaut1'>
    	</div>
	</blockquote>
	<br clear=\"all\" /><br /><br /><br />

	<!--	Auteurs secondaires 	-->
	<div class='row'>
		<label for='f_aut2' class='etiquette'>$msg[247]</label>
		<input type='hidden' name='max_aut2' value=\"!!max_aut2!!\" />
		!!auteurs_secondaires!!
	</div>
	<blockquote>
		<label for=\"add_auth2_button\">".$msg['notice_integre_author_add_secondary']."</label>
		<div class=\"row\">
	        <div id='el1Child_2b_first' class='colonne2'>
	            <div class='row'>
	               	<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut2_id!!iaut_added2!!' id='f_aut2!!iaut_added2!!' name='f_aut2!!iaut_added2!!' value=\"\" />
					<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=notice&param1=f_aut2_id!!iaut_added2!!&param2=f_aut2!!iaut_added2!!&deb_rech='+".pmb_escape()."(this.form.f_aut2!!iaut_added2!!.value), 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_aut2!!iaut_added2!!.value=''; this.form.f_aut2_id!!iaut_added2!!.value='0'; \" />
	                <input type='hidden' name='f_aut2_id!!iaut_added2!!' id='f_aut2_id!!iaut_added2!!' value=\"0\" />
	                <input type=\"hidden\" name=\"author2_type_!!iaut_added2!!\" value=\"use_existing\" />
	            </div>
	        </div>
	    	<!--    Fonction    -->
	        <div  id='el1Child_2b_others' class='colonne_suite'>
	            <div class='row'>
	                <input type='text' class='saisie-15emr' id='f_existing_f2!!iaut_added2!!' name='f_existing_f2!!iaut_added2!!' completion='fonction' autfield='f_f2_code!!iaut_added2!!' value=\"\" />
	                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_f2_code!!iaut_added2!!&p2=f_existing_f2!!iaut_added2!!', 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_existing_f2!!iaut_added2!!.value=''; this.form.f_f2_code!!iaut_added2!!.value='0'; \" />
	                <input type='hidden' name='f_f2_code!!iaut_added2!!' id='f_f2_code!!iaut_added2!!' value=\"0\" />
					<input class=\"bouton\" id=\"add_auth2_button\" onClick=\"add_aut(2);\" type=\"button\" value=\"+\" />
	             </div>
	         </div>
		</div>
    	<div id='addaut2'>
    	</div>
	</blockquote>
	<br clear=\"all\" /><br />
</div>
";

//    ----------------------------------------------------
//     Titres uniformes
//       $ptab[230] : contenu de l'onglet 230 (Titres uniformes)
//    ----------------------------------------------------
global $pmb_use_uniform_title;
if ($pmb_use_uniform_title) {
	$ptab[230] = "
	<!-- onglet 230 -->
	<div id='el230Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el230', 'titres_uniformes')."
	    ".$msg['catal_onglet_titre_uniforme']."
	</h3>
	</div>
	<div id='el230Child' class='child' etirable='yes' title='".htmlentities($msg['aut_menu_titre_uniforme'],ENT_QUOTES, $charset)."'>
		<div id='el230Child_0' title='".htmlentities($msg['aut_menu_titre_uniforme'],ENT_QUOTES, $charset)."' movable='yes'>
			<!--    Titres uniformes    -->
			!!titres_uniformes!!
		</div>
	</div>
	";
} else $ptab[230] = "";

//	----------------------------------------------------
//	Autres auteurs
//	----------------------------------------------------
$ptab[11] = "<br />
		<input type=\"radio\" id=\"author1_type_use_existing_!!iaut!!\" !!author1_type_use_existing_!! value=\"use_existing\" name=\"author1_type_!!iaut!!\" />
		<label for=\"author1_type_use_existing_!!iaut!!\">".$msg['notice_integre_author_use_existing']."</label>
		<blockquote>
	        <div id='el1Child_2b_first' class='colonne2'>
	            <div class='row'>
	               	<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut1_id!!iaut!!' id='f_aut1!!iaut!!' name='f_aut1!!iaut!!' value=\"!!f_aut1!!\" />
					<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=notice&param1=f_aut1_id!!iaut!!&param2=f_aut1!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut1!!iaut!!.value), 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_aut1!!iaut!!.value=''; this.form.f_aut1_id!!iaut!!.value='0'; \" />
	                <input type='hidden' name='f_aut1_id!!iaut!!' id='f_aut1_id!!iaut!!' value=\"!!f_aut1_id!!\" />
	                <input type='hidden' name='f_auth_number_to_compare_1!!iaut!!' id='f_auth_number_to_compare_1!!iaut!!' value=\"!!authority_number_1!!\" />
	            </div>
	        </div>
	        <div  id='el1Child_2b_others' class='colonne_suite'>
	            <div class='row'>
	                <input type='text' class='saisie-15emr' id='f_existing_f1!!iaut!!' name='f_existing_f1!!iaut!!' completion='fonction' autfield='f_f1_code!!iaut!!' value=\"!!author_function_label_1!!\" />
	                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_f1_code!!iaut!!&p2=f_existing_f1!!iaut!!', 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_existing_f1!!iaut!!.value=''; this.form.f_f1_code!!iaut!!.value='0'; \" />
	                <input type='hidden' name='f_f1_code!!iaut!!' id='f_f1_code!!iaut!!' value=\"!!author_function_1!!\" />
	            </div>
	        </div>
		</blockquote>
		<br clear=\"all\" />
		<input type=\"radio\" id=\"author1_type_insert_new_!!iaut!!\" !!author1_type_insert_new_!! value=\"insert_new\" name=\"author1_type_!!iaut!!\" />
		<label for=\"author0_type_insert_new_!!iaut!!\">".$msg['notice_integre_author_new']."</label>
		<blockquote>
			<div class='row'>
				<div class='colonne2' >
					<select name='f_author_type_1!!iaut!!' id='f_author_type_1!!iaut!!' onChange='changeAuthorType(1!!iaut!!);'>
						<option value='70' !!author_type_70_1!!>$msg[203]</option>
						<option value='71' !!author_type_71_1!!>$msg[204]</option>
						<option value='72' !!author_type_72_1!!>".$msg['congres_libelle']."</option>
					</select>
				</div>
				<div class='colonne_suite'>
					<label for='f_f1!!iaut!!' class='etiquette'>$msg[245]</label>
				</div>
			</div>
			<div class='row'>
				<div class='colonne2'>
					<input type='text' class='saisie-20emr' name='f_author_name_1!!iaut!!' value=\"!!author_name_1!!\" />
					<input type='text' class='saisie-15emr' name='f_author_rejete_1!!iaut!!' value=\"!!author_rejete_1!!\" />
					<input type='text' class='saisie-15emr' name='f_authority_number_1!!iaut!!' value=\"!!authority_number_1!!\" />
				</div>
				<div class='colonne_suite'>
					<input type='text' class='saisie-15emr' id='f_f1!!iaut!!' name='f_f1!!iaut!!' readonly value=\"!!author_function_label_1!!\" />
					<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_author_function_1!!iaut!!&p2=f_f1!!iaut!!', 'selector')\" />
					<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_f1!!iaut!!.value=''; this.form.f_author_function_1!!iaut!!.value=''; \" />
					<input type='hidden' name='f_author_function_1!!iaut!!' value=\"!!author_function_1!!\" />
				</div>
			</div>
			<div class='row'>
				<div class='colonne4' id='l_author_date_1!!iaut!!'>
					<label for='f_author_date_1!!iaut!!' class='etiquette'>$msg[713]</label>
				</div>
				<div class='colonne4' id='l_author_lieu_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
					<label for='f_author_lieu_1!!iaut!!' class='etiquette'>".$msg['congres_lieu_libelle']."</label>
				</div>
				<div class='colonne_suite' id='l_author_pays_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
					<label for='f_author_pays_1!!iaut!!' class='etiquette'>".$msg['congres_pays_libelle']."</label>
				</div>
			</div>
			<div class='row'>
				<div class='colonne4' id='div_author_date_1!!iaut!!'>
					<input type='text' class='saisie-20emr' name='f_author_date_1!!iaut!!' id='f_author_date_1!!iaut!!' value=\"!!author_date_1!!\" />
				</div>
				<div class='colonne4' id='div_author_lieu_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
					<input type='text' class='saisie-20emr' name='f_author_lieu_1!!iaut!!' id='f_author_lieu_1!!iaut!!' value=\"!!author_lieu_1!!\" />
				</div>
				<div class='colonne_suite' id='div_author_pays_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
					<input type='text' class='saisie-20emr' name='f_author_pays_1!!iaut!!' id='f_author_pays_1!!iaut!!' value=\"!!author_pays_1!!\" />
				</div>
			</div>
			<div class='row'>
					<div class='colonne4' id='l_author_ville_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
						<label for='f_author_ville_1!!iaut!!' class='etiquette'>".$msg['congres_ville_libelle']."</label>
					</div>
					<div class='colonne4' id='l_author_subdivision_1!!iaut!!'  style='display:!!display_1!!iaut!!!!'>
						<label for='f_author_subdivision_1!!iaut!!' class='etiquette'>".$msg['congres_subdivision_libelle']."</label>
					</div>
					<div class='colonne_suite' id='l_author_numero_1!!iaut!!'  style='display:!!display_1!!iaut!!!!'>
						<label for='f_author_numero_1!!iaut!!' class='etiquette'>".$msg['congres_numero_libelle']."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne4' id='div_author_ville_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
						<input type='text' class='saisie-20emr' name='f_author_ville_1!!iaut!!' id='f_author_ville_1!!iaut!!' value=\"!!author_ville_1!!\" />
					</div>
					<div class='colonne4' id='div_author_subdivision_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
						<input type='text' class='saisie-20emr' name='f_author_subdivision_1!!iaut!!' id='f_author_subdivision_1!!iaut!!' value=\"!!author_subdivision_1!!\" />
					</div>
					<div class='colonne_suite' id='div_author_numero_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
						<input type='text' class='saisie-20emr' name='f_author_numero_1!!iaut!!' id='f_author_numero_1!!iaut!!' value=\"!!author_numero_1!!\" />
					</div>
				</div>
				<div class='row'>
					<div class='colonne' id='l_author_web_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
						<label for='f_author_web_1!!iaut!!' class='etiquette'>".$msg[147]."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne' id='div_author_web_1!!iaut!!' style='display:!!display_1!!iaut!!!!'>
						<input type='text' class='saisie-80emr' name='f_author_web_1!!iaut!!' id='f_author_web_1!!iaut!!' value=\"!!author_web_1!!\" maxlength='255' />
						<input class='bouton' type='button' onClick=\"check_link('f_author_web_1!!iaut!!')\" title='".$msg['CheckLink']."' value='".$msg['CheckButton']."' />
					</div>
				</div>
				<div class='row'>
					<div class='colonne'>
						<label for='f_author_comment_1!!iaut!!' class='etiquette'>".$msg['author_comment']."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne'>
						<textarea class='saisie-80emr' id='f_author_comment_1!!iaut!!' name='f_author_comment_1!!iaut!!' cols='62' rows='4' wrap='virtual'>!!author_comment_1!!</textarea>
					</div>
				</div>
			<br />
			<hr style='margin: 2px 0 2px 0;'>
		</blockquote>
	" ;

//	----------------------------------------------------
//	Auteurs secondaires
//	----------------------------------------------------
$ptab[12] = "<br />
		<input type=\"radio\" id=\"author2_type_use_existing_!!iaut!!\" value=\"use_existing\" !!author2_type_use_existing_!! name=\"author2_type_!!iaut!!\" /><label for=\"author2_type_use_existing_!!iaut!!\">".$msg['notice_integre_author_use_existing']."</label>
		<blockquote>
	        <div id='el1Child_3b_first' class='colonne2'>
	            <div class='row'>
	             	<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut2_id!!iaut!!' id='f_aut2!!iaut!!' name='f_aut2!!iaut!!' value=\"!!f_aut2!!\" />
					<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=auteur&caller=notice&param1=f_aut2_id!!iaut!!&param2=f_aut2!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut2!!iaut!!.value), 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_aut2!!iaut!!.value=''; this.form.f_aut2_id!!iaut!!.value='0'; \" />
	                <input type='hidden' name='f_aut2_id!!iaut!!' id='f_aut2_id!!iaut!!' value=\"!!f_aut2_id!!\" />
	                <input type='hidden' name='f_auth_number_to_compare_2!!iaut!!' id='f_auth_number_to_compare_2!!iaut!!' value=\"!!authority_number_2!!\" />
	            </div>
	        </div>
	        <!--    Fonction    -->
	        <div id='el1Child_3b_others' class='colonne_suite'>
	            <div class='row'>
	                <input type='text' class='saisie-15emr' id='f_existing_f2!!iaut!!' name='f_existing_f2!!iaut!!' completion='fonction' autfield='f_f2_code!!iaut!!' value=\"!!author_function_label_2!!\" />
	                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_f2_code!!iaut!!&p2=f_existing_f2!!iaut!!', 'selector')\" />
	                <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_existing_f2!!iaut!!.value=''; this.form.f_f2_code!!iaut!!.value='0'; \" />
	                <input type='hidden' name='f_f2_code!!iaut!!' id='f_f2_code!!iaut!!' value=\"!!author_function_2!!\" />
	            </div>
	        </div>
		</blockquote>

		<br clear=\"all\" />
		<input type=\"radio\" id=\"author2_type_insert_new_!!iaut!!\" !!author2_type_insert_new_!! value=\"insert_new\" name=\"author2_type_!!iaut!!\" /><label for=\"author2_type_insert_new_!!iaut!!\">".$msg['notice_integre_author_new']."</label>
		<blockquote>
			<div class='row'>
				<div class='colonne2' >
					<select name='f_author_type_2!!iaut!!' id='f_author_type_2!!iaut!!' onChange='changeAuthorType(2!!iaut!!);'>
						<option value='70' !!author_type_70_2!!>$msg[203]</option>
						<option value='71' !!author_type_71_2!!>$msg[204]</option>
						<option value='72' !!author_type_72_2!!>".$msg['congres_libelle']."</option>
					</select>
				</div>
				<div class='colonne_suite' >
					<label for='f_f2!!iaut!!' class='etiquette'>$msg[245]</label>
				</div>
			</div>
			<div class='row'>
				<div class='colonne2'>
					<input type='text' class='saisie-20em' name='f_author_name_2!!iaut!!' value=\"!!author_name_2!!\" />
					<input type='text' class='saisie-15em' name='f_author_rejete_2!!iaut!!' value=\"!!author_rejete_2!!\" />
					<input type='text' class='saisie-15em' name='f_authority_number_2!!iaut!!' value=\"!!authority_number_2!!\" />
				</div>
				<div class='colonne_suite' >
					<input type='text' class='saisie-15emr' id='f_f2!!iaut!!' name='f_f2!!iaut!!' readonly value=\"!!author_function_label_2!!\" />
					<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=function&caller=notice&p1=f_author_function_2!!iaut!!&p2=f_f2!!iaut!!', 'selector')\" />
					<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_f2!!iaut!!.value=''; this.form.f_author_function_2!!iaut!!.value=''; \" />
					<input type='hidden' name='f_author_function_2!!iaut!!' value=\"!!author_function_2!!\" />
				</div>
			</div>
			<div class='row'>
				<div class='colonne4' id='l_author_date_2!!iaut!!'>
					<label for='f_author_date_2!!iaut!!' class='etiquette'>$msg[713]</label>
				</div>
				<div class='colonne4' id='l_author_lieu_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
					<label for='f_author_lieu_2!!iaut!!' class='etiquette'>".$msg['congres_lieu_libelle']."</label>
				</div>
				<div class='colonne_suite' id='l_author_pays_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
					<label for='f_author_pays_2!!iaut!!' class='etiquette'>".$msg['congres_pays_libelle']."</label>
				</div>
			</div>
			<div class='row'>
				<div class='colonne4' id='div_author_date_2!!iaut!!'>
					<input type='text' class='saisie-20em' name='f_author_date_2!!iaut!!' id='f_author_date_2!!iaut!!' value=\"!!author_date_2!!\" />
				</div>
				<div class='colonne4' id='div_author_lieu_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
					<input type='text' class='saisie-20em' name='f_author_lieu_2!!iaut!!' id='f_author_lieu_2!!iaut!!' value=\"!!author_lieu_2!!\" />
				</div>
				<div class='colonne_suite' id='div_author_pays_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
					<input type='text' class='saisie-20em' name='f_author_pays_2!!iaut!!' id='f_author_pays_2!!iaut!!' value=\"!!author_pays_2!!\" />
				</div>
			</div>
			<div class='row'>
					<div class='colonne4' id='l_author_ville_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
						<label for='f_author_ville_2!!iaut!!' class='etiquette'>".$msg['congres_ville_libelle']."</label>
					</div>
					<div class='colonne4' id='l_author_subdivision_2!!iaut!!'  style='display:!!display_2!!iaut!!!!'>
						<label for='f_author_subdivision_2!!iaut!!' class='etiquette'>".$msg['congres_subdivision_libelle']."</label>
					</div>
					<div class='colonne_suite' id='l_author_numero_2!!iaut!!'  style='display:!!display_2!!iaut!!!!'>
						<label for='f_author_numero_2!!iaut!!' class='etiquette'>".$msg['congres_numero_libelle']."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne4' id='div_author_ville_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
						<input type='text' class='saisie-20em' name='f_author_ville_2!!iaut!!' id='f_author_ville_2!!iaut!!' value=\"!!author_ville_2!!\" />
					</div>
					<div class='colonne4' id='div_author_subdivision_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
						<input type='text' class='saisie-20em' name='f_author_subdivision_2!!iaut!!' id='f_author_subdivision_2!!iaut!!' value=\"!!author_subdivision_2!!\" />
					</div>
					<div class='colonne_suite' id='div_author_numero_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
						<input type='text' class='saisie-20em' name='f_author_numero_2!!iaut!!' id='f_author_numero_2!!iaut!!' value=\"!!author_numero_2!!\" />
					</div>
				</div>
				<div class='row'>
					<div class='colonne' id='l_author_web_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
						<label for='f_author_web_2!!iaut!!' class='etiquette'>".$msg[147]."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne' id='div_author_web_2!!iaut!!' style='display:!!display_2!!iaut!!!!'>
						<input type='text' class='saisie-80em' name='f_author_web_2!!iaut!!' id='f_author_web_2!!iaut!!' value=\"!!author_web_2!!\" maxlength='255' />
						<input class='bouton' type='button' onClick=\"check_link('f_author_web_2!!iaut!!')\" title='".$msg['CheckLink']."' value='".$msg['CheckButton']."' />
					</div>
				</div>
				<div class='row'>
					<div class='colonne'>
						<label for='f_author_comment_2!!iaut!!' class='etiquette'>".$msg['author_comment']."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne'>
						<textarea class='saisie-80em' id='f_author_comment_2!!iaut!!' name='f_author_comment_2!!iaut!!' cols='62' rows='4' wrap='virtual'>!!author_comment_2!!</textarea>
					</div>
				</div>
			<br />
			<hr style='margin: 2px 0 2px 0;'>
		</blockquote>
	" ;


//	----------------------------------------------------
//	Adresse, éditeurs, collection
// 	  $ptab[2] : contenu de l'onglet 2
//	----------------------------------------------------
$ptab[2] = "
<!-- onglet 2 -->
<div id='el2Parent' class='parent'>
	<h3>
    ".get_expandBase_button('el2')."
	$msg[249]
	</h3>
</div>
<div id='el2Child' class='child' >
	<!--	Editeur	-->
	<input type=\"radio\" id=\"editor_type_use_existing\" value=\"use_existing\" !!editor_type_use_existing!! name=\"editor_type\" /><label for=\"editor_type_use_existing\">".$msg['notice_integre_editor_use_existing']."</label>
	<blockquote>
		<div id='el2Child_0a' class='row'>
		    <label for='f_ed1' class='etiquette'>$msg[164]</label>
		</div>
		<div id='el2Child_0b' class='row'>
			<input type='text' completion='publishers' autfield='f_ed1_id' id='f_ed1' name='f_ed1' value=\"!!f_ed1!!\" class='saisie-30emr' />
		    <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=editeur&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=f_coll_existing_id&p4=f_coll_existing&p5=f_subcoll_existing_id&p6=f_subcoll_existing&deb_rech='+".pmb_escape()."(this.form.f_ed1.value), 'selector')\" />
		    <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_ed1.value=''; this.form.f_ed1_id.value='0'; \" />
		    <input type='hidden' name='f_ed1_id' id='f_ed1_id' value=\"!!f_ed1_id!!\" />
		</div>
	</blockquote>
	<input type=\"radio\" id=\"editor_type_insert_new\" !!editor_type_insert_new!! value=\"insert_new\" name=\"editor_type\" /><label for=\"editor_type_insert_new\">".$msg['notice_integre_editor_new']."</label>
	<blockquote>
		<div class='row'>
	        <div class='colonne2'>
				<label for='f_editor_name_0' class='etiquette'>$msg[164]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_editor_name_0' name='f_editor_name_0' value=\"!!editor_name_0!!\" />
					</div>
				</div>
		    </div>
	        <div class='colonne_suite'>
				<label for='f_editor_ville_0' class='etiquette'>$msg[72]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_editor_ville_0' name='f_editor_ville_0' value=\"!!editor_ville_0!!\" />
					</div>
				</div>
		    </div>
		</div>
	</blockquote>
	<br clear=\"all\" /><br /><br /><br />

	<!--	Collection	-->
	<input type=\"radio\" id=\"collection_type_use_existing\" value=\"use_existing\" !!collection_type_use_existing!! name=\"collection_type\" /><label for=\"collection_type_use_existing\">".$msg['notice_integre_collection_use_existing']."</label>
	<blockquote>
		<div class='row'>
		    <div class='colonne2'>
		    	<label for='f_collection_name' class='etiquette'>$msg[250]</label>
		    	<div class='row'>
		    		<div class='row'>
						<input type='text' completion='collections' autfield='f_coll_existing_id' id='f_coll_existing' name='f_coll_existing' value=\"!!f_coll_existing!!\" class='saisie-30emr' linkfield='f_ed1_id' />
						<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=collection&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=f_coll_existing_id&p4=f_coll_existing&p5=f_subcoll_existing_id&p6=f_subcoll_existing&deb_rech='+this.form.f_coll_existing.value, 'selector')\" />
						<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_coll_existing.value=''; this.form.f_coll_existing_id.value='0'; \" />
						<input type='hidden' name='f_coll_existing_id' id='f_coll_existing_id' value=\"!!f_coll_existing_id!!\" />
					</div>
				</div>
			</div>

			<!--	No. dans la collection	-->
			<div class='colonne_suite'>
				<label for='f_nbr_in_collection' class='etiquette'>$msg[253]</label>
				<div class='row'>
					<input type='text' class='saisie-10em' id='f_nbr_in_collection' name='f_nbr_in_collection' value=\"!!nbr_in_collection!!\" />
				</div>
			</div>
			<div class='row'></div>
		</div>
	</blockquote>
	<input type=\"radio\" id=\"collection_type_insert_new\" value=\"insert_new\" !!collection_type_insert_new!! name=\"collection_type\"><label for=\"collection_type_insert_new\">".$msg['notice_integre_collection_new']."</label>
	<blockquote>
		<div class='row'>
	        <div class='colonne2'>
				<label for='f_collection_name' class='etiquette'>$msg[250]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_collection_name' name='f_collection_name' value=\"!!collection_name!!\" />
					</div>
				</div>
		    </div>
	        <div class='colonne_suite'>
				<label for='f_collection_issn' class='etiquette'>$msg[165]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_collection_issn' name='f_collection_issn' value=\"!!collection_issn!!\" />
					</div>
				</div>
		    </div>
		</div>
	</blockquote>
	<br clear=\"all\" /><br /><br /><br />

	<!--	Sous collection	-->
	<input type=\"radio\" id=\"subcollection_type_use_existing\" value=\"use_existing\" !!subcollection_type_use_existing!! name=\"subcollection_type\" /><label for=\"subcollection_type_use_existing\">".$msg['notice_integre_subcollection_use_existing']."</label>
	<blockquote>
	    <label for='f_subcoll_existing' class='etiquette'>$msg[251]</label>
	    <div class='row'>
			<input type='text' completion='subcollections' autfield='f_subcoll_existing_id' id='f_subcoll_existing' name='f_subcoll_existing' value=\"!!f_subcoll_existing!!\" class='saisie-30emr' linkfield='f_coll_existing_id' />
			<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=subcollection&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=f_coll_existing_id&p4=f_coll_existing&p5=f_subcoll_existing_id&p6=f_subcoll_existing&deb_rech='+".pmb_escape()."(this.form.f_subcoll_existing.value), 'selector')\" />
			<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_subcoll_existing.value=''; this.form.f_subcoll_existing_id.value='0'; \" />
			<input type='hidden' id='f_subcoll_existing_id' name='f_subcoll_existing_id' value=\"!!f_subcoll_existing_id!!\" />
		</div>
	</blockquote>
	<input type=\"radio\" id=\"subcollection_type_insert_new\" value=\"insert_new\" !!subcollection_type_insert_new!! name=\"subcollection_type\" /><label for=\"subcollection_type_insert_new\">".$msg['notice_integre_subcollection_new']."</label>
	<blockquote>
		<div class='row'>
	        <div class='colonne2'>
				<label for='f_subcollection_name' class='etiquette'>$msg[251]</label>
				<div class='row'>
					<div class='row'>
				<input type='text' class='saisie-30em' id='f_subcollection_name' name='f_subcollection_name' value=\"!!subcollection_name!!\" />
					</div>
				</div>
		    </div>
	        <div class='colonne_suite'>
				<label for='f_subcollection_issn' class='etiquette'>$msg[165]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_subcollection_issn' name='f_subcollection_issn' value=\"!!subcollection_issn!!\" />
					</div>
				</div>
		    </div>
		</div>
	</blockquote>
	<br clear=\"all\" /><br /><br />

	<div class='row'>
		<!--	Année	-->
		<div class='colonne2'>
			<label for='f_year' class='etiquette'>$msg[252]</label>
			<div class='row'>
				<input type='text' class='saisie-30em' id='f_year' name='f_year' value=\"!!year!!\" />
			</div>
		</div>

		<!--	Edition	-->
		<div class='colonne_suite'>
			<label for='f_mention_edition' class='etiquette'>".$msg['mention_edition']."</label>
			<div class='row'>
				<input type='text' class='saisie-30em' id='f_mention_edition' name='f_mention_edition' value=\"!!mention_edition!!\" />
			</div>
		</div>
	</div>
	<br clear=\"all\" /><br /><br />

	<!--	Autre éditeur	-->
	<div class='row'>
		<label for='f_ed2' class='etiquette'>$msg[254]</label>
	</div>

	<input type=\"radio\" id=\"editor1_type_use_existing\" value=\"use_existing\" !!editor1_type_use_existing!! name=\"editor1_type\" /><label for=\"editor1_type_use_existing\">".$msg['notice_integre_editor_use_existing']."</label>
	<blockquote>
		<div id='el2Child_0a' class='row'>
	    	<label for='f_ed11' class='etiquette'>$msg[164]</label>
		</div>
		<div id='el2Child_0b' class='row'>
			<input type='text' completion='publishers' autfield='f_ed11_id' id='f_ed11' name='f_ed11' value=\"!!f_ed11!!\" class='saisie-30emr' />
	   	 	<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=editeur&caller=notice&p1=f_ed11_id&p2=f_ed11&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll&deb_rech='+".pmb_escape()."(this.form.f_ed11.value), 'selector')\" />
	   	 	<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_ed11.value=''; this.form.f_ed11_id.value='0'; \" />
	    	<input type='hidden' name='f_ed11_id' id='f_ed11_id' value=\"!!f_ed11_id!!\" />
		</div>
	</blockquote>

	<input type=\"radio\" id=\"editor1_type_insert_new\" !!editor1_type_insert_new!! value=\"insert_new\" name=\"editor1_type\" /><label for=\"editor1_type_insert_new\">".$msg['notice_integre_editor_new']."</label>
	<blockquote>
		<div class='row'>
	        <div class='colonne2'>
				<label for='f_editor_name_1' class='etiquette'>$msg[164]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_editor_name_1' name='f_editor_name_1' value=\"!!editor_name_1!!\" />
					</div>
				</div>
		    </div>
	        <div class='colonne_suite'>
				<label for='f_editor_ville_1' class='etiquette'>$msg[72]</label>
				<div class='row'>
					<div class='row'>
						<input type='text' class='saisie-30em' id='f_editor_ville_1' name='f_editor_ville_1' value=\"!!editor_ville_1!!\" />
					</div>
				</div>
		    </div>
		</div>
	</blockquote>

</div><!-- fin onglet 2 -->";

//	----------------------------------------------------
//	ISBN, EAN ou no. commercial
// 	  $ptab[3] : contenu de l'onglet 3
//	----------------------------------------------------
$ptab[3] = "
<!-- onglet 3 -->
<div id='el3Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el3', '255')."
		$msg[255]
	</h3>
</div>

<div id='el3Child' class='child' >
	<!--	ISBN, EAN ou no. commercial	-->
	<div class='row'>
		<label for='f_cb' class='etiquette'>$msg[255]</label>
	</div>
	<div class='row'>
		<input class='saisie-20emr' id='f_cb' name='f_cb' readonly value=\"!!isbn!!\" />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('$base_path/catalog/setcb.php', 'getcb')\" />
		<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_cb.value=''; \" />
	</div>
</div>
";

//	----------------------------------------------------
//	Collation
// 	  $ptab[4] : contenu de l'onglet 4 (collation)
//	----------------------------------------------------

$ptab[4] = "
<!-- onglet 4 -->
<div id='el4Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el4', '257')."
		$msg[258]
	</h3>
</div>

<div id='el4Child' class='child' >
	<!--	Importance matérielle (nombre de pages, d'éléments...)	-->
	<div class='row'>
		<label for='f_page_nbr' class='etiquette'>$msg[259]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_page_nbr' name='f_page_nbr' value=\"!!page_nbr!!\" />
	</div>

	<!--	Autres caractèristiques matérielle (ill., ...)	-->
	<div class='row'>
		<label for='f_illustration' class='etiquette'>$msg[260]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_illustration' name='f_illustration' value=\"!!illustration!!\" />
	</div>

	<!--	Format	-->
	<div class='row'>
		<label for='f_size' class='etiquette'>$msg[261]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_size' name='f_size' value=\"!!size!!\" />
	</div>

	<!--	Prix	-->
	<div class='row'>
		<label for='f_prix' class='etiquette'>$msg[4050]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_prix' name='f_prix' value=\"!!prix!!\" />
	</div>

	<!--	Matériel d'accompagnement	-->
	<div class='row'>
		<label for='f_accompagnement' class='etiquette'>$msg[262]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_accompagnement' name='f_accompagnement' value=\"!!accompagnement!!\" />
	</div>

</div>
";

//	----------------------------------------------------
//	Notes
// 	  $ptab[5] : contenu de l'onglet 5 (notes)
//	----------------------------------------------------
$ptab[5] = "
<!-- onglet 5 -->
<div id='el5Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el5', '263')."
		$msg[264]
	</h3>
</div>

<div id='el5Child' class='child' >

	<!--	Note générale	-->
	<div class='row'>
		<label for='f_general_note' class='etiquette'>$msg[265]</label>
	</div>
	<div class='row'>
		<textarea id='f_general_note' class='saisie-80em' name='f_general_note' rows='4' wrap='virtual'>!!general_note!!</textarea>
	</div>

	<!--	Note de contenu	-->
	<div class='row'>
		<label for='f_content_note' class='etiquette'>$msg[266]</label>
	</div>
	<div class='row'>
		<textarea id='f_content_note' class='saisie-80em' name='f_content_note' cols='62' rows='3' wrap='virtual'>!!content_note!!</textarea>
	</div>

	<!--	Résumé/extrait	-->
	<div class='row'>
		<label for='f_abstract_note' class='etiquette'>$msg[267]</label>
	</div>
	<div class='row'>
		<textarea class='saisie-80em' id='f_abstract_note' name='f_abstract_note' cols='62' rows='4' wrap='virtual'>!!abstract_note!!</textarea>
	</div>

</div>
";

//	----------------------------------------------------
//	Indexation
// 	  $ptab[6] : contenu de l'onglet 6 (indexation)
//	----------------------------------------------------


// Correction du bug d'ajout de catégorie dans l'onglet indexation
// Reprise du traitement de catal_form.tpl.php et de notice.class.php .NG72

// nombre de parties du form
$nb_onglets = 9;
$ptab[6] = "
<script>
	function fonction_selecteur_categ() {
		name=this.getAttribute('id').substring(5);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		openPopUp('./select.php?what=categorie&caller=notice&p1='+name_id+'&p2='+name+'&dyn=1', 'selector');
	}
	function fonction_raz_categ() {
		name=this.getAttribute('id').substring(4);
		name_id = name.substr(0,7)+'_id'+name.substr(7);
		document.getElementById(name_id).value=0;
		document.getElementById(name).value='';
	}
	function add_categ() {
        template = document.getElementById('el6Child_0');
        categ=document.createElement('div');
        categ.className='row';

        suffixe = eval('document.notice.max_categ.value');

        categ.setAttribute('id','drag_'+suffixe);
        categ.setAttribute('order',suffixe);
        categ.setAttribute('highlight','categ_highlight');
        categ.setAttribute('downlight','categ_downlight');
        categ.setAttribute('dragicon','".get_url_icon('icone_drag_notice.png')."');
        categ.setAttribute('handler','handle_'+suffixe);
        categ.setAttribute('recepttype','categ');
        categ.setAttribute('recept','yes');
        categ.setAttribute('dragtype','categ');
        categ.setAttribute('draggable','yes');

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
        del_f_categ.setAttribute('value','".$msg['raz']."');

        f_categ_id = document.createElement('input');
        f_categ_id.name='f_categ_id'+suffixe;
        f_categ_id.setAttribute('type','hidden');
        f_categ_id.setAttribute('id','f_categ_id'+suffixe);
        f_categ_id.setAttribute('value','');

        var f_categ_span_handle = document.createElement('span');
        f_categ_span_handle.setAttribute('id','handle_'+suffixe);
        f_categ_span_handle.style.float='left';
        f_categ_span_handle.style.paddingRight='7px';

        var f_categ_drag_img = document.createElement('img');
        f_categ_drag_img.setAttribute('src','".get_url_icon('sort.png')."');
        f_categ_drag_img.style.width='12px';
        f_categ_drag_img.style.verticalAlign='middle';

        f_categ_span_handle.appendChild(f_categ_drag_img);
        f_categ_span_handle.appendChild(f_categ_drag_img);

        categ.appendChild(f_categ_span_handle);

        categ.appendChild(f_categ);
        space=document.createTextNode(' ');
        categ.appendChild(space);
        categ.appendChild(del_f_categ);
        categ.appendChild(f_categ_id);

        template.appendChild(categ);

        document.notice.tab_categ_order.value= document.notice.tab_categ_order.value + ',' +document.notice.max_categ.value;
        document.notice.max_categ.value=suffixe*1+1*1 ;

        ajax_pack_element(f_categ);
        init_drag();
    }
</script>
<!-- onglet 6 -->
<div id='el6Parent' class='parent'>
	<h3>
	    <img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='el6Img' title=\"$msg[268]\" onClick=\"expandBase('el6', true);recalc_recept(); return false;\" />
	    $msg[269]
	</h3>
</div>
<div id='el6Child' class='child' etirable='yes' title='".htmlentities($msg[269],ENT_QUOTES, $charset)."'>
	!!zone_categ_form!!

	<div id='el6Child_1' title='".htmlentities($msg['indexint_catal_title'],ENT_QUOTES, $charset)."' movable='yes'>
    	<!--    indexation interne    -->
    	<div id='el6Child_1a' class='row'>
        	<label for='f_categ' class='etiquette'>".$msg['indexint_catal_title']."</label>
    	</div>
    	<div id='el6Child_1b' class='row'>
			<input type=\"radio\" name=\"indexint_type\" value=\"use_existing\" !!indexint_type_use_existing!! id=\"indexint_type_use_existing\" /><label for=\"indexint_type_use_existing\">".$msg['notice_integre_indexint_use_existing']."</label>
	        <blockquote>
				!!multiple_index_int_propositions!!
				<input type='text' class='saisie-80emr' id='f_indexint' name='f_indexint' value=\"!!indexint!!\" completion=\"indexint\" autfield=\"f_indexint_id\" />
				<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=indexint&caller=notice&param1=f_indexint_id&param2=f_indexint&parent=0&deb_rech='+".pmb_escape()."(this.form.f_indexint.value), 'selector')\" />
		        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_indexint.value=''; this.form.f_indexint_id.value='0'; \" />
		        <input type='hidden' name='f_indexint_id' id='f_indexint_id' value='!!indexint_id!!' />
	        </blockquote>
			<input type='radio' name=\"indexint_type\" value=\"insert_new\" !!indexint_type_insert_new!! id=\"indexint_type_insert_new\" /><label for=\"indexint_type_insert_new\">".$msg['notice_integre_indexint_new']."</label>
	        <blockquote>
				<div class='row'>
			        <div class='colonne' style='margin-right:5px'>
						<label for='f_indexint_new' class='etiquette'>".$msg['indexint_nom']."</label>
						<div class='row'>
							<input type='text' class='saisie-20em' id='f_indexint_new' name='f_indexint_new' value=\"!!indexint_new_name!!\" />
						</div>
				    </div>
			        <div class='colonne' style='margin-right:5px'>
						<label for='f_indexint_new_comment' class='etiquette'>".$msg['indexint_comment']."</label>
						<div class='row'>
							<input type='text' class='saisie-50em' id='f_indexint_new_comment' name='f_indexint_new_comment' value=\"!!indexint_new_comment!!\" />
						</div>
				    </div>
			        <div class='colonne'>
						<br />
						<div class='row'>
							!!multiple_pclass_combo_box!!
						</div>
					</div>
				</div>
	        </blockquote>
	    </div>
	</div>
	<div id='el6Child_2' title='".htmlentities($msg[324],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Indexation libre    -->
	    <div id='el6Child_2a' class='row'>
	        <label for='f_free_index' class='etiquette'>$msg[324]</label>
	    </div>
	    <div id='el8Child_2b' class='row'>
	        <textarea class='saisie-80em' id='f_free_index' completion='tags' keys='113' name='f_free_index' rows='3' wrap='virtual'>!!f_free_index!!</textarea>
	    </div>
	    <div id='el8Child_2_comment' class='row'>
	        <span>$msg[324]$msg[1901]$msg[325]</span>
	    </div>
	</div>
	!!index_concept_form!!
</div>
";

//    ----------------------------------------------------
//     Catégories répétables
//       $ptab[60]
//    ----------------------------------------------------
$ptab[60] = "
	<table><tr><td width=\"50%\">!!vedette_libelle!!</td><td width=\"50%\">!!thesaurus_select!!</td></tr></table>
    ";

//    ----------------------------------------------------
//     Langue de la publication
//       $ptab[7] : contenu de l'onglet 7 (langues)
//    ----------------------------------------------------

$ptab[7] = "
<script type='text/javascript'>
	function fonction_selecteur_lang() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_code'+name.substr(6);
        openPopUp('./select.php?what=lang&caller=notice&p1='+name_id+'&p2='+name, 'selector');
    }
    function add_lang() {
        templates.add_completion_selection_field('f_lang', 'f_lang_code', 'langue', fonction_selecteur_lang);
    }

    function fonction_selecteur_langorg() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,9)+'_code'+name.substr(9);
        openPopUp('./select.php?what=lang&caller=notice&p1='+name_id+'&p2='+name, 'selector');
    }
    function add_langorg() {
        templates.add_completion_selection_field('f_langorg', 'f_langorg_code', 'langue', fonction_selecteur_langorg);
    }

</script>

<!-- onglet 7 -->
<div id='el7Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el7', '537')."
	    $msg[710]
	</h3>
</div>
<div id='el7Child' class='child' etirable='yes' title='".htmlentities($msg[710],ENT_QUOTES, $charset)."'>
	<div id='el7Child_0' title='".htmlentities($msg[710],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Langues    -->
	    <div id='el7Child_0a' class='row'>
	        <label class='etiquette'>$msg[710]</label>
	        <input type='button' class='bouton' value='+' onClick=\"add_lang();\"/>
	    </div>
	    <input type='hidden' id='max_lang' name='max_lang' value=\"!!max_lang!!\" />
	    !!langues_repetables!!
	    <div id='addlang'/>
	    </div>
	</div>
	<div id='el7Child_1' title='".htmlentities($msg[711],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Langues    -->
	    <div id='el7Child_1a' class='row'>
	        <label class='etiquette'>$msg[711]</label>
	        <input type='button' class='bouton' value='+' onClick=\"add_langorg();\"/>
	    </div>
	    <input type='hidden' id='max_langorg' name='max_langorg' value=\"!!max_langorg!!\" />
	    !!languesorg_repetables!!
	    <div id='addlangorg'/>
	    </div>
	</div>
</div>
";

//    ----------------------------------------------------
//     Langues répétables
//       $ptab[70]
//    ----------------------------------------------------
$ptab[70] = "
    <div id='el7Child_0a' class='row'>
        <input type='text' class='saisie-30emr' id='f_lang!!ilang!!' name='f_lang!!ilang!!' value=\"!!lang!!\" completion=\"langue\" autfield=\"f_lang_code!!ilang!!\" />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=notice&p1=f_lang_code!!ilang!!&p2=f_lang!!ilang!!', 'selector')\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_lang!!ilang!!.value=''; this.form.f_lang_code!!ilang!!.value=''; \" />
        <input type='hidden' name='f_lang_code!!ilang!!' id='f_lang_code!!ilang!!' value='!!lang_code!!' />
        <input type='button' id='button_add_f_lang_code' class='bouton' value='+' onClick=\"add_lang();\"/>
    </div>
    ";

$ptab[701] = "
    <div id='el7Child_0a' class='row'>
        <input type='text' class='saisie-30emr' id='f_lang!!ilang!!' name='f_lang!!ilang!!' value=\"!!lang!!\" completion=\"langue\" autfield=\"f_lang_code!!ilang!!\" />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=notice&p1=f_lang_code!!ilang!!&p2=f_lang!!ilang!!', 'selector')\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_lang!!ilang!!.value=''; this.form.f_lang_code!!ilang!!.value=''; \" />
        <input type='hidden' name='f_lang_code!!ilang!!' id='f_lang_code!!ilang!!' value='!!lang_code!!' />
    </div>
    ";

//    ----------------------------------------------------
//     Langues originales répétables
//       $ptab[71]
//    ----------------------------------------------------
$ptab[71] = "
    <div id='el7Child_0b' class='row'>
        <input type='text' class='saisie-30emr' id='f_langorg!!ilangorg!!' name='f_langorg!!ilangorg!!' value=\"!!langorg!!\" completion=\"langue\" autfield=\"f_langorg_code!!ilangorg!!\" />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=notice&p1=f_langorg_code!!ilangorg!!&p2=f_langorg!!ilangorg!!', 'selector')\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_langorg!!ilangorg!!.value=''; this.form.f_langorg_code!!ilangorg!!.value=''; \" />
        <input type='hidden' name='f_langorg_code!!ilangorg!!' id='f_langorg_code!!ilangorg!!' value='!!langorg_code!!' />
        <input type='button' id='button_add_f_langorg_code' class='bouton' value='+' onClick=\"add_langorg();\"/>
    </div>
    ";
$ptab[711] = "
    <div id='el7Child_0b' class='row'>
        <input type='text' class='saisie-30emr' id='f_langorg!!ilangorg!!' name='f_langorg!!ilangorg!!' value=\"!!langorg!!\" completion=\"langue\" autfield=\"f_langorg_code!!ilangorg!!\" />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=lang&caller=notice&p1=f_langorg_code!!ilangorg!!&p2=f_langorg!!ilangorg!!', 'selector')\" />
        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_langorg!!ilangorg!!.value=''; this.form.f_langorg_code!!ilangorg!!.value=''; \" />
        <input type='hidden' name='f_langorg_code!!ilangorg!!' id='f_langorg_code!!ilangorg!!' value='!!langorg_code!!' />
    </div>
    ";


//	----------------------------------------------------
//	Liens
// 	  $ptab[1] : contenu de l'onglet 8 (liens)
//	----------------------------------------------------

$ptab[8] = "
<!-- onglet 8 -->
<div id='el8Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el8', '274')."
		$msg[274]
	</h3>
</div>
<div id='el8Child' class='child'>
	<!--	URL associée	-->
	<div class='row'>
		<label for='f_link_url' class='etiquette'>$msg[275]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_link_url' name='f_link_url' value=\"!!link_url!!\" />
	</div>
	<!--	Format électronique de la ressource	-->
	<div class='row'>
		<label for='f_link_format' class='etiquette'>$msg[276]</label>
	</div>
	<div class='row'>
		<input type='text' class='saisie-80em' id='f_link_format' name='f_link_format' value=\"!!link_format!!\" />
	</div>
</div>
";

//    ----------------------------------------------------
//    Champs personalisés
//       $ptab[9] : Contenu de l'onglet 9 (champs personalisés)
//    ----------------------------------------------------
$ptab[9] = "
<!-- onglet 9 -->
<div id='el9Parent' class='parent'>
	<h3>
	    <img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='el9Img' onClick=\"expandBase('el9', true); recalc_recept(); return false;\" title='".$msg['notice_champs_perso']."' /> ".$msg['notice_champs_perso']."
	</h3>
</div>
<div id='el9Child' class='child'>
	!!champs_perso!!
</div>
";

$ptab[10] = "
<!-- onglet 10 -->
<div id='el10Parent' class='parent'>
<h3>
    ".get_expandBase_button('el10', 'notice_champs_gestion')." ".$msg['notice_champs_gestion']."
</h3>
</div>
<div id='el10Child' class='child'>
	<div class='row'>
	    <label for='f_notice_statut' class='etiquette'>".$msg['notice_statut_gestion']."</label>
	</div>
	<div class='row'>
		!!notice_statut!!
	</div>
	<div class='row'>
	    <label for='f_new_gestion' class='etiquette'>".$msg["notice_is_new_gestion"]."</label>
	</div>
	<div class='row'>
	    <input type='radio' name='f_notice_is_new' !!notice_is_new_checked_no!! value='0'>".$msg["notice_is_new_gestion_no"]."<br>
	    <input type='radio' name='f_notice_is_new' !!notice_is_new_checked_yes!! value='1'>".$msg["notice_is_new_gestion_yes"]."<br>
	</div>
	<!--    commentaire de gestion    -->
	<div class='row'>
	    <label for='f_commentaire_gestion' class='etiquette'>".$msg['notice_commentaire_gestion']."</label>
	</div>
	<div class='row'>
	    <textarea class='saisie-80em' id='f_commentaire_gestion' name='f_commentaire_gestion' cols='62' rows='5' wrap='virtual'>!!commentaire_gestion!!</textarea>
	</div>
	<!--    URL vignette speciale    -->
	<div class='row'>
	    <label for='f_thumbnail_url' class='etiquette'>".$msg['notice_thumbnail_url']."</label>
	</div>
	<div class='row'>
	    <input type=text class='saisie-80em' id='f_thumbnail_url' name='f_thumbnail_url' value=\"!!thumbnail_url!!\" />
	</div>";
global $pmb_notice_img_folder_id;
if($pmb_notice_img_folder_id)
	$ptab[10].= "
		<div id='el10Child_6' title='".htmlentities($msg['notice_img_load'],ENT_QUOTES, $charset)."' movable='yes'>
			<!--    Vignette upload    -->
			<div id='el10Child_6a' class='row'>
				<label for='f_img_load' class='etiquette'>".$msg['notice_img_load']."</label>!!message_folder!!
			</div>
			<div id='el10Child_6b' class='row'>
				<input type='file' class='saisie-80em' id='f_img_load' name='f_img_load' rows='1' wrap='virtual' value='' />
			</div>
		</div>";
$ptab[10] .= "
	<div class='row'>
	    <label for='f_notice_lang' class='etiquette'>".$msg['indexation_lang_select']."</label>
	</div>
	<div class='row'>
	   !!indexation_lang!!
	</div>
</div>
";

//    ----------------------------------------------------
//    Documents Numériques
//       $ptab[11] : Contenu de l'onglet 11 (documents numériques)
//    ----------------------------------------------------
$ptab[1110] = "
<!-- onglet 11 -->
<div id='el11Parent' class='parent'>
	<h3>
	    ".get_expandBase_button('el11', 'noticeintegre_docnum')." ".$msg['noticeintegre_docnum']."
	</h3>
</div>

<div id='el11Child' class='child'>
  <input type=\"hidden\" name=\"doc_num_count\" value=\"!!docnum_count!!\" />
  !!docnums!!
</div>
";

$ptab[1111] = "
	<div class='row'>
	    <input type=\"checkbox\" checked id=\"include_doc_num!!docnumid!!\" name=\"include_doc_num!!docnumid!!\" /><label for=\"include_doc_num!!docnumid!!\">".$msg['noticeintegre_docnum_integre']."</label>
	</div>
	<blockquote>
		<div class='row'>
		    <input name=\"doc_num_nodownload!!docnumid!!\" id=\"doc_num_nodownload!!docnumid!!\" type=\"checkbox\" !!upload_doc_num!! /><label for=\"doc_num_nodownload!!docnumid!!\">".$msg['noticeintegre_docnum_integre_nodownload']."</label>
		</div>
		<div class='row'>
		    <label for=\"doc_num_caption!!docnumid!!\">".$msg['noticeintegre_docnum_integre_caption']."</label>
		</div>
		<div class='row'>
		    <input type=\"text\" class='saisie-80em' id=\"doc_num_caption!!docnumid!!\" name=\"doc_num_caption!!docnumid!!\" value=\"!!docnum_caption!!\" />
		</div>
		<div class='row'>
		    <label for=\"doc_num_url!!docnumid!!\">".$msg['noticeintegre_docnum_integre_url']."</label>
		</div>
		<div class='row'>
			<input type='hidden' name='doc_num_filename!!docnumid!!' id='doc_num_filename!!docnumid!!' value='!!docnum_filename!!' />
		    <input type=\"text\" class='saisie-80em' id=\"doc_num_url!!docnumid!!\" name=\"doc_num_url!!docnumid!!\" value=\"!!docnum_url!!\" />
		</div>
    	<div class='row'>
		    <label for=\"doc_num_statut!!docnumid!!\">".$msg['noticeintegre_docnum_integre_statut']."</label>
		</div>
		<div class='row'>
			!!docnum_statutlist!!
		</div>
	</blockquote>
";

// 	  $form_notice : formulaire de notice
// Ajout javascript/ajax.js et ajax_parse_dom() NG72
global $pmb_catalog_verif_js_integration;
$form_notice = "
<!-- script de gestion des onglets -->
<script type='text/javascript' src='$base_path/javascript/tabform.js'></script>
".($pmb_catalog_verif_js_integration!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_catalog_verif_js_integration'></script>":"")."
<script type='text/javascript'>
<!--
  function test_notice(form)
    {
    ";
if($pmb_catalog_verif_js_integration!= ""){
	$form_notice.= "
		var check = check_perso_form(form);
		if(check == false) {
			return false;
		}";
}
$form_notice.= "
		titre0 = form.f_title_0.value;
		titre0 = titre0.replace(/^\s+|\s+$/g, ''); //trim la valeur
        if(titre0.length == 0) {
             alert(\"$msg[277]\");
             return false;
        }

        var selector = document.forms['notice'].elements['biblio_notice'].value;
        if(selector == 'art'){
        	var perio_type = document.forms['notice'].elements['perio_type_use_existing'].checked;
        	var bull_type =  document.forms['notice'].elements['bull_type_use_existing'].checked;
        	var perio_type_new = document.forms['notice'].elements['perio_type_new'].checked;
        	var bull_type_new =  document.forms['notice'].elements['bull_type_new'].checked;

        	if(!perio_type && bull_type) {
        		alert(\"".$msg['z3950_bull_already_linked']."\")
        		return false;
        	}
        	if(perio_type_new && (document.getElementById('f_perio_new').value == '')){
        		alert(\"".$msg['z3950_serial_title_mandatory']."\")
        		return false;
        	}

        	if(bull_type_new && (document.getElementById('f_bull_new_titre').value == '') && (document.getElementById('f_bull_new_mention').value == '')
        	&& (document.getElementById('f_bull_new_date').value == '') && (document.getElementById('f_bull_new_num').value == '')){
        		alert(\"".$msg['z3950_fill_bull']."\")
        		return false;
        	}

        	if(perio_type && bull_type && (document.getElementById('f_bull_existing_id').value == '')){
        		alert(\"".$msg['z3950_no_bull_selected']."\")
        		return false;
        	}
        }

		return true;
    }
-->
</script>

<h1>!!libelle_form!!</h1>
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' id='notice' name='notice' method='post' action='!!action!!' enctype='multipart/form-data'>
<!--!!form_title!!-->
<div class='form-contenu'>
<div id='notice_type'>
	<div class='row'>
		<select id='biblio_notice' name='biblio_notice' onChange='hide_perio();'>
			<option value='mono' !!checked_mono!!>".$msg['acquisition_type_mono']."</option>
			<option value='perio' !!checked_perio!!>".$msg['acquisition_type_serial']."</option>
			<option value='art' !!checked_art!!>".$msg['acquisition_type_art']."</option>
		</select>
	</div>
	<div class='row'>
		!!document_type!!
	</div>
</div>
<div class='row'>
	<a href=\"javascript:expandAll()\"><img src='".get_url_icon('expand_all.gif')."' border='0' id=\"expandall\"></a>
	<a href=\"javascript:collapseAll()\"><img src='".get_url_icon('collapse_all.gif')."' border='0' id=\"collapseall\"></a>
</div>
<input type='hidden' id='b_level' name='b_level' value='!!b_level!!' />
<input type='hidden' id='h_level' name='h_level' value='!!h_level!!' />
<input type='hidden' name='f_orinot_nom' value='!!orinot_nom!!' />
<input type='hidden' name='f_orinot_pays' value='!!orinot_pays!!' />
!!notice_entrepot!!
!!zone_article!!
!!tab0!!
<hr class='spacer' />
!!tab1!!
<hr class='spacer' />
!!tab2!!
<hr class='spacer' />
!!tab3!!
<hr class='spacer' />
!!tab4!!
<hr class='spacer' />
!!tab5!!
<hr class='spacer' />
!!tab6!!";
global $pmb_use_uniform_title;
if ($pmb_use_uniform_title) $form_notice .= "<hr class='spacer' />!!tab230!!";
$form_notice .= "<hr class='spacer' />
!!tab7!!
<hr class='spacer' />
!!tab8!!
<hr class='spacer' />
!!tab9!!
<hr class='spacer' />
!!tab10!!
<hr class='spacer' />
!!tab11!!
<hr class='spacer' />
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='!!bouton_integration!!' onClick=\"if (test_notice(this.form)) {this.form.submit();} \" />
		<!-- bt_undo -->
		<input type='hidden' name='id_notice' value='!!id_notice!!' />
	</div>
	<div class='right'>
		!!retour_a_resultats!!
	</div>
</div>
<div class='row'></div>
<input type='hidden' name='notice_org' value=\"!!notice!!\"/>
<input type='hidden' name='notice_type_org' value=\"!!notice_type!!\"/>
<!--form_suite-->
<script type='text/javascript'>document.forms['notice'].elements['f_title_0'].focus();</script>
</form>
<script type='text/javascript'>
	function hide_perio(){
		var selector = document.forms['notice'].elements['biblio_notice'].value;
		if(selector == 'mono'){
			document.getElementById('zone_article').style.display = 'none';
			document.getElementById('b_level').value = 'm';
			document.getElementById('h_level').value = '0';
		} else if(selector == 'perio'){
			document.getElementById('zone_article').style.display = 'none';
			document.getElementById('b_level').value = 's';
			document.getElementById('h_level').value = '1';
		} else if(selector == 'art'){
			document.getElementById('zone_article').style.display = '';
			document.getElementById('b_level').value = 'a';
			document.getElementById('h_level').value = '2';
			set_ajax_attributes();
			ajax_parse_dom();
		}
	}

	function set_ajax_attributes(){
		var selector = document.forms['notice'].elements['biblio_notice'].value;
		if(!document.forms['notice'].elements['f_perio_existing'].getAttribute('completion') && selector == 'art'){
			document.forms['notice'].elements['f_perio_existing'].setAttribute('completion','perio');
			document.forms['notice'].elements['f_perio_existing'].setAttribute('autfield','f_perio_existing_id');

			document.forms['notice'].elements['f_bull_existing'].setAttribute('completion','bull');
			document.forms['notice'].elements['f_bull_existing'].setAttribute('autfield','f_bull_existing_id');
			document.forms['notice'].elements['f_bull_existing'].setAttribute('linkfield','f_perio_existing_id');
		}
	}
</script>
<script>set_ajax_attributes();ajax_parse_dom();</script>

";

$zone_article_form = "
	<div class='row' id='zone_article' style='display:!!display_zone_article!!'>
		<div class='colonne3'>
			<h3>".$msg['acquisition_catal_perio']."</h3>
			<input type=\"radio\" id=\"perio_type_use_existing\"  value=\"use_existing\" name=\"perio_type\"  !!perio_type_use_existing!! /><label for=\"perio_type_use_existing\">".$msg['acquisition_catal_perio_exist']."</label>
			<blockquote>
			    <div class='row'>
		            <label for='f_perio_existing' class='etiquette'>".$msg[233]."</label>
		            <div class='row' >
						<input type='text' id='f_perio_existing' class='saisie-30emr' name='f_perio_existing' value=\"!!f_perio_existing!!\" />
		                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=perio&caller=notice&param1=f_perio_existing_id&param2=f_perio_existing&deb_rech='+".pmb_escape()."(this.form.f_perio_existing.value), 'selector_notice');this.form.f_bull_existing.value=''; this.form.f_bull_existing_id.value='0';\" />
		              	<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_perio_existing.value=''; this.form.f_perio_existing_id.value='0'; this.form.f_bull_existing.value=''; this.form.f_bull_existing_id.value='0';\" />
		               	<input type='hidden' name='f_perio_existing_id' id='f_perio_existing_id' value=\"!!f_perio_existing_id!!\" />
		            </div>
				</div>
			</blockquote>
			<input type=\"radio\" id=\"perio_type_new\"  value=\"insert_new\" name=\"perio_type\" !!perio_type_new!! /><label for=\"perio_type_new\">".$msg['acquisition_catal_perio_new']."</label>
			<blockquote>
			    <div class='row'>
		            <label for='f_perio_new' class='etiquette'>".$msg[233]."</label>
		            <div class='row' >
						<input type='text' id='f_perio_new' class='saisie-30em' name='f_perio_new' value=\"!!perio_titre!!\"/>
		            </div>
				</div>
				<div class='row'>
		            <label for='f_perio_new_issn' class='etiquette'>".$msg['z3950_issn']."</label>
		            <div class='row' >
						<input type='text' id='f_perio_new_issn' class='saisie-20em' name='f_perio_new_issn' value=\"!!perio_issn!!\"/>
		            </div>
				</div>
			</blockquote>
		</div>
		<div class='colonne3'>
			<h3>".$msg['acquisition_catal_bull']."</h3>
			<input type=\"radio\" id=\"bull_type_use_existing\" !!bull_type_use_existing!! value=\"use_existing\" name=\"bull_type\" /><label for=\"bull_type_use_existing\">".$msg['acquisition_catal_bull_exist']."</label>
			<blockquote>
			    <div class='row'>
		            <label for='f_bull_existing' class='etiquette'>".$msg['abonnements_titre_numerotation']."/".$msg[4026]."</label>
		            <div class='row' >
						<input type='text' id='f_bull_existing' class='saisie-30emr' name='f_bull_existing' value=\"!!f_bull_existing!!\"/>
		                <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=bulletin&caller=notice&param1=f_bull_existing_id&param2=f_bull_existing&no_display='+this.form.f_bull_existing_id.value+'&deb_rech='+".pmb_escape()."(this.form.f_bull_existing.value)+'&idperio='+this.form.f_perio_existing_id.value, 'selector_notice')\" />
		              	<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_bull_existing.value=''; this.form.f_bull_existing_id.value='0'; \" />
		               	<input type='hidden' name='f_bull_existing_id' id='f_bull_existing_id' value=\"!!f_bull_existing_id!!\" />
		            </div>
				</div>
			</blockquote>
			<input type=\"radio\" id=\"bull_type_new\" !!bull_type_new!! value=\"insert_new\" name=\"bull_type\" /><label for=\"bull_type_new\">".$msg['acquisition_catal_bull_new']."</label>
			<blockquote>
			    <div class='row'>
			    	<div class='colonne2'>
			    		<div class='row' >
			            	<label for='f_bull_new_num' class='etiquette'>".$msg['abonnements_titre_numerotation']."</label>
						</div>
			            <div class='row' >
							<input type='text' id='f_bull_new_num' class='saisie-10em' name='f_bull_new_num' value=\"!!bull_num!!\"/>
			            </div>
		         	</div>
		         	<div class='colonne2'>
			    		<div class='row' >
			            	<label for='f_bull_new_titre' class='etiquette'>".$msg[233]."</label>
						</div>
			            <div class='row' >
							<input type='text' id='f_bull_new_titre' class='saisie-30em' name='f_bull_new_titre' value=\"!!bull_titre!!\" />
			            </div>
		         	</div>
				</div>
				<div class='row'>
					<div class='colonne2'>
						<div class='row'>
							<label class='etiquette' >$msg[4026]</label>
						</div>
						<div class='row'>
							!!date_date!!
						</div>
					</div>
					<div class='colonne2'>
						<div class='row'>
							<label class='etiquette' for='f_bull_new_mention'>".$msg['bulletin_mention_periode']."</label>
						</div>
						<div class='row'>
							<input type='text' id='f_bull_new_mention' name='f_bull_new_mention' value=\"!!bull_date!!\" class='saisie-30em' />
						</div>
					</div>
				</div>
			</blockquote>
		</div>
	</div>
";

$bt_undo = "&nbsp;<input type='button' class='bouton' value=\"!!value!!\" onclick=\"!!action!!\" />";

$zone_categ_form = "
	<input type=\"radio\" onclick=\"document.getElementById('div_categorisation_manual').style.display='none'; document.getElementById('div_categorisation_auto').style.display=''\" id=\"categorisation_auto\" name=\"categorisation_type\" value=\"categorisation_auto\" /><label for=\"categorisation_auto\">".$msg['notice_integre_categorisation_auto']."</label>
	<div id=\"div_categorisation_auto\">
		<blockquote>
		!!message_rameau!!
		!!traitement_rameau!!
		</blockquote>
	</div>
	<br clear=\"all\" /><br />

	<input type=\"radio\" onclick=\"document.getElementById('div_categorisation_manual').style.display=''; document.getElementById('div_categorisation_auto').style.display='none'; if (!document.getElementById('f_categ0').getAttribute('completion')) {document.getElementById('f_categ0').setAttribute('completion','categories_mul'); ajax_pack_element(document.getElementById('f_categ0'));}\" id=\"categorisation_manual\" name=\"categorisation_type\" value=\"categorisation_manual\" /><label for=\"categorisation_manual\">".$msg['notice_integre_categorisation_manual']."</label>
	<div id=\"div_categorisation_manual\" class='child'>
		!!manual_categorisation!!
		<br />
		<input type='hidden' name='max_categ' value=\"1\" />
		<script type='text/javascript' src='./javascript/categ_drop.js'></script>
		<input type='hidden' name='tab_categ_order' id='tab_categ_order' value='0' />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=categorie&caller=notice&autoindex_class=autoindex_record&htmlfieldstype=html_ext&indexation_lang=!!indexation_lang_sel!!&p1=f_categ_id&p2=f_categ&dyn=1&parent=0', 'selector_category')\" />
		<input type='button' class='bouton' value='+' onClick=\"add_categ();\"/>
	   	<div id='el6Child_0'>
		  	<div id='drag_0'  class='row' dragtype='categ' draggable='yes' recept='yes' recepttype='categ' handler='handle_0'
				dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='' downlight=\"categ_downlight\" highlight=\"categ_highlight\"
				order='0' style='' >
		 		<span id=\"handle_0\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>
		        <input type='text' class='saisie-80emr' id='f_categ0' name='f_categ0' value=\"\" completion=\"categories_mul\" autfield=\"f_categ_id0\" />
		        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.f_categ0.value=''; this.form.f_categ_id0.value='0'; \" />
		        <input type='hidden' name='f_categ_id0' id='f_categ_id0' value='0' />
			</div>
			<div id='addcateg'></div>
		</div>
	</div>
	<br clear=\"all\" /><br />
    <script type='text/javascript'>
        var categ_auto = " . ($deflt_notice_catalog_categories_auto ? 1 : 0) . "
        setTimeout(() => {
           if(0 == categ_auto) {
            document.getElementById('categorisation_manual').click()
            } else {
                document.getElementById('categorisation_auto').click()
            }
        }, 2500);
    </script>
";
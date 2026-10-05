<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: catal_form.tpl.php,v 1.205.2.1.2.2 2025/03/12 15:39:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $value_deflt_fonction;
global $pmb_authors_qualification;
global $pmb_use_uniform_title;
global $deflt_notice_replace_links;
global $ptab, $msg, $charset, $current_module, $notice_tab_uniform_title_form_tpl, $pmb_authors_qualification, $notice_tab_isbn_form_tpl, $notice_tab_notes_form_tpl, $notice_tab_indexation_form_tpl, $notice_indexation_first_form_tpl, $notice_indexation_next_form_tpl, $notice_tab_lang_form_tpl, $notice_lang_first_form_tpl, $notice_lang_next_form_tpl, $notice_langorg_first_form_tpl, $notice_langorg_next_form_tpl, $notice_tab_links_form_tpl, $notice_tab_map_form_tpl, $notice_tab_customs_perso_form_tpl, $notice_tab_gestion_fields_form_tpl, $notice_content_form, $notice_replace,  $notice_replace_categories, $notice_replace_category;

// template pour le form de catalogage

// nombre de parties du form
$nb_onglets = 9;

//    ----------------------------------------------------
//       $ptab[0] : contenu de l'onglet 0 (zone de titre)
$ptab[0] = "
<!-- onglet 0 -->
<div id='el0Parent' class='parent' >
	<h3>
	    <img src='".get_url_icon('minus.gif')."' class='img_plus align_bottom' name='imEx' id='el0Img' title='$msg[236]' border='0' onClick=\"expandBase('el0', true); return false;\" />
	    $msg[712]
    </h3>
</div>

<div id='el0Child' class='child' etirable='yes' title='".htmlentities($msg[236],ENT_QUOTES, $charset)."' >
    <div id='el0Child_0' title='".htmlentities($msg[237],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Titre    -->
		<div id='el0Child_0a' class='row'>
	        <label for='f_tit1' title='".$msg['is_required']."' class='etiquette'>$msg[237] <sup>*</sup></label>
        </div>
	    <div id='el0Child_0b' class='row'>
	        <input type='text' class='saisie-80em required' id='f_tit1' name='f_tit1' data-form-name='f_tit1' data-pmb-deb-rech='1' value=\"!!tit1!!\"/>
        </div>
	</div>
    !!element_tit2!!
    !!element_tit3!!
    !!element_tit4!!
    <div id='el0Child_4' title='".htmlentities($msg[241],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Partie de    -->
	    <div class='row'>
	        <div id='el0Child_4a' class='colonne2'>
	            <label for='f_tparent' class='etiquette'>$msg[241]</label>
	            <div class='row'>
			        <input type='text' class='saisie-30emr' id='f_tparent' name='f_tparent' data-form-name='f_tparent' value=\"!!tparent!!\" completion=\"serie\" autfield=\"f_tparent_id\" />
	                <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=serie&caller=notice&param1=f_tparent_id&param2=f_tparent&deb_rech='+".pmb_escape()."(this.form.f_tparent.value), '!!force_dialog_serie!!', '!!force_popup_serie!!')\" />
	                <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_tparent.value=''; this.form.f_tparent_id.value='0'; \" />
	                <input type='hidden' name='f_tparent_id' id='f_tparent_id' data-form-name='f_tparent_id' value=\"!!tparent_id!!\" />
                </div>
            </div>
	    	<!--    No. de partie    -->
	        <div id='el0Child_5a' class='colonne_suite'>
	            <label for='f_tnvol' class='etiquette'>$msg[242]</label>
	            <div class='row'>
	                <input type='text' class='saisie-10em' id='f_tnvol' name='f_tnvol' data-form-name='f_tnvol' maxlength='100' value=\"!!tnvol!!\" />
                </div>
            </div>
			<div class='row'></div>
        </div>
	</div>
</div>
";

//    ----------------------------------------------------
//     Titres uniformes
//       $notice_tab_uniform_title_form_tpl : contenu de l'onglet 230 (Titres uniformes)
//    ----------------------------------------------------
if ($pmb_use_uniform_title) {
	$notice_tab_uniform_title_form_tpl = "
	<!-- onglet 230 -->
	<div id='el230Parent' class='parent'>
		<h3>
		    <img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='el230Img' title='titres_uniformes' border='0' onClick=\"expandBase('el230', true); return false;\" />
		    ".$msg["catal_onglet_titre_uniforme"]."
		</h3>
	</div>
	<div id='el230Child' class='child' etirable='yes' title='".htmlentities($msg["aut_menu_titre_uniforme"],ENT_QUOTES, $charset)."'>
		<div id='el230Child_0' title='".htmlentities($msg["aut_menu_titre_uniforme"],ENT_QUOTES, $charset)."' movable='yes'>
			<!--    Titres uniformes    -->
			!!titres_uniformes!!
		</div>
	</div>
	";
} else $notice_tab_uniform_title_form_tpl = "";

//    ----------------------------------------------------
//    Mention de responsabilité
//       $notice_tab_responsabilities_form_tpl : contenu de l'onglet 1 (mention de responsabilité)
//    ----------------------------------------------------
$aut_fonctions= marc_list_collection::get_instance('function');
if($pmb_authors_qualification){
	$authors_qualification_tpl="
	        <!--    Vedettes    -->
	        <div  id='el1Child_2a_vedettes' style='float:left;'>
	            <label for='f_aut0' class='etiquette'>".$msg['notice_vedette_composee_author']."</label>
				<div class='row'>
					<img class='img_plus' onclick=\"expand_vedette(this,'vedette0'); return false;\" title='détail' name='imEx' src='".get_url_icon('plus.gif')."'>
					<input type='text' class='saisie-30emr'  readonly='readonly'  name='notice_role_composed_0_vedette_composee_apercu_autre' id='notice_role_composed_0_vedette_composee_apercu_autre'  data-form-name='vedette_composee' value=\"!!vedette_apercu!!\" />
					<input type='button' class='bouton' value='$msg[raz]' onclick=\"del_vedette('role',!!iaut!!);\" />
				</div>
			</div>
			<div class='row' id='vedette0' style='margin-bottom:6px;display:none'>
				!!vedette_author!!
			</div>
			<script type='text/javascript'>
				vedette_composee_update_all('notice_role_composed_0_vedette_composee_subdivisions');
			</script>
	";
}else{
	$authors_qualification_tpl="";
}
global $notice_tab_responsabilities_form_tpl;
$notice_tab_responsabilities_form_tpl = "
<script>
    function fonction_selecteur_auteur() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUpSelector('./select.php?what=auteur&caller=notice&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), '!!force_dialog_author!!', '!!force_popup_author!!');
    }
    function fonction_selecteur_auteur_change(field) {
    	// id champ text = 'f_aut'+n+suffixe
    	// id champ hidden = 'f_aut'+n+'_id'+suffixe;
    	// select.php?what=auteur&caller=notice&param1=f_aut0_id&param2=f_aut0&deb_rech='+t
        name=field.getAttribute('id');
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        openPopUpSelector('./select.php?what=auteur&caller=notice&param1='+name_id+'&param2='+name+'&dyn=1&deb_rech='+".pmb_escape()."(document.getElementById(name).value), '!!force_dialog_author!!', '!!force_popup_author!!');
    }
    function fonction_raz_auteur() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_id'+name.substr(6);
        document.getElementById(name_id).value=0;
        document.getElementById(name).value='';
    }
    function fonction_selecteur_fonction() {
        name=this.getAttribute('id').substring(4);
        name_code = name.substr(0,4)+'_code'+name.substr(4);
        openPopUpSelector('./select.php?what=function&caller=notice&param1='+name_code+'&param2='+name+'&dyn=1', '!!force_dialog_func!!', '!!force_popup_func!!');
    }
    function fonction_raz_fonction() {
        name=this.getAttribute('id').substring(4);
        name_code = name.substr(0,4)+'_code'+name.substr(4);
        document.getElementById(name_code).value=0;
        document.getElementById(name).value='';
    }
    function add_aut(n) {
        template = document.getElementById('addaut'+n);
        aut=document.createElement('div');
        aut.className='row';

        // auteur
        colonne=document.createElement('div');
        //colonne.className='colonne3';
        colonne.style.cssFloat = 'left';
        colonne.style.marginRight = '10px';
        row=document.createElement('div');
        row.className='row';
        suffixe = eval('document.notice.max_aut'+n+'.value');
        nom_id = 'f_aut'+n+suffixe;
		var buttonAdd = document.getElementById('button_add_f_aut' + n);

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
        sel_f_aut0.setAttribute('value','$msg[parcourir]');
        sel_f_aut0.onclick=fonction_selecteur_auteur;

        del_f_aut0 = document.createElement('input');
        del_f_aut0.setAttribute('id','del_f_aut'+n+suffixe);
        del_f_aut0.onclick=fonction_raz_auteur;
        del_f_aut0.setAttribute('type','button');
        del_f_aut0.className='bouton';
        del_f_aut0.setAttribute('readonly','');
        del_f_aut0.setAttribute('value','$msg[raz]');

        f_aut0_id = document.createElement('input');
        f_aut0_id.name='f_aut'+n+'_id'+suffixe;
        f_aut0_id.setAttribute('type','hidden');
        f_aut0_id.setAttribute('id','f_aut'+n+'_id'+suffixe);
        f_aut0_id.setAttribute('value','');

        //f_aut0_content.appendChild(f_aut0);
		row.appendChild(f_aut0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(sel_f_aut0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(del_f_aut0);
        row.appendChild(f_aut0_id);
        colonne.appendChild(row);
        aut.appendChild(colonne);

        // fonction

        colonne=document.createElement('div');
        //colonne.className='colonne3';
        colonne.style.cssFloat = 'left';
        colonne.style.marginRight = '10px';
        row=document.createElement('div');
        row.className='row';
        suffixe = eval('document.notice.max_aut'+n+'.value');
        nom_id = 'f_f'+n+suffixe;
        f_f0 = document.createElement('input');
        f_f0.setAttribute('name',nom_id);
        f_f0.setAttribute('id',nom_id);
        f_f0.setAttribute('type','text');
        f_f0.className='saisie-15emr';
        f_f0.setAttribute('value','".(!empty($value_deflt_fonction) ? $aut_fonctions->table[$value_deflt_fonction] : '')."');
		f_f0.setAttribute('completion','fonction');
        f_f0.setAttribute('autfield','f_f'+n+'_code'+suffixe);

        sel_f_f0 = document.createElement('input');
        sel_f_f0.setAttribute('id','sel_f_f'+n+suffixe);
        sel_f_f0.setAttribute('type','button');
        sel_f_f0.className='bouton';
        sel_f_f0.setAttribute('readonly','');
        sel_f_f0.setAttribute('value','$msg[parcourir]');
        sel_f_f0.onclick=fonction_selecteur_fonction;

        del_f_f0 = document.createElement('input');
        del_f_f0.setAttribute('id','del_f_f'+n+suffixe);
        del_f_f0.onclick=fonction_raz_fonction;
        del_f_f0.setAttribute('type','button');
        del_f_f0.className='bouton';
        del_f_f0.setAttribute('readonly','readonly');
        del_f_f0.setAttribute('value','$msg[raz]');

        f_f0_code = document.createElement('input');
        f_f0_code.name='f_f'+n+'_code'+suffixe;
        f_f0_code.setAttribute('type','hidden');
        f_f0_code.setAttribute('id','f_f'+n+'_code'+suffixe);
        f_f0_code.setAttribute('value','".(isset($value_deflt_fonction) ? $value_deflt_fonction : '')."');

		var duplicate = document.createElement('input');
		duplicate.setAttribute('onclick','duplicate('+n+','+suffixe+')');
		duplicate.setAttribute('type','button');
		duplicate.className='bouton';
		duplicate.setAttribute('readonly','readonly');
		duplicate.setAttribute('value','".$msg["duplicate"]."');

		add_aut_node = document.getElementById('button_add_f_aut'+n);
        add_aut_node_clone = add_aut_node.cloneNode();
        parent = add_aut_node.parentNode;
        parent.removeChild(add_aut_node);

        row.appendChild(f_f0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(sel_f_f0);
        space=document.createTextNode(' ');
        row.appendChild(space);
        row.appendChild(del_f_f0);
        row.appendChild(f_f0_code);
		if(!('$pmb_authors_qualification'*1)){
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(duplicate);
			row.appendChild(add_aut_node_clone);
		}
        colonne.appendChild(row);
        aut.appendChild(colonne);

		if('$pmb_authors_qualification'*1){
	        var role_field='role';
	        if(n==1) role_field='role_autre';
	        if(n==2) role_field='role_secondaire';

			var req = new http_request();
			if(req.request('./ajax.php?module=catalog&categ=get_notice_form_vedette&role_field='+role_field+'&index='+suffixe,1)){
				// Il y a une erreur
				alert ( req.get_text() );
			}else {
			 	vedette_form=req.get_text();
			 	var row_vedette=document.createElement('div');
				row_vedette.className='row';
				row_vedette.innerHTML=vedette_form;
			}
			row_vedette.setAttribute('id','vedette'+suffixe+'_'+role_field);
			row_vedette.style.display='none';

			colonne=document.createElement('div');
			//colonne.className='colonne3';
        	colonne.style.cssFloat = 'left';
			row=document.createElement('div');
			row.className='row';

			var img_plus = document.createElement('img');
			img_plus.name='img_plus'+suffixe;
			img_plus.setAttribute('id','img_plus'+suffixe+'_'+role_field);
			img_plus.className='img_plus';
			img_plus.setAttribute('src','".get_url_icon('plus.gif')."');
			img_plus.setAttribute('onclick','expand_vedette(this, \"vedette'+suffixe+'_'+role_field+'\")');

			var nom_id = 'notice_'+role_field+'_composed_'+suffixe+'_vedette_composee_apercu_autre';
			apercu = document.createElement('input');
			apercu.setAttribute('name',nom_id);
			apercu.setAttribute('id',nom_id);
			apercu.setAttribute('type','text');
			apercu.className='saisie-30emr';
			apercu.setAttribute('readonly','readonly');

			var del_vedette = document.createElement('input');
			del_vedette.setAttribute('onclick','del_vedette(\"'+role_field+'\",'+suffixe+')');
			del_vedette.setAttribute('type','button');
			del_vedette.className='bouton';
			del_vedette.setAttribute('readonly','readonly');
			del_vedette.setAttribute('value','$msg[raz]');

			row.appendChild(img_plus);
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(apercu);
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(del_vedette);
			space=document.createTextNode(' ');
			row.appendChild(space);
			row.appendChild(duplicate);
			if (buttonAdd) row.appendChild(buttonAdd);
			colonne.appendChild(row);
			aut.appendChild(colonne);

			template.appendChild(aut);
			template.appendChild(row_vedette);
			eval(document.getElementById('vedette_script_'+role_field+'_composed_'+suffixe).innerHTML);
		}else{
			template.appendChild(aut);
		}
        eval('document.notice.max_aut'+n+'.value=suffixe*1+1*1');
        ajax_pack_element(f_aut0);
		ajax_pack_element(f_f0);
		init_drag();
    }

	function duplicate(n, suffixe) {
		add_aut(n);
        new_suffixe = eval('document.notice.max_aut'+n+'.value')-1;
        document.getElementById('f_aut'+n+new_suffixe).value = document.getElementById('f_aut'+n+suffixe).value;
        document.getElementById('f_aut'+n+'_id'+new_suffixe).value = document.getElementById('f_aut'+n+'_id'+suffixe).value;

        document.getElementById('f_f'+n+new_suffixe).value = document.getElementById('f_f'+n+suffixe).value;
        document.getElementById('f_f'+n+'_code'+new_suffixe).value = document.getElementById('f_f'+n+'_code'+suffixe).value;
	}

    function fonction_selecteur_categ() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,7)+'_id'+name.substr(7);
        openPopUpSelectorCategory('./select.php?what=categorie&caller=notice&p1='+name_id+'&p2='+name+'&dyn=1', '!!force_dialog_category!!', '!!force_popup_category!!');
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
        del_f_categ.setAttribute('value','$msg[raz]');

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

        tab_categ_order = document.getElementById('tab_categ_order');
		if (tab_categ_order.value != '') tab_categ_order.value += ','+suffixe;

        add_categ_node = document.getElementById('add_categ_btn');
        add_categ_node_clone = add_categ_node.cloneNode();
        document.getElementById('drag_'+(suffixe-1)).removeChild(add_categ_node);
        categ.appendChild(add_categ_node_clone);

		document.notice.max_categ.value=suffixe*1+1*1 ;
        ajax_pack_element(f_categ);
        init_drag();
    }
    function fonction_selecteur_lang() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,6)+'_code'+name.substr(6);
        openPopUpSelector('./select.php?what=lang&caller=notice&p1='+name_id+'&p2='+name, '!!force_dialog_lang!!', '!!force_popup_lang!!');
    }
    function add_lang() {
    	templates.add_completion_selection_field('f_lang', 'f_lang_code', 'langue', fonction_selecteur_lang);
    }

    function fonction_selecteur_langorg() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,9)+'_code'+name.substr(9);
        openPopUpSelector('./select.php?what=lang&caller=notice&p1='+name_id+'&p2='+name, '!!force_dialog_lang!!', '!!force_popup_lang!!');
    }
    function add_langorg() {
    	templates.add_completion_selection_field('f_langorg', 'f_langorg_code', 'langue', fonction_selecteur_langorg);
    }

	function expand_vedette(el,what) {
		var obj=document.getElementById(what);
		if(obj.style.display=='none'){
			obj.style.display='block';
	    	el.src = '".get_url_icon('minus.gif')."';
			init_drag();
		}else{
			obj.style.display='none';
	    	el.src =  '".get_url_icon('plus.gif')."';
		}
	}

	function del_vedette(role,index) {
		vedette_composee_delete_all('notice_'+role+'_composed_'+index+'_vedette_composee_subdivisions');
		init_drag();
	}

</script>
<div id='el1Parent' class='parent'>
    <h3>
        ".get_expandBase_button('el1', '243')."
    	$msg[243]
    </h3>
</div>
<div id='el1Child' class='child' etirable='yes' title='".htmlentities($msg[243],ENT_QUOTES, $charset)."'>
    <div id='el1Child_0' title='".htmlentities($msg[244],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Auteur principal    -->
	    <div class='row'>
	        <div id='el1Child_0a' style='float:left;margin-right:10px;'>
	            <label for='f_aut0' class='etiquette'>$msg[244]</label>
	            <div class='row' >
					<input type='text' completion='authors' autfield='f_aut0_id' id='auteur0' class='saisie-30emr' name='f_aut0' data-form-name='f_aut0' value=\"!!aut0!!\" />
	                <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=auteur&caller=notice&param1=f_aut0_id&param2=f_aut0&deb_rech='+".pmb_escape()."(this.form.f_aut0.value), '!!force_dialog_author!!', '!!force_popup_author!!')\" />
	              	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_aut0.value=''; this.form.f_aut0_id.value='0'; \" />
	               	<input type='hidden' name='f_aut0_id' data-form-name='f_aut0_id' id='f_aut0_id' value=\"!!aut0_id!!\" />
	            </div>
			</div>
	        <!--    Fonction    -->
	        <div id='el1Child_1a' style='float:left;margin-right:10px;'>
	            <label for='f_f0' class='etiquette'>$msg[245]</label>
	            <div class='row'>
			        <input type='text' class='saisie-15emr' id='f_f0' name='f_f0' data-form-name='f_f0' value=\"!!f0!!\" completion=\"fonction\" autfield=\"f_f0_code\" />
	                <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=function&caller=notice&p1=f_f0_code&p2=f_f0', '!!force_dialog_func!!', '!!force_popup_func!!')\" />
	                <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_f0.value=''; this.form.f_f0_code.value='0'; \" />
	                <input type='hidden' name='f_f0_code' data-form-name='f_f0_code' id='f_f0_code' value=\"!!f0_code!!\" />
                </div>
            </div>
            $authors_qualification_tpl
		</div>
		<div class='row'></div>
	</div>
    <div id='el1Child_2' title='".htmlentities($msg[246],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Autres auteurs    -->
	    <div id='el1Child_2a' class='row'>
	    	<div class='row'>
		        <label for='f_aut1' class='etiquette'>$msg[246]</label>
		        <input type='hidden' name='max_aut1' value=\"!!max_aut1!!\" />
				<input type='button' class='bouton' value='+' onClick=\"add_aut(1);\"/>
	        </div>
	        <div class='row' id='addaut1'>
		        !!autres_auteurs!!
			</div>
		</div>
		<div class='row'></div>
	</div>
    <div id='el1Child_3' title='".htmlentities($msg[247],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Auteurs secondaires     -->
	    <div  id='el1Child_3a' class='row'>
	    	<div class='row'>
		        <label for='f_aut2' class='etiquette'>$msg[247]</label>
		        <input type='hidden' name='max_aut2' value=\"!!max_aut2!!\" />
				<input type='button' class='bouton' value='+' onClick=\"add_aut(2);\"/>
	        </div>
	        <div class='row' id='addaut2'>
	        	!!auteurs_secondaires!!
			</div>
		</div>
		<div class='row'></div>
	</div>
</div>
";

//    ----------------------------------------------------
//    Autres auteurs
//    ----------------------------------------------------
if($pmb_authors_qualification){
	$authors_add_aut_button_tpl="";
	$authors_qualification_tpl="
		<!--    Vedettes    -->
        <div id='el1Child_2b_others_vedettes' style='float:left;'>
			<img class='img_plus' onclick=\"expand_vedette(this,'vedette!!iaut!!_autre'); return false;\" title='détail' name='imEx' src='".get_url_icon('plus.gif')."'>
			<input type='text' class='saisie-30emr'  readonly='readonly'  name='notice_role_autre_composed_!!iaut!!_vedette_composee_apercu_autre' id='notice_role_autre_composed_!!iaut!!_vedette_composee_apercu_autre'  data-form-name='vedette_composee_autre' value=\"!!vedette_apercu!!\" />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"del_vedette('role_autre',!!iaut!!);\" />
			<input class='bouton' type='button' onclick='duplicate(1,!!iaut!!);' value='".$msg['duplicate']."'>
			!!button_add_aut1!!
		</div>
		<div class='row' id='vedette!!iaut!!_autre' style='margin-bottom:6px;display:none'>
			!!vedette_author!!
		</div>
		<script type='text/javascript'>
			vedette_composee_update_all('notice_role_autre_composed_!!iaut!!_vedette_composee_subdivisions');
		</script>
	";
}else{
	$authors_add_aut_button_tpl="
		<input class='bouton' type='button' onclick='duplicate(1,!!iaut!!);' value='".$msg['duplicate']."'>";
	$authors_qualification_tpl="";
}
global $notice_responsabilities_others_form_tpl;
$notice_responsabilities_others_form_tpl = "
	<div class='row'>
        <div id='el1Child_2b_first' style='float:left;margin-right:10px;'>
       		<input type='text' class='saisie-30emr' completion='authors' autfield='f_aut1_id!!iaut!!' id='f_aut1!!iaut!!' name='f_aut1!!iaut!!' data-form-name='f_aut1' value=\"!!aut1!!\" />
			<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=auteur&caller=notice&param1=f_aut1_id!!iaut!!&param2=f_aut1!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut1!!iaut!!.value), '!!force_dialog_author!!', '!!force_popup_author!!')\" />
            <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_aut1!!iaut!!.value=''; this.form.f_aut1_id!!iaut!!.value='0'; \" />
            <input type='hidden' name='f_aut1_id!!iaut!!' data-form-name='f_aut1_id' id='f_aut1_id!!iaut!!' value=\"!!aut1_id!!\" />
        </div>
    	<!--    Fonction    -->
        <div id='el1Child_2b_others' style='float:left;margin-right:10px;'>
            <input type='text' class='saisie-15emr' id='f_f1!!iaut!!' name='f_f1!!iaut!!' data-form-name='f_f1' completion='fonction' autfield='f_f1_code!!iaut!!' value=\"!!f1!!\" />
            <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=function&caller=notice&p1=f_f1_code!!iaut!!&p2=f_f1!!iaut!!', '!!force_dialog_func!!', '!!force_popup_func!!')\" />
            <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_f1!!iaut!!.value=''; this.form.f_f1_code!!iaut!!.value='0'; \" />
            $authors_add_aut_button_tpl
            !!button_add_aut1!!
            <input type='hidden' name='f_f1_code!!iaut!!' data-form-name='f_f1_code' id='f_f1_code!!iaut!!' value=\"!!f1_code!!\" />
        </div>
		$authors_qualification_tpl
	</div>
    " ;

//    ----------------------------------------------------
//    Autres secondaires
//    ----------------------------------------------------
if (!empty($pmb_authors_qualification)) {
	$authors_add_aut_button_tpl = "";
	$authors_qualification_tpl = "
        <!--    Vedettes    -->
        <div id='el1Child_3b_others_vedettes' style='float:left;'>
			<img class='img_plus' onclick=\"expand_vedette(this,'vedette!!iaut!!_secondaire'); return false;\" title='détail' name='imEx' src='".get_url_icon('plus.gif')."'>
			<input type='text' class='saisie-30emr'  readonly='readonly'  name='notice_role_secondaire_composed_!!iaut!!_vedette_composee_apercu_autre' id='notice_role_secondaire_composed_!!iaut!!_vedette_composee_apercu_autre'  data-form-name='vedette_composee' value=\"!!vedette_apercu!!\" />
			<input type='button' class='bouton' value='$msg[raz]' onclick=\"del_vedette('role_secondaire',!!iaut!!);\" />
			<input class='bouton' type='button' onclick='duplicate(2,!!iaut!!);' value='".$msg['duplicate']."'>
			!!button_add_aut2!!
		</div>
		<div class='row' id='vedette!!iaut!!_secondaire' style='margin-bottom:6px;display:none'>
			!!vedette_author!!
		</div>
		<script type='text/javascript'>
			vedette_composee_update_all('notice_role_secondaire_composed_!!iaut!!_vedette_composee_subdivisions');
		</script>
	";
} else {
	$authors_add_aut_button_tpl = "
		<input class='bouton' type='button' onclick='duplicate(2,!!iaut!!);' value='".$msg['duplicate']."'>
		!!button_add_aut2!!";
	$authors_qualification_tpl = "";
}
global $notice_responsabilities_secondary_form_tpl;
$notice_responsabilities_secondary_form_tpl = "
	<div class='row'>
        <div id='el1Child_3b_first' style='float:left;margin-right:10px;'>
            <input type='text' class='saisie-30emr' completion='authors' autfield='f_aut2_id!!iaut!!' id='f_aut2!!iaut!!' name='f_aut2!!iaut!!' data-form-name='f_aut2' value=\"!!aut2!!\" />
			<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=auteur&caller=notice&param1=f_aut2_id!!iaut!!&param2=f_aut2!!iaut!!&deb_rech='+".pmb_escape()."(this.form.f_aut2!!iaut!!.value), '!!force_dialog_author!!', '!!force_popup_author!!')\" />
            <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_aut2!!iaut!!.value=''; this.form.f_aut2_id!!iaut!!.value='0'; \" />
            <input type='hidden' name='f_aut2_id!!iaut!!' data-form-name='f_aut2_id' id='f_aut2_id!!iaut!!' value=\"!!aut2_id!!\" />
        </div>
        <!--    Fonction    -->
        <div id='el1Child_3b_others' style='float:left;margin-right:10px;'>
            <input type='text' class='saisie-15emr' id='f_f2!!iaut!!' name='f_f2!!iaut!!' data-form-name='f_f2' completion='fonction' autfield='f_f2_code!!iaut!!' value=\"!!f2!!\" />
            <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=function&caller=notice&p1=f_f2_code!!iaut!!&p2=f_f2!!iaut!!', '!!force_dialog_func!!', '!!force_popup_func!!')\" />
            <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_f2!!iaut!!.value=''; this.form.f_f2_code!!iaut!!.value='0'; \" />
            $authors_add_aut_button_tpl
            <input type='hidden' name='f_f2_code!!iaut!!' data-form-name='f_f2_code' id='f_f2_code!!iaut!!' value=\"!!f2_code!!\" />
        </div>
        $authors_qualification_tpl
	</div>
    " ;

//    ----------------------------------------------------
//    Adresse, éditeurs, collection
//    ----------------------------------------------------
$ptab[2] = "
<!-- onglet 2 -->
<div id='el2Parent' class='parent'>
    <h3>
        ".get_expandBase_button('el2')."
	    $msg[249]
    </h3>
</div>
<div id='el2Child' class='child' etirable='yes' title='".htmlentities($msg[249],ENT_QUOTES, $charset)."'>
	<div id='el2Child_0' title='".htmlentities($msg[164],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Editeur    -->
		<div id='el2Child_0a' class='row'>
		    <label for='f_ed1' class='etiquette'>$msg[164]</label>
		</div>
		<div id='el2Child_0b' class='row'>
			<script type='text/javascript'>
				function f_ed1_id_callback() {
				}
			</script>
			<input type='text' completion='publishers' autfield='f_ed1_id' id='f_ed1' name='f_ed1' data-form-name='f_ed1' value=\"!!ed1!!\" class='saisie-30emr' callback='f_ed1_id_callback' />
		    <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=editeur&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll&deb_rech='+".pmb_escape()."(this.form.f_ed1.value), '!!force_dialog_publisher!!', '!!force_popup_publisher!!')\" />
		    <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_ed1.value=''; this.form.f_ed1_id.value='0'; \" />
		    <input type='hidden' name='f_ed1_id' data-form-name='f_ed1_id' id='f_ed1_id' value=\"!!ed1_id!!\" />
		</div>
	</div>
	<div id='el2Child_1' title='".htmlentities($msg[250],ENT_QUOTES, $charset)."' movable='yes'>
		<div class='row'>
		    <!--    Collection    -->
		    <div id='el2Child_1a' class='colonne2'>
		    <label for='f_coll' class='etiquette'>$msg[250]</label>
		    <div class='row'>
		    	<script type='text/javascript'>
					function f_coll_id_callback() {
						ajax_get_entity('get_publisher', 'collection', document.getElementById('f_coll_id').value, 'f_ed1_id', 'f_ed1');
					}
				</script>
				<input type='text' completion='collections' autfield='f_coll_id' id='f_coll' name='f_coll' data-form-name='f_coll' value=\"!!coll!!\" class='saisie-30emr' linkfield='f_ed1_id' callback='f_coll_id_callback'/>
		        <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=collection&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll&deb_rech='+".pmb_escape()."(this.form.f_coll.value), '!!force_dialog_collection!!', '!!force_popup_collection!!')\" />
		        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_coll.value=''; this.form.f_coll_id.value='0'; \" />
		        <input type='hidden' name='f_coll_id' data-form-name='f_coll_id' id='f_coll_id' value=\"!!coll_id!!\" />
		        </div>
	        </div>
		    <!--    No. dans la collection    -->
		    <div id='el2Child_2a' class='colonne_suite'>
		        <label for='f_nocoll' class='etiquette'>$msg[253]</label>
		        <div class='row'>
		            <input type='text' class='saisie-15em' id='f_nocoll' name='f_nocoll' data-form-name='f_nocoll' value=\"!!nocoll!!\" />
	            </div>
	        </div>
		</div>
	</div>
	<div id='el2Child_3' title='".htmlentities($msg[251],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='el2Child_3a' class='row'>
		    <!--    Sous collection    -->
		        <label for='f_subcoll' class='etiquette'>$msg[251]</label>
		        <div class='row'>
		        	<script type='text/javascript'>
						function f_subcoll_id_callback() {
							ajax_get_entity('get_publisher', 'sub_collection', document.getElementById('f_subcoll_id').value, 'f_ed1_id', 'f_ed1');
							ajax_get_entity('get_collection', 'sub_collection', document.getElementById('f_subcoll_id').value, 'f_coll_id', 'f_coll');
						}
					</script>
					<input type='text' completion='subcollections' autfield='f_subcoll_id' id='f_subcoll' name='f_subcoll' data-form-name='f_subcoll' value=\"!!subcoll!!\" class='saisie-30emr' linkfield='f_coll_id' callback='f_subcoll_id_callback'/>
			
					<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=subcollection&caller=notice&p1=f_ed1_id&p2=f_ed1&p3=f_coll_id&p4=f_coll&p5=f_subcoll_id&p6=f_subcoll&deb_rech='+".pmb_escape()."(this.form.f_subcoll.value), '!!force_dialog_subcollection!!', '!!force_popup_subcollection!!')\" />
					<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_subcoll.value=''; this.form.f_subcoll_id.value='0'; \" />
					<input type='hidden' id='f_subcoll_id' name='f_subcoll_id' data-form-name='f_subcoll_id' value=\"!!subcoll_id!!\" />
				</div>
		    </div>
		</div>
		<div id='el2Child_4' title='".htmlentities($msg[252],ENT_QUOTES, $charset)."' movable='yes'>
			<div id='el2Child_4a' class='row'>&nbsp;</div>
			<div class='row'>
			    <!--    Année    -->
			    <div id='el2Child_5a' class='colonne2'>
			        <label for='f_year' class='etiquette'>$msg[252]</label>
			        <div class='row'>
			            <input type='text' class='saisie-30em' id='f_year' name='f_year' data-form-name='f_year' value=\"!!year!!\" />
		            </div>
		        </div>
		
		    <div id='el2Child_6a' class='colonne_suite'>
		        <label for='f_mention_edition' class='etiquette'>$msg[mention_edition]</label>
		        <div class='row'>
		            <input type='text' class='saisie-20em' id='f_mention_edition' name='f_mention_edition' data-form-name='f_mention_edition' value=\"!!mention_edition!!\" />
	            </div>
	        </div>
	    </div>
	</div>
	<div id='el2Child_7' title='".htmlentities($msg[254],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Autre éditeur    -->
		<div id='el2Child_7a' class='row'>
		    <label for='f_ed2' class='etiquette'>$msg[254]</label>
		</div>
		<div id='el2Child_7b' class='row'>
		    <input type='text' completion='publishers' autfield='f_ed2_id' id='f_ed2' name='f_ed2' data-form-name='f_ed2' value=\"!!ed2!!\" class='saisie-30emr' />
		    <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=editeur&caller=notice&p1=f_ed2_id&p2=f_ed2&p3=dummy&p4=dummy&p5=dummy&p6=dummy&deb_rech='+".pmb_escape()."(this.form.f_ed2.value), '!!force_dialog_publisher!!', '!!force_popup_publisher!!')\" />
		    <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_ed2.value=''; this.form.f_ed2_id.value='0'; \" />
		    <input type='hidden' name='dummy' />
		    <input type='hidden' name='f_ed2_id' id='f_ed2_id' data-form-name='f_ed2_id' value=\"!!ed2_id!!\" />
		</div>
	</div>
</div>
";

//    ----------------------------------------------------
//    ISBN, EAN ou no. commercial
//       $notice_tab_isbn_form_tpl : contenu de l'onglet 3
//    ----------------------------------------------------
$notice_tab_isbn_form_tpl = "
<!-- onglet 3 -->
<div id='el3Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el3', '255')."
	    $msg[255]
	</h3>
</div>
<div id='el3Child' class='child' etirable='yes' title='".htmlentities($msg[255],ENT_QUOTES, $charset)."'>
	<div id='el3Child_0' title='$msg[255]' movable='yes'>
		<!--    ISBN, EAN ou no. commercial    -->
		<div id='el3Child_0a' class='row'>
		    <label for='f_cb' class='etiquette'>$msg[255]</label>
		</div>
		<div id='el3Child_0b' class='row'>
		    <input class='saisie-20emr' id='f_cb' name='f_cb' data-form-name='f_cb' readonly value=\"!!cb!!\" />
		    <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUp('./catalog/setcb.php?notice_id=!!notice_id!!', 'getcb')\" />
		    <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_cb.value=''; \" />
		</div>
	</div>
</div>
";

//    ----------------------------------------------------
//    Collation
//       $ptab[4] : contenu de l'onglet 4 (collation)
//    ----------------------------------------------------

$ptab[4] = "
<!-- onglet 4 -->
<div id='el4Parent' class='parent'>
    <h3>
        ".get_expandBase_button('el4', '257')."
        $msg[258]
    </h3>
</div>
<div id='el4Child' class='child' etirable='yes' title='".htmlentities($msg[258],ENT_QUOTES, $charset)."'>
	!!element_npages!!
    !!element_ill!!
    !!element_size!!
    !!element_prix!!
    !!element_accomp!!
</div>
";

//    ----------------------------------------------------
//    Notes
//       $notice_tab_notes_form_tpl : contenu de l'onglet 5 (notes)
//    ----------------------------------------------------
$notice_tab_notes_form_tpl = "
<div id='el5Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el5', '263')."
	    $msg[264]
	</h3>
</div>
<div id='el5Child' class='child' etirable='yes' title='".htmlentities($msg[264],ENT_QUOTES, $charset)."'>
	!!element_n_gen!!
    !!element_n_contenu!!
    !!element_n_resume!!
</div>
";

//    ----------------------------------------------------
//    Indexation
//       $ptab[6] : contenu de l'onglet 6 (indexation)
//    ----------------------------------------------------
$notice_tab_indexation_form_tpl = "
    <!-- onglet 6 -->
<div id='el6Parent' class='parent'>
	<h3>
	    <img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='el6Img' title=\"$msg[268]\" onClick=\"expandBase('el6', true);recalc_recept(); return false;\" />
	    $msg[269]
	</h3>
</div>
<div id='el6Child' class='child' etirable='yes' title='".htmlentities($msg[269],ENT_QUOTES, $charset)."'>
	<div id='el6Child_0' title='".htmlentities($msg[134],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Catégories    -->
	    <div id='el6Child_0a' class='row'>
	        <label for='f_categ' class='etiquette'>".$msg['categories_catal_title']."</label>
	    </div>
	    <input type='hidden' name='max_categ' value=\"!!max_categ!!\" />
	    !!categories_repetables!!
	    <div id='addcateg'/>
        </div>
	</div>
	<div id='el6Child_1' title='".htmlentities($msg["indexint_catal_title"],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    indexation interne    -->
	    <div id='el6Child_1a' class='row'>
	        <label for='f_categ' class='etiquette'>$msg[indexint_catal_title]</label>
	    </div>
	    <div id='el6Child_1b' class='row'>
	        <input type='text' class='saisie-80emr' id='f_indexint' name='f_indexint' data-form-name='f_indexint' value=\"!!indexint!!\" completion=\"indexint\" autfield=\"f_indexint_id\"  typdoc=\"typdoc\" />
	        <input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=indexint&caller=notice&param1=f_indexint_id&param2=f_indexint&parent=0&deb_rech='+".pmb_escape()."(this.form.f_indexint.value)+'&typdoc='+(this.form.typdoc.value)+'&num_pclass=!!num_pclass!!', '!!force_dialog_indexint!!', '!!force_popup_indexint!!')\" />
	        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_indexint.value=''; this.form.f_indexint_id.value='0'; \" />
	        <input type='hidden' name='f_indexint_id' data-form-name='f_indexint_id' id='f_indexint_id' value='!!indexint_id!!' />
	    </div>
	</div>
	<div id='el6Child_2' title='".htmlentities($msg[324],ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Indexation libre    -->
	    <div id='el6Child_2a' class='row'>
	        <label for='f_indexation' class='etiquette'>$msg[324]</label>
	    </div>
	    <div id='el8Child_2b' class='row'>
	        <textarea class='saisie-80em' id='f_indexation' name='f_indexation' data-form-name='f_indexation' rows='3' wrap='virtual' completion='tags' keys='113'>!!f_indexation!!</textarea>
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
$notice_indexation_first_form_tpl = "
	<script type='text/javascript' src='./javascript/categ_drop.js'></script>
	<input type='hidden' name='tab_categ_order' id='tab_categ_order' value='!!tab_categ_order!!' />
	<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelectorCategory('./select.php?what=categorie&caller=notice&autoindex_class=autoindex_record&indexation_lang=!!indexation_lang_sel!!&p1=f_categ_id!!icateg!!&p2=f_categ!!icateg!!&dyn=1&parent=0&deb_rech=', '!!force_dialog_category!!', '!!force_popup_category!!')\" />
    <input type='button' class='bouton' value='+' onClick=\"add_categ();\"/>
  	<div id='drag_!!icateg!!'  class='row' dragtype='categ' draggable='yes' recept='yes' recepttype='categ' handler='handle_!!icateg!!'
		dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!categ_libelle!!' downlight=\"categ_downlight\" highlight=\"categ_highlight\"
		order='!!icateg!!' style='' >
 		<span id=\"handle_!!icateg!!\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>

        <input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' data-form-name='f_categ' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />
        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
       	<input type='hidden' name='f_categ_id!!icateg!!' data-form-name='f_categ_id' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
       	!!add_categ_btn!!
	</div>
    ";
$notice_indexation_next_form_tpl = "
 	<div id='drag_!!icateg!!' class='row' dragtype='categ' draggable='yes' recept='yes' recepttype='categ' handler='handle_!!icateg!!'
		dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!categ_libelle!!' downlight=\"categ_downlight\" highlight=\"categ_highlight\"
		order='!!icateg!!' style='' >
    	<span id=\"handle_!!icateg!!\" style=\"float:left; padding-right : 7px\"><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>

    	<input type='text' class='saisie-80emr' id='f_categ!!icateg!!' name='f_categ!!icateg!!' value=\"!!categ_libelle!!\" completion=\"categories_mul\" autfield=\"f_categ_id!!icateg!!\" />
        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_categ!!icateg!!.value=''; this.form.f_categ_id!!icateg!!.value='0'; \" />
        <input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
        !!add_categ_btn!!
	</div>
    ";

//    ----------------------------------------------------
//     Langue de la publication
//       $notice_langues_tab_form_tpl : contenu de l'onglet 7 (langues)
//    ----------------------------------------------------

$notice_tab_lang_form_tpl = "
<div id='el7Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el7', '537')."
	    $msg[710]
	</h3>
</div>
<div id='el7Child' class='child' etirable='yes' title='".htmlentities($msg[710], ENT_QUOTES, $charset)."'>
	<div id='el7Child_0' title='".htmlentities($msg[710], ENT_QUOTES, $charset)."' movable='yes'>
	    <!--    Langues    -->
	    <div id='el7Child_0a' class='row'>
	        <label for='f_langue' class='etiquette'>$msg[710]</label>
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
	        <label for='f_langorg' class='etiquette'>$msg[711]</label>
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
//    ----------------------------------------------------
$notice_lang_first_form_tpl = "
    <div id='el7Child_0a' class='row'>
        <input type='text' class='saisie-30emr' id='f_lang!!ilang!!' name='f_lang!!ilang!!' data-form-name='f_lang' value=\"!!lang!!\" completion=\"langue\" autfield=\"f_lang_code!!ilang!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=lang&caller=notice&p1=f_lang_code!!ilang!!&p2=f_lang!!ilang!!', '!!force_dialog_lang!!', '!!force_popup_lang!!')\" />
        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_lang!!ilang!!.value=''; this.form.f_lang_code!!ilang!!.value=''; \" />
        !!button_add_lang!!
        <input type='hidden' name='f_lang_code!!ilang!!' data-form-name='f_lang_code' id='f_lang_code!!ilang!!' value='!!lang_code!!' />
    </div>
    ";

$notice_lang_next_form_tpl = "
    <div id='el7Child_0a' class='row'>
        <input type='text' class='saisie-30emr' id='f_lang!!ilang!!' name='f_lang!!ilang!!' value=\"!!lang!!\" completion=\"langue\" autfield=\"f_lang_code!!ilang!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=lang&caller=notice&p1=f_lang_code!!ilang!!&p2=f_lang!!ilang!!', '!!force_dialog_lang!!', '!!force_popup_lang!!')\" />
        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_lang!!ilang!!.value=''; this.form.f_lang_code!!ilang!!.value=''; \" />
        !!button_add_lang!!
        <input type='hidden' name='f_lang_code!!ilang!!' id='f_lang_code!!ilang!!' value='!!lang_code!!' />
    </div>
    ";

//    ----------------------------------------------------
//     Langues originales répétables
//    ----------------------------------------------------
$notice_langorg_first_form_tpl = "
    <div id='el7Child_0b' class='row'>
        <input type='text' class='saisie-30emr' id='f_langorg!!ilangorg!!' name='f_langorg!!ilangorg!!' data-form-name='f_langorg' value=\"!!langorg!!\" completion=\"langue\" autfield=\"f_langorg_code!!ilangorg!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=lang&caller=notice&p1=f_langorg_code!!ilangorg!!&p2=f_langorg!!ilangorg!!', '!!force_dialog_lang!!', '!!force_popup_lang!!')\" />
        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_langorg!!ilangorg!!.value=''; this.form.f_langorg_code!!ilangorg!!.value=''; \" />
        <input type='hidden' name='f_langorg_code!!ilangorg!!' data-form-name='f_langorg_code' id='f_langorg_code!!ilangorg!!' value='!!langorg_code!!' />
        <input id='button_add_f_langorg_code' type='button' class='bouton' value='+' onClick=\"add_langorg();\"/>
    </div>
    ";
$notice_langorg_next_form_tpl = "
    <div id='el7Child_0b' class='row'>
        <input type='text' class='saisie-30emr' id='f_langorg!!ilangorg!!' name='f_langorg!!ilangorg!!' value=\"!!langorg!!\" completion=\"langue\" autfield=\"f_langorg_code!!ilangorg!!\" />
		<input type='button' class='bouton' value='$msg[parcourir]' onclick=\"openPopUpSelector('./select.php?what=lang&caller=notice&p1=f_langorg_code!!ilangorg!!&p2=f_langorg!!ilangorg!!', '!!force_dialog_lang!!', '!!force_popup_lang!!')\" />
        <input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.f_langorg!!ilangorg!!.value=''; this.form.f_langorg_code!!ilangorg!!.value=''; \" />
        <input type='hidden' name='f_langorg_code!!ilangorg!!' id='f_langorg_code!!ilangorg!!' value='!!langorg_code!!' />
    </div>
    ";

//    ----------------------------window.open------------------------
//    Liens
//       $notice_tab_links_form_tpl : contenu de l'onglet 8 (liens)
//    ----------------------------------------------------

$notice_tab_links_form_tpl = "
<script>
const tabTokens_catal_url = !!tokens_csrf!!;
function chklnk_f_lien(element){
	if(element.value != ''){
		var wait = document.createElement('img');
		wait.setAttribute('src','".get_url_icon('patience.gif')."');
		wait.setAttribute('align','top');
		while(document.getElementById('f_lien_check').firstChild){
			document.getElementById('f_lien_check').removeChild(document.getElementById('f_lien_check').firstChild);
		}

		var csrf_token = tabTokens_catal_url[0];
		tabTokens_catal_url.splice(0, 1);

		document.getElementById('f_lien_check').appendChild(wait);
		var testlink = encodeURIComponent(element.value);
		var req = new XMLHttpRequest();
		req.open('GET', './ajax.php?module=ajax&categ=chklnk&timeout=!!pmb_curl_timeout!!&link='+testlink+'&csrf_token='+csrf_token, true);
		req.onreadystatechange = function (aEvt) {
			if(req.status == 200){
				var img = document.createElement('img');
				var src='';
				var type_status=req.responseText.substr(0,1);
				if(type_status == '2' || type_status == '3'){
					if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
					//impec, on print un petit message de confirmation
					src = '".get_url_icon('tick.gif')."';
				}else{
					//problème...
					src = '".get_url_icon('error.png')."';
					img.setAttribute('style','height:1.5em;');
				}
				img.setAttribute('src',src);
				img.setAttribute('align','top');
				while(document.getElementById('f_lien_check').firstChild){
					document.getElementById('f_lien_check').removeChild(document.getElementById('f_lien_check').firstChild);
				}
				document.getElementById('f_lien_check').appendChild(img);
			} else {
                var img = document.createElement('img');
				var src='';
				//problème...
				src = '".get_url_icon('error.png')."';
				img.setAttribute('style','height:1.5em;');
                img.setAttribute('src',src);
				img.setAttribute('align','top');
				while(document.getElementById('f_lien_check').firstChild){
					document.getElementById('f_lien_check').removeChild(document.getElementById('f_lien_check').firstChild);
				}
				document.getElementById('f_lien_check').appendChild(img);
            }
		};
		req.send(null);
	}
}
</script>
<div id='el8Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el8', '274')."
	    $msg[274]
	</h3>
</div>
<div id='el8Child' class='child' etirable='yes' title='".htmlentities($msg[274],ENT_QUOTES, $charset)."'>
	<div id='el8Child_0' title='".htmlentities($msg[275],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    URL associée    -->
		<div id='el8Child_0a' class='row'>
		    <label for='f_l' class='etiquette'>$msg[275]</label>
		</div>
		<div id='el8Child_0b' class='row'>
			<div id='f_lien_check' style='display:inline'></div>
		    <input name='f_lien' data-form-name='f_lien' type='text' class='saisie-80em' id='f_lien' onchange='chklnk_f_lien(this);' value=\"!!lien!!\" />
		    <input class='bouton' type='button' onClick=\"var l=document.getElementById('f_lien').value; eval('window.open(\''+l+'\')');\" title='$msg[CheckLink]' value='$msg[CheckButton]' />
		</div>
	</div>
	!!element_eformat!!
</div>
";

//    ----------------------------------------------------
//    Onglet map
//    ----------------------------------------------------
global $pmb_map_activate;
if ($pmb_map_activate) {
	$notice_tab_map_form_tpl = "
	<!-- onglet 14 -->
	<div id='el14Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el14', 'notice_map_onglet_title')." ".$msg["notice_map_onglet_title"]."
	</h3>
	</div>
	
	<div id='el14Child' class='child' etirable='yes' title='".htmlentities($msg['notice_map_onglet_title'],ENT_QUOTES, $charset)."'>
		!!element_notice_map!!
	</div>
	";
} else {
	$notice_tab_map_form_tpl = "";
}

//    ----------------------------------------------------
//    Onglet Nomenclature
//    ----------------------------------------------------
global $pmb_nomenclature_activate;
$ptab[15] = '';
if ($pmb_nomenclature_activate)
$ptab[15] = "
<!-- onglet 15 -->
<div id='el15Parent' class='parent'>
	<h3>
        ".get_expandBase_button('el15', 'notice_nomenclature_onglet_title')." ".$msg["notice_nomenclature_onglet_title"]."
	</h3>
</div>
<div id='el15Child' class='child' etirable='yes' title='".htmlentities($msg['notice_nomenclature_onglet_title'],ENT_QUOTES, $charset)."'>
	<div id='el15Child_0' title='".htmlentities($msg['notice_nomenclature_onglet_title'],ENT_QUOTES, $charset)."' movable='yes'>
		<div id='el15Child_0a' class='row'>
		</div>
		<div id='el15Child_0b' class='row'>
			!!nomenclature_form!!
	    </div>
	</div>
</div>
";

//    ----------------------------------------------------
//    Champs personalisés
//       $notice_tab_customs_perso_form_tpl : Contenu de l'onglet 9 (champs personalisés)
//    ----------------------------------------------------

$notice_tab_customs_perso_form_tpl = "
<div id='el9Parent' class='parent'>
	<h3>
	    <img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='el9Img' onClick=\"expandBase('el9', true); recalc_recept(); return false;\" title='".$msg["notice_champs_perso"]."' border='0' /> ".$msg["notice_champs_perso"]."
	</h3>
</div>
<div id='el9Child' class='child' etirable='yes' title='".$msg["notice_champs_perso"]."'>
	!!champs_perso!!
</div>
";

//    ----------------------------------------------------
//    Champs de gestion
//       $notice_tab_gestion_fields_form_tpl : Contenu de l'onglet 10 (champs de gestion)
//    ----------------------------------------------------

$notice_tab_gestion_fields_form_tpl = "
<div id='el10Parent' class='parent'>
<h3>
    ".get_expandBase_button('el10', 'notice_champs_gestion')." ".$msg["notice_champs_gestion"]."
</h3>
</div>
<div id='el10Child' class='child' etirable='yes' title='".htmlentities($msg["notice_champs_gestion"],ENT_QUOTES, $charset)."'>
	!!element_notice_statut!!
	<div id='el10Child_7' title='".htmlentities($msg["notice_is_new_gestion"],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    Nouveauté    -->
		<div id='el10Child_7a' class='row'>
		    <label for='f_new_gestion' class='etiquette'>".$msg["notice_is_new_gestion"]."</label>
		</div>
		<div id='el10Child_7b' class='row'>
		    <input type='radio' name='f_notice_is_new' id='f_notice_is_not_new' !!checked_no!! value='0'><label for='f_notice_is_not_new'>".$msg["notice_is_new_gestion_no"]."</label><br>
		    <input type='radio' name='f_notice_is_new' id='f_notice_is_new' !!checked_yes!! value='1'><label for='f_notice_is_new'>".$msg["notice_is_new_gestion_yes"]."</label><br>
		</div>
	</div>
	!!element_commentaire_gestion!!
	<div id='el10Child_2' title='".htmlentities($msg["notice_thumbnail_url"],ENT_QUOTES, $charset)."' movable='yes'>
		<!--    URL vignette speciale    -->
		<div id='el10Child_2a' class='row'>
			<label for='f_thumbnail_url' class='etiquette'>$msg[notice_thumbnail_url]</label>
		</div>
		<div id='el10Child_2b' class='row'>
			<div id='f_thumbnail_check' style='display:inline'></div>
			<input type='text' class='saisie-80em' id='f_thumbnail_url' name='f_thumbnail_url' rows='1' wrap='virtual' value=\"!!thumbnail_url!!\" onchange='chklnk_f_thumbnail_url(this);' />
		</div>
	</div>";
if(dilicom::is_pnb_active()) {
	$notice_tab_gestion_fields_form_tpl.= "
        !!element_is_numeric!!
		";
}
global $pmb_notice_img_folder_id;
if($pmb_notice_img_folder_id)
	$notice_tab_gestion_fields_form_tpl.= "
		<div id='el10Child_6' title='".htmlentities($msg['notice_img_load'],ENT_QUOTES, $charset)."' movable='yes'>
			<!--    Vignette upload    -->
			<div id='el10Child_6a' class='row'>
			    <label for='f_img_load' class='etiquette'>$msg[notice_img_load]</label>!!message_folder!!
			</div>
			<div id='el10Child_6b' class='row'>
			    <input type='file' class='saisie-80em' id='f_img_load' name='f_img_load' rows='1' wrap='virtual' value='' />
			</div>
			<div id='el10Child_6c' class='row'>
				<img id='f_img_loaded' alt='' src='!!f_img_loaded_src!!'/>
                <button id='reset_thumbnail_button' name='reset_thumbnail_button' onclick='reset_thumbnail()'>
                    <img alt='delete thumbnail' src='./images/cross.png'/>
                </button>
                <input type='hidden' id='f_reset_thumbnail' name='f_reset_thumbnail' value='0' />
                <script>
                    function displayResetThumbnailButton() {
                        var imgThumbnail = document.getElementById('f_img_loaded');
                        if (imgThumbnail && imgThumbnail.src == '') {
                            document.getElementById('reset_thumbnail_button').style.display = 'none';
                        }
                    }
                    function reset_thumbnail() {
                        if (confirm('Voulez-vous supprimer cette vignette ?')) {
                            let inputReset = document.getElementById('f_reset_thumbnail');
                            if (inputReset) {
                                inputReset.value = 1;
                            }
                            document.getElementById('f_img_loaded').src = '';
                            displayResetThumbnailButton();
                        }
                    }
                    displayResetThumbnailButton();
                </script>
			</div>
		</div>";
$notice_tab_gestion_fields_form_tpl.= "
	<div id='el10Child_3' title='".htmlentities($msg['admin_menu_acces'],ENT_QUOTES, $charset)."' movable='yes'>
		<!-- Droits d'acces -->
		<!-- rights_form -->
	</div>
	!!element_indexation_lang!!
";
global $pmb_notices_show_dates;
if($pmb_notices_show_dates)
	$notice_tab_gestion_fields_form_tpl.= "
		<div id='el10Child_9' title='".htmlentities($msg['noti_crea_date'],ENT_QUOTES, $charset)."' movable='yes'>
			<div id='el10Child_9a' class='row'>
				!!dates_notice!!
			</div>
		</div>";
$notice_tab_gestion_fields_form_tpl.= "
	!!element_notice_usage!!
</div>
";

$notice_content_form = "
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
if ($pmb_use_uniform_title) $notice_content_form .= "<hr class='spacer' />!!tab230!!";
$notice_content_form .= "<hr class='spacer' />
!!tab7!!
<hr class='spacer' />
!!tab8!!
<hr class='spacer' />
!!tab9!!
<hr class='spacer' />
!!tab11!!
<hr class='spacer' />
!!tab14!!
<hr class='spacer' />
!!tab15!!
<hr class='spacer' />
!!tab10!!
<hr class='spacer' />
!!authperso!!
";

// $notice_replace : form remplacement notice
$notice_replace = "
<form class='form-".$current_module."' name='notice_replace' method='post' action='./catalog.php?categ=remplace&id=!!id!!'>
	<h3>".$msg['159']." !!old_notice_libelle!! </h3>
	<div class='form-contenu'>
	    <div class='row'>
	        <label class='etiquette' for='par'>".$msg['160']."</label>
		</div>
	    <div class='row'>
	        <input type='text' class='saisie-50emr' value='' id='notice_libelle' name='notice_libelle' completion='notice' autfield='by' autexclude='!!id!!'>
	        <input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=notice&caller=notice_replace&param1=by&param2=notice_libelle&no_display=!!id!!', 'selector_notice')\" title='".$msg['157']."' value='".$msg['parcourir']."' />
	        <input type='button' class='bouton' value='".$msg['raz']."' onclick=\"this.form.notice_libelle.value=''; this.form.by.value='0'; \" />
	        <input type='hidden' id='by' name='by' value=''>
	    </div>
		!!notice_replace_categories!!
		<div class='row'>
			<input type='radio' name='notice_replace_links' value='0' ".(isset($deflt_notice_replace_links) && $deflt_notice_replace_links == 0?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_all']."
			<input type='radio' name='notice_replace_links' value='1' ".(isset($deflt_notice_replace_links) && $deflt_notice_replace_links == 1?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replacing']."
			<input type='radio' name='notice_replace_links' value='2' ".(isset($deflt_notice_replace_links) && $deflt_notice_replace_links == 2?"checked='checked'":"")." /> ".$msg['notice_replace_links_option_keep_replaced']."
		</div>
	</div>
	<div class='row'>
	    <input type='button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\">
	    <input type='submit' class='bouton' value='".$msg['159']."'>
	</div>
</form>
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";

$notice_replace_categories = "
<div class='row'>&nbsp;</div>
<div class='row'>
	<label class='etiquette' for='keep_categories_label'>".$msg["notice_replace_keep_categories"]."</label>
</div>
<div class='row'>
	".$msg[39]." <input type='radio' name='keep_categories' value='0' checked='checked' onclick=\"document.getElementById('notice_replace_categories').setAttribute('style','display:none;');\" />
	".$msg[40]." <input type='radio' name='keep_categories' value='1' onclick=\"document.getElementById('notice_replace_categories').setAttribute('style','');\" />
</div>
<div class='row'>&nbsp;</div>
<div class='row' id='notice_replace_categories' style='display:none';>
	!!notice_replace_category!!
	<input type='hidden' id='f_nb_categ' name='f_nb_categ' value='!!nb_categ!!' />
</div>
		";
$notice_replace_category = "
<div class='row'>
	<input type='checkbox' id='f_categ!!icateg!!' name='f_categ!!icateg!!' checked='checked' />
	!!categ_libelle!!
	<input type='hidden' name='f_categ_id!!icateg!!' id='f_categ_id!!icateg!!' value='!!categ_id!!' />
</div>";

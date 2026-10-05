// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: receptions.js,v 1.8 2019/07/18 13:50:35 dgoron Exp $

/*
 * n�cessite :	ajax.js
 * 				tablist.js
 * 				http_request.js
 *  
 * variables a d�clarer dans le formulaire appelant:
 * msg_parcourir		//valeur bouton parcourir
 * msg_raz				//valeur bouton suppression
 * msg_uncheckAll		//message acquisition_recept_uncheckAll
 * msg_uncheckAll		//message acquisition_recept_checkAll
 * option_num_auto		//numerotation automatique activee
 */


//initialisations
var check = true;

function expandRow(el, unexpand) {
	  if (!isDOM)
	    return;

	  var whichEl = document.getElementById(el + 'Child');
	  var whichIm = document.getElementById(el + 'Img');
	  if (whichEl.style.display == 'none' && whichIm) {
	    whichEl.style.display  = 'table-row';
	    whichIm.src            = imgOpened.src;
	    changeCoverImage(whichEl);
	  }
	  else if (unexpand) {
	    whichEl.style.display  = 'none';
	    whichIm.src            = imgClosed.src;
	  }
}


//selection fournisseur
function sel_fou() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,5)+'_code'+name.substr(5);
    var id_bibli = document.getElementById('id_bibli').value;
    var deb_rech = document.getElementById(name).value;
    openPopUp('./select.php?what=fournisseur&caller=recept_search_form&param1='+name_id+'&param2='+name+'&id_bibli='+id_bibli+'&deb_rech='+encode_URL(deb_rech), 'selector');
}


//raz fournisseur
function raz_fou() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,5)+'_code'+name.substr(5);
    document.getElementById(name_id).value=0;
    document.getElementById(name).value='';
}


//ajout fournisseur
function add_fou() {
    var template = document.getElementById('add_fou');
    var fou=document.createElement('div');
    fou.className='row';

    var suffixe = document.getElementById('max_fou').value;
    var nom_id = 'f_fou'+suffixe
    var f_fou = document.createElement('input');
    f_fou.setAttribute('id',nom_id);
    f_fou.setAttribute('type','text');
    f_fou.className='saisie-20emr';
    f_fou.setAttribute('value','');
	f_fou.setAttribute('completion','fournisseur');
    f_fou.setAttribute('autfield','f_fou_code'+suffixe);
    f_fou.setAttribute('linkfield','id_bibli');

    var del_f_fou = document.createElement('input');
    del_f_fou.setAttribute('id','del_f_fou'+suffixe);
    del_f_fou.onclick=raz_fou;
    del_f_fou.setAttribute('type','button');
    del_f_fou.className='bouton_small';
    del_f_fou.setAttribute('readonly','');
    del_f_fou.setAttribute('value',msg_raz);

    var sel_f_fou = document.createElement('input');
    sel_f_fou.setAttribute('id','sel_f_fou'+suffixe);
    sel_f_fou.setAttribute('type','button');
    sel_f_fou.className='bouton_small';
    sel_f_fou.setAttribute('readonly','');
    sel_f_fou.setAttribute('value',msg_parcourir);
    sel_f_fou.onclick=sel_fou;

    var f_fou_code = document.createElement('input');
    f_fou_code.name='f_fou_code['+suffixe+']';
    f_fou_code.setAttribute('type','hidden');
    f_fou_code.setAttribute('id','f_fou_code'+suffixe);
    f_fou_code.setAttribute('value','');

    fou.appendChild(f_fou);
    var space=document.createTextNode(' ');
    fou.appendChild(space);
    fou.appendChild(sel_f_fou);
    fou.appendChild(space.cloneNode(false));
    fou.appendChild(del_f_fou);
    fou.appendChild(f_fou_code);

    template.appendChild(fou);

    document.recept_search_form.max_fou.value=suffixe*1+1*1 ;
    ajax_pack_element(f_fou);
}


//selection demandeur
function sel_dem() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,5)+'_code'+name.substr(5);
    var name_type = 't'+name.substr(1);
    var deb_rech = document.getElementById(name).value;
    openPopUp('./select.php?what=origine&sub=empr&caller=recept_search_form&param1='+name_id+'&param2='+name+'&param3='+name_type+'&deb_rech='+encode_URL(deb_rech), 'selector');
}


//raz demandeur
function raz_dem() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,5)+'_code'+name.substr(5);
    var type = 't_dem'+name.substr(5);
    document.getElementById(name_id).value=0;
    document.getElementById(name).value='';
    document.getElementById(type).value='';
}


//ajout demandeur
function add_dem() {
    var template = document.getElementById('add_dem');
    var dem=document.createElement('div');
    dem.className='row';

    var suffixe = document.getElementById('max_dem').value;
    var nom_id = 'f_dem'+suffixe
    var f_dem = document.createElement('input');
    f_dem.setAttribute('id',nom_id);
    f_dem.setAttribute('type','text');
    f_dem.className='saisie-20emr';
    f_dem.setAttribute('value','');
	f_dem.setAttribute('completion','origine');
    f_dem.setAttribute('autfield','f_dem_code'+suffixe);
    f_dem.setAttribute('callback','after_dem');

    var del_f_dem = document.createElement('input');
    del_f_dem.setAttribute('id','del_f_dem'+suffixe);
    del_f_dem.onclick=raz_dem;
    del_f_dem.setAttribute('type','button');
    del_f_dem.className='bouton_small';
    del_f_dem.setAttribute('readonly','');
    del_f_dem.setAttribute('value',msg_raz);

    var sel_f_dem = document.createElement('input');
    sel_f_dem.setAttribute('id','sel_f_dem'+suffixe);
    sel_f_dem.setAttribute('type','button');
    sel_f_dem.className='bouton_small';
    sel_f_dem.setAttribute('readonly','');
    sel_f_dem.setAttribute('value',msg_parcourir);
    sel_f_dem.onclick=sel_dem;

    var f_dem_code = document.createElement('input');
    f_dem_code.name='f_dem_code['+suffixe+']';
    f_dem_code.setAttribute('type','hidden');
    f_dem_code.setAttribute('id','f_dem_code'+suffixe);
    f_dem_code.setAttribute('value','');

    var t_dem = document.createElement('input');
    t_dem.name='t_dem['+suffixe+']';
    t_dem.setAttribute('type','hidden');
    t_dem.setAttribute('id','t_dem'+suffixe);
    t_dem.setAttribute('value','');
    
    dem.appendChild(f_dem);
    var space=document.createTextNode(' ');
    dem.appendChild(space);
    dem.appendChild(sel_f_dem);
    dem.appendChild(space.cloneNode(false));
    dem.appendChild(del_f_dem);
    dem.appendChild(f_dem_code);
    dem.appendChild(t_dem);

    template.appendChild(dem);

    document.recept_search_form.max_dem.value=suffixe*1+1*1 ;
    ajax_pack_element(f_dem);
}


//callback apr�s selection demandeur
function after_dem(f_dem) {
	var suffixe=f_dem.substr(5);
	var f_dem_code=document.getElementById('f_dem_code'+suffixe);
	var tab=f_dem_code.value.split(',');
	f_dem_code.value=tab[0];
	document.getElementById('t_dem'+suffixe).value=tab[1];
}


//selection rubrique budgetaire
function sel_rub() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,5)+'_code'+name.substr(5);
    var id_bibli=document.getElementById('id_bibli').value;
    var id_exer=document.getElementById('id_exer').value;
    var deb_rech = document.getElementById(name).value;
    openPopUp('./select.php?what=rubriques&caller=recept_search_form&param1='+name_id+'&param2='+name+'&id_bibli='+id_bibli+'&id_exer='+id_exer+'&deb_rech='+encode_URL(deb_rech), 'selector');
}


//raz rubrique budgetaire
function raz_rub() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,5)+'_code'+name.substr(5);
    document.getElementById(name_id).value=0;
    document.getElementById(name).value='';
}


//ajout rubrique budgetaire
function add_rub() {
    var template = document.getElementById('add_rub');
    var rub=document.createElement('div');
    rub.className='row';

    var suffixe = document.getElementById('max_rub').value;
    var nom_id = 'f_rub'+suffixe
    var f_rub = document.createElement('input');
    f_rub.setAttribute('id',nom_id);
    f_rub.setAttribute('type','text');
    f_rub.className='saisie-20emr';
    f_rub.setAttribute('value','');
	f_rub.setAttribute('completion','rubrique');
    f_rub.setAttribute('autfield','f_rub_code'+suffixe);
    f_rub.setAttribute('linkfield','id_exer');

    var del_f_rub = document.createElement('input');
    del_f_rub.setAttribute('id','del_f_rub'+suffixe);
    del_f_rub.onclick=raz_rub;
    del_f_rub.setAttribute('type','button');
    del_f_rub.className='bouton_small';
    del_f_rub.setAttribute('readonly','');
    del_f_rub.setAttribute('value',msg_raz);

    var sel_f_rub = document.createElement('input');
    sel_f_rub.setAttribute('id','sel_f_rub'+suffixe);
    sel_f_rub.setAttribute('type','button');
    sel_f_rub.className='bouton_small';
    sel_f_rub.setAttribute('readonly','');
    sel_f_rub.setAttribute('value',msg_parcourir);
    sel_f_rub.onclick=sel_rub;

    var f_rub_code = document.createElement('input');
    f_rub_code.name='f_rub_code['+suffixe+']';
    f_rub_code.setAttribute('type','hidden');
    f_rub_code.setAttribute('id','f_rub_code'+suffixe);
    f_rub_code.setAttribute('value','');

    rub.appendChild(f_rub);
    var space=document.createTextNode(' ');
    rub.appendChild(space);
    rub.appendChild(sel_f_rub);
    rub.appendChild(space.cloneNode(false));
    rub.appendChild(del_f_rub);
    rub.appendChild(f_rub_code);

    template.appendChild(rub);

    document.recept_search_form.max_rub.value=suffixe*1+1*1 ;
    ajax_pack_element(f_rub);
}


//Coche et decoche les elements de la liste
function checkAll(the_form, the_objet, do_check) {

	var elts = document.forms[the_form].elements[the_objet+'[]'] ;
	var elts_cnt  = (typeof(elts.length) != 'undefined')
              ? elts.length
              : 0;

	if (elts_cnt) {
		for (var i = 0; i < elts_cnt; i++) {
			elts[i].checked = do_check;
		} 
	} else {
		elts.checked = do_check;
	}
	if (check == true) {
		check = false;
		document.getElementById('bt_chk').value = msg_uncheckAll;
	} else {
		check = true;
		document.getElementById('bt_chk').value = msg_checkAll;	
	}
}


//actualisation formulaire
function actualize(form) {
	form.setAttribute('action','./acquisition.php?categ=ach&sub=recept&action=list');
	form.submit();
}
	

//enregistrement des modifications par lot
function apply_changes(form) {
	form.setAttribute('action','./acquisition.php?categ=ach&sub=recept&action=apply_changes');
	form.submit();
}


//envoi des relances
function do_relances(form) {
	var checkboxes = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][name=\"chk[]\"]:checked:enabled'));
	if(checkboxes.length) {
		var relance_already = false;
		var date = new Date();
		var month = ''+(date.getMonth()+1);
		var day = ''+date.getDate();
		var year = date.getFullYear();
		if (month.length < 2) month = '0' + month;
	    if (day.length < 2) day = '0' + day;
		var today = year+'-'+month+'-'+day;
		checkboxes.forEach(function(checkbox) {
			var lastRelance = Array.prototype.slice.call(document.querySelectorAll('input[type=\"hidden\"][id=\"date_last_relance['+checkbox.value+']\"]'));
			if(lastRelance && (typeof lastRelance[0] !== 'undefined') && lastRelance[0].value == today) {
				relance_already = true;
			}
		});
		if(!relance_already || confirm(pmbDojo.messages.getMessage('acquisition', 'acquisition_relance_already_confirm'))) {
			form.setAttribute('action','./acquisition.php?categ=ach&sub=recept&action=do_relances');
			form.submit();
		}
	} else {
		alert(pmbDojo.messages.getMessage('acquisition', 'acquisition_act_tab_checked_empty'));
		return false;
	}
}

//catalogage
function catalog(form, id_lig) {
	document.location='./acquisition.php?categ=ach&sub=recept&action=catalog'+'&id_lig='+id_lig+'&serialized_search='+document.getElementById('serialized_search').value;
}

//Ouverture de la frame a partir du parent
function recept_openFrame(obj,no) {
	recept_btFrame=obj;
	
	var recept_layer=document.getElementById('recept_layer');
	if (undefined==recept_layer) {
		
		recept_layer=document.createElement("div");
		recept_layer.setAttribute('id','recept_layer');
		recept_layer.setAttribute('style','position:absolute;left:0;z-index:1001;');
		recept_layer.setAttribute('onclick','recept_killFrame();');
		recept_layer.style.width=getWindowWidth()+'px';
		recept_layer.style.height=getWindowHeight()+'px';
		recept_layer.style.top=getWindowScrollY()+'px';
		document.getElementsByTagName('body')[0].appendChild(recept_layer);

	}
	recept_frame=document.createElement("iframe");		
	recept_frame.setAttribute('id','recept_frame');
	recept_frame.setAttribute('name','recept_frame');
	
	var id_lig=document.getElementById('id_lig['+no+']').value;
	var typ_lig=document.getElementById('typ_lig['+no+']').value;
	var id_prod=document.getElementById('id_prod['+no+']').value;
	recept_frame.src="./acquisition/achats/receptions/receptions_frame.php?action=show&no="+no
		+"&id_lig="+id_lig+"&typ_lig="+typ_lig+"&id_prod="+id_prod;
	if ( typ_lig==1 && (option_num_auto==1 || option_num_auto==2) ) {
		recept_frame.src+="&option_num_auto=1";
	}
	if ( typ_lig==2 && (option_num_auto==1 || option_num_auto==3) ) {
		recept_frame.src+="&option_num_auto=1";
	}
	recept_resizeFrame(obj);
	recept_frame.style.visibility="visible";	
	recept_frame.style.display='block';	
	recept_layer.appendChild(recept_frame);		
	recept_layer.parentNode.style.overflow = "hidden";
}


//redimensionnement frame
function recept_resizeFrame() {
	
	var recept_layer = document.getElementById('recept_layer');
	if (recept_layer) {
		recept_layer.style.width=getWindowWidth()+'px';
		recept_layer.style.height=getWindowHeight()+'px';
		recept_layer.style.top=getWindowScrollY()+'px';
		
		recept_frame.style.top='5%';
		recept_frame.style.width='90%';
		recept_frame.style.height='90%';
		recept_frame.style.left='5%';
		
	}
}

window.onresize = recept_resizeFrame;


//Destruction de la frame
function recept_killFrame() {
	var recept_layer = document.getElementById('recept_layer');
	recept_layer.parentNode.style.overflow = "auto";
	recept_layer.parentNode.removeChild(recept_layer);
	recept_btFrame=undefined;
}


//hauteur fenetre
function getWindowHeight(){
	if(window.innerHeight) 
		return window.innerHeight;
	else return document.body.clientHeight;
}


//largeur fenetre
function getWindowWidth(){
	if(window.innerWidth) 
		return window.innerWidth;
	else return document.body.clientWidth;
}


//position verticale curseur
function getWindowScrollY(){
	if(window.scrollY)
		return window.scrollY;
	else return document.documentElement.scrollTop;
}


//recherche d'un code dans les receptions
function search_code(obj,recept_query) {
	
	var code=recept_query.value;
	if (undefined==code || ''==code ){
		return false;
	}
	var codes=new Array();
	url= "./ajax.php?module=ajax&categ=isbn&code="+encodeURI(code)+"&fname=getPatterns";
	// On initialise la classe:
	var getPatterns = new http_request();
	// Ex�cution de la requete
	if(getPatterns.request(url,1)){
		// Il y a une erreur. Afficher le message retourn�
		alert (getPatterns.get_text());			
	}else { 
		 var codes = typeof JSON !='undefined' ?  JSON.parse(getPatterns.get_text()) : eval('('+getPatterns.get_text()+')');
		 codes.unshift(code);
	}
	
	var elts=obj.form.elements['code[]'];
	var found=false;
	var i=0;var j=0;
	while(!found && (i<elts.length)) {
		j=0;
		while(!found && (j<codes.length)) {
			if ((elts[i].value==codes[j])||(elts[i].value.replace(/[ -]/g,'')==codes[j])) {
				found=true;
			}
			j++;
		}
		i++;
	}
	if (found) {
		var code_id = elts[i-1].getAttribute('id');
		var bt_rec = document.getElementById('bt_rec'+code_id.substring(4));
		recept_query.value='';
		bt_rec.click();
	}
	return false;
}


//Modification d'un commentaire
 function recept_mod_comment(id){
	var comment = document.getElementById(id);
	var comment_mod = document.getElementById(id+'_mod');
	comment_mod.value = comment.innerHTML.replace(/<br>/g, '\r');
	comment.style.display='none';
	comment_mod.parentNode.style.display='block';
	comment_mod.focus();
}

 
//Annulation modification d'un commentaire
 function recept_undo_comment(id){
	 var comment = document.getElementById(id);
	 var comment_mod = document.getElementById(id+'_mod');
	 comment_mod.parentNode.style.display='none';
	 comment.style.display='block';
}
 
 
//Mise a jour et enregistrement d'un commentaire
function recept_upd_comment(id){
	var comment = document.getElementById(id);
	var comment_mod = document.getElementById(id+'_mod');
	var url= "./ajax.php?module=acquisition&categ=ach&sub=recept&quoifaire=upd_comment&id="+id+"&comment="+encodeURIComponent(comment_mod.value);
	var req = new http_request();
	if(req.request(url,1)){
		//Il y a une erreur
	} else {
		comment_mod.value = '';
		comment.innerHTML = req.get_text();
		comment_mod.parentNode.style.display='none';
		comment.style.display='block';
	}
}


//Suppression d'un commentaire
function recept_del_comment(id){
	var comment = document.getElementById(id);
	var comment_mod = document.getElementById(id+'_mod');
	comment.innerHTML='';
	comment_mod.Value='';
	recept_upd_comment(id);
}


//Mise a jour d'un statut de ligne
function recept_upd_lgstat (id) {
	var lgstat=document.getElementById(id); 
	var url= "./ajax.php?module=acquisition&categ=ach&sub=recept&quoifaire=upd_lgstat&id="+id+"&lgstat="+lgstat.value;
	var req = new http_request();
	if(req.request(url,1)){
		//Il y a une erreur
	} else {}
}

ajax_parse_dom();

document.getElementById('cde_query').focus();

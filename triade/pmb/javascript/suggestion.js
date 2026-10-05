// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestion.js,v 1.2 2017/09/28 09:23:37 dgoron Exp $

//selection origine
function sel_orig() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,4)+'_id'+name.substr(8);
    var name_statut = name.substr(0,4)+'_statut'+name.substr(8);
    var deb_rech = document.getElementById(name).value;
    openPopUp('./select.php?what=origine&caller=search&param1='+name_id+'&param2='+name+'&param3='+name_statut+'&deb_rech='+encode_URL(deb_rech)+'&callback=filtrer_user', 'selector');
}


//raz origine
function raz_orig() {
    var name=this.getAttribute('id').substring(4);
    var name_id = name.substr(0,4)+'_id'+name.substr(8);
    var name_statut = name.substr(0,4)+'_statut'+name.substr(8);
    document.getElementById(name_id).value=0;
    document.getElementById(name).value='';
    document.getElementById(name_statut).value=0;
}


//ajout origine
function add_orig() {
    var template = document.getElementById('add_orig');
    var orig=document.createElement('div');
    orig.className='row';

    var suffixe = document.getElementById('max_orig').value;
    var nom_id = 'user_txt'+suffixe
    var f_orig = document.createElement('input');
    f_orig.setAttribute('id',nom_id);
    f_orig.setAttribute('type','text');
    f_orig.className='saisie-20emr';
    f_orig.setAttribute('value','');
	f_orig.setAttribute('completion','origine');
    f_orig.setAttribute('autfield','user_id'+suffixe);
    f_orig.setAttribute('callback','after_orig');

    var del_f_orig = document.createElement('input');
    del_f_orig.setAttribute('id','del_user_txt'+suffixe);
    del_f_orig.onclick=raz_orig;
    del_f_orig.setAttribute('type','button');
    del_f_orig.className='bouton_small';
    del_f_orig.setAttribute('readonly','');
    del_f_orig.setAttribute('value',msg_raz);

    var sel_f_orig = document.createElement('input');
    sel_f_orig.setAttribute('id','sel_user_txt'+suffixe);
    sel_f_orig.setAttribute('type','button');
    sel_f_orig.className='bouton_small';
    sel_f_orig.setAttribute('readonly','');
    sel_f_orig.setAttribute('value',msg_parcourir);
    sel_f_orig.onclick=sel_orig;

    var f_orig_code = document.createElement('input');
    f_orig_code.name='user_id['+suffixe+']';
    f_orig_code.setAttribute('type','hidden');
    f_orig_code.setAttribute('id','user_id'+suffixe);
    f_orig_code.setAttribute('value','');

    var t_orig = document.createElement('input');
    t_orig.name='user_statut['+suffixe+']';
    t_orig.setAttribute('type','hidden');
    t_orig.setAttribute('id','user_statut'+suffixe);
    t_orig.setAttribute('value','');
    
    orig.appendChild(f_orig);
    var space=document.createTextNode(' ');
    orig.appendChild(space);
    orig.appendChild(sel_f_orig);
    orig.appendChild(space.cloneNode(false));
    orig.appendChild(del_f_orig);
    orig.appendChild(f_orig_code);
    orig.appendChild(t_orig);

    template.appendChild(orig);

    document.search.max_orig.value=suffixe*1+1*1 ;
    ajax_pack_element(f_orig);
}


//callback apr�s selection origine
function after_orig(f_orig) {
	var suffixe=f_orig.substr(5);
	console.log(suffixe);
	var f_orig_code=document.getElementById('user_id'+suffixe);
	var tab=f_orig_code.value.split(',');
	f_orig_code.value=tab[0];
	document.getElementById('user_statut'+suffixe).value=tab[1];
}
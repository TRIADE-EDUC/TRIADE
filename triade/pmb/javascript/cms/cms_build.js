// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_build.js,v 1.46 2023/12/14 15:46:57 pmallambic Exp $

var cms_build_obj_list_id=new Array();
var cms_build_obj_list_type=new Array();


function cms_build_findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
		}
	}
	return [curleft,curtop];
}


function cms_build_mouse_is_on(e,obj) {
	var i;
	var pos_mouse=getCoordinate(e);

	var r=document.getElementById(obj);
	var pos=cms_build_findPos(r);
	var r_x=pos[0];
	var r_y=pos[1];
	var r_width=r.offsetWidth;
	var r_height=r.offsetHeight;
	if ( ((pos_mouse[0]>r_x)&&(pos_mouse[0] < (parseFloat(r_x)+parseFloat(r_width)))) &&
		((pos_mouse[1]>r_y)&&(pos_mouse[1] < (parseFloat(r_y)+parseFloat(r_height)))) ) 	{
		var info=new Array();
		info["id"]=r;
		info["mouse_x"]=pos_mouse[0];
		info["mouse_y"]=pos_mouse[1];
		return info;
	}
	return false;
}

function cms_change_objet_css(id){
	var obj_val=document.getElementById(id);
	var obj_val_def=document.getElementById(id+'_def');
	if(obj_val_def.options[0].selected == true){
		return 'auto';
	}else if(obj_val_def.options[3].selected == true){
		return 'inherit';
	}else if(obj_val_def.options[1].selected == true){
		return obj_val.value+'px';
	}else if(obj_val_def.options[2].selected == true){
		return obj_val.value+'%';
	}

}


function cms_change_css(id){
	var obj =parent.frames['opac_frame'].document.getElementById(id);
	obj.style.left=cms_change_objet_css("cms_left");
	obj.style.top=cms_change_objet_css("cms_top");

	obj.style.zIndex=document.getElementById("cms_zIndex").value;

	obj.style.height=cms_change_objet_css("cms_height");
	obj.style.width=cms_change_objet_css("cms_width");

	obj.style.marginTop=cms_change_objet_css("cms_margin_top");
	obj.style.marginRight=cms_change_objet_css("cms_margin_right");
	obj.style.marginBottom=cms_change_objet_css("cms_margin_bottom");
	obj.style.marginLeft=cms_change_objet_css("cms_margin_left");
	obj.style.paddingTop=cms_change_objet_css("cms_padding_top");
	obj.style.paddingRight=cms_change_objet_css("cms_padding_right");
	obj.style.paddingBottom=cms_change_objet_css("cms_padding_bottom");
	obj.style.paddingLeft=cms_change_objet_css("cms_padding_left");

	var theselector=document.getElementById("cms_float");
	obj.style.cssFloat=theselector.options[theselector.selectedIndex].value;

	var theselector=document.getElementById("cms_position");
	obj.style.position=theselector.options[theselector.selectedIndex].value;
	var theselector=document.getElementById("cms_visibility");
	obj.style.visibility=theselector.options[theselector.selectedIndex].value;
	var theselector=document.getElementById("cms_display");
	obj.style.display=theselector.options[theselector.selectedIndex].value;
}

function cms_gen_objet_css(id,val,id_block){

	var obj_val=document.getElementById(id);
	var obj_val_def=document.getElementById(id+'_def');
	if(obj_val_def){
		if(val== 'auto' || val==''){
			obj_val_def.options[0].selected = true;
			obj_val_def.style.display='block';
		}else if(val== 'inherit'){
			obj_val_def.options[3].selected = true;
			obj_val_def.style.display='block';
		}else if(val.substr(val.length-2, 2)== 'px'){
			obj_val.value=parseInt(val.substr(0,val.length-2));
			obj_val_def.options[1].selected = true;
			obj_val_def.style.display='block';
		}else if(val.substr(val.length-1, 1)== '%'){
			obj_val.value=val.substr(0,val.length-1)
			obj_val_def.options[2].selected = true;
			obj_val_def.style.display='block';
		}
	}
	obj_val.onchange="cms_change_css('"+id_block+"');return false;"


}
function cms_show_css_obj(id){

	var cms_edit_form=document.getElementById("cms_edit_form");
	document.getElementById("cms_edit_form").setAttribute("cms_edit_id",id);
	document.getElementById("cms_edit_title_obj").innerHTML=id;

	var bt=document.createElement('input');
	bt.setAttribute('type', 'button');
	bt.setAttribute('id', 'cms_edit_form_reset_css_obj_id');
	bt.setAttribute('class', 'bouton');
	bt.setAttribute('value', msg_cms_reset_css);
	bt.setAttribute("onclick", "cms_reset_css('"+id+"'); return false;");
	if(document.getElementById("cms_edit_form_reset_css_obj_id")) {
		document.getElementById("cms_edit_form_reset_css_obj").removeChild(document.getElementById("cms_edit_form_reset_css_obj_id"));
	}
	document.getElementById("cms_edit_form_reset_css_obj").appendChild(bt);

	var obj=parent.frames['opac_frame'].document.getElementById(id);
	var style=getComputedStyle(obj);

	var theselector=document.getElementById("cms_position")
	for (var i=1 ; i< theselector.options.length ; i++){
		if (theselector.options[i].value == style.getPropertyValue("position")){
			theselector.options[i].selected = true;
		}else theselector.options[i].selected = false;
	}
	var theselector=document.getElementById("cms_float")
	for (var i=1 ; i< theselector.options.length ; i++){
		if (theselector.options[i].value == style.getPropertyValue("float")){
			theselector.options[i].selected = true;
		}else theselector.options[i].selected = false;
	}
	var theselector=document.getElementById("cms_visibility")
	for (var i=1 ; i< theselector.options.length ; i++){
		if (theselector.options[i].value == style.getPropertyValue("visibility")){
			theselector.options[i].selected = true;
		}else theselector.options[i].selected = false;
	}
	var theselector=document.getElementById("cms_display")
	for (var i=1 ; i< theselector.options.length ; i++){
		if (theselector.options[i].value == style.getPropertyValue("display")){
			theselector.options[i].selected = true;
		}else theselector.options[i].selected = false;
	}

	cms_gen_objet_css("cms_left",style.left,id);
	cms_gen_objet_css("cms_top",style.top,id);
	cms_gen_objet_css("cms_zIndex",style.zIndex,id);

	cms_gen_objet_css("cms_height",style.height,id);
	cms_gen_objet_css("cms_width",style.width,id);
	cms_gen_objet_css("cms_margin_top",style.getPropertyValue("margin-top"),id);
	cms_gen_objet_css("cms_margin_right",style.getPropertyValue("margin-right"),id);
	cms_gen_objet_css("cms_margin_bottom",style.getPropertyValue("margin-bottom"),id);
	cms_gen_objet_css("cms_margin_left",style.getPropertyValue("margin-left"),id);
	cms_gen_objet_css("cms_padding_top",style.getPropertyValue("padding-top"),id);
	cms_gen_objet_css("cms_padding_right",style.getPropertyValue("padding-right"),id);
	cms_gen_objet_css("cms_padding_bottom",style.getPropertyValue("padding-bottom"),id);
	cms_gen_objet_css("cms_padding_left",style.getPropertyValue("padding-left"),id);

	document.getElementById("cms_display").value=style.getPropertyValue("display");

}
function cms_desel_all_obj(){
	var objects = parent.frames['opac_frame'].document.getElementsByClassName("cms_drag");
	for(var i=0 ; i<objects.length ; i++){
		objects[i].classList.remove("cms_drag");
	}
}

function cms_show_obj(id){
	var obj=parent.frames['opac_frame'].document.getElementById(id);

	if(obj.classList.contains("cms_drag")){
		obj.classList.remove("cms_drag");
	} else {
		cms_desel_all_obj();
		obj.classList.add("cms_drag");
		obj.style.visibility="visible";
		obj.style.display="block";
		cms_show_css_obj(id);
	}
}

function cms_add_obj_link(node,id){
	cms_build_obj_list_id[cms_build_obj_list_id.length]=id;
	cms_build_obj_list_type[cms_build_obj_list_id.length]=node;

	var tr=document.createElement('tr');
	if(cms_build_obj_list_id.length %2) var odd_even='odd';
	else var odd_even='even';
	tr.setAttribute('class', odd_even);
	tr.style.cursor= 'pointer';
	tr.setAttribute('onmouseout', "this.className='"+odd_even+"'");
	tr.setAttribute('onmouseover', "this.className='surbrillance'");
	tr.setAttribute("onclick", "cms_show_obj('"+id+"'); return false;");
	var tn = document.createTextNode(cms_name_list[id]);
	tr.appendChild(tn);
	document.getElementById(node+'_table').appendChild(tr);
}

function cms_deplacement_activate(){
	var cell = document.getElementById("cms_edit_sel_objet_list_table");
	while(cell.childNodes.length)	cell.removeChild(cell.firstChild);
	var cell = document.getElementById("cms_edit_sel_cadre_list_table");
	while(cell.childNodes.length)	cell.removeChild(cell.firstChild);

	cms_build_obj_list_id=new Array();
	cms_build_obj_list_type=new Array();

	var opac=parent.frames['opac_frame'];
	for(var i=0;i<cms_objet_list.length;i++){
		if(opac.document.getElementById(cms_objet_list[i])) cms_add_obj_link("cms_edit_sel_objet_list",cms_objet_list[i]);
	}
	for(var i=0;i<cms_zone_list.length;i++){
		if(opac.document.getElementById(cms_zone_list[i])) cms_add_obj_link("cms_edit_sel_cadre_list",cms_zone_list[i]);
	}

}

function cms_build_mouse_down(e) {
	//On annule tous les comportements par defaut du navigateur (ex : selection de texte)
	if (!e) var e=window.event;
	if (e.stopPropagation) {
		e.preventDefault();
		e.stopPropagation();
	} else {
		e.cancelBubble=true;
		e.returnValue=false;
	}
    if ('which' in e) {
        switch (e.which) {
	        case 3: // right button
	        	cms_build_mouse_right(e);
	        	return;
	        break;
        }

	}
}

function cms_drag_activate_obj(id,actif) {
	var obj=parent.frames['opac_frame'].document.getElementById(id);
	if(obj){
		if(actif){
			obj.setAttribute('draggable', 'yes');
			obj.setAttribute('dragtype', 'opacdrop');
			obj.setAttribute('oncontextmenu', 'return false');
		} else{
			obj.setAttribute('draggable', 'no');
			obj.setAttribute('dragtype', 'opacdrop');
			obj.setAttribute('oncontextmenu', '');

		}
		var list_id=document.getElementById('cms_edit_sel_objet_list');
	}
}

function cms_drop_activate_obj(id,actif) {
	var obj=parent.frames['opac_frame'].document.getElementById(id);
	if(obj){
		if(actif){
			obj.setAttribute('recept', 'yes');
			obj.setAttribute('recepttype', 'opacdrop');
			obj.setAttribute('downlight', 'cms_block_downlight');
			obj.setAttribute('highlight', 'cms_block_highlight');
			obj.setAttribute('oncontextmenu', 'return false');
		} else {
			obj.setAttribute('recept', 'no');
			obj.setAttribute('recepttype', 'opacdrop');
			obj.setAttribute('downlight', '');
			obj.setAttribute('highlight', '');
			obj.setAttribute('oncontextmenu', '');
		}
	}
}

function cms_drag_activate(actif,cms_dragable_type,cms_receptable_type) {

	if(typeof netscape !== 'undefined') {
		if(netscape.security.PrivilegeManager)netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead');
	}
	cms_memo_opacdrop =new Array();

	var opac=parent.frames['opac_frame'];
	// cadres d�placables
	var activer=0;
	if(cms_dragable_type=="cadre")	activer=actif;

	// pour toutes les zones d�clar�e dans le xml, on va chercher les childNodes div (soit les cadres )
	for(var i=0;i<cms_zone_list.length;i++){
		var div_zone=opac.document.getElementById(cms_zone_list[i]);
		if(div_zone)
		for(var j=0;j<div_zone.childNodes.length;j++){
			if(div_zone.childNodes[j].nodeType == 1){
				if(div_zone.childNodes[j].getAttribute("id")){
					// c'est un cadre du type <div id='XXXX'
					var div_cadre=div_zone.childNodes[j].getAttribute('id');
					// on active annule le d�placement du cadre
					cms_drag_activate_obj(div_cadre,activer);
				}
			}
		}
	}

	if(cms_dragable_type=="zone"){
		for(var i=0;i<cms_zone_list_dragable.length;i++){
			// if(opac.document.getElementById(cms_zone_list[i])) cms_drag_activate_obj(cms_zone_list[i],actif);
			if(opac.document.getElementById(cms_zone_list_dragable[i])) cms_drag_activate_obj(cms_zone_list_dragable[i],actif);
		}
	}else{
		for(var i=0;i<cms_zone_list_dragable.length;i++){
			//if(opac.document.getElementById(cms_zone_list[i])) cms_drag_activate_obj(cms_zone_list[i],0);
			if(opac.document.getElementById(cms_zone_list_dragable[i])) cms_drag_activate_obj(cms_zone_list_dragable[i],0);
		}
	}
	// R�cepteurs ( 'zone' ou 'conteneur' )
	if(cms_receptable_type=="zone" && cms_dragable_type!="zone"){
		for(var i=0;i<cms_zone_list.length;i++){
			if(cms_zone_list[i]!="main")
			if(opac.document.getElementById(cms_zone_list[i])) cms_drop_activate_obj(cms_zone_list[i],actif);
		}
	}else{
		for(var i=0;i<cms_zone_list.length;i++){
			if(opac.document.getElementById(cms_zone_list[i])) cms_drop_activate_obj(cms_zone_list[i],0);
		}
	}
	if(cms_receptable_type=="conteneur" ||  cms_dragable_type=="zone"){
		for(var i=0;i<cms_contener_list.length;i++){
			if(opac.document.getElementById(cms_contener_list[i])) cms_drop_activate_obj(cms_contener_list[i],actif);
		}
	}else{
		for(var i=0;i<cms_contener_list.length;i++){
			if(opac.document.getElementById(cms_contener_list[i])) cms_drop_activate_obj(cms_contener_list[i],0);
		}
	}
	cms_init_drag();
	cms_deplacement_activate();
}

function serialize (txt) {
	switch(typeof(txt)){
	case 'string':
		return 's:'+txt.length+':\"'+txt+'\";';
	case 'number':
		if(txt>=0 && String(txt).indexOf('.') == -1 && txt < 65536) return 'i:'+txt+';';
		return 'd:'+txt+';';
	case 'boolean':
		return 'b:'+( (txt)?'1':'0' )+';';
	case 'object':
		var i=0,k,ret='';
		for(k in txt){
			//alert(isNaN(k));
			if(!isNaN(k)) k = Number(k);
			ret += serialize(k)+serialize(txt[k]);
			i++;
		}
		return 'a:'+i+':{'+ret+'}';
	default:
		return 'N;';
		alert('var undefined: '+typeof(txt));return undefined;
	}
}

function cms_drag_record() {
	cms_desel_all_obj();

	var page_info=new Array();
	page_info['cms_nodes']=new Array();


	var opac=parent.frames['opac_frame'];
	var contener_name=cms_contener_list[0];
	page_info['cms_nodes'][0]=new Array();
	var nb_zone=0;
	var zone_index=new Array();
	var contener=opac.document.getElementById(contener_name);
	var zone_name="";
	for(var cpt_zone=0;cpt_zone<contener.childNodes.length;cpt_zone++){
		if(contener.childNodes[cpt_zone].nodeType == 1){
			zone_name=contener.childNodes[cpt_zone].getAttribute("id")
			if(zone_name && zone_name!='cms_build_info'){
				var obj_zone=opac.document.getElementById(zone_name);
				page_info['cms_nodes'][0][nb_zone]=new Array();
				page_info['cms_nodes'][0][nb_zone]['name']=zone_name;
				if(obj_zone.style.cssText){
					page_info['cms_nodes'][0][nb_zone]['style']=obj_zone.style.cssText;
				}
				zone_index[zone_name]=nb_zone;
				nb_zone++;
			}
		}
	}

	var index=0;
	for(var i=0;i<cms_zone_list.length;i++){
		var zone_name=cms_zone_list[i]; // bandeau ...
		var obj_zone=opac.document.getElementById(zone_name); // objet element

		if(obj_zone){
			// zone pr�sente dans la page

			if(zone_index[zone_name]!=null){
				index=zone_index[zone_name];
			}else{
				index=nb_zone;
				page_info['cms_nodes'][0][index]=new Array();
				page_info['cms_nodes'][0][index]['name']=zone_name;
			}

			page_info['cms_nodes'][0][index]['parent']=obj_zone.parentNode.id;

			if(obj_zone.style.cssText){
				page_info['cms_nodes'][0][index]['style']=obj_zone.style.cssText;
			}
			// pour tout les cadres dans la zone
			page_info['cms_nodes'][0][index]['childs']=new Array();
			var nb_cadre=0;
			for(var j=0;j<obj_zone.childNodes.length;j++){
				if(obj_zone.childNodes[j].nodeType == 1){
					if(obj_zone.childNodes[j].getAttribute("id")){
						var cadre_name=obj_zone.childNodes[j].getAttribute("id"); // Acceuil, Adresse ...
						var obj_cadre=obj_zone.childNodes[j]; // objet element
						
						if (obj_zone.childNodes[j].getAttribute("data-fixed")) {
							var cadre_fixed = obj_zone.childNodes[j].getAttribute("data-fixed");
						} else {
							var cadre_fixed = obj_zone.childNodes[j].getAttribute("fixed");
						}

						page_info['cms_nodes'][0][index]['childs'][nb_cadre]=new Array();
						page_info['cms_nodes'][0][index]['childs'][nb_cadre]['name']=cadre_name;
						page_info['cms_nodes'][0][index]['childs'][nb_cadre]['fixed']=(cadre_fixed ? true : false);

						if(obj_zone.childNodes[j].getAttribute("data-type")=="cms_module_hidden"){
							page_info['cms_nodes'][0][index]['childs'][nb_cadre]['style']=obj_zone.childNodes[j].getAttribute("data-cadre-style");
						}
						else if(obj_cadre.style.cssText){
							page_info['cms_nodes'][0][index]['childs'][nb_cadre]['style']=obj_cadre.style.cssText;
						}

						// insertion d'un <div class raw
						var div_name='add_div_'+cadre_name;
				    	var obj_div=parent.frames['opac_frame'].document.getElementById(div_name);
				    	if(obj_div){
				    		page_info['cms_nodes'][0][index]['childs'][nb_cadre]['build_div']=1;
				    	}

				    	nb_cadre++;
					}
				}
			}
			if(!zone_index[zone_name])nb_zone++;
		}
	}
	//console.log('page_info',page_info);

	// Contexte de la page Opac: cms_build_info
	var cms_build_info = parent.frames['opac_frame'].document.getElementById('cms_build_info');
	var cms_build_info_value = "";
	if (cms_build_info) {
		cms_build_info_value = cms_build_info.value;
	}
	var post_data='cms_data='+serialize(page_info)+'&cms_build_info='+cms_build_info_value;
	// Envoi du tout au serveur
	var http=new http_request();
	var url = './ajax.php?module=cms&categ=build&sub=block&action=save';
	http.request(url,true,post_data);
	return http.get_text();
}

function get_cadres_list(){
	var opac=parent.frames['opac_frame'];
	cadre_list=new Array();
	var nb_cadre=0;
	for(var i=0;i<cms_zone_list.length;i++){
		var zone_name=cms_zone_list[i]; // bandeau ...
		var obj_zone=opac.document.getElementById(zone_name); // objet element
		if(obj_zone){
			// zone pr�sente dans la page
			for(var j=0;j<obj_zone.childNodes.length;j++){
				if(obj_zone.childNodes[j].nodeType == 1){
					if(obj_zone.childNodes[j].getAttribute("id")){
						if(obj_zone.childNodes[j].getAttribute("data-type")=="cms_module_hidden") continue;
						var cadre_name=obj_zone.childNodes[j].getAttribute("id"); // Acceuil, Adresse ...
						cadre_list[nb_cadre++]=cadre_name;
					}
				}
			}
		}
	}
	return cadre_list;
}

function get_frbr_cadres_list(){
	var opac=parent.frames['opac_frame'];
	frbr_cadres_list=new Array();
	var nodes = opac.document.querySelectorAll('*[data-frbr-cadre]');
	for (var i = 0; i < nodes.length; i++) {
		frbr_cadres_list.push(nodes[i].getAttribute("data-frbr-cadre"));
	}
	return frbr_cadres_list;
}

function cms_build_new_cadre(name,contens){
	cadre =  parent.frames['opac_frame'].document.getElementById(name);
	if(cadre){
		cms_show_obj(name);
	}else{
		new_cadre=parent.frames['opac_frame'].document.createElement("div");
		new_cadre.setAttribute("id",name);
		//new_cadre.setAttribute('handler',targ.getAttribute("handler"));
		new_cadre.className="dragged cms_drag";
		new_cadre.setAttribute('draggable', 'yes');
		new_cadre.setAttribute('dragtype', 'opacdrop');
		new_cadre.setAttribute('oncontextmenu', 'return false');


		new_cadre.innerHTML=contens;
		new_cadre=parent.frames['opac_frame'].document.getElementById("main_hors_footer").appendChild(new_cadre);

		var main_hors_footer=parent.frames['opac_frame'].document.getElementById("main_hors_footer");
		if(main_hors_footer.firstChild) main_hors_footer.insertBefore(new_cadre,main_hors_footer.firstChild);
		else main_hors_footer.appendChild(new_cadre);
		cms_build_obj_list_id[cms_build_obj_list_id.length] = name;
		cms_build_obj_list_type[cms_build_obj_list_id.length]=new_cadre;
		new_cadre.style.visibility="visible";
	}
}


function cms_build_load_cadres_in_page_list(){
	var http=new http_request();

	var url = './ajax.php?module=cms&categ=module&action=cadres_list_in_page';
	var opac=parent.frames['opac_frame'];
	for(var i=0;i<cadre_list.length;i++){

		if(opac.document.getElementById(cadre_list[i])){
			url+='&in_page[]='+cadre_list[i];
		}
	}
	http.request(url);
	return http.get_text();
}

function cms_build_load_cadres_not_in_page_list(){
	var http=new http_request();

	var url = './ajax.php?module=cms&categ=module&action=cadres_list_not_in_page';
	var opac=parent.frames['opac_frame'];
	for(var i=0;i<cadre_list.length;i++){
		if(opac.document.getElementById(cadre_list[i])){
			url+='&in_page[]='+cadre_list[i];
		}
	}
	http.request(url);
	return http.get_text();
}

function cms_build_load_cadres_not_in_cms_list(){
	var http=new http_request();

	var url = './ajax.php?module=cms&categ=module&action=cadres_list_not_in_cms';
	var opac=parent.frames['opac_frame'];
	for(var i=0;i<cadre_list.length;i++){
		if(opac.document.getElementById(cadre_list[i])){
			url+='&in_page[]='+cadre_list[i];
		}
	}
	http.request(url);
	return http.get_text();
}

function cms_build_load_frbr_cadres_in_page_list(){
	//on recalcule le liste des cadres de la page
	get_frbr_cadres_list();
	
	var http=new http_request();

	var url = './ajax.php?module=cms&categ=module&action=frbr_cadres_list_in_page';
	for(var i=0;i<frbr_cadres_list.length;i++){
		url+='&in_page[]='+frbr_cadres_list[i];
	}
	http.request(url);
	return http.get_text();
}

function cms_build_save_cadre_classement(id_cadre,classement){
	var http=new http_request();
	var url = './ajax.php?module=cms&categ=module&action=cadre_save_classement';
	url+='&id_cadre='+id_cadre;
	url+='&classement='+classement;
	http.request(url);
	return http.get_text();
}

function cms_build_unchain_cadre(id_cadre){
	var http=new http_request();
	var url = './ajax.php?module=cms&categ=module&action=unchain_cadre';
	url+='&id_cadre='+id_cadre;
	http.request(url);
	return http.get_text();
}

function cms_build_save_page_classement(id_page,classement){
	var http=new http_request();
	var url = './ajax.php?module=cms&categ=page&action=page_save_classement';
	url+='&id_page='+id_page;
	url+='&classement='+classement;
	http.request(url);
	return http.get_text();
}

function cms_build_init(){
}

/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_form.js,v 1.4 2014/12/18 16:40:05 dgoron Exp $ */


function cms_create_row(){
	var row = document.createElement('div');
	row.setAttribute('class','row');
	return row;
}

function cms_create_colonne(type,row){
	var colonne = document.createElement("div");
	if(!type){
		type = "-suite";
	}
	colonne.setAttribute("class","colonne"+type);
	if(row){
		row.appendChild(colonne);
	}else{
		return colonne;
	}
}

function cms_create_label(label,name){
	var elem = document.createElement("label");
	elem.innerHTML = label;
	elem.setAttribute("for",name.replace('[','').replace(']',''));
	return elem;
}

function cms_create_form_element(type,name,value,values){
	switch(type){
		case "select" :
			var elem = document.createElement("select");
			var i = 0;
			for(opt in values){
				var option = document.createElement("option");
				option.setAttribute('value',opt);
				var text = document.createTextNode(values[opt]);
				option.appendChild(text);
				elem.appendChild(option);
				if(opt == value){
					elem.selectedIndex = i;
				}
				i++;
			}
			elem.setAttribute("name",name);
			elem.setAttribute("id",name.replace('[','').replace(']',''));
			break;
		case "textarea" :
			var elem = document.createElement("textarea");
			elem.setAttribute("id",name.replace('[','').replace(']',''));
			elem.setAttribute("name",name);
			elem.value = value;
			break;
		case "hidden" :
		case "text" :
		default :
			var elem = document.createElement("input");
			elem.setAttribute("type",type);
			elem.setAttribute("id",name.replace('[','').replace(']',''));
			elem.setAttribute("name",name);
			elem.value = value;
			break;
	}
	return elem;
}

function cms_create_element(label,type,name,value,values){
	var row = cms_create_row();
	var col =cms_create_colonne("3");
	col.appendChild(cms_create_label(label,name));
	row.appendChild(col);
	var col2 = cms_create_colonne("");
	col2.appendChild(cms_create_form_element(type,name,value,values));
	row.appendChild(col2);
	return row;
}

function cms_create_button(id,name,callback){
	var button = document.createElement("input");
	button.setAttribute("type","button");
	button.setAttribute("class","bouton");
	button.setAttribute("name",name);
	button.setAttribute("id",id);
	button.value= name;
	if(callback){
		button.setAttribute("onclick",callback+"()");
	}
	return button;
}

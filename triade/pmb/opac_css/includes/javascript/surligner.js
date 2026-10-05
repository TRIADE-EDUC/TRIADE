// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: surligner.js,v 1.9 2024/04/26 13:06:45 jparis Exp $
var surlignage_reg_exp = {};

function surlignage_get_regexp(mot){
	if(typeof surlignage_reg_exp[mot] != "undefined"){
		return surlignage_reg_exp[mot];
	}
	surlignage_reg_exp[mot] = new RegExp(mot+' *','gi');
	return surlignage_reg_exp[mot];
}

function trouver_mots_f(obj,mot,couleur,litteral,onoff) {
	var i;
	var chaine;
	if (obj.hasChildNodes()) {
		var childs=new Array();
		childs=obj.childNodes;
		
		if (litteral != 0) {
			mot=remplacer_carac(reverse_html_entities(mot));
		}
		
		var reg_mot = surlignage_get_regexp(mot);	
		for (i=0; i<childs.length; i++) {
			if (childs[i].nodeType==3 && childs[i].data.trim() !== "") {
				if (litteral==0){
					chaine=childs[i].data.toLowerCase();
					chaine=remplacer_carac(chaine);
				} else {
					chaine=childs[i].data;
					chaine=remplacer_carac(chaine);
				}

				if (chaine.match(reg_mot)) {
					var elt_found = chaine.match(reg_mot);
					var chaine_display = childs[i].data;
					var reg = 0;
					for(var k=0;k<elt_found.length;k++){
						reg = chaine.indexOf(elt_found[k],reg); 
						if (onoff==1) {
							after_shave=chaine_display.substring(reg+elt_found[k].length);
							sp=document.createElement('mark');
							if (couleur % 6!=0) {
								sp.className='text_search'+couleur;
							} else {
								sp.className='text_search0';
							}
							nmot=document.createTextNode(chaine_display.substring(reg,reg+elt_found[k].length));
							childs[i].data=chaine_display.substring(0,reg);
							sp.appendChild(nmot);
						
							if (after_shave) {
								var aftern=document.createTextNode(after_shave);
							} else var aftern='';
						
							if (i<childs.length-1) {
								obj.insertBefore(sp,childs[i+1]);
								if (aftern) { obj.insertBefore(aftern,childs[i+2]); }
							} else {
								obj.appendChild(sp);
								if (aftern) obj.appendChild(aftern);
							}
							chaine_display ='';
							i++;
						} else {
							obj.replaceChild(childs[i],obj);
						}
					}
				}
			} else if (childs[i].nodeType==1 && (childs[i].nodeName != "SCRIPT" && childs[i].nodeName != "IMG")){
				var highlight = childs[i].getAttribute('data-highlight');
				if(!highlight || (highlight && highlight != 'off')) {
					trouver_mots_f(childs[i],mot,couleur,litteral,onoff);
				}
			}
		}
	}
}

function rechercher(onoff) {
	obj=document.getElementById('res_first_page');
	if (!obj) {
		obj=document.getElementById('resultatrech_liste');
		if(obj) if (obj.getElementsByTagName('blockquote')[0]) {
			obj=obj.getElementsByTagName('blockquote')[0];
		}
	}
	if (obj) {
		if (terms_litteraux[0]!='')
		{
			for (var i=0; i<terms_litteraux.length; i++) {
				trouver_mots_f(obj,terms_litteraux[i],i+terms.length,1,onoff);			
			}
		}
		if (terms[0]!='')
		{
			for (var i=0; i<terms.length; i++) {
				trouver_mots_f(obj,terms[i],i,0,onoff);			
			}
		}
	}
}

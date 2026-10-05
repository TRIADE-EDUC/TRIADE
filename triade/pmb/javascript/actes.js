// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: actes.js,v 1.32 2020/07/07 07:06:43 dgoron Exp $


/*
variables a d�clarer dans le formulaire appelant:

 msg_parcourir					//valeur bouton parcourir
 msg raz						//valeur bouton suppression
 msg_no_fou						//message si pas de fournisseur d�fini
 msg_act_vide					//message si acte vide
 acquisition_budget				//budget obligatoire ?
 msg_no_bud						//message si pas de budget d�fini
 msg_acquisition_comment_lg		//message commentaires gestion
 msg_acquisition_comment_lo		//message commentaires opac
 lgstat_sel						//selecteur statuts
 
 act_nblines			//nb de lignes
 act curline 			//n� de ligne courante
 
*/
try {
	var gestion_tva=document.getElementById('gestion_tva').value;
	var act_type=document.getElementById('act_type').value;
	var tot_ht=document.getElementById('tot_ht');
	var tot_tva=document.getElementById('tot_tva');
	var tot_ttc=document.getElementById('tot_ttc');
	var tot_expl=document.getElementById('tot_expl');
} catch(err) {}

var precision=2;
var mod=0;

var thresholds = null;
var current_threshold = 0;
var act_curline = 0;

//Ajout ligne acte
function act_addLine(id_cde) {

	var te=document.getElementById('act_tab');
	act_nblines++;
	act_curline++;
	
	var tr=act_addRow(act_curline);
	
	switch (act_type) {

		case '0' :	//Commande

			var td9=act_addPlusCell(act_curline);
			tr.appendChild(td9);
			var td1=act_addCodeCell(act_curline);
			tr.appendChild(td1);
			var td2=act_addTitleCell(act_curline);
			tr.appendChild(td2);			
			var td3=act_addQtyCell(act_curline);
			tr.appendChild(td3);			
			var td4=act_addPriceCell(act_curline);
			tr.appendChild(td4);			
			var td5=act_addTypeCell(act_curline);
			tr.appendChild(td5);	
			var td6=act_addRubriqueCell(act_curline);
			tr.appendChild(td6);
			var td8=act_addStatutCell(act_curline);
			tr.appendChild(td8);
			var td7=act_addActionCell(act_curline);
			tr.appendChild(td7);
			
			var tr1=act_addCommentRow(act_curline);

			te.appendChild(tr);	
			te.appendChild(tr1);
			
			act_getPreviousType(tr);
			act_getPreviousRubrique(tr);
			
			var typeLine = document.getElementById('typ_lig['+act_curline+']').value;
			var showApplicants = true;
			if(typeLine != 1 && typeLine!=2){
				showApplicants = false;
			}
			
			var tr_applicants = add_applicantsRow(act_curline, showApplicants);
			var tableComment = document.getElementById('C_'+act_curline+'_Child').querySelector('table');
			tableComment.appendChild(tr_applicants);
			add_applicant_line('C_'+act_curline+'_applicants_container', true);
			
			if(act_nblines==1){
				//Pour �viter le cas o� on est en modification de commande, mais pas de ligne d'acte
				if(id_cde==0){
					ajax_parse_dom();
				}
			}else{
				ajax_pack_element(document.getElementById("lib_rub["+act_curline+"]"));
				ajax_pack_element(document.getElementById("lib_typ["+act_curline+"]"));
			}
			td1.firstChild.focus();
			break;

		case '1' :	//Devis

			var td1=act_addCodeCell(act_curline);
			tr.appendChild(td1);
			var td2=act_addTitleCell(act_curline);
			tr.appendChild(td2);			
			var td3=act_addQtyCell(act_curline);
			tr.appendChild(td3);			
			var td4=act_addPriceCell(act_curline);
			tr.appendChild(td4);			
			var td5=act_addTypeCell(act_curline);
			tr.appendChild(td5);			
			var td6=act_addActionCell(act_curline);
			tr.appendChild(td6);			
			te.appendChild(tr);
			act_getPreviousType(tr);
			document.getElementById('bt_add_line').focus();
			td1.firstChild.focus();
			break;
		
		default :
		break;
	}
	act_calc();
}

//Ajout ligne
function act_addRow(lig){

	var tr=document.createElement('TR');
	tr.setAttribute('id','R_'+lig);
	tr.setAttribute('class','act_typ_lig_0');
	return tr;
}


//Ajout cellule plus
function act_addPlusCell(lig) {
	
	var td=document.createElement('TD');
	td.style.overflow='visible';
	var i=document.createElement('IMG');
	i.setAttribute('src',pmb_img_plus);
	i.setAttribute('id','C_'+lig+'_Img');
	i.setAttribute('name','C_'+lig+'_Img');
	i.setAttribute('tabindex','1');
	i.className='act_cell_img_plus';
	i.onclick=function(){expandRow('C_'+lig+'_', true);};
	td.appendChild(i);
	return td;
}

//Ajout cellule code
function act_addCodeCell(lig) {
	
	var td=document.createElement('TD');
	var i=document.createElement('INPUT');
	i.setAttribute('type','text');
	i.setAttribute('id','code['+lig+']');
	i.setAttribute('name','code['+lig+']');
	i.setAttribute('tabindex','1');
	i.className='in_cell';
	i.setAttribute('value','');
	td.appendChild(i);
	
	var b=document.createElement('INPUT');
	b.setAttribute('type','button');
	b.setAttribute('tabindex','1');
	b.className='bouton_small';
	b.style.width='20px';
	b.setAttribute('value', msg_parcourir);
	b.onclick=function(){act_getCode(this);};
	td.appendChild(b);

	var b1=document.createElement('INPUT');
	b1.setAttribute('type','button');
	b1.setAttribute('tabindex','1');
	b1.className='bouton_small';
	b1.style.width='20px';
	b1.setAttribute('value', msg_raz);
	b1.onclick=function(){act_delCode(this);};
	td.appendChild(b1);
	
	return td;
}

//Ouverture du popup de recherche de notice/bulletin/frais/abonnements
function act_getCode(elt) {
	
	var cr=elt.parentNode.parentNode.getAttribute('id').substring(2);
	var code=document.forms['act_modif'].elements['code['+cr+']'].value;
	var lib=document.forms['act_modif'].elements['lib['+cr+']'].value;
	var typ_lig=document.forms['act_modif'].elements['typ_lig['+cr+']'].value; 
	var deb_rech='';
	if (code!='') {
		deb_rech=code;
	} else{
		if (lib!=''){
			deb_rech= lib;
		}
	}
	var typ_query='notice';
	switch(typ_lig) {
		case '2' :
			typ_query='bulletin';
			break;
		case '3' :
			typ_query='frais';
			break;
		case '4' :
			typ_query='abt';
			break;
		case '5' :
			typ_query='article';
			break;
	}
	mod=1;
	openPopUp("select.php?what=acquisition_notice&caller=act_modif&cr="+cr+"&deb_rech="+encode_URL(deb_rech)+"&typ_query="+encode_URL(typ_query)+"&callback=selectionCallback" , 'select_notice', 1024, 300, 0, 0, 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes');
	return false;
}

function act_delCode(elt) {

	var cr=elt.parentNode.parentNode.getAttribute('id').substring(2);
	document.getElementById('code['+cr+']').value='';
	document.getElementById('lib['+cr+']').value='';
	document.getElementById('typ_lig['+cr+']').value='0';
	document.getElementById('id_prod['+cr+']').value='0';
	document.getElementById('id_sug['+cr+']').value='0';
	document.getElementById('prix['+cr+']').value='0.00';
	thresholds_notification();
	return false;
}

//Ajout cellule titre
function act_addTitleCell(lig) {
	
	var td=document.createElement('TD');
	var i=document.createElement('TEXTAREA');
	i.setAttribute('id','lib['+lig+']');
	i.setAttribute('name','lib['+lig+']');
	i.setAttribute('tabindex','1');
	i.className='in_cell';
	i.setAttribute('rows','3');
	i.setAttribute('wrap','virtual');
	td.appendChild(i);
	return td;
}

//Ajout cellule quantite
function act_addQtyCell(lig) {

	var td=document.createElement('TD');
	var i=document.createElement('INPUT');
	i.setAttribute('type','text');
	i.setAttribute('id','qte['+lig+']');
	i.setAttribute('name','qte['+lig+']');
	i.setAttribute('tabindex','1');
	i.className='in_cell_nb';
	i.setAttribute('value','1');
	i.onchange=function() {
		thresholds_notification();
	}
	td.appendChild(i);
	return td;
}

//Ajout cellule prix
function act_addPriceCell(lig) {
	
	var td=document.createElement('TD');
	var i=document.createElement('INPUT');
	i.setAttribute('type','text');
	i.setAttribute('id','prix['+lig+']');
	i.setAttribute('name','prix['+lig+']');
	i.setAttribute('tabindex','1');
	i.className='in_cell_nb';
	i.setAttribute('value','0');
	td.appendChild(i);
	if(gestion_tva>0){
        var spanTag = document.createElement('span');     
        spanTag.id = 'convert_ht_ttc_'+lig;       
        spanTag.className ='convert_ht_ttc';
        var obj='input_convert_ht_ttc_'+lig;
        spanTag.onclick=function() { 
        	document.getElementById(obj).value='';
        	document.getElementById(obj).style.visibility='visible';
        	document.getElementById(obj).focus();
        };
      
        spanTag.innerHTML = '0';
        td.appendChild(spanTag);
        
        var i_convert = document.createElement('INPUT');      
        i_convert.type='text';
        i_convert.id='input_convert_ht_ttc_'+lig;
        i_convert.name='input_convert_ht_ttc_'+lig;
        i_convert.style.visibility='hidden';
        i_convert.value='0'; 
        var obj='input_convert_ht_ttc_'+lig;
        i_convert.onblur=function() { 
        	document.getElementById(obj).style.visibility='hidden';
        }
        var obj_prix='prix['+lig+']';
        var obj_tva='tva['+lig+']';
        var obj_convert= 'convert_ht_ttc_'+lig;		
			
		if(gestion_tva==1){	
	        i_convert.onchange=function() { 
	        	document.getElementById(obj_prix).value = ttc_to_ht(document.getElementById(obj).value,document.getElementById(obj_tva).value);
	        	document.getElementById(obj).style.visibility='hidden'; 
	    		document.getElementById(obj_convert).innerHTML=document.getElementById(obj).value;
	    		thresholds_notification();
	        }		      
	        i.onchange=function() { 
	        	document.getElementById(obj_convert).innerHTML = ht_to_ttc(document.getElementById(obj_prix).value,document.getElementById(obj_tva).value);
	        	thresholds_notification();
	        }
			
		}else if(gestion_tva==2){		
	        i_convert.onchange=function() { 
	        	document.getElementById(obj_prix).value = ht_to_ttc(document.getElementById(obj).value,document.getElementById(obj_tva).value);
	        	document.getElementById(obj).style.visibility='hidden'; 
	    		document.getElementById(obj_convert).innerHTML=document.getElementById(obj).value;
	    		thresholds_notification();
	        }		      
	        i.onchange=function() { 
	        	document.getElementById(obj_convert).innerHTML = ttc_to_ht(document.getElementById(obj_prix).value,document.getElementById(obj_tva).value);
	        	thresholds_notification();
	        }
		} 
        td.appendChild(i_convert);   
	}

	return td;
}

//Ajout cellule type
function act_addTypeCell(lig) {

	var td=document.createElement('TD');
	var i=document.createElement('INPUT');
	i.setAttribute('type','hidden');
	i.setAttribute('id','typ['+lig+']');
	i.setAttribute('name','typ['+lig+']');
	i.setAttribute('value','0');
	td.appendChild(i);
	
	var i1=document.createElement('INPUT');
	i1.setAttribute('type','text');
	i1.setAttribute('id','lib_typ['+lig+']');
	i1.setAttribute('name','lib_typ['+lig+']');
	i1.setAttribute('tabindex','1');
	i1.setAttribute('completion','types_produits');
	i1.setAttribute('linkfield','id_fou');
	i1.setAttribute('autfield','typ['+lig+']');
	i1.setAttribute('autocomplete','off');
	i1.setAttribute('callback','callBackTypeProduit');
	i1.className='in_cell_ro';
	i1.setAttribute('value','');
	td.appendChild(i1);

	var b=document.createElement('INPUT');
	b.setAttribute('type','button');
	b.setAttribute('tabindex','1');
	b.className='in_cell_ro';
	b.className='bouton_small';
	b.style.width='20px';
	b.setAttribute('value', msg_parcourir);
	b.onclick=function() {act_getType(this);};
	td.appendChild(b);

	var b1=document.createElement('INPUT');
	b1.setAttribute('type','button');
	b1.setAttribute('tabindex','1');
	b1.className='bouton_small';
	b1.style.width='20px';
	b1.setAttribute('value', msg_raz);
	b1.onclick=function(){act_delType(this);};
	td.appendChild(b1);
	switch (gestion_tva) {
		case '1' :
		case '2' :
			var s=document.createTextNode(' ');
			td.appendChild(s);
			var i2=document.createElement('INPUT');
			i2.setAttribute('type','text');
			i2.setAttribute('id', 'tva['+lig+']');
			i2.setAttribute('name', 'tva['+lig+']');
			i2.setAttribute('tabindex','1');
			i2.className='in_cell_nb';
			i2.style.width='20%';
			i2.setAttribute('value', '0');
			if(gestion_tva==1){
				i2.onchange=function() { 
		        	document.getElementById('convert_ht_ttc_'+lig).innerHTML = ht_to_ttc(document.getElementById('prix['+lig+']').value,document.getElementById('tva['+lig+']').value);
		        	thresholds_notification();
		        }
			} else {
				i2.onchange=function() { 
		        	document.getElementById('convert_ht_ttc_'+lig).innerHTML = ttc_to_ht(document.getElementById('prix['+lig+']').value,document.getElementById('tva['+lig+']').value);
		        	thresholds_notification();
		        }
			}	
			td.appendChild(i2);
			var n=document.createTextNode(' %');
			td.appendChild(n);
			break;
		default:
			break;
	}

	var s1=document.createTextNode(' ');
	td.appendChild(s1);
	var i3=document.createElement('INPUT');
	i3.setAttribute('type','text');
	i3.setAttribute('id', 'rem['+lig+']');
	i3.setAttribute('name', 'rem['+lig+']');
	i3.setAttribute('tabindex','1');
	i3.className='in_cell_nb';
	i3.style.width='20%';
	i3.setAttribute('value', '0');
	i3.onchange=function() {
		thresholds_notification();
	}
	td.appendChild(i3);
	var n1=document.createTextNode(' %');
	td.appendChild(n1);
	return td;
}

//Ouverture du popup de recherche de type de produit
function act_getType(elt) {

	if('sel_typ_for_checked' == elt.id) {
		openPopUp("select.php?what=types_produits&caller=act_modif&param1=typ_for_checked&param2=lib_typ_for_checked&param3=rem_for_checked&param4=tva_for_checked&id_fou="+document.getElementById('id_fou').value+"&close=1", 'select_notice', 400, 400, -2, -2, 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes');
	} else {
		var cr=elt.parentNode.parentNode.getAttribute('id').substring(2);
		openPopUp("select.php?what=types_produits&caller=act_modif&param1=typ["+cr+"]&param2=lib_typ["+cr+"]&param3=rem["+cr+"]&param4=tva["+cr+"]&param5="+cr+"&id_fou="+document.getElementById('id_fou').value+"&close=1&callback=thresholds_notification", 'select_notice', 400, 400, -2, -2, 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes');
	}
	return false;
}

function act_delType(elt) {

	if('del_typ_for_checked' == elt.id) {		
		document.getElementById('typ_for_checked').value='0';
		document.getElementById('lib_typ_for_checked').value='';
		if(document.getElementById('tva_for_checked')) {
			document.getElementById('tva_for_checked').value='0.00';
		}
		document.getElementById('rem_for_checked').value='0.00';
	} else {
		var cr=elt.parentNode.parentNode.getAttribute('id').substring(2);
		document.getElementById('typ['+cr+']').value='0';
		document.getElementById('lib_typ['+cr+']').value='';
		if (gestion_tva!=0) {
			document.getElementById('tva['+cr+']').value='0.00';
		}
		document.getElementById('rem['+cr+']').value='0.00';
		if (document.getElementById('convert_ht_ttc_'+cr)) document.getElementById('convert_ht_ttc_'+cr).innerHTML=document.getElementById('prix['+cr+']').value;
		act_calc();
		thresholds_notification();
	}
	return false;
}

function act_getPreviousType(tr) {
	var previous = 2; // commande
	if (act_type == 1) { // devis
		previous = 1;
	}	
	var i = tr.rowIndex;
	if (i < previous) return false;
	var te = tr.parentNode;
	var cr = tr.getAttribute('id').substring(2);
	if (!te.rows[i - previous].getAttribute('id')) return false;
	var pr = te.rows[i -previous].getAttribute('id').substring(2);
	document.getElementById('typ['+cr+']').value = document.getElementById('typ['+pr+']').value;
	document.getElementById('lib_typ['+cr+']').value = document.getElementById('lib_typ['+pr+']').value;
	try {
		document.getElementById('tva['+cr+']').value = document.getElementById('tva['+pr+']').value;
	} catch(err){}
	document.getElementById('rem['+cr+']').value = document.getElementById('rem['+pr+']').value;
	return false;
}

//Ajout cellule rubrique budgetaire
function act_addRubriqueCell(lig) {

	var td=document.createElement('TD');
	var i=document.createElement('INPUT');
	i.setAttribute('type','hidden');
	i.setAttribute('id','rub['+lig+']');
	i.setAttribute('name','rub['+lig+']');
	i.setAttribute('value','0');
	td.appendChild(i);
	
	var i1=document.createElement('INPUT');
	i1.setAttribute('type','text');
	i1.setAttribute('id','lib_rub['+lig+']');
	i1.setAttribute('name','lib_rub['+lig+']');
	i1.setAttribute('tabindex','1');
	i1.setAttribute('completion','rubriques');
	i1.setAttribute('param1',document.getElementById('id_bibli').value);
	i1.setAttribute('param2',document.getElementById('id_exer').value);
	i1.setAttribute('autfield','rub['+lig+']');
	i1.setAttribute('autocomplete','off');
	i1.className='in_cell_ro';
	i1.setAttribute('value','');
	td.appendChild(i1);
	
	var b=document.createElement('INPUT');
	b.setAttribute('type','button');
	b.setAttribute('tabindex','1');
	b.className='bouton_small';
	b.style.width='20px';
	b.setAttribute('value', msg_parcourir);
	b.onclick=function(){act_getRubrique(this);};
	td.appendChild(b);

	var b1=document.createElement('INPUT');
	b1.setAttribute('type','button');
	b1.setAttribute('tabindex','1');
	b1.className='bouton_small';
	b1.style.width='20px';
	b1.setAttribute('value', msg_raz);
	b1.onclick=function(){act_delRubrique(this);};
	td.appendChild(b1);
	
	if(gestion_tva>0){
	    var force_ht_ttc='force_ht_ttc_'+lig;
	    var force_debit='force_debit['+lig+']';	
	
        var spanTag = document.createElement('span');     
        spanTag.id = force_ht_ttc;  
        spanTag.className ='force_ht_ttc';	
        
		var force_tva=document.createElement('INPUT');
		force_tva.type='hidden';
		force_tva.id=force_debit;
		force_tva.name=force_debit;
		
		if(gestion_tva==1){
			force_tva.value=1;	
	        spanTag.onclick=function() { 
	        	if(document.getElementById(force_debit).value==2){
	        		document.getElementById(force_ht_ttc).innerHTML='&nbsp;'+acquisition_force_ht;
	        		document.getElementById(force_debit).value=1;
	        	}else{				
	        		document.getElementById(force_ht_ttc).innerHTML='&nbsp;'+acquisition_force_ttc;
	        		document.getElementById(force_debit).value=2;
	        	}	
	        };			
		}else if(gestion_tva==2){
			force_tva.value=2;	
	        spanTag.onclick=function() { 
	        	if(document.getElementById(force_debit).value==2){
	        		document.getElementById(force_ht_ttc).innerHTML='&nbsp;'+acquisition_force_ht;
	        		document.getElementById(force_debit).value=1;
	        	}else{				
	        		document.getElementById(force_ht_ttc).innerHTML='&nbsp;'+acquisition_force_ttc;
	        		document.getElementById(force_debit).value=2;
	        	}	
	        };
		}  
		td.appendChild(force_tva);
	    spanTag.innerHTML = '&nbsp;'+acquisition_force_ttc;
    	td.appendChild(spanTag);
	}    
	return td;
}

//Ouverture du popup de recherche de rubrique budgetaire
function act_getRubrique(elt) {

	if('sel_rub_for_checked' == elt.id) {
		openPopUp("select.php?what=rubriques&caller=act_modif&param1=rub_for_checked&param2=lib_rub_for_checked&id_bibli="+document.getElementById('id_bibli').value+"&id_exer="+document.getElementById('id_exer').value+"&close=1", 'select_notice', 600, 500, -2, -2, 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes');
	} else {
		var cr=elt.parentNode.parentNode.getAttribute('id').substring(2);
		openPopUp("select.php?what=rubriques&caller=act_modif&param1=rub["+cr+"]&param2=lib_rub["+cr+"]&id_bibli="+document.getElementById('id_bibli').value+"&id_exer="+document.getElementById('id_exer').value+"&close=1", 'select_notice', 600, 500, -2, -2, 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes');
	}
	return false;
}

function act_delRubrique(elt) {

	if('del_rub_for_checked' == elt.id) {
		document.getElementById('rub_for_checked').value='0';
		document.getElementById('lib_rub_for_checked').value='';
	} else {
		var cr=elt.parentNode.parentNode.getAttribute('id').substring(2);
		document.getElementById('rub['+cr+']').value='0';
		document.getElementById('lib_rub['+cr+']').value='';
	}
	return false;
}

function act_getPreviousRubrique(tr) {
	
	var i=tr.rowIndex;
	if (i<2) return false;
	var te = tr.parentNode;
	var cr = tr.getAttribute('id').substring(2);
	var pr = te.rows[i-2].getAttribute('id').substring(2);
	document.getElementById('rub['+cr+']').value=document.getElementById('rub['+pr+']').value;
	document.getElementById('lib_rub['+cr+']').value=document.getElementById('lib_rub['+pr+']').value;
	return false;
}

//Ajout cellule action
function act_addActionCell(lig) {

	var td=document.createElement('TD');
	td.style.overflow='visible';
	var i=document.createElement('INPUT');
	i.setAttribute('type', 'checkbox');
	i.setAttribute('id','chk['+lig+']');
	i.setAttribute('name','chk['+lig+']');
	i.setAttribute('tabindex','1');
	i.setAttribute('value','1');
	i.className='act_cell_chkbox2';
	td.appendChild(i);
	
	var i1=document.createElement('INPUT');
	i1.setAttribute('type', 'hidden');
	i1.setAttribute('id','id_sug['+lig+']');
	i1.setAttribute('name','id_sug['+lig+']');
	i1.setAttribute('value','0');
	td.appendChild(i1);
	
	var i2=document.createElement('INPUT');
	i2.setAttribute('type', 'hidden');
	i2.setAttribute('id','id_lig['+lig+']');
	i2.setAttribute('name','id_lig['+lig+']');
	i2.setAttribute('value','0');
	td.appendChild(i2);

	var i3=document.createElement('INPUT');
	i3.setAttribute('type', 'hidden');
	i3.setAttribute('id','typ_lig['+lig+']');
	i3.setAttribute('name','typ_lig['+lig+']');
	i3.setAttribute('value','0');
	td.appendChild(i3);

	var i4=document.createElement('INPUT');
	i4.setAttribute('type', 'hidden');
	i4.setAttribute('id','id_prod['+lig+']');
	i4.setAttribute('name','id_prod['+lig+']');
	i4.setAttribute('value','0');
	td.appendChild(i4);
	return td;
}

//Ajout cellule statut
function act_addStatutCell(lig) {

	var td=document.createElement('TD');
	var s=new String(lgstat_sel);
	s=s.replace(/!!lig!!/g,lig);
	td.innerHTML=s;
	return td;
}


//Ajout ligne commentaires
function act_addCommentRow(lig){

	var tr=document.createElement('TR');
	tr.setAttribute('id','C_'+lig+'_Child');
	tr.style.display='none';
	tr.className='act_cell_comments';
	var td=document.createElement('TD');
	td.setAttribute('colspan','9');
	tr.appendChild(td);
	
	var tab=document.createElement('TABLE');
	td.appendChild(tab);
	
	var tr1=document.createElement('TR');
	
	var td1=document.createElement('TD');
	td1.setAttribute('width','10%');
	var n1=document.createTextNode(msg_acquisition_comment_lg);
	td1.appendChild(n1);
	tr1.appendChild(td1);
	
	var td2=document.createElement('TD');
	td2.setAttribute('width','40%');
	var i2=document.createElement('TEXTAREA');
	i2.setAttribute('id','comment_lg['+lig+']');
	i2.setAttribute('name','comment_lg['+lig+']');
	i2.setAttribute('tabindex','1');
	i2.className='in_cell';
	i2.setAttribute('rows','1');
	i2.setAttribute('wrap','virtual');
	td2.appendChild(i2);
	tr1.appendChild(td2);
	
	var td3=document.createElement('TD');
	td3.setAttribute('width','10%');
	var n3=document.createTextNode(msg_acquisition_comment_lo);
	td3.appendChild(n3);
	tr1.appendChild(td3);
	
	var td4=document.createElement('TD');
	td4.setAttribute('width','40%');
	var i4=document.createElement('TEXTAREA');
	i4.setAttribute('id','comment_lo['+lig+']');
	i4.setAttribute('name','comment_lo['+lig+']');
	i4.setAttribute('tabindex','1');
	i4.className='in_cell';
	i4.setAttribute('rows','1');
	i4.setAttribute('wrap','virtual');
	td4.appendChild(i4);
	tr1.appendChild(td4);

	tab.appendChild(tr1);
	
	return tr;
}

function add_applicantsRow(lig, show){
	var tr = document.createElement('tr');
	if(!show){
		tr.setAttribute('style', 'display:none');	
	}
	
	var tdTitle = document.createElement('td');
	tdTitle.setAttribute('colspan', '2');
	
	var n1=document.createTextNode(msg_acquisition_applicants);
	tdTitle.appendChild(n1);
	
	var tdContainer = document.createElement('td');
	tdContainer.setAttribute('id', 'C_'+lig+'_applicants_container');
	tdContainer.setAttribute('colspan', '2');
	
	tr.appendChild(tdTitle);
	tr.appendChild(tdContainer);
	return tr;
	
}


//Suppression lignes cochees
function act_delLines(){

	var n=act_curline;
	var i,j,c,cr,tab;
	for (i=1;i<=n;i++) {
		c=document.getElementById('chk['+i+']');
		try {
			if(c.checked) {

				cr=document.getElementById('C_'+i+'_Child');
				if (cr) {
					j=cr.rowIndex;
					act_removeChilds(cr);
					tab=cr.parentNode;
					tab.deleteRow(j);
				}

				cr=document.getElementById('R_'+i);
				j=cr.rowIndex;
				act_removeChilds(cr);
				tab=cr.parentNode;
				tab.deleteRow(j);
				
				act_nblines--;
			}
		}catch(err) {}
	}
	act_calc();
	thresholds_notification();
	return false;
}

//Suppression noeuds fils
function act_removeChilds(elt) {

	while (elt.hasChildNodes()){
		act_removeChilds(elt.lastChild);
		elt.removeChild(elt.lastChild);
	}
}

//calcul du total
function act_calc(){

	act_clean();
	var mnt_ht=0;
	var mnt_ttc=0;
	var mnt_tva=0;
	var n=act_curline;
	var i,q,p,t,r;
	var qt=0; 
	var remise=0;
	for (i=1;i<=n;i++) {
		try {
			q=document.getElementById('qte['+i+']').value;
			qt=qt+(q*1);
			p=document.getElementById('prix['+i+']').value;
			if (gestion_tva!=0) {
				t=document.getElementById('tva['+i+']').value;
			}
			r=document.getElementById('rem['+i+']').value;

				switch(gestion_tva) {
					case '1' :
						mnt_ht=mnt_ht+(q*p*((100-r)/100));
						mnt_tva=mnt_tva+(q*p*((100-r)/100)*(t/100));
						break;
					case '2' :
						mnt_ttc=mnt_ttc+(q*p*((100-r)/100));
						
						mnt_ht=mnt_ht+((q*p*((100-r)/100))/(1+(t/100))) ;
						break;
					default :
						mnt_ttc=mnt_ttc+(q*p*((100-r)/100));
						break;
				}
			
		}catch(err) {}
	}
	switch(gestion_tva) {
	case '1' :
		tot_ht.value=mnt_ht.toFixed(precision);
		tot_tva.value=mnt_tva.toFixed(precision);
		tot_ttc.value=(mnt_ht+mnt_tva).toFixed(precision);
		break;
	case '2' :
		tot_ht.value=mnt_ht.toFixed(precision);
		tot_tva.value=(mnt_ttc-mnt_ht).toFixed(precision);
		tot_ttc.value=mnt_ttc.toFixed(precision);
		break;
	default :
		tot_ttc.value=mnt_ttc.toFixed(precision);
		break;
	}
	tot_expl.value=qt;
	
	return false;
}

//Nettoyage des valeurs en fonction de leur type
function act_clean() {

	if (gestion_tva!=0) {
		tot_ht.value='0';
		tot_tva.value='0';
	} 
	tot_ttc.value='0';
	var n=act_curline;
	var i;
	for (i=1;i<=n;i++) {
		try {
			val_clean(document.getElementById('qte['+i+']'), true,false);
			val_clean(document.getElementById('prix['+i+']'), false,true);
			if (gestion_tva!=0) {
				val_clean(document.getElementById('tva['+i+']'), false,false);
			}
			val_clean(document.getElementById('rem['+i+']'), false,true);
		}
		catch (err) {}
	}
	return false;
}


//Nettoyage des valeurs
function val_clean(x, int ,neg) {

	var v=x.value;
	v=v.replace(/,/g,".");
	if (neg) { 
		v=v.replace(/[^0-9|\.|-]/g,"");
	} else {
		v=v.replace(/[^0-9|\.]/g,"");
	}
	if (int) {
		x.value=new Number(v).toFixed(0);
	} else {
		x.value=new Number(v).toFixed(precision);
	}
	return false;
}


//Verification formulaire
function act_verif() {

	if (document.getElementById('id_fou').value==0) {
		alert(msg_no_fou);
		return false; 
	} 
	if (act_nblines<1) {
		alert(msg_act_vide); 
		return false;
	} 		
	var t,i,j,n,rub;
	if (acquisition_budget==1) {
		t=document.getElementById('act_tab').parentNode;
		for (i=1;i<t.rows.length;i++) {
			try {
				n=t.rows[i].getAttribute('id');
				if (n.substring(0,1)=='R') { 
					j=n.substring(2);
					rub=document.getElementById('rub['+j+']');
					if (rub.value==0) {
						alert(msg_no_bud);
						document.getElementById('lib_rub['+t.rows[i].getAttribute('id').substring(2)+']').focus();
						return false;
					}
				}
			} catch(err) {}
		} 
	}
	if (acquisition_type_produit==1) {
		t=document.getElementById('act_tab').parentNode;
		for (i=1;i<t.rows.length;i++) {
			try {
				n=t.rows[i].getAttribute('id');
				console.log('n', n);
				if (n.substring(0,1)=='R') { 
					j=n.substring(2);
					typ=document.getElementById('typ['+j+']');
					if (typ.value==0) {
						alert(msg_no_typ);
						document.getElementById('lib_typ['+t.rows[i].getAttribute('id').substring(2)+']').focus();
						return false;
					}
				}
			} catch(err) {}
		}
	}
	return true;
} 

//une fonction pout tout cocher/decocher
function act_switchCheck() {

	var n=act_curline;
	var i,c;
	for (i=1;i<=n;i++) {
		try {
			c=document.forms['act_modif'].elements['chk['+i+']'];
			c.checked = !c.checked;
		}catch(err) {}
	}
	return false;
}


//Pour verifier les doublons sur les commandes
function act_lineAlreadyExists(lig, id_prod, typ_lig) {
	if (typ_lig==0) return false;
	
	var t=document.getElementById('act_tab').parentNode;
	var i,j,n,x,y;
	for (i=1;i<t.rows.length;i++) {
		try {
			n=t.rows[i].getAttribute('id');
			if (n.substring(0,1)=='R') { 
				j=n.substring(2);		
				if (j!=lig) {
					x=document.getElementById('id_prod['+j+']').value;
					y=document.getElementById('typ_lig['+j+']').value;
			
					if (x==id_prod && y==typ_lig) {
						return j;
					}		
				}
			}
		}catch(err) {}
	}
	return false;
}

//Pour chercher la premiere ligne vide
function act_getEmptyLine() {

	var t=document.getElementById('act_tab').parentNode;
	var i,j,n,x,y,z;
	for (i=1;i<t.rows.length;i++) {
		try {
			n=t.rows[i].getAttribute('id');
			if (n.substring(0,1)=='R') { 
				j=n.substring(2);
	
				x=document.getElementById('code['+j+']').value;
				y=document.getElementById('lib['+j+']').value;
				z=document.getElementById('id_prod['+j+']').value;
				if (x=='' && y=='' && z==0) { 
					return j;
				}
			}
		}catch(err) {}
	}
	act_addLine();
	return act_curline;;
}

function ttc_to_ht(val,tva){
	return(Math.round((val / ((tva/100)+1))*100)/100);	
}
function ht_to_ttc(val,tva){
	val=val*1;
	return(Math.round((val+(val/100*tva))*100)/100);	
}

function selectionCallback(ligne_num){
	typeSwitched(ligne_num);
	thresholds_notification();
}

function thresholds_notification(init) {
	var mnt_ht=0;
	var mnt_ttc=0;
	var mnt_tva=0;
	var n=act_curline;
	var i,q,p,t,r;
	var qt=0;
	
	if (thresholds == null) {
		thresholds = get_thresholds();
	}
	if (!thresholds.length) {
		return false;
	}
	var thresholds_info = document.getElementById('thresholds_info');
	if (!thresholds_info) {
		thresholds_info = document.createElement('span');
		thresholds_info.setAttribute('id', 'thresholds_info');
		document.getElementById('devise').parentNode.appendChild(thresholds_info);
	}

	for (i=1;i<=n;i++) {
		try {
			q=document.getElementById('qte['+i+']').value;
			qt=qt+(q*1);
			p=document.getElementById('prix['+i+']').value;
			if (gestion_tva!=0) {
				t=document.getElementById('tva['+i+']').value;
			}
			r=document.getElementById('rem['+i+']').value;

			switch(gestion_tva) {
				case '1' :
					mnt_ht=mnt_ht+(q*p*((100-r)/100));
					mnt_tva=mnt_tva+(q*p*((100-r)/100)*(t/100));
					break;
				case '2' :
					mnt_ttc=mnt_ttc+(q*p*((100-r)/100));
					
					mnt_ht=mnt_ht+((q*p*((100-r)/100))/(1+(t/100))) ;
					break;
				default :
					mnt_ttc=mnt_ttc+(q*p*((100-r)/100));
					break;
			}
			
		}catch(err) {}
	}
	var found = false;
	for (var i = (thresholds.length-1); i >= 0; i--) {
		if ((!thresholds[i].amount_tax_included && (thresholds[i].amount <= mnt_ht)) || (thresholds[i].amount_tax_included && (thresholds[i].amount <= mnt_ttc))) {
			if (!current_threshold || (current_threshold != thresholds[i].id)) {
				current_threshold = thresholds[i].id;
				thresholds_info.innerHTML = pmbDojo.messages.getMessage('thresholds', 'threshold_change_notification') + '<b>' + thresholds[i].label + '</b>';
				if (!init) {
					thresholds_info.className = 'erreur';
					setTimeout(function (){thresholds_info.className = '';}, 5000);
					require(["dojo/topic"], function(topic){
							topic.publish("dGrowl", pmbDojo.messages.getMessage('thresholds', 'threshold_change_notification') + '<b>' + thresholds[i].label + '</b>', {});
					});
				}
			}
			found = true;
			break;
		}
	}
	if (!found && current_threshold) {
		current_threshold = 0;
		thresholds_info.innerHTML = pmbDojo.messages.getMessage('thresholds', 'threshold_change_notification_no_threshold');
		if (!init) {
			thresholds_info.className = 'erreur';
			setTimeout(function (){thresholds_info.className = '';}, 5000);
			require(["dojo/topic"], function(topic){
					topic.publish("dGrowl", pmbDojo.messages.getMessage('thresholds', 'threshold_change_notification_no_threshold'), {});
			});
		}
	}
}

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
	  if(typeof ajax_resize_elements == "function"){
		  ajax_resize_elements();
	  }
}

function expandAllCommentsRows(context) {
	if ((context == undefined) || !context) context = document;
	var tempCollCommentsChild = context.querySelectorAll('tr[class~="act_cell_comments"]');
	var tempColl = Array.prototype.slice.call(tempCollCommentsChild);
	var tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		if (tempColl[i].previousElementSibling.style.display != 'none')
			tempColl[i].style.display = 'table-row';
		var callback = tempColl[i].getAttribute("callback");
		if(callback){
			window[callback]();
		}
		if(typeof ajax_resize_elements == "function"){
			ajax_resize_elements();
		}
	}
	tempColl    = context.querySelectorAll('img[class~="act_cell_img_plus"]');
	tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		tempColl[i].src = imgOpened.src;
	}
}

function collapseAllCommentsRows(context) {
	if ((context == undefined) || !context) context = document;
	var tempCollCommentsChild = context.querySelectorAll('tr[class~="act_cell_comments"]');
	var tempColl = Array.prototype.slice.call(tempCollCommentsChild);
	  
	var tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		tempColl[i].style.display = 'none';
	}
	tempColl    = context.querySelectorAll('img[class~="act_cell_img_plus"]');
	tempCollCnt = tempColl.length;
	for (var i = 0; i < tempCollCnt; i++) {
		//on teste sur 2 niveaux
//		if(Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.classList).indexOf('parent')!= -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('notice-parent') != -1 || Array.prototype.slice.call(tempColl[i].parentElement.parentElement.classList).indexOf('parent') != -1) {
			tempColl[i].src = imgClosed.src;
//		}
	}
}

//callback pour l'ajax fournisseurs
function callBackAdresseFournisseur(){
	var id_fou=document.getElementById('id_fou');
	var tab=id_fou.value.split(',');
	var tmpConcat=tab[0] + ',' + tab[1] + ',';
	var adresse=id_fou.value.substr(tmpConcat.length);
	id_fou.value=tab[0];
	document.getElementById('id_adr_fou').value=tab[1];
	document.getElementById('adr_fou').value=adresse;
}

//callback pour l'ajax types produits
function callBackTypeProduit(field){
	var tmpSplit=field.split('[');
	var tmpSplit=tmpSplit[1].split(']');
	var id=tmpSplit[0];
	
	var typ=document.getElementById('typ[' + id + ']');
	var tab=typ.value.split(',');
	
	typ.value=tab[0];
	document.getElementById('rem[' + id + ']').value=tab[1];
	//La tva est g�r�e sur param�tre
	try {
		document.getElementById('tva[' + id + ']').value=tab[2];
	}catch(err) {}
	
	if(acquisition_gestion_tva==1){
		document.getElementById('convert_ht_ttc_' + id).innerHTML=ht_to_ttc(document.getElementById('prix[' + id + ']').value,document.getElementById('tva[' + id + ']').value);
	}else if(acquisition_gestion_tva==2){
		document.getElementById('convert_ht_ttc_' + id).innerHTML=ttc_to_ht(document.getElementById('prix[' + id + ']').value,document.getElementById('tva[' + id + ']').value);
	}
	thresholds_notification();
}

function add_applicant_line(td_id, plus_button){
	var td = document.getElementById(td_id);
	var cmd_indice = parseInt(td_id.split('_')[1]);
	var applicant_indice = td.children.length;

	var div_row = document.createElement('div');
	div_row.setAttribute('class', 'row');
	
	var span = document.createElement('span');
	span.style.width = "428px";
	
	var input_label = document.createElement('input');
	input_label.setAttribute('id', 'C_'+cmd_indice+'_empr_label_'+applicant_indice);
	input_label.setAttribute('class', 'saisie-50emr');
	input_label.setAttribute('type', 'text');
	input_label.setAttribute('autocomplete', 'off');
	input_label.setAttribute('autfield', 'C_'+cmd_indice+'_applicants_'+applicant_indice);
	input_label.setAttribute('completion', 'empr');
	input_label.setAttribute('name', 'C_'+cmd_indice+'_empr_label_'+applicant_indice);
	
	var input_id = document.createElement('input');
	input_id.setAttribute('id', 'C_'+cmd_indice+'_applicants_'+applicant_indice);
	input_id.setAttribute('type', 'hidden');
	input_id.setAttribute('name', 'applicants['+cmd_indice+'][]');
	
	
	var popup_button = document.createElement('input');
	popup_button.setAttribute('class', 'bouton');
	popup_button.setAttribute('value', '...');
	popup_button.setAttribute('type', 'button');
	popup_button.onclick = function(){
		openPopUp('./select.php?what=emprunteur&caller=act_modif&param1=C_'+cmd_indice+'_applicants_'+applicant_indice+'&param2=C_'+cmd_indice+'_empr_label_'+applicant_indice+'', 'C_!!no!!_applicants_0', 400, 400, -2, -2, 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes'); 
	}
	
	var purge_btn = document.createElement('input');
	purge_btn.setAttribute('class', 'bouton');
	purge_btn.setAttribute('value', 'X');
	purge_btn.setAttribute('type', 'button');
	purge_btn.onclick = function(){
		document.getElementById('C_'+cmd_indice+'_empr_label_'+applicant_indice).value=''; 
		document.getElementById('C_'+cmd_indice+'_applicants_'+applicant_indice).value=0;
	}
	
	
	span.appendChild(input_label);
	span.appendChild(input_id);
	div_row.appendChild(span);
	div_row.appendChild(popup_button);
	div_row.appendChild(purge_btn);
	if(plus_button){
		var plus = document.createElement('input');
		plus.setAttribute('value', '+');
		plus.setAttribute('class', 'bouton');
		plus.setAttribute('type', 'button');
		plus.addEventListener('click', function(){
			add_applicant_line('C_'+cmd_indice+'_applicants_container');
		});
		div_row.appendChild(plus);
	}
	td.appendChild(div_row);
	
	ajax_pack_element(input_label);
	
}

function get_thresholds() {
	var id_entity = '';
	if (document.getElementById('id_bibli')) {
		id_entity = document.getElementById('id_bibli').value;
	}
	var httprequest = new http_request();
	httprequest.request('./ajax.php?module=acquisition&categ=ach&sub=thresholds&action=get_data&id_entity='+id_entity,false,'',false);
	return JSON.parse(httprequest.get_text());
}

function typeSwitched(num_ligne){
	var rowContainer = document.getElementById('R_'+num_ligne);
	if(rowContainer && document.getElementById('typ_lig['+num_ligne+']')) {
		rowContainer.setAttribute('class', 'act_typ_lig_'+document.getElementById('typ_lig['+num_ligne+']').value);
		
	}
	var applicantsContainer = document.getElementById("C_"+num_ligne+"_applicants_container");
	if(!applicantsContainer) return;
	var nbApplicantsLine = applicantsContainer.children.length;
	var trParent = applicantsContainer.parentNode;
	if(document.getElementById('typ_lig['+num_ligne+']').value == 1 || document.getElementById('typ_lig['+num_ligne+']').value == 2){
		trParent.setAttribute('style', 'display:table-row;');
	}else{
		for(var i=0 ; i<nbApplicantsLine ; i++){
			if(document.getElementById('C_'+num_ligne+'_empr_label_'+nbApplicantsLine)) {
				document.getElementById('C_'+num_ligne+'_empr_label_'+nbApplicantsLine).value='';
			}
			if(document.getElementById('C_'+num_ligne+'_applicants_'+nbApplicantsLine)) {
				document.getElementById('C_'+num_ligne+'_applicants_'+nbApplicantsLine).value=0;
			}
		}
		trParent.setAttribute('style', 'display:none;');
	}
}

//Copie lignes cochees
function act_copyLines(){
	
	var ac=act_curline;
	var i,c;
	var cur_row,new_row;
	
	for (i=1;i<=ac;i++) {
		
		c=document.getElementById('chk['+i+']');
		try {
			if(c.checked) {

				cur_row=document.getElementById('R_'+i);
				if(cur_row) {
					act_addLine();
					document.getElementById('code['+act_curline+']').value = document.getElementById('code['+i+']').value;
					document.getElementById('lib['+act_curline+']').value = document.getElementById('lib['+i+']').value;
					document.getElementById('qte['+act_curline+']').value = document.getElementById('qte['+i+']').value;
					document.getElementById('prix['+act_curline+']').value = document.getElementById('prix['+i+']').value;
					document.getElementById('typ['+act_curline+']').value = document.getElementById('typ['+i+']').value;
					document.getElementById('lib_typ['+act_curline+']').value = document.getElementById('lib_typ['+i+']').value;
					if(gestion_tva>0){
						document.getElementById('tva['+act_curline+']').value = document.getElementById('tva['+i+']').value;
						document.getElementById('force_debit['+act_curline+']').value = document.getElementById('force_debit['+i+']').value;
						document.getElementById('force_ht_ttc_'+act_curline).innerHTML = document.getElementById('force_ht_ttc_'+i).innerHTML;
					}
					document.getElementById('rem['+act_curline+']').value = document.getElementById('rem['+i+']').value;
					document.getElementById('rub['+act_curline+']').value = document.getElementById('rub['+i+']').value;
					document.getElementById('lib_rub['+act_curline+']').value = document.getElementById('lib_rub['+i+']').value;
					document.getElementById('lg_statut['+act_curline+']').value = document.getElementById('lg_statut['+i+']').value;
					document.getElementById('id_sug['+act_curline+']').value = document.getElementById('id_sug['+i+']').value;
					document.getElementById('id_lig['+act_curline+']').value = document.getElementById('id_lig['+i+']').value;
					document.getElementById('typ_lig['+act_curline+']').value = document.getElementById('typ_lig['+i+']').value;
					document.getElementById('id_prod['+act_curline+']').value = document.getElementById('id_prod['+i+']').value;
				}
	
			}
		}catch(err) {
		}
	}
	act_calc();
	thresholds_notification();
	return false;	
}

//Augmentation lignes cochees
function act_increaseRate(){

	var n=act_curline;
	var increase_rate_percent=document.getElementById('increase_rate_percent').value;
	if(isNaN(increase_rate_percent)) {
		return false;
	}
	var i,j,c,p;
	
	for (i=1;i<=n;i++) {
		c=document.getElementById('chk['+i+']');
		try {
			if(c.checked) {
				p = document.getElementById('prix['+i+']').value;
				if(!isNaN(p)) {
					p = p * (1 + (increase_rate_percent / 100));
					p = p.toFixed(precision);
					document.getElementById('prix['+i+']').value = p;
				}
			}
		}catch(err) {}
	}
	act_calc();
	thresholds_notification();
	return false;
}

function act_applyTypeToChecked() {

	var n=act_curline;
	var typ_for_checked = document.getElementById('typ_for_checked').value;
	var lib_typ_for_checked = document.getElementById('lib_typ_for_checked').value;
	var rem_for_checked = document.getElementById('rem_for_checked').value;
	var tva_for_checked = document.getElementById('tva_for_checked').value;
	
	for (i=1;i<=n;i++) {
		try {
			if(document.getElementById('chk['+i+']').checked) {
				document.getElementById('typ['+i+']').value = typ_for_checked;
				document.getElementById('lib_typ['+i+']').value = lib_typ_for_checked;
				document.getElementById('rem['+i+']').value = rem_for_checked;
				document.getElementById('tva['+i+']').value = tva_for_checked;
			}

		}catch(err) {}
	}
	act_calc();
	return false;
}


function act_applyBudgetToChecked() {
	
	var n=act_curline;
	var rub_for_checked = document.getElementById('rub_for_checked').value;
	var lib_rub_for_checked = document.getElementById('lib_rub_for_checked').value;
	for (i=1;i<=n;i++) {
		try {
			if(document.getElementById('chk['+i+']').checked) {
				document.getElementById('rub['+i+']').value = rub_for_checked;
				document.getElementById('lib_rub['+i+']').value = lib_rub_for_checked;
			}

		}catch(err) {}
	}
	act_calc();
	return false;
}

function callbackUpdateType() {
	var typ_for_checked = document.getElementById('typ_for_checked').value;
	var tab = typ_for_checked.split(',');
	var rem_for_checked = tab[1];
	var tva_for_checked = tab[2];
	document.getElementById('rem_for_checked').value = tab[1];
	document.getElementById('tva_for_checked').value = tab[2];
}

window.onload = function () {thresholds_notification(true);}
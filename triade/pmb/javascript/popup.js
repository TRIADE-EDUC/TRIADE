// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: popup.js,v 1.29.6.1.2.1 2025/01/29 13:17:19 dgoron Exp $

// openPopUp : permet d'afficher une popup de la taille et � la position donn�e
//		la fonction gere aussi l'autoCentrage de la popup
//		(ATTENTION au mode double ecran : la fonction ne gere pas le centrage par rapport � la fenetre mais par rapport � la taille �cran !!)
//
//MyFile :	nom du fichier contenant le code HTML du pop-up
//MyWindow :	nom de la fenetre (ne pas mettre d'espace)
//MyWidth :	entier indiquant la largeur de la fenetre en pixels
//MyHeight :	entier indiquant la hauteur de la fenetre en pixels
//MyLeft :	entier indiquant la position du haut de la fenetre en pixels (-1 pour centrer, -2 pour laisser le navigateur g�rer)
//MyTop :	entier indiquant la position gauche de la fenetre en pixels (-1 pour centrer, -2 pour laisser le navigateur g�rer)
//MyParam :	Les parametres supplementaires pour la methode open (par def :infobar=no, status=no, scrollbars=no, menubar=no)
//ForceSelectorDialog : forçage de l'ouverture en pop-up
function openPopUp(MyFile,MyWindow,MyWidth,MyHeight,MyLeft,MyTop,MyParam,ForceSelectorDialog,ForceSelectorPopUp) {
	var ns4 = (document.layers)? true:false;		//NS 4
	var ie4 = (document.all)? true:false;			//IE 4
	var dom = (document.getElementById)? true:false;	//DOM
	var xMax, yMax, xOffset, yOffset;

	//les valeurs par d�faut
	MyParam = MyParam || 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no';
	ForceSelectorDialog = ForceSelectorDialog || false;
	ForceSelectorPopUp = ForceSelectorPopUp || false;
	//MyTop = MyTop || -1;
	MyTop=0;
	//MyLeft = MyLeft || -1;
	MyLeft=0;

	xOffset = MyLeft;
	yOffset = MyTop;

	if(!MyWidth || !MyHeight) {
		switch(MyWindow) {
			case 'cart':
				MyWidth = '600';
				MyHeight = '700';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes';
				break;
			case 'print_cart':
			case 'print_dsi':
				MyWidth = '500';
				MyHeight = '400';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes';
				break;
			case 'print_doc_dsi':
				MyWidth = '800';
				MyHeight = '600';
				MyParam = 'scrollbars=yes, toolbar=yes, dependent=yes, resizable=yes';
				break;
			case 'audit_popup':
				MyWidth = '700';
				MyHeight = '500';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes';
				break;
			case 'regex_howto':
				MyWidth = '500';
				MyHeight = '400';
				MyParam = 'scrollbars=yes, resizable=yes';
				break;
			case 'lettre':
			case 'print_PDF':
				MyWidth = '600';
				MyHeight = '500';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes';
				break;
			case 'mail':
				MyWidth = '600';
				MyHeight = '500';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes';
				break;
			case 'print':
				MyWidth = '500';
				MyHeight = '600';
				MyParam = 'scrollbars=yes,menubar=0,resizable=yes';
				break;
			case 'download':
				MyWidth = '500';
				MyHeight = '600';
				MyParam = 'scrollbars=yes,menubar=0';
				break;
			case 'selector':
				MyWidth = '500';
				MyHeight = '400';
				MyParam = 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes';
				break;
			case 'calendar':
				MyWidth = '250';
				MyHeight = '300';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes';
				ForceSelectorDialog = true;
				break;
			case 'getcb':
				MyWidth = '220';
				MyHeight = '200';
				MyParam = 'toolbar=no, resizable=yes';
				break;
			case 'selector_commande':
				MyWidth = '600';
				MyHeight = '400';
				MyParam = 'infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes';
				break;
			case 'selector_notice':
			case 'selector_category':
			case 'selector_ontology':
				MyWidth = '700';
				MyHeight = '500';
				MyParam = 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes';
				break;
			case 'options':
				MyWidth = '550';
				MyHeight = '600';
				MyParam = 'menubars=no,resizable=yes,scrollbars=yes';
				break;
			case 'print_rel':
				MyWidth = '600';
				MyHeight = '500';
				MyParam = 'scrollbars=yes,menubar=0,resizable=yes';
				break;
			case 'circulation':
				MyWidth = '600';
				MyHeight = '500';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes';
				break;
			case 'date_retbloc':
				MyWidth = '250';
				MyHeight = '260';
				MyParam = 'toolbar=no, dependent=yes, resizable=yes';
				ForceSelectorDialog = true;
				break;
		}
	}
		
	//on precise la taille pour la methode open	
	var fParam = 'width='+MyWidth
			+',height='+MyHeight;

	//on ajoute les parametres en plus 
	var fParam = MyParam + ',' + fParam;
	if(!ForceSelectorPopUp && MyFile && (MyFile.indexOf('select.php') != -1)){
		require(["apps/pmb/PMBSelectorDialog", 
		         "dojo/dom", 
		         "dojo/io-query",
		         "dojo/dom-attr",
		         "dojo/topic",
		         "dojo/dom-construct",
		         "dojo/on",
		         "dojo/_base/lang"], function(PMBSelectorDialog, dom, ioQuery, domAttr, topic, domConstruct, on, lang){
			
			/** Récupération de l'élément courant **/
			/** Pas très propre, mais pas ou peu de moyen annexe **/
			var clickedElement = document.activeElement;
			
			//on teste la presence de l'id du schema de catalogage dans l'url
			if (typeof catalogingSchemeId !== 'undefined') {
				MyFile +="&cataloging_scheme_id=" + catalogingSchemeId + "&cataloging_scheme_level=" + (parseInt(catalogingSchemeLevel) + 1);
				delete catalogingSchemeId;
				delete catalogingSchemeLevel;
			}
			var params = ioQuery.queryToObject(MyFile.split('?')[1]);
			var form = clickedElement.form;
			
			/*
			 * Nous ne publions l'évenement que dans le cas ou les boutons sont dans un formulaire
			 * marqué part l'attribut "data-advanced-form" et si l'url du openPopup pointe bien vers
			 * une page select.php
			 */
			if(!ForceSelectorDialog && form && domAttr.get(form, 'data-advanced-form') && (MyFile.split('?')[0].indexOf('select.php') != -1)){
				topic.publish('openPopup', 'openPopup', 'buttonClicked', {
					params: params,
					url: MyFile,
					button: clickedElement
				});
			}else{ //Dans le reste des cas, on conserve le cas standard de la popup
		    	var http = new http_request();
		    	var MyFileTitleUrl = MyFile.replace('select.php?', 'ajax.php?module=selectors&is_iframe=1&')+'&action=title';
		    	http.request(MyFileTitleUrl);
		    	var MyFileTitle = http.get_text();
				var dialog = new PMBSelectorDialog({'params' : params});
				var iframe = domConstruct.create('iframe', {popupFrame: 'true', seamless: '', 'class': 'selectorsIframe', style:{minWidth:"200px", width: '100%', height:'100%'}, src: MyFile, title : MyFileTitle});				
				dialog.set('content', iframe);
				dialog.startup();
				dialog.show();
				
				//Suppression du dialog au cas ou (peut être inutile grace à l'iframe mais permet de nettoyer le dom)
				dialog.onHide = lang.hitch(dialog, function(){
					this.destroyRecursive();
				});
			}
			//Il serait interessant de voir si le retour de la fonction est utilisé de temps en temps 
			return dialog;
		});	
	}else{
		var currentPageObjects = getCurrentPageObjects();
		var selectedObjects = getSelectedObjects();
		if(currentPageObjects || selectedObjects) {
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			form.setAttribute("id", MyWindow);
			form.setAttribute("action", MyFile);
			form.setAttribute("target", MyWindow);

			if (currentPageObjects) {
				var hiddenField = document.createElement("input"); 
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "current_page_objects");
				hiddenField.setAttribute("value", currentPageObjects);
				form.appendChild(hiddenField);
			}
			if (selectedObjects) {
				var hiddenField = document.createElement("input"); 
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "selected_objects");
				hiddenField.setAttribute("value", selectedObjects);
				form.appendChild(hiddenField);
			}
			
			document.body.appendChild(form);
			
			w = window.open('', MyWindow,fParam);
			form.submit();
			document.body.removeChild(form);
		} else {
			//on ouvre la popup
			w = window.open(MyFile,MyWindow,fParam);
		}
	
		if(MyFile) {
			//on force la taille
			w.window.resizeTo(MyWidth,MyHeight);
			
			//on force la position  uniquement si on est pas en mode -2 (position g�r�e par le navigateur)
			if ((MyTop!=-2)&&(MyLeft!=-2)) {
				w.window.moveTo(xOffset,yOffset);
			}
		
			//on force le focus
			w.window.focus();
		}
		return w;
	}
}

function openPopUpSelector(MyFile,ForceSelectorDialog,ForceSelectorPopUp) {
	switch(ForceSelectorDialog) {
		case '1':
			ForceSelectorDialog = true;
			break;
		case '0':
			ForceSelectorDialog = false;
			break;
		default:
			ForceSelectorDialog = ForceSelectorDialog || false;
			break;
	}
	switch(ForceSelectorPopUp) {
		case '1':
			ForceSelectorPopUp = true;
			break;
		case '0':
			ForceSelectorPopUp = false;
			break;
		default:
			ForceSelectorPopUp = ForceSelectorPopUp || false;
			break;
	}
	return openPopUp(MyFile,'selector',0,0,0,0,'',ForceSelectorDialog,ForceSelectorPopUp)
}

function openPopUpSelectorCategory(MyFile,ForceSelectorDialog,ForceSelectorPopUp) {
	switch(ForceSelectorDialog) {
		case '1':
			ForceSelectorDialog = true;
			break;
		case '0':
			ForceSelectorDialog = false;
			break;
		default:
			ForceSelectorDialog = ForceSelectorDialog || false;
			break;
	}
	switch(ForceSelectorPopUp) {
		case '1':
			ForceSelectorPopUp = true;
			break;
		case '0':
			ForceSelectorPopUp = false;
			break;
		default:
			ForceSelectorPopUp = ForceSelectorPopUp || false;
			break;
	}
	return openPopUp(MyFile,'selector_category',0,0,0,0,'',ForceSelectorDialog,ForceSelectorPopUp)
}

function getCurrentPageObjects(context) {
	if(context == 'opener') {
		var selectionNodes = window.opener.document.querySelectorAll('input[name=objects_selection]');
	} else {
		var selectionNodes = document.querySelectorAll('input[name=objects_selection]');
	}
	var selectionObjects = new Array();
	for(var i=0 ; i<selectionNodes.length ; i++){
		selectionObjects.push(selectionNodes.item(i).value);
	}
	return selectionObjects.join(',');
}

function getSelectedObjects(context) {
	if(context == 'opener') {
		var selectionSelectedNodes = window.opener.document.querySelectorAll('input[name=objects_selection]:checked');
	} else {
		var selectionSelectedNodes = document.querySelectorAll('input[name=objects_selection]:checked');
	}
	var selectionSelectedObjects = new Array();
	for(var i=0 ; i<selectionSelectedNodes.length ; i++){
		selectionSelectedObjects.push(selectionSelectedNodes.item(i).value);
	}
	return selectionSelectedObjects.join(',');
}

function checkScrollPosition(id) {
	var box = document.querySelector("#" + id + " .uk-overflow-container");
	if (!((box.scrollHeight - box.offsetHeight) > box.scrollTop)) {
		document.getElementById(id).querySelector(".uk-modal-close")
				.removeAttribute("disabled");
		box.removeEventListener("scroll", eval("handleScroll_" + id));
	}
}

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb.js,v 1.6 2021/02/09 15:30:46 jlaurent Exp $
dojo.require('dojox.widget.DialogSimple');
dojo.require('dijit.registry');
dojo.require('dijit.ConfirmDialog');

function pnb_post_loan_info(notice_id, pnb_loan_display_mode = 0) {
	
	var node_id = 'response_pnb_pret_' + notice_id;
	var dialog = create_dialog(pnb_loan_display_mode, node_id, pmbDojo.messages.getMessage('pnb', 'pnb_loan_modal_title'));
	
	//On recupere les donnees
	let request = new http_request();
    request.request("./ajax.php?module=ajax&categ=pnb&action=get_empr_devices_list&notice_id="+notice_id+"", false,'', true, function(data) {
		data = JSON.parse(data);
		var tempoShow = false;
		return refresh_dialog(dialog, data.vue, pnb_loan_display_mode, tempoShow);
    });

}

//fonction appelee lors de la confirmation de l'emprunt par l'utilisateur
function pnb_confirm_loan(userAgent,notice_id){
	//on fait le check form avant de poursuivre si l'utilisateur choisi un nouveau MDP
	var pass= "";
	var hintPass = "";
	if (document.getElementById('new_pass').checked){
		var validateForm = check_passform();
		pass= document.getElementById('pnb_password').value;
		hintPass= document.getElementById('pnb_password_hint').value;
	} else {
		var validateForm = true;
	}
	
	if (validateForm == true) {
		if (userAgent == ''){
			//Dans le cas ou le user agent est vide
			var selectedUserAgent = userAgent;
		} else {
			//Sinon on recupere dans la modale celui selectionne par l'utilisateur
			var selectedUserAgent = document.querySelector('input[name='+userAgent+']:checked').value;
		}
		
		return pnb_post_loan(selectedUserAgent, notice_id, pass, hintPass);
	}
}

//fonction appelant le pret cote WS
function pnb_post_loan(selectedUserAgent, notice_id, pass = "", hintPass = ""){
	
	var infoComp = "";
	var loan_display_mode = 0;
	
	//On cherche la modal, si on ne l'a pas on est sur un affichage inline
	var dialog = dijit.registry.byId('response_pnb_pret_' + notice_id);
	if (!dialog){
		loan_display_mode = 1;
		var dialog = document.getElementById('response_pnb_pret_' + notice_id);
	}
	
	//Dans le cas ou le userAgent est vide, on affiche un message pour parametrer
	if (selectedUserAgent == '') {
		infoComp = "<br><i>" + pmbDojo.messages.getMessage('pnb', 'empr_pnb_no_device_set') + "</i>";
	}
	var tempoShow = false;
	refresh_dialog(dialog, "<div style='width:100%; height:30px;text-align:center'><img style='padding 0 auto; border:0px;' src='images/patience.gif' id='collapseall'></div>", loan_display_mode, tempoShow);

    let request = new http_request();
    request.request('./ajax.php?module=ajax&categ=pnb&action=post_loan_info&notice_id=' + notice_id + '&empr_pnb_device=' + selectedUserAgent, true,'&pass='+pass+'&hint_pass='+hintPass, true, (data) => {
        let response = JSON.parse(data);
		let formatedResponse = "<div style='width:100%'>"+response.message + infoComp+"</div><div class='row'>&nbsp;</div>";
		//On affiche la reponse temporairement
		tempoShow = true;
		refresh_dialog(dialog, formatedResponse, loan_display_mode, tempoShow);
        if (response.infos && response.infos.link && response.infos.link.url) { 
            window.open(response.infos.link.url, '_blank'); 
        }
    });

}

function refresh_dialog(nodeRef, content, loan_display_mode, tempoShow = false) {

	if (loan_display_mode == 0){
		nodeRef.set("class","");
		nodeRef.set('content',content);
		//On affiche le noeud s'il ne l'est pas
		if (!nodeRef.isFocusable()){
			nodeRef.show();
		}
		if (tempoShow){
			nodeRef.set("class","tempDialog");
			setTimeout(()=>{
				nodeRef.hide();
			}, 5000);
		} else {
			nodeRef.set("class","dialog");
		}
	} else if (loan_display_mode == 1){
		nodeRef.classList.remove("tempDialog", "dialog");
		nodeRef.style = "display:block;";
		nodeRef.innerHTML = content;
		if (tempoShow){
			nodeRef.classList.add("tempDialog");
			setTimeout(()=>{
				nodeRef.style = "display:none;";
			}, 5000);
		} else {
			nodeRef.classList.add("dialog");
		}
	}
	
}

function pnb_post_loan_after_confirm(pnb_loan_display_mode, userAgentId, notice_id) {
	
	if (pnb_loan_display_mode == 0){
		var confirmDialog = new dijit.ConfirmDialog({
	        title: pmbDojo.messages.getMessage('pnb', 'pnb_loan_modal_title'),
	        content: "<div style='width:100%; height:70%;text-align:center'>"+pmbDojo.messages.getMessage('pnb', 'pnb_confirm_loan')+"</div><div class='row'>&nbsp;</div>",
	        style: "width: 500px; height:200px; class='left'",
 			onExecute :(() => {
                 return pnb_post_loan(userAgentId, notice_id);
            }),
			onCancel : (() => {
				return false;
			})
    	});		
		confirmDialog.show();
	} else {
		var confirmDialog = confirm(pmbDojo.messages.getMessage('pnb', 'pnb_confirm_loan'));
		if(false == confirmDialog) {
			return false;	
		} else {
			return pnb_post_loan(userAgentId, notice_id);
		}
	}
	
}

function pnb_stop_loan(notice_id){
	
	//On cherche la modal, si on ne l'a pas on est sur un affichage inline
	var dialog = dijit.registry.byId('response_pnb_pret_' + notice_id);
	if (!dialog){
		loan_display_mode = 1;
		var dialog = document.getElementById('response_pnb_pret_' + notice_id);
		return dialog.innerHTML = "";
	}
	return dialog.hide();
	
}

function create_dialog(pnb_loan_display_mode, node_id, title) {
	var nodeRef = document.getElementById(node_id);
	if (pnb_loan_display_mode == 0){
		//Affichage sous forme de Dialog Dojo
		//On le recupere s'il est deja dans la page
		var dialog = dijit.registry.byId(node_id);
		//Sinon on le creee
		if (!dialog){
			dialog = new dojox.widget.DialogSimple({
				title: title, 
				style: 'min-width:550px; min-height:300px;'
				}, 
				nodeRef
			);
		}
	} else if (pnb_loan_display_mode == 1) {
		var dialog = nodeRef;
	}
	
	return dialog;
	
}

function returnLoan(expl_id, pnb_loan_display_mode, drm){
	
	var fromPortal = 1;
	let request = new http_request();
    request.request('./ajax.php?module=ajax&categ=pnb&action=returnLoan&expl_id=' + expl_id + '&fromPortal='+ fromPortal + '&drm='+ drm, false,'', true, (data) => {
        let response = JSON.parse(JSON.parse(data));
		let titleModal = pmbDojo.messages.getMessage('pnb', 'pnb_loan_modal_title');
		//afficher infos
		var node_id = 'response_pnb_return';
		var dialog = create_dialog(pnb_loan_display_mode, node_id, titleModal);

		//Affichage temporaire du message de retour
		tempoShow = true;

		if (response.status == true){
			
			if (response.message) {
				var msg = response.message;
			} else {
				var msg = pmbDojo.messages.getMessage('pnb', 'pnb_success_msg');
			}
			//On efface visuellement le prêt
			var tr = document.getElementById("loan_row_"+expl_id);
			var td = tr.childNodes;
			for (i = 0; i < td.length; i++){
				td[i].innerHTML =  "";
			}

			refresh_dialog(dialog,msg,pnb_loan_display_mode, tempoShow);
			
		} else {
			
			if (response.message){
				var error = response.message;
			} else {
				var error = pmbDojo.messages.getMessage('pnb', 'pnb_error_msg');
			}

			refresh_dialog(dialog,error,pnb_loan_display_mode, tempoShow);
			
		}
    });
}

function extendLoan(expl_id, pnb_loan_display_mode, drm){
	
	var fromPortal = 1;
	let request = new http_request();
    request.request('./ajax.php?module=ajax&categ=pnb&action=extendLoan&expl_id=' + expl_id + '&fromPortal='+ fromPortal + '&drm='+ drm, false,'', true, (data) => {
        let response = JSON.parse(JSON.parse(data));
		let titleModal = pmbDojo.messages.getMessage('pnb', 'pnb_loan_modal_title');
		//afficher infos
		//var node_id = 'response_pnb_return_' + expl_id;
		var node_id = 'response_pnb_return';
		var dialog = create_dialog(pnb_loan_display_mode, node_id, titleModal);
		
		//Affichage temporaire du message de retour
		tempoShow = true;
		
		if (response.status == true){
			
			if (response.message) {
				var msg = response.message;
			} else {
				var msg = pmbDojo.messages.getMessage('pnb', 'pnb_success_msg');
			}
			//Affichage de la nouvelle date
			var td = document.getElementById("loan_date_back_"+expl_id);
			if (response.loanEndDate) {
				td.innerHTML = response.loanEndDate
			}

			refresh_dialog(dialog,msg,pnb_loan_display_mode, tempoShow);
			
		} else {
			
			if (response.message){
				var error = response.message;
			} else {
				var error = pmbDojo.messages.getMessage('pnb', 'pnb_error_msg');
			}

			refresh_dialog(dialog,error,pnb_loan_display_mode, tempoShow);
			
		}
    });
}

function check_passform(form = ""){
	if (form){
		event.preventDefault();
	}
	var pass= document.getElementById('pnb_password');
	var confirmPass= document.getElementById('pnb_password_confirm');
	var hintPass= document.getElementById('pnb_password_hint');
	var divError = document.getElementById('pnb_error');

	pass.classList.remove('pnb_alert');
	confirmPass.classList.remove('pnb_alert');
	hintPass.classList.remove('pnb_alert');
	divError.innerHTML = "";
	
	if ((typeof pass != "undefined" && !pass.value) || (typeof confirmPass != "undefined" && !confirmPass.value) || (typeof hintPass != "undefined" && !hintPass.value)) {
		
		if (!pass.value) {
			pass.classList.add('pnb_alert');
		}
		if (!confirmPass.value) {
			confirmPass.classList.add('pnb_alert');
		}
		if (!hintPass.value) {
			hintPass.classList.add('pnb_alert');
		}
		divError.innerHTML = pmbDojo.messages.getMessage('pnb', 'pnb_error_empty_input');
		
		return false;
		
	} else if (pass.value !== confirmPass.value) {
		
		pass.classList.add('pnb_alert');
		confirmPass.classList.add('pnb_alert');
		divError.innerHTML = pmbDojo.messages.getMessage('pnb', 'pnb_error_pass');

		return false;
		
	} else {
		if (form){
			form.submit();
		}
		return true;
		
	}
	
}

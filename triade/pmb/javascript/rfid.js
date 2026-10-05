// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rfid.js,v 1.5 2016/05/09 10:13:02 dgoron Exp $

function f_expl(cb) {
	nb_part_readed=cb.length;
	if(flag_program_rfid_ask==1) {
		program_rfid();
		flag_cb_rfid=0; 
		return;
	}
	if(cb.length==0) {
		flag_cb_rfid=1;
		return;
	} 
	if(!cb[0]) {
		flag_cb_rfid=0; 
		return;
	}
	if(document.getElementById('f_ex_cb').value	== cb[0]) flag_cb_rfid=1;
	else  flag_cb_rfid=0;
	if(document.getElementById('f_ex_cb').value	== '') {	
		flag_cb_rfid=0;				
		document.getElementById('f_ex_cb').value=cb[0];
	}
}

function script_rfid_encode() {
	if(!flag_cb_rfid && flag_rfid_active) {
	    var confirmed = confirm(msg_rfid_programmation_confirmation);
	    if (confirmed) {
	    	program_rfid_ask();
			return false;
	    } 
	}
}
			
function program_rfid_ask() {
	if (flag_semaphore_rfid_read==1) {
		flag_program_rfid_ask=1;
	} else {
		program_rfid();
	}
}
			
function rfid_ack_erase(ack) {
	var cb = document.getElementById('f_ex_cb').value;
	var nbparts = 0;
	if(document.getElementById('f_ex_nbparts')) {
		nbparts = document.getElementById('f_ex_nbparts').value;	
		if(!nbparts)nbparts=1;
	} else {
		nbparts=1;
	}
	init_rfid_write_etiquette(cb,nbparts,rfid_ack_write);
	
}

function rfid_ack_write(ack) {				
	init_rfid_antivol_all(1,rfid_ack_antivol_actif);				
}

function rfid_ack_antivol_actif(ack) {
	alert (msg_rfid_etiquette_programmee_message);
	flag_semaphore_rfid=0;
}
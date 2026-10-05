// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_3m.js,v 1.6 2013/04/22 15:15:53 ngantier Exp $

var f_empr_client;
var f_expl_client;
var f_ack_write;	
var f_ack_erase;
var f_ack_detect;
var f_ack_write_empr;
var f_ack_antivol_all;
var f_ack_antivol;
var f_ack_read_uid;
var flag_semaphore_rfid=0;
var flag_semaphore_rfid_read=0;
var flag_rfid_active=1;
var rfid_active_test=1;
var rfid_active_test_exec=0;
var rfid_focus_active=1;
var get_uid_from_cb=new Array();
var pmb_rfid_driver3m;
function afficheErreur(){
	
}
function init_rfid_read_cb(empr_client,expl_client){	
	f_empr_client=empr_client;
	f_expl_client=expl_client;

	pmb_rfid_driver3m=new rfid_3m_810(url_serveur_rfid);
	// RFID init
	read_cb();
}

function timeout() {
	if(!flag_rfid_active_test) {
		flag_rfid_active=0;
		return;
	}	
}

function read_cb() {	
	if(!rfid_focus_active) {setTimeout('read_cb()',1500); return;}
	if(flag_disable_antivol) {
		return;
	}
	if(!rfid_active_test_exec) {
		rfid_active_test_exec++;
		setTimeout('timeout()',20000);
	}
	if (flag_semaphore_rfid || flag_semaphore_rfid_read) {
		setTimeout('read_cb()',1500); 
		return;
	}
	flag_rfid_active_test=0;
	if(!rfid_focus_active) {setTimeout('read_cb()',1500); return;}	
	flag_semaphore_rfid_read=1;	
	pmb_rfid_driver3m.cmd("inventory",{'uidOnly':false,'getAFI':false},result_read_cb,afficheErreur);
}

function result_read_cb (retVal) {
	var i;
	var array_cb_expl=new Array();
	var array_cb_empr=new Array();
	var array_cb_index=new Array();
	var array_cb_count=new Array();
	var array_cb_eas=new Array();
	var nb_doc=0;
	var nb_patroncard=0;
	flag_rfid_active_test=1;
	flag_rfid_active=1;
	
	
	for (i=0; i<retVal.length; i++) {	
		var uid=retVal[i].uid;
		if(!retVal[i].error){
			if(retVal[i].type==1) {			
				array_cb_empr[nb_patroncard++]=retVal[i].cb;
			}else{ 
				array_cb_expl[nb_doc]=retVal[i].cb;
				array_cb_index[nb_doc]=retVal[i].part;
				array_cb_count[nb_doc]=retVal[i].part_number;

				if(!get_uid_from_cb[retVal[i].cb])get_uid_from_cb[retVal[i].cb]=new Array();
				get_uid_from_cb[retVal[i].cb][get_uid_from_cb[retVal[i].cb].length]=uid;			
				nb_doc++;								
			}		
		}	
	}
			
	if(f_expl_client)	f_expl_client(array_cb_expl,array_cb_index,array_cb_count,array_cb_eas);
	if(f_empr_client)	f_empr_client(array_cb_empr);
	setTimeout('read_cb()',1000);
	flag_semaphore_rfid_read=0;
}

function read_uid(f_ack) {	

	flag_rfid_active_test=0;
	flag_semaphore_rfid_read=1;
	f_ack_read_uid=f_ack;
	pmb_rfid_driver3m.cmd("inventory",{'uidOnly':true,'getAFI':false},result_read_uid,afficheErreur);
}

function result_read_uid (retVal) {
	var i;
	var nb_doc=0;
	var liste_uid=new Array();
	flag_rfid_active_test=1;
	flag_rfid_active=1;
	
	for (i=0; i<retVal.length; i++) {	
		liste_uid[nb_doc++]=retVal[i].uid;
	}
	flag_semaphore_rfid_read=0;
	if(f_ack_read_uid) f_ack_read_uid(liste_uid);
}

// Detect présence d'élement rfid
function init_rfid_detect(ack_detect) {
	if(!flag_rfid_active) return;
	f_ack_detect=ack_detect;
	pmb_rfid_driver3m=new rfid_3m_810(url_serveur_rfid);
	pmb_rfid_driver3m.cmd("inventory",{'uidOnly':true,'getAFI':false},result_detect,afficheErreur); 
}
	
	
function result_detect(retVal) {
	flag=retVal.length;
	if(!retVal.length)flag='false';
	if(f_ack_detect)f_ack_detect(flag);
}

// Efface tout !!!
function init_rfid_erase(ack_erase) {
	f_ack_erase=ack_erase;
  	if(!flag_rfid_active) return;
	read_uid(rfid_erase_suite); 
}
	
function rfid_erase_suite(retVal) {
	if(!flag_rfid_active) return;

}	

function result_erase(retVal) {
	if(f_ack_erase)f_ack_erase(true);
}


var write_etiquette_data=new Array();
	
// Programme une étiquette
function init_rfid_write_etiquette (cb,nbtags,ack_write) {
	f_ack_write=ack_write;
	if(!flag_rfid_active) return;
	var afi= rfid_afi_security_active;
	write_etiquette_data.ack_write=ack_write;
	pmb_rfid_driver3m.cmd("encode",{'type':0,'cb':cb,'afi':afi},result_write,afficheErreur);
    
}

function result_write(retVal) {		

	if(f_ack_write)f_ack_write(retVal.error);
}  
  
// Programme une carte lecteur
var write_patron_data=new Array();
function init_rfid_write_empr (cb,ack_write) {
	if(!flag_rfid_active) return;
	
	write_patron_data.ack_write=ack_write;
	pmb_rfid_driver3m.cmd("encode",{'type':1,'cb':cb,'afi':""},result_write_empr,afficheErreur);
    
}  


function result_write_empr(retVal) {	
	
	if(f_ack_write_empr)f_ack_write_empr(retVal.error);
}     

// Active / désactive un antivol
function init_rfid_antivol (cb,level,ack_antivol) {
	if(!flag_rfid_active) return;
	f_ack_antivol=ack_antivol;
	if(!pmb_rfid_driver3m)pmb_rfid_driver3m=new rfid_3m_810(url_serveur_rfid);
	var afi= rfid_afi_security_off;
	if(level)afi= rfid_afi_security_active;
	var uidlist=new Array();
	if(!get_uid_from_cb[cb]){
		pmb_rfid_driver3m.cmd("inventory",{'uidOnly':false,'getAFI':false},rfid_antivol_suite_1,afficheErreur);
		param_antivol_level=level;
		param_antivol_cb=cb;
		return;
	}	
	var list=get_uid_from_cb[cb];
	if(!list)f_ack_antivol(0);
	for(var i=0;i<list.length;i++){
		uidlist[i]=new Array();
		uidlist[i]['uid']=list[i];
	}
	pmb_rfid_driver3m.cmd("writeAFI",{'uidList':uidlist,'afi':afi},result_rfid_antivol ,afficheErreur);
	
}  

function rfid_antivol_suite_1 (retVal) {

	for (i=0; i<retVal.length; i++) {	
		var uid=retVal[i].uid;
		if(!retVal[i].error){
			if(!retVal[i].type) { 				
				if(!get_uid_from_cb[retVal[i].cb])get_uid_from_cb[retVal[i].cb]=new Array();
				get_uid_from_cb[retVal[i].cb][get_uid_from_cb[retVal[i].cb].length]=uid;	
			}		
		}	
	}

	var level=param_antivol_level;
	var cb= param_antivol_cb;
	
	var afi= rfid_afi_security_off;
	if(level)afi= rfid_afi_security_active;
	var uidlist=new Array();
	var list=get_uid_from_cb[cb];
	if(!list)f_ack_antivol(0);
	for(var i=0;i<list.length;i++){
		uidlist[i]=new Array();
		uidlist[i]['uid']=list[i];
	}
	pmb_rfid_driver3m.cmd("writeAFI",{'uidList':uidlist,'afi':afi},f_ack_antivol,afficheErreur);	
}


// Active / désactive tous les antivols
var rfid_antivol_all_data=new Array();

function init_rfid_antivol_all (level,ack_antivol) {
	f_ack_antivol=ack_antivol;

	pmb_rfid_driver3m=new rfid_3m_810(url_serveur_rfid);
	//pour enlever l'antivol
	rfid_antivol_level=level;
	pmb_rfid_driver3m.cmd("inventory",{'uidOnly':true,'getAFI':false},result_rfid_antivol_1,afficheErreur); 
}  

function result_rfid_antivol_1(retVal){	
	var afi= rfid_afi_security_off;
	if(rfid_antivol_level)afi= rfid_afi_security_active;
	pmb_rfid_driver3m.cmd("writeAFI",{'uidList':retVal,'afi':afi},result_rfid_antivol,afficheErreur);	
} 

function result_rfid_antivol(retVal){	
	f_ack_antivol(1);
	return;
	
} 


function effacer_ligne_tableau(array, valueOrIndex){
  var output=[];
  var j=0;
  for(var i in array){
    if (i!=valueOrIndex){
      output[j]=array[i];
      j++;
    }
  }
  return output;
} 
          


function mode1_init_rfid_read_cb(empr_client,expl_client){	
	f_empr_client=empr_client;
	f_expl_client=expl_client;
	// RFID init

	pmb_rfid_driver3m=new rfid_3m_810(url_serveur_rfid);
	mode1_read_cb();
}






// Pour le prêt a la chaine mode1

function mode1_read_cb() {		
	flag_semaphore_rfid_read=1;
	if(!rfid_focus_active) {setTimeout('mode1_read_cb()',1500); return;}
	

	pmb_rfid_driver3m.cmd("inventory",{'uidOnly':false,'getAFI':false},mode1_result_read_cb,afficheErreur);
	
}

function mode1_result_read_cb (retVal) {
	var i;
	var array_cb_expl=new Array();
	var array_cb_empr=new Array();
	var array_cb_index=new Array();
	var array_cb_count=new Array();
	var array_cb_eas=new Array();
	var array_cb_uid=new Array();
	var nb_doc=0;
	var nb_patroncard=0;		
	flag_semaphore_rfid_read=0;	
		
	for (i=0; i<retVal.length; i++) {	
		if(!retVal[i].error && retVal[i].cb){
			var uid=retVal[i].uid;
			if(retVal[i].type==1) {			
				array_cb_empr[nb_patroncard++]=retVal[i].cb;
			} else { 
				array_cb_expl[nb_doc]=retVal[i].cb;
				array_cb_index[nb_doc]=retVal[i].part;
				array_cb_count[nb_doc]=retVal[i].part_number;
				
				array_cb_uid[nb_doc]=uid;
				if(!get_uid_from_cb[retVal[i].cb])get_uid_from_cb[retVal[i].cb]=new Array();
				get_uid_from_cb[retVal[i].cb][get_uid_from_cb[retVal[i].cb].length]=uid;				
				nb_doc++;								
			}
		}	
	}
	if(f_expl_client)	f_expl_client(array_cb_expl,array_cb_index,array_cb_count,array_cb_eas,array_cb_uid);
	if(f_empr_client)	f_empr_client(array_cb_empr);
}





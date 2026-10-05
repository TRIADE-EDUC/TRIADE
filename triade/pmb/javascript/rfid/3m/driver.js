//Communication avec le webservice de la platine
var httpcom = function(url) {
	//URL du webservice
	this.url=url;
	
	//Focntion de rappel avec la réposne ou l'erreur !
	this.callback="";
	//Envoi d'une trame
	this.send=function(frame,callback,timeout) {
		this.callback=callback;
		if(typeof(netscape)!='undefined'){if(netscape.security.PrivilegeManager)netscape.security.PrivilegeManager.enablePrivilege('UniversalBrowserRead')};	
		var req_rfid = new http_request();
		var getUrl=url+"?cmd=cmd&frame="+frame;
		req_rfid.request(getUrl,0,"",1,pmbtk.c(this,"response"),pmbtk.c(this,"error"),0);	
	}
	
	//Réponse tout va bien !
	this.response=function(rawResponse) {
		var ret={
				response:rawResponse,
				error:0,
				errorMsg:""
		}
		this.callback(ret);
	}
	
	//Statut = erreur HTTP
	this.error=function(status,rawResponse) {
		var ret={
				response:"",
				error:status,
				errorMsg:rawResponse
		}
		this.callback(ret);
	}
};

var rfid_3m_810 = function(url) {
	this.url = url;
	this.callback="";
	this.callbackError="";
	this.sendCallback="";
	this.httpcom=new httpcom(url);
	this.params={};
	
	//Fonctions 3M
	//Init
	this.cmd_init=function(){
		switch(this.params.status) {
			case "init":
				this.params.status="get_version";
				this.sendCmd("D5","040011",pmbtk.c(this,"cmd_init"));
				break;
			case "get_version":
				this.params.finalResponse=this.params.response;
				this.params.status="end";
				this.sendCmd("D6","13040100020003000400",pmbtk.c(this,"cmd_init"));
				break;
			case "end":
				this.params.finalResponse+=" "+this.params.response;
				this.callback(this.params.finalResponse);
				break;
			default:
				break;
		}
	}
	
	
	//Inventaire
	this.cmd_inventory=function() {
		switch(this.params.status) {
			case "init":
				this.params.status="uid";
				this.sendCmd("D6","FE0005",pmbtk.c(this,"cmd_inventory"))
				break;
			case "uid":
				var uid_list=new Array();
				var nb = this.params.response.substring(8,8+2);
				nb=this.h2d(nb);
				if(nb>0){
					for(var i=0;i<nb;i++){			
						uid_list[i]=new Array();
						uid_list[i]['error']=0;
						uid_list[i]['uid']= this.params.response.substring((i*16)+10,(i*16)+10+16);			
					}			
				}else{
					this.callback(uid_list); //pas detiquette. on s'en va
					return;
				}
				if (this.params.params.uidOnly){
					this.callback(uid_list);
					return;
				}
				this.params.uidList=uid_list;
				this.params.cpt=0;
				this.params.nb=nb;
				this.params.status="read_data_init";				
				this.params.status="read_data";
				var uid=this.params.uidList[this.params.cpt]['uid'];	
				this.sendCmd("D6","02"+uid+"0008",pmbtk.c(this,"cmd_inventory"));
				break;
			case "read_data":
				var data ="";    
				var nb_block = this.params.response.substring(20,20+2);
				nb_block=this.h2d(nb_block);
				if(nb_block>0 && this.params.response.length>30){     
					var size_block=12;
					for(var i=0;i<nb_block;i++){
						i_start=22+(i*size_block)
						i_stop=22+(i*size_block)+size_block;
						data_block= this.params.response.substring(i_start,i_stop);	
						data+= this.params.response.substring(i_start+2+2,i_stop);	
					}					    	
					this.params.uidList[this.params.cpt]['cb']=this.hex2str(data.substring(32,data.length));
					this.params.uidList[this.params.cpt]['bib_id']=this.h2d(data.substr(8,10));
					this.params.uidList[this.params.cpt]['part']=this.h2d(data.substr(18,2));
					this.params.uidList[this.params.cpt]['part_number']=this.h2d(data.substr(20,2));
					this.params.uidList[this.params.cpt]['type']=this.h2d(data.substr(6,2)) & 3;
					this.params.uidList[this.params.cpt]['error']=0;
				} else{
					// etiquette en limite de porté, les data ne sont pas lues
					this.params.uidList[this.params.cpt]['error']=1;
				}
				this.params.cpt++;
				if(this.params.cpt < this.params.nb){
					// on va lire l'étiquette suivante
					this.params.status="read_data";
					var uid=this.params.uidList[this.params.cpt]['uid'];	
					this.sendCmd("D6","02"+uid+"0008",pmbtk.c(this,"cmd_inventory"));									
				}else{
					// plus d'étiquette à lire. on s'occupe de lire les AFI ?
					if (this.params.params.getAFI){						
						this.params.status="afi";
						// lecture du registre AFI ( antivol)
						var driver3m=new rfid_3m_810(this.url);
						driver3m.cmd("getAFI",{'uidList':this.params.uidList},pmbtk.c(this,"cmd_inventory"));
					}						
					else{// tout est lu 
						this.callback(this.params.uidList); // tout est lu 
					}
					return;
				}
				break;
			case "afi":
				// tout est lu 
				this.callback(this.params.uidList);
				break;
		}
	}
	

	// read afi
	this.cmd_getAFI=function() {
		switch(this.params.status) {
			case "init":
				this.params.cpt=0;
				this.params.uidList=this.params.params.uidList;
				this.params.nb=this.params.uidList.length;
				this.params.status="afi";
				var uid=this.params.uidList[this.params.cpt]['uid'];						
				this.params.status="afi";
				this.sendCmd("D6","0A"+uid, pmbtk.c(this,"cmd_getAFI"));	
	
				break;
			case "afi":
				this.params.uidList[this.params.cpt]['afi']=this.params.response.substr(20,2);
				this.params.cpt++;
				if(this.params.cpt < this.params.nb){
					this.params.status="afi";
					var uid=this.params.uidList[this.params.cpt]['uid'];	
					this.sendCmd("D6","0A"+uid,pmbtk.c(this,"cmd_getAFI"));						
				}else{
					// tout est lu 
					this.callback(this.params.uidList);
				}	
				break;
		}		
	}
	
	this.cmd_inventory_uid=function() {
		switch(this.params.status) {
			case "init":
				this.params.status="uid";
				this.sendCmd("D6","FE0005",pmbtk.c(this,"cmd_inventory_uid"))
				break;
			case "uid":
				this.params.uidList=new Array();
				var nb = this.params.response.substring(8,8+2);
				nb=this.h2d(nb);
				if(nb>0){
					for(var i=0;i<nb;i++){			
						this.params.uidList[i]=new Array();
						this.params.uidList[i]['error']=0;
						this.params.uidList[i]['uid']= this.params.response.substring((i*16)+10,(i*16)+10+16);			
					}			
				}				
				//console.log(this.params);				
				this.callback(this.params.uidList);	
				break;
		}		
	}

	//encode
	this.cmd_encode=function(){
		switch(this.params.status) {
			case "init":
				this.params.status="uid";
				this.sendCmd("D6","FE0005",pmbtk.c(this,"cmd_encode"))
				break;
			case "uid":
				this.params.uidList=new Array();
				var nb = this.params.response.substring(8,8+2);
				nb=this.h2d(nb);
				if(nb>0){
					for(var i=0;i<nb;i++){			
						this.params.uidList[i]=new Array();
						this.params.uidList[i]['error']=0;
						this.params.uidList[i]['uid']= this.params.response.substring((i*16)+10,(i*16)+10+16);			
					}			
				}else {
					// pas d'étiquette
					this.callback({"nb":nb, "info":"no tags"});
					return;
				}			
				if(this.params.params.type==1 && nb>1)	{
					// carte lecteur à programmer: trop détiquette 
					this.callback({"nb":nb, "info":"too many tags"});
					return;
				}
				var cb=this.str2hex(this.params.params.cb);
				this.params.cpt=0;
				this.params.nb=nb;
				this.params.status="write";		
				var uid=this.params.uidList[this.params.cpt]['uid'];
				if(this.params.params.type==1){	
					this.params.data="4652010106266762010101000000000000000000000000000000000000000000";// patron
				}else{
					this.params.data="4652010006266762010101000000000000000000000000000000000000000000";// document
					// parts
					this.params.data=this.params.data.substr(0,18)+ this.d2h(this.params.cpt+1) + this.d2h(this.params.nb) + this.params.data.substr(22,this.params.data.length);
				}
				this.params.data=this.params.data.substring(0,this.params.data.length - cb.length)+cb;		
				var data_blocks="";
				for(var i=0;i<8;i++){
					data_blocks+= this.params.data.substring(i*8,(i*8) +8);
				}				
				this.sendCmd("D6","04"+uid+"000800"+data_blocks, pmbtk.c(this,"cmd_encode"));
				
				break;
			case "write":
				this.params.cpt++;
				if(this.params.cpt < this.params.nb){
					// on va ecrire l'étiquette suivante
					this.params.status="write";

					var uid=this.params.uidList[this.params.cpt]['uid'];	
					// parts	
					this.params.data=this.params.data.substr(0,18)+ this.d2h(this.params.cpt+1) + this.d2h(this.params.nb) + this.params.data.substr(22,this.params.data.length);					
					var data_blocks="";
					for(var i=0;i<8;i++){
						data_blocks+= this.params.data.substring(i*8,(i*8) +8);
					}				
					this.sendCmd("D6","04"+uid+"000800"+data_blocks, pmbtk.c(this,"cmd_encode"));									
				}else{
					// plus d'étiquette à écrire. on s'occupe des antivol AFI pour les type document 
					if (this.params.params.type != 1 && this.params.params.afi){
						this.params.status="afi";
						// écriture du registre AFI ( antivol)
						var driver3m=new rfid_3m_810(this.url);
						driver3m.cmd("writeAFI",{'uidList':this.params.uidList,'afi':this.params.params.afi},pmbtk.c(this,"cmd_encode"));
					}						
					else this.callback(this.params.uidList); // tout est lu 
					return;
				}
				break;
			case "afi":
				// tout est ecrit 
				this.callback(this.params.uidList);
				break;
		}
	}
	
	// Write afi
	this.cmd_writeAFI=function() {
		switch(this.params.status) {
			case "init":
				this.params.cpt=0;
				this.params.uidList=this.params.params.uidList;
				this.params.afi=this.params.params.afi;
				this.params.nb=this.params.uidList.length;
				this.params.status="afi";
				var uid=this.params.uidList[this.params.cpt]['uid'];						
				this.params.status="afi";
				this.sendCmd("D6","09"+uid+this.params.afi, pmbtk.c(this,"cmd_writeAFI"));	
	
				break;
			case "afi":
				
				this.params.cpt++;
				if(this.params.cpt < this.params.nb){
					this.params.status="afi";
					var uid=this.params.uidList[this.params.cpt]['uid'];	
					this.sendCmd("D6","09"+uid+this.params.afi, pmbtk.c(this,"cmd_writeAFI"));						
				}else{
					// tout est ecrit 
					this.callback();
				}	
				break;
		}		
	}
	
	/*
	 * params = {
	 * 	cmd: commande à appeler
	 *  status : statut pour la commande
	 *  params : paramètres pour la commande en cours (structure libre)
	 *  response : réponse du webservice
	 *  finalResponse : réponse finale
	 * }
	 */
	this.cmd=function(cmd,params,callback,callbackError) {
		this.callback=callback;
		this.callbackError=callbackError;
		this.params={
				cmd:cmd,
				params:params,
				response:"",
				finalResponse:"",
				status:"init"
		}
		this["cmd_"+cmd]();
	}
	
	//Envoi d'une commande au webservice
	this.sendCmd=function(header,cmd,callback) {
		this.sendCallback=callback;
		this.httpcom.send(this.build_frame(header,cmd),pmbtk.c(this,"getResponse"),10);
	}
	
	//Récupération de la réponse
	this.getResponse=function(response) {
		if (!response.error) {
			//Nettoyage de la frame
			var ack = response.response;
			var size = ack.substring(4,6);
			size=this.h2d(size);	
			var frame = ack.substring(6,6+(size *2)-4);
			//On renvoie la réponse nettoyée
			this.params.response=frame;
			this.sendCallback();
		} else {
			//Arguments à définir
			this.callbackError();
		}
	}
	//Fonctions bas niveau pour la manipulation des Frames
	
	//Construction d'une Frame : header = classe de commandes, cmd = commande
	this.build_frame=function(header,cmd){
		var size_frame=(cmd.length/2)+2 ; // prise en compte crc
		var frame=header+"00"+this.d2h(size_frame)+cmd;
		var crc=this.gen_crc16("00"+this.d2h(size_frame)+cmd);
		return frame+crc;
	}
	
	//Décimal to hexa
	this.d2h=function(d) {	
		var val=d.toString(16);
		if(val.length==1){
			val='0'+val;
		}
		return val;
	}

	//Hexa to décimal
	this.h2d=function(h) {
		return parseInt(h,16);
	}

	//Conversion d'une chaine binaire en Hexadécimal lisible
	this.str2hex=function(str){
	    var r="";
	    var e=str.length;
	    var i=0;
	    var h;
	    while(i<e){
	        r+=this.d2h( str.charCodeAt(i++) );    
	    }
	    return r;
	}

	//Concersion hexadécimal lisible en chaine binaire
	this.hex2str=function(str){
	    var r="";
	    var e=str.length;
	    var s;
	    while(e>0){
	        s=e-2;
	        if(!(str.substring(s,e)=="ff" || str.substring(s,e)=="00"))
	        	r=String.fromCharCode("0x"+str.substring(s,e))+r;
	        e=s;
	    }
	    return r;
	}

	//Calcul du CRC d'une commande
	this.gen_crc16=function(frame){
		var Crc = 0xFFFF;
		var Polynome = 0x1021;
		
		var Adresse_tab=new Array();
		for(var i=0;i<frame.length/2;i++){
			Adresse_tab[i]="0x"+frame.substr(i*2,2);
		}
		for (var i= 0 ; i < Adresse_tab.length ; i++)	{
			Crc ^= Adresse_tab[i]<<8; 
			Crc&=0xFFFF;
			for ( var CptBit = 0; CptBit < 8 ; CptBit++){
				if(Crc & 0x8000)   Crc=(Crc<<1) ^ Polynome; 
				else Crc=(Crc<<1);
				Crc&=0xFFFF;
			} 
		}
		Crc^=0xFFFF;
		var tpl="0000";
		var crc_string=Crc.toString(16);	
		crc_string=tpl.substring(0,tpl.length-crc_string.length)+crc_string;	
		return crc_string;
	}
}
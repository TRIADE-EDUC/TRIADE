// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notification.js,v 1.6 2020/03/19 14:07:06 btafforeau Exp $

var notification = function (module,empty_msg,new_msg,new_icon,empty_icon){
	this.dom; //Objet DOM du div de notification...
	this.img; //Objet DOM de l'image de notification
	this.something_new = false; //quelque chose de nouveau?
	this.zone = false; 	//zone de notification
	this.current_module = module;
	this.new_msg = new_msg;
	this.empty_msg = empty_msg;
	this.new_icon = new_icon;
	this.empty_icon = empty_icon;
	
	//pseudo-constructeur, on attache la fonction init �  l'�v�nement onload de la page...
	window.addEventListener("load",pmbtk.c(this,"init"),false);
	this.init = function(){
		this.dom = document.getElementById('notification');
		if(this.dom){
			this.img = this.dom.getElementsByTagName('img').item(0);
			this.zone = document.getElementById("notification_zone");
			var ajax = new http_request();
			ajax.request("ajax.php?module="+this.current_module+"&categ=dashboard&sub=get_notifications_state",0,"",true,pmbtk.c(this,"got_notification_state"));
			this.load_icon();
			window.addEventListener('click',pmbtk.c(this,"open_close"),true);
		}
	}

	this.check_new_alert = function (struct){
		if(!this.something_new){
			var alert = document.getElementById("div_alert");
			var alert_zone = document.getElementById("alert_zone");
			if(struct.html){
				var div = document.createElement('div');
				div.innerHTML = struct.html;
				if(alert.innerHTML == div.innerHTML || alert_zone.innerHTML == div.innerHTML){
					this.something_new = false;
				}else{
					div.innerHTML = struct.separator+struct.html;
					if(alert.innerHTML == div.innerHTML || alert_zone.innerHTML == div.innerHTML){
						this.something_new = false;
					}else{
						this.something_new = true;
						this.save_new_notification();
					}
				}
			}
		}else{
			this.load_new_icon();
		}
		return this.something_new;
	}
	
	this.got_notification_state = function(response){
		if(response == 1){
			this.something_new = true;
		}else{
			this.something_new = false;
		}
		this.load_icon();		
	}
	
	this.save_new_notification = function(){
		var ajax = new http_request();
		ajax.request("ajax.php?module="+this.current_module+"&categ=dashboard&sub=save_new_notification",0,"",true,pmbtk.c(this,"new_notification_saved"));
	}
	
	this.new_notification_saved = function(response){
		if(response == 1){
			this.something_new = true;
		}else{
			this.something_new = false;
		}
		this.load_icon();
	}
	
	this.notification_readed = function(response){
		if(response == 1){
			this.something_new = false;
		}else{
			this.something_new = true;
		}
		this.load_icon();
	}	
	
	this.read_new_notification = function(){
		var ajax = new http_request();
		ajax.request("ajax.php?module="+this.current_module+"&categ=dashboard&sub=save_notification_readed",0,"",true,pmbtk.c(this,"notification_readed"));
	}
	
	this.load_icon = function(){
		if(this.something_new){
			this.load_new_icon();
		}else{
			this.load_empty_icon();
		}
	}
	
	this.load_new_icon = function() {
		this.img.src = this.new_icon;
		this.dom.title = this.new_msg;
		this.img.alt = this.dom.title;
		if (!this.dom.classList.contains('ui-active')) {
			this.dom.classList.add('ui-active');
		}
	}
	
	this.load_empty_icon = function() {
		this.img.src = this.empty_icon;
		this.dom.title = this.empty_msg;
		this.img.alt = this.dom.title;
		if (this.dom.classList.contains('ui-active')) {
			this.dom.classList.remove('ui-active');
		}
	}
	
	this.open_close = function(e){
		if(!this.is_a_child(e.target,"notification_zone")){
			if(this.is_a_child(e.target,"notification")){
				if(this.zone.className!= "") {
					this.close();
				}else{
					this.open();
				}
			}else if(this.zone.className!= "") {
				this.close();
			}
		}
	}
	
	this.close = function(){
		var alert = document.getElementById("div_alert");
		var alert_zone = document.getElementById("alert_zone");
		if(alert){
			document.getElementById("menu").appendChild(alert);
		}
		this.zone.className = "";
		this.load_icon();
	}
	
	this.open = function(){
		var alert = document.getElementById("div_alert");
		var alert_zone = document.getElementById("alert_zone");
		this.zone.className+= "zone_active"; 
		if(alert){
			alert_zone.appendChild(alert);
		}
		this.read_new_notification();
	}

	this.is_a_child = function(node,parent){
		do{
			if(node.id == parent){
				return true;
			}
			node = node.parentNode;
		}while(node.parentNode);
		return false;
	}
}
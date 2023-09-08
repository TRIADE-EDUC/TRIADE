function popup_modules() {
	this.couleur_texte = '#000000';
	this.couleur_fond = '#FBFFD9';
	this.largeur = 0;
	this.hauteur = 0;
	
	this.id_local = '';
	//this.div_principal = '';
	//this.div_texte = '';

	this.scrollTop = 0;
	this.scrollLeft = 0;
	
	this.window_x = 0;
	this.window_y = 0;
	this.document_x = 0;
	this.document_y = 0;

	this.taille_min_x = 0;
	this.taille_min_y = 0;

	this.div_x=0;
	this.div_y=0;

	this.getElementByIdPrefix = "";
	
	this.document_in_frame = false;
	
	
	
	// Afficher le message d'attente
	this.afficher = function(id_parent, str_message) {
		var tab_res;
		var str_html;
		
		// Generer le id unique
		if(this.id_local == "") {
			this.id_local = this.random_id();
			eval(this.id_local + " = this;");
		}
		
		// Preparer le HTML qui sera affiche dans la celule
		str_html  = '<table border="0" width="100%" cellpadding="0" cellspacing="0">';
		str_html += '	<tr>';
		str_html += '		<td align="center" valign="center">' + str_message + '</td>';
		str_html += '	</tr>';
		str_html += '</table>';
		
		// Preparer le tableau global
		obj_div_main = document.createElement("table");
		obj_div_main.style.position = "absolute";
		obj_div_main.id = this.id_local;	
		obj_div_main.cellPadding = 0;
		obj_div_main.cellSpacing = 0;
		obj_div_main.style.color = this.couleur_texte;
		obj_div_main.style.backgroundColor = this.couleur_fond;
		obj_div_main.style.border = "#000000 solid 1px";
		obj_div_main.style.margin = "1px";
		obj_div_main.style.zIndex = 9001;
		obj_div_main.style.display = "";
				
		// Ajouter une ligne
		var obj_ligne = obj_div_main.insertRow(0);

		// Ajouter une cellule
		var obj_cellule = obj_ligne.insertCell(0);
		obj_cellule.align = "center";
		obj_cellule.style.padding = "3px";
		
		// Ajouter le HML dans la cellule
		obj_cellule.innerHTML = str_html;

		// Ajouter le tableau global dans le document
		document.body.appendChild(obj_div_main)
		
		var tab_pos_dim_div = this.lire_dimensions(id_parent);
		
		document.getElementById(this.id_local).style.top = parseInt((tab_pos_dim_div["top"] + tab_pos_dim_div["height"]), 10) +  "px";
		document.getElementById(this.id_local).style.left = parseInt(tab_pos_dim_div["left"], 10) +  "px";
	}


	// Cacher le message d'attente
	this.cacher = function () {
		var obj_div_attente;
		if(this.id_local != "") {
			obj_div_attente = document.getElementById(this.id_local);
			if(obj_div_attente) {
				//eval(" res = clearInterval(int_timeout_attente_serveur_" + this.id_local + ");");
				document.body.removeChild(obj_div_attente);
			}
		}
	}

	// Generer un id unique
	this.random_id = function () {
		var str_available_char = new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
		var i;
		var str_id = "";
		var int_pos;
		for(i=1;i<=32;i++) {
			int_pos = Math.floor((16)*Math.random()) + 0;
			str_id += str_available_char[int_pos];
		}
		return("_" + str_id);
	}
	
	// Lire la position d'un objet ainsi que les dimensions de la fenetre et du document
	// Valeurs retournees :
	//		- tab["left"] : x position of the object
	//		- tab["top"] : y position of the object
	//		- tab["width"] : width of the object
	//		- tab["height"] : height of the object
	//		- tab["window_width"] : width of the window		
	//		- tab["window_height"] : height of the window		
	//		- tab["document_width"]	 : width of the document	
	//		- tab["document_height"] : height of the document		
	//		- tab["scroll_width"] : x scroll position
	//		- tab["scroll_height"] : y scroll position
	this.lire_dimensions = function (id) {
		var curleft = curtop = 0;
		var res=Array();
		
		var curleft = curtop = 0;
		var res=Array();
		
		var window_x;
		var window_y;
		var document_x;
		var document_y;
		var scroll_x;
		var scroll_y;
		
		if(id != "") {
			obj =document.getElementById(id);
	
			if(obj) {
				if (obj.offsetParent) {
					try {
						curleft_tmp = obj.offsetLeft;
					}
					catch(e) {
						curleft_tmp = 0;
					}
					curleft = curleft_tmp;
					try {
						curtop_tmp = obj.offsetTop;
					}
					catch(e) {
						curtop_tmp = 0;
					}
					curtop = curtop_tmp;
					valid = true;
					try {
						obj = obj.offsetParent;
					}
					catch(e) {
						valid = false;
					}
					
					while (valid) {
			
						try {
							curleft_tmp = obj.offsetLeft;
						}
						catch(e) {
							curleft_tmp = 0;
						}
						try {
							curtop_tmp = obj.offsetTop;
						}
						catch(e) {
							curtop_tmp = 0;
						}
			
			
						curleft += curleft_tmp;
						curtop += curtop_tmp;
						
						try {
							obj = obj.offsetParent;
						}
						catch(e) {
							valid = false;
						}
			
					}
				}
				
				try {
					var html_elemento = document.getElementById(id);
					curwidth = parseInt(html_elemento.offsetWidth,10);
				}
				catch(e) {
					curwidth = 0;
				}
				
				try {
					var html_elemento = document.getElementById(id);
					curheight = parseInt(html_elemento.offsetHeight,10);
				}
				catch(e) {
					curheight = 0;
				}
				
				try {
					
					if (navigator.appName == "Microsoft Internet Explorer"){
						window_x = document.body.clientWidth;
						window_y = document.body.clientHeight;
						document_x = document.body.clientWidth;
						document_y = document.body.clientHeight;
						if(window_x == 0) {
							window_x = document.documentElement.clientWidth;
							window_y = document.documentElement.clientHeight;
							document_x = document.body.clientWidth;
							document_y = document.body.clientHeight;		
						}
					} else {
						window_x = document.documentElement.clientWidth;
						window_y = document.documentElement.clientHeight;
						document_x = document.body.clientWidth;
						document_y = document.body.clientHeight;		
					}
					
					//alert("window_x=" + this.window_x);
				}
				catch(err) {
					window_x = 0;
					window_y = 0;
					document_x = 0;
					document_y = 0;
				}
			} else {
				window_x = 0;
				window_y = 0;
				document_x = 0;
				document_y = 0;
			}
		} else {
			window_x = 0;
			window_y = 0;
			document_x = 0;
			document_y = 0;
		}
		
		// Get maximum x dimension
		if(window_x>document_x) {
			res["window_width"]=document_x;
			res["document_width"]=window_x;
		} else {
			res["window_width"]=window_x;
			res["document_width"]=document_x;
		}
	
		// Get maximum y dimension
		if(window_y>document_y) {
			res["window_height"]=document_y;
			res["document_height"]=window_y;
		} else {
			res["window_height"]=window_y;
			res["document_height"]=document_y;
		}		

		if (navigator.appName == "Microsoft Internet Explorer"){
			scroll_x = document.documentElement.scrollLeft;
			if(scroll_x == 0) {
				scroll_x = document.body.scrollLeft;
			}
			scroll_y = document.documentElement.scrollTop;
			if(scroll_y == 0) {
				scroll_y = document.body.scrollTop;
			}
		} else { 
			scroll_x = window.pageXOffset; 
			scroll_y = window.pageYOffset; 
		}
		
		res["left"]=curleft;
		res["top"]=curtop;
		res["width"]= parseInt(curwidth,10);
		res["height"]= parseInt(curheight,10);
		
		res["scroll_width"] = scroll_x;
		res["scroll_height"] = scroll_y;
		
		return res;
	}	



	
}
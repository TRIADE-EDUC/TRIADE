



function ajax_recherche() {
	
	var type_recherche;
	var id_formulaire;
	var id_champ_critere_1;
	var id_champ_critere_2;
	var id_champ_critere_3;
	var id_champ_critere_4;
	var id_champ_critere_5;
	var mode_resultat;
	var id_conteneur_resultat;
	var url_module;
	
	var msg_validation_1;
	var msg_validation_2;
	var msg_validation_3;
	
	var obj_ajax_recherche;
	var id_local;
	
	var fonction_onclick;
	// Initialiser
	this.init = function() {
		this.id_local = this.random_id();
		eval(this.id_local + " = this;");
		
		// Valeurs par defaut
		this.type_recherche = 'eleve';
		this.id_formulaire = 'formulaire';
		this.id_champ_critere_1 = 'critere_1';
		this.id_champ_critere_2 = '';
		this.id_champ_critere_3 = '';
		this.id_champ_critere_4 = '';
		this.id_champ_critere_5 = '';
		this.mode_resultat = 'TABLE_DANS_DIV';
		this.id_conteneur_resultat = 'resultat_recherche';
		this.url_module = '';
		this.msg_validation_1 = 'Votre session a expiré';
		this.msg_validation_2 = 'Erreur dans le script appelé';
		this.msg_validation_3 = 'Erreur communication avec serveur';
		this.obj_ajax_recherche = null;
		this.fonction_onclick = '';
	}
	
	// Lancer la recherche
	this.rechercher = function() {
		var obj_champ_critere_1 = null;
		var str_champ_critere_1 = '';
		var obj_zone_affichage = null;
		var obj_ligne;
		var obj_cellule;
		var str_html = '';
		var obj_parent;
		

		// recuperer le champ de formulaire contenant le premier critere de recherche
		try {
			eval("obj_champ_critere_1 = document." + this.id_formulaire + "." + this.id_champ_critere_1 + ";");
		}
		catch(e) {
			obj_champ_critere_1 = null;
		}

		// Recuperer le contenu du champ
		if(obj_champ_critere_1 != null) {
			switch(obj_champ_critere_1.type) {
				case "text" :
				case "TEXT" :
					str_champ_critere_1 = this.trim(obj_champ_critere_1.value);
					break;
				case "select" :
				case "SELECT" :
					if(obj_champ_critere_1.selectedIndex >= 0) {
						str_champ_critere_1 = obj_champ_critere_1.options[obj_champ_critere_1.selectedIndex].value;
					} else {
						str_champ_critere_1 = '';
					}
					break;
			}
		}
					
		
		// Recuperer le resultat de la recherche (par Ajax)
		if(obj_champ_critere_1 != null) {
			//str_html = str_champ_critere_1;
			
			if(trim(str_champ_critere_1) != '') {
				this.obj_ajax_recherche = new Ajax();
				// Parametres de l'Ajax
				this.obj_ajax_recherche.setParam ({
					url : this.url_module + "/ajax_recherche.php",
					returnFormat : "txt",
					method : "POST",
					data : {
						type_recherche : this.type_recherche,
						critere_1 : str_champ_critere_1,
						critere_2 : '',
						critere_3 : '',
						critere_4 : '',
						critere_5 : '',
						fonction_onclick : this.fonction_onclick
					},
					asynchronus : false,
					onComplete : this.id_local + ".recherche_reussite(response)",
					onFailure : this.id_local + ".recherche_echec(errorCode)"
					
				});
							
				// Appeler l'Ajax
				this.obj_ajax_recherche.execute();
			} else {
				this.afficher_resultat('');
			}
			
		}
			

		/*
		
		onComplete : this.id_local + ".recherche_reussite(response, '" + this.id_conteneur_resultat + "')",
		*/		

	}
	
	
	this.ltrim = function(s) {
	   return s.replace(/^\s+/, "");
	}
	this.rtrim = function(s) {
	   return s.replace(/\s+$/, "");
	}
	
	this.trim = function(s) {
	   return this.rtrim(this.ltrim(s));
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


	this.recherche_reussite = function (response) {
		var total_enregistrements = 0;
		var donnees = new String(response);
							
		// Decoupage de la reponse (envoyee par le script Ajax)
		donnees_decoupees = donnees.split('¬');
		
		// alert('reponse=' + response);
		//document.getElementById('resultat_recherche').innerHTML = response;
		// alert(donnees_decoupees[2]);
		
		switch(donnees_decoupees[0]) {
			case '0': // Pas d'erreur
				if(donnees_decoupees[1] > 0) {
					this.afficher_resultat(donnees_decoupees[2]);
				} else {
					this.afficher_resultat('');
				}
				break;
				
			case '99': // L'utilisateur n'est pas autorise a executer le script (pas le droit ou plus authentifie)
				this.afficher_resultat('');
				alert(this.msg_validation_1);
				break;
				
			default: // Erreur inconuue
				this.afficher_resultat('');
				// Remplacer la liste deroulante par la nouvelle
				alert(this.msg_validation_2);
		}
		
	}
	
	this.recherche_echec = function (errorCode) {
		this.afficher_resultat('');
		alert(this.msg_validation_3);
	}
	
	this.afficher_resultat = function(str_html) {
		if(str_html != '') {
			obj_parent = document.getElementById(this.id_conteneur_resultat);
			if(obj_parent != null) {
				obj_parent.innerHTML = str_html;
			}
		} else {
			obj_parent = document.getElementById(this.id_conteneur_resultat);
			if(obj_parent != null) {
				obj_parent.innerHTML = '';
			}

		}
	}
						
}
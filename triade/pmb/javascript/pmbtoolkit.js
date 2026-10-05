var pmbtk={
		c:function(obj,method) {
			return function() {
				if (typeof(method)=="string")
					obj[method].apply(obj,arguments);
				else method.apply(obj,arguments);
			}
		},
		defineClass:function(className,heritedFrom,definition) {
			var classe=function() {
				//Copie de la définition
				for (var m in definition) {
					this[m]=definition[m];
				}

				//Héritage ?
				if (heritedFrom) {
					//Création de la méthode parent : elle renvoie une méthode du parent dans le contexte de l'instance et non du prototype
					this.parent=function(parentMethod) {
						return pmbtk.c(this,window[className].prototype[parentMethod]);
					}
				}
				
				this.construct.apply(this,arguments);
			}
			//Création de la fonction dans l'espace global
			window[className]=classe;
			
			//Héritage ? Si oui, on charge le prototype !
			if (heritedFrom) {
				parentClass=window[heritedFrom];
				//Ajout des méthodes du parent au prototype
				window[className].prototype=new parentClass();
				//Ajout des méthodes du prototype du parent au prototype
				for (var p in parentClass.prototype){
					if((typeof(parentClass.prototype[p]=="function"))&&(!window[className].prototype[p])) {
						window[className].prototype[p]=parentClass.prototype[p];
					}	
				}
			}
		}	
}



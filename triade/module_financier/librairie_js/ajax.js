	/* Classe pour AJAX Par HoLyVieR aka Arto_8000	*/

	// Synthaxe : ErreurObj ([String Message]) Objet
	// Objet pour la gestion d'erreur
	function ErreurObj(txt)
	{
		// Variable //
		this.txt = txt;
		this.caller = getCaller(ErreurObj.caller);
		
		// Fonction //
		this.toString = function () {
		return "Erreur : " + this.txt + " Fonction : " + this.caller;
		}
	}
	
	// Synthaxe : isset ([Variable]) Boolean
	// Même chose que en PHP
	function isset(toTest)
	{
		return (typeof toTest != "undefined");
	}
	
	// Synthaxe : Array.inArray ([String ChercheQuoi]) Boolean
	// Chercher pour une chaine particuliere dans un tableau
	function inArray(text)
	{
		for (a=0;a<this.length;a++)
		{
			if(this[a] == text)
			{
				return true;
			}
		}
	}
	Array.prototype.inArray = inArray;
	
	//  Synthaxe : getCaller ([String fonction.caller]) String
	// Retourne le nom de la fonction appartir de l'attribut caller //
	function getCaller(rawCaller)
	{
		rawCaller = rawCaller.toString();
		if (rawCaller == null)
		return "";
		
		return rawCaller.substring(rawCaller.indexOf("function ") + 9,rawCaller.indexOf("(")).replace(" ","");
	}
	
	// Synthaxe : Ajax () Objet
	// Objet centrale de la classe
	function Ajax()
	{
		// Variable //
		this.asyn = true;
		this.data = "";
		this.url = "";
		this.method = "GET";
		this.returnFormat = "txt";
		this.timeout = 0;
		this.obj;
		this.init();
		this.debug = false;
	}
	
	// Synthaxe : function("")
	// Crée l'objet XMLHttpRequest //
	Ajax.prototype.showError = function(errorCode)
	{
		alert("<?php echo strtoupper(_ERROR); ?>\n\n   - Script : " + this.url + "\n   - Code/Msg : " + errorCode + "\n\n" + "<?php echo _AJAX_CONTACT_DBA; ?>");
	}
	
	// Synthaxe : httprequest () Objet XMLHttpRequest
	// Crée l'objet XMLHttpRequest //
	Ajax.prototype.init = function()
	{
		this.obj = null;
		if (window.XMLHttpRequest)
			this.obj = new XMLHttpRequest();
		else if (window.ActiveXObject)  // if IE
		{
			var ieversions = ['Msxml2.XMLHTTP','Microsoft.XMLHTTP','Msxml2.XMLHTTP.5.0','Msxml2.XMLHTTP.4.0','Msxml2.XMLHTTP.3.0'];
	
			for(var i=0; !this.obj && i<ieversions.length; i++)
			{
				try
				{
					this.obj = new ActiveXObject(ieversions[i]);
				}
				catch(e)  { }
			}
		}
	}
	
	// Lorsque la requête ne réussit pas
	Ajax.prototype.onFailure = function (errorCode)
	{
		// ...
	}
	
	// Lorsque la requête réussit
	Ajax.prototype.onComplete = function (response)
	{
		// ...
	}
	
	// Synthaxe : setParamFromForm ([HTML Form || String Name || Int Index])
	// Ajoute les paramètres et les données d'un formulaire à celui de la requête
	Ajax.prototype.setParamFromForm = function (obj)
	{
		if (!isNaN(obj))
		obj = document.forms[obj];
		
		if (typeof obj == "string")
		eval("obj = document."+obj);
		
		if (!isset(obj))
		{
			return ErreurObj("Donnée Invalide");
		}
		
		this.method = (isset(obj.method) && (["GET","POST"].inArray(obj.method.toUpperCase()))) ? obj.method.toUpperCase() : this.method;
		this.url = obj.action;
		
		for (i=0;i<obj.elements.length;i++)
		{
			if (["file","button","reset","submit"].inArray(obj.elements[i].type.toLowerCase()))
			continue;
			
			if (this.data != null)
			this.data += "&";
			
			this.data += obj.elements[i].name + "=" + escape(obj.elements[i].value);
		}
	}
	
	// Synthaxe : setParam ([Array data])
	// Ajoute les paramètres à la requête à partir d'un tableau
	Ajax.prototype.setParam = function (arr)
	{
	
		if (typeof arr != "object" && !isset(arr))
		{
			return ErreurObj("Donnée Invalide");
		}
		
		for(k in arr)
		{
			switch (k)
			{
				case "url" : this.url = arr[k]; break;
				case "method" : this.method = (["GET","POST"].inArray(arr[k].toUpperCase())) ? arr[k].toUpperCase() : this.method; break;
				case "data" :
				if (typeof arr[k] == "string")
				{
					if (this.data != "")
					this.data += "&";
					this.data += arr[k];
				}
				else
				{
					if (typeof arr[k] != "object")
					break;
					
					for (j in arr[k])
					{
						if (this.data != "")
						this.data += "&";
						this.data += j + "=" + escape(arr[k][j]);
						// alert(escape(arr[k][j]));
					}
				}
				break;
				case "asynchronus" : this.asyn = arr[k]; break;
				case "onComplete" : this.onComplete = arr[k];break;
				case "onFailure" : this.onFailure = arr[k];break;
				case "returnFormat" : this.returnFormat = arr[k];break;
				case "timeout" : this.timeout = arr[k];break;
			}
		}
	}
	
	// Synthaxe : Function execRequest () //
	// Exécute la requête, ainsi que le callback
	Ajax.prototype.execute = function ()
	{
		if(this.timeout > 0) {
			this.obj.timeout = this.timeout;
		}
		this.obj.open(this.method,this.url,this.asyn);
		
		if (this.method == "POST")
		this.obj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		
		if (this.asyn)
		{
			_tempAJAX_Reference_ = this; // Crée une copie de l'objet AJAX courant pour pouvoir le récupérer après //
			
			this.obj.onreadystatechange = function () {
				if (_tempAJAX_Reference_.obj.readyState == 4 && _tempAJAX_Reference_.obj.status == 200)
				{
					// alert(_tempAJAX_Reference_.obj.responseText);
					if (_tempAJAX_Reference_.returnFormat != "txt")
					response = _tempAJAX_Reference_.obj.responseXML;
					
					else
					response = _tempAJAX_Reference_.obj.responseText;

					if (_tempAJAX_Reference_.returnFormat != "txt" && response == null) {
						alert("Reponse XML incorrecte : \n" + _tempAJAX_Reference_.obj.responseText);
						errorCode = _tempAJAX_Reference_.obj.status;
						
						if (typeof _tempAJAX_Reference_.onFailure == "string") {
							eval(_tempAJAX_Reference_.onFailure);
						}
						else
						{
							_tempAJAX_Reference_.onFailure(errorCode);
						}

					} else {
						if (typeof _tempAJAX_Reference_.onComplete == "string")
						eval (_tempAJAX_Reference_.onComplete);
						else
						_tempAJAX_Reference_.onComplete(response);
					}
				}
				else if (_tempAJAX_Reference_.obj.readyState == 4)
				{
					errorCode = _tempAJAX_Reference_.obj.status;
					
					if (typeof _tempAJAX_Reference_.onFailure == "string") {
					//alert(_tempAJAX_Reference_.onFailure);	
					eval(_tempAJAX_Reference_.onFailure);
					}
					else
					{
					_tempAJAX_Reference_.onFailure(errorCode);
					}
				}
			}
			this.obj.send(this.data);
		}
		else
		{
			this.obj.send(this.data);
			if (this.obj.status == "200")
			{
				if (this.returnFormat != "txt")
				response = this.obj.responseXML;
				else
				response = this.obj.responseText;
				

				if (typeof this.onComplete == "string")
				eval (this.onComplete);
				else
				this.onComplete(response);
				
			}
			else
			{
				errorCode = this.obj.status;
				if (typeof this.onFailure == "string")
				eval (this.onFailure);
				else
				this.onFailure(errorCode);
			}
		}
		
	}
	
	
	
	// Synthaxe : getParam ([String param])
	// Get the value of a parameter comming from the XML
	Ajax.prototype.getParam = function (param, default_value)
	{
		try {
			var docXML= this.obj.responseXML; // ici
			var items = docXML.getElementsByTagName(param)
			param_value = items.item(0).firstChild.data;
		}
		catch(e) {
			param_value = default_value;
		}
		return(param_value);
		//on fait juste une boucle sur chaque élément "donnee" trouvé
		//for (i=0;i<items.length;i++)
		//{
		//	alert (items.item(i).firstChild.data);
		//}
	}
	
	// Synthaxe : getProperty ([String param], [String property])
	// Get the value of a property of a parameter comming from the XML
	Ajax.prototype.getProperty = function (param, property)
	{
		//alert(docXML.getElementsByTagName("fichier_1")[0].getAttribute("hauteur"));

		var docXML= this.obj.responseXML; // ici
		var items = docXML.getElementsByTagName(param)
		try {
			//property_value = items[0].getAttribute(property);
			property_value = docXML.getElementsByTagName(param)[0].getAttribute(property);
		}
		catch(e) {
			property_value = "undefined";
		}
		
		return(property_value);
	}	
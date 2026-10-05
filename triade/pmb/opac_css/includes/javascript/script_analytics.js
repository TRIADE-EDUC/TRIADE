// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: script_analytics.js,v 1.8 2021/03/09 14:52:02 arenou Exp $


var scriptAnalytics = {}

scriptAnalytics.CookieConsent = function() {
	// Remplacez la valeur UA-XXXXXX-Y par l'identifiant analytics de votre site.
    var gaProperty = 'UA-XXXXXX-Y '
    // D�sactive le tracking si le cookie d'Opt-out existe d�j� .
    var disableStr = 'ga-disable-' + gaProperty;
    var firstCall = false;

    //Cette fonction retourne la date d'expiration du cookie de consentement 
    function getCookieExpireDate() { 
    	// Le nombre de millisecondes que font 13 mois 
    	var cookieTimeout = 33696000000;
    	var date = new Date();
    	date.setTime(date.getTime()+cookieTimeout);
    	var expires = "; expires="+date.toGMTString();
    	return expires;
    }

    // Fonction utile pour r�cup�rer un cookie � partir de son nom
    function getCookie(NameOfCookie)  {
        if (document.cookie.length > 0) {        
        	begin = document.cookie.indexOf(NameOfCookie+"=");
	        if (begin != -1)  {
	            begin += NameOfCookie.length+1;
	            end = document.cookie.indexOf(";", begin);
	            if (end == -1) end = document.cookie.length;
	            return unescape(document.cookie.substring(begin, end)); 
	        }
        }	
        return null;
    }
    
    // Fonction d'effacement des cookies   
    function delCookie(name )   {
        var path = ";path=" + "/";
        var hostname = document.location.hostname;
        if (hostname.indexOf("www.") === 0)
            hostname = hostname.substring(4);
        var domain = ";domain=" + "."+hostname;
        var expiration = "Thu, 01-Jan-1970 00:00:01 GMT";       
        document.cookie = name + "=" + path + domain + ";expires=" + expiration;
    }
    
    //La fonction qui informe et demande le consentement. Il s'agit d'un div qui apparait au centre de la page
    /*function renderInformAndAsk() {
    	var scriptAnalyticsElement = document.getElementById('script_analytics');
    	var div = document.createElement('div');
        div.setAttribute('id','inform_and_ask');
        div.style.width= window.innerWidth+"px" ;
        div.style.height= window.innerHeight+"px";
                
        //Le code HTML de la demande de consentement
        div.innerHTML = "<div id='inform_and_ask_content'><div><span><b>"+msg_script_analytics_inform_title+"</b></span></div><br>" +
        		"<div>"+msg_script_analytics_inform_content+"</div>" +
				"<div style='padding :10px 10px;text-align:center;'>" +
				"<button style='margin-right:50px;text-decoration:underline;' name='opposite' onclick='scriptAnalytics.CookieConsent.opposite();scriptAnalytics.CookieConsent.hideInform();' id='opposite-button' >"+msg_script_analytics_inform_ask_opposite+"</button>" +
				"<button style='text-decoration:underline;' name='accept' onclick='scriptAnalytics.CookieConsent.accept();scriptAnalytics.CookieConsent.hideInform()' id='accept-button'>"+msg_script_analytics_inform_ask_accept+"</button></div>" +
				"</div>" +
				"</div>";
        scriptAnalyticsElement.appendChild(div);
    }*/
    
    //Affichage
    function render() {
    	var bodytag = document.getElementsByTagName('body')[0];
        var div = document.createElement('div');
        div.setAttribute('id','script_analytics');
        div.setAttribute('align','center');
        // Le code HTML de la demande de consentement
        link_more_insert = '';
        if (script_analytics_content_link_more!='') {
        	link_more_insert = "<a href='"+script_analytics_content_link_more+"' target='_blank'>"+script_analytics_content_link_more_msg+"</a>";
        }
        div.innerHTML = "<div id='script_analytics_content'>"+msg_script_analytics_content+" "+link_more_insert+
        "<button style='margin-left:50px;text-decoration:underline;' name='opposite' onclick='scriptAnalytics.CookieConsent.opposite();' id='opposite-button' >"+msg_script_analytics_inform_ask_opposite+"</button>" +
    	"<button style='text-decoration:underline;' name='accept' onclick='scriptAnalytics.CookieConsent.accept();' id='accept-button'>"+msg_script_analytics_inform_ask_accept+"</button>" +
        "</div>";
        bodytag.appendChild(div);
//        renderInformAndAsk();
    }
    
    function renderNotToTrack() {
    	var bodytag = document.getElementsByTagName('body')[0];
        var div = document.createElement('div');
        div.setAttribute('id','script_analytics');
        div.setAttribute('align','center');
        div.innerHTML = "<div id='script_analytics_content'>"+pmbDojo.messages.getMessage("opac","opac_dnt_enabled")+
        "<button style='text-decoration:underline;' name='dnt-confirm' onclick='scriptAnalytics.CookieConsent.dntConfirm();' id='dnt-confirm-button'>"+msg_script_analytics_button_dnt_confirm+"</button>" +
        "</div>";
        bodytag.appendChild(div);
    }
          
    //R�cup�re la version d'Internet Explorer, si c'est un autre navigateur la fonction renvoie -1
    function getInternetExplorerVersion() {
    	var rv = -1;
    	if (navigator.appName == 'Microsoft Internet Explorer')  {
    		var ua = navigator.userAgent;
    		var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    		if (re.exec(ua) != null)
    			rv = parseFloat( RegExp.$1 );
    	}  else if (navigator.appName == 'Netscape')  {
    		var ua = navigator.userAgent;
    		var re  = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
    		if (re.exec(ua) != null)
    			rv = parseFloat( RegExp.$1 );
    	}
    	return rv;
	}

    //Effectue une demande de confirmation de DNT pour les utilisateurs d'IE
    function askDNTConfirmation() {
        var r = confirm("La signal DoNotTrack de votre navigateur est activ�, confirmez vous activer \
        la fonction DoNotTrack?")
        return r;
    }

    //V�rifie la valeur de navigator.DoNotTrack pour savoir si le signal est activ� et est �  1
    function notToTrack() {
        if ( (navigator.doNotTrack && (navigator.doNotTrack=='yes' || navigator.doNotTrack=='1'))
            || ( navigator.msDoNotTrack && navigator.msDoNotTrack == '1') ) {
            var isIE = (getInternetExplorerVersion()!=-1)
            if (!isIE){    
                 return true;
            }
            return false;
        }
    }

    //Si le signal est �  0 on consid�re que le consentement a d�j�  �t� obtenu
    function isToTrack() {
        if ( navigator.doNotTrack && (navigator.doNotTrack=='no' || navigator.doNotTrack==0 )) {
            return true;
        }
    }
       
    // Efface tous les types de cookies utilis�s par Google Analytics    
    function deleteAnalyticsCookies() {
        var cookieNames = ["__utma","__utmb","__utmc","__utmt","__utmv","__utmz","_ga","_gat"]
        for (var i=0; i<cookieNames.length; i++)
            delCookie(cookieNames[i])
    }

    return {
        
    	accept: function() {
    		let  secure = location.protocol=='https:'? '; secure=true' : '';
    		document.cookie = disableStr + '=true;'+ getCookieExpireDate() +' ; path=/'+secure;      
    		document.cookie = 'PhpMyBibli-COOKIECONSENT=true;'+ getCookieExpireDate() +' ; path=/'+secure;
    		var div = document.getElementById('script_analytics');
    		// Message affich� apr�s que l'utilisateur est accept�
    		if ( div!= null ) div.innerHTML = '';
    		window[disableStr] = false;
//    		window.location.reload();
    	},
       
    	opposite: function() {
    		let  secure = location.protocol=='https:'? '; secure=true' : '';
    		document.cookie = disableStr + '=true;'+ getCookieExpireDate() +' ; path=/'+secure;
	        document.cookie = 'PhpMyBibli-COOKIECONSENT=false;'+ getCookieExpireDate() +' ; path=/'+secure
	        var div = document.getElementById('script_analytics');
	        // Message affich� apr�s que l'utilisateur se soit oppos�
	        if ( div!= null ) div.innerHTML = ''
	        window[disableStr] = true;
	        deleteAnalyticsCookies();
	    },
	    
	    dntConfirm: function() {
	    	let  secure = location.protocol=='https:'? '; secure=true' : '';
    		document.cookie = disableStr + '=true;'+ getCookieExpireDate() +' ; path=/'+secure;   
	        document.cookie = 'PhpMyBibli-COOKIECONSENT=false;'+ getCookieExpireDate() +' ; path=/'+secure;
    		var div = document.getElementById('script_analytics');
    		// Message affiché après que l'utilisateur est accepté
    		if ( div!= null ) div.innerHTML = '';
    		window[disableStr] = true;
    	},
        
	    showInform: function() {
	    	var div = document.getElementById("inform_and_ask");
            div.style.display = "block";
	    },
          
	    hideInform: function() {
	    	var div = document.getElementById("inform_and_ask");
            div.style.display = "none";
            var div = document.getElementById("script_analytics");
            div.style.display = "none";
        },
        
        start: function() {
            //V�rifie que le consentement n'a pas d�j� �t� obtenu avant d'afficher
            var consentCookie =  getCookie('PhpMyBibli-COOKIECONSENT');
            clickprocessed = false;
            if (!consentCookie) {
                //L'utilisateur n'a pas encore de cookie, on affiche la banni�re. 
                if ( notToTrack() ) {
                    //L'utilisateur a activ� DoNotTrack. Do not ask for consent and just opt him out
                    scriptAnalytics.CookieConsent.opposite();
//                    alert(pmbDojo.messages.getMessage("opac","opac_dnt_enabled"))
                    if (window.addEventListener) {
	                  window.addEventListener("load", renderNotToTrack, false);
                    } else {
                      window.attachEvent("onload", renderNotToTrack);
                    }
                } else {
                    if (!isToTrack() ) { 
	                    if (window.addEventListener) {
	                      window.addEventListener("load", render, false);
	                    } else {
	                      window.attachEvent("onload", render);
	                    }
                    }
                }
            } else {
                if (document.cookie.indexOf('PhpMyBibli-COOKIECONSENT=false') > -1) 
                    window[disableStr] = true;
                else 
                    window[disableStr] = false;
            }
        }
    }

}();
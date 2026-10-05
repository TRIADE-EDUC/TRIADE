// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.js,v 1.1.2.2 2025/03/06 14:16:24 dgoron Exp $

function monitorSession(duration) {
	const date = new Date();
	const timeOutdated = date.getTime() + duration; // Delai d'1 minute pour repondre
    const timeDelay = duration - 60 * 1000; // 1 minute avant expiration
        
    //Stockage temporaire des messages pour ne pas prolonger la session lorsque le setTimeout demarre
    //pmbDojo.messages.getMessage genere une requete AJAX si les messages ne sont pas encore connus
    let sessionExpireNear = pmbDojo.messages.getMessage('common', 'session_expire_near');
    let sessionExtended = pmbDojo.messages.getMessage('common', 'session_extended');
    let sessionOutdated = pmbDojo.messages.getMessage('common', 'session_outdated');
    setTimeout(() => {
        if (confirm(sessionExpireNear)) {
        	let currentDate = new Date();
        	if (currentDate.getTime() <= timeOutdated) {
        		fetch('./ajax.php?module=ajax&categ=session&action=increase_connect_time', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'no-cache': true
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (parseInt(data.time_expired) == 0) {
//                        alert(sessionExtended);
                        monitorSession(duration); // Relancer le suivi
                    } else {
                    	alert(sessionOutdated);
                    }
                })
                .catch(error => console.error("Erreur lors de la prolongation de session :", error));
        	} else {
        		alert(sessionOutdated);
        	}
        }
    }, timeDelay);
}

function activeNotification() {
	if (Notification.permission !== 'granted') {
	        Notification.requestPermission();
        }

	var notification = new Notification('TRIADE-NOTIF', {
//        	icon: '/image/commun/triade-ico.gif',
        	icon: 'image/commun/icone-triade.png',
                body: 'Bonjour, les notifications sont maintenant actives.'
	});
	document.getElementById('notifier-btn').style.display="none";

	var requete = getRequete2();
        if (requete != null) {
                requete.open("POST","notification.php",true);
                requete.onreadystatechange = function() {
                        if(requete.readyState == 4) {
                                if(requete.status == 200) {
					// ok
                                }
                        };
                }
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send();
        }
	
}

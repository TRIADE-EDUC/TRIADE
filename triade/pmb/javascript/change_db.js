/* +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: change_db.js,v 1.2 2023/04/06 14:55:09 dbellamy Exp $ */

function change_db(selected_db){
    
    try {
        var http=new http_request();
        var post_data='selected_db='+selected_db;
        var url = './change_db.php';
        http.request(url,true,post_data);
        var myObject = JSON.parse(http.get_text());

        if (myObject['nb_docs']) {
            document.getElementById('extra_nb_docs').firstChild.nodeValue=myObject['nb_docs'];
        } else {
        	document.getElementById('extra_nb_docs').firstChild.nodeValue='-';
        }
        if (myObject['bdd']) {
        document.getElementById('extra_bdd').firstChild.nodeValue=myObject['bdd'];
        } else {
            document.getElementById('extra_bdd').firstChild.nodeValue='-';
        }
        if (myObject['bdd_version']) {
            document.getElementById('bdd_version').firstChild.nodeValue=myObject['bdd_version'];
        } else {
            document.getElementById('bdd_version').firstChild.nodeValue='-';
        }
        if (myObject['login_message']) {
            document.getElementById('pmb_login_message').innerHTML=myObject['login_message'];
        } else {
            document.getElementById('pmb_login_message').innerHTML='';
        }
        if (myObject['opac_url']) {
            document.getElementById('opac_url').href=myObject['opac_url'];
        }
    } catch (e) {
    }
}
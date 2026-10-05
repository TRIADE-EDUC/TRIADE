// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: upload.js,v 1.7 2022/09/28 07:08:47 dbellamy Exp $

require(['apps/pmb/FolderTree', 'dojo/domReady!'], function(FolderTree){

    //Ouverture de la frame a partir du parent
    window.upload_openFrame= function (e) {
            
    	up_frame=document.getElementById('up_frame');
    	if (up_frame==null) {
    		if(!e) e=window.event;			
    		e.cancelBubble = true;
    		if (e.stopPropagation) e.stopPropagation();			
    		up_frame=document.createElement("div");		
    		up_frame.setAttribute('id','up_frame');
    		up_frame.setAttribute('name','up_frame');
    		var ft = new FolderTree({'att':up_frame});	
    		var att=document.getElementById("att");	
    		up_frame.style.visibility="hidden";
    		up_frame.style.display="block";
    		up_frame=att.appendChild(up_frame);		
    		var btn_pos = findPos(document.getElementById("upload_path"));
    		var pos_contenu = findPos(document.getElementById("contenu"));
    		var w=getWindowWidth();
    		var h=getWindowHeight();
    		var wTop= document.documentElement.scrollTop;
    		up_frame.style.width=Math.round(0.7*w)+'px';
    		up_frame.style.height=Math.round(0.6*h)+'px';
    		
    		up_frame.style.left=pos_contenu[0]+'px';
    		up_frame.style.top=wTop+'px';
    	}
    	up_frame.style.visibility="visible";	
    	up_frame.style.display='block';
    	document.addEventListener("mousedown", up_clic);
    	return false;
    }
});

//Gestion clic dans la page
function up_clic(e){
  	if (!e) var e=window.event;
	if(false == document.getElementById('up_frame').contains(e.target)) {
        up_hideFrame();
    }
}


//Occultation frame
function up_hideFrame() {
    try {
        up_frame=document.getElementById('up_frame');
        up_frame.style.visibility="hidden";
        up_frame.style.display='none';
        
    } catch(e) {}
    document.removeEventListener("mousedown", up_clic);
	return false;
}


//Destruction frame
//DB 14/09/2022 : Inutilisé 
function up_killFrame() {
    try {
	   up_frame=document.getElementById('up_frame');
	   up_frame.parentNode.removeChild(up_frame);
	} catch(e) {}
	return false;
}

//Fonction Ajax qui teste l'existence du fichier
function testing_file(id){
	var upl_check = document.getElementById('upload').checked;
	if(upl_check){
		var fichier = document.getElementById('f_fichier').value;
		if(fichier){
			var action = new http_request();
			var url = "./ajax.php?module=catalog&categ=explnum&id="+id+"&id_repertoire="+document.getElementById('id_rep').value+"&fichier="+encodeURIComponent(fichier)+"&quoifaire=exist_file";
			
			action.request(url);
			if(action.get_status() == 0){
				if(action.get_text() != "0"){
					return ecraser_fichier(action.get_text());
				}
			}
		}
	} 
	return true;
}

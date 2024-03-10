function getRequete() {
	if (window.XMLHttpRequest) { 
        	result = new XMLHttpRequest();     // Firefox, Safari, ...
	}else { 
	      if (window.ActiveXObject)  {
	      result = new ActiveXObject("Microsoft.XMLHTTP");    // Internet Explorer 
	      }
       	}
	return result;
}

function ajaxIAOrtho(commentaire,productID,ia,retour,CKEDITOR) {
	var requete = getRequete();
        var corps="commentaire="+escape(commentaire)+"&productID="+productID+"&ia="+ia;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
                                document.getElementById('bt_copilot').disabled=true;
                                document.getElementById('bt_copilot').value="Veuillez patienter...";
                        }

                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
					var reponse=CKEDITOR.instances['editor'].getData()+requete.responseText;
					CKEDITOR.instances['editor'].setData(reponse);
					document.getElementById('bt_copilot').value="TRIADE-COPILOT";
                                	document.getElementById('bt_copilot').disabled=false;
					document.getElementById('commentaire').value="";
                                }
                        }
                };
                requete.open("POST","https://ia.triade-educ.net/apimessagerie.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxContenuCours(question,productID,ia,matiere,classe) {
	var requete = getRequete();   
        var corps="commentaire="+escape(question)+"&productID="+productID+"&ia="+ia+"&matiere="+escape(matiere)+"&classe="+escape(classe);
	if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
				 tinymce.get('elm1').getBody().innerHTML="<img src='image/commun/3pp.gif' width='50' />";
				 document.getElementById('btq').value="Veuillez patienter...";
				 document.getElementById('btq').disabled = true;
                        }

                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
					document.getElementById('btq').value="TRIADE-COPILOT";
					document.getElementById('btq').disabled = false;
					reponse=requete.responseText.replace(/^&quot;/,"");
					reponse=reponse.replace(/&quot;$/,"");
					reponse=reponse.replace(/<br>/,"\n");
					tinymce.get('elm1').getBody().innerHTML=reponse;
	                        }
                        }
                };
                requete.open("POST","https://ia.triade-educ.net/apiContenuCours.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}

function ajaxDevoirEns(question,productID,ia,retour,type,matiere,classe) {
	var requete = getRequete();
        var corps="commentaire="+escape(question)+"&productID="+productID+"&ia="+ia+"&type="+type+"&matiere="+escape(matiere)+"&classe="+escape(classe);
	if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
                                document.getElementById(retour).innerHTML="<img src='image/commun/3pp.gif' width='50' />";
                        }

                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
                                        var reponse=requete.responseText;
                                        document.getElementById(retour).innerHTML=reponse;
                                }
                        }
                };
                requete.open("POST","https://ia.triade-educ.net/apiDevoir.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }	
} 


function ajaxIAVisaDir(i,productID,ia,retour) {
        var requete = getRequete();
        var corps="commentaire="+escape(document.getElementById('comm_'+i).value)+"&productID="+productID+"&ia="+ia;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
                                document.getElementById('bt_copilot_'+i).value="Veuillez patienter...";
                        }

                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
                                        var reponse=requete.responseText;
					document.getElementById(retour).value=reponse;
                                        document.getElementById('bt_copilot_'+i).value="TRIADE-COPILOT";
                                }
                        }
                };
                requete.open("POST","https://ia.triade-educ.net/apiVisaDir.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxIAMessagerieReponse(commentaire,productID,ia,retour,CKEDITOR) {
        var requete = getRequete();
        var corps="commentaire="+escape(commentaire)+"&productID="+productID+"&ia="+ia;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
                                document.getElementById('bt_copilot').disabled=true;
                                document.getElementById('bt_copilot').value="Veuillez patienter...";
                        }

                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
                                        var reponse=requete.responseText+CKEDITOR.instances['editor'].getData();
                                        CKEDITOR.instances['editor'].setData(reponse);
                                        document.getElementById('bt_copilot').value="TRIADE-COPILOT";
                                	document.getElementById('bt_copilot').disabled=false;
                                        document.getElementById('commentaire').value="";
                                }
                        }
                };
                requete.open("POST","https://ia.triade-educ.net/apimessagerie.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxIABulletinCom(commentaire,moyenne,productID,ia,retour,prenom,tonia) {
	var requete = getRequete();
	var corps="commentaire="+escape(commentaire)+"&moyenne="+moyenne+"&productID="+productID+"&ia="+ia+"&prenom="+prenom+"&tonia="+tonia;
	if (requete != null) {
		requete.onreadystatechange = function() { 
			if (requete.readyState != 4)  {
				document.getElementById(retour).value="Veuillez patienter...";
    			}    

	    		if (requete.readyState == 4) {
	       			if(requete.status == 200) {
					document.getElementById(retour).value=requete.responseText;
				}
  			}
		}; 
		
		requete.open("POST","https://ia.triade-educ.net/apicombull.php",true); 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}	
}

function verifToken(productID,ia,retour) {
	var requete = getRequete();
        var corps="productID="+productID+"&ia="+ia;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
					if (requete.responseText == "ko") {
	                                        document.getElementById(retour).innerHTML="<center><a title='Plus de cr&eacute;dit pour utiliser TRIADE-COPILOT \ncontacter votre administrateur Triade' style='text-decoration:none'  ><b><font id='color3' >PLUS DE TOKEN !!</font></b> <img src='image/commun/openai.gif' width='30' align='center' style='-moz-border-radius:10px 10px; -webkit-border-radius:10px 10px; border-radius:10px 10px; box-shadow: 5px 5px 5px grey;' /></a></center>" ;
					}
                                }
                        }
                };
                requete.open("POST","https://ia.triade-educ.net/verifToken.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxCopilot(search,productID,ia,afficheretour) {
	if (search.length < 5) return;
	var numRandom=getRandomArbitrary(1000,9999);
	var newDiv = document.createElement("div");
	newDiv.setAttribute("id","tocopy_"+numRandom);
	newDiv.style.cssText = 'position:relative;top:0px;left:170px;width:70%;-moz-border-radius:100px;border:1px  solid #ddd; box-shadow: 3px 3px 3px #CCCCCC; background-image: linear-gradient(180deg, #176EE9, #176EE9 40%, #176EE9  );padding:5px;margin:20px;border-radius: 10px;color:#FFFFFF ';
        var newContent = document.createTextNode(search);
        newDiv.appendChild(newContent);


	newDiv.innerHTML+="<br/><img src='image/commun/copy_paste_icon2.png' width='30' style='position:relative;top:0px;' border='0' class='js-copy'  data-target='#tocopy_"+numRandom+"' />";
        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);

	var btncopy = document.querySelector('.js-copy');
	if(btncopy) { btncopy.addEventListener('click', docopy); }

        verifToken(productID,ia,afficheretour);

        var requete = getRequete();
        var corps="commentaire="+escape(search)+"&productID="+productID+"&ia="+ia;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
                                document.getElementById('question').value="Veuillez patienter...";
			}
                       
			 if (requete.readyState == 1)  {
				var newDiv = document.createElement("img");
				newDiv.src = "image/commun/3pp.gif";
				newDiv.id="imgatt";
				newDiv.setAttribute("id","imgatt");
				newDiv.style.cssText = 'position:relative;top:0px;left:0px;padding:5px;margin:20px;border-radius:8px;width:50px';
                                document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
				  
                        }

                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
					if (requete.responseText == "") return ;
                                        var newDiv = document.createElement("div");
					var numRandom2=getRandomArbitrary(1000,9999);
					newDiv.setAttribute("id","tocopy_"+numRandom2);
					newDiv.style.cssText = 'position:relative;top:0px;left:0px;width:70%;-moz-border-radius:100px;border:1px  solid #ddd; box-shadow: 3px 3px 3px #CCCCCC; background-image: linear-gradient(180deg, #fff, #ddd 40%, #ccc );padding:5px;margin:20px;border-radius: 10px;';
					
                                        //var newContent = document.createTextNode(requete.responseText);
					reponse=requete.responseText.replace(/^&quot;/,"");
					reponse=reponse.replace(/&quot;$/,"");
                                        newDiv.innerHTML=reponse+"<br/><img src='image/commun/copy_paste_icon.png' width='30' style='position:relative;top:0px' class='js-copy2'  data-target='#tocopy_"+numRandom2+"' />";
                                        //newDiv.appendChild(newContent);


                                        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
				        var btncopy2 = document.querySelector('.js-copy2');
				        if(btncopy2) { btncopy2.addEventListener('click', docopy); }

                                        document.getElementById('question').value="";
					imgatt.remove();
                                }
                        }
                };

                requete.open("POST","https://ia.triade-educ.net/apisearch.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }

}

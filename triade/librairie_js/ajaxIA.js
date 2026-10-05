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
	var corps="commentaire="+encodeURIComponent(commentaire)+"&productID="+productID+"&ia="+ia;
	if (requete != null) {
		var editorInst = CKEDITOR.instances[retour];
		var btn = document.getElementById('bt_copilot');
		var origLabel = btn ? btn.innerHTML : '';
		if (btn) { btn.disabled=true; btn.textContent="Veuillez patienter..."; }
		if (editorInst) editorInst.setData("<img src='image/commun/3pp.gif' width='50' />");
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (requete.status == 200) {
					var inst = CKEDITOR.instances[retour];
					if (inst) inst.setData(requete.responseText);
					if (btn) { btn.innerHTML=origLabel; btn.disabled=false; }
					document.getElementById('commentaire').value="";
				}
			}
		};
		requete.open("POST","proxy-ia.php?e=apimessagerie.php",true);
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		requete.send(corps);
	}
}

function ajaxIAOrthoAgent(commentaire,productID,ia,retour,CKEDITOR) {
	var requete = getRequete();
	var corps="commentaire="+encodeURIComponent(commentaire)+"&productID="+productID+"&ia="+ia;
	if (requete != null) {
		var editorInst = CKEDITOR.instances[retour];
		var btn = document.getElementById('bt_copilot');
		var origLabel = btn ? btn.innerHTML : '';
		if (btn) { btn.disabled=true; btn.textContent="Veuillez patienter..."; }
		if (editorInst) editorInst.setData("<img src='image/commun/3pp.gif' width='50' />");
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (requete.status == 200) {
					var inst = CKEDITOR.instances[retour];
					if (inst) inst.setData(requete.responseText);
					if (btn) { btn.innerHTML=origLabel; btn.disabled=false; }
					document.getElementById('commentaire').value="";
				}
			}
		};
		requete.open("POST","proxy-ia.php?e=apiagentmessagerie.php",true);
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		requete.send(corps);
	}
}

function ajaxAudioMessage(question,productID,ia) {
	var requete = getRequete();   
        var corps="commentaire="+escape(question)+"&productID="+productID+"&ia="+ia;
	if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
                                        var reponse=requete.responseText;
				//	alert(reponse);
					const audio = new Audio(reponse);
					audio.play();
	                        }
                        }
                };
                requete.open("POST","proxy-ia.php?e=apitext-to-speech-ia.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}

function ajaxAudioMessageFichier(question,productID,ia) {
        var requete = getRequete();
        var corps="commentaire="+escape(question)+"&productID="+productID+"&ia="+ia;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        if (requete.readyState != 4)  {
				 document.getElementById('retourmp3').innerHTML="<img src='image/commun/3pp.gif' width='50' />";
                        }
                        if (requete.readyState == 4) {
                                if(requete.status == 200) {
                                        var reponse=requete.responseText.trim();
                                        if (reponse === '' || reponse.indexOf('.') === -1) {
                                            document.getElementById('retourmp3').innerHTML='';
                                            if (typeof alertify !== 'undefined') {
                                                alertify.error("Fichier audio non genere : cette option n'est pas activee ou vous n'avez plus de credit.");
                                            } else {
                                                alert("Fichier audio non genere : cette option n'est pas activee ou vous n'avez plus de credit.");
                                            }
                                        } else {
                                            document.getElementById('retourmp3').innerHTML="<a href='"+reponse+"' download style='display:inline-flex;align-items:center;gap:6px;padding:7px 18px;background:#080A66;color:#fff;border-radius:5px;text-decoration:none;font-size:12px;font-weight:700;font-family:Electrolize,Arial,sans-serif'>⬇ Télécharger le fichier audio</a>";
                                        }
                                }
                        }
                };
                requete.open("POST","proxy-ia.php?e=apitext-to-speech-ia.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxContenuCours(question,productID,ia,matiere,classe,idpiecejointe) {
	tinymce.get('elm1').getBody().innerHTML="<img src='image/commun/3pp.gif' width='50' />";
	document.getElementById('btq').value="Veuillez patienter...";
	document.getElementById('btq').disabled = true;

	function lancerIA(questionFinale) {
		var requete = getRequete();
		if (requete == null) return;
		var corps="commentaire="+encodeURIComponent(questionFinale)+"&productID="+productID+"&ia="+ia+"&matiere="+encodeURIComponent(matiere)+"&classe="+encodeURIComponent(classe);
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				document.getElementById('btq').value="TRIADE-COPILOT";
				document.getElementById('btq').disabled = false;
				if (requete.status == 200) {
					reponse=requete.responseText.replace(/^&quot;/,"");
					reponse=reponse.replace(/&quot;$/,"");
					reponse=reponse.replace(/<br>/,"\n");
					tinymce.get('elm1').getBody().innerHTML=reponse;
				}
			}
		};
		requete.open("POST","proxy-ia.php?e=apiContenuCours.php",true);
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		requete.send(corps);
	}

	if (idpiecejointe) {
		var r2 = getRequete();
		if (r2) {
			r2.onreadystatechange = function() {
				if (r2.readyState == 4) {
					var questionFinale = question;
					if (r2.status == 200) {
						try {
							var d = JSON.parse(r2.responseText);
							if (d.texte && d.texte.trim() !== '') {
								questionFinale = question + "\n\nDocument de référence (" + (d.nom||'') + ") :\n" + d.texte;
							}
						} catch(e) {}
					}
					lancerIA(questionFinale);
				}
			};
			r2.open("POST","ajaxLireDocIA.php",true);
			r2.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			r2.send("idpiecejointe="+encodeURIComponent(idpiecejointe));
		} else {
			lancerIA(question);
		}
	} else {
		lancerIA(question);
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
                requete.open("POST","proxy-ia.php?e=apiDevoir.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }	
} 


function ajaxIAVisaDir(i,productID,ia,retour,prenom,moyenne) {
        var requete = getRequete();
        var corps="commentaire="+escape(document.getElementById('comm_'+i).value)+"&productID="+productID+"&ia="+ia+"&prenom="+encodeURIComponent(prenom)+"&moyenne="+moyenne;
        if (requete != null) {
                requete.onreadystatechange = function() {
                        var btn=document.getElementById('bt_copilot_'+i);
                        if (requete.readyState != 4)  {
                                if (btn) { btn.disabled=true; btn.style.opacity='0.6'; }
                        }

                        if (requete.readyState == 4) {
                                if (btn) { btn.disabled=false; btn.style.opacity=''; }
                                if(requete.status == 200) {
                                        var reponse=requete.responseText.trim();
					document.getElementById(retour).value=reponse;
                                        var ctr=document.getElementsByName('CharRestant_'+i);
                                        if (ctr.length) ctr[0].value=reponse.length;
                                }
                        }
                };
                requete.open("POST","proxy-ia.php?e=apiVisaDir.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxIAMessagerieReponse(commentaire,productID,ia,retour,CKEDITOR) {
	var requete = getRequete();
	var corps="commentaire="+escape(commentaire)+"&productID="+productID+"&ia="+ia;
	if (requete != null) {
		var editorInst = CKEDITOR.instances[retour];
		var existingContent = editorInst ? editorInst.getData() : '';
		var btn = document.getElementById('bt_copilot');
		if (btn) { btn.disabled=true; btn.value="Veuillez patienter..."; }
		if (editorInst) editorInst.setData("<img src='image/commun/3pp.gif' width='50' />");
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (requete.status == 200) {
					var inst = CKEDITOR.instances[retour];
					if (inst) inst.setData(requete.responseText + existingContent);
					if (btn) { btn.value="TRIADE-COPILOT"; btn.disabled=false; }
					document.getElementById('commentaire').value="";
				}
			}
		};
		requete.open("POST","proxy-ia.php?e=apimessagerie.php",true);
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
					var rep=requete.responseText.trim();
					document.getElementById(retour).value=rep;
					var idx=retour.replace(/[^0-9]/g,'');
					var ctr=document.getElementsByName('CharRestant_'+idx);
					if (ctr.length) ctr[0].value=rep.length;
				}
  			}
		}; 
		
		requete.open("POST","proxy-ia.php?e=apicombull.php",true); 
		requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  		requete.send(corps); 
	}	
}

function verifToken(productID,ia,retour,iapers='') {
	var requete = getRequete();
        var corps="productID="+productID+"&ia="+ia+"&iapers="+iapers;
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
                requete.open("POST","proxy-ia.php?e=verifToken.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}


function ajaxCopilot(search,productID,ia,afficheretour,imggo,img,websearch,iapers) {
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


        verifToken(productID,ia,afficheretour,iapers);

        var requete = getRequete();
        var corps="commentaire="+escape(search)+"&productID="+productID+"&ia="+ia+"&imggo="+imggo+"&img="+img+"&websearch="+websearch+"&iapers="+iapers;
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

                requete.open("POST","proxy-ia.php?e=apisearch.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}

function ajaxCopilotCoach(search,productID,ia,afficheretour,coach,imggo,img,websearch) {
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

	if (coach != "") {
		var corps="commentaire=Base%20toi%20sur%20ce%20contenu%20suivant%20:%20"+escape(coach)+"%20pour%20repondre%20a%20la%20demande%20de%20l%20eleve%20ci-apres%20:%20"+escape(search)+"&productID="+productID+"&ia="+ia+"&imggo="+imggo+"&img="+img+"&websearch="+websearch;
	}else{
		var corps="commentaire="+escape(search)+"&productID="+productID+"&ia="+ia+"&imggo="+imggo+"&img="+img+"&websearch="+websearch;
	}
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

                requete.open("POST","proxy-ia.php?e=apisearch.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}

function ajaxCopilotPers(search,productID,ia,iapers,afficheretour) {
        if (search.length < 5) return;
        var numRandom=getRandomArbitrary(1000,9999);
        var newDiv = document.createElement("div");
        newDiv.setAttribute("id","tocopy_"+numRandom);
        newDiv.style.cssText = 'position:relative;top:0px;left:170px;width:70%;-moz-border-radius:100px;border:1px solid #ddd; box-shadow: 3px 3px 3px #CCCCCC; background-image: linear-gradient(180deg, #176EE9, #176EE9 40%, #176EE9  );padding:5px;margin:20px;border-radius: 10px;color:#FFFFFF ';
        var newContent = document.createTextNode(search);
        newDiv.appendChild(newContent);
        newDiv.innerHTML+="<br/><img src='image/commun/copy_paste_icon2.png' width='30' style='position:relative;top:0px;' border='0' class='js-copy'  data-target='#tocopy_"+numRandom+"' />";
        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
        var btncopy = document.querySelector('.js-copy');
        if(btncopy) { btncopy.addEventListener('click', docopy); }
        verifToken(productID,ia,afficheretour);
        var requete = getRequete();
	var corps="commentaire="+escape(search)+"&productID="+productID+"&ia="+ia+"&iapers="+iapers;
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
                                        newDiv.innerHTML=reponse+"<br/><img src='image/commun/copy_paste_icon.png'width='30' style='position:relative;top:0px' class='js-copy2'  data-target='#tocopy_"+numRandom2+"' />";
                                        //newDiv.appendChild(newContent);


                                        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
                                        var btncopy2 = document.querySelector('.js-copy2');
                                        if(btncopy2) { btncopy2.addEventListener('click', docopy); }

                                        document.getElementById('question').value="";
                                        imgatt.remove();
                                }
                        }
                };
                requete.open("POST","proxy-ia.php?e=apisearch.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}

function ajaxCopilotPersCoach(search,productID,ia,iapers,afficheretour,coach,imggo,img,websearch) {
        if (search.length < 5) return;
        var numRandom=getRandomArbitrary(1000,9999);
        var newDiv = document.createElement("div");
        newDiv.setAttribute("id","tocopy_"+numRandom);
        newDiv.style.cssText = 'position:relative;top:0px;left:170px;width:70%;-moz-border-radius:100px;border:1px solid #ddd; box-shadow: 3px 3px 3px #CCCCCC; background-image: linear-gradient(180deg, #176EE9, #176EE9 40%, #176EE9  );padding:5px;margin:20px;border-radius: 10px;color:#FFFFFF ';
        var newContent = document.createTextNode(search);
        newDiv.appendChild(newContent);
        newDiv.innerHTML+="<br/><img src='image/commun/copy_paste_icon2.png' width='30' style='position:relative;top:0px;' border='0' class='js-copy'  data-target='#tocopy_"+numRandom+"' />";
        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
        var btncopy = document.querySelector('.js-copy');
        if(btncopy) { btncopy.addEventListener('click', docopy); }
        verifToken(productID,ia,afficheretour);
        var requete = getRequete();
	if (coach != "") {
		var corps="commentaire=Base%20toi%20sur%20ce%20contenu%20suivant%20:%20"+escape(coach)+"%20pour%20repondre%20a%20la%20demande%20de%20l%20eleve%20sur%20cette%20question%20:%20" +escape(search)+"&productID="+productID+"&ia="+ia+"&iapers="+iapers+"&imggo="+imggo+"&img="+img+"&websearch="+websearch;
	}else{
		var corps="commentaire="+escape(search)+"&productID="+productID+"&ia="+ia+"&iapers="+iapers+"&imggo="+imggo+"&img="+img+"&websearch="+websearch;
	}
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
			
//					reponse=markdownParser(reponse);

                                        newDiv.innerHTML=reponse+"<br/><img src='image/commun/copy_paste_icon.png'width='30' style='position:relative;top:0px' class='js-copy2'  data-target='#tocopy_"+numRandom2+"' />";
                                        //newDiv.appendChild(newContent);
	

                                        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
                                        var btncopy2 = document.querySelector('.js-copy2');
                                        if(btncopy2) { btncopy2.addEventListener('click', docopy); }

                                        document.getElementById('question').value="";
                                        imgatt.remove();
                                }
                        }
                };
                requete.open("POST","proxy-ia.php?e=apisearch.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}



function ajaxSetupVeilleAgent(productID, ia, retour) {
	document.getElementById(retour).innerHTML = "<em style='font-size:12px;color:#555;'>Initialisation en cours...</em>";
	var requete = getRequete();
	var corps = 'product_id=' + encodeURIComponent(productID) + '&key=' + encodeURIComponent(ia);
	if (requete != null) {
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (requete.status == 200) {
					try {
						var res = JSON.parse(requete.responseText);
						if (res.success) {
							localStorage.setItem('triade_veille_agent_id', res.agent_id);
							document.getElementById(retour).innerHTML =
								"<span style='color:#1a7f4b;font-size:12px;'>&#10003; Agent initialisé — ID : "
								+ "<code style='background:#e8e8e8;padding:1px 5px;border-radius:3px;font-size:11px;'>" + res.agent_id + "</code></span>";
						} else {
							document.getElementById(retour).innerHTML = "<span style='color:#c00;font-size:12px;'>" + (res.erreur || 'Erreur inconnue.') + "</span>";
						}
					} catch(e) {
						document.getElementById(retour).innerHTML = "<span style='color:#c00;font-size:12px;'>Réponse inattendue.</span>";
					}
				} else if (requete.status == 402) {
					try { var err = JSON.parse(requete.responseText); alertify.error(err.erreur || 'Plus de tokens disponibles.'); }
					catch(e) { alertify.error('Plus de tokens disponibles.'); }
					document.getElementById(retour).innerHTML = '';
				} else {
					document.getElementById(retour).innerHTML = "<span style='color:#c00;font-size:12px;'>Erreur serveur : " + requete.status + "</span>";
				}
			}
		};
		requete.open('POST', 'proxy-ia.php?e=agent-veille-setup.php', true);
		requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		requete.send(corps);
	}
}

function ajaxRunAgent(agentId, agentNom, productID, ia) {
	var message = document.getElementById('agent_chat_input').value.trim();
	if (message.length < 2) return;
	var retour  = 'agent_chat_result';
	var btnEnv  = document.getElementById('btn_agent_send');
	document.getElementById(retour).innerHTML = "<img src='image/commun/3pp.gif' width='40' />";
	if (btnEnv) btnEnv.disabled = true;

	// Afficher le message utilisateur dans l'historique
	var hist = document.getElementById('agent_chat_history');
	if (hist) {
		hist.innerHTML += "<div style='text-align:right;margin:6px 0;'>"
			+ "<span style='display:inline-block;background:#080A66;color:#fff;padding:5px 10px;border-radius:10px 10px 2px 10px;font-size:12px;max-width:80%;'>"
			+ message.replace(/</g,'&lt;')
			+ "</span></div>";
	}
	document.getElementById('agent_chat_input').value = '';

	var requete = getRequete();
	var corps = 'agent_id=' + encodeURIComponent(agentId)
	          + '&message='    + encodeURIComponent(message)
	          + '&product_id=' + encodeURIComponent(productID)
	          + '&key='        + encodeURIComponent(ia);
	if (requete != null) {
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (btnEnv) btnEnv.disabled = false;
				document.getElementById(retour).innerHTML = '';
				if (requete.status == 200) {
					if (hist) {
						hist.innerHTML += "<div style='text-align:left;margin:6px 0;'>"
							+ "<div style='font-size:10px;color:#999;margin-bottom:2px;'>" + agentNom + "</div>"
							+ "<div style='display:inline-block;background:#f0f2fa;border:1px solid #dde3f5;padding:6px 10px;border-radius:2px 10px 10px 10px;font-size:12px;max-width:90%;'>"
							+ requete.responseText
							+ "</div></div>";
						hist.scrollTop = hist.scrollHeight;
					}
				} else if (requete.status == 402) {
					try { var err = JSON.parse(requete.responseText); alertify.error(err.erreur || 'Plus de tokens disponibles.'); }
					catch(e) { alertify.error('Plus de tokens disponibles.'); }
				} else if (requete.status == 403) {
					alertify.error('Agent non autorisé pour ce compte.');
				} else {
					alertify.error('Erreur serveur : ' + requete.status);
				}
			}
		};
		requete.open('POST', 'proxy-ia.php?e=agent-run.php', true);
		requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		requete.send(corps);
	}
}

function ajaxEduxpert(question, productID, ia, retour) {
	if (question.length < 3) return;
	var btn = document.getElementById('btn_eduxpert');
	document.getElementById(retour).innerHTML = "<img src='image/commun/3pp.gif' width='40' />";
	if (btn) btn.disabled = true;
	var requete = getRequete();
	var corps = 'message='     + encodeURIComponent(question)
	          + '&product_id=' + encodeURIComponent(productID)
	          + '&key='        + encodeURIComponent(ia);
	if (requete != null) {
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (btn) btn.disabled = false;
				if (requete.status == 200) {
					document.getElementById(retour).innerHTML = requete.responseText;
				} else if (requete.status == 402) {
					try { var e = JSON.parse(requete.responseText); alertify.error(e.erreur || 'Plus de tokens disponibles.'); }
					catch(x) { alertify.error('Plus de tokens disponibles.'); }
					document.getElementById(retour).innerHTML = '';
				} else {
					alertify.error('Erreur serveur : ' + requete.status);
					document.getElementById(retour).innerHTML = '';
				}
			}
		};
		requete.open('POST', 'proxy-ia.php?e=agent-eduxpert-COT-vGemini.php', true);
		requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		requete.send(corps);
	}
}

function ajaxSetupDirectionAgent(productID, ia, retour) {
	document.getElementById(retour).innerHTML = "<em style='font-size:12px;color:#555;'>Initialisation en cours...</em>";
	var requete = getRequete();
	var corps = 'product_id=' + encodeURIComponent(productID) + '&key=' + encodeURIComponent(ia);
	if (requete != null) {
		requete.onreadystatechange = function() {
			if (requete.readyState == 4) {
				if (requete.status == 200) {
					try {
						var res = JSON.parse(requete.responseText);
						if (res.success) {
							localStorage.setItem('triade_direction_agent_id', res.agent_id);
							document.getElementById(retour).innerHTML =
								"<span style='color:#1a7f4b;font-size:12px;'>&#10003; Agent initialisé — ID : "
								+ "<code style='background:#e8e8e8;padding:1px 5px;border-radius:3px;font-size:11px;'>" + res.agent_id + "</code></span>";
						} else {
							document.getElementById(retour).innerHTML = "<span style='color:#c00;font-size:12px;'>" + (res.erreur || 'Erreur inconnue.') + "</span>";
						}
					} catch(e) {
						document.getElementById(retour).innerHTML = "<span style='color:#c00;font-size:12px;'>Réponse inattendue.</span>";
					}
				} else if (requete.status == 402) {
					try { var err = JSON.parse(requete.responseText); alertify.error(err.erreur || 'Plus de tokens disponibles.'); }
					catch(e) { alertify.error('Plus de tokens disponibles.'); }
					document.getElementById(retour).innerHTML = '';
				} else {
					document.getElementById(retour).innerHTML = "<span style='color:#c00;font-size:12px;'>Erreur serveur : " + requete.status + "</span>";
				}
			}
		};
		requete.open('POST', 'proxy-ia.php?e=agent-direction-setup.php', true);
		requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		requete.send(corps);
	}
}

function ajaxAgentList(productID, ia, retour, showDelete) {
	var requete = getRequete();
	var corps = 'product_id=' + encodeURIComponent(productID) + '&key=' + encodeURIComponent(ia)
	          + '&show_delete=' + (showDelete ? '1' : '0');
	if (requete != null) {
		requete.onreadystatechange = function() {
			if (requete.readyState == 4 && requete.status == 200) {
				document.getElementById(retour).innerHTML = requete.responseText;
			}
		};
		requete.open('POST', 'proxy-ia.php?e=agent-list.php', true);
		requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		requete.send(corps);
	}
}

function ajaxVeille(sujet, productID, ia, retour) {
	if (sujet.length < 3) return;
	var requete = getRequete();
	var sources = 'eduscol.education.fr,education.gouv.fr,legifrance.gouv.fr,bulletin-officiel.education.fr,onisep.fr,ac-paris.fr';
	var corps = 'commentaire=' + escape(sujet)
	          + '&sources='    + escape(sources)
	          + '&productID='  + productID
	          + '&ia='         + ia;
	if (requete != null) {
		requete.onreadystatechange = function() {
			if (requete.readyState != 4) {
				document.getElementById(retour).innerHTML = "<img src='image/commun/3pp.gif' width='50' />";
				document.getElementById('btn_veille').disabled = true;
			}
			if (requete.readyState == 4) {
				document.getElementById('btn_veille').disabled = false;
				if (requete.status == 200) {
					var reponse = requete.responseText.replace(/^&quot;/, '').replace(/&quot;$/, '');
					document.getElementById(retour).innerHTML = reponse;
				} else if (requete.status == 402) {
					try {
						var err = JSON.parse(requete.responseText);
						alertify.error(err.erreur || "Plus de tokens disponibles.");
					} catch(e) {
						alertify.error("Plus de tokens disponibles.");
					}
					document.getElementById(retour).innerHTML = '';
				} else {
					document.getElementById(retour).innerHTML = "<span style='color:red'>Erreur serveur : " + requete.status + "</span>";
				}
			}
		};
		requete.open('POST', 'proxy-ia.php?e=apiVeille.php', true);
		requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		requete.send(corps);
	}
}

function ajaxCopilotEdt(search,productID,ia,afficheretour,imggo,img,datededebut) {
	if (search.length < 5) return;
	var numRandom=getRandomArbitrary(1000,9999);
	var newDiv = document.createElement("div");
	newDiv.setAttribute("id","tocopy_"+numRandom);
	newDiv.style.cssText = 'position:relative;top:0px;left:170px;width:70%;-moz-border-radius:100px;border:1px  solid #ddd; box-shadow: 3px 3px 3px #CCCCCC; background-image: linear-gradient(180deg, #176EE9, #176EE9 40%, #176EE9  );padding:5px;margin:20px;border-radius: 10px;color:#FFFFFF ';
        var newContent = document.createTextNode("Analyse du fichier image pour intégration dans l'EDT en cours...");
        newDiv.appendChild(newContent);

	newDiv.innerHTML+="<br/><img src='image/commun/copy_paste_icon2.png' width='30' style='position:relative;top:0px;' border='0' class='js-copy'  data-target='#tocopy_"+numRandom+"' />";
        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);

	var btncopy = document.querySelector('.js-copy');
	if(btncopy) { btncopy.addEventListener('click', docopy); }

        verifToken(productID,ia,afficheretour);

        var requete = getRequete();
        var corps="commentaire="+escape(search)+"&productID="+productID+"&ia="+ia+"&imggo="+imggo+"&img="+img;
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
					// 
					// reponse --> ICI INFO JSON	
                                        newDiv.innerHTML="Analyse terminée."+"<br/><img src='image/commun/copy_paste_icon.png' width='30' style='position:relative;top:0px' class='js-copy2'  data-target='#tocopy_"+numRandom2+"' />"; 
					document.getElementById('edt').value=reponse;
					document.getElementById('formul').submit();
                                        //newDiv.appendChild(newContent);
                                        document.getElementById(afficheretour).insertBefore(newDiv,document.getElementById(afficheretour).children[0]);
				        var btncopy2 = document.querySelector('.js-copy2');
				        if(btncopy2) { btncopy2.addEventListener('click', docopy); }

                                        document.getElementById('question').value="Analyse cet emploi du temps et fais un retour au format JSON, sans ajouter de remarque, avec une clef : debut, duree, jour_de_semaine, matiere, couleur, jour_du_mois sachant que cela commence le "+datededebut+"  et remplace h par ':' et min par ':' pour avoir unformat comme celui ci 'heure:minute:seconde' , indique les couleurs en code couleur HTML";
					imgatt.remove();
                                }
                        }
                };

                requete.open("POST","proxy-ia.php?e=apisearch.php",true);
                requete.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                requete.send(corps);
        }
}

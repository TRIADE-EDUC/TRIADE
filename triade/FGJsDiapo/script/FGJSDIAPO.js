/* 
################################################################
###                 Script FGJSDIAPO                         ###
############################################# Version 1.4 ######
################################################################

Auteur : fg
Site : http://fg.logiciel.free.fr
E-mail : fg.logiciel@free.fr
FREEWARE
*/

//Initialisation des variables

var fgjsdiapo_js_version = "1.4";
//J'utilise le systeme dit "binaire" , 0 : non , 1 : oui.
var fgjsdiapo_slide_auto = "0";//le diaporama est lancé ?
var fgjsdiapo_fen_options_var = "0";//le div "options" est ouvert ?
var fgjsdiapo_fen_about_var = "0";//le div "about" est ouvert ?


//Initialisation de FGJSDIAPO

//Test pour savoir combien il y a de fichier "lang",et permet aussi de connaitre si le fichier "lang" communiqué par l'url correspond aux fichiers répertoriés
//accessible via : lg=lang/fra.txt
for(test=0;test<fgjsdiapo_lang_browse.length;test++)
{
    if(jshp_get_var("lg"))
    {
        if(fgjsdiapo_lang_browse[test]==jshp_get_var("lg"))
        {
            document.write("<scr"+"ipt src='"+fgjsdiapo_lang_browse[test]+"' id='fgjsdiapo_lang_include' type='text/javascript' language='javascript'></scr"+"ipt>");
        }
        else
        {
            document.write('<scr'+'ipt src="'+fgjsdiapo_lang_browse[0]+'" id="fgjsdiapo_lang_include" type="text/javascript" language="javascript"></scr'+'ipt>');
        }
    }
    else
    {
        document.write('<scr'+'ipt src="'+fgjsdiapo_lang_browse[0]+'" id="fgjsdiapo_lang_include" type="text/javascript" language="javascript"></scr'+'ipt>');
    }
}

//On écrit le titre de la page en fonction de "config.txt", la variable : "fgjsdiapo_fen_title"
document.write('<title>'+fgjsdiapo_fen_title+'</title>');

//On ajoute le fichier de ressource FGJSDIAPO_effect SEULEMENT sous IE (implémentation en natif)
if(compatible==0)
{
	document.write('<scr'+'ipt type="text/javascript" src="./FGJsDiapo/script/FGJSDIAPO_effect.txt"></scr'+'ipt>');
}


document.write('<link href="'+fgjsdiapo_style_browse[0]+'" rel="stylesheet" rev="stylesheet" id="fgjsdiapo_style_include" type="text/css">');

//On écrit le fichier style
if(jshp_get_var("css"))
{
document.write('<link href="'+jshp_get_var("css")+'" rel="stylesheet" rev="stylesheet" id="fgjsdiapo_style_include" type="text/css">');
}
else
{
document.write('<link href="'+fgjsdiapo_style_browse[0]+'" rel="stylesheet" rev="stylesheet" id="fgjsdiapo_style_include" type="text/css">');
}

//On écrit le fichier de donné contenant les images
if(fgjsdiapo_img_browse[0] > "")
{
document.write('<scr'+'ipt src="'+fgjsdiapo_img_browse[0]+'" id="fgjsdiapo_img_db" type="text/javascript" language="javascript"></scr'+'ipt>');
}
else
{
document.write('<scr'+'ipt src="'+fgjsdiapo_img_browse[1]+'" id="fgjsdiapo_img_db" type="text/javascript" language="javascript"></scr'+'ipt>');
}

fgjsdiapo_addon_init();

//Maintenant passons aux fonctions

//Cette fonction passe partout, permet l'écriture rapidement des variables (même si certains dirons qu'un document.write suffit ;-)
function fgjsdiapo_write_var(whatvar){document.write(whatvar);}

function fgjsdiapo_write_bvar(wb,w)
{
document.getElementById(wb).innerHTML = w;
}

//Cette fonctione permet l'initialisation des scripts add-ons
function fgjsdiapo_addon_init(){if(fgjsdiapo_addon_browse.length > ""){for(i=0;i<fgjsdiapo_addon_browse.length;i++){document.write('<scr'+'ipt src="'+fgjsdiapo_addon_browse[i]+'" id="fgjsdiapo_addon_'+fgjsdiapo_addon_browse[i]+'" type="text/javascript" language="javascript"></scr'+'ipt>');}}}

//Permet de créer la liste des images (pour les connaisseurs , version 1.3 de barreslide pour FGJSDIAPO)
function fgjsdiapo_write_preview(dir){if(fgjsdiapo_fen_preview=="1"){switch(dir){case 'h':if(fgjsdiapo_fen_preview_dir=="0"){document.getElementById('fgjsdiapo_fen_preview_h').className="fgjsdiapo_fen_preview_h";document.write('<tr>');for(i=0;i<jsdiapo_images.length;i++){document.write('<td align="center"><a  href="javascript:;" onclick="fgjsidapo_jsdiapo_open(\''+i+'\')"><img alt="'+fgjsdiapo_fen_img+' :: '+jsdiapo_images[i]+'" border="0" class="jsdiapo_images_img_preview" src="'+jsdiapo_images[i]+'" ></a></td>');}document.write('</tr>');}break;case 'v':if(fgjsdiapo_fen_preview_dir=="1"){document.getElementById('fgjsdiapo_fen_preview_v').className="fgjsdiapo_fen_preview_v";for(i=0;i<jsdiapo_images.length;i++){document.write('<tr><td align="center"><a  href="javascript:;" onclick="fgjsidapo_jsdiapo_open(\''+i+'\')"><img alt="'+fgjsdiapo_fen_img+' :: '+jsdiapo_images[i]+'" border="0" class="jsdiapo_images_img_preview" src="'+jsdiapo_images[i]+'" ></a></td></tr>');}}break;}}}

//Permet la réinitialisation de barreslide
function fgjsdiapo_write_preview_reinit(){if(fgjsdiapo_fen_preview=="1"){var contain="";if(fgjsdiapo_fen_preview_dir=="0"){contain+='<div id="fgjsdiapo_fen_preview_h" style="overflow: auto;border: 0px solid #000;"><table width="100%" cellspacing="0" cellpadding="0" class="fgjsdiapo_table_script_td_nav">';for(i=0;i<jsdiapo_images.length;i++){contain+='<td align="center"><a href="javascript:;" onclick="fgjsidapo_jsdiapo_open(\''+i+'\')"><img alt="'+fgjsdiapo_fen_img+' :: '+jsdiapo_images[i]+'" border="0" class="jsdiapo_images_img_preview" src="'+jsdiapo_images[i]+'" ></a></td>';}contain+='</table></div></div>';document.getElementById('fgjsdiapo_fen_preview_h').innerHTML=contain;}else{contain+='<div id="fgjsdiapo_fen_preview_v" style="overflow: auto;border: 0px solid #000;"><div align="center" style="padding: 0" ><table width="100%" cellspacing="0" cellpadding="0" class="fgjsdiapo_table_script_td_nav">';for(i=0;i<jsdiapo_images.length;i++){contain+='<tr><td align="center"><a href="javascript:;" onclick="fgjsidapo_jsdiapo_open(\''+i+'\')"><img alt="'+fgjsdiapo_fen_img+' :: '+jsdiapo_images[i]+'" border="0" class="jsdiapo_images_img_preview" src="'+jsdiapo_images[i]+'" ></a></td></tr>';}contain+="</table></div></div>";document.getElementById('fgjsdiapo_fen_preview_v').innerHTML=contain;}}}

//Permet d'afficher une image à partir de son n°
function fgjsidapo_jsdiapo_open(what)
{
	firstopen = 1;
	jsdiapo_open(jsdiapo_images[what],"preview");
	jsdiapo_images_now=what;
	jsdiapo_move_nfo();
    jsdiapo_comm_update();
}

//A partir de la fenêtre "options", cette fonction permet de transmettre le contenu du champ "fen_options_form01_input" à JSDIAPO  par la fonction jsdiapo_lanceauto() pour lancer le diaporama automatique
function fgjsdiapo_options_slide_auto(){if(fgjsdiapo_slide_auto=="0"){fgjsdiapo_slide_auto="1";document.getElementById('fen_options_btn_slide_auto').innerHTML=fen_options_btn_slide_auto1;jsdiapo_lanceauto(document.getElementById('fen_options_form01_input').value);}else{fgjsdiapo_slide_auto="0";document.getElementById('fen_options_btn_slide_auto').innerHTML=fen_options_btn_slide_auto0;jsdiapo_lanceauto('')}}

//Permet d'afficher/cacher le menu options
function fgjsdiapo_fen_options(){if(fgjsdiapo_fen_options_var!= "0"){fgjsdiapo_fen_options_var = "0";document.getElementById('options').style.visibility="hidden";}else{fgjsdiapo_fen_options_var= "1";document.getElementById('options').style.visibility="visible";}}

//Permet d'afficher/cacher le menu about
function fgjsdiapo_fen_about(){if(fgjsdiapo_fen_about_var!= "0"){fgjsdiapo_fen_about_var = "0";document.getElementById('about').style.visibility="hidden";}else{fgjsdiapo_fen_about_var= "1";document.getElementById('about').style.visibility="visible";}}

//Écrit dans le select de la fenêtre options tout les fichiers "lang".
function fgjsdiapo_fen_options_lang_write()
{
	for(i=0;i<fgjsdiapo_lang_browse.length;i++)
	{
		if(fgjsdiapo_lang_browse[i]!=jshp_get_var("lg"))
		{
			document.write('<option value="'+fgjsdiapo_lang_browse[i]+'">'+fgjsdiapo_lang_browse[i]+'</option>');
		}
	}
	
	if(jshp_get_var("lg"))
	{
		document.write('<option value="'+jshp_get_var("lg")+'">'+jshp_get_var("lg")+'</option>');
	}
}

//Applique le changement de langue
function fgjsdiapo_fen_options_lang_apply()
{
	if(jsdiapo_images_auto==1)
	{
		if(confirm(var_fen_options_confirm_auto))
		{
			alert('ok');
			document.location.href='?lg='+document.getElementById('fen_options_form01_select2').value+other_var_than("lg");
		}
	}
	else
	{
		document.location.href='?lg='+document.getElementById('fen_options_form01_select2').value+other_var_than("lg");
	}
}

//Écrit dans le select de la fenêtre options tout les fichiers style .
function fgjsdiapo_fen_options_style_write()
{
	for(i=0;i<fgjsdiapo_style_browse.length;i++)
	{
		if(fgjsdiapo_style_browse[i]!=jshp_get_var("css"))
		{
			document.write('<option value="'+fgjsdiapo_style_browse[i]+'">'+fgjsdiapo_style_browse[i]+'</option>');
		}
	}
		
	if(jshp_get_var("css"))
	{
		document.write('<option value="'+jshp_get_var("css")+'">'+jshp_get_var("css")+'</option>');
	}
}
//Applique le changement de style
function fgjsdiapo_fen_options_style_apply(value)
{
	document.location.href = "?css="+value+other_var_than("css");
}

//Ecrit dans le select de la fenêtre options tous les fichiers sources d'images
function fgjsdiapo_fen_options_img_write()
{
	if(!jshp_get_var("o"))
	{
		document.write('<option value="0">- - - - - - - - - - - - - - -</option>');
	}
	
	
	for(i=1;i<fgjsdiapo_img_name.length;i++)
	{
		if(jshp_get_var("o") && jshp_get_var("o")==fgjsdiapo_img_browse[i])
		{
			document.write('<option value="'+fgjsdiapo_img_browse[i]+'" selected>=>'+fgjsdiapo_img_name[i]+'</option>');
		}
		else
		{
			document.write('<option value="'+fgjsdiapo_img_browse[i]+'">'+fgjsdiapo_img_name[i]+'</option>');
		}
	}
}

//Permet d'appliquer le changement de diaporama (fonction/testée sous IE & FireFox)
function fgjsdiapo_fen_option_img_apply(src)
{
	if(src != 0)
	{
	document.location.href = "?o="+src+other_var_than("o");
	}
}

//Si vous êtes développeur ou que vous voulez plus d'informations :
//un forum sur FGJSDIAPO et JSDIAPO est disponible à : http://fg.logiciel.free.fr/php/forums/
/* 
################################################################
###                 Script FGJSDIAPO_fen                     ###
############################################# Version 1.1 ######
################################################################

Auteur : fg
Site : http://fg.logiciel.free.fr
E-mail : fg.logiciel@free.fr
FREEWARE
*/

//Cette partie permet d'afficher,centrer,cacher les fenêtres .
//Centrage des fenêtres en fonction de la résolution (pas moins de 800*600)
var top=(screen.height-150)/2;

function byid(id)
{
return document.getElementById(id).style;
}
if(screen.height<=768 && screen.width<=1024)
{
	witdhpour = 80*1/100*screen.width;
	var left=(screen.width-witdhpour)/2;
	witdhpour2 = -28*1/100*screen.width;
	var left2=(screen.width-witdhpour2)/2;

}
if(screen.height>768 && screen.width>1024)
{
	witdhpour = 72*1/100*screen.width;
	var left=(screen.width-witdhpour)/2;
	witdhpour2 = -24*1/100*screen.width;
	var left2=(screen.width-witdhpour2)/2;
}
byid('options').top = top;
byid('options').left = left;
byid('about').top = top;
byid('about').left = left2;

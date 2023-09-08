<!-- Debut script
/* Fenetre de messagerie */
var navigateur=navigator.appName;
var abrege=navigateur.substring(0,2);
var ftx=740;
var fty=580;
var fpx=0;var fpy=0;var fpxf=0;var fpxc=0;var tempobe=0;var toclose=0;var decax=0;var decay=0;var deplace=0;

function Mouvement() {
        Xpos = event.clientX+document.body.scrollLeft;
        Ypos = event.clientY+document.body.scrollTop;
  if (deplace!=0) {
          if (deplace==1) {
                  decax=Xpos-fpx;
                  decay=Ypos-fpy;
                  deplace=2;
          }
           fpx=Xpos-decax;
           fpy=Ypos-decay;
           document.all.fenetre.style.top=fpy;
           document.all.fenetre.style.left=fpx;
           return false;
  }
}

document.onmousemove = Mouvement;

function cliquer(flag) {
        deplace=flag;
        return false;
}

function beWindow(fpx,fpy,ftx,fty,ftitre,fichier) {
var ftxb=130;var hide="hidden";

fchaine=''
+'<div id="fenetre" style="position:absolute;visibility:hidden;z-index:50;top:'+fpy+'px;left:'+fpx+'px;width:'+ftx+'px;height:'+fty+'px;bgcolor:#0000ff;">'
+'<div id="window" style="position:absolute;z-index:50;top:21px;left:0px;width:'+(ftx)+'px;height:'+(fty)+'px;">'
+'<div id="hg" style="position:absolute;z-index:50;top:0px;left:0px;width:6px;height:6px;"><img src="./image/hg.gif" width="6" height="6"></div>'
+'<div id="hd" style="position:absolute;z-index:50;top:0px;left:'+(ftx-6)+'px;width:6px;height:6px;"><img src="./image/hd.gif" width="6" height="6"></div>'
+'<div id="bg" style="position:absolute;z-index:50;top:'+(fty-6)+'px;left:0px;width:6px;height:6px;"><img src="./image/bg.gif" width="6" height="6"></div>'
+'<div id="bd" style="position:absolute;z-index:50;top:'+(fty-6)+'px;left:'+(ftx-6)+'px;width:6px;height:6px;"><img src="./image/bd.gif" width="6" height="6"></div>'
+'<div id="h" style="position:absolute;z-index:50;top:0px;left:6px;width:1px;height:6px;"><img src="./image/h.gif" width="'+(ftx-10)+'" height="6"></div>'
+'<div id="b" style="position:absolute;z-index:50;top:'+(fty-6)+'px;left:6px;width:1px;height:6px;"><img src="./image/b.gif" width="'+(ftx-10)+'" height="6"></div>'
+'<div id="g" style="position:absolute;z-index:50;top:6px;left:0px;width:6px;height:1px;"><img src="./image/g.gif" width="6" height="'+(fty-10)+'"></div>'
+'<div id="d" style="position:absolute;z-index:50;top:6px;left:'+(ftx-6)+'px;width:6px;height:1px;"><img src="./image/d.gif" width="6" height="'+(fty-10)+'"></div>'
+'</div>'
+'<div id="titre" style="position:absolute;z-index:50;top:1px;left:0px;width:'+(ftx)+'px;height:21px;" onmousedown="return cliquer(1);" onmouseup="return cliquer(0);">'
+'<table CELLPADDING=0 CELLSPACING=0 border=0 width="'+(ftxb)+'" height="21"><tr><td background="./image/tm.gif"><FONT FACE="verdana" SIZE=1><B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+ftitre+'</B></FONT></td></tr></table>'
+'<div style="position:absolute;z-index:50;top:0px;left:0px;"><a href="#" onclick="javascript:return closebe();"><IMG src="./image/tg.gif" BORDER=0 WIDTH=21 HEIGHT=21></A></div>'
+'<div style="position:absolute;z-index:50;top:0px;left:'+(ftxb-4)+'px;"><IMG src="./image/td.gif" BORDER=0 WIDTH=4 HEIGHT=21></div>'
+'</div>'
+'<div id="interieur" style="position:absolute;z-index:50;top:27px;left:6px;width:'+(ftx-12)+'px;height:'+(fty-12)+'px;">'
+'<iframe name="inbeos" TOP=0 LEFT=0 WIDTH='+(ftx-12)+' HEIGHT='+(fty-12)+' border=0 FRAMEBORDER=0 scrolling=AUTO src="'+fichier+'"></iframe>'
+'</DIV>'
+'</div>'
+'</div>'
document.write(fchaine);
}
function slidebe() {
        if (toclose!=3) {
    fpx+=fpxc;
    if ((fpx>fpxf) && (toclose==0)) {fpx=fpxf;fpxc=0;toclose=3;}
    if (toclose==1) {fpxc=fpxc-15;}
    document.all.fenetre.style.left=fpx;
    if (fpx<-700) {
      fpxc=0;fpx=700;toclose=3;
      inbeos.location.href="./attente_messagerie.php";
      document.all.fenetre.style.visibility="hidden";
    }
  }
  tempobe=setTimeout('slidebe();',5);
}

function apercu(fichier) {
	if ((abrege=="Mi") || (navigateur=="Netscape")){
		self.clearTimeout(null);
		self.clearInterval(null);
		fpy=document.body.scrollTop+(document.body.clientHeight/2)-190;
		document.all.fenetre.style.top=fpy;
		document.all.fenetre.style.left=-700;fpx=-700;fpxf=(document.body.clientWidth/2)-320;fpxc=100;toclose=0;
		document.all.fenetre.style.visibility="visible";
		inbeos.location.href=fichier;
	}else{
		myWindow=open(fichier, "messagerie", "width=640,height=500,menubar=no,resizable=no,scrollbars=YES,status=no,toolbar=no");
	}
	return false;
}

function closebe() {
fpxc=80;toclose=1;
return false;
}
function CreerFenetreBe() {
	if ((abrege=="Mi") || (navigateur=="Netscape")) {beWindow(0,0,740,580," <---  Quitter","./attente_messagerie.php");slidebe();}
}
//  Fin script -->

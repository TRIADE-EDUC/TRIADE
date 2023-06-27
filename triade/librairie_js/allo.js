var IE5=(document.getElementById && document.all)? true : false;
var W3C=(document.getElementById)? true: false;
var currIDb=null, currIDs=null, xoff=0, yoff=0; zctr=0; totz=0;
function trackmouse(evt){
if((currIDb!=null) && (currIDs!=null)){
var x=(IE5)? event.clientX+document.body.scrollLeft : evt.pageX;
var y=(IE5)? event.clientY+document.body.scrollTop : evt.pageY;
currIDb.style.left=x+xoff+'px';
currIDs.style.left=x+xoff+10+'px';
currIDb.style.top=y+yoff+'px';
currIDs.style.top=y+yoff+10+'px';
return false;
}}
function stopdrag(){
currIDb=null;
currIDs=null;
NS6bugfix();
}
function grab_id(evt){
xoff=parseInt(this.IDb.style.left)-((IE5)? event.clientX+document.body.scrollLeft : evt.pageX);
yoff=parseInt(this.IDb.style.top)-((IE5)? event.clientY+document.body.scrollTop : evt.pageY);
currIDb=this.IDb;
currIDs=this.IDs;
}
function NS6bugfix(){
if(!IE5){
//self.resizeBy(0,1);
//self.resizeBy(0,-1);
}}
function incrzindex(){
zctr=zctr+2;
this.subb.style.zIndex=zctr;
this.subs.style.zIndex=zctr-1;
}
function createPopup(id, title, width, height, x , y , isdraggable, boxcolor, barcolor, shadowcolor, text, textcolor, textptsize, textfamily, titlecolor ){
if(W3C){
zctr+=2;
totz=zctr;
var txt='';
txt+='<div id="'+id+'_s" style="position:absolute; left:'+(x+10)+'px; top:'+(y+10)+'px; width:'+width+'px; height:'+height+'px; background-color:'+shadowcolor+'; filter:alpha(opacity=50); visibility:visible"> </div>';
txt+='<div id="'+id+'_b" style="border:outset '+barcolor+' 2px; position:absolute; left:'+x+'px; top:'+y+'px; width:'+width+'px; overflow:hidden; height:'+height+'px; background-color:'+boxcolor+'; visibility:visible">';
txt+='<div style="width:'+width+'px; height:16px; background-color:'+barcolor+'; padding:0px; border:1px"><table cellpadding="0" cellspacing="0" border="0" width="'+(IE5? width-4 : width)+'"><tr><td width="'+(width-20)+'"><div id="'+id+'_h" style="width:'+(width-20)+'px; height:12px; font: 9px Arial; color:'+titlecolor+'"> '+title+'</div></td><td align="right"><a onmousedown="document.getElementById(\''+id+'_s\').style.display=\'none\'; document.getElementById(\''+id+'_b\').style.display=\'none\';return false"><img src="./image/closeb.gif" title="Pour fermer la fenêtre" border="0" height="15" width="15"></a></td></tr></table></div>';
txt+='<div id="'+id+'_ov" width:'+width+'px; style="margin:2px; color:'+textcolor+'; font:'+textptsize+'pt '+textfamily+';">'+text+'</div></div>';
document.write(txt);
this.IDh=document.getElementById(id+'_h');
this.IDh.IDb=document.getElementById(id+'_b');
this.IDh.IDs=document.getElementById(id+'_s');
this.IDh.IDb.subs=this.IDh.IDs;
this.IDh.IDb.subb=this.IDh.IDb;
this.IDh.IDb.IDov=document.getElementById(id+'_ov');
if(IE5){
this.IDh.IDb.IDov.style.width=width-6;
this.IDh.IDb.IDov.style.height=height-22;
this.IDh.IDb.IDov.style.scrollbarBaseColor=boxcolor;
this.IDh.IDb.IDov.style.overflow="auto";
}else{
this.IDh.IDs.style.MozOpacity=.5;
}
this.IDh.IDb.onmousedown=incrzindex;
if(isdraggable){
this.IDh.onmousedown=grab_id;
this.IDh.onmouseup=stopdrag;
}}}
if(W3C)document.onmousemove=trackmouse;
if(!IE5 && W3C)window.onload=NS6bugfix;
/*
createPopup( 'b997698952850', 'Service Triade - Votre publicité ici, tout de suite !' , 475, 260, 100, 100, true,  '#ffffff' , '#403D9F' , 'black' ,  '<form method="POST" action="http://www.g1script.com/home/mailing/option.php" target="_blank"><div align="center"><center><table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td width="100%"><p align="center"><a href="http://www.g1script.com/home/partenaires/sortie.php?id=251" target="_blank"><img src="http://www.g1script.com/home/images/g1domaine.gif" border="0" alt=""></a></p></td></tr><tr><td width="100%"><p align="center"><font face="Verdana"><b><font size="2">Bienvenue sur G1Script.Com<br></font></b><font size="2">&nbsp;<br>Pensez à vous inscrire à la mailing liste pour être tenu au courant<br>des nouveautés du site. Celle-ci est gratuite et mensuelle.</font></font><p align="center"><input type="hidden" name="idliste" value="1" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><input type="text" name="ademail" value="   Votre@Mail.Extension   " onFocus="this.value=\'\'" size="22" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><input type="submit" value="Ok" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><br><input type="hidden" name="action" value="inscription" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><input type="hidden" name="format" value="2" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"></td></tr></center></tr></table></div><br><center><font face=verdana size=1><b><a href=http://www.g1script.com/home/ANNUAIRES/sitewebmaster/index.php>Pensez à référencer votre site dans l\'Annuaire du Webmaster !</a></form>' , '#000000',8,'Arial','#FFFFFF');
*/
// createPopup( 'b997', 'Service Triade ' , 375, 260, 100, 100, true,  '#ffffff' , '#CC9900', 'black' ,  'text de la fenetre' , '#000000',8,'Arial','#FFFFFF');

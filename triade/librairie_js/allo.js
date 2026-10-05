var IE5=(document.getElementById && document.all)? true : false;
var W3C=(document.getElementById)? true: false;
var currIDb=null, currIDs=null, xoff=0, yoff=0; zctr=0; totz=0;
function trackmouse(evt){
if((currIDb!=null) && (currIDs!=null)){
var e=evt||window.event;
var x=e.pageX||(e.clientX+document.body.scrollLeft);
var y=e.pageY||(e.clientY+document.body.scrollTop);
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
var e=evt||window.event;
xoff=parseInt(this.IDb.style.left)-(e.pageX||(e.clientX+document.body.scrollLeft));
yoff=parseInt(this.IDb.style.top)-(e.pageY||(e.clientY+document.body.scrollTop));
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
function createPopup(id, title, width, height, x , y , isdraggable, boxcolor, barcolor, shadowcolor, text, textcolor, textptsize, textfamily, titlecolor, bordercolor ){
if(W3C){
zctr+=2;
totz=zctr;
var txt='';
var _border=bordercolor?'1px solid '+bordercolor:'outset '+barcolor+' 2px';
txt+='<div id="'+id+'_s" style="position:absolute;left:'+(x+10)+'px;top:'+(y+10)+'px;width:'+width+'px;height:'+height+'px;background-color:'+shadowcolor+';opacity:0.5;visibility:visible"> </div>';
txt+='<div id="'+id+'_b" style="border:'+_border+';position:absolute;left:'+x+'px;top:'+y+'px;width:'+width+'px;overflow:hidden;height:'+height+'px;background-color:'+boxcolor+';visibility:visible">';
txt+='<div id="'+id+'_bar" draggable="false" style="width:'+width+'px;height:16px;background-color:'+barcolor+';padding:0px;cursor:move;user-select:none;-webkit-user-select:none"><table cellpadding="0" cellspacing="0" border="0" width="'+width+'"><tr><td width="'+(width-20)+'"><div id="'+id+'_h" style="width:'+(width-20)+'px;height:14px;line-height:14px;font:9px Arial;color:'+titlecolor+'">&nbsp;&nbsp;'+title+'</div></td><td><a id="'+id+'_close" style="cursor:pointer"><img src="./image/closeb.gif" title="Pour fermer la fenêtre" border="0" height="15" width="15" draggable="false" style="margin-right:8px;display:block"></a></td></tr></table></div>';
txt+='<div id="'+id+'_ov" style="margin:2px;color:'+textcolor+';font:'+textptsize+'pt '+textfamily+';">'+text+'</div></div>';
document.write(txt);
var IDb=document.getElementById(id+'_b');
var IDs=document.getElementById(id+'_s');
var IDbar=document.getElementById(id+'_bar');
var IDclose=document.getElementById(id+'_close');
IDbar.addEventListener('dragstart',function(e){e.preventDefault();});
IDclose.addEventListener('mousedown',function(e){e.stopPropagation();});
IDclose.addEventListener('click',function(){IDb.style.display='none';IDs.style.display='none';});
IDb.addEventListener('mousedown',function(){zctr+=2;IDb.style.zIndex=zctr;IDs.style.zIndex=zctr-1;});
if(isdraggable){
IDbar.addEventListener('mousedown',function(e){
if(e.target===IDclose||e.target.tagName==='IMG') return;
e.preventDefault();
var startX=e.clientX,startY=e.clientY;
var startL=parseInt(IDb.style.left)||0;
var startT=parseInt(IDb.style.top)||0;
function onMove(e){
IDb.style.left=(startL+e.clientX-startX)+'px';
IDb.style.top=(startT+e.clientY-startY)+'px';
IDs.style.left=(startL+e.clientX-startX+10)+'px';
IDs.style.top=(startT+e.clientY-startY+10)+'px';
}
function onUp(){document.removeEventListener('mousemove',onMove);document.removeEventListener('mouseup',onUp);}
document.addEventListener('mousemove',onMove);
document.addEventListener('mouseup',onUp);
});
}}};
if(W3C)document.onmousemove=trackmouse;
if(!IE5 && W3C)window.onload=NS6bugfix;
/*
createPopup( 'b997698952850', 'Service Triade - Votre publicité ici, tout de suite !' , 475, 260, 100, 100, true,  '#ffffff' , '#403D9F' , 'black' ,  '<form method="POST" action="http://www.g1script.com/home/mailing/option.php" target="_blank"><div align="center"><center><table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td width="100%"><p align="center"><a href="http://www.g1script.com/home/partenaires/sortie.php?id=251" target="_blank"><img src="http://www.g1script.com/home/images/g1domaine.gif" border="0" alt=""></a></p></td></tr><tr><td width="100%"><p align="center"><font face="Verdana"><b><font size="2">Bienvenue sur G1Script.Com<br></font></b><font size="2">&nbsp;<br>Pensez à vous inscrire à la mailing liste pour être tenu au courant<br>des nouveautés du site. Celle-ci est gratuite et mensuelle.</font></font><p align="center"><input type="hidden" name="idliste" value="1" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><input type="text" name="ademail" value="   Votre@Mail.Extension   " onFocus="this.value=\'\'" size="22" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><input type="submit" value="Ok" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><br><input type="hidden" name="action" value="inscription" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"><input type="hidden" name="format" value="2" style="font-family: Verdana; font-size: 9; background-color: #CCCCCC; font-weight: bold; color: #000099; background: silver"></td></tr></center></tr></table></div><br><center><font face=verdana size=1><b><a href=http://www.g1script.com/home/ANNUAIRES/sitewebmaster/index.php>Pensez à référencer votre site dans l\'Annuaire du Webmaster !</a></form>' , '#000000',8,'Arial','#FFFFFF');
*/
// createPopup( 'b997', 'Service Triade ' , 375, 260, 100, 100, true,  '#ffffff' , '#CC9900', 'black' ,  'text de la fenetre' , '#000000',8,'Arial','#FFFFFF');

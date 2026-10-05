/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_drop.js,v 1.6 2014/11/19 08:17:09 ngantier Exp $ */

var cms_memo_opacdrop =new Array();

 // Fonction pour placer les cadres de l'opac
 
function opacdrop_opacdrop(dragged,target,x,y,xorig,yorig){
	//On récupère les enfants du cadre récepteur
	var childs=target.childNodes;
	var flag_moved=false;	
	for (var i=0; i<childs.length; i++) {
		var child_block=childs[i];
		if((child_block.offsetWidth!=0)&&(child_block.offsetHeight!=0)) {
			left_coords=cms_findPos(child_block);
			//On a trouvé !
			if (((x>=left_coords[0])&&(x<=left_coords[0]+child_block.offsetWidth))&&((y>=left_coords[1])&&(y<=left_coords[1]+child_block.offsetHeight))) {
				//J'enlève le noeud d'origine
				if(dragged.id != child_block.id){			
					
					
					dragged=dragged.parentNode.removeChild(dragged);
					target.insertBefore(dragged,child_block);
					cms_memo_opacdrop[dragged.id]=1;
					
				}					
				flag_moved=true;
				break;
			}
		}		
	}
	if (!flag_moved) {
		dragged=dragged.parentNode.removeChild(dragged);
		target.appendChild(dragged);		
	}	
	dragged.style.position="static";
	dragged.style.left=0;
	dragged.style.top=0;	
	
	cms_block_downlight(target);	
	cms_recalc_recept();
}

function opacdrop_moved(target,x,y,xorig,yorig) {
	
	//console.log("xdepose,ydepose,xorigine,yorigine ",x,y,xorig,yorig);
	var depx=x-xorig;
	var depy=y-yorig;
	//console.log("Déplacement ",depx,depy);
	var position=window.getComputedStyle(target).position;
	if (position=="static") {
		target.style.position="relative";
		/*depx=depx+target.offsetLeft;
		depy=depy+target.offsetTop;*/
		target.style.left=depx+"px";
		target.style.top=depy+"px";
	} else if((position=="relative")||(position=="absolute")) {
		
		var xtarget=window.getComputedStyle(target).left;
		var ytarget=window.getComputedStyle(target).top;
		xtarget=xtarget.substring(0,xtarget.length-2)*1;
		ytarget=ytarget.substring(0,ytarget.length-2)*1;
		//console.log("Position relative actuelle avant déplacement ",xtarget,ytarget);
		depx=depx+xtarget;
		depy=depy+ytarget;	
		
	//	depx=xtarget;
	//	depy=ytarget;
		target.style.left=depx+"px";
		target.style.top=depy+"px";
	}
	cms_recalc_recept();
	
}

function cms_block_highlight(obj) {
	obj.style.background="#DDD";
	obj.className='cms_drag'; 
}
function cms_block_downlight(obj) {
	parent.frames['opac_frame'].document.getElementById("cadre_depos").style.visibility="hidden";
	obj.className=''; 
	obj.style.background="";
	obj.style.outline="";
}

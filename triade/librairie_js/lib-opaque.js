/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
/************************************************************
Last updated: 28.03.2005  by  Eric Taesch
*************************************************************/
<script type="text/javascript">
<!--//

	var ie4=document.all // pour IE4++
	var dom=document.getElementById // pour NS $ Moz
	var tempaffichagefade = 25 	// pour ralentir augmenter la valeur!
					
	// function creation objet + appel fadeoutpic() pour fadeout sur image
	function unfadeimg(pic) {
	Nbriteration2=tempaffichagefade*2;
	myimg=ie4? eval("document.all."+pic) : document.getElementById(pic)
	setInterval("fadeoutpic(myimg)",tempaffichagefade)
	}
	
	// function de diminution de l'affichage par itération
	function fadeoutpic(tempobj) {
		if ( Nbriteration2 > tempaffichagefade ) {
			if (tempobj.filters)
			tempobj.filters.alpha.opacity=Nbriteration2
			else if (
			tempobj.style.MozOpacity
			)
			tempobj.style.MozOpacity=Nbriteration2/100
		}
	Nbriteration2=Nbriteration2-1;
	}
	
	// function de plein affichage de l'image
	function resetit(what){
	resetvalue=100
	var crossobj=ie4? eval("document.all."+what) : document.getElementById(what)
	if (crossobj.filters)
	crossobj.filters.alpha.opacity=resetvalue
	else if (crossobj.style.MozOpacity)
	crossobj.style.MozOpacity=resetvalue/100
	}
	
//-->
</script>

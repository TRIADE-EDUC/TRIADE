<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
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
?>
<div ID="cache">
<TABLE border=0 width=400 >
     <TR><TD align=center  >
     <font size=2><?php print LANGPATIENT?></font><br>
     <CENTER><br>
     <table border=0><TR><TD><img src="./image/commun/indicator.gif" align=center></TD></TR></TABLE>
     <BR><br>
     <font size=2>L'équipe Triade.</font>
     </CENTER></TD><TR>
     </table>
</div>

<script language=JavaScript>
var nava=(document.layers);
var dom=(document.getElementById);
var iex=(document.all);
if (nava) {cach = document.cache }
else if (dom) { cach =  document.getElementById("cache").style }
else if (iex) { cach = cache.style }
largeur = screen.width;
cach.left = Math.round((largeur/2)-200);
cach.visibility = "visible";

function cacheOff() {
	cach.visibility = "hidden";
}
window.onload=cacheOff
</script>

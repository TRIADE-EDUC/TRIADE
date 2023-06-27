<span id="CMenu" class="ContextMenu" style="visibility:hidden;"> 
<table width="200" cellpadding="0" cellspacing="0" class="texte" style="display:inline;"> 
  <tr height="20" onClick="history.go(-1);" onMouseOut="this.className='ContextMenuOut';" onMouseOver="this.className='ContextMenuOver';"> 
  <td width="25"></td><td width="175"> Page précédente</td></tr> 
  <tr height="20" onClick="history.go(+1);" onMouseOut="this.className='ContextMenuOut';" onMouseOver="this.className='ContextMenuOver';"> 
  <td></td><td> Page suivante</td></tr> 
  <tr height="10"><td colspan="2"><hr width="95%" align="center"></td></tr> 


 <!-- <tr height="20" onclick="window.location='../Accueil/PlanDuSite.html';" onMouseOut="this.className='ContextMenuOut';" onMouseOver="this.className='ContextMenuOver';"> 
  <td><img src="../Images/ICO_Plan.gif" width="19"></td><td> Plan du site</td></tr> -->
  
<tr height="20" onclick="document.getElementById('menu').style.left='100';document.getElementById('menu').style.top='100';document.getElementById('menu').style.visibility='visible';" onMouseOut="this.className='ContextMenuOut';" onMouseOver="this.className='ContextMenuOver';"> 
  <td></td><td> A propos</td></tr> 
</table> 
</span> 

<script language="JavaScript"> 
   if (document.all && window.print) { 
     document.oncontextmenu = MontrerMenu; 
     document.body.onclick = MasquerMenu; 
   } 
</script> 

// function disparition d'objet et reapparition.
//
// Exemple
//----------------------------------------------------------------
//<TABLE ID=tableau BORDER=2 BGCOLOR=#FFCC00> 
//<TR><TD>Mon tableau</TD></TR>
//</TABLE>
//<A HREF=# onClick="disparition('tableau');return(false)">On efface le tableau</A>
//<a HREF=# onClick="afficher('tableau');return(false)">tu reviens le tableau</a> 
//-----------------------------------------------------------------

function disparition(id)
{
if(document.getElementById)
document.getElementById(id).style.visibility = 'hidden'
}


function afficher(id)
{
if(document.getElementById)
document.getElementById(id).style.visibility = 'visible'
}

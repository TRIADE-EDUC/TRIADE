<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.org
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Vos réservations" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php   
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

if (isset($_GET["id"])) { suppResa($_SESSION["id_pers"],$_GET["id"]);  }
?>
<br>
<table border=0 align=center width=100%>
<tr>
<form action='resr_equip.php' method='post'>
<td><script language=JavaScript>buttonMagicSubmit("<?php  print LANGRESA12?>","rien"); //text,nomInput</script></td>
</form>
<td><script language='JavaScript'>buttonMagicRetour2('resa_prof.php','_self','Retour menu')</script>&nbsp;&nbsp;</td>
<form action='resr_salle.php' method='post'>
<td><script language=JavaScript>buttonMagicSubmit("<?php  print LANGRESA13?>","rien"); //text,nomInput</script></td>
</form>



</tr>
</table>
<br>
<?php

print "<table width='100%' border=1 bordercolor='#000000' >";
print "<tr>";
print "<td bgcolor='yellow' width=5% >&nbsp;Date&nbsp;</td>";
print "<td bgcolor='yellow' width=15%  >&nbsp;Horaire&nbsp;</td>";
print "<td bgcolor='yellow' >&nbsp;Equipement&nbsp;/&nbsp;Salle&nbsp;</td>";
print "<td bgcolor='yellow' >&nbsp;Etat&nbsp;</td>";
print "<td bgcolor='yellow' width=5% >&nbsp;Supprimer.&nbsp;</td>";
print "</tr>";
$data=ListeResa($_SESSION["id_pers"]); //id,idmatos,idqui,quand,heure_depart,heure_fin,info,valider
for($i=0;$i<count($data);$i++) {
	$em=""; $emF=""; $bgcolor="";
	if (preg_replace("/-/",'',$data[$i][3]) < dateYMD() ) {  $em="<i>"; $emF="</i>"; $bgcolor="bgcolor='#E4E4E4'"; }
	$id=$data[$i][0];
	print "<tr class='tabnormal2' onmouseover=\"this.className='tabover';\" onmouseout=\"this.className='tabnormal2'\" >";
	print "<td $bgcolor >$em&nbsp;".dateForm($data[$i][3])."&nbsp;$emF</td>";
	print "<td $bgcolor >$em&nbsp;".timeForm($data[$i][4])."&nbsp;-&nbsp;".timeForm($data[$i][5])."&nbsp;$emF</td>";
	$mess=$em.$data[$i][6].$emF;
	$information="Information";
	if ((LAN == "oui") && (AGENTWEB == "oui")) {
		$information="Agent Web Mélanie";
		$vocal=urlencode(stripHTMLtags($data[$i][6]));
		$http=protohttps();  // retourne https:// ou http://
		$mess="<iframe width=100 height=100 src=\'${http}www.triade-educ.org/agentweb/agentmel.php?inc=5&mess=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$data[$i][6] ;
	}
	print "<td $bgcolor >&nbsp;<a href='#' onMouseOver=\"AffBulle3('$information','./image/commun/info.jpg','$mess');\"  onMouseOut=\"HideBulle()\";>".chercheNomMatos($data[$i][1])."</a>&nbsp;</td>";
	if ($data[$i][7] == 1) {
		$okimg="<img src='image/commun/valid.gif' alt='Réservation acceptée' />";
	}else{
		$okimg="<img src='image/temps1.gif' alt='En attente de confirmation' />";	
	}
	print "<td $bgcolor >&nbsp;$okimg&nbsp;</td>";
	if (preg_replace("/-/",'',$data[$i][3]) >= dateYMD() ) {
		print "<td><input type='button' class='bouton2' value='Supprimer' onClick=\"open('resr_liste.php?id=$id','_parent','');\" ></td>";
	}else{
		print "<td $bgcolor align='center'><i>Impossible</i></td>";
	}
	print "</tr>";
}


Pgclose(); 
?>
</table>
<br><br>
<!-- // fin  -->
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
</BODY></HTML>

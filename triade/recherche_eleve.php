<?php
session_start();
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print LANGTITRE30?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<form method=post onsubmit="return valide_recherche_eleve()" name="formulaire">
<blockquote><BR>
<table border=0 cellspacing=0><tr><td style="padding-top:0px;" nowrap>
<font class="T2"><?php print LANGABS3?> : </font><input type="text" name="saisie_nom_eleve" size="20" id="search" autocomplete="off" onkeyup="searchRequest(this,'eleve','target','formulaire','saisie_nom_eleve')"   style="width:15em;" />
</td></tr>
<tr><td style="padding-top:0px;" ><div id="target" style="width:16em;border-style:none; background-color:#EEEEEE;"></div></td></tr> 
</table><div style="position:relative">
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT39?>","create"); //text,nomInput</script></UL></UL></UL>
</div>
 </blockquote>
 <?php brmozilla($_SESSION["navigateur"]);?>
 <?php brmozilla($_SESSION["navigateur"]);?>
 </form>
 <!-- // fin form -->
 </td></tr></table>
<br /><br />

<?php
//alertJs(empty($create));
// affichage de la liste d élèves trouvés
if(isset($_POST["saisie_nom_eleve"]))
{
$saisie_nom_eleve=trim($_POST["saisie_nom_eleve"]);
$motif=strtolower($saisie_nom_eleve);
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#CCCCCC" >
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
		<?php print LANGRECH2?> : <font id="color2"><B><?php print ucwords(stripslashes($motif))?></font>
	</font></td>
</tr>
<?php

if( count($data) <= 0 )
	{
	print("<tr><td align=center valign=center>".LANGRECH3."</td></tr>");
	}
else {
?>
<tr bgcolor="#FFFFFF"><td><b><?php print ucwords(LANGIMP10)?></b></td><td><B><?php print LANGIMP8?></B></td><td><B><?php print LANGIMP9?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr>
	<td bgcolor="#FFFFFF"><?php print $data[$i][0]?></td>
	<td bgcolor="#FFFFFF"><a style="text-decoration:underline" href="edit_eleve.php?eid=<?php print $data[$i][3]?>" onMouseOver="AffBulle('<font face=Verdana size=1><?php print LANGRECH4?></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><?php print strtoupper($data[$i][1])?></a></td>
	<td bgcolor="#FFFFFF"><?php print ucwords($data[$i][2])?></td>
	</tr>
	<?php
	}
}

?>
</table>
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<?php
}
?>
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

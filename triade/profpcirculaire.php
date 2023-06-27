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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de la classe
$saisie_classe=$_GET["sClasseGrp"];
verif_profp_class($_SESSION["id_pers"],$saisie_classe);
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><font   id='menumodule1' >
<?php print LANGPROFP4 ?> </font> <font id=color2><?php print ucwords($cl)?></font> </td>
</tr>
<tr id='cadreCentral0'>
<td>
<br>
     <!-- // fin  -->
  <form method=post  action='./circulaire_ajout2.php' name=formulaire ENCTYPE="multipart/form-data">
  <table  width=100%  border="0" align="center" >
   <tr  >
      <td align="right"><font class=T2><?php print LANGCIRCU6 ?> :</font> </TD>
      <TD align="left"><input type="text" name="saisie_titre" size=30 maxlength=28 ></td>
    </tr>
   <tr  >
      <td align="right"><font class=T2><?php print LANGCIRCU7 ?> :</font> </TD>
      <TD align="left"><input type="text" name="saisie_ref" size=30 maxlength=28 ></td>
    </tr>
    <tr>
      <td align="right"  ><font class=T2><?php print LANGCIRCU8 ?> :</font> </TD>
      <TD  align="left">
	<input type="file" name="fichier" size=30 >
	<A href='#' onMouseOver="AffBulle3('ATTENTION','./image/commun/warning.jpg','<?php print LANGCIRCU11 ?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>

</td>
    </tr>
    <tr>
      <td width=35% align="right"  ><font class=T2><?php print LANGCIRCU9 ?> : </font></TD>
      <TD  align="left"><input type="checkbox" name="saisie_envoi_prof" class="btradio1"> <A href='#' onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<?php print LANGCIRCU12 ?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>
      </td>
    </tr>
</td>
</tr></table><BR>
<table align=center><tr><td>
<input type=hidden name='saisie_classe[]' value='<?php print $saisie_classe?>' />
<script language=JavaScript>buttonMagicSubmit("<?php print LANGCIRCU15?>","rien"); //text,nomInput</script>
</td><td><?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?></td>
</td></tr></table>
</form>
<BR>

<table bgcolor=#FFFFFF border=1 bordercolor="#CCCCCC" width=100%>
<?php

if (isset($_POST["supp"])) {
	$cr=circulaireSup($_POST["saisie_id"]) ;
        if($cr) {
		$nomfichier=$_POST["saisie_nom_fic"];
		@unlink ("./data/circulaire/".trim($_POST["saisie_nom_fic"]));
        	// alertJs("Circulaire supprimée --  Service Triade");
	    	// reload_page('circulaire_supp.php');
        }else{
                error(0);
        }
}


if ($_SESSION["membre"] == "menuprof") {
	$visuProf="true";
	$data=circulaireAffProfP($_SESSION["id_pers"],"date");
?>
	<tr>
	<td bgcolor="yellow" width=5%><?php print LANGTE7 ?></td>
	<td bgcolor="yellow"><?php print LANGFORUM12?></td>
	<td bgcolor="yellow"><?php print LANGCIRCU20 ?></td>
	<td bgcolor="yellow" align=center width=5%><?php print LANGBT50?></td>
	</tr>
<?php
	for($i=0;$i<count($data);$i++)
	{
?>
	<form method=post>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top><?php print dateForm($data[$i][4])?></td>
	<td valign=top>
<A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B> <?php print LANGCIRCU21 ?>:</font> <font color=blue><?php print $data[$i][2]?></font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'>
<?php print $data[$i][1]?>
</A>
	</td>
	<td valign=top width=15>&nbsp;[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="accès au fichier" target="_blank">Visualiser</a>&nbsp;]&nbsp;</td>
	<td valign=top>
	<input type=submit name=supp value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" onclick="this.value='<?php print LANGattente222 ?>'">
	<input type=hidden name="saisie_id" value="<?php print $data[$i][0]?>">
	<input type=hidden name="saisie_nom_fic" value="<?php print $data[$i][3]?>">
	</td>
	</tr>
	</form>
<?php
	}
}
?>
</td>
</tr>
</table>

</td>
</tr>
</table>


<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
print "</SCRIPT>";
endif ;
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>

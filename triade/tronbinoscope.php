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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE32?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // debut form  -->
<blockquote><BR>
<font class="T2"><?php print LANGELE4?> :</font> <select id="saisie_classe" name="saisie_classe">
                         <option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select> <BR>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
</UL></UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>
<!-- // fin form -->
</td></tr></table>
<?php
// affichage de la classe
if(isset($_POST["consult"])) {
$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1' >
		<?php print LANGELE4?> : <font id="color2"><B><?php print $cl?></font>
	</font></td>
</tr>
<?php
if( count($data) <= 0 )
	{
	print("<tr id='cadreCentral0' ><td align=center valign=center>".LANGRECH1."</td></tr>");
	}
else {
?>
<tr  ><td bgcolor="yellow"> <B><?php print ucwords(LANGIMP8)?></B></td><td colspan=2 bgcolor="yellow"><B><?php print ucwords(LANGIMP9)?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++) {
	$photoeleve="image_trombi.php?idE=".$data[$i][1];
?>
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td ><?php print "<a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >');\"  onMouseOut='HideBulle()'>".strtoupper($data[$i][2])?></A></td>
	<td ><?php print ucwords($data[$i][3])?></td>
	<td align=center width=5>&nbsp;[&nbsp;<a href="#" onclick="open('photoajouteleve.php?ideleve=<?php print $data[$i][1]?>','photo','width=450,height=280')"><?php print "ajouter" ?></a>&nbsp;]&nbsp;[&nbsp;<a href="#" onclick="open('photoajouteleve.php?idelevesupp=<?php print $data[$i][1]?>','photo','width=450,height=280')"><?php print "Supprimer" ?></a>&nbsp;]&nbsp;</td>

	</tr>
	<?php
	}
      }
print "</table>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

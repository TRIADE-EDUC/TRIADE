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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<br><br>
<ul><font class='T2'><?php print LANGCHAN0?>.</font></ul><br>
<center>
<b><font class='T2' color=red><?php print LANGCHAN1?>.</font></b>
</center>
<br>
<BR>
<br>
<script language=JavaScript>
      function suite() {
              // var confirmation=confirm('<?php print LANGCHAN3?>','')
              // if (confirmation) {
                   location.href="./base_de_donne_key.php?base=change";
              // }
      }
      </script>
<BR><div align="center"> <input type=button  class="BUTTON" value='<?php print LANGBTS?>' onclick='suite();'> </div><br />
&nbsp;<i><b><?php print LANGASS10 ?> </b><?php print "Brevets, plan de classe"  ?></i>
<br>
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
<form method=post action="chgmentClas1.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1' >
<?php print LANGBASE26 ?> <font color="red"><B><?php print $cl?></font>
	</font></td>
</tr>
<?php
if ( count($data) <= 0 ) {
	print("<tr><td align=center valign=center>".LANGPROJ6."</td></tr>");
} else {
?>
<tr><td>&nbsp;</td></tr>
<tr bgcolor="#FFFFFF"><td> <B><?php print LANGNA1 ?></B></td><td><B><?php print LANGNA2 ?></B></td> <td><B><?php print LANGBASE36 ?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++) {
	?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td ><?php print strtoupper($data[$i][2])?>
	<input type=hidden name="idEleve[]" value="<?php print $data[$i][1]?>" ></td>
	<td ><?php print ucwords($data[$i][3])?></td>
	<td ><select id="new_classe[]" name="new_classe[]">
	<option value='rien'  STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
	<option value='quit'  STYLE='color:#000066;background-color:red' ><?php print LANGBASE37 ?></option>
	<?php
	select_classe(); // creation des options
	?>
	</select>
	</td>
	</tr>
	<?php
	}
      }
print "</table>";
print "<input type=hidden name='nbEleve' value='".count($data)."'>";
print "<br><br>";
?>
<ul><ul><ul><script language=JavaScript>buttonMagicSubmit("<?php print LANGBASE38 ?>","rien"); //text,nomInput</script></ul></ul></ul><br><br>

<?php
}
?>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

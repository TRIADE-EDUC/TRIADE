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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGETUDE41 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
validerequete("2");
if (isset($_POST["modif"])) {
	for($i=0;$i<$_POST["nb"];$i++) {
		$sortir="sortir".$i;
		if ($_POST[$sortir] == 1) {
			$sorti="1";
		}else{
			$sorti="0";
		}
		modif_eleve_etude($_POST["id_eleve"][$i],$_POST["id_etude"],$_POST["info"][$i],$sorti);
	}
	$id_etude=$_POST["id_etude"];
}else{
	$id_etude=$_POST["saisie_etude"];
}
?>
<form method=post>
<table width=95% bordercolor='#000000' border=1 align=center>
<tr><td bgcolor=yellow><b><?php print LANGTP1 ?></b> <b><?php print LANGTP1 ?></b></td></td><td bgcolor=yellow><b><?php print LANGASS27 ?></b></td><tr>
<?php
$data=liste_eleve_etude($id_etude);
for($i=0;$i<count($data);$i++) {
?>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'"   bordercolor='#FFFFFF'>
<?php
print "<td>".strtoupper(recherche_eleve_nom($data[$i][0]))." ".strtolower(recherche_eleve_prenom($data[$i][0]))."<br>";
?>
<?php $idclasse=chercheIdClasseDunEleve($data[$i][0]); $nomclasse=chercheClasse($idclasse); print $nomclasse[0][1]?> </td>
<td align=left width=125><input type=checkbox value="1" name="sortir<?php print $i;?>" <?php print verifchecketude($data[$i][0],$id_etude)  ?> > <?php print LANGETUDE44 ?><br>  <input type=text name="info[]" size=30 value="<?php print trim($data[$i][2])?>">
<input type=hidden name="id_eleve[]" value='<?php print $data[$i][0]?>' >
</td>
</tr>
<?php } ?>
</table>
<br><br>
<input type=hidden name="id_etude" value="<?php print $id_etude?>" >
<input type=hidden name="nb" value="<?php print count($data)?>" >
<script language=JavaScript>buttonMagicSubmit("<?php print LANGETUDE43 ?>","modif"); //text,nomInput</script>
</form>




<br><br>
<!-- // fin  -->
</td></tr></table>


<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
</BODY></HTML>

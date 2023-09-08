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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
        if (!verifDroit($_SESSION["id_pers"],"edt")) {
                accesNonReserveFen();
                exit;
        }
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGEDT9 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<br />
<!-- // fin  -->
<form method="post" action="edt_import.php" name="formulaire" >
<table border='0' width='100%' >
<tr><td align="right" ><font class="T2"><?php print LANGEDT5 ?> :</font></td>
    <td><input type=radio name="edt" value="VT" > </td>
</tr>
<tr><td align="right" ><font class="T2"><?php print LANGEDT6 ?> :</font></td>
    <td><input type=radio name="edt" value="EX" > </td>
<td  ><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create"); //text,nomInput</script></td></tr>
</table>
</form>
<br />
<table border=0 align='center' >
<tr><td align='right'><font class="T2"><?php print LANGEDT7 ?> :</td><td><input type=button class="bouton2" onclick="open('edt_visu.php','edt','width=1400,height=650,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes')" value="<?php print LANGBREVET1  ?>" />
</td></tr>

<tr><td align='right'><font class="T2"><?php print LANGMESS229 ?> :</font></td>
<td><input type=button class="bouton2" onclick="open('gestion_vacation_horaire.php','_parent','')" value="<?php print LANGBREVET1 ?>" />
</td></tr>

<tr><td align='right'><font class="T2"><?php print LANGTMESS489 ?> :</font></td>
<td><input type=button class="bouton2" onclick="open('edt_duplicate.php','_parent','')" value="<?php print LANGBREVET1 ?>" />
</td></tr>


<tr><td align='right'><font class="T2"><?php print "Remplacer un enseignant" ?> :</font></td>
<td><input type=button class="bouton2" onclick="open('edt_remplace_enseignant.php','_parent','')" value="<?php print LANGBREVET1 ?>" />
</td></tr>

<tr><td align='right'><font class="T2"><?php print LANGMESS228 ?> :</font></td>
<td><input type=button class="bouton2" onclick="open('gestion_suppr_edt.php','_parent','')" value="<?php print LANGBREVET1 ?>" />
</td></tr>

</table>
<br>
<hr>
<br>
<?php 
if (isset($_POST["createEDT"])) {
	enr_parametrage("datefinEDT",$_POST["datefinEDT"]);
	enr_parametrage("datedebutEDT",$_POST["datedebutEDT"]);
}

$datefinEDT=aff_enr_parametrage("datefinEDT");
$datedebutEDT=aff_enr_parametrage("datedebutEDT");

?>
<table border=0 align='center' >
<form action='edt.php' method='post' name="form2" >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' />
<td align=right><font class="T2"><?php print LANGMESS230 ?> :</td><td>
<?php print LANGMESS109 ?> <input type="text" name="datedebutEDT" value="<?php print $datedebutEDT[0][1] ?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;<?php
include_once("librairie_php/calendar.php");
calendarEDT("id111","document.form2.datedebutEDT",$_SESSION["langue"],"0","0");
?>&nbsp;<br><?php print LANGMESS110 ?>&nbsp;<input type="text" name="datefinEDT" value="<?php print $datefinEDT[0][1] ?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;<?php
calendarEDT("id222","document.form2.datefinEDT",$_SESSION["langue"],"0","0");
?> </font></td>
<td align=left valign='bottom' ><script language=JavaScript> buttonMagicSubmit3("<?php print "ok" ?>","createEDT","")</script></td></tr>
</form>
</table>
<br>
<hr>
<br>
<?php

if (isset($_POST["suppimg"])) {
	@unlink("data/image_pers/".$_POST["numimg"]."_edt.jpg");
	@unlink("data/image_pers/".$_POST["numimg"]."_edt.pdf");
	print "<script>alert(\"".LANGTMESS430." \\n\\n ".LANGTMESS428."\");</script>";
}

if (isset($_POST["img"])) {
	$photo=$_FILES['photo']['name'];
	$type=$_FILES['photo']['type'];
	$tmp_name=$_FILES['photo']['tmp_name'];
	$size=$_FILES['photo']['size'];
	$idclasse=$_POST["saisie_classe"];
//	$taille = getimagesize($tmp_name);
	if ((!empty($photo)) &&  ($size <= 2000000)) {
		$type=str_replace("image/","",$type);
		$type=str_replace("application/","",$type);
		$type=str_replace("pjpeg","jpg",$type);
		$type=str_replace("x-png","jpg",$type);
		$type=str_replace("jpeg","jpg",$type);
		if (verifImageJpg($type))  {
			$nomphoto="${idclasse}_edt.$type";
			move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
			history_cmd($_SESSION["nom"],"EDT","AJOUT $nomphoto");
			print "<script>alert(\"".LANGTMESS429." \\n\\n ".LANGTMESS428."\");</script>";
		 }elseif(verifPdf($type)) {
			$nomphoto="${idclasse}_edt.pdf";
			move_uploaded_file($tmp_name,"data/image_pers/$nomphoto");
			history_cmd($_SESSION["nom"],"EDT","AJOUT $nomphoto");
			print "<script>alert(\"".LANGTMESS427." \\n\\n ".LANGTMESS428."\");</script>";

		 }else{
			print "<font class=T1 color=red>".LANGTRONBI3."</font>";
		 }
	} else {
		print "<font class=T1 color=red>".LANGTRONBI4."</font>";
	}
}

?>
<center>
<form method=post ENCTYPE="multipart/form-data" >
<font class='T2'><?php print LANGMESS231 ?> : </font> <input type="file" name="photo" size="30"  class="bouton2" /> 
<br><br><i><font class=T1><?php print LANGMESS232 ?></font></i><br><br>
<font class='T2'><?php print LANGMESS233 ?> : </font> 
<select id="saisie_classe" name="saisie_classe">
    <option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select>
<input type=submit name="img" value="<?php print LANGENR ?>"  class="bouton2" />
</form>
</center>
<br><br>

<table width='100%' >
<?php 
$data=affClasse(); //code_class,libelle
for($i=0;$i<count($data);$i++) {
	if (file_exists("./data/image_pers/".$data[$i][0]."_edt.jpg")) {
		print "<form method=post><tr><td align=right><font class='T2'> Suppression EDT en classe : ".$data[$i][1]." : </font>";
		print "<input type=hidden name='numimg' value='".$data[$i][0]."' >";
		print "</td><td width='1%'><input type=submit name='suppimg' value=\"Supprimer\"  class='bouton2' ></td></tr></form> ";	
	}
	if (file_exists("./data/image_pers/".$data[$i][0]."_edt.pdf")) {
		print "<form method=post><tr><td align=right><font class='T2'> Suppression EDT en classe : ".$data[$i][1]." : </font>";
		print "<input type=hidden name='numimg' value='".$data[$i][0]."' >";
		print "</td><td width='1%'><input type=submit name='suppimg' value=\"Supprimer\"  class='bouton2' ></td></tr></form> ";	
	}
}

?>
</table>


<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php PgClose();  ?>
</BODY></HTML>

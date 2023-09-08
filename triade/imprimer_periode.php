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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onUnload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL7?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (isset($_GET["sClasseGrp"])) { 
	$idclasse=$_GET["sClasseGrp"];
	if ($_SESSION["membre"] == "menuprof") {
		verif_profp_class($_SESSION["id_pers"],$idclasse);
	}else{
		validerequete("2");
	}
}else{
	validerequete("2");
}

?>
<form name="formulaire" method="post" action="imprimer_periode2.php"  >
<br />
<!-- // fin  -->
<table width="90%" border="0" align="center">
<tr>
<td align="right"><font class="T2"><?php print LANGBULL8?> :</font></td>
<td colspan="2"   align="left"><input type="text" value="" name="saisie_date_debut" TYPE="text" size=13  class=bouton2 onKeyPress="onlyChar(event)" >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0");
?>
</td>
</tr>
<tr>
<td  align="right"><br><font class="T2"><?php print LANGBULL9?>  : </font></td>
<td colspan="2"  align="left"><br><input type="text" value="" name="saisie_date_fin" TYPE="text" size=13 class=bouton2 onKeyPress="onlyChar(event)" >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0");
?>
</td>
</tr>
<tr>
<td >
         <div align="right"><br><font class="T2"><?php print "Type d'impression" ?> : </font></div>
        </td>
        <td colspan="2" ><br>
	<select name="type_periode" size=1   >
		<option value="0" STYLE='color:#000066;background-color:#CCCCFF'>Défaut</option>
		<option value="1" STYLE='color:#000066;background-color:#CCCCFF'>Bonifacio</option>
		<option value="2" STYLE='color:#000066;background-color:#CCCCFF'>Lycée Chicago</option>
		<option value="3" STYLE='color:#000066;background-color:#CCCCFF'>Cours Renaissance</option>
		<option value="4" STYLE='color:#000066;background-color:#CCCCFF'>Mont Lyonnais</option>
	</select>
	 </td>
    </tr>
<tr>
<td >
         <div align="right"><br><font class="T2"><?php print LANGBULL10?> : </font></div>
        </td>
        <td colspan="2" ><br>
	<select name="nom_periode" size=1>
	<option value="0"   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
		<option value="periode1" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG1ER ?></option>
		<option value="periode2" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG2EME ?></option>
		<option value="periode3" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG3EME ?></option>
		<option value="periode4" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG4EME ?></option>
		<option value="periode5" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG5EME ?></option>
		<option value="periode6" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG6EME ?></option>
		<option value="periode7" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG7EME ?></option>
		<option value="periode8" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG8EME ?></option>
		<option value="periode9" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANG9EME ?></option>
	</select>
	 </td>
    </tr>
    <tr>
      <td >
        <div align="right"><br><font class="T2"><?php print LANGBULL11 ?> : </font></div>
      </td>
      <td colspan="2" ><br>

<?php
 if (isset($_GET["sClasseGrp"])) {
	 $nomClasse=chercheClasse_nom($_GET["sClasseGrp"]);

?>
	<input type=hidden name='saisie_classe' value="<?php print $_GET["sClasseGrp"] ?>" />
	<font class=T2><b><?php print trunchaine($nomClasse,20) ?></b></font>

<?php }else{ ?>
        <select name="saisie_classe">
        <option selected value=0 selected   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
	<?php
	select_classe2(20);
	Pgclose();
	?>
	</select>
<?php } ?>
        </td>
    </tr>
<tr>
<td >
         <div align="right"><br><font class="T2"><?php print "Hauteur des matières" ?> : </font></div>
        </td>
        <td colspan="2" ><br>
	<select name="hauteur" size=1>
		<option value="7" STYLE='color:#000066;background-color:#CCCCFF'>07</option>
		<option value="7.5" STYLE='color:#000066;background-color:#CCCCFF'>7.5</option>
		<option value="8" STYLE='color:#000066;background-color:#CCCCFF'>08</option>
		<option value="9" STYLE='color:#000066;background-color:#CCCCFF'>09</option>
		<option value="10" STYLE='color:#000066;background-color:#CCCCFF'>10</option>
		<option value="11" STYLE='color:#000066;background-color:#CCCCFF'>11</option>
		<option value="12" STYLE='color:#000066;background-color:#CCCCFF'>12</option>
		<option value="13" STYLE='color:#000066;background-color:#CCCCFF'>13</option>
		<option value="14" STYLE='color:#000066;background-color:#CCCCFF'>14</option>
		<option value="15" STYLE='color:#000066;background-color:#CCCCFF'>15</option>
	</select>
	 </td>
    </tr>
  </table>
  <br>
  <table border="0" align="center">
    <tr>
      <td  align="center">
<table border=0 align=center width="250" ><tr><td align="center">
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL12 ?>","rien","onclick='attente()'"); //text,nomInput</script>
</td><td><?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?>
<?php 
	/*
	if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) { ?>
	<script language=JavaScript>buttonMagic("<?php print LANGBULL13 ?>","historyperiode.php","periode","scrollbars=yes,width=600,height=350","");</script>
	<?php } */ ?>
</td></tr></table>
      </td>
    </tr>
  </table><BR><BR>
<br />
<!-- // fin  -->
</td></tr></table>
</form>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
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
// deconnexion en fin de fichier
?>
<?php @nettoyage_repertoire("./data/pdf_bull"); ?>
</BODY></HTML>

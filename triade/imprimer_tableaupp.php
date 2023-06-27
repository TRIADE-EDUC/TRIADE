<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["annee_scolaire"])) {
        $anneeScolaire=$_POST["annee_scolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-impr_periode.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onUnload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS357 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"imprtableau")) {
		Pgclose();
		accesNonReserveFen();
		exit();
	}
}else{
	validerequete("3");
}
$erreurdeja=0;
$valeur=aff_Trimestre();
if (count($valeur)) {
?>


     <form name="formulaire" id="formulaire" method="post" action="./tableaupp.php"  onSubmit="return valideTab();" >
     <table width="100%" border="0" align="center" >
     <tr><td width=100%>

     <table width="100%" border="0" align="center" height=150>

     <tr>
     <td width="50%" align="right"><font class="T2"><?php print LANGBULL2?> :</font> </td>
     <td><select name="saisie_classe" onChange='impr_periode(document.formulaire.saisie_classe.options[document.formulaire.saisie_classe.options.selectedIndex].value);' >
          <option selected value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
         </select>
     </td>
     </tr>

     <tr><td height="10" ></td><td></td></tr>

     <TR><TD align="right"  valign=top ><font class="T2"><?php print LANGBASE40 ?></font> <select name="typetrisem" onchange="trimes();" >
     <option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     </select>  <font class="T2">:</font> </TD>
     <TD  valign=top><select name="saisie_trimestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
              </Select>
         </TD>
     </TR>
     <tr><td height="10" ></td><td></td></tr>
    <tr><td align=right  valign=top ><font class="T2"><?php print LANGBULL3?> :</font> </td>
     <td  valign=top>
     <select name='annee_scolaire'>
      <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
      ?>
     </select>
     </td></tr>
     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGMESS358 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="affrang"  id="affrang" value="1" onclick="changementform2()" /> (<i><?php print LANGOUI ?></i>)
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>

<!--
     <tr><td align=right  valign=top ><font class="T2"><?php print LANGMESS359 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="affcolvide" id="affcolvide"value="1" onclick="changementform2()" /> (<i>oui</i>)
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>
-->
     <tr><td align=right  valign=top ><font class="T2"><?php print LANGMESS360 ?>&nbsp;:</font> </td>
     <td  valign=top>

     <input type="checkbox" id="unite_enseig" name="unite_enseig" value="1" onclick="changementform()" /> (<i><?php print LANGOUI ?></i>)&nbsp;&nbsp;<a href='#'  onMouseOver="AffBulle3('<?php print "Information" ?>','./image/commun/info.jpg','<?php print "Le regroupement est effectué via le module `Unités enseignmts` de la rubrique affectation. " ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGMESS361 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="affmatiere" id="affmatiere" value="non"  /> (<i><?php print LANGNON ?></i>)
     </td></tr>

	<?php if ((VATEL != "1") || (!defined("VATEL")))  { ?>
     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGTMESS466 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="affsousmatiere" id="affsousmatiere" value="non"  /> (<i><?php print LANGNON ?></i>)
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGTMESS467 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="noteexamen" id="noteexamen" value="oui"  /> (<i><?php print LANGOUI ?></i>)
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print "Seulement les notes de type examen"  ?> :</font> </td><td  valign=top><?php include("typeexamen.php"); ?></td></tr>

     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGTMESS468 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="pointsupp" id="pointsupp" value="oui"  /> (<i><?php print LANGOUI ?></i>)&nbsp;&nbsp;<a href='#'  onMouseOver="AffBulle3('<?php print "Information" ?>','./image/commun/info.jpg','<?php print LANGTMESS469 ?>');"  onMouseOut="HideBulle()";><img src="./image/help.gif" border=0 align=center></a>
     </td></tr>
	<?php } ?>

     </table>
     <BR>
     <center>
     <table width="250" border="0" align="center">
     <tr><td  align="center">
	 <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43pp?>","rien",""); //text,nomInput,action</script>
     </td></tr></table>
     </form>
     </td></tr></table>

<script>
function changementform() {
	if (document.getElementById("unite_enseig").checked==true) {
		document.getElementById("affrang").checked=false;
		//document.getElementById("affcolvide").checked=false;
		document.getElementById("formulaire").action="tableaupp_vatel.php";
	}else{
		document.getElementById("formulaire").action="tableaupp.php";
	}
}

function changementform2() {
	document.getElementById("unite_enseig").checked=false;
}

</script>

<?php
}else {

if ($erreurdeja != 1) {
?>
<br />
<center>
<?php print LANGMESS10?> <br>
<br>
<br>
<font size=3><?php print LANGMESS13?><br>
<br>
<?php print LANGMESS12?><br>
</center>

<?php } } ?>

</td></tr></table>
<br/><br />


<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFP39?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >

<form method=post action="tableaupp2an.php" name="formulairean" target="_blank"  onSubmit="return valideTab2();" >
<table border=0 width='100%' align=center height="100">

<tr><td  align="right"><font class="T2"><?php print LANGBULL2?> :</font> </td>
<td align=left id='bordure'>
<select name="saisie_classe" onChange='impr_periode(document.formulairean.saisie_classe.options[document.formulairean.saisie_classe.options.selectedIndex].value);' >
          <option selected value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
	 </select>
</td></tr>

<tr><td height="20" colspan="2"></td></tr>

<TR><br><TD align="right"  id="bordure"  valign=top ><font class="T2"><?php print LANGMESS374 ?></font> <select name="typetriseman" onChange="trimesan();" >
     <option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     </select> <font class="T2"> : </font></TD>
     <TD  valign=top id="bordure" ><select name="saisie_trimestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
              </Select>
         </TD>
     </TR>


<tr><td height="20" colspan="2"></td></tr>

    <tr><td align=right  valign=top id="bordure" ><font class=T2><?php print LANGBULL3?> : </font></td>
     <td  valign=top id="bordure" >
     <select name='annee_scolaire'>
      <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
      ?>
     </select>
     </td></tr>
     <tr><td height="10" ></td><td></td></tr>
 <tr><td align=right  valign=top ><font class="T2"><?php print LANGMESS358 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="affrang" value="1" /> (<i><?php print LANGOUI ?></i>)
     </td></tr>

    <tr><td height="10" ></td><td></td></tr>
	<?php if ((VATEL != "1") || (!defined("VATEL")))  { ?>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGTMESS467 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="noteexamen" id="noteexamen" value="oui"  /> (<i><?php print LANGOUI ?></i>)
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>

     <tr><td align=right  valign=top ><font class="T2"><?php print "Seulement les notes de type examen"  ?> :</font> </td><td  valign=top><?php include("typeexamen.php"); ?></td></tr>
     <?php } ?>

     <tr><td  valign=top id="bordure" colspan=2 ><br />
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43pp?>","rien","onclick='document.getElementById(\"attenteDiv\").style.visibility=\'visible\';'"); //text,nomInput,action</script>
<br /><br />
</td></tr></table>
</td></tr></table>
</form>
</td></tr></table>
<br><br>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS362 ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<table border=0 width='100%' align=center height="100">
<form method='post'>
<br>

<tr><td align=right  valign=top id="bordure" ><font class=T2><?php print LANGBULL3?> : </font></td>
<td  valign=top id="bordure" >
<select name='annee_scolaire' onChange="this.form.submit()">
<?php
           filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
</td></tr>
</form>


<tr><td height="20" colspan="2"></td></tr>
<form method=post action="tableauppxls.php" name="formulaire3" onSubmit="return valideTab3();" target="_blank" >
<input type='hidden' name='annee_scolaire' value='<?php print $anneeScolaire ?>' />


<tr><td  align="right"><font class="T2"><?php print LANGBULL2?> :</font> </td>
<td align=left id='bordure'>
<select name="saisie_classe" onChange='impr_periode(document.formulaire3.saisie_classe.options[document.formulaire3.saisie_classe.options.selectedIndex].value);' >
          <option selected value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
	 </select>
</td></tr>

<tr><td height="20" colspan="2"></td></tr>


 <tr><td align=right  valign=top ><font class="T2"><?php print LANGMESS358 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="affrang" value="1" /> (<i><?php print LANGOUI ?></i>)
     </td></tr>

    <tr><td height="20" ></td><td></td></tr>
     
	<?php if ((VATEL != "1") || (!defined("VATEL")))  { ?>

     <tr><td align=right  valign=top ><font class="T2"><?php print LANGTMESS467 ?> :</font> </td>
     <td  valign=top>
     <input type="checkbox" name="noteexamen" id="noteexamen" value="oui"  /> (<i><?php print LANGOUI ?></i>)
     </td></tr>

     <tr><td height="10" ></td><td></td></tr>


     <tr><td align=right  valign=top ><font class="T2"><?php print "Seulement les notes de type examen"  ?> :</font> </td><td  valign=top><?php include("typeexamen.php"); ?></td></tr>
<?php } ?>




     <tr><td  valign=top id="bordure" colspan=2 ><br />
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGMESS375 ?>","rien","onclick='document.getElementById(\"attenteDiv\").style.visibility=\'visible\';'"); //text,nomInput,action</script>
<br /><br />
</td></tr></table>
</td></tr></table>
</form>



<!-- // fin  -->
</td></tr></table>

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
attente();

?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
</BODY></HTML>

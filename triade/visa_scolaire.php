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
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe4()" name="formulaire" action="visa_scolaire2.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Visa Vie Scolaire" ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // debut form  -->
     <blockquote>
<BR>
<font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='annee_scolaire' >
                 <?php
                 $anneeScolaire=$_COOKIE["anneeScolaire"];
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>
                <br><br>


               <font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe">
                                   <option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select> <br /><br />
<font class=T2>

<?php print LANGBASE40 ?> <select name="typetrisem" onchange="trimes();" >
     <option value=0   STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     </select>  : 
     <select name="saisie_trimestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
                     <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
     </Select>
</font>

<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult"); //text,nomInput</script>
</UL></UL></UL>
</blockquote>
</form>
<br><br><br>
<font class='T1'><i> Information : ce module est lié à certain bulletin scolaire dont les commentaires ne sont pas rattachés à la matière 'Vie Scolaire'. Si vous souhaitez mettre un commentaire sur la matière 'Vie Scolaire' consulter le module 'Note vie scolaire'</i></font>
<?php 
if (isset($_POST["namur"])) {
       	$cr=enr_comport_namur($_POST["periode_1_namur"],$_POST["periode_2_namur"],$_POST["periode_3_namur"]); 
	if ($cr) {
		alertJs(LANGDONENR);
	}
}
?>

<?php if (MODNAMUR0 == "oui") {  


?>
</td></tr></table>
<br /><br />
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><?php print "Information comportement social et personnel" ?></font></b></td></tr>
<tr id='cadreCentral0'><td align="center">
<form method="post">
<br />
1er période : <br /><textarea cols="80" rows="4" name="periode_1_namur" onkeypress="compter(this,'250', this.form.CharRestant_1)" ><?php $com=recup_comport_namur("perio_1_namur"); 	print "$com" ; $nbtexte=strlen($com); $com=""; ?></textarea>
<input type=text name='CharRestant_1' size=3 disabled='disabled' value='<?php print $nbtexte ?>' />
<br /><br />
2eme période : <br /><textarea cols="80" rows="4" name="periode_2_namur" onkeypress="compter(this,'250', this.form.CharRestant_2)" ><?php $com=recup_comport_namur("perio_2_namur"); print "$com" ; $nbtexte=strlen($com); $com=""; ?></textarea>
<input type=text name='CharRestant_2' size=3 disabled='disabled' value='<?php print $nbtexte ?>' />
<br /><br />
3eme période : <br /><textarea cols="80" rows="4" name="periode_3_namur" onkeypress="compter(this,'250', this.form.CharRestant_3)" ><?php $com=recup_comport_namur("perio_3_namur"); print "$com" ; $nbtexte=strlen($com); $com=""; ?></textarea>
<input type=text name='CharRestant_3' size=3 disabled='disabled' value='<?php print $nbtexte ?>' />
<br /><br />
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","namur"); //text,nomInput</script></td></tr></table>
</form>
<font class='T1'><i> Information : ce module est lié au bulletin Namur</i></font>
<?php } ?>

<!-- // fin form -->
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
// deconnexion en fin de fichier
Pgclose();
?>

</BODY>
</HTML>

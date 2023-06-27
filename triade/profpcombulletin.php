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

<?php
// affichage de la classe
if(isset($_GET["sClasseGrp"])) {
	$saisie_classe=$_GET["sClasseGrp"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);

	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
?>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
	<tr id='coulBar0' ><td height="2" colspan=3 ><b><font   id='menumodule1' >
	<?php print LANGEL3?> : <font id="color2"><B><?php print $cl?></font>
	</font></td>
	</tr>
	<?php
	if( count($data) <= 0 )	{
		print("<tr id='cadreCentral0' ><td align=center height='100%' valign=center><br><br>".LANGRECH1."<br><br></td></tr></table>");
	}else {
	?>
	<tr id='cadreCentral0' ><td valign=top>
	<form method=post onsubmit="return valide_consul_classe3()" name="formulaire" action="profpcombulletin2.php">
	<br /><ul>
	<font class=T2><?php print LANGBULL3 ?> : <?php print $_COOKIE["anneeScolaire"] ?><br><br>
	<font class=T2><?php print LANGBASE40 ?> <select name="typetrisem" onchange="trimes2();" >
     	<option value='0' STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
     	<option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
     	<option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
     	<option value="periode"  STYLE='color:#000066;background-color:#CCCCFF'><?php print strtolower(LANGASS26) ?></option>
     	<option value="examen"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGGRP55 ?></option>
     	</select>  : 
     	<select name="saisie_trimestre">
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
	<option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
     	</Select>
	<input type=hidden name="saisie_classe" value="<?php print $_GET["sClasseGrp"]?>" >


	<br /><br />
        Type du bulletin : <select name="type_bulletin" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='default' >Standard</option>
	<optgroup label="Parcours éducatifs" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='paravenir' >Parcours avenir</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='parcitoyen' >Parcours citoyen</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='pareducart' >Parcours d'éducation artistique et culturelle </option>
	<optgroup label="LEAP" >
	<option STYLE='color:#000066;background-color:#CCCCFF' value='leap' >Bulletin standard</option>
        </select>


	</ul>
	<UL><UL><UL>
	<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult"); //text,nomInput</script>
<?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?>
	</UL></UL></UL>
	<br><br>
	</form>
	</td></tr></table>
	
	<br><br>

	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
        <tr id='coulBar0' ><td height="2" colspan=3 ><b><font   id='menumodule1' >
        <?php print LANGEL3?> : <font id="color2"><B><?php print $cl?></font>
        </font></td>
        </tr>
	<tr id='cadreCentral0' ><td valign=top>
        <form method=post onsubmit="return valide_consul_classe1()" name="formulaire1" action="profpcomScolaireBulletin.php">
        <br /><ul>
        <font class=T2><?php print LANGBULL3 ?> : <?php print $_COOKIE["anneeScolaire"] ?><br><br>
        <font class=T2><?php print LANGBASE40 ?> <select name="typetrisem" onchange="trimes22();" >
        <option value='0' STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX?></option>
        <option value="trimestre" STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28?></option>
        <option value="semestre"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29?></option>
        <option value="periode"  STYLE='color:#000066;background-color:#CCCCFF'><?php print strtolower(LANGASS26) ?></option>
        <option value="examen"  STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGGRP55 ?></option>
	<option value="cycle"  STYLE='color:#000066;background-color:#CCCCFF'><?php print "Cycle" ?></option>
        </select>  :
        <select name="saisie_trimestre">
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        <option STYLE='color:#000066;background-color:#CCCCFF'>        </option>
        </Select>
        <input type=hidden name="saisie_classe" value="<?php print $_GET["sClasseGrp"]?>" >

        <br /><br />
        Type du commentaire : <select name="type_rubrique" >
        <option STYLE='color:#000066;background-color:#CCCCFF' value='EPI' >EPI (Enseignements pratiques interdisciplinaires)</option>
        <option STYLE='color:#000066;background-color:#CCCCFF' value='AP' >AP (Accompagnement Personnalisé)</option>
		<option STYLE='color:#000066;background-color:#CCCCFF' value='MCSFC' >Maîtrise des composantes du socle en fin de cycle</option>
		
        </select>
	<br><br>

	Proposition : <select name='num'>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='1'>1</option>
	<option STYLE='color:#000066;background-color:#CCCCFF' value='2'>2</option>
	</select>

	<br /><br />
	<ul><ul>

	<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER?>","consult"); //text,nomInput</script>
<?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?>
	</ul></ul>

	</form>
	<br><br>
	</td></tr></table>
	<br><br>
<?php 
	} 
}
?>
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
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

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
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
//error($cnx);
$sql_ngrp="SELECT trim(libelle) FROM ${prefixe}groupes ORDER by 1";
$res=execSql($sql_ngrp);
// on génère un tableau bidim JavaScript
// nommé liste_grp qui servira à la
// fonction JavaScript verif_nom_grp(mat)
// pour vérifier que le nom de groupe
// saisi n'existe pas déjà -> cf. <input name="saisie_intitule ...>
genMatJs("liste_grp",chargeMat($res));
?>
<script language="JavaScript">
// mettre en librairie
function verif_nom_grp(mat) {
	for(i=0;i<mat.length;i++) {
		if ((mat[i][0] == document.formulaire.saisie_intitule.value) && (document.formulaire.saisie_intitule.value != "")){
			alert("<?php print LANGGRP46 ?>");
			document.formulaire.saisie_intitule.focus();
			document.formulaire.saisie_intitule.select();
			document.formulaire.rien.disabled=true;
			return false;
		}else{
			document.formulaire.rien.disabled=false;
		}
	}
return true;
}

function verif_nom_grp2(mat) {
	for(i=0;i<mat.length;i++) {
		if ((mat[i][0] == document.formulaire2.saisie_intitule.value) && (document.formulaire2.saisie_intitule.value != "")){
			alert("<?php print LANGGRP46 ?>");
			document.formulaire2.saisie_intitule.focus();
			document.formulaire2.saisie_intitule.select();
			document.formulaire2.rien.disabled=true;
			return false;
		}else{
			document.formulaire2.rien.disabled=false;
		}
	}
return true;
}

</script>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit='return validecreatgroupe()' name="formulaire" action='./creat_groupe_suite.php' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE11?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<ul><BR>
<table>
<tr><td><font class=T2><?php print LANGGRP1?> : </font> <input onChange="return verif_nom_grp(liste_grp);" type=text name='saisie_intitule' size='15' maxlength='30' ></td><td><script language=JavaScript>buttonMagic("<?php print LANGGRP50 ?>","modifier_groupe.php","_parent","","");</script></td></tr></table>
<br>
<font class="T2"><?php print LANGBULL3?> : </font><select name="annee_scolaire" size="1">
<?php
filtreAnneeScolaireSelectNote('',3); // creation des options
?>
</select>
<br>
<BR><U><?php print LANGGRP2?></U><BR><BR></UL>
<center>
<table width=100% border=0>
<TR><TD>&nbsp;&nbsp;
<select align=top name="saisie_liste[]" size=6  style="width:120px" multiple="multiple">
<?php
select_classe(); // creation des options
?>
</select>
</TD>
<TD valign=top align=center>
<TABLE border="1" width=80% bordercolor="#000000">
<TR><TD bgcolor="#FFFFFF" bordercolor="#FFFFFF" >
<?php print LANGGRP3?> <font color=red><B><?php print LANGGRP4?></b></font> <?php print LANGGRP5?><BR>  <BR>
</td></tr>
</table>
</TD></TR></TABLE></center>
<BR><BR><UL>
<script language=JavaScript>buttonMagic("<?php print LANGBT12?>","liste_groupe.php","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT13?>","rien"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print LANGGRP44 ?>","suppression_groupe.php","_parent","","");</script>
</form>
</ul>
<br><br><br>
<hr>
<ul>
<form method=post  action='./creat_groupe_import.php' name="formulaire2" ENCTYPE="multipart/form-data" onsubmit='return validecreatgroupe3()'>
<font class=T2><?php print LANGGRP1?> :  <input onChange="return verif_nom_grp2(liste_grp);" type='text' name='saisie_intitule' size='35' maxlength='30' ><BR>
<br>
<font class="T2"><?php print LANGBULL3?> : </font><select name="annee_scolaire" size="1">
<?php
filtreAnneeScolaireSelectNote('',3); // creation des options
?>
</select>
<br>

<br /><?php print LANGMESS353 ?> : <input type="file" name="fichier" size=30 >

<br><br><script language=JavaScript>buttonMagicSubmit("<?php print LANGGRP45?>","rien"); //text,nomInput</script>
</form>
<br /><br />
</ul>
<br>
<ul><?php print LANGMESS354 ?> : <br><br></ul>
<table width="50%" border="1" bgcolor="#FCE4BA" bordercolor=#000000 align="center">
<!-- //$nom,$pren,$mdp,$tp,$civ,$pren2='',$adr,$codepostal,$tel,$mail,$commune -->
        <tr bgcolor="#FFCC00">
          <td valign=top width=5>&nbsp;1)&nbsp;<?php print LANGIMP48?>&nbsp;</td>
          <td valign=top width=5>&nbsp;2)&nbsp;<?php print LANGIMP46?>&nbsp;</td>
	  <?php $t1=LANGELE10; ?>
	  <td valign=top width=5>&nbsp;3)&nbsp;<?php print preg_replace('/ /','&nbsp;',$t1) ?>&nbsp;</td>
	</tr>
</table>

<br><br>
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>

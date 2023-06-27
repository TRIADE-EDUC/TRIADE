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
<script language="JavaScript" src="./librairie_js/lib_circulaire.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCIRCU5 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once("./librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
$fichier="";
if (isset($_GET["idcirculaire"])) {
	$idcirculaire=$_GET["idcirculaire"];
	$data=chercheCirculaire($idcirculaire); //id_circulaire,sujet,refence,file,date,enseignant,classe,idprofp,comptepersonnel,compteviescolaire,comptedirection,comptetuteurdestage
	$id_circulaire=$data[0][0];
	$sujet=htmlentities($data[0][1]);
	$reference=htmlentities($data[0][2]);
	$fichier=$data[0][3];
	$enseignant=$data[0][5];
	$classe=$data[0][6];
	$idprofp=$data[0][7];
	$comptepersonnel=$data[0][8];
	$compteviescolaire=$data[0][9];
	$comptedirection=$data[0][10];
	$comptetuteurdestage=$data[0][11];
	$categorie=$data[0][12];
}

?>
<!-- // fin  -->
<form method=post  action='./circulaire_ajout2.php' name=formulaire ENCTYPE="multipart/form-data">
<table  width=100%  border="0" align="center" >
<tr  >
<td align="right" width=40%><font class="T2"><?php print LANGCIRCU6 ?> :</font> </TD>
<TD align="left"><input type="text" name="saisie_titre" size=30 maxlength=28 value="<?php print $sujet ?>" ></td>
</tr>
<tr  >
<td align="right"><font class="T2"><?php print LANGCIRCU7 ?> :</font> </TD>
<TD align="left"><input type="text" name="saisie_ref" size=30 maxlength=28 value="<?php print $reference ?>" ></td>
</tr>
<tr  >
<td align="right"><font class="T2"><?php print "Catégorie" ?> :</font> </TD>
<TD align="left"><input type="text" name="saisie_cat" size=30 maxlength=200 value="<?php print $categorie ?>" ></td>
</tr>
<tr>
<td align="right"  ><font class="T2"><?php print LANGCIRCU8 ?> :</font> </TD>
<TD  align="left">
<?php 
if ($fichier != "") {
	print " $fichier ";

}else{ ?>
<input type="file" name="fichier" size=30 >
<?php 
if (UPLOADIMG == "oui") {
	$taille="8Mo";
}else{
	$taille="2Mo";
}

include_once('librairie_php/db_triade.php');
$mess=LANGCIRCU11." (Taille max : $taille) ";
$information="Attention";

if (file_exists("common/config8.inc.php")) {
	include_once("common/config8.inc.php");
}else{
	define("AGENTWEBPRENOM","Lise");
}

if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="";
	$mess="<iframe width=100 height=100 src=\'http://www.triade-educ.com/agentweb/agentmel.php?inc=5&mess=$vocal&m=M2\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}
?>
<A href='#' onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/warning.jpg','<?php print $mess?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>
<?php } ?>

</td>
    </tr>
<tr  >
<td align="right"><font class="T2"><?php print "Avertir par messagerie " ?> :</font> </TD>
<TD align="left"><input type="checkbox" name="envoimessage" value="oui" > <i>(oui)</i></td>
</tr>

    <tr>
      <td width=35% align="right"  ><font class="T2"><?php print LANGCIRCU9 ?> :</font> </TD>
<?php
$mess=LANGCIRCU12;
$information="Information";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="";
	$mess="<iframe width=100 height=100 src=\'http://www.triade-educ.com/agentweb/agentmel.php?inc=5&mess=$vocal&m=M3\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}

if ($enseignant == 1) { $checkedProf="checked='checked'"; }else{  $checkedProf=""; } 
?>
      <TD  align="left"><input type="checkbox" name="saisie_envoi_prof" id="btradio1" value="1" <?php print $checkedProf ?> > <A href='#' onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/info.jpg','<?php print $mess?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>
      </td>
    </tr>
<?php if ($comptepersonnel == 1) { $checkedpersonnel="checked='checked'"; }else{  $checkedpersonnel=""; }  ?>

<tr>
      <td width=35% align="right"  ><font class="T2"><?php print "Pour le personnel" ?> :</font> </TD>
      <TD align="left"><input type="checkbox" name="saisie_envoi_pers" id="btradio1" value="1" <?php print $checkedpersonnel ?> ></td>
</tr>
<?php if ($compteviescolaire == 1) { $checkedviescolaire="checked='checked'"; }else{  $checkedviescolaire=""; } ?>

<tr>
      <td width=35% align="right"  ><font class="T2"><?php print "Pour la vie scolaire" ?> :</font> </TD>
      <TD align="left"><input type="checkbox" name="saisie_envoi_mvs" id="btradio1" value="1" <?php print $checkedviescolaire ?> ></td>
</tr>

<?php if ($comptetuteurdestage == 1) { $checkedtuteurdestage="checked='checked'"; }else{  $checkedtuteurdestage=""; } ?>

<tr>
      <td width=35% align="right"  ><font class="T2"><?php print "Pour tuteurs de stage" ?> :</font> </TD>
      <TD align="left"><input type="checkbox" name="saisie_envoi_tut" id="btradio1" value="1" <?php print $checkedtuteurdestage ?>  ></td>
</tr>

<?php $checkeddirection="checked='checked'"; if ($comptedirection == 1) { $checkeddirection="checked='checked'"; }else{ $checkeddirection=""; } ?>

<tr>
      <td width=35% align="right"  ><font class="T2"><?php print "Pour la direction" ?> :</font> </TD>
      <TD align="left"><input type="checkbox" name="saisie_envoi_dir" id="btradio1" value="1" <?php print $checkeddirection ?> ></td>
</tr>

    <tr>
      <td  align="right" valign=top><font class="T2"><?php print "la ou les classe(s)" ?> : </font></td>
      <TD  align="left">
<?php

$data=affclasse();
// $classe {XX,YY,..}
$liste_classe=preg_replace('/{/','',$classe);
$liste_classe=preg_replace('/}/','',$liste_classe);
$dataClasse=explode(",",$liste_classe);
?>
<SCRIPT LANGUAGE=JavaScript>
nbcase="<?php print count($data)?>";
nbcase+=4;
function tout() {
	for (i=10;i<=nbcase;i++) {
                document.formulaire.elements[i].checked=true;
	}
}
</SCRIPT>
<?php
$j=0;
for($i=0;$i<count($data);$i++){
	if ($j == 2 ) { $j=0; print "<br/>"; }
	$checked="";
	foreach($dataClasse as $key=>$value) {
		if ($value==$data[$i][0]) {
			$checked="checked='checked'";
			break;
		 }
     	}
      	print "<input type=checkbox  id='btradio1'  name='saisie_classe[]' value='".$data[$i][0]."' $checked />".trim($data[$i][1])."\n";
      	$j++;
}
?>
<br>
<BR><div align=right><a HREF="#" onclick="tout();"><?php print LANGCIRCU13?></a></DIV>
<br>
</td>
</tr></table><BR>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGCIRCU14 ?>","Javascript:history.go(-1)","_parent","","");</script>
<?php if ($idcirculaire != "") { ?>
	<input type=hidden name="id_circulaire" value="<?php print $id_circulaire ?>" />
	<script language=JavaScript>buttonMagicSubmit3("<?php print "Modifier"?>","modif","onclick='attente();'");</script>&nbsp;&nbsp;
<?php }else{ ?>
	<script language=JavaScript>buttonMagicSubmit3("<?php print LANGCIRCU15?>","rien","onclick='attente();'");</script>&nbsp;&nbsp;
<?php } ?>
</td></tr></table>
</form>
<BR>


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
	Pgclose();
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>

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
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
include_once("./librairie_php/ajax-select.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<FORM name=formulaire  method=post action='gestion_discipline-retenu-ajout2.php'>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGacce17 ?> </font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<BR>
<!-- // fin  -->
<?php
$id=$_GET["id"];
$data=cherche_eleve_retenu_id($id);
//id_eleve,sanction,devoir_a_faire,devoir_pour_le,demande_retenu,retenu_enrg,info_plus,motif,idprof,classe,id,description_fait,idsanction
for($i=0;$i<count($data);$i++) {
        $nom_eleve=recherche_eleve_nom($data[$i][0]);
        $prenom_eleve=recherche_eleve_prenom($data[$i][0]);
        $devoir_a_faire=$data[$i][2];
        $classe=$data[$i][9];
        $nomprof=$data[$i][8];
	$idcategory=$data[$i][1];
	$devoir=$data[$i][2];
	$motif=$data[$i][7];
	$description_fait=$data[$i][11];
	$idsanction=$data[$i][12];
}
?>
<UL>
<font class="T2">
<?php print LANGDISC6 ?> : <font color="red"><B><?php print $cl?></b></font><BR><BR>
<?php print LANGDISC7 ?> : <select name="saisie_sanction" onchange="searchRequest(this,'sanction','rien','formulaire','saisie_motif')"  >
<option value="<?php print $idcategory?>" STYLE='color:#000066;background-color:#FCE4BA'><?php print rechercheCategory($idcategory) ?></option>
<?php
select_category();
?>
</select>
<br><br>
<?php print LANGDISC8 ?> : <select name="saisie_motif">
<?php 
$sanction=rechercheSanction($idsanction);
if ($sanction != "") { 
	$sanctionvalue=preg_replace('/"/',"'",$sanction);
?>
	<option value="<?php print $sanctionvalue ?>" ><?php print $sanction ?></option>
<?php }else{ ?>
	<option></option>
<?php } ?>
</select>

<BR><BR>
<?php print LANGDISC9 ?> :
<select name="saisie_qui">
<option value="<?php print recherche_personne($nomprof) ?>"  STYLE='color:#000066;background-color:#FCE4BA'><?php print recherche_personne($nomprof)?></option>
</select>
<br><br>
Description des faits : <br><br>
<textarea name="description_fait" cols=80 rows=5><?php print $description_fait?></textarea>
<br><br>
Devoir à faire : <br><br>
<textarea name="devoir_a_faire" cols=80 rows=5><?php print $devoir?></textarea>
</UL>
</font>
<table border="1" bordercolor="#000000" width="100%">
<?php
$sub=0;
if( count($data) <= 0 )
        {
        print("<tr><td align=center valign=center><BR><font size=3>".LANGDISP1."</font><BR><BR></td></tr>");
        }
else {
?>
<tr>
<td bgcolor="yellow" ><B><?php print LANGNA1 ?> <?php print LANGNA2 ?></B></td>
<td bgcolor="yellow" width=5 align=center><B>&nbsp;Retenue&nbsp;</B></td>
<td bgcolor="yellow" width=110 ><B>Le</B></td>
<td bgcolor="yellow" ><B>À</B>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>durée</b>&nbsp;</td>
<td bgcolor="yellow" align=center>&nbsp;<A href='#' onMouseOver="AffBulle3('Attention','./image/commun/warning.jpg','<font face=Verdana size=1><B><font color=red>C</font></B>ochez la case si l\'élève est soit en <br>retenue soit sanctionné.</FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>&nbsp;</td>
</tr>
<?php
for($i=0;$i<count($data);$i++)
        {
        ?>
<tr id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td>
<?php print trunchaine($nom_eleve." ".$prenom_eleve,15)?></td>
<td align=center>
<select name="saisie_retenu_<?php print $i?>" onChange="Valid_retenue('<?php print $i?>')" >
<option value=1 STYLE='color:#000066;background-color:#FCCCCC'>Oui</option>
</select>
</td>
<td>
<input type=text name="saisie_date_retenue_<?php print $i?>" size=10 onblur="valid_date(document.formulaire.saisie_date_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)"><?php
include_once("librairie_php/calendar.php");
calendar("id1$i","document.formulaire.saisie_date_retenue_$i",$_SESSION["langue"],"0");
?>
</td>
<td>
<input type=text name="saisie_heure_retenue_<?php print $i?>" size=5  value='hh:mm' onclick="this.value=''" onblur="valid_heure(document.formulaire.saisie_heure_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)" >&nbsp;<input type=text name="saisie_duree_retenue_<?php print $i?>" size=5 onclick="this.value=''" onblur="valid_heure(document.formulaire.saisie_duree_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)" value='01:00' >
</td>
<td align=center>
<input type=checkbox name="saisie_choisi_<?php print $i?>" checked="checked" >
<input type=hidden name="saisie_pers_<?php print $i?>" value="<?php print $data[$i][0]?>">
<input type=hidden name="iddisc_prof_<?php print $i?>" value="<?php print $data[$i][10]?>">
</td>
</tr>
        <?php
        }
	$sub=1;
      }
print "</table>";
?>
<?php if ($sub == 1) { ?>
<BR>
<input type=hidden name=saisie_id value="<?php print count($data)?>">
<script>var nb=<?php print count($data)?>;</script>
<table align=center border=0><tr><td>
<script language=JavaScript>buttonMagicSubmit("Enregistrer Sanction(s)","rien"); //text,nomInput</script>
</td></tr></table>
<br>
<?php } ?>
     <!-- // fin  -->
     </td></tr></table>
     </form>
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
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>

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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
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
<FORM name=formulaire  onsubmit="return valide_discipline()" method=post action='gestion_discipline_ajoute_2.php'>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET67 ?> </font></b></td></tr>
<tr  id='cadreCentral0'>
<td >
<BR>
<!-- // fin  -->
<?php
// -----------------------------------------



// affichage de la classe
$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<UL><font class="T2"><?php print LANGDISC6?> :</font> <font color="red"><B><?php print $cl?></b></font><BR><BR>
<font class="T2"><?php print LANGDISC7?> : <font><select name=saisie_sanction onchange="searchRequest(this,'sanction','rien','formulaire','saisie_motif')"  >
<option value="-1" STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_category();
?>
</select>
<BR><BR>
<font class="T2"><?php print LANGDISC8?> :</font> <select name="saisie_motif">
<option STYLE='color:#000066;background-color:#CCCCFF' ></option>
</select>
<BR><BR>
<font class="T2"><?php print LANGDISC9?> :</font>
<select name="saisie_qui">
<option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<optgroup label="Enseignant">
<?php
select_personne_nom_len('ENS','35'); // creation des options
?>
<optgroup label="Vie Scolaire">
<?php
select_personne_nom_len('MVS','35'); // creation des options
?>
<optgroup label="Administration">
<?php
select_personne_nom_len('ADM','35'); // creation des options
?>
</select>
<br><br>
<font class="T2">Sanction donnée le : </font><input type="text" value="<?php print dateDMY() ?>" name="saisie_le" TYPE="text" size=13  class=bouton2  onKeyPress="onlyChar(event)"  onblur="valid_date(document.formulaire.saisie_le,document.formulaire.saisie_le)" >
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.saisie_le",$_SESSION["langue"],"0");
?>

<br><br>
Description des faits : <br><br>
<textarea name="description_fait" cols=80 rows=5></textarea>

<br><br>
<?php print LANGPROFJ?> : <br><br>
<textarea name="devoir_a_faire" cols=80 rows=5></textarea>
</UL>

<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;" >
<?php
$sub=0;
if( count($data) <= 0 )
        {
        print("<tr><td align=center id=bordure valign=center><BR><font size=3>".LANGPROJ6."</font><BR><BR></td></tr>");
        }
else {
?>
<tr>
<td bgcolor="yellow" ><B><?php print LANGTP1." ".LANGTP2 ?></B></td>
<td bgcolor="yellow" width=5 align=center><B>&nbsp;<?php print LANGDISC11?>&nbsp;</B></td>
<td bgcolor="yellow" width=110 ><B><?php print LANGDISC11bis?></B></td>
<td bgcolor="yellow" ><B><?php print LANGDISC11Ter?></B>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php print LANGDISC12?></b>&nbsp;</td>
<?php

$mess="<font face=Verdana size=1><B>".LANGDISC13."</FONT>";
$information="Information";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$vocal="M8";
	$information="Agent Web ".AGENTWEBPRENOM;
	$mess="<iframe width=100 height=100 src=\'http://www.triade-educ.com/agentweb/agentmel.php?inc=5&mess=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe><br>$mess" ;
}
?>


	<td bgcolor="yellow" align=center>&nbsp;<A href='#' onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/warning.jpg','<?php print $mess ?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>&nbsp;</td>
</tr>
<?php
for($i=0;$i<count($data);$i++) {
?>
<tr id='tr<?php print $i ?>' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<td>
<?php print trunchaine(ucwords($data[$i][2])." ".ucwords($data[$i][3]),35)?></td>
<td align=center>
<select name="saisie_retenu_<?php print $i?>" onChange="Valid_retenue('<?php print $i?>')" >
<option value=0 STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGNON?></option>
<option value=1 STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGOUI?></option>
</select>
</td>
<td>
<input type=text  onKeyPress="onlyChar(event)" name="saisie_date_retenue_<?php print $i?>"  size=10 onblur="valid_date(document.formulaire.saisie_date_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)"><?php
include_once("librairie_php/calendar.php");
calendarpopup("id1$i","document.formulaire.saisie_date_retenue_$i",$_SESSION["langue"],"0");
?>
</td>
<td>
<input type=text  onKeyPress="onlyChar2(event)"  name="saisie_heure_retenue_<?php print $i?>" size=5 onclick="this.value=''" onblur="valid_heure(document.formulaire.saisie_heure_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)" >&nbsp;<input type=text onKeyPress="onlyChar2(event)" name="saisie_duree_retenue_<?php print $i?>" size=5 onclick="this.value=''" onblur="valid_heure(document.formulaire.saisie_duree_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)">
</td>
<td align=center>
<input type=checkbox name="saisie_choisi_<?php print $i?>" onClick="DisplayLigne('tr<?php print $i?>')" >
<input type=hidden name=saisie_pers_<?php print $i?> value="<?php print $data[$i][1]?>">
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
<script>var nba='<?php print count($data)?>';</script>
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

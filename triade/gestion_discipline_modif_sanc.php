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
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
include_once("./librairie_php/ajax-select.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC18?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
// affichage de la liste d'élèves trouvées
//

if (isset($_POST["idmodif"])) {
	$cr=modifSanction($_POST["idmodif"],$_POST["saisie_sanction"],$_POST["saisie_motif"],$_POST["saisie_date_sanct"]);
	if ($cr) {
		alertJs("Données modifiées.");
	}
}



$motif=strtolower(trim($_POST["saisie_nom_eleve"]));
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);

?>
<?php
if( count($data) <= 0 )
        {
        print("<BR><center>".LANGDISP1."<BR><BR></center>");
        }
else {
for($i=0;$i<count($data);$i++)
        {
        ?>
<table border="1" bordercolor="#000000" width="100%"  style="border-collapse: collapse;"  >
<tr>
<td bgcolor="#FFFFFF" width=50%><?php print LANGTP1?> : <B><?php print ucwords(trim($data[$i][1]))?></b></td>
<td bgcolor="#FFFFFF"><?php print ucwords(LANGDST11) ?> : <font color=red><?php print trim($data[$i][0])?></font>
</td></tr>
<tr>
<td bgcolor="#FFFFFF"><?php print LANGTP2?> : <b><?php print ucwords(trim($data[$i][2]))?></b></td>
<td bgcolor="#FFFFFF"><?php print LANGDISC19 ?></td>
</tr>
</table>
<table border="1" bordercolor="#000000" width="100%" style="border-collapse: collapse;"  >
<TR>
<TD bgcolor='yellow' align=center width=5%>&nbsp;<?php print ucwords(LANGPROFK) ?>&nbsp;</td>
<TD bgcolor='yellow' align=center width=5%><?php print LANGDISC20?></td>
<TD bgcolor='yellow' align=center ><?php print "Motif."?> </td>
<TD bgcolor='yellow' align=center width=5%>&nbsp;<?php print ucwords("Info.") ?>&nbsp;/&nbsp;Save&nbsp;</td>
</TR>
<?php
$data_2=affSanction_par_eleve($data[$i][3]);
// id,id_eleve,motif,id_category,date_saisie,origin_saisie,signature_parent,attribuer_par,devoir_a_faire
// $data : tab bidim - soustab 3 champs
$nb=0;
for($j=0;$j<count($data_2);$j++) {
	$raison=$data_2[$j][8];
	$raison=preg_replace('/\r\n/',"<br />",$raison);
	$raison=preg_replace('/\n/',"<br />",$raison);
	$raison=html_quotes($raison);
	$qui=$data_2[$j][7];
	$qui=preg_replace('/\r\n/',"<br />",$qui);
	$qui=preg_replace('/\n/',"<br />",$qui);
	$qui=html_quotes($qui);

?>
	<TR  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<form method=POST name="formulaire<?php print $j ?>" action="gestion_discipline_modif_sanc.php" >
	<TD align=center valign="top"><span id='ida_<?php print $j?>' ><a href="#" onclick="document.getElementById('ida_<?php print $j?>').style.display='none';document.getElementById('idaa_<?php print $j?>').style.display='block'; return false;" ><?php print dateForm($data_2[$j][4])?></a></span><input type=text size=9 style="display:none" name='saisie_date_sanct' id="idaa_<?php print $j?>" value="<?php print dateForm($data_2[$j][4])?>" onKeyPress="onlyChar(event)" />
	</td>
	<TD align="center" valign="top">
	<select name=saisie_sanction onchange="searchRequest(this,'sanction','rien','formulaire<?php print $j ?>','saisie_motif')"  >
	<option id="select0" value="<?php print $data_2[$j][3] ?>"><?php print rechercheCategory($data_2[$j][3]) ?></option>
	<?php
	select_category();
	?>
	</select>
	</td>
	<TD valign="top">
	<select name="saisie_motif">
	<option id='select0' value="<?php print  $data_2[$j][2] ?>" ><?php print  $data_2[$j][2] ?></option>
	</select>
	</td> 
	<TD align='center'><a href='#'  onMouseOver="AffBulle('<?php print "Attribué par : <b>".$qui?></b> <br> Sanction : <?php print $raison?> ');"  onMouseOut="HideBulle()";><img src="image/commun/show.png" border='0' /></a>&nbsp;&nbsp;&nbsp;<a href='#' onclick='document.formulaire<?php print $j?>.submit();' title="Enregistrer" ><img src="image/commun/export.png" border='0' /></a>&nbsp;
	<input type="hidden" name="idmodif" value="<?php print $data_2[$j][0] ?>" />
	<input type="hidden" name="saisie_nom_eleve" value="<?php print $motif?>" />
	</td>
	
	</form>
	</TR>

<?php
		if (!isset($_POST["nb"])) {
			$nb++;
			if ($nb == 5) { break; }
		}
        }
?>
</table>
	<center>
<BR>
<form method=post action="gestion_discipline_modif_sanc.php">
<input type=hidden name=saisie_nom_eleve value="<?php print $motif?>" >
<input type=hidden name=nb value="aucun" >
<input type=submit value="<?php print LANGDISC21 ?> <?php print ucwords(trim($data[$i][1]))." ". ucwords(trim($data[$i][2]))?>" onclick="this.value='<?php print LANGatte_mess2 ?>'" >
</form>
<form method=post action="gestion_discipline_modif_retenue_bis.php">
<input type=hidden name=saisie_nom_eleve value="<?php print $data[$i][3]?>" >
<input type=submit value="<?php print LANGDISC22?> <?php print ucwords(trim($data[$i][1]))." ". ucwords(trim($data[$i][2]))?>" onclick="this.value='<?php print LANGatte_mess2 ?>'"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
</form>
</center>
<BR><BR>
<?php
        }
      }
?>
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
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>

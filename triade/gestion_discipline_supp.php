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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' > <?php print LANGDISC35 ?> </font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<?php
//--------------------------------------------------//
if(isset($_POST[supp_dispence])) {
        $cr=suppression_discipline($_POST[saisie_id]);
        if($cr):
//                alertJs("Sanction supprimé --  Service Triade");
        else:
                error(0);
        endif;
}
//--------------------------------------------------//
?>

<?php
// affichage de la liste d'élèves trouvées
$motif=strtolower($_POST["saisie_nom_eleve"]);
$sql=<<<EOF

SELECT c.libelle,e.nom,e.prenom,e.elev_id
FROM ${prefixe}eleves e, ${prefixe}classes c
WHERE lower(e.nom) LIKE '%$motif%'
AND c.code_class = e.classe
ORDER BY c.libelle, e.nom, e.prenom

EOF;
$res=execSql($sql);
$data=chargeMat($res);


if( count($data) <= 0 )
        {
        print("<BR><center>".LANGDISP1."<BR><BR></center>");
        }
else {
for($i=0;$i<count($data);$i++)
        {
        ?>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<td bgcolor="#FFFFFF" width=50%><?php print LANGTP1?> : <B><?php print ucwords(trim($data[$i][1]))?></b></td>
<td bgcolor="#FFFFFF"><?php print ucwords(LANGDST11) ?> : <font color=red><?php print trim($data[$i][0])?></font>
</td></tr>
<tr>
<td bgcolor="#FFFFFF"><?php print LANGTP2?> : <b><?php print ucwords(trim($data[$i][2]))?></b></td>
<td bgcolor="#FFFFFF"><?php print LANGDISC19 ?></td>
</tr>

</table>
<table border="1" bordercolor="#000000" width="100%">
<TR>
<TD bgcolor='yellow' align=center width=10%><?php print LANGDISC30?></td>
<TD bgcolor='yellow' align=center width=10%><?php print LANGPARENT15 ?></td>
<TD bgcolor='yellow' align=center ><?php print LANGABS12?> </td>
<TD bgcolor='yellow' align=center width=20%> <?php print LANGDISC9?>  </td>
<TD bgcolor='yellow' align=center width=10%><?php print LANGacce21?></td>
</TR>
<?php
$data_2=affSanction_par_eleve($data[$i][3]);
// $data : tab bidim - soustab 3 champs
$nb=0;
for($j=0;$j<count($data_2);$j++)
        {
?>
	<TR  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<form method=POST>
<TD align=center valign=top><?php print dateForm($data_2[$j][4])?></td>
	<TD align=center valign=top><?php print rechercheCategory($data_2[$j][3])?></td>
	<TD valign=top><?php print $data_2[$j][2]?></td>
	<TD valign=top><?php print $data_2[$j][7]?></td>
	<TD align=center><input type=submit name=supp_dispence value="<?php print LANGacce21?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"  onclick="this.value='<?php print LANGattente222?>'">
	<input type=hidden name=saisie_id value="<?php print $data_2[$j][0]?>">
	<input type=hidden name=saisie_nom_eleve value="<?php print $_POST[saisie_nom_eleve]?>">
	</td>
	</form>
	</TR>

<?php
	$nb++;
	if ($nb == 5) { break; }
        }
?>
</table>
	<center>
<BR>
<form method=post action="gestion_discipline_supp_2.php">
<input type=hidden name=saisie_nom_eleve value="<?php print $data[$i][3]?>" >
<input type=submit value="Liste complete de <?php print ucwords(trim($data[$i][1]))." ". ucwords(trim($data[$i][2]))?>" onclick="this.value='<?php print LANGattente2?>'" >
</form>
<form method=post action="gestion_discipline_supp_retenue.php">
<input type=hidden name=saisie_nom_eleve value="<?php print $data[$i][3]?>" >
<input type=submit value="Supprimer une retenue de <?php print ucwords(trim($data[$i][1]))." ". ucwords(trim($data[$i][2]))?>" onclick="this.value='<?php print LANGattente2?>'"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
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
</BODY></HTML>

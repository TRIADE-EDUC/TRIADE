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
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' > <?php print LANGTITRE28?> </font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->

<?php
//--------------------------------------------------//
if(isset($_POST["modif_dispence"])) {

$heure0="saisie_heure_0";
$jour0="saisie_jour_0";
$heure1="saisie_heure_1";
$jour1="saisie_jour_1";
$heure2="saisie_heure_2";
$jour2="saisie_jour_2";

$id_eleve=$_POST["saisie_eleve_id"];
$certif=$_POST["saisie_certif"];
$motif=$_POST["saisie_motif"];
$date_debut=$_POST["saisie_date_debut"];
$date_fin=$_POST["saisie_date_fin"];
$matiere=$_POST["saisie_matiere"];
$heure0=$_POST[$heure0];
$jour0=$_POST[$jour0];
$heure1=$_POST[$heure1];
$jour1=$_POST[$jour1];
$heure2=$_POST[$heure2];
$jour2=$_POST[$jour2];


if ($certif == "") { $certif="false"; }

/*
print $id_eleve."<BR>";
print $certif."<BR>";
print $motif."<BR>";
print $date_debut."<BR>";
print $date_fin."<BR>";
print $matiere."<BR>";
print $heure0."<BR>";
print $jour0."<BR>";
print $heure1."<BR>";
print $jour1."<BR>";
print $heure2."<BR>";
print $jour2."<BR>";
*/

if (strtolower($motif) == strtolower(LANGINCONNU)) { $motif="inconnu"; }
	$cr=modif_dispence($id_eleve,$matiere,$date_debut,dateFormBase($date_fin),dateDMY2(),$_SESSION["nom"],$certif,$motif,$heure0,$jour0,$heure1,$jour1,$heure2,$jour2);

        if($cr):
                  alertJs(LANGBT36);
        else:
         //       error(0);
        endif;
}
//--------------------------------------------------//
?>

<?php
// affichage de la liste d'élèves trouvées
$jj=0;
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
        print("<BR><center><font size=3>".LANGDISP1." </font><BR><BR></center>");
        }
else {
for($i=0;$i<count($data);$i++)
        {
        ?>
<table border="1" bordercolor="#000000" width="100%">
<tr>
<td bgcolor="#FFFFFF" width=50% id='bordure' ><?php print LANGEL1 ?> : <B><?php print ucwords(trim($data[$i][1]))?></b></td>
<td bgcolor="#FFFFFF" id='bordure'><?php print LANGCALEN7 ?> : <font color=red><?php print trim($data[$i][0])?></font>
</td></tr>
<tr>
<td bgcolor="#FFFFFF" id='bordure'><?php print LANGEL2 ?> : <b><?php print ucwords(trim($data[$i][2]))?></b></td>
<td bgcolor="#FFFFFF" id='bordure'> <?php print LANGDISP11 ?> </td>
</tr>
</table>
<table border="1" bordercolor="#000000" width="100%">
<TR>
<TD bgcolor='yellow' align=center width=10%><?php print LANGPARENT10?> </td>
<TD bgcolor='yellow' align=center ><?php print LANGABS12 ?> </td>
<TD bgcolor='yellow' align=center width=5%> <?php print LANGTE6 ?>  </td>
<TD bgcolor='yellow' align=center width=10%> <?php print LANGTE12 ?>  </td>
<TD bgcolor='yellow' align=center width=10%><?php print LANGPER30?></td>
</TR>
<?php
$data_2=affDispence($data[$i][3]);
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data_2);$j++)
        {
	$jj++;
	if ($data_2[$j][10] != " ") {
		$suite="<BR>".$data_2[$j][10];
		$suite2="<BR>".$data_2[$j][11];
	}
	if ($data_2[$j][12] != " ") {
                $suite3="<BR>".$data_2[$j][12];
                $suite4="<BR>".$data_2[$j][13];
        }

$k=$data_2[$j][1];
$sql="SELECT code_mat, libelle FROM ${prefixe}matieres WHERE  code_mat='$k' ORDER BY code_mat";
$res=execSql($sql);
$data_matiere=chargeMat($res);

?>

	<TR  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<form method=POST name="formulaire_<?php print $jj?>" >
<TD align=center valign=top id='bordure' ><?php print dateForm($data_2[$j][2])?>
	<br>au <br> <input type=text value="<?php print dateForm($data_2[$j][3])?>" name="saisie_date_fin"  size=11>
	</td>
	<TD valign=top id='bordure'>
<?php
$motif=$data_2[$j][7];
if ($data_2[$j][7] == "inconnu" ) { $motif=LANGINCONNU; }
?>
<input type=text value="<?php print $motif?>" size=30  name="saisie_motif">
<script language=JavaScript>
document.formulaire_<?php print $jj?>.saisie_matiere.options.text="<?php print $data_2[$j][1]?>"
</script>
<script language=JavaScript>
	<?php
		if (DBTYPE == "pgsql") {
			if ($data_2[$j][6] == "t" ) {
				$booleen="checked";
			}else {
				$booleen="";
			}
		}
		if (DBTYPE == "mysql") {
			if ($data_2[$j][6] == "1" ) {
                                $booleen="checked";
                        }else {
                                $booleen="";
                        }
		}
	?>
</script>
<?php print LANGPARENT13 ?> : <input type=checkbox name="saisie_certif" <?php print $booleen?> > <I>(oui)</I><BR>
<?php print LANGEN ?><b> <?php print $data_matiere[0][1]?></b>
	</td>
	<TD valign=top id='bordure'>
<input type=text maxlength=5 value="<?php print trim($data_2[$j][8])?>" name="saisie_heure_0" size=5><BR>
<input type=text maxlength=5 value="<?php print trim($data_2[$j][10])?>" name="saisie_heure_1" size=5><BR>
<input type=text maxlength=5 value="<?php print trim($data_2[$j][12])?>" name="saisie_heurer_2" size=5>
	<TD valign=top>
<?php
$k=9;
for ($a=0;$a<3;$a++) {
?>
<select name="saisie_jour_<?php print $a?>" >
<option STYLE='color:#000066;background-color:#FCE4BA'></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGLUNDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGMARDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGMERCREDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGJEUDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGVEBDREDI?></option>
<option STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGSAMEDI?></option>
<option ></option>
</select>
<script language=JavaScript>
document.formulaire_<?php print $jj?>.saisie_jour_<?php print $a?>.options[0].text="<?php print trim($data_2[$j][$k])?>";
</script>
<?php
 $k=$k+2;
 }

?>
</td>
<TD align=center><input type=submit name="modif_dispence" value="Valider" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
	<input type=hidden name=saisie_eleve_id value="<?php print $data[$i][3]?>">
	<input type=hidden name=saisie_date_debut value="<?php print $data_2[$j][2]?>">
	<input type=hidden name=saisie_nom_eleve value="<?php print $data[$i][1]?>">
	<input type=hidden value="<?php print $data_2[$j][1]?>" name="saisie_matiere">
	</td>
	</form>
	</TR>
<tr>
<td colspan=6 bgcolor="#FFFFFF" id='bordure'>
Derniere  modif: <?php print dateForm($data_2[$j][4])?>
 - <?php print LANGTE3 ?> : <?php print ucwords($data_2[$j][5])?>
</td></tr>

<?php
        }
?>
</table>
<BR>
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

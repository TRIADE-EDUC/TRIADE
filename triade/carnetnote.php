<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // debut form  -->
<blockquote>
<?php if ($_SESSION["membre"] == "menuadmin") { ?>
	<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
	<br>
 <font class="T2"><?php print LANGBULL3 ?> :</font>
                 <select name='anneeScolaire'  >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,10);
                 ?>
                 </select>
                <br><br>


<BR>
<font class="T2"><?php print LANGCARNET2 ?> : </font><select id="saisie_classe" name="saisie_classe">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_classe(); // creation des options
?>
                               </select> <BR><br>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script>
<br><br>
</UL></UL></UL>
</form>


<?php } ?>

<br><br>
<form method=post action="notevisuadmin.php" name="formulaire1"
	onsubmit="return valide_choix_pers('<?php print " un enseignant" ?>')" >
<font class="T2"><?php print LANGMESS238 ?>  :</font> <select name="saisie_pers">
             <option   STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_personne_2('ENS','25'); // creation des options
?>
</select>

 <BR><br>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script>
<br><br>
</UL></UL></UL>


 </blockquote>
 </form>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if($_POST["consult"])
{
$saisie_classe=$_POST["saisie_classe"];
$anneeScolaire=$_POST["anneeScolaire"];

//$sql="(SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire') UNION (SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire'  ORDER BY e.nom)";
 $sql=" SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom,code_class FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' UNION ALL SELECT c.libelle,e.elev_id,e.nom,e.prenom,e.classe FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY s.nom";


// $sql="SELECT libelle,elev_id,nom,prenom,code_class FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
<tr id='coulBar0' ><td height="2" colspan=2 ><b><font   id='menumodule1' >
		<?php print LANGELE4 ?> : <font id="color2" ><B><?php print $cl?></font>
		/ </b>  <?php print LANGCOM3 ?> <font id="color2"><b><?php print count($data) ?></b></font>  / Année Scolaire <font id="color2"><b><?php print $anneeScolaire ?></b></font> / &nbsp; <?php print LANGCARNET3 ?> </font>
	</font></td>
</tr>
<?php
if( count($data) <= 0 )
	{
	print("<tr id='cadreCentral0'><td  align=center valign=center><font class=T2>".LANGRECH1."</font></td></tr>");
	}
else {
?>
<tr ><td bgcolor="yellow"> <B><?php print LANGTP1 ?></B></td><td bgcolor="yellow"><B><?php print LANGTP2 ?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td >
	<a href="./carnetnote2.php?nom=<?php print strtolower($data[$i][2])?>&prenom=<?php print strtolower($data[$i][3])?>&id_pers=<?php print $data[$i][1]?>&idClasse=<?php print $saisie_classe?>&anneeScolaire=<?php print $anneeScolaire ?>"><?php print strtoupper($data[$i][2])?></a></td>
	<td ><?php print ucwords($data[$i][3])?></td>
	</tr>
	<?php
	}
      }
print "</table>";
}
?>
     <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

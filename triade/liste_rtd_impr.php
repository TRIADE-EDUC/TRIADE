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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTITRE23?></font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br>
<!-- // fin  -->

<ul><font class="T2"><?php print "Indiquer la liste des parents d'étudiants qui recevront un email" ?>.</font><br><br></ul>

<form method="post">
<table>
<tr>
<td><script language=JavaScript>buttonMagicSubmit("<?php print "Trier par nom" ?>","trie_nom"); //text,nomInput</script>
<script language=JavaScript>buttonMagicSubmit("<?php print "Trier par date" ?>","trie_date"); //text,nomInput</script></td>
</tr>
<tr><td height='15'></td></tr>
<tr>
<td>&nbsp;&nbsp;<font class='T2'>Indiquer la classe : <select name="saisie_classe" onchange='this.form.submit()'>
<?php if ( (isset($_POST["saisie_classe"])) && ($_POST["saisie_classe"] != 'tous')) {
	print "<option  value='".$_POST["saisie_classe"]."' selected  id='select1' >".trunchaine(chercheClasse_nom($_POST["saisie_classe"]),35)."</option>";
}
?>
<!-- <option  value='tous' id='select0' ><?php print 'Toutes les classes' ?></option> -->
<?php select_classe2(35);?>
</select>
</font></td>
</form>
</tr></table>
<br>

<?php
if (isset($_POST["trie_nom"])) {
	$trie='nom';
}

if (isset($_POST["trie_date"])) {
	$trie='date';
}

if (isset($_POST["trie_classe"])) {
	$trie='classe';
}
if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
}
?>

<?php
if ($idclasse != "") { 
	print "<form method=post action='liste_rtd_impr2.php'><table border=1 bgcolor='#FFFFFF' border=1 align='center' width='100%' >";
	$data_2=affRetardNonJustifie2bis($trie,$idclasse);
	//a.elev_id, a.heure_ret, a.date_ret, a.date_saisie, a.origin_saisie, a.duree_ret, a.motif, a.idmatiere, e.nom, e.elev_id, e.classe,a.courrierenvoyer
	// $data : tab bidim - soustab 3 champs
	for($j=0;$j<count($data_2);$j++) {
		$ideleve=$data_2[$j][0];
		$idmatiere=$data_2[$j][7];
		$courrierenvoyer=$data_2[$j][11];
		$emailP1=$data_2[$j][12];
                $emailP2=$data_2[$j][13];
		if ($data_2[$j][0] == "-4") {
			continue;
		}
		if ($idmatiere != null) {
			$nomMatiere="pour le cours ". chercheMatiereNom($idmatiere);
		}
		$duree=$data_2[$j][5];
		if ($data_2[$j][5] == "0" ) {
			$duree="???";
		}

                if ((trim($emailP1) == "") && (trim($emailP2) == "")) {
                        $disabled="disabled='disabled'";
                        $title=" title=\"Aucun email parent d'indiqué\" ";
                        $img="<img src='image/commun/alerte.png' $title  />";
                }else{
                        $title="";
                        $disabled="";
                        $img="";
                }
	
		$datedebut=$data_2[$j][2];
		$heure=$data_2[$j][1];
		$classe=chercheClasse_nom($data_2[$j][10]);
	
		print "<tr  id='tr$j'  class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
		print "<td id='bordure' >".trunchaine(strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve))),15)."</td>";
		print "<td id='bordure' >$classe</td>";
		print "<td id='bordure' > en retard le ".dateForm($data_2[$j][2])." à ".timeForm($data_2[$j][1])." $nomMatiere </td>";
		print "<td id='bordure' ><input type=checkbox $disabled $title name=liste[] value='$ideleve;$datedebut;$heure;$duree;$nomMatiere' onClick=\"DisplayLigne('tr$j');\" >";
		print "$img </td>";
		print "</tr> ";
	}

	if (count($data_2) == "0") {
                print "<tr><td id='bordure' align='center'><font class='T2'>aucune donnée</font></td></tr>";
        }


?>
	</table>
	<input type=hidden name=nb value="<?php print count($data_2) ?>">
	<br><br>
	<table align=center><tr><td>
	<script>buttonMagicRetour2('gestion_abs_retard.php','_self','Retour')</script> 
	<?php
		if (count($data_2) == "0") { 	
			print "<script language=JavaScript>buttonMagicSubmit(\"Envoyer email\",'rien');</script>";
		}
	?>
	</form>
	</td></tr></table>
<?php } ?>
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

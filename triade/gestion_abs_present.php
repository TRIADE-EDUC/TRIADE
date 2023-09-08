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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if (PRESENTPROF == "oui") { 
	validerequete("3");
}else{
	if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
		$profpclasse=$_SESSION["profpclasse"];
		validerequete("menuprof");
	}else{
		validerequete("2");
	}
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post  name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Valider les presents "?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // debut form  -->
<blockquote><BR>
               <font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe">
				   <option id='select0' value="0" ><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select>
<br><bR>

<font class=T2><?php print "Indiquer un groupe" ?> :</font> <select id="saisie_groupe" name="saisie_groupe">
				   <option id='select0' value="0" ><?php print LANGCHOIX?></option>
<?php
select_groupe_id();
?>
</select>
<br><br>

<font class=T2><?php print "Indiquer une étude"?> :</font> <select id="saisie_etude" name="saisie_etude">
				   <option id='select0' value="0" ><?php print LANGCHOIX?></option>
<?php
select_etude();
?>
</select> <BR><br>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
<?php
$fic="gestion_abs_retard.php";
if ($_SESSION["membre"] == "menuprof") $fic="retardprof.php";
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) $fic="gestion_abs_retard.php";
?>
<script language=JavaScript>buttonMagicRetour2('<?php print $fic ?>','_self','Retour menu')</script>
</br>
<?php
if (isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];
	$saisie_etude=$_POST["saisie_etude"];
	$saisie_groupe=$_POST["saisie_groupe"];

	if ($saisie_classe != "0") {
		$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
		$res=execSql($sql);
		$data=chargeMat($res);
	}elseif ($saisie_etude != "0") {
		$idetude=$_POST["saisie_etude"];
		$typevaleur=$_POST["saisie_etude"];
		$typechamps="saisie_etude";
		$sql="SELECT id_etude,id_eleve FROM ${prefixe}etude_affect WHERE id_etude='$idetude' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		$idmatiere="-".$data[0][0]; // correspond à l'etude avec le "-"
		$nometude=rechercheEtude($data[0][0]);
		for($i=0;$i<count($data);$i++) {
			$liste_eleves.=$data[$i][1].",";
		}
		$liste_eleves=preg_replace('/,$/',"",$liste_eleves);
		if ($liste_eleves != "") {
			$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
			$res=execSql($sql);
			$data=chargeMat($res);
		}
	}elseif ($saisie_groupe != "0") {
		$gid=$saisie_groupe;
		$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid' ";
		$res=execSql($sql);
		$data=chargeMat($res);
		$cl=$data[0][0];
		$nomgrp=$data[0][0];
		$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
		$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
		$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
		$res=execSql($sql);
		$data=chargeMat($res);
	}

	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];

	if( count($data) > 0 ) {
		$fic=$_POST["saisie_classe"];
		$fichierpdf="./data/pdf_certif/Classe_".suppCaracFichier($cl).".pdf";
	
		if ($_SESSION['membre'] == "menuadmin") print "<script language=JavaScript>buttonMagic('Imprimer','visu_pdf_admin.php?id=$fichierpdf','_blank','','');</script>";
	
		define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
		include_once('./librairie_pdf/fpdf/fpdf.php');
		include_once('./librairie_pdf/html2pdf.php');
		include_once("librairie_php/timezone.php");
	
		$pdf=new PDF();  // declaration du constructeur
		$pdf->AddPage();

		$date=dateDMY();
		// insertion de la Annee SCOLAIRE
		$cl=ucwords($cl);
		if ($saisie_classe != "0") { $Pdate="En classe $cl -  $date"; }
		if ($saisie_groupe != "0") { $Pdate="En groupe $nomgrp -  $date"; }
		if ($saisie_etude != "0") { $Pdate="En étude $nometude -  $date"; }


		$pdf->SetFont('Courier','',12);
		$xcoor0=30;
		$ycoor0=20;
		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->WriteHTML($Pdate);
		
		$xcoor0+=20;
		$ycoor0+=10;
		$j=0;
		for($i=0;$i<count($data);$i++) {

			if ($ii == 45) {
	                	$pdf->AddPage();
				$ii=0;
				$xcoor0=50;
				$ycoor0=20;
			}
			$ii++;

			$ycoor0+=5;
			$j++;
			$eleve=$j.") ".strtoupper($data[$i][2])." ".trunchaine(ucwords($data[$i][3]),30);
			$pdf->SetXY($xcoor0,$ycoor0);
			$pdf->WriteHTML($eleve);
		}
		if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
		$pdf->output('F',$fichierpdf);
	}
}

?>
</UL></UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if(isset($_POST["consult"])) { ?>
	<BR><BR><BR>
	<form method="post" name="formulaire2" action="gestion_abs_present2.php">
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan="3">
	<b><font   id='menumodule1' >
<?php if (($saisie_classe != "0") && ($cl != "")) { ?>
	<?php print LANGELE4?> : <font id="color2"><b><?php print $cl?></b></font>&nbsp;&nbsp; <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b></font></font></td></tr>
<?php } ?>

<?php if (($saisie_groupe != "0") && ($nomgrp != "")) { ?>
	<?php print "Groupe" ?> : <font id="color2"><b><?php print $nomgrp?></b></font>&nbsp;&nbsp; <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b></font></font></td></tr>
<?php } ?>

<?php if (($saisie_etude != "0") && ($nometude != "")) { ?>
	<?php print "Etude" ?> : <font id="color2"><b><?php print $nometude?></b></font>&nbsp;&nbsp; <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b></font></font></td></tr>
<?php } ?>


	<tr><td id='cadreCentral0' > 
<?php 
	if( count($data) <= 0 ) {
		print("<center><font class=T2>".LANGRECH1."</font></center>");
	} else {
?>		<table>
		<tr><td colspan="3" > <br>
		<font class=T2> Horaire : <select name="saisie_heure" onChange="fonc2()">
		<?php
		$disabled="disabled";
		$data3=recupCreneauDefault("creneau"); // libelle,text
		if (count($data3) > 0) {
			$data3=recupInfoCreneau($data3[0][1]);
			print "<option  id='select1' value=\"".trim($data3[0][0])."#".$data3[0][1]."#".$data3[0][2]."\" >".trim($data3[0][0])." : ".timeForm($data3[0][1])." - ".timeForm($data3[0][2])."</option>\n";
			$disabled="";
		}else{
		?>
			<option STYLE='color:#000066;background-color:#FCE4BA' value="null" ><?php print LANGCHOIX ?></option>
		<?php
		}
		select_creneaux2();
		?>
		</select> - <input type=text name="datedepart" value="<?php print dateDMY() ?>" size='12' readonly class="bouton2" /> <?php
		include_once("librairie_php/calendar.php");
		calendar('id1','document.formulaire2.datedepart',$_SESSION["langue"],"1");
		?>

		</font> 
		<br><br>

		<font class=T2>Matière : </font><select name="idmatiere" >
		<option STYLE='color:#000066;background-color:#FCE4BA' value="" ><?php print LANGCHOIX ?></option>
		<?php select_matiere3("20") ?>
		</select>
		<br><br>

		<font class=T2>Enseignant : </font><select name="idprof" >
		<option STYLE='color:#000066;background-color:#FCE4BA' value="" ><?php print LANGCHOIX ?></option>
		<?php select_personne_2('ENS',25) ?>
		</select>
		
		</td></tr>

		<tr><td height="20" ></td></tr>

		<tr>
		<td bgcolor="yellow" > <B>&nbsp;<?php print ucwords(LANGIMP8)?>&nbsp;</B></td>
		<td bgcolor="yellow"><B>&nbsp;<?php print ucwords(LANGIMP9)?>&nbsp;</B></td>
		<td bgcolor="yellow" width="1%"><b>&nbsp;<?php print ucwords("présent")?>&nbsp;</b></td>
		</tr>
<?php
		for($i=0;$i<count($data);$i++) {
	?>
	<tr   id='tr<?php print $i ?>' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td><?php
	$photoeleve="image_trombi.php?idE=".$data[$i][1];
	print "<a href='#' onMouseOver=\"AffBulle('<img src=\'$photoeleve\' >' );\"  onMouseOut='HideBulle()'>".ucwords($data[$i][2])."</a></td>";?>
	<td><?php print trunchaine(ucwords($data[$i][3]),30)?></td>
	<td><input type=checkbox value="<?php print $data[$i][1]?>" name="ideleve[]" onclick="DisplayLigne('tr<?php print $i?>',this.value);" ></td>
	</tr>
	<?php
		}

$fic="gestion_abs_retard.php";
if ($_SESSION["membre"] == "menuprof") $fic="retardprof.php";
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) $fic="gestion_abs_retard.php";

print "</table><br><script language=JavaScript>buttonMagicSubmit('".LANGENR."','create');</script> <script language=JavaScript>buttonMagicRetour2('$fic','_self','Retour menu')</script><br><br>";

	}

print "</td></tr></table><br>";

}
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
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
</BODY>
</HTML>

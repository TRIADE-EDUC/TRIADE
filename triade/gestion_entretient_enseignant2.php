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
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

$date=date("Y");
$date2=date("Y")-1;

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<?php
$heureDebut="hh:mm";
$heureFin="hh:mm";
$date="";
if (isset($_GET["modif"])) {
	$data=recupEntretiensProf($_GET["eid"],$_GET["modif"]); // ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation
	if ($data != "") {
		$eid=$data[0][0];	
		$heureDebut=timeForm($data[0][2]);
		$heureFin=timeForm($data[0][3]);
		$date=dateForm($data[0][1]);
		$commentaire=$data[0][5];
		$len=strlen($commentaire);
		$preparation=$data[0][8];
	}
}

if (isset($_GET["eid"])) { $eid=$_GET["eid"]; }
if (isset($_POST["idpers"])) {$eid=$_POST["idpers"]; }
if($eid) {
	$sql="SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM ${prefixe}personnel WHERE pers_id='$eid'";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nomProf=$data[0][1];
	$prenomProf=$data[0][2];

	if (!is_dir("./data/pdf_bull/entretien")) { mkdir("./data/pdf_bull/entretien"); }
	$nomProf1=TextNoAccent($nomProf);
	$prenomProf1=TextNoCarac($prenomProf);
	$fichier="./data/pdf_bull/entretien/".$nomProf1."_".$prenomProf1.".pdf";
}



?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
		<?php print "Enregistrement d'entretien" ?> </b>
	</font></td>
</tr>
<?php

if( count($data) <= 0 ) {
	print("<tr id='cadreCentral0' ><td align=center valign=center>"."<font class='T2'>Aucun compte pour cette recherche</font>"."</td></tr>");
}else {
?>
<tr  id='cadreCentral0' >
<td >
<br>
<form method="post" name="formulaire" onsubmit="return validentretien()" action="gestion_entretient_enseignant2.php" >
<table border=0 width="100%" >
<tr><td>
<font class=T2>&nbsp;&nbsp;&nbsp;</font><img src="image_trombi.php?idP=<?php print $eid ?>" border=0 >
</td><td>
<font class=T2>
Nom prénom : <b><?php print ucwords($data[0][1]." ".$data[0][2])?></b>
<br><br>
Classe : <b><?php print ucwords($data[0][3])?></b>
<br><font class=T1>
<?php 
$lvo=chercheLvo($eid);   // lv1,lv2,`option`
?>
Lv1/Spé : <a href="#" title="<?php print $lvo[0][0] ?>" ><?php print trunchaine($lvo[0][0],40); ?></a>
<br>
Lv2/Spé : <a href="#" title="<?php print $lvo[0][1] ?>" ><?php print trunchaine($lvo[0][1],40); ?></a>
<br>
Option : <a href="#" title="<?php print $lvo[0][2] ?>" ><?php print trunchaine($lvo[0][2],40); ?></a>
</font>
<br>
<br> 
<?php
$checked="";
if ($preparation == 1) { $checked="checked='checked'"; }
?>
Préparation d'entretien : <input type="checkbox" name="preparation" value="1" <?php print $checked ?> />  (oui)
<br>
<br>le : </font> 
<input type="text" name="saisiedate"  onKeyPress="onlyChar(event)"  size=10 value="<?php print $date ?>" > 
<?php include_once("librairie_php/calendar.php"); 
calendarDim('id2','document.formulaire.saisiedate',$_SESSION["langue"],"1","0");
?> à <input type="text" name="heuredepart"  value="<?php print $heureDebut ?>"  onclick="this.value=''" size=5   onKeyPress="onlyChar2(event)" > 
</td></tr></table>

<br>

<font class=T2>&nbsp;&nbsp;&nbsp;Objet et contenu de l'entretien / Conclusion - Actions </font>
<br><br><font class=T2>&nbsp;&nbsp;&nbsp;</font><textarea name="objet" style="width:400;font-size:16" onkeypress="compter(this,'2000', this.form.CharRestant)" cols='100' rows='20' ><?php print $commentaire ?></textarea>
<br><font class=T2>&nbsp;&nbsp;&nbsp;</font><input type='text' name='CharRestant' size='2' disabled='disabled' value="<?php print $len ?>" > (2000 caractères maximum)

<br><br><font class=T2>&nbsp;&nbsp;&nbsp;L'entretien s'est terminé à  : <input type="text" name="heurefin"  value="<?php print $heureFin ?>"  onclick="this.value=''" size=5   onKeyPress="onlyChar2(event)" > 

<input type="hidden" name="idpers"  value="<?php print $data[0][0] ?>"  /> 
<input type="hidden" name="identretien"  value="<?php print $_GET["modif"]  ?>"  /> 
<br><br>
<?php
$act="create";
$textbutton=LANGENR;
if (isset($_GET["modif"])) {
	$act="modify";
	$textbutton=LANGPER30;
}
?>
<table border="0" align="center" ><td><script language=JavaScript>buttonMagicSubmit("<?php print $textbutton?>","<?php print $act ?>"); //text,nomInput</script></td><td>
&nbsp;&nbsp;<input type="button" value="Imprimer les entretiens" class="bouton2" onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" />
	<input type="button" value="Prochain rendez-vous" class="bouton2" onclick="open('gestion_entretient_enseignant3.php?eid=<?php print $eid ?>','','width=400,height=300');" />
</td></tr></table>

<br>
</td></tr></table>

<br><br>

<?php 
if (isset($_POST["create"])) {
	enrg_entretienProf($_POST["idpers"],$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["preparation"]);
}

if (isset($_POST["modify"])) {
	modif_entretienProf($_POST["idpers"],$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["identretien"],$_POST["preparation"]);
}

if (isset($_GET["supp"])) {
	suppEntretienProf($_GET["supp"]);
}
?>

<?php
// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Entretien Individuel - $nomProf $prenomProf");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Entretien Individuel "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$xcoor0=0;
$ycoor0=0;


$pdf->SetFont('Arial','B',14);
$pdf->SetXY(0,$ycoor0+=10);
$pdf->MultiCell(210,10,"Année $date2 - $date",0,'C',0);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(0,$ycoor0+=10);
$pdf->MultiCell(210,10,"JOURNAL D'ENTRETIENS INDIVIDUELS",0,'C',0);

$cumulHeure=cumulEntretienProf($data[0][0],$data[0][3]);

$nomprenom=ucwords($data[0][1]." ".$data[0][2]);
$pdf->SetFont('Arial','',12);
$pdf->SetXY(20,$ycoor0+=20);
$pdf->MultiCell(100,10,"Prénom / Nom de l'Enseignant : $nomProf ",0,'L',0);
$pdf->SetXY(20,$ycoor0+=10);
$clas=$data[0][3];
$pdf->MultiCell(100,10,"Classe : $clas - Cumul des heures : $cumulHeure ",0,'L',0);
$pdf->SetXY($x,$ycoor0+=10);
$x=10;



?>


<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
<?php print "Journal d'entretiens de " ?>  <font id="color2"><B><?php print ucwords($data[0][1]." ".$data[0][2])?></b></font></font></td></tr>
<tr  id='cadreCentral0' >
<td valign=top >
<br>
<font class=T2>&nbsp;&nbsp; Cumul des heures : <?php print $cumulHeure ?></font>
<br><br>
<table border=1 bgcolor="#FFFFFF" width='100%' bordercolor="#000000"  >
<?php

$data=listeEntretienProf($eid); //idpers,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation
$j=0;$A=0;
for($i=0;$i<count($data);$i++) {
	$j++;
	$date=dateForm($data[$i][1]);
	$preparation=$data[$i][8];

	if ($preparation == 1) {
		$preparation="Préparation d'entretien";
	}else{
		$preparation="Entretien";
	}

	$pdf->SetXY($x,$ycoor0+=10);
	$pdf->MultiCell(190,60,"",1,'L',0);

	$pdf->SetXY($x,$ycoor0);
	$pdf->SetFont('Arial','',9);
	$nomprof=$data[$i][6];
	$time=timeForm($data[$i][2])." ".timeForm($data[$i][3]);
	$duree=timeForm(calculduree($data[$i][2],$data[$i][3]));

	$pdf->MultiCell(190,5,"$preparation le $date  - ($time) durée  $duree - Reçu(e) par $nomprof ",1,'L',0);
	$pdf->SetXY($x,$ycoor0+5);
	$pdf->SetFont('Arial','B',9);
	$pdf->MultiCell(190,5,"Objet et contenu de l'entretien / Conclusion - Actions",0,'L',0);

	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($x,$ycoor0+10);
	$mess=preg_replace('/\n/'," ",$data[$i][5]);
	$mess=stripslashes($mess);

	$pdf->MultiCell(190,3,"$mess",0,'L',0);
	$A++;
	if (($j == 3) && ($A < 4)){
		$pdf->AddPage();
		$ycoor0=10;
		$A=0;
	}elseif($A == 4) {
		$A=0;
		$pdf->AddPage();
		$ycoor0=10;

	}else{
		$ycoor0+=50;
	}

?>
	<tr>
	<td id="bordure" valign="top"><u><?php print $preparation  ?></u> le  <?php print $date ?> (<?php print $time?>) durée <?php print $duree ?> reçu(e) par <?php print $nomprof?>
	<br><br><?php print stripslashes($data[$i][5])?><br><br>
	<div align=right>[ <a href="gestion_entretient_enseignant2.php?modif=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>">Modifier</a> ] [ <a href="gestion_entretient_enseignant2.php?supp=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>">Supprimer</a> ]</div>
	</td>
	</tr>
	<tr><td id="bordure" ><hr></td></tr>
	
<?php
}
?>
</table>

</td></tr>

<?php
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);

?>

<?php  }  ?>
</table>




<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

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
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
if (($_SESSION["membre"] == "menuprof") && (ENTRETIENPROF == "oui") ) {
	validerequete("menuprof");
}elseif($_SESSION["membre"] == "menupersonnel") {
	if (!verifDroit($_SESSION["id_pers"],"entretien")) {
		accesNonReserveFen();
		exit;
	}
}else{
	validerequete("menuadmin");
}
$cnx=cnx();

if (defined("PASSMODULEINDIVIDUEL")) {
	if (PASSMODULEINDIVIDUEL == "oui") {
		if (empty($_SESSION["adminplus"])) {
			print "<script>";
			print "location.href='./base_de_donne_key.php?key=passmoduleindividuel'";
			print "</script>";
		}
	}
}

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
	$data=recupEntretiens($_GET["eid"],$_GET["modif"]); // ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation
	if ($data != "") {
		$eid=$data[0][0];	
		$heureDebut=timeForm($data[0][2]);
		$heureFin=timeForm($data[0][3]);
		$date=dateForm($data[0][1]);
		$commentaire=$data[0][5];
		$len=strlen($commentaire);
		$preparation=$data[0][8];
		$identretien=$data[0][7];
	}
}
if (isset($_GET["eid"])) { $eid=$_GET["eid"]; }
if (isset($_POST["ideleve"])) {$eid=$_POST["ideleve"]; }
if($eid) {
	$sql="SELECT elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,lieu_naissance,nationalite,	passwd,	passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,email,email_eleve,class_ant,annee_ant,tel_eleve,email_resp_2,sexe,code_compta,code_class FROM ${prefixe}eleves, ${prefixe}classes c WHERE elev_id='$eid' AND c.code_class=classe";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nomEleve=$data[0][1];
	$prenomEleve=$data[0][2];
	$idclasse=$data[0][45];

	if (!is_dir("./data/pdf_bull/entretien")) { mkdir("./data/pdf_bull/entretien"); }
	$nomEleve1=TextNoAccent($nomEleve);
	$prenomEleve1=TextNoCarac($prenomEleve);
	$fichier="./data/pdf_bull/entretien/".$nomEleve1."_".$prenomEleve1.".pdf";
}



?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
		<?php print "Enregistrement d'entretien" ?> </b>
	</font></td>
</tr>
<?php

if( count($data) <= 0 ) {
	print("<tr id='cadreCentral0' ><td align=center valign=center>".LANGRECH3."</td></tr>");
}else {
?>
<tr  id='cadreCentral0' >
<td >
<br>
<form method="post" name="formulaire" onsubmit="return validentretien()" action="entretien2.php" >
<table border=0 width="100%" >
<tr><td width='5%' valign='top' >
<font class=T2>&nbsp;&nbsp;&nbsp;</font><img src="image_trombi.php?idE=<?php print $eid ?>" border=0 >
</td><td>
<font class=T2>
Nom prénom : <b><?php print ucwords($data[0][1]." ".$data[0][2])?></b>
<br><br>
Classe : <b><?php print ucwords($data[0][3])?></b><br><br>
<table width='100%' >
	<tr><td valign='top' >
	<font class=T1>
	<?php 
	$lvo=chercheLvo($eid);   // lv1,lv2,`option`
	?>
	Lv1/Spé : <a href="#" title="<?php print $lvo[0][0] ?>" ><?php print trunchaine($lvo[0][0],40); ?></a>
	<br>
	Lv2/Spé : <a href="#" title="<?php print $lvo[0][1] ?>" ><?php print trunchaine($lvo[0][1],40); ?></a>
	<br>
	Option : <a href="#" title="<?php print $lvo[0][2] ?>" ><?php print trunchaine($lvo[0][2],40); ?></a>
	</td>
	<td valign='top' >
	<?php
	$dataV=recupConfigVersement($idclasse); //id,idclasse,libellevers,montantvers,datevers
	if ($dataV == "") { $dataV=array(); }
	$dataVE=recupConfigVersementEleve($eid);
	if ($dataVE == "") { $dataVE=array(); }
	$dataV=array_merge($dataV,$dataVE);
	for($j=0;$j<count($dataV);$j++) { $id=$dataV[$j][0]; if(!verifcomptaExclu($id,$eid)) { $montantScol+=$dataV[$j][3]; } }
	$montantScol=affichageFormatMonnaie($montantScol);
	$unite=unitemonnaie(); 
	?>
	Droit de scolarité : <?php print $montantScol." ".$unite?>
	<br>
	Boursier : <?php print etatBoursier($ideleve) ?> (<?php print montantBourse($ideleve) ?>)
	<br>
	Indemnité de stage : <?php print montantIndemniteStage($ideleve) ?> </font>
	<br>
	<br>
	</td></tr></table> 
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
<br><br><font class=T2>&nbsp;&nbsp;&nbsp;</font><textarea name="objet" style="width:98%;font-size:16" onkeypress="compter(this,'2000', this.form.CharRestant)" cols='100' rows='20' ><?php print $commentaire ?></textarea>
<br><font class=T2>&nbsp;&nbsp;&nbsp;</font><input type='text' name='CharRestant' size='2' disabled='disabled' value="<?php print $len ?>" > (2000 caractères maximum)

<br><br><font class=T2>&nbsp;&nbsp;&nbsp;L'entretien s'est terminé à  : <input type="text" name="heurefin"  value="<?php print $heureFin ?>"  onclick="this.value=''" size=5   onKeyPress="onlyChar2(event)" > 


<?php
$listePedago=recupListNomPrenomPedago($identretien); //p.nom,p.prenom,p.civ
$listing="";
for($j=0;$j<count($listePedago);$j++) {
	$listing.=" - ".civ($listePedago[$j][2])." ".$listePedago[$j][0]." ".$listePedago[$j][1];
}
?>
<br><br><font class=T2>&nbsp;&nbsp;&nbsp;Equipe Pédagogue  : <?php print recherche_personne($_SESSION["id_pers"]) ?><br />
&nbsp;&nbsp;&nbsp;<font class='T1'><?php print $listing ?> <span id='listing'></span></font>
<br /><br />
&nbsp;&nbsp;&nbsp;<select id="pers" name="pers" >
<option value='' >Autres personnes</option> 

<?php 
print "<optgroup label='Direction' />";
select_personne_2("ADM",'25');
print "<optgroup label='Vie Scolaire' />";
select_personne_2("MVS",'25')
?>
</select>&nbsp;<input type='button' value="Ajouter" onclick="ajoutPers()" />


<input type="hidden" name="idpers" id='idpers' value="<?php print $_SESSION["id_pers"] ?>;"  /> 
<input type="hidden" name="ideleve"  value="<?php print $data[0][0] ?>"  /> 
<input type="hidden" name="identretien"  value="<?php print $_GET["modif"]  ?>"  /> 
<input type="hidden" name="nomclasse"  value="<?php print $data[0][3] ?>"  /> 
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
<?php 
if (($_SESSION["membre"] == "menuprof") && (ENTRETIENPROF == "oui") ) { 
	print "<script language=JavaScript>buttonMagicRetour('ficheeleve2.php?idclasse=$idclasse','".LANGRETOUR."');</script></td><td>"; 
}
?>
&nbsp;&nbsp;<input type="button" value="Imprimer les entretiens" class="bouton2" onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" />
	<input type="button" value="Prochain rendez-vous" class="bouton2" onclick="open('entretien3.php?eid=<?php print $eid ?>','','width=400,height=300');" />
</td></tr></table>

<br>
</td></tr></table>

<br><br>

<?php 
if (isset($_POST["create"])) {
	enrg_entretien($_POST["ideleve"],$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["preparation"],$_POST["idpers"]);
}

if (isset($_POST["modify"])) {
	modif_entretien($_POST["ideleve"],$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["identretien"],$_POST["preparation"]);
}

if (isset($_GET["supp"])) {
	suppEntretien($_GET["supp"]);
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
$pdf->SetTitle("Entretien Individuel - $nomEleve $prenomEleve");
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

$cumulHeure=cumulEntretien($data[0][0],$data[0][3]);

$nomprenom=ucwords($data[0][1]." ".$data[0][2]);
$pdf->SetFont('Arial','',12);
$pdf->SetXY(20,$ycoor0+=20);
$pdf->MultiCell(100,10,"Prénom / Nom de l'Etudiant : $nomprenom ",0,'L',0);
$pdf->SetXY(20,$ycoor0+=10);
$clas=$data[0][3];
$pdf->MultiCell(100,10,"Classe : $clas - Cumul des heures : $cumulHeure ",0,'L',0);
$pdf->SetXY($x,$ycoor0+=10);
$x=10;



?>
</form>

<script>
function ajoutPers() {
	var idqui=document.formulaire.pers.options[document.formulaire.pers.options.selectedIndex].value;
	var qui=document.formulaire.pers.options[document.formulaire.pers.options.selectedIndex].text;
	if ((idqui != '') && (idqui != '<?php print $_SESSION["id_pers"] ?>')) {
		document.getElementById('listing').innerHTML+=qui+" - ";
		document.formulaire.idpers.value+=idqui+";"
	}
	

}
</script>


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

$data=listeEntretien($eid); //ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation
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
	$classe=ucwords($data[$i][4]);
	$time=timeForm($data[$i][2])."-".timeForm($data[$i][3]);
	$duree=timeForm(calculduree($data[$i][2],$data[$i][3]));

	$listePedago=recupListNomPrenomPedago($data[$i][7]); //p.nom,p.prenom,p.civ
	$listing="";
	for($j=0;$j<count($listePedago);$j++) {
		$listing.=" - ".civ($listePedago[$j][2])." ".$listePedago[$j][0]." ".$listePedago[$j][1];
	}

	$pdf->MultiCell(190,5,"$preparation le $date  - ($time) durée  $duree - Reçu(e) par $nomprof en classe de $classe",1,'L',0);
	$pdf->SetXY($x,$ycoor0+5);
	$pdf->SetFont('Arial','B',9);
	$pdf->MultiCell(190,5,"Objet et contenu de l'entretien / Conclusion - Actions",0,'L',0);

	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($x,$ycoor0+10);
	$mess=preg_replace('/\n/'," ",$data[$i][5]);

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

	if ($listing == "") { $listing=$nomprof; }
?>
	<tr>
	<td id="bordure" valign="top"><u><?php print $preparation  ?></u> le  <?php print $date ?> (<?php print $time?>) durée <?php print $duree ?> reçu(e) par <?php print "$listing" ?> en classe de <?php print $classe ?>
	<br><br><?php print $data[$i][5]?><br><br>
	<div align=right>[ <a href="entretien2.php?modif=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>">Modifier</a> ] [ <a href="entretien2.php?supp=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>">Supprimer</a> ]</div>
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
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

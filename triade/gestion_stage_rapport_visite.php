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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();

$idstage=$_GET["idstage"];
$identreprise=$_GET["identreprise"];



$date=date("Y");
$date2=date("Y")-1;

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>

<?php
if (isset($_GET["eid"])) { $eid=$_GET["eid"]; }
if (isset($_POST["ideleve"])) {$eid=$_POST["ideleve"]; }
if($eid) {
	$sql="SELECT elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,lieu_naissance,nationalite,	passwd,	passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,email,email_eleve,class_ant,annee_ant,tel_eleve,email_resp_2,sexe,code_compta FROM ${prefixe}eleves, ${prefixe}classes c WHERE elev_id='$eid' AND c.code_class=classe";
	$res=execSql($sql);
	$data=chargeMat($res);
	$nomEleve=$data[0][1];
	$prenomEleve=$data[0][2];
}

if (isset($_GET["idsupp"])) {
	$dataContreRendu=InfoContreRenduStage($_GET["idmodif"]); // id,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,datesaisie,fichier_md5,fichier_name,id_prof_visite
	$fichiermd5=$dataContreRendu[0][8];
	@unlink("./data/pdf_stage/$fichiermd5");
	SuppFichierContreRenduStage($fichiermd5);
}

$date="";
$heure="";
if (isset($_GET["idmodif"])) {
	$idstage=$_GET["idstage"];
	$identreprise=$_GET["identreprise"];
	$dataContreRendu=InfoContreRenduStage($_GET["idmodif"]); // id,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,datesaisie,fichier_md5,fichier_name,id_prof_visite
	$date=dateForm($dataContreRendu[0][2]);
	$heure=timeForm($dataContreRendu[0][3]);
	$contenu=$dataContreRendu[0][5];
	$len=strlen($contenu);
	$idcontrerendu=$_GET["idmodif"];
	$fichiermd5=$dataContreRendu[0][8];
	$fichierName=$dataContreRendu[0][9];
	$idProfVisite=$dataContreRendu[0][10];
}


?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'>
		<?php print "Compte rendu de visite de stage" ?> </b>
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
<form method="post" name="formulaire" onsubmit="return validentretien()" action="gestion_stage_rapport_visite0.php" ENCTYPE="multipart/form-data">
<table border=0 width="100%" >
<tr><td>
<font class=T2>&nbsp;&nbsp;&nbsp;</font><img src="image_trombi.php?idE=<?php print $eid ?>" border=0 >
</td><td>
<font class=T2>
Nom prénom : <b><?php $nomprenomeleve=ucwords($data[0][1]." ".$data[0][2]); print $nomprenomeleve ?></b>
<br>
Classe : <?php print $data[0][3] ?>
<br>
<?php 
$datastage=recherchedatestage($idstage); 
//idclasse,datedebut,datefin,numstage,id,nom_stage  
?> 
Stage : <?php print $datastage[0][5] ?>
<br>
<?php
$ent=recupInfoEntreprise($identreprise); 
// id_serial,nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent
?>
Société : <?php print $ent[0][1] ?>
<br>
</font>
<br>Visite le : </font> 
<input type="text" name="datevisite"  onKeyPress="onlyChar(event)"  size=10 value="<?php print $date ?>" > 
<?php include_once("librairie_php/calendar.php"); 
calendarDim('id2','document.formulaire.datevisite',$_SESSION["langue"],"1","0");
?> à <input type="text" name="heurevisite"  value="<?php print $heure ?>"  onclick="this.value=''" size=5   onKeyPress="onlyChar2(event)" > 
</td></tr></table>

<br>
<?php
// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');



$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Compte Rendu de Stage - $nomEleve $prenomEleve");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Compte Rendu de Stage"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$xcoor0=0;
$ycoor0=0;


$pdf->SetFont('Arial','B',14);
$pdf->SetXY(0,$ycoor0+=10);
$pdf->MultiCell(210,10,"Année $date2 - $date",0,'C',0);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(0,$ycoor0+=10);
$pdf->MultiCell(210,10,"COMPTE RENDU DE STAGE",0,'C',0);



$nomprenom=ucwords($data[0][1]." ".$data[0][2]);
$pdf->SetFont('Arial','',12);
$pdf->SetXY(20,$ycoor0+=20);
$pdf->MultiCell(100,10,"Prénom / Nom de l'Etudiant : $nomprenom ",0,'L',0);
$pdf->SetXY(20,$ycoor0+=10);
$clas=$data[0][3];
$pdf->MultiCell(100,10,"Classe : $clas ",0,'L',0);
$pdf->SetXY($x,$ycoor0+=10);
$x=10;

if (!is_dir("./data/pdf_bull/contrerendustage")) { mkdir("./data/pdf_bull/contrerendustage"); }
$nomEleve1=TextNoAccent($nomEleve);
$prenomEleve1=TextNoCarac($prenomEleve);
$fichier="./data/pdf_bull/contrerendustage/".$nomEleve1."_".$prenomEleve1.".pdf";

?>


<font class=T2>&nbsp;&nbsp;&nbsp;Compte  rendu : </font>

<span style="position:relative;left:100px"><input type="button" value="retour"  class="button" onclick="open('gestion_stage_rapport_visite0.php?id=<?php print $eid ?>&idclasse=12&nc','_parent','')" /></span>

<br><br><font class=T2>&nbsp;&nbsp;&nbsp;</font><textarea name="contrerendu" style="width:95%;font-size:16" onkeypress="compter(this,'2000', this.form.CharRestant)" cols='100' rows='20' ><?php print $contenu ?></textarea>
<br><font class=T2>&nbsp;&nbsp;&nbsp;</font><input type='text' name='CharRestant' size='2' disabled='disabled' value="<?php print $len ?>" > (2000 caractères maximum)


<input type="hidden" name="ideleve"  		value="<?php print $eid ?>"  /> 
<input type="hidden" name="identreprise"  	value="<?php print $identreprise ?>"  /> 
<input type="hidden" name="idstage"  		value="<?php print $idstage ?>"  /> 
<input type="hidden" name="idcontrerendu"  	value="<?php print $idcontrerendu ?>"  /> 
<br><br>
<?php 
if (UPLOADIMG == "oui") {
	$taille="8Mo";
}else{
	$taille="2Mo";
}
$mess="Le document doit être au format : doc, pdf ou office. "." (Taille max : $taille) ";
$information="Attention";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$information="Agent Web ".AGENTWEBPRENOM;
	$vocal="";
	$mess="<iframe width=100 height=100 src=\'http://www.triade-educ.com/agentweb/agentmel.php?inc=5&mess=$vocal&m=M13\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe>".$mess ;
}
?>
<?php
if ( (file_exists("./data/pdf_stage/$fichiermd5")) && (trim($fichiermd5) != "")) {
	print "<font class='T2'>&nbsp;&nbsp;&nbsp;Document pièce jointe : ";
	print "<a href='telecharger.php?fichier=./data/pdf_stage/$fichiermd5&fichiername=$fichierName' target='_blank' title='Télécharger' ><img src='image/commun/download.png' border='0' /></a></font> [ <a href='gestion_stage_rapport_visite.php?idmodif=".$_GET["idmodif"]."&idstage=".$_GET["idstage"]."&identreprise=".$_GET["identreprise"]."&eid=".$_GET["eid"]."&idsupp=1'>supprimer</a> ]" ;

}else{
?>
<font class='T2'>&nbsp;&nbsp;&nbsp;Document annexe : <input type=file name='fichier' class="bouton2" /> <A href='#' onMouseOver="AffBulle3('<?php print $information ?>','./image/commun/info.jpg','<?php print $mess?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>
<?php } ?>
<br><br>

<font class='T2'>&nbsp;&nbsp;&nbsp;Enseignant visiteur : <select name="idprofvisiteur">
<?php
if ($idProfVisite != '0' ) {
	print "<option id='select0' value='0' >".recherche_personne($idProfVisite)."</option>";
}else{
	print "<option id='select1' value='$idProfVisite' >".LANGCHOIX."</option>";	
	print "<option id='select0' value='0' >aucun</option>";
}
select_personne('ENS'); // creation des options
?>
</select>
<br><br>

<?php 
if (isset($_GET["idmodif"])) {
?>
	<table border="0" align="center" ><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGPER30?>","<?php print "modifcontrerendu" ?>"); //text,nomInput</script>
<?php
}else{ ?>
	<table border="0" align="center" ><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","<?php print "createcontrerendu" ?>"); //text,nomInput</script>
<?php } ?>

<?php if (($_SESSION["membre"] == "menuadmin") ||  ($idProfVisite == $_SESSION["id_pers"] )  || ((verif_profp_eleve2($eid,$_SESSION["id_pers"])))) { ?>
<input type="button" value="Imprimer les comptes rendus" class="bouton2" onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" />
<?php } ?>



</td><td></table> 
</form>
</td></tr></table>

<br><br>




<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1'><?php print "Listes des comptes rendus " ?> </font></td></tr>
<tr  id='cadreCentral0' >
<td valign=top >
<table width=100% bgcolor="#FFFFFF">
<tr>
<td bgcolor='yellow' >&nbsp;Stage&nbsp;</td>
<td bgcolor='yellow' width='5%'>&nbsp;Saisie&nbsp;le&nbsp;</td>
<td bgcolor='yellow' width='5%'>&nbsp;Consulter&nbsp;</td>
<td bgcolor='yellow' width='5%'>&nbsp;Supprimer&nbsp;</td>
</tr>
<?php
if (isset($_GET["idsupp"])) {
	if (verif_profp_eleve2($eid,$_SESSION["id_pers"])) {
		$cr=suppContreRenduViaId($_GET["idsupp"]);
		if ($cr) history_cmd($_SESSION["nom"],"SUPPRIMER","Contre rendu $nomprenomeleve");
	}

}

$data=listingContreRenduStage($eid,$identreprise);
//id,idstage,dateVisite,heureVisite,identreprise,contrerendu,visiteur,saisiele
for($i=0;$i<count($data);$i++) {
	if ($data[0][1] != $idstage ) continue;
	$datastage=recherchedatestage($idstage);
	print "<tr  class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
	print "<td>&nbsp;".$datastage[0][5]."</td>";
	print "<td>".dateForm($data[$i][7])."</td>";
	print "<td><input type='button' class='button'  value='Consulter' onclick=\"open('gestion_stage_rapport_visite.php?idmodif=".$data[$i][0]."&idstage=$idstage&identreprise=$identreprise&eid=$eid','_parent','')\" /></td>";
	print "<td><input type='button' class='button'  value='Supprimer' onclick=\"open('gestion_stage_rapport_visite.php?idsupp=".$data[$i][0]."&idstage=$idstage&identreprise=$identreprise&eid=$eid','_parent','')\" /></td>";
	print "</tr>";

	$pdf->SetXY($x,$ycoor0+=10);
	$pdf->MultiCell(190,60,"",1,'L',0);

	$pdf->SetXY($x,$ycoor0);
	$pdf->SetFont('Arial','',9);
	$nomprof=$data[$i][6];
	$classe=ucwords($data[$i][4]);

	$pdf->MultiCell(190,5,"Viste le ".dateForm($data[$i][2])." à ".timeform($data[$i][3])." - Reçu(e) par $nomprof ",1,'L',0);
	$pdf->SetXY($x,$ycoor0+5);
	$pdf->SetFont('Arial','B',9);
	$pdf->MultiCell(190,5,"Compte rendu : ",0,'L',0);

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

}
?>
</table>

</td></tr>
<?php

}

?>


</table>

<?php
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);

?>


<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION[membre] == "menuadmin") :
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
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY>
</HTML>

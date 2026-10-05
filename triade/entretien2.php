<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.ent-student-card { display:flex !important; gap:16px !important; align-items:flex-start !important; }
.ent-photo { box-shadow:0 4px 12px rgba(0,0,0,.3) !important; border-radius:8px !important; flex-shrink:0 !important; }
.ent-info { flex:1 !important; }
.ent-name { font-size:15px !important; font-weight:700 !important; color:#080A66 !important; font-family:Electrolize,Trebuchet MS,Arial,sans-serif !important; margin-bottom:4px !important; }
.ent-class { font-size:13px !important; font-weight:700 !important; color:#333 !important; font-family:Electrolize,Trebuchet MS,Arial,sans-serif !important; margin-bottom:10px !important; }
.ent-details { display:flex !important; gap:20px !important; font-size:12px !important; color:#333 !important; font-family:Electrolize,Trebuchet MS,Arial,sans-serif !important; flex-wrap:wrap !important; line-height:1.8 !important; margin-top:4px !important; }
.ent-col { flex:1 !important; min-width:140px !important; }
.ent-col a { color:#1565c0 !important; }
.ent-list { margin:8px 0 !important; }
.ent-entry { background:#fff !important; border:1px solid #dde0f0 !important; border-radius:8px !important; padding:12px 14px !important; margin-bottom:10px !important; font-family:Electrolize,Trebuchet MS,Arial,sans-serif !important; }
.ent-entry-header { font-size:12px !important; font-weight:700 !important; color:#080A66 !important; margin-bottom:8px !important; }
.ent-entry-body { font-size:12px !important; color:#333 !important; line-height:1.6 !important; margin-bottom:8px !important; }
.ent-entry-actions { text-align:right !important; font-size:12px !important; }
.ent-entry-actions a { color:#1565c0 !important; text-decoration:none !important; font-weight:700 !important; margin-left:10px !important; }
.ent-entry-actions a:hover { text-decoration:underline !important; }
.ent-cumul { font-size:13px !important; font-weight:700 !important; color:#080A66 !important; font-family:Electrolize,Trebuchet MS,Arial,sans-serif !important; padding:4px 0 !important; }
</style>
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
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

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
	$sql="SELECT elev_id,nom,prenom,c.libelle,lv1,lv2,`option`,regime,date_naissance,lieu_naissance,nationalite,	passwd,	passwd_eleve,civ_1,nomtuteur,prenomtuteur,adr1,code_post_adr1,commune_adr1,tel_port_1,civ_2,nom_resp_2,prenom_resp_2,adr2,code_post_adr2,commune_adr2,tel_port_2,telephone,profession_pere,tel_prof_pere,profession_mere,tel_prof_mere,nom_etablissement,numero_etablissement,code_postal_etablissement,commune_etablissement,numero_eleve,email,email_eleve,class_ant,annee_ant,tel_eleve,email_resp_2,sexe,code_compta,code_class FROM {$prefixe}eleves, {$prefixe}classes c WHERE elev_id='$eid' AND c.code_class=classe";
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
	<?php print "Enregistrement d'entretien" ?></font></b></td>
</tr>
<?php
if( countTriade($data) <= 0 ) {
	print("<tr id='cadreCentral0' ><td align=center valign=center>".LANGRECH3."</td></tr>");
}else {
?>
<tr  id='cadreCentral0' >
<td>
<!-- // fin  -->
<form method="post" name="formulaire" onsubmit="return validentretien()" action="entretien2.php">

<div class="na-card ent-student-card">
  <img src="image_trombi.php?idE=<?php print $eid ?>" border='0' class="ent-photo">
  <div class="ent-info">
    <div class="ent-name"><?php print ucwords($data[0][1]." ".$data[0][2]) ?></div>
    <div class="ent-class">Classe : <?php print ucwords($data[0][3]) ?></div>
    <div class="ent-details">
      <div class="ent-col">
        <?php $lvo=chercheLvo($eid); ?>
        Lv1/Spé : <a href="#" title="<?php print $lvo[0][0] ?>"><?php print trunchaine($lvo[0][0],40) ?></a><br>
        Lv2/Spé : <a href="#" title="<?php print $lvo[0][1] ?>"><?php print trunchaine($lvo[0][1],40) ?></a><br>
        Option : <a href="#" title="<?php print $lvo[0][2] ?>"><?php print trunchaine($lvo[0][2],40) ?></a>
      </div>
      <div class="ent-col">
        <?php
        $dataV=recupConfigVersement($idclasse);
        if ($dataV == "") { $dataV=array(); }
        $dataVE=recupConfigVersementEleve($eid);
        if ($dataVE == "") { $dataVE=array(); }
        $dataV=array_merge($dataV,$dataVE);
        for($j=0;$j<countTriade($dataV);$j++) { $id=$dataV[$j][0]; if(!verifcomptaExclu($id,$eid)) { $montantScol+=$dataV[$j][3]; } }
        $montantScol=affichageFormatMonnaie($montantScol);
        $unite=unitemonnaie();
        ?>
        Droit de scolarité : <?php print $montantScol." ".$unite ?><br>
        Boursier : <?php print etatBoursier($ideleve) ?> (<?php print montantBourse($ideleve) ?>)<br>
        Indemnité de stage : <?php print montantIndemniteStage($ideleve) ?>
      </div>
    </div>
  </div>
</div>

<?php
$checked="";
if ($preparation == 1) { $checked="checked='checked'"; }
?>

<div class="na-card">
  <div class="na-row">
    <span class="na-lbl">Préparation :</span>
    <label class="cb-check"><input type="checkbox" name="preparation" value="1" <?php print $checked ?>> (oui)</label>
  </div>
  <div class="na-row">
    <span class="na-lbl">le :</span>
    <div style="display:inline-flex;align-items:center;gap:5px;flex-wrap:wrap;">
      <input type="text" name="saisiedate" onKeyPress="onlyChar(event)" size=10 value="<?php print $date ?>" class="bouton2">
      <?php include_once("librairie_php/calendar.php");
      calendarDim('id2','document.formulaire.saisiedate',$_SESSION["langue"],"1","0"); ?>
      <span style="font-size:12px;color:#333;">à</span>
      <input type="text" name="heuredepart" value="<?php print $heureDebut ?>" onclick="this.value=''" size=5 onKeyPress="onlyChar2(event)" class="bouton2">
    </div>
  </div>
  <div style="margin-bottom:9px;">
    <span class="na-textarea-lbl">Objet et contenu de l'entretien / Conclusion - Actions</span>
    <textarea name="objet" style="width:98%;font-size:16px;" onkeypress="compter(this,'2000',this.form.CharRestant)" cols='100' rows='20'><?php print stripslashes($commentaire) ?></textarea>
    <div style="font-size:11px;color:#555;margin-top:3px;"><input type='text' name='CharRestant' size='2' disabled='disabled' value="<?php print $len ?>"> (2000 caractères maximum)</div>
  </div>
  <div class="na-row">
    <span class="na-lbl">Fin entretien :</span>
    <input type="text" name="heurefin" value="<?php print $heureFin ?>" onclick="this.value=''" size=5 onKeyPress="onlyChar2(event)" class="bouton2">
  </div>
  <div style="margin-bottom:0;">
    <span class="na-textarea-lbl">Équipe Pédagogue :</span>
    <div style="font-size:12px;color:#333;line-height:1.8;margin-bottom:8px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">
      <?php print recherche_personne($_SESSION["id_pers"]) ?><br>
      <?php
      $listePedago=recupListNomPrenomPedago($identretien); //p.nom,p.prenom,p.civ
      $listing="";
      for($j=0;$j<countTriade($listePedago);$j++) {
          $listing.=" - ".civ($listePedago[$j][2])." ".$listePedago[$j][0]." ".$listePedago[$j][1];
      }
      ?>
      <span style="color:#666;"><?php print $listing ?></span> <span id='listing'></span>
    </div>
    <div class="na-row">
      <select id="pers" name="pers" class="cc-select">
        <option value=''>Autres personnes</option>
        <?php
        print "<optgroup label='Direction' />";
        select_personne_2("ADM",'25');
        print "<optgroup label='Vie Scolaire' />";
        select_personne_2("MVS",'25');
        ?>
      </select>
      <button type="button" onclick="ajoutPers()" class="btn-retour">Ajouter</button>
    </div>
  </div>
  <input type="hidden" name="idpers" id='idpers' value="<?php print $_SESSION["id_pers"] ?>;"/>
  <input type="hidden" name="ideleve" value="<?php print $data[0][0] ?>"/>
  <input type="hidden" name="identretien" value="<?php print $_GET["modif"] ?>"/>
  <input type="hidden" name="nomclasse" value="<?php print $data[0][3] ?>"/>
</div>

<?php
$act="create";
$textbutton=LANGENR;
if (isset($_GET["modif"])) {
	$act="modify";
	$textbutton=LANGPER30;
}
?>
<div class="na-foot">
  <button type="submit" name="<?php print $act ?>" value="1" class="btn-enr"><?php print $textbutton ?></button>
  <?php if (($_SESSION["membre"] == "menuprof") && (ENTRETIENPROF == "oui")): ?>
  <button type="button" onclick="open('ficheeleve2.php?idclasse=<?php print $idclasse ?>','_self','')" class="btn-retour"><?php print LANGRETOUR ?></button>
  <?php endif ?>
  <button type="button" onclick="open('visu_pdf_admin.php?id=<?php print $fichier ?>','_blank','');" class="btn-retour">Imprimer les entretiens</button>
  <button type="button" onclick="open('entretien3.php?eid=<?php print $eid ?>','','width=400,height=300');" class="btn-retour">Prochain rendez-vous</button>
</div>

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
<!-- // fin  -->
</td></tr></table>

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
<td valign=top>
<!-- // fin  -->
<div class="na-card">
  <div class="ent-cumul">Cumul des heures : <?php print $cumulHeure ?></div>
</div>
<div class="ent-list">
<?php
$data=listeEntretien($eid); //ideleve,date,heuredebut,heurefin,nomclasse,objet,recupar,id,preparation
$j=0;$A=0;
for($i=0;$i<countTriade($data);$i++) {
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
	for($j=0;$j<countTriade($listePedago);$j++) {
		$listing.=" - ".civ($listePedago[$j][2])." ".$listePedago[$j][0]." ".$listePedago[$j][1];
	}

	$pdf->MultiCell(190,5,"$preparation le $date  - ($time) durée  $duree - Reçu(e) par ".stripslashes($nomprof)." en classe de $classe",1,'L',0);
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
  <div class="ent-entry">
    <div class="ent-entry-header"><u><?php print $preparation ?></u> le <?php print $date ?> (<?php print $time ?>) durée <?php print $duree ?> reçu(e) par <?php print stripslashes($listing) ?> en classe de <?php print $classe ?></div>
    <div class="ent-entry-body"><?php print nl2br(stripslashes($data[$i][5])) ?></div>
    <div class="ent-entry-actions">[ <a href="entretien2.php?modif=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>">Modifier</a> ] [ <a href="entretien2.php?supp=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>">Supprimer</a> ]</div>
  </div>
<?php
}
?>
</div>

<?php
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
?>

<?php  }  ?>
<!-- // fin  -->
</td></tr>
</table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
print "</SCRIPT>";

top_d();

print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
print "</SCRIPT>";

endif ;

// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>

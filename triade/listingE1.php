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
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGLIST1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="padding:12px 8px">
<div class="card">
  <div class="card-body">
<?php
$idprof=$_POST["idprof"];
$anneeScolaire=$_POST["anneeScolaire"];

if ($idprof == "tous") {

	// creation PDF
	define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
	include_once('./librairie_pdf/fpdf/fpdf.php');
	include_once('./librairie_pdf/html2pdf.php');

	$pdf=new PDF();

	$data=listingPersonnel("ENS");
	/* pers_id, nom, prenom, prenom2, mdp, type_pers, civ, photo, email,
	   valid_forward_mail, adr, code_post, commune, tel, tel_port,
	   identifiant, lieudenseigement, offline, id_societe_tuteur,
	   pays, indice_salaire, qualite */

	unset($textp);

	for($i=0;$i<countTriade($data);$i++) {
		$idpers=$data[$i][0];
		$nom=$data[$i][1];
		$prenom=$data[$i][2];
		$civ=civ($data[$i][5]);

		$pdf->AddPage();

		$textp.="<b>$civ $nom $prenom</b> : <br><br>";

		$datapp=visu_affectation_detail_ens($idpers,$anneeScolaire);
		// ordre_affichage,code_matiere,code_prof,code_classe,coef,g.libelle,a.langue,a.avec_sous_matiere,a.visubull,a.nb_heure

		for($j=0;$j<countTriade($datapp);$j++) {
			$textp.="en classe ".chercheClasse_nom($datapp[$j][3])." (<i>".ucwords(chercheMatiereNom($datapp[$j][1]))."</i>) ";
			$textp.="<br>";
		}
		unset($datapp);

		// insertion de la date
		$date=dateDMY();
		$Pdate=LANGLIST4.": ".$date;
		$pdf->SetFont('Courier','',10);
		$pdf->SetXY(150,3);
		$pdf->WriteHTML($Pdate);

		// cadre principale
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY(15,35);
		$pdf->WriteHTML($textp);
		unset($textp);
	}
	unset($data);

	$fichierpdf="./data/pdf_certif/edition-classe.pdf";
	if (file_exists($fichierpdf)) { @unlink($fichierpdf); }
	$pdf->output('F',$fichierpdf);

?>
    <div class="alert alert-success"><?php print LANGLIST5 ?></div>
    <div style="margin-top:10px">
      <input type="button" class="btn btn-primary"
        onclick="open('visu_pdf_admin.php?id=<?php print $fichierpdf?>','_blank','');"
        value="<?php print LANGPER5?>">
    </div>
<?php

}else{

	$nomprof=recherche_personne($idprof);
	$data=visu_affectation_detail_ens($idprof,$anneeScolaire);
	// ordre_affichage,code_matiere,code_prof,code_classe,coef,g.libelle,a.langue,a.avec_sous_matiere,a.visubull,a.nb_heure

	print "<div style='font-size:13px;font-weight:700;color:#080A66;margin-bottom:12px;'>".$nomprof."</div>";
	print "<div style='border:1px solid #dde0f0;border-radius:6px;overflow:hidden;'>";
	for($i=0;$i<countTriade($data);$i++) {
		$bg = ($i % 2 == 0) ? "#fff" : "#f5f7ff";
		print "<div style='padding:7px 12px;background:".$bg.";font-size:12px;color:#333;border-bottom:1px solid #eef0f8;'>";
		print "en classe ".chercheClasse_nom($data[$i][3]);
		print " <span style='color:#666;font-style:italic;'>(".ucwords(chercheMatiereNom($data[$i][1])).")</span>";
		print "</div>";
	}
	print "</div>";

}

Pgclose();
?>
  </div>
</div>

<div style="margin-top:14px;padding-left:4px">
  <script language="JavaScript">buttonMagicRetour("listing.php","_parent")</script>
</div>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>

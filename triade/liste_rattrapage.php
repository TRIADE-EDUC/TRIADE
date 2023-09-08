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
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");

if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}

$cnx=cnx();

if (isset($_GET["dateDebut"])) {
	$dateDebut=dateForm($_GET["dateDebut"]);
	$dateFin=dateForm($_GET["dateFin"]);
	if ($dateFin == "//") { $dateFin=""; }
	if ($dateDebut == "//") { $dateDebut=""; }
}

if (isset($_POST["dateDebut"])) {
	$dateDebut=dateForm($_POST["dateDebut"]);
	$dateFin=dateForm($_POST["dateFin"]);
	if ($dateFin == "//") { $dateFin=""; }
	if ($dateDebut == "//") { $dateDebut=""; }
}

if (isset($_POST["createrattra"])) {
	for($i=0;$i<$_POST["nb"];$i++){
		$id=$_POST["id$i"];
		$valide=$_POST["valide$i"];
		if ($valide == 1) {
			valideRattrappage($id); 
		}	
	}
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Rattrapages non validés." ?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<form method='post' name="formulaire0" >
<br />
&nbsp;&nbsp;<font class=T2>Abs, Rtds du <input type=text name="dateDebut" value="<?php print $dateDebut?>"  onclick="this.value=''" size=10 class="bouton2" onKeyPress="onlyChar(event)">
<?php
include_once("librairie_php/calendar.php");
calendar("idZ1","document.formulaire0.dateDebut",$_SESSION["langue"],"0");
?>
 au <input type=text name="dateFin" value="<?php print $dateFin ?>"  onclick="this.value=''" size=10 class="bouton2" onKeyPress="onlyChar(event)">

<?php
calendar("idZ2","document.formulaire0.dateFin",$_SESSION["langue"],"0");
?>
<input type="submit" value="<?php print LANGBT28 ?>"  class="bouton2" >

<br><br>

<table border='1' width='100%' bordercolor='#000000' style="border-collapse: collapse;" >
<tr>
<td bgcolor='yellow' >Nom prénom</td>
<td bgcolor='yellow' >Date</td>
<td bgcolor='yellow' >Heure</td>
<td bgcolor='yellow' >Durée</td>
<td bgcolor='yellow' >Effectuer</td>
</tr>
<?php
$afficheNbPage=1;
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Edition des rattrapages");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Edition des rattrapages"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$X=5;
$Y=5;

$pdf->SetXY($X,$Y);
if ($dateDebut != "") {
	$pdf->MultiCell(120,5,"Rattrapage pour la période du $dateDebut au $dateFin",0,'L',0);
}else{
	$pdf->MultiCell(120,5,"Rattrapage Listing complet",0,'L',0);
}
$Y+=10;

if (isset($_GET["supp"])) {
	$idsupp=$_GET["supp"];
	suppressionRattrapage($idsupp,$_SESSION["nom"]);
}

$data=listingRattrapageNonValider($dateDebut,$dateFin); // date,heure_depart,duree,ref_id_absrtd,id,valider
for($i=0;$i<count($data);$i++) {
	list($nomeleve,$idEleve)=preg_split('/#/',rechercheEleveViaRef_id_absrtd($data[$i][3]));

	$date=dateForm($data[$i][0]);
	$heure_depart=timeForm($data[$i][1]);
	$duree=timeForm($data[$i][2]);

	if ($duree == "00:00") $duree="???"; 
	if ($heure_depart == "00:00") $heure_depart="???";
	if ($date == "00/00/0000") $date="???"; 


	print "<tr id='tr$i' class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
	print "<td>".infoBulleEleveSansLoupeAvecLien($idEleve,$nomeleve,"gestion_abs_retard_modif_donne.php?ideleve=$idEleve")."</td>";
	print "<td><span id='d$i'>$date</span></td>";
	print "<td><span id='h$i'>$heure_depart</span></td>";
	print "<td><span id='t$i'>$duree</span></td>";
	print "<td><input type='checkbox' name='valide$i' value='1' onClick=\"DisplayLigne('tr$i');\" title='Effectuer'  /><input type='hidden' name='id$i' value='".$data[$i][4]."' /> ";
	print "&nbsp;&nbsp;&nbsp;<a href='#' onclick=\"modifligne('$i','".dateForm($data[$i][0])."','".timeForm($data[$i][1])."','".timeForm($data[$i][2])."','".$data[$i][4]."'); return false;\"  ><img src='image/commun/editer.gif' border='0' align='center' title='Modifier'  /></a>";
	print "<a name='anc$i'></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='#anc$i' onclick=\"open('liste_rattrapage.php?supp=".$data[$i][4]."&dateDebut=$dateDebut&dateFin=$dateFin','_self','');\"  ><img src='image/commun/trash.png' border='0' align='center' title='Supprimer'  /></a> </td>";
	print "</tr>";

	$pdf->SetXY($X,$Y);
	
	$pdf->SetFillColor(255,203,145);
	$BG=($BG == 1) ? 0 : 1 ;
	$pdf->MultiCell(80,5,"$nomeleve",1,'L',$BG);
	$pdf->SetXY($X+=80,$Y);
	$pdf->MultiCell(40,5,"le $date à $heure_depart",1,'L',$BG);
	$pdf->SetXY($X+=40,$Y);
	$pdf->MultiCell(30,5,"Durée de  $duree",1,'L',$BG);
	$pdf->SetXY($X+=30,$Y);
	$pdf->MultiCell(45,5,"Effectué : (oui) / (non)",1,'L',$BG);
	$Y+=5;
	$X=5;
	if ($Y >= 270) { 
		$pdf->addPage();
		$X=5;$Y=5;
	}
	
}
print "<input type='hidden' name='nb' value='".count($data)."' />";

$fichier="./data/pdf_bull/rattrapage_".$_SESSION["id_pers"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);

?>

</table>
<br><br>
<table align='center'><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","createrattra"); //text,nomInput</script></td><td><script language=JavaScript>buttonMagic("<?php print LANGaffec_cre41?> (PDF)","visu_pdf_scolaire.php?id=<?php print $fichier?>",'_blank','',''); //text,nomInput</script></td><td><script language=JavaScript>buttonMagicRetour("gestion_abs_retard.php","_parent")</script></td></tr></table>

</form>

<script language='JavaScript' >
function ajaxEnr(element,id,retourAffiche,value) {
         var divid=retourAffiche;
         var myAjax = new Ajax.Request(
                "ajaxModifRattrapage.php",
                {       method: "post",
                        asynchronous: true,
                        parameters: "id="+id+"&element="+element+"&value="+value,
                        timeout: 5000,
                        onComplete: function (request) {
                                    document.getElementById(divid).innerHTML="<b>"+request.responseText+"</b>";
                        }
                }
        );
}

function modifligne(i,d,h,t,id) {
	document.getElementById('d'+i).innerHTML="<input type=text value='"+d+"' id='dd"+i+"' size=10 onblur=\"ajaxEnr('d','"+id+"','dd"+i+"',this.value)\"  />";
	document.getElementById('h'+i).innerHTML="<input type=text value='"+h+"' id='hh"+i+"' size=4  onblur=\"ajaxEnr('h','"+id+"','hh"+i+"',this.value)\"  />";
	document.getElementById('t'+i).innerHTML="<input type=text value='"+t+"' id='tt"+i+"' size=4  onblur=\"ajaxEnr('t','"+id+"','tt"+i+"',this.value)\"  />";
}

</script>

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
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY></HTML>

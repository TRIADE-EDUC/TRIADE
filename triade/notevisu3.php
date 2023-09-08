<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
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
include_once("./librairie_php/lib_error.php");
include("common/config.inc.php");
include("librairie_php/db_triade.php");
$cnx=cnx();
//error($cnx);
$libel=urldecode($_GET["libel"]);
$data=$_GET["args"];
$data=urldecode($data);
$data=explode(";",$data);
array_shift($data);
$i=1;
$l=count($data);
while($i<$l){
	unset($data[$i]);
	$i=$i+2;
}
foreach($data as $tmp){
	$inter=explode("\"",trim($tmp));
    if (get_magic_quotes_gpc()) {
	 $dataTmp[]=substr($inter[1],0,-1);
    }else{
	$dataTmp[]=$inter[1];
    }
}
$data=$dataTmp;
unset($dataTmp);
if ($_GET["sujet"] != "") {
	$sujet=$_GET["sujet"];
}else{
	$sujet=$data[0];
}
$date=change_date(trim($data[1]));
$coef=$data[2];
$elev_id=$data[3];
$code_mat=$data[4];
$prof_id=$data[5];
unset($data);

if (trim($elev_id) != "") {


$sql="
SELECT
	note_id,
";
if(DBTYPE=='pgsql')
{
	$sql .= " upper(trim(e.nom))||' '||initcap(trim(e.prenom)), ";
}
elseif(DBTYPE=='mysql')
{
	$sql .= " CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) , ";
}
$sql .= "
	round(note,2),
	n.elev_id,
	n.typenote,
	n.notationsur
FROM
	${prefixe}notes n, ${prefixe}eleves e
WHERE
	sujet = '$sujet'
AND date  = '$date'
AND coef  = '$coef'
AND n.elev_id IN ($elev_id)
AND code_mat = '$code_mat'
AND prof_id = '$prof_id'
AND n.elev_id = e.elev_id
ORDER BY e.nom,e.prenom
";

$sujet2=$sujet;
$notationsur=$mat[0][5];
if (trim($mat[0][4]) == "en") { $note_usa=1;$typenote="en";$titre="Note en mode USA";}
if ((trim($mat[0][4]) == "fr") || (trim($mat[0][4]) == "")) { $note_usa=0;$typenote="fr";$titre="Note de 0 a $notationsur";}

$curs=execSql($sql);
$mat=chargeMat($curs);
for($i=0;$i<count($mat);$i++){
	for($j=0;$j<count($mat[$i]);$j++){
		if($mat[$i][$j] == -1){
			$mat[$i][$j] = 'abs';
		} elseif ($mat[$i][$j] == -2) {
			$mat[$i][$j] = 'disp';
		} elseif ($mat[$i][$j] == -3) {
			$mat[$i][$j] = 'néant';
		} elseif ($mat[$i][$j] == -4) {
			$mat[$i][$j] = 'DNN';
		} elseif ($mat[$i][$j] == -5) {
			$mat[$i][$j] = 'DNR';
		} elseif ($mat[$i][$j] == -6) {
			$mat[$i][$j] = 'VAL';
		} elseif ($mat[$i][$j] == -7) {
			$mat[$i][$j] = 'NVAL';
		} else {
			continue;
		}
	}
}

}

$mySession[Sn]=$_SESSION["nom"];
$mySession[Sp]=$_SESSION["prenom"];
$note_en=0;
?>
<html>
<head>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD>
<td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF22 ?> </b><font id="color2"><?php print urldecode($_GET["libel"])?></font> </font></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<br />
<!-- // fin  -->
<table border='0' align='center' style="border-collapse: collapse;"  width='500' >
<tr><td>
<table border="1" width='300' >
        <tr>
            	<td bgcolor="#FFFFFF" align='right' >&nbsp;<?php print LANGPROF6 ?> : &nbsp;</td>
            	<td bgcolor="#FFFFFF">&nbsp;<?php print stripslashes($sujet2)?>&nbsp;</td>
            </tr>
            <tr>
            	<td bgcolor="#FFFFFF" align='right' >&nbsp;<?php print LANGTE7 ?> : &nbsp;</td>
            	<td bgcolor="#FFFFFF">&nbsp;<?php print dateForm($date)?>&nbsp;</td>
            </tr>
            <tr>
            	<td bgcolor="#FFFFFF" align='right' >&nbsp;<?php print LANGPER19 ?> : &nbsp;</td>
            	<td bgcolor="#FFFFFF">&nbsp;<?php print $coef?>&nbsp;</td>
            </tr>
            <tr>
            	<td bgcolor="#FFFFFF" align='right' >&nbsp;<?php print "Notation sur" ?> : &nbsp;</td>
            	<td bgcolor="#FFFFFF">&nbsp;<?php print "$notationsur" ?>&nbsp;</td>
            </tr>

            </table>
</td><td align="center" width="80%">
<table width='100%'>
<?php
$fichier="./data/pdf_bull/edition_".$_SESSION["id_pers"].".pdf";
?>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="open('visu_pdf_prof.php?id=<?php print $fichier?>','_blank','');"><img src="image/commun/print.gif" border=0 ></a></td><td>
<?php 
if ($_SESSION["membre"] == "menuprof") { ?>
	<form method='post' action="devoirvisu2.php" >
	<input type='hidden' name="sClasseGrp" value="<?php print $_SESSION["sClasseGrp"] ?>" />
	<input type='hidden' name="sMat" value="<?php print $_SESSION["sMat"] ?>" />
	<input type='hidden' name="anneeScolaire" value="<?php print $anneeScolaire ?>" />
	<script>buttonMagicSubmit("Retour","create",'')</script>
	</form>
<?php } ?>
</td></tr></table>
</td></tr></table>
<br /><br />
<ul>
<?php
// creation PDF
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();
$pdf->AddPage();
$xcoor=20;
$ycoor=20;
$texte="Note du devoir du ".dateform($date);
$texte2="Sujet : <b>".stripslashes($sujet)."</b> / Coef : <b>$coef</b> ";
$texte3="Classe de  <b>".urldecode($_GET["libel"])."</b>";
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor,$ycoor);
$pdf->WriteHTML($texte);
$ycoor+=10;
$pdf->SetXY($xcoor,$ycoor);
$pdf->WriteHTML($texte2);
$ycoor+=10;
$pdf->SetXY($xcoor,$ycoor);
$pdf->WriteHTML($texte3);
$ycoor+=20;
?>
            <table border="1" style="border-collapse: collapse;" >
            <?php
	    $nb=0;
	    $notetotal=0;
	    $ii=0;
	    $note_en=0;
            for($ai=0;$ai<count($mat);$ai++){
            	print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">\n";
	    		$note=$mat[$ai][2];
	    		if ($note == "néant") {$note="";}
	       	 	$noteaff=$note;
	       	 	if (trim($mat[$ai][4]) == "en") {
	       	 			$note_en=1;
			           	$noteaff=recherche_note_en($note);
            		}

	       	 	print "<td>&nbsp;".trunchaine($mat[$ai][1],30)."&nbsp;</td>\n";
        		print "<td>&nbsp;".$noteaff."&nbsp;</td>\n";
           		print "</tr>\n";

			if (($note >= 0) && (is_numeric($note))) {
				$notetotal+=$note;
				$nb++;
			}

			if (($note >= 0) && (is_numeric($note))) {
				if ($note_en) {
					if (trim($mat[$ai][4]) == "fr") {
						$note = $note * 5 ;
					}
				}
        	            	$notEntier=intval($note);
                	    	$noteTab1[$notEntier]++;
                	}

		$xlogo=90;
		$ylogo=60;

		if ($ii == 35) {
	                $pdf->AddPage();
			$ii=0;
			$xcoor=20;
			$ycoor=20;
		}
		$ii++;

		$pdf->SetFont('Arial','',11);
		$pdf->SetFillColor(220);
		$pdf->SetXY($xcoor,$ycoor); // placement du cadre du nom de l eleve
		$nomprenom=trunchaine(ucwords(strtolower($mat[$ai][1])),20);
		$pdf->MultiCell(50,5,"$nomprenom",1,'',1);
		$xcoor+=50;
		$pdf->SetXY($xcoor,$ycoor);
		// if ($noteaff < 10) { $noteaff="0".$noteaff; }
		$pdf->MultiCell(15,5,"$noteaff",1,'',0);
		$ycoor+=5;
		$xcoor-=50;
            }

		@ksort($noteTab1);
        	foreach ($noteTab1 as $cle => $value) {
                	$noteTab2[]=$cle;
                	$Nbnote2[]=$value;
        	}
	        $noteTab=@implode(",",$noteTab2);
		$Nbnote=@implode(",",$Nbnote2);



		$moy=$notetotal / $nb;
		$moy=number_format($moy,2,'.','');
		$moyenneleveaff=$moy;
		if ($note_en) {
			$moyenneleveaff=recherche_note_en($moy);
		}
		print "<tr><td align=right><b>Moyenne du devoir :&nbsp;</b></td><td align=center><b>$moyenneleveaff</b></td></tr>";
		$pdf->SetXY($xcoor,$ycoor);
		$pdf->MultiCell(50,10,"Moyenne du devoir : ",1,'R',0);
		$xcoor+=50;
                $pdf->SetXY($xcoor,$ycoor);
                $pdf->MultiCell(15,10,"$moyenneleveaff",1,'',0);




//------------------------------------------------------------------------//
//-----------------------------------------------------------------------//
$idNote=$noteTab;
$idNombre=$Nbnote;
$nomdudevoir=stripslashes($sujet);

include_once("jpgraph/src/jpgraph.php");
include_once("jpgraph/src/jpgraph_line.php");
include_once("jpgraph/src/jpgraph_bar.php");

DEFINE ("DEFAULT_GFORMAT" ,"auto");

$idNbEleveTab=explode(",",$idNombre);
$idNoteTab=explode(",",$idNote);

$l2datay=$idNbEleveTab; // note
$datax=$idNoteTab;
$l1datay = array(0);

// Create the graph.
$graph = new Graph(330,300,"auto");
$graph->img->SetMargin(40,20,20,40);
$graph->SetScale("textlin");
$graph->SetShadow();

$graph ->img->SetImgFormat("jpeg");


// Create the linear error plot
$l1plot=new LinePlot($l1datay);
$l1plot->SetColor("red");
$l1plot->SetWeight(2);
$l1plot->SetLegend(""); // legende de la ligne rouge

// Create the bar plot
$l2plot = new BarPlot($l2datay);
$l2plot->SetFillColor("orange");
$l2plot->SetLegend("Répartition des notes");

// Add the plots to the graph
$graph->Add($l2plot);

$graph->title->Set("$nomdudevoir"); // legende en haut
$graph->xaxis->title->Set("$titre");
$graph->yaxis->title->Set("Nombre Eleve");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->SetTickLabels($datax);

// Display the graph
$image="./data/pdf_bull/graph_".$_SESSION["id_pers"].".jpg";
@unlink($image);
$graph->Stroke("$image");
$pdf->Image($image,$xlogo,$ylogo);
?>

            </table>
</ul>
<br><br>
<?php
if (file_exists($fichier)) unlink($fichier);
$pdf->output('F',$fichier);
?>
     <!-- // fin  -->
     </td>
	 </tr>
	 </table>
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
</body>
</html>
<?php Pgclose() ?>

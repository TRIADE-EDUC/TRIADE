<?php
session_start();
error_reporting(0);
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
if (isset($_GET["order"])) {
	$triEleve=$_GET["order"];
}else{
	if ($_COOKIE["tri_eleve"] == "classe") {
		$triEleve="classe";
	}elseif ($_COOKIE["tri_eleve"] == "nomeleve") {
		$triEleve="nomeleve";
	}else{
		$triEleve="nomeleve";
	}
}
setcookie("tri_eleve",$triEleve,time()+36000*24*30);

$anneeScolaire=$_COOKIE["anneeScolaire"];

include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuprof");
$cnx=cnx();


if(isset($_POST["create"])) {
	$cgrp1=$_POST["sClasseGrp"];
	$cgrp=explode(":",$cgrp1);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_POST["sMat"];
	$choix_tri=$_POST["choix_trimestre"];
}else {
	$cgrp1=$_GET["sClasseGrp"];
	$cgrp=explode(":",$cgrp1);
	$cid=$cgrp[0];
	$gid=$cgrp[1];
	$mid=$_GET["sMat"];
	if (VISUTRIAUTO == "oui")  {
		$choix_tri=recherche_trimestre_en_cours_via_classe($cid,$anneeScolaire);
        }else{
                $choix_tri="trimestre1";
        }
}

if ($choix_tri == '') $choix_tri="trimestre1";

$nomClasse=chercheClasse($cid);
$nomClasse=$nomClasse[0][1];
$nomMat=chercheMatiereNom($mid);
$nomGrp=chercheGroupeNom($gid);
$libel=$nomClasse." ".$nomGrp." ".$nomMat;


// recherche de l'intervalle de date
// creation de la requete

	$data=recherche_intervalle_trimestre_via_classe($choix_tri,$cid,$anneeScolaire);
	for($i=0;$i<count($data);$i++){
		$date_debut=$data[$i][0];
		$date_fin=$data[$i][1];
		$sql2="date >= '$date_debut' AND date <= '$date_fin' ";
	}	

	// fin de la creation
	$listTmp=explode(":",$cgrp);
	unset($HPV[cgrp]);
	$HPV[cid]=$cid;
	$HPV[gid]=$gid;
	unset($listTmp);
	//print_r($HPV);
	if($HPV[gid]):
	        $who="<font color=\"#FFFFFF\">- ".LANGPROF4." : </font> ".chercheGroupeNom($HPV[gid]);
	else:
	        $cl=chercheClasse($HPV[cid]);
	        $who="<font color=\"#FFFFFF\">- ".strtolower(LANGELE4)." : </font>".$cl[0][1];
	        unset($cl);
	endif;

?>
<HTML>
<HEAD>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<br>

<table border=0>
<tr><td>
<form method=POST>
&nbsp;&nbsp;<font class="T2"><?php print LANGPROF5 ?> :</font>
<?php
$choix_tri_text=$choix_tri;
if ($choix_tri_text == "trimestre1") {
	$choix_tri_text=LANGPROJ3;
}
if ($choix_tri_text == "trimestre2") {
        $choix_tri_text=LANGPROJ4;
}
if ($choix_tri_text == "trimestre3") {
        $choix_tri_text=LANGPROJ5;
}
?>
<select name="choix_trimestre">
<option value='<?php print $choix_tri?>' STYLE="color:#000066;background-color:#FCE4BA"><?php print ucfirst($choix_tri_text)?></option>
<option value='trimestre1' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
<option value='trimestre2' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
<option value='trimestre3' STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGPROJ5?></option>
</select>
<input type=hidden name="sMat" value='<?php print $_GET["sMat"];?>' />
<input type=hidden name="sClasseGrp" value='<?php print $_GET["sClasseGrp"];?>' />
<input type=hidden name="anneeScolaire" value='<?php print $anneeScolaire ?>' />
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGOK ?>","create"); //text,nomInput</script>
<?php
$fichier="./data/pdf_bull/edition_".$_SESSION["id_pers"].".pdf";
// creation excel
require_once "./librairie_php/class.writeexcel_workbook.inc.php";
require_once "./librairie_php/class.writeexcel_worksheet.inc.php";
$fichierxls="./data/fichier_ASCII/exportnote_".$_SESSION["id_pers"].".xls";
@unlink($fichierxls);
$fichiername="Rapport-${nomClasse}_$choix_tri.xls";
?>
&nbsp;&nbsp;&nbsp;
<a href="visu_pdf_prof.php?id=<?php print $fichier?>" target="_blank"><img src="image/commun/print.gif" border=0 align=center></a>
<a href="telecharger.php?fichier=<?php print $fichierxls?>&fichiername=<?php print $fichiername ?>" target="_blank"><img src="image/commun/Logo-Excel.gif" border=0 align=center></a>
</td></tr></table>
</form>
</ul>
<div id="MoyenClasse"></div>
<br>
<br>

<table  border="0" bordercolor="#000000">

<?php 
if ($HPV[gid]) {

if ($triEleve == "nomeleve") { ?>
	<tr><td align="center">[ <a href="visunoteprof.php?sMat=<?php print $mid ?>&sClasseGrp=<?php print $cgrp1 ?>&order=classe "><?php print LANGNNOTE2 ?></a> ]</td></tr>
<?php } ?>

<?php if ($triEleve == "classe") { ?>
<tr><td align="center">[ <a href="visunoteprof.php?sMat=<?php print $mid ?>&sClasseGrp=<?php print $cgrp1 ?>&order=nomeleve "><?php print LANGNNOTE3 ?></a> ]</td></tr>
<?php } 
}


if ($triEleve == "classe") {
	$order="ORDER BY 3,2";
}elseif($triEleve == "nomEleve") {
	$order="ORDER BY 2";
}else{
	$order="ORDER BY 2";
}

if($HPV[gid]){
        $gid=$HPV[gid];
        $sqlIn=<<<SQL
        SELECT
        	liste_elev
        FROM
        	${prefixe}groupes
        WHERE
		group_id='$gid'
SQL;
        $curs=execSql($sqlIn);
        $in=chargeMat($curs);
        freeResult($curs);
        $in=$in[0][0];
	$in=substr($in,1);
	$in=substr($in,0,-1);
	if (trim($in) != "") {
		
	$sql="
        SELECT
        	elev_id,
	";
        if(DBTYPE=='pgsql')
	{
		$sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
	}
	elseif(DBTYPE=='mysql')
	{
		$sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	}
	$sql .= ",classe
        FROM
        	${prefixe}eleves
        WHERE
        	elev_id IN ($in)
        $order
	";
		unset($in);
	}

} else {
        $cid=$HPV[cid];
/*	$sql="
        SELECT
        	elev_id,
	";
	if(DBTYPE=='pgsql')
	{
        	$sql .= " upper(trim(nom))||' '||initcap(trim(prenom)) ";
        }
	elseif(DBTYPE=='mysql')
	{
        	$sql .= " CONCAT( UPPER(TRIM(nom)) , ' ' , TRIM(prenom) ) ";
	}
	$sql .= ",classe
	FROM
        	${prefixe}eleves
        WHERE
        	classe='$cid'
	ORDER BY 2

	"; */

//	$sql="(SELECT e.elev_id, CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) ,e.classe  FROM ${prefixe}eleves e WHERE e.classe='$cid' AND e.compte_inactif != 1 $order  ) UNION ( SELECT e.elev_id,  CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ) ,e.classe  FROM ${prefixe}eleves e , ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.compte_inactif != 1 AND e.elev_id=h.ideleve  ) $order";

	$sql=" SELECT s.* FROM ( SELECT elev_id,CONCAT(upper(trim(nom)),' ',trim(prenom)),classe FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$cid' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT e.elev_id,CONCAT( upper(trim(e.nom)),' ',trim(e.prenom) ),e.classe FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$cid' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 2";

}
	if ($sql != "") {
	        $curs=execSql($sql);
       	 	unset($sql);
        	$mat=chargeMat($curs);
        	freeResult($curs);
        	unset($curs);
	}


//

$workbook = new writeexcel_workbook($fichierxls);
$worksheet1 = $workbook->addworksheet("$nomClasse");

$header = $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('orange');

$center = $workbook->addformat();
$center->set_align('left');

$moyennne = $workbook->addformat();
$moyennne->set_color('white');
$moyennne->set_align('center');
//$moyennne->set_align('vcenter');
//$moyennne->set_pattern();
$moyennne->set_fg_color('blue');


//$worksheet1->set_selection('A0');

// creation PDF
$nomclasse=chercheClasse_nom($cid);
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/lib.php');
$pdf=new RPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetTitle("Releve - $nomclasse");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Releve - $nomclasse"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
$xcoor=5;
$ycoor=5;

$texte=LANGMESS87." : $libel ";
$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor,$ycoor);
$pdf->Write(5,$texte);
$ycoor+=30;
$sujetaff=0;
$okmoy=0;
$noteeng=0;
$nbeleve=0;
$moyenmax=0;
$moyenmin=100;
$idClasseA="";
$y=0;$yxls=0;
$x=2;
	for($i=0;$i<count($mat);$i++){


		if ($ii == 28) {
	                $pdf->AddPage();
			$ii=0;
			$xcoor=5;
			$ycoor=5;
		}
		$ii++;  

		$pdf->SetFont('Arial','',11);
		$h=0;
		if ($triEleve == "classe") {
			$idClasseA=chercheIdClasseDunEleve($mat[$i][0]);
			if ($idClasseA != $afficheClasse) {
				$h=5;
				$afficheClasse=$idClasseA;
				$affi=chercheClasse_nom($afficheClasse);
				$affi=preg_replace('/ /','&nbsp;',$affi);
				print "<tr><td colspan=2 ><font class='T2' color='blue' >&nbsp;".LANGELE4."&nbsp;:&nbsp;<b>".$affi."</b></font></td></tr>";
				$pdf->SetXY($xcoor,$ycoor);
				$pdf->SetTextColor(0,0,255);
				$nomclasse="Classe : ".chercheClasse_nom($afficheClasse);
				$pdf->MultiCell(70,$h,"$nomclasse",0,'',0);
				$pdf->SetTextColor(0,0,0);
			}
				
		}

		
		$pdf->SetFillColor(220);
		$pdf->SetXY($xcoor,$ycoor+=$h); // placement du cadre du nom de l eleve
		$nomprenom=trunchaine($mat[$i][1],13);
		$pdf->MultiCell(40,5,"$nomprenom",1,'',0);

		$worksheet1->write($x+=1, $y, $mat[$i][1] , $center); // colonne des noms et prenoms
	
	
	
	

		$xcoor+=40;
		$xsujet=$xcoor + 2;
		$ysujet=$ycoor - 4;

		$photoeleve="image_trombi.php?idE=".$mat[$i][0];

		print "<tr>\n";
		print "<td><input type=text width='5' readonly size=20 value=\"".$mat[$i][1]."\" title=\"".$mat[$i][1]."\" ></td>\n";
		$data_note=recherche_note_pour_prof($mat[$i][0],$sql2,$mid,$_SESSION["id_pers"]);


		$noteelevetotal=0;
		$nbnoteleve=0;
		$moyenneeleve=0;
	

		$unenote=0;
print "<td><table><tr>";
		for($t=0;$t<count($data_note);$t++){
			//note_id,elev_id,prof_id,code_mat,coef,date,sujet,$f_trunc(note,2),typenote,noteexam,notationsur
			$noteinfo="";
			$note=$data_note[$t][7];
			$sujet=$data_note[$t][6];
			$notationSur=$data_note[$t][10];
			$sujetpdf=$sujet;
			$sujet=preg_replace('/"/','&rdquo;',$sujet);
		    	$sujet=preg_replace('/\'/','\\\'',$sujet);
			$coef=$data_note[$t][4];
			$coefPDF=$data_note[$t][4];
			$date=dateForm($data_note[$t][5]);
			$noteaff=$note;
         		if ($note == -1) {$noteaff="abs";}
	            	if ($note == -2) {$noteaff="disp";}
            		if ($note == -3) {$noteaff=" ";}
			if ($note == -4) {$noteaff="DNN";}
			if ($note == -5) {$noteaff="DNR";}
			if ($note == -6) {$noteaff="VAL";}
			if ($note == -7) {$noteaff="NVAL";}

			$bgexam="bgcolor=\"#FFFFFF\"";
			if ($data_note[$t][9] != "") {
				$bgexam="bgcolor=\"yellow\"";
			}


            		$bold="";
            		$boldf="";
            		$rgb1=0;
            		$rgb2=0;
			$rgb3=0;

			$minNote=$notationSur/2;

            		if (($note < $minNote) &&  (trim($data_note[$t][8]) != "en") && ($note >= 0) ){
            			$bold="<B><font color='red'>";
            			$boldf="</font></B>";
            			$rgb1=255;
				$rgb2=0;
				$rgb3=0;
            		}

			$maxNote=$notationSur/2;
			$maxNote+=$maxNote/2;

		        if (($note >= $maxNote) &&  (trim($data_note[$t][8]) != "en") ){
            			$bold="<B><FONT color='green'>";
           	 		$boldf="</FONT></B>";
            			$rgb1=0;
				$rgb2=128;
				$rgb3=0;
            		}


           		if (trim($data_note[$t][8]) == "en") {
				$noteeng=1;
				$noteaff=recherche_note_en($note);
		   	}
	     
			if ((trim($data_note[$t][8]) == "fr") || (trim($data_note[$t][8]) == "")) {
            			$noteinfo="<i><font size=1>note sur $notationSur</font></i>";
           		}

			if (($data_note[$t][9] != "") && ($data_note[$t][9] != "aucun")) {
				$noteExam="<u>Examen</u> : ".$data_note[$t][9]."<br>";
			}else{
				$noteExam="";
			}

			if (($noteaff < 10) && (is_numeric($noteaff))) { $noteaff="0".$noteaff; }
        	?>
			<td width='10' <?php print $bgexam ?> ><a href="#" onMouseOver="AffBulle('<font face=Georgia, Times New Roman, Times, serif><u><?php print LANGPARENT12 ?></u> <?php print $date?> <br> <u><?php print LANGPROF6 ?></u> : <?php print $sujet?><br><?php print $noteExam ?><u><?php print LANGPER19 ?></u> : <?php print $coef?> <br> <?php print $noteinfo?></FONT>');"  onMouseOut="HideBulle()";><?php print $bold.$noteaff.$boldf ?></a></td>
			<?php
			// moyenne de l'élève
		   	if (($note >= 0) && (is_numeric($note))) {
				$unenote=1;
				if ($noteeng) {
					if (trim($data_note[$t][8]) == "fr") {
						$note = $note * 5 ;
					}
					$sur20="";
				}else{
					$sur20="sur 20";
				}
				if ($notationSur != 20){
                                        if ($notationSur == 40) { $note/=2; $coef*=2; }
                                        if ($notationSur == 30) { $note/=1.5; $coef*=1.5; }
					if ($notationSur == 15) { $note*=1.333333333333333; $coef /= 1.33333333333333; }									
					if ($notationSur == 10) { $note*=2; $coef/=2; }	
					if ($notationSur == 5) { $note*=4; $coef/=4;}	

					if ($notationSur == 6) { $sur20="sur 6"; }	
				}
            			$noteelevetotal+=$note*$coef;
            			$nbnoteleve+=$coef;
				$okmoy=1;
	        	}
	   		if ($nbnoteleve != 0) {
				$moyenneeleve=$noteelevetotal/$nbnoteleve;
				$moyenneeleve=number_format($moyenneeleve,2,'.','');
			}
		
			$pdf->SetXY($xcoor,$ycoor);
			if (($data_note[$t][9] != "") && ($data_note[$t][9] != "aucun")) { $pdf->SetFillColor(225,255,0); }
			$pdf->SetTextColor($rgb1,$rgb2,$rgb3);
			$pdf->MultiCell(12,5,"$noteaff",1,'',0);
			
			$worksheet1->write($x, $y+=1, "$noteaff", $center);

			$pdf->SetFillColor(255,255,255);		
			$pdf->SetTextColor(0,0,0);
			if ($sujetaff == 0) {
					$pdf->SetFont('Arial','',9);
					$pdf->TextWithRotation($xsujet,$ysujet,trunchaine($sujetpdf,13),45,-45);
					$xsujet1=$xsujet+4;
					$pdf->TextWithRotation($xsujet1,$ysujet,"$date",45,-45);
					$xsujet2=$xsujet1+4;
					$pdf->TextWithRotation($xsujet2,$ysujet,"coef : $coefPDF",45,-45);
					$xsujet+=15;
					
					$worksheet1->write(0, $yxls+=1 ,"$sujetpdf", $center);
					$worksheet1->write(1, $yxls ,"$date ", $center);
					$worksheet1->write(2, $yxls ,"coef : $coefPDF", $center);
				}
				$pdf->SetFont('Arial','',11);
				$xcoor+=15;
			}
			$rgb1=0;
			$rgb2=0;
			$rgb3=0;
			if ($notationSur == 6) { 
				if ($moyenneeleve < 3) {
					$fontf="</font>";
					$font="<font color=red>";
					$rgb1=255;
					$rgb2=0;
					$rgb3=0;
				}else{
					$fontf="";
					$font="";
				}

			}else{
				if ($moyenneeleve < 10) {
					$fontf="</font>";
					$font="<font color=red>";
					$rgb1=255;
					$rgb2=0;
					$rgb3=0;
				}else{
					$fontf="";
					$font="";
				}
			}
			if ($okmoy == 1) {
				print "</tr></table>";
				$moyenneleveaff=$moyenneeleve;
				if ($noteeng == 1) {
					$moyenneleveaff=$moyenneeleve."% - ".recherche_note_en($moyenneeleve);
				}
				if (($moyenneleveaff < 10) && (is_numeric($moyenneleveaff))) { $moyenneleveaff="0".$moyenneleveaff; }
				/*-------------*/
				if ($moyenneeleve > $moyenmax) {$moyenmax=$moyenneeleve; }
				if ($moyenmin > $moyenneeleve) {$moyenmin=$moyenneeleve; }

				/*-------------*/
				$moyenClasse+=$moyenneeleve;
				/*-------------*/
				if ($unenote == 1) {
					$nbeleve++;;
					print "<td bgcolor='#FFCC99'><b><a href='#' title=\"".LANGMESS82." ".INTITULEELEVE." $sur20 \" >$font $moyenneleveaff $fontf</a></b></td>";
				}else{
					$moyenneleveaff="";
					print "<td bgcolor='#FFCC99'>&nbsp;</td>";
				}
				if (($sujetaff == 0) && ($moyenneeleve > 0)) {
					$pdf->SetFont('Arial','',9);
					$xsujet3=$xsujet2+10;
					$pdf->TextWithRotation($xsujet3,$ysujet,LANGMESS82,45,-45);
					$xsujet4=$xsujet3+4;
					$pdf->TextWithRotation($xsujet4,$ysujet,INTITULEELEVE,45,-45);
					
					$worksheet1->write(2, $yxls+=1 ,LANGMESS82, $center);
				}
				$pdf->SetFont('Arial','',11);
				$pdf->SetFillColor(220);
	            		$pdf->SetXY($xsujet3,$ycoor);
			        $pdf->SetTextColor($rgb1,$rgb2,$rgb3);
				$pdf->MultiCell(12,5,$moyenneleveaff,1,'',1);
			
				$worksheet1->write($x, $yxls, "$moyenneleveaff", $moyenne);
			
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(255);

			}
$y=0;
$sujetaff=1;
$ycoor+=5;
$xcoor=5;
	}
print "</tr>\n";

if ($nbeleve > 0) { $moyenClasse=$moyenClasse/$nbeleve; }

$pdf->SetFont('Arial','',10);
$pdf->SetXY(5,$ycoor+5);
$moyenneclasse=LANGMESS83." : ".number_format($moyenClasse,2,'.','')." (".LANGMESS84." : $moyenmax) (".LANGMESS85." : $moyenmin)";
$pdf->MultiCell(90,5,$moyenneclasse,0,'',0);


print "<script>document.getElementById('MoyenClasse').innerHTML='<font class=\'T2\'>&nbsp;&nbsp;".LANGMESS83." : <font color=\'blue\'>";
print number_format($moyenClasse,2,'.','')."</font>";
print " <font class=\'T1\'>&nbsp;(".LANGMESS84." : $moyenmax) (".LANGMESS85." : $moyenmin)</font>";
print "'</script>";

$workbook->close();
@unlink($fichier);
$pdf->output('F',$fichier);

?>
<!----------------------------------------------------->
</table>
<?php
Pgclose() ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#CCCCCC","red",1);</SCRIPT>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY>
</html>

<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/

$dstJ.="\" \"";
$dstT.="\" \"";
$date_deja="00/0000";
$txt_fini=0;
$passe=0;

$LLUNDI=LANGLETTRELUNDI;
$LMARDI=LANGLETTREMARDI;
$LMERCREDI=LANGLETTREMERCREDI;
$LJEUDI=LANGLETTREJEUDI;
$LVENDREDI=LANGLETTREVENDREDI;
$LSAMEDI=LANGLETTRESAMEDI;
$LDIMANCHE=LANGLETTREDIMANCHE;

if (isset($_POST["sClasseGrp"])) { $idclasse=$_POST["sClasseGrp"]; }
if ($idclasse > 0) {
	$data=affDst2($idclasse); // id_dst,date,matiere,code_classe,heure,duree,idsalle
}else{
	$data=affDst(); // id_dst,date,matiere,code_classe,heure,duree,idsalle
}

$tab_j=array();
// $data : tab bidim - soustab 3 champs
for($i=0;$i<count($data);$i++)
{
	$date_recup=dateMoisAnnee($data[$i][1]);
        $date_recup_jour_mois=dateJourMois($data[$i][1]);
        $date_recup_annee=dateAnnee($data[$i][1]);
	$annee=$saisie_annee_choix;
	$idsalle=$data[$i][6];
	$data2=rechercheInfoSalleEquipement($idsalle);
	// id,libelle,info,type FROM
	$nomsalle=$data2[0][1];
        if ($annee == $date_recup_annee) {
		$data[$i][2]=preg_replace('/"/',"&quot;",$data[$i][2]);
		$data[$i][2]=preg_replace('/\'/','\\\\\'',$data[$i][2]);
		$data[$i][3]=preg_replace('/"/',"&quot;",$data[$i][3]);
		$data[$i][3]=preg_replace('/\'/','\\\\\'',$data[$i][3]);
		$passe=1;
		$heure=$data[$i][5];
		$data[$i][5]=preg_replace('/\./',"h",$data[$i][5])."0 mn";
		$data[$i][5]="0".$data[$i][5];
		if (!preg_match('/h/',$data[$i][5])) { $data[$i][5]="0${heure}h00 mn"; }
 		if ($date_deja == $date_recup_jour_mois ) {
			if ($data[$i][5] == "0choix0 mn") $data[$i][5]="Non Indiqué";
                        $dstT.="<HR>";
                        $dstT.=LANGEL3.": ".trim($data[$i][3])."<BR>".LANGPER17.": ".trim($data[$i][2]). " à ".timeForm($data[$i][4])." (".$data[$i][5].")" ;
			$dstT.="<br>";
			$dstT.="En salle: $nomsalle";
                }else{
                        if ($txt_fini == 1) {
                                $dstT.="\"";
                        }
                        $date_deja=$date_recup_jour_mois;
			if ($data[$i][5] == "0choix0 mn") $data[$i][5]="Non Indiqué";
                        $dstJ.=",\"".$date_recup_jour_mois."\"";
                        $dstT.=",\" ".LANGEL3.": ".trim($data[$i][3])."<BR>".LANGPER17.": ".trim($data[$i][2]). " à ".timeForm($data[$i][4])." (".$data[$i][5].")" ;
			$dstT.="<br>En salle: $nomsalle";
                        $txt_fini=1;
                }
       }
}
if ($passe == 1 ) { $dstT.="\""; }

$FerieSamedi="";
$FerieMercredi="";
if ((CALMERCREDIAP == "oui") && (CALMERCREDIMATIN == "oui"))  { $FerieMercredi=" || (j==2)"; }
if ((CALMERCREDIAP == "oui") && (CALMERCREDIMATIN == "non"))  { $Ferieap=""; $FerieMatin=""; }
if ((CALMERCREDIAP == "non") && (CALMERCREDIMATIN == "oui"))  { $FerieMatin=""; $Ferieap=""; }

if ((CALSAMEDIAP == "oui") && (CALSAMEDIMATIN == "oui"))  { $FerieSamedi=" || (j==5)"; }
if ((CALSAMEDIAP == "oui") && (CALSAMEDIMATIN == "non"))  { $FerieSaap=""; $FerieSaMatin=""; }
if ((CALSAMEDIAP == "non") && (CALSAMEDIMATIN == "oui"))  { $FerieSaMatin=""; $FerieSaap=""; }


print "<script language=JavaScript>";
print "\nvar dstJ=new Array(".$dstJ.");";
print "\nvar dstT=new Array(".$dstT.");";

// Ici on déclare les dates des jours fériés fixes
print "var ferie=new Array(".FERIE.");";


print <<<EOF
var mois=new Array(langfuncmois1,langfuncmois2,langfuncmois3,langfuncmois4,langfuncmois5,langfuncmois6,langfuncmois7,langfuncmois8,langfuncmois9,langfuncmois10,langfuncmois11,langfuncmois12);

function disp(txt) { document.write(txt) }
function estFerie(j,m) {
        var nb=ferie.length;
        var test=false;
        for(var i=0;i<nb;i++) {
                if ((ferie[i].substring(0,2)==j)&&(ferie[i].substring(3,5)==m)) return true;
        }
        return false;
}

function estConseil(j,m) {
        var nb=dstJ.length;
        var test=false;
        for(var i=0;i<nb;i++) {
           if ((dstJ[i].substring(0,2)==j)&&(dstJ[i].substring(3,5)==m)) return true;
        }
        return false;
}

function showCalInfo(txt) {
    var el = document.getElementById('cal-info');
    if (!el) return;
    el.innerHTML = txt;
    el.style.display = 'block';
}
function hideCalInfo() {
    var el = document.getElementById('cal-info');
    if (el) el.style.display = 'none';
}

var ii=1;


function calendar(m, a) {
EOF;
	$newDATE=datecalendrier();
	print "var d_jour=$newDATE";
print <<<EOF
	var d_jour=new Date();
	var d=new Date(a,m-1,1);
	var dfin=new Date(a,m-1,1);
	var nb_jour=31;
	var aff_j="";
	for(var k=32;k>27;k--) {
		dfin.setMonth(m-1);
		dfin.setDate(k);
		if (dfin.getMonth()!=m-1) {nb_jour=k-1;}
	}

	var j1=d.getDay(); if (j1==0) j1=7;
	var jour=0;
	disp("<div class='cal-wrap'><div class='cal-title'>"+mois[d.getMonth()]+" "+a+"<\/div>");
	disp("<table class='cal-table'>");
	disp("<tr class='cal-head'><th>$LLUNDI<\/th><th>$LMARDI<\/th><th>$LMERCREDI<\/th><th>$LJEUDI<\/th><th>$LVENDREDI<\/th><th>$LSAMEDI<\/th><th>$LDIMANCHE<\/th><\/tr>");
	for(var i=0;i<6;i++) {
		disp("<tr>");
		for (j=0;j<7;j++) {
			jour=7*i+j-j1+2;
			aff_j=jour;
			if ((jour==d_jour.getDate())&&(m==d_jour.getMonth()+1)) {aff_j="<span class='cal-day-today'>"+jour+"<\/span>";}
			if ((7*i+j>=j1-1)&&(jour<=nb_jour)) {
				if ((j==6)||(estFerie(jour,m)) || (estConseil(jour,m)) $FerieSamedi $FerieMercredi ) {
					if (estConseil(jour,m)) {
						disp("<td class='cal-day cal-day-dst'><a href='#' onmouseover=\"showCalInfo('"+dstT[ii]+"')\" onmouseout='hideCalInfo()' onclick=\"PopupCentrer('calendrier_config_dst2.php?saisiejour="+jour+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','900','350','scrollbars=yes','calendrier'); return false;\">"+aff_j+"<\/a><\/td>");
						ii++;
					}else {
						disp("<td class='cal-day cal-day-ferie'><a href='#' onclick=\"PopupCentrer('calendrier_config_dst2.php?saisiejour="+jour+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','900','350','','calendrier'); return false;\">"+aff_j+"<\/a><\/td>");
					}
				} else {
					disp("<td class='cal-day'><a href='#' onclick=\"PopupCentrer('calendrier_config_dst2.php?saisiejour="+jour+"&saisiemois="+mois[d.getMonth()]+"&saisieannee="+a+"','900','350','','calendrier'); return false;\">"+aff_j+"<\/a><\/td>");
				}
			}
			else disp("<td class='cal-day'>&nbsp;<\/td>");
		}
		disp("<\/tr>");
	}
	disp("<\/table><\/div>");
}
function annee(an) {
	disp("<div class='cal-year-grid'>");
	for (var i=0;i<4;i++) {
		for (var j=0;j<3;j++) {
			disp("<div>");
			calendar(i*3+j+1,an);
			disp("<\/div>");
		}
	}
	disp("<\/div>");
}
</script>
EOF;

?>

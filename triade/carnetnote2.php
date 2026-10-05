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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("menuadmin");


$cnx=cnx();
// variables
$Snom=$_GET['nom'];
$Sprenom=$_GET['prenom'];
$Seid=$_GET['id_pers'];
$Scid=$_GET['idClasse'];

$visu="mois";

$anneeScolaire=anneeScolaireViaIdClasse($Scid);
if (isset($_GET["anneeScolaire"])) $anneeScolaire=$_GET["anneeScolaire"];

// la date
if(!$date=$_GET['m']){
	$date=dateM();
	$annee=dateY();
}

if (isset($_GET['annee'])) {
	$annee=$_GET['annee'];
}
$prevannee=$annee;
$nextannee=$annee;

if ($date == 1) $prevannee=$annee-1;
if ($date == 12) $nextannee=$annee+1;

// à verifier avec postgresql
if ($date < 10) {
	$date=preg_replace("/0/","",$date);
}

if($date==1):
	$prev=12;
else:
	$prev=$date-1;
endif;

if($date==12):
	$next=1;
else:
	$next=$date+1;
endif;

// les matières et leur ordre d'affectation
if (!isset($_GET["anneeScolaire"])) {
$sql=<<<SQL
SELECT
	a.code_matiere,
	case
		when sous_matiere = '0' then lower(trim(libelle))
		else lower(trim(CONCAT(libelle,' ',sous_matiere)))
	end,
	a.ordre_affichage,a.code_groupe
FROM
	{$prefixe}affectations a, {$prefixe}eleves e, {$prefixe}matieres m
WHERE
	e.elev_id = '$Seid'
	AND e.classe = a.code_classe
	AND classe = a.code_classe
	AND a.code_matiere = m.code_mat
	AND a.annee_scolaire = '$anneeScolaire'
	AND a.visubull='1'
ORDER BY
	a.ordre_affichage,a.code_groupe
SQL;


$curs=execSql($sql);
$ordre=chargeMat($curs);
}else{

	if ((defined("ISMAPP")) && (ISMAPP == 1)) {

$sql=<<<SQL
SELECT
        a.code_matiere,
        case
                when sous_matiere = '0' then lower(trim(libelle))
                else lower(trim(sous_matiere))
        end,
	a.ordre_affichage,a.code_groupe
FROM
        {$prefixe}affectations a, {$prefixe}matieres m
WHERE
        a.code_classe = '$Scid'
        AND a.code_matiere = m.code_mat
        AND a.annee_scolaire = '$anneeScolaire'
        AND a.visubull='1'
ORDER BY
        a.ordre_affichage
SQL;

	}else{

$sql=<<<SQL
SELECT
        a.code_matiere,
        case
                when sous_matiere = '0' then lower(trim(libelle))
                else lower(trim(CONCAT(libelle,' ',sous_matiere)))
        end,
        a.ordre_affichage,a.code_groupe
FROM
        {$prefixe}affectations a, {$prefixe}matieres m
WHERE
        a.code_classe = '$Scid'
        AND a.code_matiere = m.code_mat
        AND a.annee_scolaire = '$anneeScolaire'
        AND a.visubull='1'
ORDER BY
        a.ordre_affichage
SQL;

	}

$curs=execSql($sql);
$ordre=chargeMat($curs);

}



// les notes
if ($date < 10) {$date="0".$date;}

$sql=<<<SQL
SELECT
	m.code_mat,
	coef,
	sujet,
	DATE_FORMAT(date,'%d-%m-%Y'),
	TRUNCATE(note,2),
	n.typenote,
	n.notationsur,
	n.notevisiblele,
	n.noteexam
FROM
	{$prefixe}notes n, {$prefixe}matieres m
WHERE
	elev_id = '$Seid'
	AND DATE_FORMAT(date,'%m')='$date'
	AND DATE_FORMAT(date,'%Y')='$annee'
	AND m.code_mat = n.code_mat
SQL;

$curs=execSql($sql);
$mat=chargeMat($curs);
unset($curs);

class Note {

	var $coeff;
	var $sujet;
	var $date;
	var $valeur;
	var $typenote;

	function __construct($c,$s,$d,$v,$t,$u,$w,$e){
		$this->coeff = $c;
		$this->sujet = $s;
		$this->date = $d;
		$this->valeur = $v;
		$this->typenote= $t;
		$this->notationsur= $u;
		$this->notevisiblele=$w;
		$this->examen=$e;
	}

}

$mat2 = [];
for($i=0;$i<countTriade($mat);$i++){
	$cm=$mat[$i][0];
	$mat2[$cm][]= new Note($mat[$i][1],$mat[$i][2],$mat[$i][3],$mat[$i][4],$mat[$i][5],$mat[$i][6],$mat[$i][7],$mat[$i][8]);
}
$cles=array_keys($mat2);
$matFinal = [];
for($i=0;$i<countTriade($ordre);$i++){
	$cle1=$ordre[$i][0];
	$cle2=$ordre[$i][1];
	$j=$ordre[$i][2];
	$groupe=$ordre[$i][3];
//	print $cle1." ".$cle2."<br>";
	$cle2=chercheMatiereNom2($cle1);
	if(@in_array($cle1,$cles)):
		$cle2.="|x|$i|x|$j|x|$groupe";
		$matFinal[$cle2]= $mat2[$cle1];
	else:
		$cle2.="|x|$i|x|$j|x|$groupe";
		$matFinal[$cle2]=array();
	endif;
}
?>
<HTML>
<HEAD>
<title>Triade - Carnet de notes - <?php print stripslashes(ucwords($Sprenom)." ".strtoupper($Snom)) ?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin1.js"></SCRIPT>
<?php
if ($date <= 9) { $date=preg_replace("/0/","",$date); }
$baseUrl="carnetnote2.php?nom=".stripslashes(strtolower($Snom))."&prenom=".stripslashes(strtolower($Sprenom))."&id_pers=".$Seid."&idClasse=".$Scid."&anneeScolaire=".$anneeScolaire."&visu=mois";
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">

<tr id='coulBar0'>
<td colspan="2">
<div class="ne-topbar">
  <a class="ne-nav-btn" href="<?php print $baseUrl."&m=".$prev."&annee=".$prevannee ?>">&larr; <?php print $MOIS[$prev] ?></a>
  <span class="ne-month"><?php print stripslashes(ucwords($Sprenom)." ".strtoupper($Snom)) ?> &mdash; <span style="color:#f9c600"><?php print $MOIS[$date] ?> <?php print $annee ?></span></span>
  <a class="ne-nav-btn" href="<?php print $baseUrl."&m=".$next."&annee=".$nextannee ?>"><?php print $MOIS[$next] ?> &rarr;</a>
</div>
<form name="formnote">
<input type="text" name="note" class="ne-info-input" size="85" readonly>
</td>
</tr>

<tr id='cadreCentral0'>
<td colspan="2">
<table class="ne-table">
<?php
foreach($matFinal as $key => $value){
	list($key,$pos,$pos2,$idgroupe)=preg_split("/\|x\|/",$key);
	$idMatiere=chercheIdMatiere(stripslashes($key));
	$verifGroupe=verifMatiereAvecGroupeCarnetDeNote2($idMatiere,$Seid,$Scid,$pos2);
	if ($verifGroupe) { continue; }
	$idprof=profAff($idMatiere,$Scid,$pos2);
	$prof=recherche_personne2($idprof);
	$titresansaccent=$keysansaccent=recuplibellesansaccent($idMatiere);
	$titresansaccent=preg_replace('/"/',"'",$keysansaccent);
	$keysansaccent=stripslashes($keysansaccent);

	echo "<tr class=\"ne-tr\">";
	echo "<td class=\"ne-td-subject\">";
	echo "<a href=\"javascript:void\" style=\"color:inherit;text-decoration:none\""
	    ." onmouseover=\"document.formnote.note.value='Enseigné par ".$prof."'; return true;\""
	    ." onmouseout=\"document.formnote.note.value='';\" title=\"".$titresansaccent."\">"
	    .$keysansaccent."</a>";
	echo "</td><td class=\"ne-td-notes\">";

	$j=0;
	for($i=0;$i<countTriade($value);$i++){
		$coeff      =$value[$i]->coeff;
		$date       =$value[$i]->date;
		$sujet      =$value[$i]->sujet;
		$note       =$value[$i]->valeur;
		$typenote   =$value[$i]->typenote;
		$notationsur=$value[$i]->notationsur;
		$examen     =$value[$i]->examen;

		$moyenne   =moyenneDevoir($idMatiere,$date,$idprof,$sujet,$coeff,$examen,$idgroupe,$Scid);
		$moyendevoir=$moyenne['moy'];
		$mindevoir  =$moyenne['min'];
		$maxdevoir  =$moyenne['max'];
		$j++;
		if ($j==12) { print "<br>"; $j=0; }

		$sujet  =preg_replace('/"/','&rdquo;',$sujet);
		$sujetJs=str_replace("'","&#39;",$sujet);
		$noteaff=$note;
		$text2  ="";

		if      ($note == -1) { $noteaff="abs";  }
		elseif  ($note == -2) { $noteaff="disp"; }
		elseif  ($note == -3) { $noteaff="";     }
		elseif  ($note == -4) { $noteaff="DNN";  }
		elseif  ($note == -5) { $noteaff="DNR";  }
		elseif  ($note == -6) { $noteaff="VAL";  }
		elseif  ($note == -7) { $noteaff="NVAL"; }
		else {
			if ($note == "-3") { $noteaff=""; }
			elseif (trim($typenote) == "en") { $noteaff=recherche_note_en($note); }
		}

		if (trim($typenote) != "en") { $noteaff=preg_replace('/0$/','',$noteaff); }

		if ($note != -3 && $note != "-3") {
			$text2="$date  –  $sujetJs  –  coeff: $coeff  /  $notationsur";
		}

		$nc="ne-note";
		if     ($note == -1) $nc.=" ne-note-abs";
		elseif (in_array($note,[-2,-4,-5,-6,-7])) $nc.=" ne-note-special";
		else   $nc.=" ne-note-normal";
		if ($examen != "") $nc.=" ne-note-exam";

		if ($note != -3 && $note != "-3") {
			$nc.=" ne-popup-trigger";
			$popupAttrs=" data-date=\"".  htmlspecialchars($date,        ENT_QUOTES)."\""
			           ." data-sujet=\"". htmlspecialchars($sujet,       ENT_QUOTES)."\""
			           ." data-coeff=\"". htmlspecialchars($coeff,       ENT_QUOTES)."\""
			           ." data-sur=\"".   htmlspecialchars($notationsur, ENT_QUOTES)."\""
			           ." data-moy=\"".   htmlspecialchars($moyendevoir, ENT_QUOTES)."\""
			           ." data-min=\"".   htmlspecialchars($mindevoir,   ENT_QUOTES)."\""
			           ." data-max=\"".   htmlspecialchars($maxdevoir,   ENT_QUOTES)."\""
			           ." data-examen=\"".htmlspecialchars($examen,      ENT_QUOTES)."\"";
		} else {
			$popupAttrs="";
		}

		echo "<a class=\"$nc\" href=\"javascript:void\""
		    ." onmouseover=\"document.formnote.note.value='".addslashes($text2)."'; return true;\""
		    ." onmouseout=\"document.formnote.note.value='';\""
		    .$popupAttrs.">"
		    .$noteaff."</a>";
	}
	echo "</td></tr>";
}
?>
</table>
</td></tr>
</table>
</form>

<?php
if ($_SESSION['membre'] == "menuadmin") {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>

<div id="ne-popup-overlay">
  <div id="ne-popup-card">
    <button id="ne-popup-close" onclick="nePopupHide()">&#x2715;</button>
    <div id="ne-popup-header">
      <span id="ne-popup-date"></span>
      <span id="ne-popup-coeff"></span>
    </div>
    <div id="ne-popup-sujet"></div>
    <div id="ne-popup-stats">
      <div><div class="ne-popup-stat-lbl">Moy.</div><div class="ne-popup-moy-val"   id="ne-popup-moy"></div></div>
      <div><div class="ne-popup-stat-lbl">Min</div> <div class="ne-popup-other-val" id="ne-popup-min"></div></div>
      <div><div class="ne-popup-stat-lbl">Max</div> <div class="ne-popup-other-val" id="ne-popup-max"></div></div>
    </div>
    <div id="ne-popup-examen"></div>
  </div>
</div>
<script>
(function(){
  function nePopupShow(el){
    document.getElementById('ne-popup-date').textContent  = el.dataset.date  || '';
    document.getElementById('ne-popup-coeff').textContent = 'coeff ' + (el.dataset.coeff||'') + ' / ' + (el.dataset.sur||'');
    document.getElementById('ne-popup-sujet').textContent = el.dataset.sujet || '';
    document.getElementById('ne-popup-moy').textContent   = el.dataset.moy   || '';
    document.getElementById('ne-popup-min').textContent   = el.dataset.min   || '';
    document.getElementById('ne-popup-max').textContent   = el.dataset.max   || '';
    var ex = document.getElementById('ne-popup-examen');
    if(el.dataset.examen){ ex.textContent='Examen : '+el.dataset.examen; ex.style.display='block'; }
    else { ex.style.display='none'; }
    document.getElementById('ne-popup-overlay').classList.add('ne-popup-open');
  }
  window.nePopupHide = function(){
    document.getElementById('ne-popup-overlay').classList.remove('ne-popup-open');
  };
  document.addEventListener('click',function(e){
    var trigger = e.target.closest && e.target.closest('.ne-popup-trigger');
    if(trigger){ e.preventDefault(); nePopupShow(trigger); return; }
    if(!e.target.closest || !e.target.closest('#ne-popup-card')){ nePopupHide(); }
  });
})();
</script>
</BODY>
</HTML>
<?php @Pgclose() ?>

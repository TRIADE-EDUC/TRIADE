<?php
session_start();
error_reporting(0);
include_once('common/config.inc.php');
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
$cnx = cnx();

$ideleve      = $_GET["saisie_eleve"];
$idclasse     = $_GET["saisie_classe"];
$vvs          = $_SESSION["validenoteviescolaire"] ?? "";
$ordre        = ordre_matiere_visubull($idclasse);
$eleveT       = recupEleve($idclasse);

function vp0_dates($tri, $idc) {
    $dr = recupDateTrimByIdclasse($tri, $idc);
    $d = $f = "";
    for ($j = 0; $j < countTriade($dr); $j++) { $d = $dr[$j][0]; $f = $dr[$j][1]; }
    return [dateForm($d), dateForm($f)];
}
list($d1,$f1) = vp0_dates("trimestre1", $idclasse);
list($d2,$f2) = vp0_dates("trimestre2", $idclasse);
list($d3,$f3) = vp0_dates("trimestre3", $idclasse);

function vp0_moyClasse($idc, $eleveT, $d, $f, $ordre) {
    $ordreM = ordre_matiere($idc);
    $tabmatiere = [];
    for ($i = 0; $i < count($ordreM); $i++) {
        $tabmatiere[$ordreM[$i][2]] = $ordreM[$i][2]."##".$ordreM[$i][0];
    }
    $moyenClasseGen = 0.0; $nbeleve2 = 0;
    for ($j = 0; $j < count($eleveT); $j++) {
        $idEleve = $eleveT[$j][4];
        $noteMoyEleG = 0.0; $coefEleG = 0.0;
        foreach ($tabmatiere as $value) {
            list($num_ordre, $idMatiere) = preg_split("/##/", $value);
            if (verifMatiereAvecGroupe($idMatiere, $idEleve, $idc, $num_ordre)) continue;
            $idprof  = recherche_prof($idMatiere, $idc, $num_ordre);
            $noteaff = moyenneEleveMatiere($idEleve, $idMatiere, $d, $f, $idprof);
            $coef    = (float)recupCoeff($idMatiere, $idc, $num_ordre);
            if (trim($noteaff) !== "") { $noteMoyEleG += (float)$noteaff * $coef; $coefEleG += $coef; }
        }
        if ($coefEleG > 0) { $moyenClasseGen += $noteMoyEleG / $coefEleG; $nbeleve2++; }
    }
    if ($nbeleve2 == 0) return null;
    $m = $moyenClasseGen / $nbeleve2;
    return ($m < 0) ? null : round($m, 2);
}
$mc1 = vp0_moyClasse($idclasse,$eleveT,$d1,$f1,$ordre);
$mc2 = vp0_moyClasse($idclasse,$eleveT,$d2,$f2,$ordre);
$mc3 = vp0_moyClasse($idclasse,$eleveT,$d3,$f3,$ordre);

function vp0_moyEleve($eleveT, $idc, $d, $f, $ordre, $eid, $tri, $vvs) {
    $moyG = 0; $coefG = 0;
    foreach ($eleveT as $elv) {
        if ($elv[4] != $eid) continue;
        $idEleve = $elv[4];
        for ($i = 0; $i < countTriade($ordre); $i++) {
            $idM = $ordre[$i][0];
            if (verifMatiereAvecGroupe($idM, $idEleve, $idc, $ordre[$i][2])) continue;
            $coef   = recupCoeff($idM, $idc, $ordre[$i][2]);
            $idprof = recherche_prof($idM, $idc, $ordre[$i][2]);
            $idg    = verifMatierAvecGroupeRecupId($idM, $idEleve, $idc, $ordre[$i][2]);
            $note   = ($idg == "0")
                ? moyenneEleveMatiere($idEleve, $idM, $d, $f, $idprof)
                : moyenneEleveMatiereGroupe($idEleve, $idM, $d, $f, $idg, $idprof);
            if (trim($note) != "") { $moyG += $coef * $note; $coefG += $coef; }
        }
        if ($vvs == "oui") {
            $ri   = recupCaractVieScolaire($idc);
            $note = calculNoteVieScolaire($idEleve, $ri[0][2], $ri[0][3], $tri);
            if (trim($note) != "") { $moyG += $ri[0][1] * $note; $coefG += $ri[0][1]; }
        }
    }
    if ($coefG == 0) return null;
    $m = moyGenEleve($moyG, $coefG);
    $m = preg_replace('/,/', '.', $m);
    return ($m < 0) ? null : round((float)$m, 2);
}
$me1 = vp0_moyEleve($eleveT,$idclasse,$d1,$f1,$ordre,$ideleve,"trimestre1",$vvs);
$me2 = vp0_moyEleve($eleveT,$idclasse,$d2,$f2,$ordre,$ideleve,"trimestre2",$vvs);
$me3 = vp0_moyEleve($eleveT,$idclasse,$d3,$f3,$ordre,$ideleve,"trimestre3",$vvs);

$jE = json_encode([$me1,$me2,$me3]);
$jC = json_encode([$mc1,$mc2,$mc3]);
Pgclose();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Graphique Général</title>
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Electrolize, Arial, sans-serif; background: #f5f7ff; }
.g-header { background: linear-gradient(135deg,#080A66,#1a1c8a); color: #fff; padding: 8px 14px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 7px; }
.g-header i { color: #CACCEF; }
.g-wrap { position: relative; width: 100%; height: calc(100vh - 37px); padding: 10px; }
</style>
</head>
<body>
<div class="g-header"><i class="bi bi-bar-chart-line-fill"></i> Évolution des moyennes générales</div>
<div class="g-wrap">
  <canvas id="chart"></canvas>
</div>
<script src="librairie_js/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chart'), {
  type: 'line',
  data: {
    labels: ['Trimestre 1','Trimestre 2','Trimestre 3'],
    datasets: [
      { label:'Moyenne Élève', data:<?= $jE ?>, borderColor:'#080A66', backgroundColor:'rgba(8,10,102,0.1)', borderWidth:2.5, pointRadius:6, pointBackgroundColor:'#080A66', tension:0.3, spanGaps:true, fill:true },
      { label:'Moyenne Classe', data:<?= $jC ?>, borderColor:'#e65100', backgroundColor:'rgba(230,81,0,0.06)', borderWidth:2, borderDash:[6,3], pointRadius:4, pointBackgroundColor:'#e65100', tension:0.3, spanGaps:true }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    plugins: { legend:{ position:'bottom', labels:{ font:{size:11}, padding:12 } } },
    scales: {
      y: { min:0, max:20, grid:{color:'#eef0f8'}, ticks:{stepSize:5, callback:v=>v+'/20'} },
      x: { grid:{display:false} }
    }
  }
});
</script>
</body>
</html>

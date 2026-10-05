<?php
session_start();
error_reporting(0);
include_once('common/config.inc.php');
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
$cnx = cnx();

$ideleve  = $_GET["saisie_eleve"];
$idclasse = $_GET["saisie_classe"];
$ordre    = ordre_matiere_visubull($idclasse);
$eleveT   = recupEleve($idclasse);

function vp1_dates($tri, $idc) {
    $dr = recupDateTrimByIdclasse($tri, $idc);
    $d = $f = "";
    for ($j = 0; $j < countTriade($dr); $j++) { $d = $dr[$j][0]; $f = $dr[$j][1]; }
    return [dateForm($d), dateForm($f)];
}
list($d1,$f1) = vp1_dates("trimestre1",$idclasse);
list($d2,$f2) = vp1_dates("trimestre2",$idclasse);
list($d3,$f3) = vp1_dates("trimestre3",$idclasse);

function vp1_labels($eleveT, $ordre, $eid, $idc) {
    $tab = [];
    foreach ($eleveT as $elv) {
        if ($elv[4] != $eid) continue;
        $idEleve = $elv[4];
        for ($i = 0; $i < countTriade($ordre); $i++) {
            $idM = $ordre[$i][0];
            if (verifMatiereAvecGroupe($idM,$idEleve,$idc,$ordre[$i][2])) continue;
            $mat  = chercheMatiereNom($idM);
            $code = chercheCodeMatiere($idM);
            if ($code != "") $mat = $code;
            $tab[] = ucwords(strtolower(substr($mat, 0, 7)));
        }
        break;
    }
    return $tab;
}

function vp1_notes($eleveT, $ordre, $eid, $idc, $d, $f) {
    $tab = [];
    foreach ($eleveT as $elv) {
        if ($elv[4] != $eid) continue;
        $idEleve = $elv[4];
        for ($i = 0; $i < countTriade($ordre); $i++) {
            $idM = $ordre[$i][0];
            if (verifMatiereAvecGroupe($idM,$idEleve,$idc,$ordre[$i][2])) continue;
            $idprof = recherche_prof($idM,$idc,$ordre[$i][2]);
            $note   = moyenneEleveMatiere($idEleve,$idM,$d,$f,$idprof);
            $tab[]  = ($note === "" || $note === null) ? null : round((float)preg_replace('/,/','.', $note), 2);
        }
        break;
    }
    return $tab;
}

$labels = vp1_labels($eleveT,$ordre,$ideleve,$idclasse);
$t1     = vp1_notes($eleveT,$ordre,$ideleve,$idclasse,$d1,$f1);
$t2     = vp1_notes($eleveT,$ordre,$ideleve,$idclasse,$d2,$f2);
$t3     = vp1_notes($eleveT,$ordre,$ideleve,$idclasse,$d3,$f3);

$jL = json_encode($labels);
$j1 = json_encode($t1);
$j2 = json_encode($t2);
$j3 = json_encode($t3);
Pgclose();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Graphique Évolution</title>
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
<div class="g-header"><i class="bi bi-graph-up-arrow"></i> Notes par matière — T1 / T2 / T3</div>
<div class="g-wrap">
  <canvas id="chart"></canvas>
</div>
<script src="librairie_js/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chart'), {
  type: 'bar',
  data: {
    labels: <?= $jL ?>,
    datasets: [
      { label:'Trimestre 1', data:<?= $j1 ?>, backgroundColor:'rgba(8,10,102,0.8)', borderRadius:3 },
      { label:'Trimestre 2', data:<?= $j2 ?>, backgroundColor:'rgba(21,101,192,0.8)', borderRadius:3 },
      { label:'Trimestre 3', data:<?= $j3 ?>, backgroundColor:'rgba(2,136,209,0.8)', borderRadius:3 }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    plugins: { legend:{ position:'bottom', labels:{font:{size:10}, padding:10} } },
    scales: {
      y: { min:0, max:20, grid:{color:'#eef0f8'}, ticks:{stepSize:5, callback:v=>v+'/20'} },
      x: { grid:{display:false}, ticks:{font:{size:10}, maxRotation:35} }
    }
  }
});
</script>
</body>
</html>

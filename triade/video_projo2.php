<?php
session_start();
error_reporting(0);
include_once('common/config.inc.php');
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
$cnx = cnx();

$ideleve       = $_GET["saisie_eleve"];
$idclasse      = $_GET["saisie_classe"];
$trim_en_cours = $_GET["trimestre"];
$ordre         = ordre_matiere_visubull($idclasse);
$eleveT        = recupEleve($idclasse);

$dr = recupDateTrimByIdclasse($trim_en_cours, $idclasse);
$dateDebut = $dateFin = "";
for ($j = 0; $j < countTriade($dr); $j++) { $dateDebut = $dr[$j][0]; $dateFin = $dr[$j][1]; }
$dateDebut = dateForm($dateDebut);
$dateFin   = dateForm($dateFin);

function vp2_labels($eleveT, $ordre, $eid, $idc) {
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
            $tab[] = ucwords(strtolower(substr($mat, 0, 8)));
        }
        break;
    }
    return $tab;
}

function vp2_notes_eleve($eleveT, $ordre, $eid, $idc, $d, $f) {
    $tab = [];
    foreach ($eleveT as $elv) {
        if ($elv[4] != $eid) continue;
        $idEleve = $elv[4];
        for ($i = 0; $i < countTriade($ordre); $i++) {
            $idM = $ordre[$i][0];
            if (verifMatiereAvecGroupe($idM,$idEleve,$idc,$ordre[$i][2])) continue;
            $idprof = recherche_prof($idM,$idc,$ordre[$i][2]);
            $note   = moyenneEleveMatiere($idEleve,$idM,$d,$f,$idprof);
            $tab[]  = ($note === "" || $note === null) ? 0 : round((float)preg_replace('/,/','.', $note), 2);
        }
        break;
    }
    return $tab;
}

function vp2_notes_classe($eleveT, $ordre, $eid, $idc, $d, $f) {
    $tab = [];
    foreach ($eleveT as $elv) {
        if ($elv[4] != $eid) continue;
        $idEleve = $elv[4];
        for ($i = 0; $i < countTriade($ordre); $i++) {
            $idM = $ordre[$i][0];
            if (verifMatiereAvecGroupe($idM,$idEleve,$idc,$ordre[$i][2])) continue;
            $idprof = recherche_prof($idM,$idc,$ordre[$i][2]);
            $note   = moyeMatGen($idM,$d,$f,$idc,$idprof);
            $tab[]  = ($note === "" || $note === null) ? 0 : round((float)preg_replace('/,/','.', $note), 2);
        }
        break;
    }
    return $tab;
}

$labels = vp2_labels($eleveT,$ordre,$ideleve,$idclasse);
$eleve  = vp2_notes_eleve($eleveT,$ordre,$ideleve,$idclasse,$dateDebut,$dateFin);
$classe = vp2_notes_classe($eleveT,$ordre,$ideleve,$idclasse,$dateDebut,$dateFin);

$jL = json_encode($labels);
$jE = json_encode($eleve);
$jC = json_encode($classe);
Pgclose();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Graphique Radar</title>
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Electrolize, Arial, sans-serif; background: #f5f7ff; }
.g-header { background: linear-gradient(135deg,#080A66,#1a1c8a); color: #fff; padding: 8px 14px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 7px; }
.g-header i { color: #CACCEF; }
.g-wrap { position: relative; width: 100%; height: calc(100vh - 37px); padding: 10px; display: flex; align-items: center; justify-content: center; }
.g-wrap canvas { max-width: 490px; max-height: 490px; }
</style>
</head>
<body>
<div class="g-header"><i class="bi bi-bullseye"></i> Radar des moyennes par matière</div>
<div class="g-wrap">
  <canvas id="chart"></canvas>
</div>
<script src="librairie_js/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chart'), {
  type: 'radar',
  data: {
    labels: <?= $jL ?>,
    datasets: [
      { label:'Moy. Élève', data:<?= $jE ?>, borderColor:'#080A66', backgroundColor:'rgba(8,10,102,0.15)', borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#080A66' },
      { label:'Moy. Classe', data:<?= $jC ?>, borderColor:'#e65100', backgroundColor:'rgba(230,81,0,0.08)', borderWidth:2, borderDash:[5,3], pointRadius:3, pointBackgroundColor:'#e65100' }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:true,
    plugins: { legend:{ position:'bottom', labels:{font:{size:11}, padding:12} } },
    scales: {
      r: {
        min:0, max:20,
        ticks:{ stepSize:5, backdropColor:'transparent', font:{size:9}, callback:v=>v+'/20' },
        grid:{ color:'#dde0f0' }, angleLines:{ color:'#dde0f0' },
        pointLabels:{ font:{size:10, weight:'600'}, color:'#333' }
      }
    }
  }
});
</script>
</body>
</html>

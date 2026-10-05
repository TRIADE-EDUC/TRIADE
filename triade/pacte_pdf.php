<?php
session_start();
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<title>Pacte Enseignant — Impression</title>
<style>
* { box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 12px; color: #000; margin: 20px; }
h1 { font-size: 16px; color: #0B3A0C; margin: 0 0 4px; }
h2 { font-size: 13px; color: #0B3A0C; margin: 14px 0 6px; }
.meta { font-size: 11px; color: #555; margin-bottom: 16px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 12px; }
th { background: #ffd600; color: #000; padding: 5px 8px; text-align: left; }
td { border: 1px solid #ccc; padding: 4px 8px; vertical-align: top; }
tr:nth-child(even) td { background: #fffde7; }
.total-row td { background: #d4edda !important; font-weight: bold; }
.kpi-box { display: inline-block; width: 30%; border: 1px solid #ccc; background: #e8f5e9;
           padding: 10px; text-align: center; margin-right: 2%; vertical-align: top; }
.kpi-val { font-size: 22px; font-weight: bold; color: #0B3A0C; }
.no-print { text-align: center; margin: 20px 0; }
.btn-print { padding: 8px 20px; font-size: 13px; background: #0B3A0C; color: #fff;
             border: none; border-radius: 4px; cursor: pointer; }
.btn-print:hover { background: #1a5c1a; }
@media print {
    .no-print { display: none; }
    body { margin: 10px; }
    th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .kpi-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
$prefixe = PREFIXE;
validerequete("menuadmin");

$annee = $_GET['annee'] ?? '';
$type  = in_array($_GET['type'] ?? '', ['indemnites','rapport']) ? $_GET['type'] : 'indemnites';
$titre = ($type === 'indemnites') ? '&Eacute;l&eacute;ments de paie — Pacte Enseignant' : 'Reporting — Pacte Enseignant';
$ws_annee  = $annee ? "AND i.annee_scolaire='$annee'" : '';
$ws_annee2 = $annee ? "AND annee_scolaire='$annee'" : '';
$ws_annee3 = $annee ? "i.annee_scolaire='$annee'" : '1=1';

if ($type === 'indemnites') {
    $sql = "SELECT p.pers_id, p.nom, p.prenom, t.libelle, t.taux_horaire,
                   COUNT(h.id_heure) AS nb_seances,
                   SUM(h.nb_heures) AS total_heures,
                   SUM(h.nb_heures)*t.taux_horaire AS montant_brut
            FROM {$prefixe}pacte_heures h
            JOIN {$prefixe}pacte_inscriptions i ON i.id_inscription=h.id_inscription
            JOIN {$prefixe}pacte_type_mission t ON t.id_type=i.id_type
            JOIN {$prefixe}personnel p ON p.pers_id=i.id_pers
            WHERE h.valide=1 $ws_annee
            GROUP BY p.pers_id, t.id_type ORDER BY p.nom, p.prenom, t.libelle";
    $rs   = @execSql($sql);
    $rows = $rs ? @chargeMat($rs) : [];
    $totG = array_sum(array_column($rows, 7));
} else {
    $rs1 = @execSql("SELECT t.libelle, COUNT(DISTINCT i.id_inscription), COALESCE(SUM(h.nb_heures),0), COALESCE(SUM(h.nb_heures)*t.taux_horaire,0)
                     FROM {$prefixe}pacte_type_mission t
                     LEFT JOIN {$prefixe}pacte_inscriptions i ON i.id_type=t.id_type AND $ws_annee3
                     LEFT JOIN {$prefixe}pacte_heures h ON h.id_inscription=i.id_inscription AND h.valide=1
                     GROUP BY t.id_type ORDER BY t.libelle");
    $d1 = $rs1 ? @chargeMat($rs1) : [];

    $rs2 = @execSql("SELECT statut, COUNT(*) FROM {$prefixe}pacte_inscriptions WHERE 1=1 $ws_annee2 GROUP BY statut");
    $d2  = $rs2 ? @chargeMat($rs2) : [];

    $rs3 = @execSql("SELECT COUNT(DISTINCT id_pers) FROM {$prefixe}pacte_inscriptions WHERE statut IN ('accepte','termine') $ws_annee2");
    $d3  = $rs3 ? @chargeMat($rs3) : []; $nbP = (int)($d3[0][0] ?? 0);

    $rs4 = @execSql("SELECT SUM(h.nb_heures), SUM(h.nb_heures*t.taux_horaire)
                     FROM {$prefixe}pacte_heures h
                     JOIN {$prefixe}pacte_inscriptions i ON i.id_inscription=h.id_inscription
                     JOIN {$prefixe}pacte_type_mission t ON t.id_type=i.id_type
                     WHERE h.valide=1 $ws_annee");
    $d4 = $rs4 ? @chargeMat($rs4) : []; $tH = $d4[0][0] ?? 0; $tM = $d4[0][1] ?? 0;
}
?>

<h1><?php echo $titre; ?></h1>
<div class="meta">
  Ann&eacute;e scolaire : <b><?php echo $annee ? htmlspecialchars($annee) : 'Toutes'; ?></b>
  &nbsp;&nbsp;|&nbsp;&nbsp;
  &Eacute;dit&eacute; le <?php echo date('d/m/Y &agrave; H:i'); ?>
</div>

<div class="no-print">
  <button class="btn-print" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
</div>

<?php if ($type === 'indemnites'): ?>

<h2>&Eacute;l&eacute;ments de paie par enseignant et type de mission</h2>
<table>
<tr><th>Enseignant</th><th>Type de mission</th><th>Taux horaire (€)</th><th>S&eacute;ances</th><th>Total heures</th><th>Montant brut (€)</th></tr>
<?php if (empty($rows)): ?>
<tr><td colspan="6" style="text-align:center;"><i>Aucune heure valid&eacute;e pour cette p&eacute;riode.</i></td></tr>
<?php endif; ?>
<?php foreach ($rows as $r): ?>
<tr>
  <td><?php echo htmlspecialchars($r[1].' '.$r[2]); ?></td>
  <td><?php echo htmlspecialchars($r[3]); ?></td>
  <td style="text-align:right"><?php echo number_format($r[4], 2, ',', ' '); ?></td>
  <td style="text-align:center"><?php echo (int)$r[5]; ?></td>
  <td style="text-align:right"><?php echo number_format($r[6], 2, ',', ' '); ?>h</td>
  <td style="text-align:right"><b><?php echo number_format($r[7], 2, ',', ' '); ?></b></td>
</tr>
<?php endforeach; ?>
<?php if (!empty($rows)): ?>
<tr class="total-row">
  <td colspan="5" style="text-align:right">TOTAL G&Eacute;N&Eacute;RAL :</td>
  <td style="text-align:right"><?php echo number_format($totG, 2, ',', ' '); ?>&nbsp;€</td>
</tr>
<?php endif; ?>
</table>

<?php else: /* rapport */ ?>

<div style="margin-bottom:20px;">
  <div class="kpi-box">
    <div class="kpi-val"><?php echo $nbP; ?></div>
    enseignants engag&eacute;s
  </div>
  <div class="kpi-box">
    <div class="kpi-val"><?php echo number_format($tH, 1, ',', ' '); ?>h</div>
    heures valid&eacute;es
  </div>
  <div class="kpi-box">
    <div class="kpi-val"><?php echo number_format($tM, 2, ',', ' '); ?>&nbsp;€</div>
    montant brut total
  </div>
</div>

<?php if (!empty($d2)): ?>
<h2>R&eacute;partition des inscriptions par statut</h2>
<table style="width:auto;min-width:300px;">
<tr><th>Statut</th><th>Nombre</th></tr>
<?php foreach ($d2 as $s): ?>
<tr><td><?php echo htmlspecialchars(ucfirst($s[0])); ?></td><td style="text-align:center"><?php echo (int)$s[1]; ?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<h2>D&eacute;tail par type de mission</h2>
<table>
<tr><th>Type de mission</th><th>Nb inscriptions</th><th>Heures valid&eacute;es</th><th>Montant brut (€)</th></tr>
<?php foreach ($d1 as $r): ?>
<tr>
  <td><?php echo htmlspecialchars($r[0]); ?></td>
  <td style="text-align:center"><?php echo (int)$r[1]; ?></td>
  <td style="text-align:right"><?php echo $r[2] ? number_format($r[2], 2, ',', ' ').'h' : '—'; ?></td>
  <td style="text-align:right"><?php echo $r[3] ? number_format($r[3], 2, ',', ' ').' €' : '—'; ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>

<div class="no-print" style="margin-top:30px;">
  <button class="btn-print" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
</div>

</body>
</html>

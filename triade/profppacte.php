<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 * Module : Pacte Enseignant (compte enseignant)
 ***************************************************************************/
error_reporting(0);

// CSRF init avant le HTML — pas de DB ici
if (empty($_SESSION['pacte_prof_csrf'])) {
    $_SESSION['pacte_prof_csrf'] = bin2hex(random_bytes(16));
}
$csrf     = $_SESSION['pacte_prof_csrf'];
$onglet   = $_GET['onglet'] ?? 'missions';
$msgOk    = '';
$msgError = '';
$postOk   = ($_SERVER['REQUEST_METHOD'] === 'POST') && hash_equals($csrf, $_POST['csrf_token'] ?? '');
$action   = $postOk ? ($_POST['action'] ?? '') : '';
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<style>
.pacte-tab { display:inline-block; padding:5px 14px; margin:2px; cursor:pointer; border:1px solid #ccaa00; border-radius:4px 4px 0 0; background:#fffde7; color:#000; font-weight:bold; font-size:12px; text-decoration:none; }
.pacte-tab.active { background:#ffd600; color:#000; border-color:#ccaa00; }
.pacte-tab:hover  { background:#fff176; color:#000; }
.tbl-pacte { width:100%; border-collapse:collapse; font-size:12px; }
.tbl-pacte th { background:#ffd600; color:#000; padding:5px 8px; }
.tbl-pacte td { border:1px solid #ccc; padding:4px 8px; }
.tbl-pacte tr:nth-child(even) td { background:#fffde7; }
.badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold; }
.badge-propose { background:#fff3cd; color:#856404; }
.badge-accepte { background:#d4edda; color:#155724; }
.badge-refuse  { background:#f8d7da; color:#721c24; }
.badge-termine { background:#cce5ff; color:#004085; }
</style>
<title>Triade — Pacte Enseignant</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$ident     = array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession = hashSessionVar($ident);
validerequete("profadmin");
$id_pers   = (int)$_SESSION['id_pers'];
$prefixe   = PREFIXE;
$y = (int)date('Y'); $m = (int)date('n');
$annee     = $_COOKIE['anneeScolaire'] ?? ($m >= 9 ? "$y-".($y+1) : ($y-1)."-$y");

// V&eacute;rification tables
$tableOk = false;
$res = @execSql("SHOW TABLES LIKE '{$prefixe}pacte_type_mission'");
if ($res && @chargeMat($res)) $tableOk = true;

// ─── Actions POST ──────────────────────────────────────────────────────────
if ($postOk && $tableOk) {

    if ($action === 'inscrire') {
        $id_type = (int)($_POST['id_type'] ?? 0);
        if ($id_type > 0) {
            $rCheck = @execSql("SELECT id_inscription FROM {$prefixe}pacte_inscriptions
                                WHERE id_pers=$id_pers AND id_type=$id_type
                                AND annee_scolaire='$annee' AND statut NOT IN ('refuse')");
            $dCheck = $rCheck ? @chargeMat($rCheck) : [];
            if (!empty($dCheck)) {
                $msgError = 'Vous &ecirc;tes d&eacute;j&agrave; inscrit &agrave; cette mission pour cette ann&eacute;e.';
            } else {
                $today = date('Y-m-d');
                $comm  = trim($_POST['commentaire'] ?? '');
                execSql("INSERT INTO {$prefixe}pacte_inscriptions
                         (id_type, id_pers, annee_scolaire, date_demande, statut, commentaire, created_at)
                         VALUES ($id_type, $id_pers, '$annee', '$today', 'propose', '$comm', NOW())");
                $msgOk = 'Votre demande d\'inscription a &eacute;t&eacute; enregistr&eacute;e. En attente de validation de la direction.';
            }
        }
    }

    if ($action === 'saisir_heures') {
        $id_insc = (int)($_POST['id_inscription'] ?? 0);
        $date_r  = $_POST['date_realisation'] ?? '';
        $nb_h    = number_format((float)($_POST['nb_heures'] ?? 0), 2, '.', '');
        $desc    = trim($_POST['description'] ?? '');

        $rInsc = @execSql("SELECT id_inscription FROM {$prefixe}pacte_inscriptions
                           WHERE id_inscription=$id_insc AND id_pers=$id_pers AND statut='accepte'");
        $dInsc = $rInsc ? @chargeMat($rInsc) : [];

        if (empty($dInsc)) {
            $msgError = 'Inscription introuvable ou non accept&eacute;e.';
        } elseif ($nb_h <= 0 || $nb_h > 24) {
            $msgError = 'Nombre d\'heures invalide (0–24).';
        } elseif (!$date_r) {
            $msgError = 'Date de r&eacute;alisation obligatoire.';
        } else {
            execSql("INSERT INTO {$prefixe}pacte_heures
                     (id_inscription, date_realisation, nb_heures, description, valide, created_at)
                     VALUES ($id_insc, '$date_r', $nb_h, '$desc', 0, NOW())");
            $msgOk = 'Heures enregistr&eacute;es, en attente de validation.';
        }
    }

    $_SESSION['pacte_prof_csrf'] = bin2hex(random_bytes(16));
    $csrf = $_SESSION['pacte_prof_csrf'];
}
?>
<?php print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre'].".js'></SCRIPT>"; ?>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<?php print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."1.js'></SCRIPT>"; ?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Pacte Enseignant</font></b></td></tr>
<tr id="cadreCentral0"><td><p align="left"><font color="#000000">

<?php if ($msgOk):    ?><br><center><font color="green"><b><?php echo $msgOk; ?></b></font></center><?php endif; ?>
<?php if ($msgError): ?><br><center><font color="red"><b><?php echo $msgError; ?></b></font></center><?php endif; ?>

<?php if (!$tableOk): ?>
<br><center><font color="orange">Le module Pacte Enseignant n'est pas encore configur&eacute;. Contactez votre administration.</font></center>
<?php else: ?>

<br>
<p style="font-size:12px;margin:0 0 8px;">
  <b>Ann&eacute;e scolaire :</b> <?php echo htmlspecialchars($annee); ?> &nbsp;|&nbsp;
  <b><?php echo htmlspecialchars($_SESSION['prenom'] ?? ''); ?> <?php echo htmlspecialchars($_SESSION['nom'] ?? ''); ?></b>
</p>

<!-- Onglets -->
<div style="margin:0 0 0;">
<a href="?onglet=missions"     class="pacte-tab <?php echo $onglet==='missions'     ? 'active':''; ?>">Missions disponibles</a>
<a href="?onglet=mes_missions" class="pacte-tab <?php echo $onglet==='mes_missions' ? 'active':''; ?>">Mes missions</a>
</div>
<div style="border:1px solid #0B3A0C;padding:12px;background:#fff;">

<?php /* ══════════════ MISSIONS DISPONIBLES ══════════════ */ ?>
<?php if ($onglet === 'missions'): ?>

<h4 style="color:#0B3A0C;margin:0 0 10px;">Missions disponibles — Pacte Enseignant</h4>
<p style="font-size:11px;color:#555;">
  Le Pacte Enseignant vous permet d'effectuer des missions suppl&eacute;mentaires r&eacute;mun&eacute;r&eacute;es.
  S&eacute;lectionnez une mission pour envoyer votre demande d'inscription &agrave; la direction.
</p>

<?php
$rs = @execSql("SELECT id_type, libelle, code, taux_horaire, description FROM {$prefixe}pacte_type_mission WHERE actif=1 ORDER BY libelle");
$missions = $rs ? @chargeMat($rs) : [];
?>

<?php if (empty($missions)): ?>
<p><i>Aucune mission disponible pour le moment.</i></p>
<?php else: ?>

<table class="tbl-pacte">
<tr><th>Mission</th><th>Taux horaire brut</th><th>Description</th><th>Action</th></tr>
<?php foreach ($missions as $m): ?>
<tr>
  <td><b><?php echo htmlspecialchars($m[1]); ?></b></td>
  <td><?php echo number_format($m[3], 2, ',', ' '); ?>&nbsp;€/h</td>
  <td style="font-size:11px;color:#555;"><?php echo htmlspecialchars($m[4] ?? ''); ?></td>
  <td>
    <form method="post" action="?onglet=missions" style="display:inline;">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
      <input type="hidden" name="action"     value="inscrire">
      <input type="hidden" name="id_type"    value="<?php echo $m[0]; ?>">
      <input type="text"   name="commentaire" placeholder="Commentaire (optionnel)" size="20" class="inputTexte" style="font-size:11px;">
      <input type="submit" value="S'inscrire" class="button" style="font-size:11px;">
    </form>
  </td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>

<?php /* ══════════════ MES MISSIONS ══════════════ */ ?>
<?php elseif ($onglet === 'mes_missions'): ?>

<h4 style="color:#0B3A0C;margin:0 0 10px;">Mes missions</h4>
<?php
$sql = "SELECT i.id_inscription, t.libelle, t.taux_horaire, i.date_demande,
               i.date_debut, i.date_fin, i.statut, i.commentaire,
               COALESCE(SUM(h.nb_heures),0) AS total_h,
               COALESCE(SUM(CASE WHEN h.valide=1 THEN h.nb_heures ELSE 0 END),0) AS h_valides
        FROM {$prefixe}pacte_inscriptions i
        JOIN {$prefixe}pacte_type_mission t ON t.id_type = i.id_type
        LEFT JOIN {$prefixe}pacte_heures h ON h.id_inscription = i.id_inscription
        WHERE i.id_pers = $id_pers
        GROUP BY i.id_inscription
        ORDER BY i.created_at DESC";
$rs   = @execSql($sql);
$insc = $rs ? @chargeMat($rs) : [];
$badgeClass = ['propose'=>'badge-propose','accepte'=>'badge-accepte','refuse'=>'badge-refuse','termine'=>'badge-termine'];
?>

<?php if (empty($insc)): ?>
<p><i>Vous n'avez aucune inscription pour cette ann&eacute;e. Rendez-vous dans "Missions disponibles".</i></p>
<?php else: ?>

<table class="tbl-pacte">
<tr><th>Mission</th><th>Demande</th><th>Statut</th><th>Heures saisies</th><th>Heures valid&eacute;es</th><th>Montant estim&eacute;</th></tr>
<?php foreach ($insc as $i): ?>
<tr>
  <td><b><?php echo htmlspecialchars($i[1]); ?></b><br><small style="color:#555;"><?php echo htmlspecialchars($i[7] ?? ''); ?></small></td>
  <td><?php echo htmlspecialchars($i[3]); ?></td>
  <td><span class="badge <?php echo $badgeClass[$i[6]] ?? ''; ?>"><?php echo htmlspecialchars($i[6]); ?></span></td>
  <td><?php echo number_format($i[8], 2, ',', ' '); ?>h</td>
  <td><?php echo number_format($i[9], 2, ',', ' '); ?>h</td>
  <td><?php echo number_format($i[9] * $i[2], 2, ',', ' '); ?>&nbsp;€</td>
</tr>
<?php if ($i[6] === 'accepte'): ?>
<tr>
  <td colspan="6" style="background:#f9f9f9;padding:8px 12px;">
    <form method="post" action="?onglet=mes_missions" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
      <input type="hidden" name="csrf_token"     value="<?php echo $csrf; ?>">
      <input type="hidden" name="action"         value="saisir_heures">
      <input type="hidden" name="id_inscription" value="<?php echo $i[0]; ?>">
      <small><b>Saisir heures :</b></small>
      Date : <input type="date" name="date_realisation" value="<?php echo date('Y-m-d'); ?>" class="inputTexte" style="font-size:11px;" required>
      Heures : <input type="number" name="nb_heures" step="0.5" min="0.5" max="24" value="1" class="inputTexte" style="font-size:11px;width:60px;" required>
      Description : <input type="text" name="description" maxlength="255" placeholder="ex : Cours de maths 6&egrave;me" class="inputTexte" style="font-size:11px;width:180px;">
      <input type="submit" value="Enregistrer" class="button" style="font-size:11px;">
    </form>
  </td>
</tr>
<?php endif; ?>
<?php endforeach; ?>
</table>

<!-- Historique des heures saisies -->
<?php
$ids = array_column($insc, 0);
if (!empty($ids)) {
    $idsStr = implode(',', array_map('intval', $ids));
    $sqlH = "SELECT h.date_realisation, t.libelle, h.nb_heures, h.description, h.valide
             FROM {$prefixe}pacte_heures h
             JOIN {$prefixe}pacte_inscriptions i ON i.id_inscription = h.id_inscription
             JOIN {$prefixe}pacte_type_mission t ON t.id_type = i.id_type
             WHERE h.id_inscription IN ($idsStr)
             ORDER BY h.date_realisation DESC
             LIMIT 50";
    $rsh    = @execSql($sqlH);
    $heures = $rsh ? @chargeMat($rsh) : [];
    if (!empty($heures)):
?>
<h5 style="color:#0B3A0C;margin:15px 0 5px;">D&eacute;tail des heures saisies (50 derni&egrave;res)</h5>
<table class="tbl-pacte">
<tr><th>Date</th><th>Mission</th><th>Heures</th><th>Description</th><th>Statut validation</th></tr>
<?php foreach ($heures as $h): ?>
<tr>
  <td><?php echo htmlspecialchars($h[0]); ?></td>
  <td><?php echo htmlspecialchars($h[1]); ?></td>
  <td><?php echo number_format($h[2], 2, ',', ' '); ?>h</td>
  <td><?php echo htmlspecialchars($h[3] ?? ''); ?></td>
  <td><?php echo $h[4] ? '<font color="green">✓ Valid&eacute;</font>' : '<font color="orange">⏳ En attente</font>'; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php } ?>

<?php endif; ?>

<?php endif; // fin onglets ?>
</div>
<?php endif; // tableOk ?>

</font></p></td></tr></table>

<?php
if ($_SESSION['membre'] == 'menuadmin') {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY>
</HTML>

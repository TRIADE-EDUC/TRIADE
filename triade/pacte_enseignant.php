<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 * Module : Pacte Enseignant (direction)
 ***************************************************************************/
error_reporting(0);
include_once("./common/config2.inc.php");

if (empty($_SESSION['pacte_csrf'])) {
    $_SESSION['pacte_csrf'] = bin2hex(random_bytes(16));
}
$csrf   = $_SESSION['pacte_csrf'];
$onglet = $_GET['onglet'] ?? 'missions';
$y = (int)date('Y'); $m = (int)date('n');
$annee  = $_COOKIE['anneeScolaire'] ?? ($m >= 9 ? "$y-".($y+1) : ($y-1)."-$y");
if (isset($_GET['annee'])) $_GET['annee'] = preg_replace('/\s*-\s*/', '-', trim($_GET['annee']));
$msgOk    = '';
$msgError = '';
$postOk   = ($_SERVER['REQUEST_METHOD'] === 'POST') && hash_equals($csrf, $_POST['csrf_token'] ?? '');
$action   = $postOk ? ($_POST['action'] ?? '') : '';
if (!$postOk && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $msgError = 'Requête invalide (CSRF).';
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Pacte Enseignant</title>
<style>
.pacte-tab         { display:inline-flex;align-items:center;gap:6px;padding:6px 14px;font-size:11px;font-weight:700;border:1px solid #c5cae9;border-bottom:none;border-radius:4px 4px 0 0;background:#eef0f8;color:#080A66;text-decoration:none;cursor:pointer;transition:background .15s }
.pacte-tab:hover   { background:#dde1f5;color:#080A66 }
.pacte-tab.active  { background:#080A66;color:#fff;border-color:#080A66 }
.badge-propose     { background:#fff8e1;color:#e65100;border:1px solid #ffb74d }
.badge-accepte     { background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7 }
.badge-refuse      { background:#fff9f9;color:#c62828;border:1px solid #f5c6cb }
.badge-termine     { background:#eef0f8;color:#080A66;border:1px solid #c5cae9 }
#coulBar0 { background-image: none; }
</style>
</HEAD>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
include_once("librairie_php/timezone.php");
$prefixe = PREFIXE;
validerequete("menuadmin");

if ($postOk) {

    if ($action === 'save_type') {
        $id   = (int)($_POST['id_type'] ?? 0);
        $lib  = trim($_POST['libelle'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $taux = number_format((float)($_POST['taux_horaire'] ?? 26.51), 2, '.', '');
        $desc = trim($_POST['description'] ?? '');
        $actif = isset($_POST['actif']) ? 1 : 0;
        if ($lib === '') {
            $msgError = 'Le libellé est obligatoire.';
        } elseif ($id > 0) {
            execSql("UPDATE {$prefixe}pacte_type_mission SET libelle='$lib', code='$code', taux_horaire=$taux, description='$desc', actif=$actif WHERE id_type=$id");
            $msgOk = 'Type de mission mis à jour.';
        } else {
            execSql("INSERT INTO {$prefixe}pacte_type_mission (libelle, code, taux_horaire, description, actif) VALUES ('$lib', '$code', $taux, '$desc', $actif)");
            $msgOk = 'Type de mission créé.';
        }
    }

    if ($action === 'del_type') {
        $id          = (int)($_POST['id_type']      ?? 0);
        $actifActuel = (int)($_POST['actif_actuel'] ?? 1);
        $nouvelActif = $actifActuel ? 0 : 1;
        if ($id > 0) {
            execSql("UPDATE {$prefixe}pacte_type_mission SET actif=$nouvelActif WHERE id_type=$id");
            $msgOk = $nouvelActif ? 'Type de mission activé.' : 'Type de mission désactivé.';
        }
    }

    if ($action === 'delete_type') {
        $id = (int)($_POST['id_type'] ?? 0);
        if ($id > 0) {
            execSql("DELETE FROM {$prefixe}pacte_type_mission WHERE id_type=$id");
            $msgOk = 'Type de mission supprimé.';
        }
    }

    if ($action === 'set_statut') {
        $id_insc = (int)($_POST['id_inscription'] ?? 0);
        $statut  = $_POST['statut'] ?? '';
        if ($id_insc > 0 && in_array($statut, ['accepte','refuse','termine','propose'])) {
            execSql("UPDATE {$prefixe}pacte_inscriptions SET statut='$statut' WHERE id_inscription=$id_insc");
            $msgOk = 'Statut mis à jour.';
        }
    }

    if ($action === 'valide_heure') {
        $id_h  = (int)($_POST['id_heure'] ?? 0);
        $valid = (int)($_POST['valide'] ?? 0);
        if ($id_h > 0) {
            execSql("UPDATE {$prefixe}pacte_heures SET valide=$valid WHERE id_heure=$id_h");
            $msgOk = $valid ? 'Heures validées.' : 'Validation annulée.';
        }
    }

    $_SESSION['pacte_csrf'] = bin2hex(random_bytes(16));
    $csrf = $_SESSION['pacte_csrf'];
    if ($msgOk) history_cmd($_SESSION['nom'] ?? '', 'PACTE', "Action: $action");
}

$tableOk = false;
$res = @execSql("SHOW TABLES LIKE '{$prefixe}pacte_type_mission'");
if ($res && @chargeMat($res)) $tableOk = true;
?>
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2">
  <b><font id='menumodule1'>Pacte Enseignant — Direction</font></b>
</td></tr>
<tr id="cadreCentral0"><td>
<!-- // debut -->

<div style="display:flex;flex-direction:column;gap:10px;padding:10px 6px">

<?php if ($msgOk): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ alertify.success(<?php echo json_encode($msgOk); ?>); });</script>
<?php endif; ?>
<?php if ($msgError): ?>
<div class="alert alert-danger">
  <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $msgError; ?>
</div>
<?php endif; ?>

<?php if (!$tableOk): ?>
<div class="card" style="border-top:3px solid #e65100">
  <div class="card-body" style="display:flex;align-items:center;gap:10px">
    <i class="bi bi-exclamation-triangle-fill" style="color:#e65100;font-size:20px;flex-shrink:0"></i>
    <div>
      <p style="margin:0;font-size:12px;font-weight:700;color:#e65100">Module non configuré</p>
      <p style="margin:4px 0 0;font-size:11px;color:#555">Créez les tables nécessaires via phpMyAdmin (contacter l'administrateur Triade).</p>
    </div>
  </div>
</div>

<?php else: ?>

<!-- Navigation onglets -->
<div style="display:flex;gap:0;flex-wrap:wrap">
  <a href="?onglet=missions"     class="pacte-tab <?php echo $onglet==='missions'     ?'active':''; ?>"><i class="bi bi-list-check"></i>Missions</a>
  <a href="?onglet=inscriptions" class="pacte-tab <?php echo $onglet==='inscriptions' ?'active':''; ?>"><i class="bi bi-person-check"></i>Inscriptions</a>
  <a href="?onglet=heures"       class="pacte-tab <?php echo $onglet==='heures'       ?'active':''; ?>"><i class="bi bi-clock"></i>Suivi heures</a>
  <a href="?onglet=indemnites"   class="pacte-tab <?php echo $onglet==='indemnites'   ?'active':''; ?>"><i class="bi bi-currency-euro"></i>Indemnités</a>
  <a href="?onglet=rapport"      class="pacte-tab <?php echo $onglet==='rapport'      ?'active':''; ?>"><i class="bi bi-bar-chart-line"></i>Reporting</a>
</div>
<div style="border:1px solid #c5cae9;border-radius:0 4px 4px 4px;background:#fff;padding:14px">

<?php /* ══ ONGLET MISSIONS ══ */ ?>
<?php if ($onglet === 'missions'): ?>
<?php
$edit_id = (int)($_GET['edit'] ?? 0);
$editRow = [];
if ($edit_id > 0) {
    $r = @execSql("SELECT * FROM {$prefixe}pacte_type_mission WHERE id_type=$edit_id");
    $d = $r ? @chargeMat($r) : [];
    if (!empty($d)) $editRow = $d[0];
}
?>

<!-- Formulaire ajout/modification -->
<div class="card" style="margin-bottom:12px;border-top:3px solid <?php echo $edit_id ? '#3949ab' : '#2e7d32' ?>">
  <div class="card-header">
    <span class="card-title">
      <i class="bi bi-<?php echo $edit_id ? 'pencil' : 'plus-circle' ?>" style="margin-right:5px"></i>
      <?php echo $edit_id ? 'Modifier la mission' : 'Ajouter une mission' ?>
    </span>
  </div>
  <div class="card-body">
    <form method="post" action="?onglet=missions">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
      <input type="hidden" name="action"     value="save_type">
      <input type="hidden" name="id_type"    value="<?php echo $edit_id; ?>">
      <div class="form-row">
        <label class="form-label">Libellé <span style="color:#c62828">*</span></label>
        <input type="text" name="libelle" value="<?php echo htmlspecialchars($editRow[1] ?? ''); ?>"
               maxlength="255" class="form-control">
      </div>
      <div class="form-row">
        <label class="form-label">Code court</label>
        <input type="text" name="code" value="<?php echo htmlspecialchars($editRow[2] ?? ''); ?>"
               maxlength="50" placeholder="ex: SOUTIEN_FR" class="form-control" style="max-width:180px">
      </div>
      <div class="form-row">
        <label class="form-label">Taux horaire (€ brut)</label>
        <input type="number" name="taux_horaire" value="<?php echo htmlspecialchars($editRow[3] ?? '26.51'); ?>"
               step="0.01" min="0" class="form-control" style="max-width:100px">
      </div>
      <div class="form-row">
        <label class="form-label">Description</label>
        <textarea name="description" rows="2" class="form-control"><?php echo htmlspecialchars($editRow[4] ?? ''); ?></textarea>
      </div>
      <div class="form-row" style="border-bottom:none">
        <label class="form-label">Active</label>
        <input type="checkbox" name="actif" value="1"
               <?php echo (!isset($editRow[5]) || $editRow[5]) ? 'checked' : ''; ?>
               style="accent-color:#080A66;width:16px;height:16px;cursor:pointer">
      </div>
      <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-<?php echo $edit_id ? 'check-lg' : 'plus-lg' ?>"></i>
          <?php echo $edit_id ? 'Enregistrer' : 'Créer' ?>
        </button>
        <?php if ($edit_id): ?>
        <a href="?onglet=missions" class="btn btn-secondary">
          <i class="bi bi-x-lg"></i> Annuler
        </a>
        <button type="button" class="btn btn-danger"
          onclick="if(confirm('Supprimer définitivement cette mission ?')) document.getElementById('del-form-<?php echo $edit_id; ?>').submit()">
          <i class="bi bi-trash3"></i> Supprimer
        </button>
        <?php endif; ?>
      </div>
    </form>
    <?php if ($edit_id): ?>
    <form id="del-form-<?php echo $edit_id; ?>" method="post" action="?onglet=missions" style="display:none">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
      <input type="hidden" name="action"     value="delete_type">
      <input type="hidden" name="id_type"    value="<?php echo $edit_id; ?>">
    </form>
    <?php endif; ?>
  </div>
</div>

<!-- Liste des missions -->
<div class="card">
  <div class="card-header"><span class="card-title"><i class="bi bi-table" style="margin-right:5px"></i>Types de missions</span></div>
  <div class="card-body" style="padding:0">
    <?php
    $rs   = @execSql("SELECT * FROM {$prefixe}pacte_type_mission ORDER BY actif DESC, libelle");
    $rows = $rs ? @chargeMat($rs) : [];
    ?>
    <table class="table" style="font-size:11px;margin:0">
      <thead>
        <tr>
          <th class="cc-th">ID</th>
          <th class="cc-th">Libellé</th>
          <th class="cc-th">Code</th>
          <th class="cc-th">Taux (€)</th>
          <th class="cc-th" style="text-align:center">Actif</th>
          <th class="cc-th">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="6" style="text-align:center;padding:12px;font-style:italic;color:#888">Aucune mission configurée.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r):
          $ri  = @execSql("SELECT COUNT(*) FROM {$prefixe}pacte_inscriptions WHERE id_type={$r[0]} AND annee_scolaire='$annee'");
          $di  = $ri ? @chargeMat($ri) : [];
          $nbI = (int)($di[0][0] ?? 0);
        ?>
        <tr class="cc-tr-data">
          <td><?php echo $r[0]; ?></td>
          <td><strong><?php echo htmlspecialchars($r[1]); ?></strong></td>
          <td><code style="background:#f0f2fa;padding:1px 5px;border-radius:3px;font-size:10px"><?php echo htmlspecialchars($r[2]); ?></code></td>
          <td><?php echo number_format($r[3], 2, ',', ' '); ?> €</td>
          <td style="text-align:center">
            <?php if ($r[5]): ?>
            <i class="bi bi-check-circle-fill" style="color:#2e7d32"></i>
            <?php else: ?>
            <i class="bi bi-x-circle" style="color:#c62828"></i>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:4px;flex-wrap:wrap;align-items:center">
              <a href="?onglet=missions&edit=<?php echo $r[0]; ?>" class="btn btn-secondary" style="padding:3px 8px;font-size:10px">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="post" action="?onglet=missions" style="display:inline">
                <input type="hidden" name="csrf_token"   value="<?php echo $csrf; ?>">
                <input type="hidden" name="action"       value="del_type">
                <input type="hidden" name="id_type"      value="<?php echo $r[0]; ?>">
                <input type="hidden" name="actif_actuel" value="<?php echo (int)$r[5]; ?>">
                <button type="submit" class="btn <?php echo $r[5] ? 'btn-secondary' : 'btn-action' ?>" style="padding:3px 8px;font-size:10px"
                        onclick="return confirm('<?php echo $r[5] ? 'Désactiver' : 'Activer'; ?> cette mission ?')">
                  <?php echo $r[5] ? '<i class="bi bi-toggle-on"></i>' : '<i class="bi bi-toggle-off"></i>'; ?>
                </button>
              </form>
              <?php if ($nbI > 0): ?>
              <span class="badge badge-primary" style="font-size:10px"><?php echo $nbI; ?> insc.</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php /* ══ ONGLET INSCRIPTIONS ══ */ ?>
<?php elseif ($onglet === 'inscriptions'): ?>

<div class="toolbar" style="margin-bottom:12px">
  <form method="get" action="" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0">
    <input type="hidden" name="onglet" value="inscriptions">
    <label style="font-size:12px;font-weight:600;color:#080A66">Année :</label>
    <input type="text" name="annee" value="<?php echo htmlspecialchars($_GET['annee'] ?? ''); ?>"
           placeholder="toutes" class="form-control" style="width:90px">
    <label style="font-size:12px;font-weight:600;color:#080A66">Statut :</label>
    <select name="filtre_statut" class="form-control" style="width:auto">
      <option value="">Tous</option>
      <?php foreach (['propose','accepte','refuse','termine'] as $s): ?>
      <option value="<?php echo $s; ?>" <?php echo (($_GET['filtre_statut']??'')===$s)?'selected':''; ?>><?php echo ucfirst($s); ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:4px 12px"><i class="bi bi-funnel"></i> Filtrer</button>
  </form>
</div>
<?php
$af = $_GET['annee'] ?? '';
$sf = $_GET['filtre_statut'] ?? '';
$ws_annee = $af ? "AND i.annee_scolaire='$af'" : '';
$ws = $sf ? "AND i.statut='$sf'" : '';
$sql = "SELECT i.id_inscription, t.libelle, p.nom, p.prenom, i.annee_scolaire,
               i.date_demande, i.date_debut, i.date_fin, i.statut, i.commentaire
        FROM {$prefixe}pacte_inscriptions i
        JOIN {$prefixe}pacte_type_mission t ON t.id_type=i.id_type
        JOIN {$prefixe}personnel p ON p.pers_id=i.id_pers
        WHERE 1=1 $ws_annee $ws ORDER BY i.created_at DESC";
$rs   = @execSql($sql);
$rows = $rs ? @chargeMat($rs) : [];
$bc   = ['propose'=>'badge-propose','accepte'=>'badge-accepte','refuse'=>'badge-refuse','termine'=>'badge-termine'];
?>
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="bi bi-person-check" style="margin-right:5px"></i>Demandes d'inscription</span>
    <span class="badge badge-primary"><?php echo count($rows); ?></span>
  </div>
  <div class="card-body" style="padding:0">
    <div style="overflow-x:auto">
      <table class="table" style="font-size:11px;margin:0">
        <thead>
          <tr>
            <th class="cc-th">#</th>
            <th class="cc-th">Mission</th>
            <th class="cc-th">Enseignant</th>
            <th class="cc-th">Année</th>
            <th class="cc-th">Demande</th>
            <th class="cc-th">Début</th>
            <th class="cc-th">Fin</th>
            <th class="cc-th">Statut</th>
            <th class="cc-th">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
          <tr><td colspan="9" style="text-align:center;padding:12px;font-style:italic;color:#888">Aucune inscription.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $r): ?>
          <tr class="cc-tr-data">
            <td><?php echo $r[0]; ?></td>
            <td><?php echo htmlspecialchars($r[1]); ?></td>
            <td><strong><?php echo htmlspecialchars($r[2].' '.$r[3]); ?></strong></td>
            <td><?php echo htmlspecialchars($r[4]); ?></td>
            <td style="white-space:nowrap"><?php echo htmlspecialchars($r[5]); ?></td>
            <td style="white-space:nowrap"><?php echo htmlspecialchars($r[6] ?? '—'); ?></td>
            <td style="white-space:nowrap"><?php echo htmlspecialchars($r[7] ?? '—'); ?></td>
            <td>
              <span class="badge <?php echo $bc[$r[8]] ?? ''; ?>" style="padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700">
                <?php echo htmlspecialchars($r[8]); ?>
              </span>
            </td>
            <td>
              <form method="post" action="?onglet=inscriptions" style="display:flex;gap:4px;flex-wrap:wrap;align-items:center">
                <input type="hidden" name="csrf_token"     value="<?php echo $csrf; ?>">
                <input type="hidden" name="action"         value="set_statut">
                <input type="hidden" name="id_inscription" value="<?php echo $r[0]; ?>">
                <select name="statut" class="form-control" style="font-size:10px;padding:2px 4px;width:auto">
                  <?php foreach (['propose','accepte','refuse','termine'] as $s): ?>
                  <option value="<?php echo $s; ?>" <?php echo ($r[8]===$s)?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" style="padding:2px 8px;font-size:10px">OK</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php /* ══ ONGLET HEURES ══ */ ?>
<?php elseif ($onglet === 'heures'): ?>

<div class="toolbar" style="margin-bottom:12px">
  <form method="get" action="" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0">
    <input type="hidden" name="onglet" value="heures">
    <label style="font-size:12px;font-weight:600;color:#080A66">Année :</label>
    <input type="text" name="annee" value="<?php echo htmlspecialchars($_GET['annee'] ?? ''); ?>"
           placeholder="toutes" class="form-control" style="width:90px">
    <label style="font-size:12px;font-weight:600;color:#080A66">Validées :</label>
    <select name="filtre_valide" class="form-control" style="width:auto">
      <option value="">Toutes</option>
      <option value="1" <?php echo (($_GET['filtre_valide']??'')==='1')?'selected':''; ?>>Validées</option>
      <option value="0" <?php echo (($_GET['filtre_valide']??'')==='0')?'selected':''; ?>>En attente</option>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:4px 12px"><i class="bi bi-funnel"></i> Filtrer</button>
  </form>
</div>
<?php
$af  = $_GET['annee'] ?? '';
$vf  = $_GET['filtre_valide'] ?? '';
$ws_annee = $af ? "AND i.annee_scolaire='$af'" : '';
$wv  = ($vf !== '') ? "AND h.valide=".(int)$vf : '';
$sql = "SELECT h.id_heure, p.nom, p.prenom, t.libelle, h.date_realisation,
                h.nb_heures, h.description, h.valide, t.taux_horaire
         FROM {$prefixe}pacte_heures h
         JOIN {$prefixe}pacte_inscriptions i ON i.id_inscription=h.id_inscription
         JOIN {$prefixe}pacte_type_mission t ON t.id_type=i.id_type
         JOIN {$prefixe}personnel p ON p.pers_id=i.id_pers
         WHERE 1=1 $ws_annee $wv ORDER BY h.date_realisation DESC";
$rs   = @execSql($sql);
$rows = $rs ? @chargeMat($rs) : [];
$totH = 0; $totM = 0;
foreach ($rows as $r) { if ($r[7]) { $totH += $r[5]; $totM += $r[5]*$r[8]; } }
?>
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="bi bi-clock" style="margin-right:5px"></i>Suivi des heures réalisées</span>
    <?php if ($totH > 0): ?>
    <span class="badge badge-success" style="font-size:11px"><?php echo number_format($totH,2,',',' '); ?>h validées</span>
    <?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div style="overflow-x:auto">
      <table class="table" style="font-size:11px;margin:0">
        <thead>
          <tr>
            <th class="cc-th">#</th>
            <th class="cc-th">Enseignant</th>
            <th class="cc-th">Mission</th>
            <th class="cc-th">Date</th>
            <th class="cc-th">Heures</th>
            <th class="cc-th">Montant brut</th>
            <th class="cc-th">Description</th>
            <th class="cc-th" style="text-align:center">Validé</th>
            <th class="cc-th">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
          <tr><td colspan="9" style="text-align:center;padding:12px;font-style:italic;color:#888">Aucune saisie.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $r): ?>
          <tr class="cc-tr-data">
            <td><?php echo $r[0]; ?></td>
            <td><strong><?php echo htmlspecialchars($r[1].' '.$r[2]); ?></strong></td>
            <td><?php echo htmlspecialchars($r[3]); ?></td>
            <td style="white-space:nowrap"><?php echo htmlspecialchars($r[4]); ?></td>
            <td><?php echo number_format($r[5], 2, ',', ' '); ?>h</td>
            <td><?php echo number_format($r[5]*$r[8], 2, ',', ' '); ?> €</td>
            <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?php echo htmlspecialchars($r[6]??''); ?>"><?php echo htmlspecialchars($r[6]??''); ?></td>
            <td style="text-align:center">
              <?php if ($r[7]): ?>
              <i class="bi bi-check-circle-fill" style="color:#2e7d32"></i>
              <?php else: ?>
              <i class="bi bi-hourglass-split" style="color:#e65100"></i>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" action="?onglet=heures" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="action"     value="valide_heure">
                <input type="hidden" name="id_heure"   value="<?php echo $r[0]; ?>">
                <input type="hidden" name="valide"     value="<?php echo $r[7]?0:1; ?>">
                <button type="submit" class="btn <?php echo $r[7] ? 'btn-secondary' : 'btn-action'; ?>" style="padding:3px 8px;font-size:10px">
                  <?php echo $r[7] ? 'Annuler' : 'Valider'; ?>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (!empty($rows)): ?>
          <tr style="background:#e8f5e9;font-weight:700;font-size:11px">
            <td colspan="4" style="text-align:right;padding:8px 12px;color:#2e7d32">Totaux (heures validées) :</td>
            <td style="padding:8px 12px;color:#2e7d32"><?php echo number_format($totH,2,',',''); ?>h</td>
            <td style="padding:8px 12px;color:#2e7d32"><?php echo number_format($totM,2,',',' '); ?> €</td>
            <td colspan="3"></td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php /* ══ ONGLET INDEMNITÉS ══ */ ?>
<?php elseif ($onglet === 'indemnites'): ?>

<div class="toolbar" style="margin-bottom:12px">
  <form method="get" action="" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0">
    <input type="hidden" name="onglet" value="indemnites">
    <label style="font-size:12px;font-weight:600;color:#080A66">Année scolaire :</label>
    <input type="text" name="annee" value="<?php echo htmlspecialchars($_GET['annee'] ?? ''); ?>"
           placeholder="toutes" class="form-control" style="width:90px">
    <button type="submit" class="btn btn-primary" style="padding:4px 12px"><i class="bi bi-calculator"></i> Calculer</button>
    <a href="pacte_pdf.php?annee=<?php echo urlencode($_GET['annee'] ?? ''); ?>&type=indemnites" target="_blank"
       class="btn btn-secondary" style="padding:4px 12px">
      <i class="bi bi-file-earmark-pdf"></i> PDF / Impression
    </a>
  </form>
</div>
<?php
$af  = $_GET['annee'] ?? '';
$ws_annee = $af ? "AND i.annee_scolaire='$af'" : '';
$sql = "SELECT p.pers_id, p.nom, p.prenom, t.libelle, t.taux_horaire,
               COUNT(h.id_heure) AS nb_seances,
               SUM(h.nb_heures) AS total_heures,
               SUM(h.nb_heures)*t.taux_horaire AS montant_brut,
               MIN(h.date_realisation) AS date_min,
               MAX(h.date_realisation) AS date_max,
               i.annee_scolaire
        FROM {$prefixe}pacte_heures h
        JOIN {$prefixe}pacte_inscriptions i ON i.id_inscription=h.id_inscription
        JOIN {$prefixe}pacte_type_mission t ON t.id_type=i.id_type
        JOIN {$prefixe}personnel p ON p.pers_id=i.id_pers
        WHERE h.valide=1 $ws_annee
        GROUP BY p.pers_id, t.id_type, i.annee_scolaire ORDER BY date_max DESC, p.nom, p.prenom";
$rs   = @execSql($sql);
$rows = $rs ? @chargeMat($rs) : [];
$totG = array_sum(array_column($rows, 7));
?>
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="bi bi-currency-euro" style="margin-right:5px"></i>Calcul des indemnités</span>
    <?php if ($totG > 0): ?>
    <span class="badge badge-success" style="font-size:11px">Total : <?php echo number_format($totG,2,',',' '); ?> €</span>
    <?php endif; ?>
  </div>
  <div class="card-body" style="padding:0">
    <div style="overflow-x:auto">
      <table class="table" style="font-size:11px;margin:0">
        <thead>
          <tr>
            <th class="cc-th">Enseignant</th>
            <th class="cc-th">Mission</th>
            <th class="cc-th">Période</th>
            <th class="cc-th">Taux/h (€)</th>
            <th class="cc-th">Séances</th>
            <th class="cc-th">Total heures</th>
            <th class="cc-th">Montant brut (€)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
          <tr><td colspan="7" style="text-align:center;padding:12px;font-style:italic;color:#888">Aucune heure validée pour cette période.</td></tr>
          <?php endif; ?>
          <?php foreach ($rows as $r): ?>
          <tr class="cc-tr-data">
            <td><strong><?php echo htmlspecialchars($r[1].' '.$r[2]); ?></strong></td>
            <td><?php echo htmlspecialchars($r[3]); ?></td>
            <td style="font-size:10px;color:#555" title="Année : <?php echo htmlspecialchars($r[10]); ?>">
              <?php echo date('d/m/Y', strtotime($r[8])); ?>
              <?php if ($r[8] !== $r[9]): ?> → <?php echo date('d/m/Y', strtotime($r[9])); ?><?php endif; ?>
            </td>
            <td><?php echo number_format($r[4],2,',',' '); ?></td>
            <td><?php echo (int)$r[5]; ?></td>
            <td><?php echo number_format($r[6],2,',',' '); ?></td>
            <td><strong><?php echo number_format($r[7],2,',',' '); ?></strong></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!empty($rows)): ?>
          <tr style="background:#e8f5e9;font-weight:700;font-size:11px">
            <td colspan="6" style="text-align:right;padding:8px 12px;color:#2e7d32">TOTAL GÉNÉRAL :</td>
            <td style="padding:8px 12px;color:#2e7d32"><?php echo number_format($totG,2,',',' '); ?> €</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php /* ══ ONGLET REPORTING ══ */ ?>
<?php elseif ($onglet === 'rapport'): ?>

<div class="toolbar" style="margin-bottom:12px">
  <form method="get" action="" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0">
    <input type="hidden" name="onglet" value="rapport">
    <label style="font-size:12px;font-weight:600;color:#080A66">Année scolaire :</label>
    <input type="text" name="annee" value="<?php echo htmlspecialchars($_GET['annee'] ?? ''); ?>"
           placeholder="toutes" class="form-control" style="width:90px">
    <button type="submit" class="btn btn-primary" style="padding:4px 12px"><i class="bi bi-bar-chart-line"></i> Afficher</button>
    <a href="pacte_pdf.php?annee=<?php echo urlencode($_GET['annee'] ?? ''); ?>&type=rapport" target="_blank"
       class="btn btn-secondary" style="padding:4px 12px">
      <i class="bi bi-file-earmark-pdf"></i> PDF / Impression
    </a>
  </form>
</div>
<?php
$af  = $_GET['annee'] ?? '';
$ws_annee  = $af ? "AND i.annee_scolaire='$af'" : '';
$ws_annee2 = $af ? "AND annee_scolaire='$af'" : '';
$ws_annee3 = $af ? "i.annee_scolaire='$af'" : '1=1';
$rs1 = @execSql("SELECT t.libelle, COUNT(DISTINCT i.id_inscription), COALESCE(SUM(h.nb_heures),0), COALESCE(SUM(h.nb_heures)*t.taux_horaire,0)
                 FROM {$prefixe}pacte_type_mission t
                 LEFT JOIN {$prefixe}pacte_inscriptions i ON i.id_type=t.id_type AND $ws_annee3
                 LEFT JOIN {$prefixe}pacte_heures h ON h.id_inscription=i.id_inscription AND h.valide=1
                 GROUP BY t.id_type ORDER BY t.libelle");
$d1  = $rs1 ? @chargeMat($rs1) : [];
$rs2 = @execSql("SELECT statut, COUNT(*) FROM {$prefixe}pacte_inscriptions WHERE 1=1 $ws_annee2 GROUP BY statut");
$d2  = $rs2 ? @chargeMat($rs2) : [];
$rs3 = @execSql("SELECT COUNT(DISTINCT id_pers) FROM {$prefixe}pacte_inscriptions WHERE statut IN ('accepte','termine') $ws_annee2");
$d3  = $rs3 ? @chargeMat($rs3) : []; $nbP = (int)($d3[0][0] ?? 0);
$rs4 = @execSql("SELECT SUM(h.nb_heures), SUM(h.nb_heures*t.taux_horaire)
                 FROM {$prefixe}pacte_heures h
                 JOIN {$prefixe}pacte_inscriptions i ON i.id_inscription=h.id_inscription
                 JOIN {$prefixe}pacte_type_mission t ON t.id_type=i.id_type
                 WHERE h.valide=1 $ws_annee");
$d4  = $rs4 ? @chargeMat($rs4) : []; $tH=$d4[0][0]??0; $tM=$d4[0][1]??0;
$bc  = ['propose'=>'badge-propose','accepte'=>'badge-accepte','refuse'=>'badge-refuse','termine'=>'badge-termine'];
?>

<!-- KPIs -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:14px">
  <div class="card" style="border-top:3px solid #080A66">
    <div class="card-body" style="text-align:center;padding:12px">
      <div style="font-size:24px;font-weight:700;color:#080A66"><?php echo $nbP; ?></div>
      <div style="font-size:11px;color:#555;margin-top:4px">enseignants engagés</div>
    </div>
  </div>
  <div class="card" style="border-top:3px solid #2e7d32">
    <div class="card-body" style="text-align:center;padding:12px">
      <div style="font-size:24px;font-weight:700;color:#2e7d32"><?php echo number_format($tH,1,',',' '); ?>h</div>
      <div style="font-size:11px;color:#555;margin-top:4px">heures validées</div>
    </div>
  </div>
  <div class="card" style="border-top:3px solid #3949ab">
    <div class="card-body" style="text-align:center;padding:12px">
      <div style="font-size:24px;font-weight:700;color:#3949ab"><?php echo number_format($tM,2,',',' '); ?> €</div>
      <div style="font-size:11px;color:#555;margin-top:4px">montant brut total</div>
    </div>
  </div>
</div>

<!-- Statuts inscriptions -->
<?php if (!empty($d2)): ?>
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;align-items:center">
  <span style="font-size:11px;font-weight:600;color:#555">Statuts :</span>
  <?php foreach ($d2 as $s): ?>
  <span class="badge <?php echo $bc[$s[0]]??''; ?>" style="padding:3px 10px;border-radius:4px;font-size:10px;font-weight:700">
    <?php echo htmlspecialchars($s[0]); ?> : <?php echo $s[1]; ?>
  </span>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Détail par type -->
<div class="card">
  <div class="card-header"><span class="card-title"><i class="bi bi-table" style="margin-right:5px"></i>Détail par type de mission</span></div>
  <div class="card-body" style="padding:0">
    <table class="table" style="font-size:11px;margin:0">
      <thead>
        <tr>
          <th class="cc-th">Type de mission</th>
          <th class="cc-th">Nb inscriptions</th>
          <th class="cc-th">Heures validées</th>
          <th class="cc-th">Montant brut (€)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($d1 as $r): ?>
        <tr class="cc-tr-data">
          <td><?php echo htmlspecialchars($r[0]); ?></td>
          <td><?php echo (int)$r[1]; ?></td>
          <td><?php echo $r[2] ? number_format($r[2],2,',',' ').'h' : '—'; ?></td>
          <td><?php echo $r[3] ? number_format($r[3],2,',',' ').' €' : '—'; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php endif; // fin onglets ?>
</div><!-- fin tab-content -->
<?php endif; // fin tableOk ?>

  <div style="margin-top:10px">
    <script language=JavaScript>buttonMagicRetour("acces2.php","_self")</script>
  </div>

</div>
<!-- // fin -->
</td></tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menuadmin2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuadmin22.js"></SCRIPT>
</body>
</html>

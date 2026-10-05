<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");

include_once("../librairie_php/db_visio.php");

$msg = $msgtype = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sauvegarder'])) {
    $ok = sauvegarderAbonnement(
        $_POST['plan']                ?? 'starter',
        $_POST['statut']              ?? 'inactif',
        $_POST['date_debut']          ?? '',
        $_POST['date_fin']            ?? '',
        $_POST['prix_mois']           ?? 0,
        $_POST['nb_max_participants'] ?? 6,
        $_POST['nb_max_salles']       ?? 2,
        $_POST['contact']             ?? '',
        $_POST['notes']               ?? ''
    );
    $msg = $ok ? 'Abonnement enregistré avec succès.' : 'Erreur lors de la sauvegarde (API centrale).';
    $msgtype = $ok ? 'success' : 'error';
}

$abo = getAbonnementVisio();
$plans = [
    'starter'  => ['label' => 'Starter',  'participants' => 6,  'salles' => 2,  'prix' => 15, 'icon' => '&#11088;'],
    'premium'  => ['label' => 'Premium',  'participants' => 15, 'salles' => 5,  'prix' => 35, 'icon' => '&#128640;'],
    'illimite' => ['label' => 'Illimité', 'participants' => 50, 'salles' => 20, 'prix' => 60, 'icon' => '&#8734;'],
];
$statutAff  = $abo ? $abo['statut'] : 'inactif';
$dateFinAff = $abo && $abo['date_fin'] ? date('d/m/Y', strtotime($abo['date_fin'])) : '—';
$planAff    = $abo ? ($plans[$abo['plan']]['label'] ?? $abo['plan']) : '—';
$badgeClass = 'badge-inactif';
if ($abo && $abo['statut'] === 'actif') {
    $badgeClass = ($abo['date_fin'] && $abo['date_fin'] < date('Y-m-d')) ? 'badge-expire' : 'badge-actif';
}
?>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<title>Triade — Abonnement Visioconférence</title>
<style>
.abo-wrap { padding: 10px 16px; }
.abo-status {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    background: #f0f2fa; border: 1px solid #c5caee; border-radius: 8px;
    padding: 14px 16px; margin-bottom: 18px; font-size: 12px;
}
.status-badge { padding: 4px 14px; border-radius: 20px; font-weight: bold; font-size: 12px; }
.badge-actif   { background: #d4edda; color: #155724; }
.badge-inactif { background: #f8d7da; color: #721c24; }
.badge-expire  { background: #fff3cd; color: #856404; }
.plan-cards { display: flex; gap: 14px; margin-bottom: 16px; flex-wrap: wrap; }
.plan-card {
    flex: 1; min-width: 130px; background: #fff;
    border: 1px solid #c5caee; border-radius: 10px;
    padding: 20px 16px; text-align: center;
    text-decoration: none !important; color: inherit; cursor: pointer;
}
.plan-card:hover, .plan-card:focus, .plan-card:active { text-decoration: none !important; color: inherit; outline: none; }
.plan-card *, .plan-card *:hover { text-decoration: none !important; }
.plan-card.selected { border-color: #080A66; background: #f0f2fa; }
.plan-card .tc-icon  { font-size: 28px; margin-bottom: 10px; }
.plan-card .tc-titre { font-weight: bold; color: #080A66; font-size: 13px; margin-bottom: 6px; }
.plan-card .tc-desc  { font-size: 11px; color: #666; line-height: 1.4; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.fg label { display: block; font-size: 11px; color: #555; margin-bottom: 3px; font-weight: bold; }
.fg input, .fg select, .fg textarea {
    width: 100%; padding: 6px 8px; border: 1px solid #c5caee;
    border-radius: 5px; font-size: 12px; box-sizing: border-box;
}
.fg textarea { height: 60px; resize: vertical; }
.btn-save {
    background: #080A66; color: #fff; border: none; padding: 9px 24px;
    border-radius: 6px; font-size: 13px; font-weight: bold; cursor: pointer; margin-top: 12px;
}
.btn-save:hover { background: #0d12a3; }
.section-box { background: #fff; border: 1px solid #dde; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px; }
.section-box h3 { color: #080A66; font-size: 13px; margin: 0 0 12px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Abonnement Visioconférence</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div class="abo-wrap">
    <div class="abo-status">
        <div>
            <div style="color:#666;margin-bottom:3px;">Statut</div>
            <span class="status-badge <?php echo $badgeClass; ?>"><?php echo strtoupper($statutAff); ?></span>
        </div>
        <div>
            <div style="color:#666;margin-bottom:3px;">Plan</div>
            <strong style="color:#080A66;"><?php echo htmlspecialchars($planAff); ?></strong>
        </div>
        <div>
            <div style="color:#666;margin-bottom:3px;">Expire le</div>
            <strong><?php echo $dateFinAff; ?></strong>
        </div>
        <?php if ($abo && $abo['statut'] === 'actif'): ?>
        <div style="margin-left:auto;">
            <a href="../visio/rooms.php" target="_blank"
               style="background:#080A66;color:#fff;padding:6px 14px;border-radius:5px;text-decoration:none;font-size:12px;">
                Ouvrir la visio →
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div style="font-size:12px;font-weight:bold;color:#080A66;margin-bottom:8px;">Choisir un plan :</div>
    <div class="plan-cards">
        <?php foreach ($plans as $key => $p): ?>
        <div class="plan-card <?php echo ($abo && $abo['plan'] === $key) ? 'selected' : ''; ?>"
             onclick="selectionnerPlan('<?php echo $key; ?>', <?php echo $p['participants']; ?>, <?php echo $p['salles']; ?>, <?php echo $p['prix']; ?>, event)">
            <div class="tc-icon"><?php echo $p['icon']; ?></div>
            <div class="tc-titre"><?php echo $p['label']; ?> — <?php echo $p['prix']; ?>€/mois</div>
            <div class="tc-desc"><?php echo $p['participants']; ?> participants<br><?php echo $p['salles']; ?> salles</div>
        </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="visio_abo.php">
        <div class="section-box">
            <h3>Configuration</h3>
            <div class="form-grid">
                <div class="fg">
                    <label>Plan</label>
                    <select name="plan" id="f_plan">
                        <?php foreach ($plans as $key => $p): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($abo && $abo['plan'] === $key) ? 'selected' : ''; ?>>
                            <?php echo $p['label']; ?> — <?php echo $p['prix']; ?>€/mois
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="fg">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="actif"    <?php echo ($abo && $abo['statut'] === 'actif')    ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactif"  <?php echo (!$abo || $abo['statut'] === 'inactif') ? 'selected' : ''; ?>>Inactif</option>
                        <option value="suspendu" <?php echo ($abo && $abo['statut'] === 'suspendu') ? 'selected' : ''; ?>>Suspendu</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Date début</label>
                    <input type="date" name="date_debut" id="f_date_debut"
                           value="<?php echo $abo ? $abo['date_debut'] : date('Y-m-d'); ?>">
                </div>
                <div class="fg">
                    <label>Date fin</label>
                    <input type="date" name="date_fin" id="f_date_fin"
                           value="<?php echo $abo ? $abo['date_fin'] : ''; ?>">
                </div>
                <div class="fg">
                    <label>Prix mensuel (€)</label>
                    <input type="number" name="prix_mois" id="f_prix" step="0.01" min="0"
                           value="<?php echo $abo ? $abo['prix_mois'] : 15; ?>">
                </div>
                <div class="fg">
                    <label>Participants max / salle</label>
                    <input type="number" name="nb_max_participants" id="f_maxp" min="2" max="50"
                           value="<?php echo $abo ? $abo['nb_max_participants'] : 6; ?>">
                </div>
                <div class="fg">
                    <label>Salles simultanées max</label>
                    <input type="number" name="nb_max_salles" id="f_maxs" min="1" max="100"
                           value="<?php echo $abo ? $abo['nb_max_salles'] : 2; ?>">
                </div>
                <div class="fg">
                    <label>Contact facturation</label>
                    <input type="text" name="contact"
                           value="<?php echo htmlspecialchars($abo ? $abo['contact_facturation'] : ''); ?>"
                           placeholder="email ou nom">
                </div>
                <div class="fg" style="grid-column:1/-1;">
                    <label>Notes internes</label>
                    <textarea name="notes"><?php echo htmlspecialchars($abo ? $abo['notes'] : ''); ?></textarea>
                </div>
            </div>
        </div>
        <button type="submit" name="sauvegarder" class="btn-save">Enregistrer</button>
    </form>
</div>

</td></tr>
</table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>
function selectionnerPlan(plan, maxP, maxS, prix, e) {
    document.getElementById('f_plan').value = plan;
    document.getElementById('f_maxp').value = maxP;
    document.getElementById('f_maxs').value = maxS;
    document.getElementById('f_prix').value = prix;
    var fin = new Date();
    fin.setMonth(fin.getMonth() + 1);
    document.getElementById('f_date_fin').value = fin.toISOString().split('T')[0];
    document.querySelectorAll('.plan-card').forEach(function(c) { c.classList.remove('selected'); });
    e.currentTarget.classList.add('selected');
}
<?php if ($msg): ?>
window.addEventListener('DOMContentLoaded', function() {
    alertify.<?php echo $msgtype === 'success' ? 'success' : 'error'; ?>('<?php echo addslashes($msg); ?>');
});
<?php endif; ?>
</script>
</body>
</HTML>

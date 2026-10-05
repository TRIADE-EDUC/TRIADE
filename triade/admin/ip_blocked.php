<?php
session_start();
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade — IP bloquées</title>
<style>
.ipb-badge-blocked { display:inline-block; padding:2px 10px; border-radius:12px; font-size:11px; font-weight:700; background:#fce4ec; color:#c62828; }
.ipb-badge-expired { display:inline-block; padding:2px 10px; border-radius:12px; font-size:11px; background:#f5f5f5; color:#999; }
.ipb-remaining    { font-size:12px; font-family:Electrolize,Trebuchet MS,Arial; color:#c62828; font-weight:700; }
.ipb-card         { display:inline-block; background:#fff3e0; border:1px solid #ffcc80; border-radius:8px; padding:8px 16px;
                    font-size:12px; font-family:Electrolize,Trebuchet MS,Arial; color:#e65100; margin-bottom:10px; }
.ipb-card.ok      { background:#e8f5e9; border-color:#a5d6a7; color:#2e7d32; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>IPs bloquées — Tentatives de connexion</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$cnx = cnx();

// Déverrouillage d'une IP
if (isset($_POST["action"]) && $_POST["action"] === "unlock" && !empty($_POST["ip"])) {
    $ip_del = addslashes($_POST["ip"]);
    execSql("DELETE FROM ".PREFIXE."ip_timeout WHERE ip='$ip_del'");
}

// Déverrouillage de toutes les IPs
if (isset($_POST["action"]) && $_POST["action"] === "unlock_all") {
    execSql("DELETE FROM ".PREFIXE."ip_timeout");
}

// Lecture
$res  = execSql("SELECT ip, timeout, locked_until FROM ".PREFIXE."ip_timeout ORDER BY locked_until DESC");
$data = chargeMat($res);

$nb_bloquees = 0;
if (countTriade($data) > 0) {
    foreach ($data as $row) {
        if (!empty($row[2]) && strtotime($row[2]) > time()) $nb_bloquees++;
    }
}

// Résumé
if ($nb_bloquees > 0) {
    echo '<div class="ipb-card">' . $nb_bloquees . ' IP' . ($nb_bloquees > 1 ? 's' : '') . ' actuellement bloquée' . ($nb_bloquees > 1 ? 's' : '') . '</div>&nbsp;';
    echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Déverrouiller toutes les IPs ?\');">';
    echo '<input type="hidden" name="action" value="unlock_all">';
    echo '<input type="submit" value="Tout déverrouiller" class="btn-suppr">';
    echo '</form>';
} else {
    echo '<div class="ipb-card ok">Aucune IP bloquée actuellement</div>';
}

if (countTriade($data) == 0) {
    echo '<br><br><span style="font-size:12px;color:#666;font-family:Electrolize,Trebuchet MS,Arial;">Aucun enregistrement.</span>';
} else {
?>
<br>
<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;">
<tr>
    <th class="cc-th" style="width:150px">Adresse IP</th>
    <th class="cc-th" style="width:110px">Délai cumulé</th>
    <th class="cc-th" style="width:160px">Bloquée jusqu'au</th>
    <th class="cc-th" style="width:110px">Temps restant</th>
    <th class="cc-th" style="width:50px">Statut</th>
    <th class="cc-th">Action</th>
</tr>
<?php
foreach ($data as $row) {
    $ip_val       = htmlspecialchars($row[0]);
    $timeout_val  = intval($row[1]);
    $locked_until = $row[2];
    $remaining    = !empty($locked_until) ? strtotime($locked_until) - time() : 0;
    $is_blocked   = $remaining > 0;

    $badge = $is_blocked
        ? '<span class="ipb-badge-blocked">BLOQUÉE</span>'
        : '<span class="ipb-badge-expired">Expiré</span>';

    if ($is_blocked) {
        $m = floor($remaining / 60);
        $s = $remaining % 60;
        $restant = '<span class="ipb-remaining">' . ($m > 0 ? $m . 'min ' : '') . $s . 's</span>';
    } else {
        $restant = '<span style="color:#999;font-size:11px;">—</span>';
    }

    $locked_fmt = !empty($locked_until) ? date('d/m/Y H:i:s', strtotime($locked_until)) : '—';

    if ($timeout_val >= 3600) {
        $timeout_fmt = '≥ 60 min';
    } elseif ($timeout_val >= 60) {
        $timeout_fmt = floor($timeout_val / 60) . 'min ' . ($timeout_val % 60) . 's';
    } else {
        $timeout_fmt = $timeout_val . 's';
    }

    echo '<tr class="cc-tr-data">';
    echo '<td style="font-family:monospace;font-size:12px;padding:6px 8px;">' . $ip_val . '</td>';
    echo '<td style="text-align:center;font-size:12px;">' . $timeout_fmt . '</td>';
    echo '<td style="text-align:center;font-size:12px;">' . $locked_fmt . '</td>';
    echo '<td style="text-align:center;">' . $restant . '</td>';
    echo '<td style="text-align:center;">' . $badge . '</td>';
    echo '<td style="text-align:center;">';
    echo '<form method="post" style="margin:0;">';
    echo '<input type="hidden" name="action" value="unlock">';
    echo '<input type="hidden" name="ip" value="' . $ip_val . '">';
    echo '<input type="submit" value="Déverrouiller" class="btn-valider" style="font-size:11px;padding:3px 10px;">';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}
?>
</table>
<div style="margin:8px 0 2px;font-size:11px;color:#888;font-family:Electrolize,Trebuchet MS,Arial;">
    <?php echo countTriade($data); ?> enregistrement(s) — délai doublé à chaque échec, plafonné à 3600s.
</div>
<?php } ?>

</td></tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
<?php Pgclose(); ?>

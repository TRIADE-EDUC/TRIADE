<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<HTML>
<HEAD>
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../common/config6.inc.php");
include_once("../librairie_php/db_visio.php");
$cnx = cnx();

function genererCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code  = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

$code_cree  = '';
$lien_host  = '';
$lien_guest = '';

if (isset($_POST['creer'])) {
    $code_cree  = genererCode();
    $base_url   = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $visio_path = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/') . '/visio';
    $lien_host  = $base_url . $visio_path . '/room.php?code=' . $code_cree . '&role=host';
    $lien_guest = $base_url . $visio_path . '/room.php?code=' . $code_cree;
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
<title>Triade — Créer une salle Visio</title>
<style>
.code-badge {
    display:inline-block; background:#080A66; color:#fff;
    font-size:22px; letter-spacing:6px; padding:10px 20px;
    border-radius:8px; font-weight:bold; margin:12px 0;
}
.lien-box {
    background:#f0f2fa; border:1px solid #c5caee; border-radius:6px;
    padding:10px 12px; font-family:monospace; font-size:12px;
    word-break:break-all; margin-bottom:6px;
}
.lien-label { font-size:11px; color:#555; margin-bottom:3px; font-weight:bold; }
.sep { border:none; border-top:1px solid #e0e3f0; margin:18px 0; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Créer une salle Visioconférence</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="padding:14px 16px;">

<?php if ($code_cree): ?>

    <div style="margin-bottom:10px;font-size:12px;color:#555;">Salle créée — Code :</div>
    <div class="code-badge"><?php echo htmlspecialchars($code_cree); ?></div>
    <hr class="sep">

    <div class="lien-label">Lien Animateur (vous) :</div>
    <div class="lien-box" id="lien_host"><?php echo htmlspecialchars($lien_host); ?></div>
    <button class="btn-secondary" onclick="copier('lien_host', this)" style="margin-bottom:14px;">Copier</button>

    <div class="lien-label">Lien à partager (élèves / parents) :</div>
    <div class="lien-box" id="lien_guest"><?php echo htmlspecialchars($lien_guest); ?></div>
    <button class="btn-secondary" onclick="copier('lien_guest', this)" style="margin-bottom:14px;">Copier</button>

    <hr class="sep">
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="<?php echo htmlspecialchars($lien_host); ?>" target="_blank" class="btn-primary">&#9654; Rejoindre comme animateur</a>
        <a href="visio_create.php" class="btn-secondary">+ Créer une autre salle</a>
        <a href="visio_rooms.php" class="btn-secondary">&#128249; Voir les salles</a>
    </div>

<?php else: ?>

    <form method="POST" action="visio_create.php">
        <div class="fg" style="margin-bottom:14px;">
            <label style="display:block;font-size:11px;color:#555;margin-bottom:4px;font-weight:bold;">Nom de la salle</label>
            <input type="text" name="nom_salle" placeholder="Ex : Cours de maths - Lundi 14h"
                   style="width:100%;padding:8px 10px;border:1px solid #c5caee;border-radius:5px;font-size:13px;box-sizing:border-box;">
        </div>
        <button type="submit" name="creer" class="btn-primary">Créer la salle</button>
        <a href="visio_rooms.php" class="btn-secondary" style="margin-left:8px;">&#8592; Retour aux salles</a>
    </form>

<?php endif; ?>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>

<script>
function copier(id, btn) {
    var texte = document.getElementById(id).innerText;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(texte).then(function() {
            btn.innerText = 'Copie !';
            setTimeout(function() { btn.innerText = 'Copier'; }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = texte;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.innerText = 'Copie !';
        setTimeout(function() { btn.innerText = 'Copier'; }, 2000);
    }
}
</script>
</body>
</HTML>

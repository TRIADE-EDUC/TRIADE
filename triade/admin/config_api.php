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
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../librairie_php/timezone.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Configuration des API</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<!-- ═══════════════════════════════════════════════════════
     Section 1 : Configuration des API
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" height="85" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Configuration des API</font></b></td></tr>
<tr id="cadreCentral0"><td>

<?php
include_once("../common/config2.inc.php");
$cnx = cnx();
$APIAccess = "non";
if (defined('APIACCESS') && APIACCESS == "oui") $APIAccess = "oui";

if ($APIAccess == "non") {
    print "<div style='margin:16px 8px;padding:10px 14px;background:#fff0f0;border:1px solid #fcc;border-radius:6px;color:#c62828;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;'>
        Vous n'avez pas activé les API dans le module configuration générale.
    </div>";
} else {
?>

<form method='post' action='config_api.php'>
<div class="na-card">
    <div class="na-row">
        <span class="na-lbl">Nom de la clé :</span>
        <input type='text' name='nom' size='20' class="cc-select">
    </div>
    <div class="na-row">
        <span class="na-lbl">IP du client :</span>
        <input type='text' name='ip' size='20' class="cc-select">
    </div>
</div>
<br>
<div class="na-foot">
    <button type="submit" name="create" class="btn-enr">Enregistrer</button>
</div>
<br>
</form>

<?php
    function generateApiKey($length = 32) {
        return bin2hex(random_bytes($length));
    }

    if (isset($_GET['ids'])) {
        suppApiClient($_GET['ids']);
    }

    if (isset($_POST['create'])) {
        $ip  = $_POST['ip'];
        $nom = $_POST['nom'];
        $apiKey = generateApiKey();
        engApiClient($apiKey, $nom, $ip);
    }

    $data = listingApiClient();
    // nom, clef, ip, date_creation, active, id
    if (count($data) > 0) {
?>

<table class="cc-data-table" style="margin:8px 0;">
<thead>
<tr class="cc-thead-row">
    <th class="cc-th">Nom</th>
    <th class="cc-th">IP</th>
    <th class="cc-th">Clé API</th>
    <th class="cc-th"></th>
</tr>
</thead>
<tbody>
<?php
        for ($i = 0; $i < count($data); $i++) {
            $id = $data[$i][5];
            echo '<tr class="cc-tr-data">';
            echo '<td class="cc-td"><b>' . htmlspecialchars($data[$i][0]) . '</b></td>';
            echo '<td class="cc-td" style="font-size:11px;color:#555;">' . htmlspecialchars($data[$i][2]) . '</td>';
            echo '<td class="cc-td" style="font-size:10px;color:#555;word-break:break-all;">' . htmlspecialchars($data[$i][1]) . '</td>';
            echo '<td class="cc-td cc-td-center">';
            echo '<button type="button" class="btn-enr" style="background:#c62828;padding:2px 8px;font-size:10px;"'
               . ' onclick="if(confirm(\'Supprimer ce client API ?\')) open(\'config_api.php?ids=' . $id . '\',\'_self\',\'\')">'
               . 'Supprimer</button>';
            echo '</td>';
            echo '</tr>';
        }
?>
</tbody>
</table>

<?php
    }
} // end else APIAccess
?>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Section 2 : Documentation API v1
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Documentation API v1</font></b></td></tr>
<tr id="cadreCentral0"><td>

<div class="dest-list" style="margin:8px 5px;">
    <div class="dest-row">
        <span class="dest-row-label">Étape 1 : Authentification</span>
        <a href="api_doc_chapitre.php?chapitre=authentification" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.1 — Récupérer tous les élèves</span>
        <a href="api_doc_chapitre.php?chapitre=eleves" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.2 — Récupérer les notes d'un élève par période</span>
        <a href="api_doc_chapitre.php?chapitre=notes" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.3 — Récupérer l'emploi du temps</span>
        <a href="api_doc_chapitre.php?chapitre=edt" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.4 — Récupérer le personnel</span>
        <a href="api_doc_chapitre.php?chapitre=prof" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.5 — Récupérer les classes</span>
        <a href="api_doc_chapitre.php?chapitre=classes" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.6 — Récupérer les matières</span>
        <a href="api_doc_chapitre.php?chapitre=matieres" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.7 — Récupérer les affectations professeurs/classes</span>
        <a href="api_doc_chapitre.php?chapitre=affectation" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.8 — Récupérer les absences et retards d'un élève</span>
        <a href="api_doc_chapitre.php?chapitre=absencesretards" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.9 — Récupérer les sanctions et retenues d'un élève</span>
        <a href="api_doc_chapitre.php?chapitre=sanctionsdisciplines" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">2.10 — Récupérer les unités d'enseignement (UE)</span>
        <a href="api_doc_chapitre.php?chapitre=ue" class="btn-dest">Consulter</a>
    </div>
    <div class="dest-row">
        <span class="dest-row-label">Contrôles de sécurité</span>
        <a href="api_doc_chapitre.php?chapitre=securite" class="btn-dest">Consulter</a>
    </div>
</div>

</td></tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>

<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<style>
body{font-family:Arial,sans-serif;background:#f4f6fb;margin:0;padding:12px}
.history-header{background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.history-header-title{font-size:14px;font-weight:700;letter-spacing:.3px}
.history-header-sub{font-size:11px;opacity:.75}
.history-log-link{display:inline-flex;align-items:center;gap:5px;font-size:11px;color:#fff;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);border-radius:5px;padding:3px 9px;text-decoration:none}
.history-log-link:hover{background:rgba(255,255,255,.25)}
.history-table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #dde0f0;border-top:none;border-radius:0 0 8px 8px;overflow:hidden;box-shadow:0 2px 10px rgba(8,10,102,.07)}
.history-table thead th{background:#f0f2fa;color:#080A66;font-size:11px;font-weight:700;padding:8px 10px;text-align:left;border-bottom:2px solid #dde0f0}
.history-table tbody tr{border-bottom:1px solid #eef0f8;transition:background .12s}
.history-table tbody tr:hover{background:#f5f7ff}
.history-table td{padding:7px 10px;font-size:11px;color:#333;vertical-align:top}
.history-date{white-space:nowrap;color:#555}
.history-time{font-size:10px;color:#888}
.history-user{font-weight:600;color:#080A66}
.history-op{color:#333}
.history-comment{color:#555;font-style:italic}
.history-error{text-align:center;padding:30px;color:#c62828;font-weight:600}
</style>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body>
<?php
$valide=0;

if ( (defined("VIESCOLAIREHISTORYCMD")) && (VIESCOLAIREHISTORYCMD == "oui") && ($_SESSION["membre"] == "menuscolaire")) { $valide=1; }
if ($_SESSION["admin1"] == "Administrateur") { $valide=1; }
if ($_SESSION["membre"] == "menuadmin") { $valide=1; }
if ($valide == 0) {
    print "<div class='history-error'>".LANGMESS37."</div>";
} else {

$log_link = "";
if (file_exists("./data/install_log/access.log")) {
    if (($_SESSION["membre"] == "menuadmin") || (isset($_SESSION['admin1']))) {
        $log_link = "<a href='visu_accesslog.php' target='_blank' class='history-log-link'>&#128196; Journal d'activité</a>";
    }
}
?>

<div class="history-header">
    <div>
        <div class="history-header-title">Liste des opérations effectuées</div>
        <div class="history-header-sub">400 dernières opérations</div>
    </div>
    <?php print $log_link; ?>
</div>

<table class="history-table">
<thead>
<tr>
    <th>Date</th>
    <th>Individu</th>
    <th>Opération</th>
    <th>Commentaire</th>
</tr>
</thead>
<tbody>
<?php
$cnx=cnx();
$data=affHistoryCmd();
// time_cmd,date_cmd,user_cmd,cmd,commentaire
for($i=0;$i<countTriade($data);$i++) {
    $data[$i][4]=XSSHtml($data[$i][4]);
    ?>
    <tr>
        <td><span class="history-date"><?php print dateForm($data[$i][1])?></span><br><span class="history-time"><?php print $data[$i][0]?></span></td>
        <td class="history-user"><?php print $data[$i][2]?></td>
        <td class="history-op"><?php print $data[$i][3]?></td>
        <td class="history-comment"><?php print stripslashes(stripslashes($data[$i][4])) ?></td>
    </tr>
    <?php
}
?>
</tbody>
</table>
<?php
Pgclose();
}
?>
</BODY></HTML>

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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td><b><font id='menumodule1'>Savoir / être</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign='top'>

<?php
$anneeScolaire = anneeScolaireViaIdClasse($_SESSION["idClasse"]);
$dataInfo = recupSavoirEtre($_SESSION["id_pers"],$_SESSION["idClasse"],$anneeScolaire);
// ponctualite, motivation, dynamisme, id, date, idpers, idmatiere

$items = [];
for ($j=0; $j<countTriade($dataInfo); $j++) {
    $ponct = stripslashes($dataInfo[$j][0]);
    $motiv = stripslashes($dataInfo[$j][1]);
    $dynam = stripslashes($dataInfo[$j][2]);
    $id    = $dataInfo[$j][3];
    if (($ponct == "") && ($motiv == "") && ($dynam == "")) {
        deleteSavoirEtre2($id);
        continue;
    }
    $items[] = [
        'date'    => dateForm($dataInfo[$j][4]),
        'personne'=> recherche_personne2($dataInfo[$j][5]),
        'matiere' => chercheMatiereNom($dataInfo[$j][6]),
        'ponct'   => htmlspecialchars($ponct, ENT_QUOTES),
        'motiv'   => htmlspecialchars($motiv, ENT_QUOTES),
        'dynam'   => htmlspecialchars($dynam, ENT_QUOTES),
    ];
}

if (empty($items)) {
    echo "<div class='sev-empty'>Aucune évaluation enregistrée.</div>";
} else {
    echo "<div class='sev-list'>";
    foreach ($items as $it) {
        echo "<div class='sev-card'>";
        echo   "<div class='sev-card-head'>";
        echo     "<span class='sev-card-date'>".$it['date']."</span>";
        echo     "<span class='sev-card-teacher'><b>".htmlspecialchars($it['personne'])."</b><br>".htmlspecialchars($it['matiere'])."</span>";
        echo   "</div>";
        echo   "<div class='sev-card-body'>";
        if ($it['ponct'] != "") {
            echo "<div class='sev-aptitude'>"
               . "<div class='sev-apt-label'>Aptitude à manifester de l'intérêt pour son travail</div>"
               . "<div class='sev-apt-value'>".$it['ponct']."</div>"
               . "</div>";
        }
        if ($it['motiv'] != "") {
            echo "<div class='sev-aptitude'>"
               . "<div class='sev-apt-label'>Aptitude à la méthode et au soin</div>"
               . "<div class='sev-apt-value'>".$it['motiv']."</div>"
               . "</div>";
        }
        if ($it['dynam'] != "") {
            echo "<div class='sev-aptitude'>"
               . "<div class='sev-apt-label'>Aptitude à écouter</div>"
               . "<div class='sev-apt-value'>".$it['dynam']."</div>"
               . "</div>";
        }
        echo   "</div>";
        echo "</div>";
    }
    echo "</div>";
}
?>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY>
</HTML>

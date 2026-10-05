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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade - Plan de classe</title>
</head>
<body class="pcv-body" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");

if (($_SESSION["membre"] == "menuparent") && (PLANCLASSEPARENT == "non")) {
    print "<div class='pcv-denied'>".LANGMESS37."</div>";
} else {
    $cnx      = cnx();
    $idclasse = chercheIdClasseDunEleve($_SESSION["id_pers"]);

    $sql = "SELECT libelle, elev_id, nom, prenom
            FROM {$prefixe}eleves, {$prefixe}classes
            WHERE classe='$idclasse' AND code_class='$idclasse'
            ORDER BY nom";
    $res  = execSql($sql);
    $data = chargeMat($res);

    for ($i = 0; $i < countTriade($data); $i++) {
        $x = cherchePlanX($data[$i][1], $idclasse);
        $y = cherchePlanY($data[$i][1], $idclasse);
        if ($y == "-1") {
            $y = 45;
            $x = 40 * $i;
            if ($x == 0) { $x = 10; }
        }
        $elevId = $data[$i][1];
        $nom    = strtoupper($data[$i][2]);
        $prenom = ucwords(strtolower($data[$i][3]));
        $hidden = ((TROMBIELEVE == "non") && ($_SESSION["membre"] == "menueleve") && ($_SESSION["id_pers"] != $elevId))
               || ((TROMBIPARENT == "non") && ($_SESSION["membre"] == "menuparent") && ($_SESSION["id_pers"] != $elevId));
        $imgSrc = $hidden ? "./image/commun/photo_vide.jpg" : "image_trombi.php?idE=".$elevId;
    ?>
        <div id="E<?php print $elevId ?>" class="pcv-student" style="top:<?php print intval($y) ?>px;left:<?php print intval($x) ?>px">
            <img src="<?php print $imgSrc ?>" class="pcv-photo" alt="<?php print htmlspecialchars($nom) ?>">
            <div class="pcv-name"><?php print htmlspecialchars($nom) ?><br><?php print htmlspecialchars($prenom) ?></div>
        </div>
    <?php
    }

    $bx = cherchePlanX("-$idclasse", $idclasse);
    $by = cherchePlanY("-$idclasse", $idclasse);
    if ($by == "-1") { $by = 20; $bx = 600; }
    $idid = "B".$idclasse;
    ?>
    <div id="<?php print $idid ?>" class="pcv-bureau" style="top:<?php print intval($by) ?>px;left:<?php print intval($bx) ?>px">BUREAU</div>
    <?php
    Pgclose();
}
?>
</BODY>
</HTML>

<?php
session_start();
if (isset($_COOKIE["anneeScolaire"])) $anneeScolaire=$_COOKIE["anneeScolaire"];
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom'].' '.$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if (isset($_POST["create"])) {
    $ok=1;
    $nbT=$_POST["semestre"];
    $nbT = $nbT ? 3 : 4;
    $nb=1;
    $anneeScolaire=$_POST["annee_scolaire"];
    $idclasse=$_POST["saisie_classe"];
    if ($idclasse == "tous") {
        $idclasse=0;
        $sql="DELETE FROM {$prefixe}date_trimestrielle WHERE annee_scolaire='$anneeScolaire' ";
        execSql($sql);
        history_cmd($_SESSION["nom"],"SUPPRESSION","Suppression de toutes les dates trimestres pour l'année $anneeScolaire");
    }
    $idclasse=$_POST["saisie_classe"];
    if ((is_numeric($idclasse)) && ($anneeScolaire != "")) {
        $sql="DELETE FROM {$prefixe}date_trimestrielle WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
        execSql($sql);
        history_cmd($_SESSION["nom"],"SUPPRESSION","Suppression dates trimestres pour l'année $anneeScolaire de la classe $idclasse");
    }
    $sql="SELECT code_class FROM {$prefixe}classes WHERE offline='0'";
    $res=execSql($sql);
    $listeIdClasse=chargeMat($res);
    while ($nb < $nbT) {
        $valeur="trimestre".$nb;
        $debut="trimestre".$nb."_debut";
        $fin="trimestre".$nb."_fin";
        $date_form_debut=$_POST[$debut];
        $date_form_fin=$_POST[$fin];
        if ((trim($date_form_debut) != "") && (trim($date_form_fin) != "")) {
            $date_form_debut=dateFormBase($date_form_debut);
            $date_form_fin=dateFormBase($date_form_fin);
            if ($idclasse == "tous") {
                for ($O=0;$O<countTriade($listeIdClasse);$O++) {
                    $idclasse2=$listeIdClasse[$O][0];
                    $cr=def_trimestre($valeur,$date_form_debut,$date_form_fin,$idclasse2,$anneeScolaire);
                    history_cmd($_SESSION["nom"],"CREATION","Mise en place dates trimestres pour $idclasse2 en $anneeScolaire");
                }
            } else {
                $cr=def_trimestre($valeur,$date_form_debut,$date_form_fin,$idclasse,$anneeScolaire);
                history_cmd($_SESSION["nom"],"CREATION","Mise en place dates trimestres pour $idclasse en $anneeScolaire");
            }
            if ($cr != 1) { alertJs(LANGPARAM26); $ok=0; break; }
        }
        $nb=$nb+1;
    }
    if ($idclasse2 > 0) $idclasse=$idclasse2;
    $data=affDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
    if (countTriade($data)) { $date1_debut=dateNonForm($data[0][0]); $date1_fin=dateNonForm($data[0][1]); }
    $data=affDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
    if (countTriade($data)) { $date2_debut=dateNonForm($data[0][0]); $date2_fin=dateNonForm($data[0][1]); }
    $data=affDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
    if (countTriade($data)) { $date3_debut=dateNonForm($data[0][0]); $date3_fin=dateNonForm($data[0][1]); }
    if (($date1_debut < $date1_fin) && ($date1_fin < $date2_debut) && ($date2_debut < $date2_fin)) {
        // ok
    } else {
        $ok=0;
        alertJs(LANGPARAM26);
        $sql="DELETE FROM {$prefixe}date_trimestrielle WHERE idclasse='$idclasse' AND annee_scolaire='$anneeScolaire' ";
        execSql($sql);
        history_cmd($_SESSION["nom"],"SUPPRESSION","Suppression date trimestre $anneeScolaire de la classe $idclasse");
    }
    if ($ok) { alertJs(LANGPARAM27); }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPARAM17 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<!-- // fin  -->
<?php
if (isset($_GET['id'])) {
    $date11_debut=""; $date11_fin="";
    $date22_debut=""; $date22_fin="";
    $date33_debut=""; $date33_fin="";
    $anneeScolaire=$_GET["annee_scolaire"];
    $data=affDateTrimByIdclasse('trimestre1',$_GET['id'],$anneeScolaire);
    if (countTriade($data)) { $date11_debut=dateForm($data[0][0]); $date11_fin=dateForm($data[0][1]); }
    $data=affDateTrimByIdclasse('trimestre2',$_GET['id'],$anneeScolaire);
    if (countTriade($data)) { $date22_debut=dateForm($data[0][0]); $date22_fin=dateForm($data[0][1]); }
    $data=affDateTrimByIdclasse('trimestre3',$_GET['id'],$anneeScolaire);
    if (countTriade($data)) { $date33_debut=dateForm($data[0][0]); $date33_fin=dateForm($data[0][1]); }
    if (($date33_debut == "") && ($date11_debut != "") && ($date22_debut != "")) { $checked="checked='checked'"; }
}
if (isset($_GET["suppid"])) {
    $idsupp=$_GET["suppid"];
    $sql="DELETE FROM {$prefixe}date_trimestrielle WHERE idclasse='$idsupp'";
    execSql($sql);
}
?>

<div class="dt-stack">

<!-- Formulaire de saisie -->
<div class="dt-card">
    <div class="dt-card-title"><span class="dt-card-icon">&#9999;</span> <?php print LANGPARAM17 ?></div>

    <form name="formulaire" method="post" action="definir_trimestre.php">

    <div class="dt-filters">
        <span class="dt-filter-label"><?php print LANGPROFG ?> :</span>
        <select name="saisie_classe" class="dt-select">
            <?php if ((isset($_GET["id"])) && ($_GET["id"] > 0)): ?>
            <option id='select1' value='<?php print $_GET["id"] ?>'><?php print chercheClasse_nom($_GET["id"]) ?></option>
            <?php endif; ?>
            <option id='select0' value='tous'><?php print LANGMESS147 ?></option>
            <?php select_classe(); ?>
        </select>

        <span class="dt-filter-label"><?php print "Année scolaire" ?> :</span>
        <select name="annee_scolaire" class="dt-select">
            <option id='select0' value='inconnu'><?php print LANGCHOIX ?></option>
            <?php filtreAnneeScolaireSelect($anneeScolaire); ?>
        </select>
    </div>

    <table class="dt-table">
        <thead>
            <tr>
                <th><?php print LANGPARAM18 ?></th>
                <th><?php print LANGPARAM19 ?></th>
                <th><?php print LANGPARAM20 ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="dt-trim-name"><?php print LANGPARAM21 ?></span></td>
                <td>
                    <input type="text" name="trimestre1_debut" value="<?php print $date11_debut ?>" class="dt-date-input" readonly>
                    <?php include_once("librairie_php/calendar.php"); calendarDim('id1','document.formulaire.trimestre1_debut',$_SESSION["langue"],"0","0"); ?>
                </td>
                <td>
                    <input type="text" name="trimestre1_fin" value="<?php print $date11_fin ?>" class="dt-date-input" readonly>
                    <?php include_once("librairie_php/calendar.php"); calendarDim('id2','document.formulaire.trimestre1_fin',$_SESSION["langue"],"0","0"); ?>
                </td>
            </tr>
            <tr>
                <td><span class="dt-trim-name"><?php print LANGPARAM22 ?></span></td>
                <td>
                    <input type="text" name="trimestre2_debut" value="<?php print $date22_debut ?>" class="dt-date-input" readonly>
                    <?php include_once("librairie_php/calendar.php"); calendarDim('id3','document.formulaire.trimestre2_debut',$_SESSION["langue"],"0","0"); ?>
                </td>
                <td>
                    <input type="text" name="trimestre2_fin" value="<?php print $date22_fin ?>" class="dt-date-input" readonly>
                    <?php include_once("librairie_php/calendar.php"); calendarDim('id4','document.formulaire.trimestre2_fin',$_SESSION["langue"],"0","0"); ?>
                </td>
            </tr>
            <tr>
                <td><span class="dt-trim-name"><?php print LANGPARAM23 ?> *</span></td>
                <td>
                    <input type="text" name="trimestre3_debut" value="<?php print $date33_debut ?>" class="dt-date-input" readonly>
                    <?php include_once("librairie_php/calendar.php"); calendarDim('id5','document.formulaire.trimestre3_debut',$_SESSION["langue"],"0","0"); ?>
                </td>
                <td>
                    <input type="text" name="trimestre3_fin" value="<?php print $date33_fin ?>" class="dt-date-input" readonly>
                    <?php include_once("librairie_php/calendar.php"); calendarDim('id6','document.formulaire.trimestre3_fin',$_SESSION["langue"],"0","0"); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="dt-checkbox-row">
        <input type="checkbox" name="semestre" value="1" class="btradio1" <?php print $checked ?>>
        <span><?php print LANGMESS146 ?></span>
    </div>

    <?php
    $sql="SELECT code_class FROM {$prefixe}classes WHERE offline='0'";
    $res=execSql($sql);
    $listeIdClasse=chargeMat($res);
    if (countTriade($listeIdClasse) > 0) {
        print "<script language=JavaScript>buttonMagicSubmit(\"".LANGPARAM24."\",'create');</script>";
    } else {
        print "<a href='creat_classe.php' style='text-decoration:none'><span style='color:#c62828;font-size:12px;font-weight:600'>Vous devez avoir des classes enregistrées !</span></a>";
    }
    ?>
    <div class="dt-note">* <?php print LANGPARAM25 ?></div>

    </form>
</div>

</div><!-- /.dt-stack -->

<!-- // fin  -->
</td></tr></table>

<!-- Visualisation -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-top:12px">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS148 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<form method="post" action="definir_trimestre.php">
<div style="padding:10px 8px 6px">
    <span style="font-size:12px;font-weight:600;color:#333">Année scolaire :</span>
    <select name="annee_scolaire" class="dt-select" style="margin-left:8px" onchange="this.form.submit()">
        <option id='select0' value=''><?php print LANGCHOIX ?></option>
        <?php
        if (isset($_POST["annee_scolaire"])) $anneeScolaire=$_POST["annee_scolaire"];
        filtreAnneeScolaireSelect($anneeScolaire);
        ?>
    </select>
</div>
</form>

<div style="padding:6px 8px 12px">
<?php
$idclasse=0;
$data=recupDateTrimIdclasse($idclasse,$anneeScolaire);
$dateDebutT1=""; $dateFinT1="";
$dateDebutT2=""; $dateFinT2="";
$dateDebutT3=""; $dateFinT3="";
for ($i=0;$i<countTriade($data);$i++) {
    $trim=$data[$i][2];
    if ($trim == "trimestre1") { $dateDebutT1=$data[$i][0]; $dateFinT1=$data[$i][1]; }
    if ($trim == "trimestre2") { $dateDebutT2=trim($data[$i][0]); $dateFinT2=trim($data[$i][1]); }
    if ($trim == "trimestre3") { $dateDebutT3=trim($data[$i][0]); $dateFinT3=$data[$i][1]; }
    if ($dateDebutT1 != "") $dateDebutT1=dateForm($dateDebutT1);
    if ($dateFinT1   != "") $dateFinT1=dateForm($dateFinT1);
    if ($dateDebutT2 != "") $dateDebutT2=dateForm($dateDebutT2);
    if ($dateFinT2   != "") $dateFinT2=dateForm($dateFinT2);
    if ($dateDebutT3 != "") $dateDebutT3=dateForm($dateDebutT3);
    if ($dateFinT3   != "") $dateFinT3=dateForm($dateFinT3);
    if ($idclasse == 0) $nomClasse="Toutes les classes";
}
if ($nomClasse != "") {
    print "<div class='dt-class-item'>";
    print "<div class='dt-class-header'>";
    print "<span class='dt-class-name'><span class='dt-class-dot'></span>".LANGBULL33." : $nomClasse</span>";
    print "<div class='dt-class-actions'>";
    print "<a href='definir_trimestre.php?id=0&annee_scolaire=$anneeScolaire' class='dt-link'>".LANGMESS149."</a>";
    print "<a href='definir_trimestre.php?suppid=0' class='dt-link dt-link-del'>".LANGMESS150."</a>";
    print "</div></div>";
    print "<div class='dt-trims'>";
    print "<span class='dt-trim-badge'>".LANGMESS157." 1 : <b>$dateDebutT1 — $dateFinT1</b></span>";
    print "<span class='dt-trim-badge'>".LANGMESS157." 2 : <b>$dateDebutT2 — $dateFinT2</b></span>";
    print "<span class='dt-trim-badge'>".LANGMESS157." 3 : <b>$dateDebutT3 — $dateFinT3</b></span>";
    print "</div></div>";
}

$dataC=affClasse();
if (countTriade($dataC)) {
    for ($j=0;$j<countTriade($dataC);$j++) {
        $idclasse=$dataC[$j][0];
        $nomClasse=ucwords($dataC[$j][1]);
        $data=recupDateTrimIdclasse($idclasse,$anneeScolaire);
        $dateDebutT1=""; $dateFinT1="";
        $dateDebutT2=""; $dateFinT2="";
        $dateDebutT3=""; $dateFinT3="";
        for ($i=0;$i<countTriade($data);$i++) {
            $trim=$data[$i][2];
            if ($trim == "trimestre1") { $dateDebutT1=$data[$i][0]; $dateFinT1=$data[$i][1]; }
            if ($trim == "trimestre2") { $dateDebutT2=$data[$i][0]; $dateFinT2=$data[$i][1]; }
            if ($trim == "trimestre3") { $dateDebutT3=$data[$i][0]; $dateFinT3=$data[$i][1]; }
            if ($dateDebutT1 != "") $dateDebutT1=dateForm($dateDebutT1);
            if ($dateFinT1   != "") $dateFinT1=dateForm($dateFinT1);
            if ($dateDebutT2 != "") $dateDebutT2=dateForm($dateDebutT2);
            if ($dateFinT2   != "") $dateFinT2=dateForm($dateFinT2);
            if ($dateDebutT3 != "") $dateDebutT3=dateForm($dateDebutT3);
            if ($dateFinT3   != "") $dateFinT3=dateForm($dateFinT3);
        }
        if (trim($dateDebutT1) == "") continue;
        if (trim($nomClasse)   == "") continue;
        print "<div class='dt-class-item'>";
        print "<div class='dt-class-header'>";
        print "<span class='dt-class-name'><span class='dt-class-dot'></span>".LANGASS17." : $nomClasse</span>";
        print "<div class='dt-class-actions'>";
        print "<a href='definir_trimestre.php?id=$idclasse&annee_scolaire=$anneeScolaire' class='dt-link'>".LANGPER30."</a>";
        print "<a href='definir_trimestre.php?suppid=$idclasse' class='dt-link dt-link-del'>".LANGacce21."</a>";
        print "</div></div>";
        print "<div class='dt-trims'>";
        print "<span class='dt-trim-badge'>".LANGMESS157." 1 : <b>$dateDebutT1 — $dateFinT1</b></span>";
        print "<span class='dt-trim-badge'>".LANGMESS157." 2 : <b>$dateDebutT2 — $dateFinT2</b></span>";
        print "<span class='dt-trim-badge'>".LANGMESS157." 3 : <b>$dateDebutT3 — $dateFinT3</b></span>";
        print "</div></div>";
    }
}
?>
</div>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY></HTML>

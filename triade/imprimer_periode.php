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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onUnload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if (isset($_GET["sClasseGrp"])) {
    $idclasse=$_GET["sClasseGrp"];
    if ($_SESSION["membre"] == "menuprof") {
        verif_profp_class($_SESSION["id_pers"],$idclasse);
    }else{
        validerequete("2");
    }
}else{
    validerequete("2");
}
?>

<div style="max-width:720px;margin:14px auto">
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-printer"></i> <?php print LANGBULL7 ?>
</div>
<div class="card-body">
<form name="formulaire" method="post" action="imprimer_periode2.php">

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar-event" style="color:#080A66"></i> <?php print LANGBULL8 ?></label>
    <div style="display:flex;align-items:center;gap:6px">
        <input type="text" value="" name="saisie_date_debut" size="13" class="cc-select" style="width:110px" onKeyPress="onlyChar(event)">
        <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0"); ?>
    </div>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar-event" style="color:#080A66"></i> <?php print LANGBULL9 ?></label>
    <div style="display:flex;align-items:center;gap:6px">
        <input type="text" value="" name="saisie_date_fin" size="13" class="cc-select" style="width:110px" onKeyPress="onlyChar(event)">
        <?php include_once("librairie_php/calendar.php"); calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0"); ?>
    </div>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-printer" style="color:#080A66"></i> Type d'impression</label>
    <select name="type_periode" class="cc-select">
        <option value="0">Défaut</option>
        <option value="1">Bonifacio</option>
        <option value="2">Lycée Chicago</option>
        <option value="3">Cours Renaissance</option>
        <option value="4">Mont Lyonnais</option>
    </select>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGBULL10 ?></label>
    <select name="nom_periode" class="cc-select">
        <option value="0"><?php print LANGCHOIX ?></option>
        <option value="periode1"><?php print LANG1ER ?></option>
        <option value="periode2"><?php print LANG2EME ?></option>
        <option value="periode3"><?php print LANG3EME ?></option>
        <option value="periode4"><?php print LANG4EME ?></option>
        <option value="periode5"><?php print LANG5EME ?></option>
        <option value="periode6"><?php print LANG6EME ?></option>
        <option value="periode7"><?php print LANG7EME ?></option>
        <option value="periode8"><?php print LANG8EME ?></option>
        <option value="periode9"><?php print LANG9EME ?></option>
    </select>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGBULL11 ?></label>
    <?php if (isset($_GET["sClasseGrp"])) {
        $nomClasse=chercheClasse_nom($_GET["sClasseGrp"]);
    ?>
        <input type=hidden name='saisie_classe' value="<?php print $_GET["sClasseGrp"] ?>">
        <span style="font-size:12px;font-weight:700;color:#080A66;font-family:'Trebuchet MS',Arial"><?php print trunchaine($nomClasse,30) ?></span>
    <?php }else{ ?>
        <select name="saisie_classe" class="cc-select">
            <option value="0"><?php print LANGCHOIX ?></option>
            <?php select_classe2(20); Pgclose(); ?>
        </select>
    <?php } ?>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-text-height" style="color:#080A66"></i> Hauteur des matières</label>
    <select name="hauteur" class="cc-select">
        <option value="7">07</option>
        <option value="7.5">7.5</option>
        <option value="8">08</option>
        <option value="9">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
    </select>
</div>

<div class="toolbar" style="justify-content:center;margin-top:10px;gap:8px">
    <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBULL12 ?>","rien","onclick='attente()'");</script>
    <?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?>
</div>

</form>
</div>
</div>
</div>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else:
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
?>
<?php @nettoyage_repertoire("./data/pdf_bull"); ?>
</BODY></HTML>

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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade Editer Bulletin</title>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
$cnx=cnx();

$valeur=aff_Trimestre();
if (countTriade($valeur)) {
        $disabled="";
        $alert="";
}else{
        $disabled="disabled=disabled";
        $alert=LANGMESS10."<br>".LANGMESS11."<br>".LANGMESS12;
}
?>
<?php if ($alert): ?>
<script>document.addEventListener('DOMContentLoaded',function(){alertify.error(<?php echo json_encode(strip_tags($alert)); ?>);});</script>
<?php endif; ?>

<div style="max-width:560px;margin:18px auto;display:flex;flex-direction:column;gap:12px">

<!-- ── Carte 1 : Éditer commentaires ──────────────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-pencil-square"></i> <?php print LANGPROFP35 ?>.
</div>
<div class="card-body">
<form method=post action="editer_bulletin02.php" onsubmit="return valide_choix_projo()" name="formulaire">

<?php if ($_SESSION["membre"] != "menuprof") { ?>
<div class="form-row">
    <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGPROJ1 ?></label>
    <select name="saisie_classe" class="cc-select">
        <option><?php print LANGCHOIX ?></option>
        <?php select_classe(); ?>
    </select>
</div>
<?php }else{ print "<input type='hidden' name='saisie_classe' value='".$_GET["sClasseGrp"]."' >"; } ?>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGPROJ2 ?></label>
    <select name="saisie_trimestre" class="cc-select">
        <option value='0'><?php print LANGCHOIX ?></option>
        <option value="trimestre1"><?php print LANGPROJ3.' '.LANGOU.' '.LANGPROJ19 ?></option>
        <option value="trimestre2"><?php print LANGPROJ4.' '.LANGOU.' '.LANGPROJ20 ?></option>
        <option value="trimestre3"><?php print LANGPROJ5 ?></option>
    </select>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar-range" style="color:#080A66"></i> <?php print LANGBULL3 ?></label>
    <select name='anneeScolaire' class="cc-select">
        <?php $anneeScolaire=$_COOKIE["anneeScolaire"]; filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
</div>

<?php if (COMBULTINTYPE == "oui") { ?>
<div class="form-row">
    <label class="cc-label"><i class="bi bi-chat-left-text" style="color:#080A66"></i> Choix du commentaire</label>
    <select name="typecom" class="cc-select">
        <optgroup label="Standard">
            <option value="0">Appréciations, Conseils pour progresser.</option>
            <option value="5">Elèments du programme travaillés.</option>
        </optgroup>
        <optgroup label="Spécif.">
            <option value="1">Points d'appui. Progrès. Efforts</option>
            <option value="2">Ecarts par rapport aux objectifs attendu</option>
            <option value="3">Conseils pour progresser</option>
        </optgroup>
        <optgroup label="Examen">
            <option value="4">Partiel Blanc</option>
        </optgroup>
    </select>
</div>
<?php } ?>

<div class="toolbar" style="justify-content:center;margin-top:10px">
    <script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
</div>

</form>
</div>
</div>

<!-- ── Carte 2 : Liste des commentaires ──────────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-list-ul"></i> Listes des commentaires des bulletins effectués.
</div>
<div class="card-body">
<?php if (isset($_GET["info"])) { ?>
<div class="alert alert-success" style="margin-bottom:10px"><i class="bi bi-check-circle"></i> <?php print LANGPROJ6 ?></div>
<?php } ?>
<form method=post action="liste_bulletin_com.php" onsubmit="return valide_choix_projo2()" name="formulaire2">

<?php if ($_SESSION["membre"] != "menuprof") { ?>
<div class="form-row">
    <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGPROJ1 ?></label>
    <select name="saisie_classe" class="cc-select">
        <option><?php print LANGCHOIX ?></option>
        <?php include_once('librairie_php/db_triade.php'); $cnx=cnx(); select_classe(); ?>
    </select>
</div>
<?php }else{ print "<input type='hidden' name='saisie_classe' value='".$_GET["sClasseGrp"]."' >"; } ?>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGPROJ2 ?></label>
    <select name="saisie_trimestre" class="cc-select">
        <option value=0><?php print LANGCHOIX ?></option>
        <option value="trimestre1"><?php print LANGPROJ3.' '.LANGOU.' '.LANGPROJ19 ?></option>
        <option value="trimestre2"><?php print LANGPROJ4.' '.LANGOU.' '.LANGPROJ20 ?></option>
        <option value="trimestre3"><?php print LANGPROJ5 ?></option>
    </select>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar-range" style="color:#080A66"></i> <?php print LANGBULL3 ?></label>
    <select name='anneeScolaire' class="cc-select">
        <?php $anneeScolaire=$_COOKIE["anneeScolaire"]; filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
</div>

<div class="toolbar" style="justify-content:center;margin-top:10px">
    <script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
</div>

</form>
</div>
</div>

<!-- ── Carte 3 : Commentaires par matière ───────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-book"></i> Consulter les commentaires de la classe par matière.
</div>
<div class="card-body">
<form method="post" onsubmit="return verifAccesNote5()" name="formulaire5" action="bulletin_comm_classe22.php">

<div class="form-row">
    <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGPROJ1 ?></label>
    <select name="sClasseGrp" class="cc-select" onChange="upSelectMat(this)">
        <option value="0"><?php print LANGCHOIX3 ?></option>
        <?php select_classe(); ?>
    </select>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGPROJ2 ?></label>
    <?php
    $choix_tri=recherche_trimestre_en_cours_via_classe($cid);
    $choix_tri_text=$choix_tri;
    if ($choix_tri_text == "trimestre1") $choix_tri_text=LANGPROJ3. " ou ".LANGPROJ19;
    if ($choix_tri_text == "trimestre2") $choix_tri_text=LANGPROJ4. " ou ".LANGPROJ20;
    if ($choix_tri_text == "trimestre3") $choix_tri_text=LANGPROJ5;
    ?>
    <select name="choix_trimestre" class="cc-select">
        <option value='0'><?php print LANGCHOIX ?></option>
        <option value='trimestre1'><?php print LANGPROJ3." ou ".LANGPROJ19 ?></option>
        <option value='trimestre2'><?php print LANGPROJ4." ou ".LANGPROJ20 ?></option>
        <option value='trimestre3'><?php print LANGPROJ5 ?></option>
    </select>
</div>

<div class="form-row">
    <label class="cc-label"><i class="bi bi-calendar-range" style="color:#080A66"></i> <?php print LANGBULL3 ?></label>
    <select name='anneeScolaire' class="cc-select">
        <?php filtreAnneeScolaireSelectNote($_COOKIE["anneeScolaire"],3); ?>
    </select>
</div>

<div class="toolbar" style="justify-content:center;margin-top:10px">
    <script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","rien","<?php print $disabled?>");</script>
</div>

</form>
</div>
</div>

</div><!-- /wrapper -->

<?php Pgclose(); ?>
</body>
</html>

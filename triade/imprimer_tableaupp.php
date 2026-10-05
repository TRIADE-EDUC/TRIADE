<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["annee_scolaire"])) {
        $anneeScolaire=$_POST["annee_scolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/ajax-impr_periode.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.imp-lbl { min-width: 260px !important; }
</style>
</head>
<body  id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onUnload="attente_close()" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<!-- ═══════════════════════════════════════════════════════
     Section 1 : Tableau de points de période
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS357 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
if ($_SESSION["membre"] == "menupersonnel") {
    if (!verifDroit($_SESSION["id_pers"],"imprtableau")) {
        Pgclose();
        accesNonReserveFen();
        exit();
    }
} else {
    validerequete("3");
}
$erreurdeja=0;
$valeur=aff_Trimestre();
if (countTriade($valeur)) {
?>

<form name="formulaire" id="formulaire" method="post" action="./tableaupp.php" onSubmit="return valideTab();">
<div class="na-card">

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL2 ?> :</span>
        <select name="saisie_classe" class="cc-select" onChange='impr_periode(document.formulaire.saisie_classe.options[document.formulaire.saisie_classe.options.selectedIndex].value);'>
            <option selected value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <?php select_classe(); ?>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBASE40 ?> :</span>
        <select id="tt_f1" name="typetrisem" class="cc-select">
            <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <option value="trimestre" style='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28 ?></option>
            <option value="semestre"  style='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29 ?></option>
        </select>
        &nbsp;
        <select id="st_f1" name="saisie_trimestre" class="cc-select">
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
        </select>
        <script>(function(){var src=document.getElementById('tt_f1'),dst=document.getElementById('st_f1'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'        ';dst.options[i].value=o[i]?o[i][1]:'0';}dst.selectedIndex=0;};})()</script>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL3 ?> :</span>
        <select name="annee_scolaire" class="cc-select">
            <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGMESS358 ?> :</span>
        <label><input type="checkbox" name="affrang" id="affrang" value="1" onclick="changementform2()"> (<i><?php print LANGOUI ?></i>)</label>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGMESS360 ?> :</span>
        <span>
            <label><input type="checkbox" id="unite_enseig" name="unite_enseig" value="1" onclick="changementform()"> (<i><?php print LANGOUI ?></i>)</label>
            &nbsp;
            <span class="htip-wrap">
                <img src="./image/help.gif" border=0 align=center>
                <span class="htip"><?php print "Le regroupement est effectué via le module `Unités enseignmts` de la rubrique affectation." ?></span>
            </span>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGMESS361 ?> :</span>
        <label><input type="checkbox" name="affmatiere" id="affmatiere" value="non"> (<i><?php print LANGNON ?></i>)</label>
    </div>

    <?php if ((VATEL != "1") || (!defined("VATEL"))) { ?>
    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGTMESS466 ?> :</span>
        <label><input type="checkbox" name="affsousmatiere" id="affsousmatiere" value="non"> (<i><?php print LANGNON ?></i>)</label>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGTMESS467 ?> :</span>
        <label><input type="checkbox" name="noteexamen" id="noteexamen" value="oui"> (<i><?php print LANGOUI ?></i>)</label>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print "Seulement les notes de type examen" ?> :</span>
        <span><?php include("typeexamen.php"); ?></span>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGTMESS468 ?> :</span>
        <span>
            <label><input type="checkbox" name="pointsupp" id="pointsupp" value="oui"> (<i><?php print LANGOUI ?></i>)</label>
            &nbsp;
            <span class="htip-wrap">
                <img src="./image/help.gif" border=0 align=center>
                <span class="htip"><?php print LANGTMESS469 ?></span>
            </span>
        </span>
    </div>
    <?php } ?>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print "Marge du haut de la page" ?> :</span>
        <span><?php include("typemargeduhaut.php"); ?></span>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print "Marge c&ocirc;t&eacute; gauche de la page" ?> :</span>
        <span><?php include("typemargegauche.php"); ?></span>
    </div>

</div>
<br>
<div class="na-foot">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43pp ?>","rien",""); //text,nomInput,action</script></span>
</div>
<br>
</form>

<script>
function changementform() {
    if (document.getElementById("unite_enseig").checked == true) {
        document.getElementById("affrang").checked = false;
        document.getElementById("formulaire").action = "tableaupp_vatel.php";
    } else {
        document.getElementById("formulaire").action = "tableaupp.php";
    }
}

function changementform2() {
    document.getElementById("unite_enseig").checked = false;
}
</script>

<?php
} else {
    if ($erreurdeja != 1) {
?>
<div style="padding:24px;text-align:center;color:#555;">
    <p><?php print LANGMESS10 ?></p>
    <p style="font-size:13px;"><?php print LANGMESS13 ?></p>
    <p style="font-size:13px;"><?php print LANGMESS12 ?></p>
</div>
<?php } } ?>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Section 2 : Relevé Spécifique
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "Relevé Spécifique" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<form name="formulaire7" id="formulaire7" method="post" action="./tableauspecif.php" onSubmit="return validTabSpe();">
<div class="na-card">

    <div class="na-row">
        <span class="na-lbl imp-lbl">Type du relevé :</span>
        <select name="type_releve" class="cc-select">
            <option value=''>Choix...</option>
            <option value='INTIME-UNIV'>INTIME-UNIV</option>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL2 ?> :</span>
        <select name="saisie_classe" class="cc-select" onChange='impr_periode(document.formulaire7.saisie_classe.options[document.formulaire7.saisie_classe.options.selectedIndex].value);'>
            <option selected value='0' style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <?php select_classe(); ?>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBASE40 ?> :</span>
        <select id="tt_f7" name="typetrisem" class="cc-select">
            <option value='0' style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <option value="trimestre" style='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28 ?></option>
            <option value="semestre"  style='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29 ?></option>
        </select>
        &nbsp;
        <select id="st_f7" name="saisie_trimestre" class="cc-select">
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
        </select>
        <script>(function(){var src=document.getElementById('tt_f7'),dst=document.getElementById('st_f7'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['','0']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'        ';dst.options[i].value=o[i]?o[i][1]:'0';}dst.selectedIndex=0;};})()</script>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL3 ?> :</span>
        <select name="annee_scolaire" class="cc-select">
            <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
        </select>
    </div>

</div>
<br>
<div class="na-foot">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43pp ?>","rien",""); //text,nomInput,action</script></span>
</div>
<br>
</form>
</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Section 3 : Tableau sur 2 années
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFP39 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<form method="post" action="tableaupp2an.php" name="formulairean" target="_blank" onSubmit="return valideTab2();">
<div class="na-card">

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL2 ?> :</span>
        <select name="saisie_classe" class="cc-select" onChange='impr_periode(document.formulairean.saisie_classe.options[document.formulairean.saisie_classe.options.selectedIndex].value);'>
            <option selected value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <?php select_classe(); ?>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGMESS374 ?> :</span>
        <select id="tt_fan" name="typetriseman" class="cc-select">
            <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <option value="trimestre" style='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM28 ?></option>
            <option value="semestre"  style='color:#000066;background-color:#CCCCFF'><?php print LANGPARAM29 ?></option>
        </select>
        &nbsp;
        <select id="st_fan" name="saisie_trimestre" class="cc-select">
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
            <option style='color:#000066;background-color:#CCCCFF'>        </option>
        </select>
        <script>(function(){var src=document.getElementById('tt_fan'),dst=document.getElementById('st_fan'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],[' ','trimestre3']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'        ';dst.options[i].value=o[i]?o[i][1]:'0';}dst.selectedIndex=0;};})()</script>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL3 ?> :</span>
        <select name="annee_scolaire" class="cc-select">
            <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGMESS358 ?> :</span>
        <label><input type="checkbox" name="affrang" value="1"> (<i><?php print LANGOUI ?></i>)</label>
    </div>

    <?php if ((VATEL != "1") || (!defined("VATEL"))) { ?>
    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGTMESS467 ?> :</span>
        <label><input type="checkbox" name="noteexamen" id="noteexamen" value="oui"> (<i><?php print LANGOUI ?></i>)</label>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print "Seulement les notes de type examen" ?> :</span>
        <span><?php include("typeexamen.php"); ?></span>
    </div>
    <?php } ?>

</div>
<br>
<div class="na-foot">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43pp ?>","rien","onclick='document.getElementById(\"attenteDiv\").style.visibility=\'visible\';'"); //text,nomInput,action</script></span>
</div>
<br>
</form>
</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Section 4 : Export Excel
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS362 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post">
<div class="na-card">
    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL3 ?> :</span>
        <select name="annee_scolaire" class="cc-select" onChange="this.form.submit()">
            <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
        </select>
    </div>
</div>
</form>

<div style="border-top:2px solid #c5cae9;margin:8px 0;"></div>

<form method="post" action="tableauppxls.php" name="formulaire3" onSubmit="return valideTab3();" target="_blank">
<input type="hidden" name="annee_scolaire" value="<?php print $anneeScolaire ?>">
<div class="na-card">

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGBULL2 ?> :</span>
        <select name="saisie_classe" class="cc-select" onChange='impr_periode(document.formulaire3.saisie_classe.options[document.formulaire3.saisie_classe.options.selectedIndex].value);'>
            <option selected value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
            <?php select_classe(); ?>
        </select>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGMESS358 ?> :</span>
        <label><input type="checkbox" name="affrang" value="1"> (<i><?php print LANGOUI ?></i>)</label>
    </div>

    <?php if ((VATEL != "1") || (!defined("VATEL"))) { ?>
    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print LANGTMESS467 ?> :</span>
        <label><input type="checkbox" name="noteexamen" id="noteexamen" value="oui"> (<i><?php print LANGOUI ?></i>)</label>
    </div>

    <div class="na-row">
        <span class="na-lbl imp-lbl"><?php print "Seulement les notes de type examen" ?> :</span>
        <span><?php include("typeexamen.php"); ?></span>
    </div>
    <?php } ?>

</div>
<br>
<div class="na-foot">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGMESS375 ?>","rien","onclick='document.getElementById(\"attenteDiv\").style.visibility=\'visible\';'"); //text,nomInput,action</script></span>
</div>
<br>
</form>

</td></tr></table>

<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif;
// deconnexion en fin de fichier
attente();
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>

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
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css">
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
if (isset($_GET["err"])) {
    echo '<script>document.addEventListener("DOMContentLoaded",function(){alertify.error("Merci d\'indiquer le choix du bulletin !");});</script>';
}

include_once('librairie_php/db_triade.php');
include_once("librairie_php/lib_bulletin.php");
$cnx = cnx();

if (!is_dir("./data/archive/bulletin")) mkdir("./data/archive/bulletin");

if (isset($_GET["sClasseGrp"])) {
    $data = aff_enr_parametrage("autorisebulletinprof");
    if ($data[0][1] == "oui") {
        if ($_SESSION["membre"] == "menuprof") {
            $tabClasse = affClasseAffectationProf($_SESSION["id_pers"]);
        }
    } else {
        $idclasse = $_GET["sClasseGrp"];
        verif_profp_class($_SESSION["id_pers"], $idclasse);
    }
} else {
    if ($_SESSION["membre"] == "menuprof") {
        $tabClasse = affClasseAffectationProf($_SESSION["id_pers"]);
    }
}

if (isset($_POST["saisie_classe"])) { $idclasse = $_POST["saisie_classe"]; }
if ($_SESSION["membre"] == "menueleve")    { validerequete("menuadmin"); }
if ($_SESSION["membre"] == "menuparent")   { validerequete("menuadmin"); }
if ($_SESSION["membre"] == "menupersonnel") {
    if (!verifDroit($_SESSION["id_pers"], "imprbulletin")) {
        Pgclose(); accesNonReserveFen(); exit();
    }
}
if ($_SESSION["membre"] == "menututeur")   { validerequete("menuadmin"); }
if ($_SESSION["membre"] == "menuadmin") {
    if (isset($_POST["param"])) {
        enrbulletinclasse($_POST["saisie_classe"], $_POST["enrbull"]);
        enr_parametrage("autorisebulletinprof", $_POST["autorisebulletinprof"]);
    }
}

$idsite    = chercheIdSite($idclasse);
$erreurdeja = 0;
$valeur    = aff_Trimestre();

if (countTriade($valeur)):
?>

<div style="max-width:680px;margin:8px auto;display:flex;flex-direction:column;gap:10px">

<!-- ── Carte principale : impression bulletin ─────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-printer"></i> <?php print LANGBULL1?>
</div>
<div class="card-body">

    <!-- Sélection classe (form séparé pour onChange submit) -->
    <form method="post" action="imprimer_trimestre.php">
    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGBULL2?></label>
        <?php if ((isset($idclasse)) && ($_SESSION["membre"] != "menuadmin")): ?>
            <b style="font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial"><?php print chercheClasse_nom($idclasse) ?></b>
            <input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>">
        <?php else: ?>
            <select name="saisie_classe" class="cc-select" onchange="this.form.submit()">
                <?php if (isset($idclasse)): ?>
                <option value='<?php print $idclasse ?>' id='select0'><?php print chercheClasse_nom($idclasse) ?></option>
                <?php endif; ?>
                <option value='0' id='select0'><?php print LANGCHOIX?></option>
                <?php
                if (isset($_GET["sClasseGrp"])) {
                    $deja = 0;
                    for ($i=0; $i<countTriade($tabClasse); $i++) {
                        if ($tabClasse[$i][1] == $_GET["sClasseGrp"]) { $deja=1; break; }
                    }
                    if ($deja == 0) print "<option value='".$_GET["sClasseGrp"]."' id='select1'>".chercheClasse_nom($_GET["sClasseGrp"])."</option>";
                }
                if (countTriade($tabClasse) > 0) {
                    for ($i=0; $i<countTriade($tabClasse); $i++) {
                        print "<option value='".$tabClasse[$i][1]."' id='select1'>".$tabClasse[$i][0]."</option>";
                    }
                } else {
                    select_classe();
                }
                print "</select>";
                ?>
        <?php endif; ?>
    </div>
    </form>

    <!-- Formulaire principal impression -->
    <form name="formulaire" method="post" action="./bulletin_construction0.php" onsubmit="return verifimpbull();">
    <input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>">

    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGBASE40?></label>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <select name="typetrisem" id="typetrisem" class="cc-select">
                <option value=0 id='select0'><?php print LANGCHOIX?></option>
                <option value="trimestre" id='select1'><?php print LANGPARAM28?></option>
                <option value="semestre"  id='select1'><?php print LANGPARAM29?></option>
                <option value="cycle"     id='select1'>Cycle</option>
                <option value="annuel"    id='select1'>Annuel</option>
            </select>
            <select name="saisie_trimestre" id="saisie_trimestre" class="cc-select">
                <option value="">&nbsp;</option>
                <option value="">&nbsp;</option>
                <option value="">&nbsp;</option>
                <option value="">&nbsp;</option>
            </select>
            <script>(function(){var src=document.getElementById('typetrisem'),dst=document.getElementById('saisie_trimestre'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3'],['','']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel'],['','']],cycle:[['Cycle 1','cycle1'],['Cycle 2','cycle2'],['Cycle 3','cycle3'],['Cycle 4','cycle4']],annuel:[['Annuel','annuel'],['','0'],['','0'],['','']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>

        </div>
    </div>

    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-calendar-range" style="color:#080A66"></i> <?php print LANGBULL3?></label>
        <select name="anneeScolaire" class="cc-select">
        <?php
        $anneeScolaire = $_COOKIE["anneeScolaire"];
        filtreAnneeScolaireSelectNote($anneeScolaire, 7);
        ?>
        </select>
    </div>

    <div class="form-row" style="margin-bottom:14px">
        <label class="cc-label"><i class="bi bi-file-earmark-text" style="color:#080A66"></i> <?php print LANGPARAM35?></label>
        <select name="typebull" class="cc-select">
            <option value=0 id='select0'><?php print LANGCHOIX?></option>
            <?php
            if (isset($_COOKIE["bulletinannee"])) {
                print "<option id='select1' value='".$_COOKIE["bulletinselection"]."' selected='selected'>".RecupBulletin(trim($_COOKIE["bulletinselection"]))."</option>";
            }
            $tab = array();
            if (file_exists("./common/config.bulletin.php")) {
                include_once("./common/config.bulletin.php");
                $liste = LISTEBULLETIN;
                $tab   = explode(",", $liste);
            }
            if (countTriade($tab) >= 1) {
                foreach ($tab as $key => $value) {
                    $libelle = RecupBulletin(trim($value));
                    print "<option id='select1' value='$value'>$libelle</option>";
                }
            } else {
                for ($i=0; $i<countTriade($tabClasse); $i++) {
                    $data = recupBulletinClasse($tabClasse[$i][1]);
                    if (countTriade($data) > 0) {
                        $libel = RecupBulletin($data[0][0]);
                        $tablibel[$libel] = $data[0][0];
                    }
                }
                if (!empty($tablibel)) {
                    foreach ($tablibel as $key => $value) {
                        print "<option value='$value' id='select1'>$key</option>";
                    }
                }
                if ($_SESSION["membre"] != "menuprof") { listingBulletin(); }
                listBulletinBlanc();
            }
            ?>
        </select>
    </div>

    <div id='bullperso'></div>

    <div class="toolbar" style="margin-top:4px;flex-wrap:wrap;gap:6px">
        <?php if ($_SESSION["membre"] == "menuadmin"): ?>
        <script language=JavaScript>buttonMagic("<?php print "Autorisation d'accès aux bulletins"?>","bulletin_param.php",'_self','','')</script>
        <?php endif; ?>
        <script language=JavaScript>buttonMagicSubmit3("<?php print LANGBT43?>","rien","");</script>
        <?php if (PREVISUBULLETIN == "oui"): ?>
        <script language=JavaScript>buttonMagicSubmit3("<?php print "Pré-visualiser le bulletin"?>","previsu","");</script>
        <?php endif; ?>
        <?php if (isset($_SESSION["profpclasse"])): ?>
        <script>buttonMagicRetour('profp2.php','_self')</script>
        <?php endif; ?>
        <?php if ($_SESSION["membre"] == "menuadmin"): ?>
        <script language=JavaScript>buttonMagic("<?php print LANGMESS386?>","https://support.triade-educ.org/support/bulletinPerso.php?type=bull",'_blank','','')</script>
        <?php endif; ?>
    </div>

    </form>
</div>
</div>

<?php if ($_SESSION["membre"] == "menuadmin"): ?>

<!-- ── Carte admin : affecter bulletin à une classe ──────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-gear"></i> <?php print LANGASS25?>
</div>
<div class="card-body">
<form method="post" action="imprimer_trimestre.php">

    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-file-earmark-text" style="color:#080A66"></i> <?php print LANGASS25?></label>
        <select name="enrbull" class="cc-select">
            <option value='0' id='select0'><?php print LANGCHOIX?></option>
            <?php
            if (file_exists("./common/config.bulletin.php")) {
                include_once("./common/config.bulletin.php");
                $liste = LISTEBULLETIN;
                $tab   = explode(",", $liste);
                foreach ($tab as $key => $value) {
                    $libelle = RecupBulletin(trim($value));
                    print "<option id='select1' value='$value'>$libelle</option>";
                }
            } else {
                listingBulletin();
                listBulletinBlanc();
            }
            ?>
        </select>
    </div>

    <div style="font-size:11px;color:#666;font-style:italic;margin-bottom:10px;font-family:Electrolize,'Trebuchet MS',Arial">
        <i class="bi bi-info-circle" style="color:#080A66"></i> <?php print LANGMESS387?>
    </div>

    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGMESS388?></label>
        <select name="saisie_classe" class="cc-select">
            <option selected value=0 id='select0'><?php print LANGAFF5?></option>
            <?php select_classe(); ?>
        </select>
    </div>

    <div class="form-row" style="margin-bottom:14px;align-items:center">
        <label class="cc-label"><i class="bi bi-person-check" style="color:#080A66"></i> <?php print LANGMESS389?></label>
        <?php
        $checked = "";
        $data = aff_enr_parametrage("autorisebulletinprof");
        if ((countTriade($data) > 0) && ($data[0][1] != "")) { $checked = "checked='checked'"; }
        ?>
        <div style="display:flex;align-items:center;gap:6px">
            <input type="checkbox" name="autorisebulletinprof" <?php print $checked?> value="oui"
                   style="width:15px;height:15px;cursor:pointer">
            <span style="font-size:11px;color:#555;font-family:Electrolize,'Trebuchet MS',Arial">(<?php print LANGOUI?>)</span>
        </div>
    </div>

    <div class="toolbar">
        <script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR?>","param","");</script>
    </div>
</form>
</div>
</div>

<!-- ── Carte admin : liste bulletins par classe ───────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-list-ul"></i> <?php print LANGASS17?> — <?php print LANGASS25?>
</div>
<div class="card-body" style="padding:0">
<?php
suppBulletinClasse($_GET["idsupp"]);
$dataliste = listeBulletinClasse();
if (countTriade($dataliste) > 0):
?>
<div style="overflow-x:auto">
<table class="se-listing-table" style="width:100%;border-collapse:collapse;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial">
    <thead>
    <tr>
        <th class="cc-th"><?php print LANGASS17?></th>
        <th class="cc-th"><?php print LANGASS25?></th>
        <th class="cc-th" style="width:5%;text-align:center"><?php print LANGASS7?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $i<countTriade($dataliste); $i++) {
        $idclasseL = $dataliste[$i][0];
        $idbull    = $dataliste[$i][1];
        $libel     = RecupBulletin($idbull);
        print "<tr class='cc-tr-data'>";
        print "<td style='padding:7px 10px'>&nbsp;".chercheClasse_nom($idclasseL)."</td>";
        print "<td style='padding:7px 10px'>&nbsp;".$libel."</td>";
        print "<td style='padding:7px 10px;text-align:center'><a href='imprimer_trimestre.php?idsupp=$idclasseL' title='Supprimer'><i class='bi bi-trash' style='color:#c62828;font-size:14px'></i></a></td>";
        print "</tr>";
    }
    ?>
    </tbody>
</table>
</div>
<?php else: ?>
<div style="padding:14px;text-align:center;font-size:12px;color:#888;font-style:italic">Aucune affectation enregistrée.</div>
<?php endif; ?>
</div>
</div>

<?php endif; ?>

</div><!-- /max-width wrapper -->

<?php
else:
    if ($erreurdeja != 1):
?>
<div style="margin:16px 8px">
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    <?php print LANGMESS10?>
    <?php if ($_SESSION["membre"] == "menuadmin"): ?>
    <br><br>
    <span style="font-size:12px"><?php print LANGMESS13?><br><br><?php print LANGMESS12?></span>
    <?php endif; ?>
</div>
</div>
<?php
    endif;
endif;
?>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else:
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;

@nettoyage_repertoire("./data/pdf_bull");
Pgclose();
?>
</BODY></HTML>

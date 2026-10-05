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
include_once('librairie_php/db_triade.php');
include_once("librairie_php/lib_bulletin.php");
validerequete("menuadmin");
$cnx = cnx();

$anneeScolaire = $_COOKIE["anneeScolaire"];

if (isset($_GET["idsupp"])) {
    $anneeScolaire = $_GET["anneeScolaire"];
    $id = $_GET["idsupp"];
    suppBulletinElPar($id);
}

if (isset($_POST["create3"])) {
    $tri          = $_POST["saisie_trimestre"];
    $anneeScolaire = $_POST["anneeScolaire"];
    $idclasse     = $_POST["saisie_classe"];
    accesBulletinElPar($tri, $anneeScolaire, $idclasse);
}

if ($anneeScolaire == "") $anneeScolaire = anneeScolaireViaIdClasse($idClasse);
if (isset($_POST["anneeScolaire"])) $anneeScolaire = $_POST["anneeScolaire"];
?>

<div style="max-width:680px;margin:8px auto;display:flex;flex-direction:column;gap:10px">

<!-- ── Carte : accorder un accès ─────────────────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-shield-check"></i> Autorisation accès bulletin — Parents, &Eacute;l&egrave;ves, Tuteurs
</div>
<div class="card-body">
<form name="formulaire" method="post" action="./bulletin_param.php">

    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-calendar3" style="color:#080A66"></i> <?php print LANGBASE40?></label>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <select id="tt_bp" name="typetrisem" class="cc-select">
                <option value=0 id='select0'><?php print LANGCHOIX?></option>
                <option value="trimestre" id='select1'><?php print LANGPARAM28?></option>
                <option value="semestre"  id='select1'><?php print LANGPARAM29?></option>
            </select>
            <select id="st_bp" name="saisie_trimestre" class="cc-select">
                <option id='select1'>&nbsp;</option>
                <option id='select1'>&nbsp;</option>
                <option id='select1'>&nbsp;</option>
            </select>
            <script>(function(){var src=document.getElementById('tt_bp'),dst=document.getElementById('st_bp'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>
        </div>
    </div>

    <div class="form-row" style="margin-bottom:10px">
        <label class="cc-label"><i class="bi bi-calendar-range" style="color:#080A66"></i> <?php print LANGBULL3?></label>
        <select name="anneeScolaire" class="cc-select">
        <?php filtreAnneeScolaireSelectNote($anneeScolaire, 3); ?>
        </select>
    </div>

    <div class="form-row" style="margin-bottom:14px">
        <label class="cc-label"><i class="bi bi-people" style="color:#080A66"></i> <?php print LANGBULL2?></label>
        <select name="saisie_classe" class="cc-select">
            <option value='0' id='select0'><?php print LANGCHOIX?></option>
            <?php select_classe(); ?>
        </select>
    </div>

    <div class="toolbar" style="flex-wrap:wrap;gap:6px">
        <script language=JavaScript>buttonMagicSubmit3("<?php print VALIDER?>","create3","");</script>
        <script language=JavaScript>buttonMagicRetour2("imprimer_trimestre.php","_parent","<?php print LANGCIRCU14?>");</script>
    </div>

</form>
</div>
</div>

<!-- ── Carte : liste des accès accordés ──────────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <span><i class="bi bi-list-ul"></i> Acc&egrave;s accord&eacute;s</span>
    <div style="margin-left:auto">
        <form method="post" action="./bulletin_param.php" style="display:inline">
            <select name="anneeScolaire" class="cc-select" style="font-size:11px;padding:3px 6px" onchange="this.form.submit();">
                <?php filtreAnneeScolaireSelectNote($anneeScolaire, 7); ?>
            </select>
        </form>
    </div>
</div>
<div class="card-body" style="padding:0">
<?php
$listing = affichAccesBulletinElPar($anneeScolaire);
if (countTriade($listing) > 0):
?>
<div style="overflow-x:auto">
<table style="width:100%;border-collapse:collapse;font-size:12px;font-family:Electrolize,'Trebuchet MS',Arial">
    <thead>
    <tr>
        <th class="cc-th"><?php print LANGBULL2?></th>
        <th class="cc-th"><?php print LANGBASE40?></th>
        <th class="cc-th" style="width:80px;text-align:center">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $i<countTriade($listing); $i++): ?>
    <tr class="cc-tr-data">
        <td style="padding:7px 10px"><?php print chercheClasse_nom($listing[$i][1]); ?></td>
        <td style="padding:7px 10px"><?php print $listing[$i][2]; ?></td>
        <td style="padding:7px 10px;text-align:center">
            <a href="bulletin_param.php?idsupp=<?php print $listing[$i][0]?>&anneeScolaire=<?php print urlencode($anneeScolaire)?>"
               onclick="return confirm('Supprimer cet accès ?')"
               title="Supprimer">
                <i class="bi bi-trash" style="color:#c62828;font-size:14px"></i>
            </a>
        </td>
    </tr>
    <?php endfor; ?>
    </tbody>
</table>
</div>
<?php else: ?>
<div style="padding:14px;text-align:center;font-size:12px;color:#888;font-style:italic">
    Aucun acc&egrave;s accord&eacute; pour cette ann&eacute;e scolaire.
</div>
<?php endif; ?>
</div>
</div>

</div><!-- /wrapper -->

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else:
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>

<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/style.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
<script language="JavaScript" src="./librairie_js/brevet.js"></script>
<script>var panelWidth = 40;</script>
<script language="JavaScript" src="./librairie_js/lib_aide.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Config. matières Brevet</title>
<style>
#dhtmlgoodies_leftPanel {
    background-color:#CCCCCC; color:#000000;
    height:100%; left:0px; z-index:10;
    position:absolute; display:none; padding:0px;
}
body { font-family:Electrolize,'Trebuchet MS',Arial,sans-serif; font-size:12px; background:#f5f7ff; margin:0; }
.brv-header {
    background:linear-gradient(135deg,#080A66 0%,#1a1c8a 100%);
    color:#fff; padding:7px 14px; font-size:13px; font-weight:700;
    display:flex; align-items:center; gap:8px;
    position:sticky; top:0; z-index:20;
}
.brv-header i { color:#CACCEF; }
.brv-toolbar {
    background:#fff; border-bottom:1px solid #e8eaf6;
    padding:8px 14px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;
}
.brv-info {
    background:#f0f2fa; border-left:3px solid #080A66;
    padding:7px 12px; margin:10px 14px 6px; border-radius:4px;
    font-size:11px; color:#333; line-height:1.7;
}
.brv-content { padding:0 14px 14px; }
</style>
</head>
<body>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

if (isset($_POST["saisie_classe"])) { $idclasse=$_POST["saisie_classe"]; }
if (isset($_GET["saisie_classe"]))  { $idclasse=$_GET["saisie_classe"]; }

if (isset($_POST["donnee"])) {
    $donnee=$_POST["donnee"];
    $tab=explode(";",$donnee);
    enrConfigBrevet($tab,$_POST["idclasse"]);
    echo "<script>document.addEventListener('DOMContentLoaded',function(){alertify.success('".addslashes(LANGDONENR)."');});</script>";
    $idclasse=$_POST["idclasse"];
}
?>

<div id="dhtmlgoodies_leftPanel">
  <img src="image/commun/info2.gif" align=left>
  <font class=T1><b><?php print LANGAIDE1 ?></b><br><br></font>
</div>

<div class="brv-header">
  <i class="bi bi-mortarboard-fill"></i> Config. matières Brevet
  <?php $nomclasse=chercheClasse_nom($idclasse); if($nomclasse) echo "<span style='font-size:11px;color:#CACCEF;font-weight:400;margin-left:6px'>— ".htmlspecialchars($nomclasse)."</span>"; ?>
</div>

<div class="brv-toolbar">
  <form method='post' name='formulaire0'>
    <input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>">
    <script language=JavaScript>buttonMagicRetour2('gestion_examen_brevet_config_classe2.php?saisie_classe=<?php print $idclasse ?>','_self','<?php print LANGBT25 ?>')</script>
    <script language=JavaScript>buttonMagic3("<?php print LANGAIDE ?>","initSlideLeftPanel();return false");</script>
    <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:#333;margin-left:4px;cursor:pointer">
      <input type="checkbox" name='affMatiereTous' value='1' <?php if ($_POST["affMatiereTous"] == 1) print "checked=checked" ?> onclick="document.formulaire0.submit()" style="accent-color:#080A66">
      Afficher toutes les matières (colonne de gauche)
    </label>
  </form>
</div>

<div class="brv-info">
  <i class="bi bi-info-circle" style="color:#080A66;margin-right:4px"></i>
  1* La langue LV1 doit être précisée sur la fiche élève. &nbsp;·&nbsp;
  2* La langue LV2 doit être précisée sur la fiche élève. &nbsp;·&nbsp;
  3* L'option doit être précisée sur la fiche élève.<br>
  <i class="bi bi-arrow-left-right" style="color:#080A66;margin-right:4px"></i>
  Modifier le coef. d'une matière en cliquant sur son titre.
</div>

<?php $data=affMatiereAffectation($idclasse); ?>

<div class="brv-content">
<div id="dhtmlgoodies_dragDropContainer" style="width:100%">

    <div id="dhtmlgoodies_listOfItems">
        <div><p>&nbsp;Vos&nbsp;matières&nbsp;TRIADE&nbsp;</p>
        <ul id="allItems">
        <?php
        for($i=0;$i<countTriade($data);$i++) {
            if ($_POST["affMatiereTous"] != "1") {
                if (verifMatiereBrevetLI($data[$i][0],$idclasse)) { continue; }
            }
            if ($data[$i][2] == "0") { $data[$i][2]=""; }
            print "<li id='".$data[$i][0]."'>".$data[$i][1]." ".$data[$i][2]."</li>";
        }
        ?>
        </ul></div>
    </div>

    <div id="dhtmlgoodies_mainContainer">
        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Français&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Français</p></a>
        <ul id="Français"><?php listMatiereBrevetLI("Français",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Mathématiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Mathématiques</p></a>
        <ul id="Mathématiques"><?php listMatiereBrevetLI("Mathématiques",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Langue vivante 1&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Lv1 <small>(1*)</small></p></a>
        <ul id="Langue vivante 1"><?php listMatiereBrevetLI("Langue vivante 1",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=SVT&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>SVT</p></a>
        <ul id="SVT"><?php listMatiereBrevetLI("SVT",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Sciences Physiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Sciences&nbsp;Physiques</p></a>
        <ul id="Sciences Physiques"><?php listMatiereBrevetLI("Sciences Physiques",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Education Socioculturelle&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Educ.&nbsp;socioculturelle</p></a>
        <ul id="Education Socioculturelle"><?php listMatiereBrevetLI("Education Socioculturelle",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Prevention Sante Environnement&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Prévention&nbsp;Santé&nbsp;Env.</p></a>
        <ul id="Prevention Sante Environnement"><?php listMatiereBrevetLI("Prevention Sante Environnement",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Sciences Biologiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Sciences&nbsp;Biologiques</p></a>
        <ul id="Sciences Biologiques"><?php listMatiereBrevetLI("Sciences Biologiques",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Physique - Chimie&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Physique&nbsp;-&nbsp;Chimie</p></a>
        <ul id="Physique - Chimie"><?php listMatiereBrevetLI("Physique - Chimie",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelleEducation physique et sportive=&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Educ.&nbsp;physique&nbsp;et&nbsp;sportive</p></a>
        <ul id="Education physique et sportive"><?php listMatiereBrevetLI("Education physique et sportive",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Arts plastiques&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Arts&nbsp;plastiques</p></a>
        <ul id="Arts plastiques"><?php listMatiereBrevetLI("Arts plastiques",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Education musicale&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Education&nbsp;musicale</p></a>
        <ul id="Education musicale"><?php listMatiereBrevetLI("Education musicale",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Techno Secteur Agricoles&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Techno.&nbsp;Secteur&nbsp;Agricoles</p></a>
        <ul id="Techno Secteur Agricoles"><?php listMatiereBrevetLI("Techno Secteur Agricoles",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Technologique&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Technologique</p></a>
        <ul id="Technologique"><?php listMatiereBrevetLI("Technologique",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=langue vivante 2&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>lv2 <small>(2*)</small></p></a>
        <ul id="langue vivante 2"><?php listMatiereBrevetLI("langue vivante 2",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Education civique&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Education&nbsp;civique</p></a>
        <ul id="Education civique"><?php listMatiereBrevetLI("Education civique",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=histoire des arts&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Histoire&nbsp;des&nbsp;arts</p></a>
        <ul id="histoire des arts"><?php listMatiereBrevetLI("histoire des arts",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Découverte professionnelle 6h&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Découverte&nbsp;prof.&nbsp;6h</p></a>
        <ul id="Découverte professionnelle 6h"><?php listMatiereBrevetLI("Découverte professionnelle 6h",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Latin ou grec ou Découverte professionnelle 3h (option facultative)&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Latin,&nbsp;grec,&nbsp;Découverte&nbsp;3h <small>(3*)</small></p></a>
        <ul id="Latin ou grec ou Découverte professionnelle 3h (option facultative)"><?php listMatiereBrevetLI("Latin ou grec ou Découverte professionnelle 3h (option facultative)",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Latin ou grec ou langue vivante 2 (option facultative)&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Latin,&nbsp;grec,&nbsp;lv2 <small>(3*)</small></p></a>
        <ul id="Latin ou grec ou langue vivante 2 (option facultative)"><?php listMatiereBrevetLI("Latin ou grec ou langue vivante 2 (option facultative)",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Histoire - Géographie&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Histoire&nbsp;-&nbsp;Géographie</p></a>
        <ul id="Histoire - Géographie"><?php listMatiereBrevetLI("Histoire - Géographie",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Histoire - Géographie - Civique&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Hist.&nbsp;Géo.&nbsp;Educ.&nbsp;Civique</p></a>
        <ul id="Histoire - Géographie - Civique"><?php listMatiereBrevetLI("Histoire - Géographie - Civique",$idclasse) ?></ul></div>

        <div><a href="#" onclick="PopupCentrer('modifcoefbrevet.php?libelle=Prévention Santé&idclasse=<?php print $idclasse ?>','500','300','scrollbars=yes','')"><p>Prévention&nbsp;Santé&nbsp;Env.</p></a>
        <ul id="Prévention Santé"><?php listMatiereBrevetLI("Prévention Santé",$idclasse) ?></ul></div>
    </div>

</div><!-- dhtmlgoodies_dragDropContainer -->

<form method="post" name="formulaire" style="margin-top:14px">
  <input type="hidden" name="affMatiereTous" value="<?php print $_POST["affMatiereTous"] ?>">
  <input type="hidden" name="idclasse" value="<?php print $idclasse ?>">
  <input type="button" onclick="saveDragDropNodes()" value="<?php print LANGENR ?>" class="btn btn-primary" style="font-size:12px">
  <input type="text" name="donnee" style="visibility:hidden;width:1px">
</form>

</div><!-- brv-content -->

<ul id="dragContent"></ul>
<div id="dragDropIndicator"><img src="./image/commun/insert.gif"></div>

<?php Pgclose(); ?>
</BODY>
</HTML>

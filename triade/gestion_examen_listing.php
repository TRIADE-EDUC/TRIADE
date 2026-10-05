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
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Listing examens</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onUnload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");
$datap=config_param_visu("affNomEleExam");
$affNomEleExam=$datap[0][0];
$datap=config_param_visu("affMatriEleExam");
$affMatriEleExam=$datap[0][0];
if ($affNomEleExam == "oui")    { $affNomEleExam="checked='checked'"; }    else { $affNomEleExam=""; }
if ($affMatriEleExam == "oui")  { $affMatriEleExam="checked='checked'"; }  else { $affMatriEleExam=""; }
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-list-check" style="margin-right:6px"></i><?php print LANGBULL7 ?></font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:16px">

<div class="card" style="max-width:580px;margin:0 auto">
<form method="post" onsubmit="return valide_consul_classe()" name="formulaire" action="gestion_examen_listing_2.php">
<div class="card-body" style="padding:0">

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right"><?php print LANGBULL3 ?></label>
    <span style="font-size:12px;color:#333"><?php print $_COOKIE["anneeScolaire"] ?></span>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right"><?php print LANGBULL2 ?></label>
    <div style="flex:1">
    <?php if (isset($idclasse)) { ?>
      <b style="font-size:12px"><?php print chercheClasse_nom($idclasse) ?></b>
      <input type="hidden" name="saisie_classe" value="<?php print $idclasse ?>">
    <?php } else { ?>
      <select name="saisie_classe" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;width:100%">
        <option selected value="0" id='select0'><?php print LANGCHOIX ?></option>
        <?php
        if (countTriade($tabClasse) > 0) {
          for($i=0;$i<countTriade($tabClasse);$i++) {
            print "<option value='".$tabClasse[$i][1]."'>".$tabClasse[$i][0]."</option>";
          }
        } else {
          select_classe();
        }
        ?>
      </select>
    <?php } ?>
    </div>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right"><?php print LANGBASE40 ?></label>
    <div style="display:flex;gap:8px;flex:1;flex-wrap:wrap">
      <select id="tt_gel" name="typetrisem" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px">
        <option value="0" id='select0'><?php print LANGCHOIX ?></option>
        <option value="trimestre" id='select1'><?php print LANGPARAM28 ?></option>
        <option value="semestre" id='select1'><?php print LANGPARAM29 ?></option>
      </select>
      <select id="st_gel" name="saisie_trimestre" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px">
        <option id='select1'>&nbsp;</option>
        <option id='select1'>&nbsp;</option>
        <option id='select1'>&nbsp;</option>
      </select>
      <script>(function(){var src=document.getElementById('tt_gel'),dst=document.getElementById('st_gel'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>
    </div>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right">Type d'examen</label>
    <select name="saisie_examen" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1">
      <option value=""><?php print LANGCHOIX ?></option>
      <?php if (EXAMENBLANC == "oui") { ?>
        <optgroup label="Blanc">
          <option value="Brevet Blanc">Brevet Blanc</option>
          <option value="Brevet Professionnel Blanc">Brevet Professionnel Blanc</option>
          <option value="BAC Blanc">BAC Blanc</option>
          <option value="CAP Blanc">CAP Blanc</option>
          <option value="BEP Blanc">BEP Blanc</option>
          <option value="BTS Blanc">BTS Blanc</option>
          <option value="Partiel Blanc">Partiel Blanc</option>
          <option value="Concours Blanc">Concours Blanc</option>
        </optgroup>
      <?php } ?>
      <?php if (EXAMENNAMUR == "oui") { ?>
        <optgroup label="Spécif. Namur">
          <option value="décembre">Décembre</option>
          <option value="juin">Juin</option>
        </optgroup>
      <?php } ?>
      <?php if (EXAMENISMAP == "oui") { ?>
        <optgroup label="ISMAP">
          <option value="CC">CC - Participation</option>
          <option value="DST">DST</option>
          <option value="Partiel">Partiel</option>
          <option value="Soutenance">Soutenance</option>
          <option value="Rapport">Rapport</option>
          <option value="Fiche de lecture">Fiche de lecture</option>
          <option value="Exposé">Exposé</option>
          <option value="Dad">Dad</option>
          <option value="Lecture">Lecture</option>
          <option value="Examen écrit">Examen écrit</option>
          <option value="Recopiage vocabulaire">Recopiage vocabulaire</option>
          <option value="Mémoire Ip">Mémoire Ip</option>
          <option value="Evaluation Tutorat">Evaluation Tutorat</option>
        </optgroup>
      <?php } ?>
      <?php if (EXAMENDS == "oui") { ?>
        <optgroup label="DS">
          <option value="DS1">DS1</option>
          <option value="DS2">DS2</option>
          <option value="DS3">DS3</option>
          <option value="DS4">DS4</option>
        </optgroup>
      <?php } ?>
      <?php if (EXAMEN == "oui") { ?>
        <optgroup label="Examen">
          <option value="Partiel">Partiel</option>
        </optgroup>
      <?php } ?>
      <?php if (EXAMENCIEFORMATION == "oui") { ?>
        <optgroup label="Spécif. Cie. Formation">
          <option value="TAS">TAS</option>
          <option value="BTS Blanc">BTS Blanc</option>
        </optgroup>
      <?php } ?>
      <?php if (EXAMENEEPP == "oui") { ?>
        <optgroup label="Spécif. EEPP">
          <option value="semestre">Semestriel</option>
          <option value="2session">2ème session</option>
        </optgroup>
      <?php } ?>
    </select>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right">Nom de l'élève</label>
    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#333;cursor:pointer">
      <input type="checkbox" name="saisie_avec_nom" value="oui" <?php print $affNomEleExam ?> style="accent-color:#080A66">
      Afficher
    </label>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right">Matricule élève</label>
    <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#333;cursor:pointer">
      <input type="checkbox" name="saisie_avec_matricule" value="oui" <?php print $affMatriEleExam ?> style="accent-color:#080A66">
      Afficher
    </label>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:160px;text-align:right">Hauteur des lignes</label>
    <select name="hauteur" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;width:80px">
      <option value="6">06</option>
      <option value="7">07</option>
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

  <div style="padding:12px 16px;display:flex;gap:8px;justify-content:center">
    <script language=JavaScript>buttonMagicSubmit3("<?php print "Valider la sélection" ?>","rien","onclick='attente()'");</script>
    <script language=JavaScript>buttonMagicRetour2("gestion_examen.php","_self","<?php print "Retour" ?>");</script>
  </div>

</div>
<input type="hidden" name="type_notation" value="<?php print $_POST["type_notation"] ?>">
</form>
</div>

</td></tr>
</table>
</div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY>
</HTML>

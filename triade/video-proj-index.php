<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade Vidéo-Projecteur</title>
<style>
* { box-sizing: border-box; }
html, body { height: 100%; margin: 0; padding: 0; font-family: Electrolize, Trebuchet MS, Arial, sans-serif; background: linear-gradient(135deg,#080A66 0%,#1a1c8a 60%,#2d2fa0 100%); display: flex; align-items: center; justify-content: center; }
.vp-card { background: #fff; border-radius: 12px; box-shadow: 0 16px 48px rgba(8,10,102,.35); width: 520px; overflow: hidden; }
.vp-card-header { background: linear-gradient(135deg,#080A66,#1a1c8a); padding: 18px 24px; display: flex; align-items: center; gap: 12px; }
.vp-card-header i { font-size: 26px; color: #CACCEF; }
.vp-card-header h1 { margin: 0; font-size: 16px; font-weight: 700; color: #fff; letter-spacing: .03em; }
.vp-card-header p { margin: 0; font-size: 11px; color: #CACCEF; }
.vp-card-body { padding: 20px 24px; }
.vp-field { margin-bottom: 14px; }
.vp-field label { display: block; font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
.vp-field select, .vp-field input[type=text] {
  width: 100%; border: 1px solid #c5cae9; border-radius: 5px;
  padding: 6px 10px; font-size: 13px; color: #222; background: #fff;
}
.vp-field select:focus { border-color: #080A66; outline: none; }
.vp-radio-row { display: flex; gap: 16px; align-items: center; font-size: 13px; }
.vp-radio-row label { display: flex; align-items: center; gap: 5px; cursor: pointer; font-weight: 400; color: #333; }
.vp-divider { border: none; border-top: 1px solid #f0f2fa; margin: 16px 0; }
.vp-card-footer { padding: 12px 24px 20px; display: flex; align-items: center; gap: 10px; }
.vp-alert { font-size: 12px; color: #e65100; font-weight: 600; text-align: center; padding: 6px 0; }
</style>
<script>
function modifForm() {
	var a=document.formulaire.type_bulletin.options.selectedIndex;
	if (document.formulaire.type_bulletin.options[a].value == "UE") {
		document.formulaire.action="video-proj-affichage-UE.php";
	} else {
		document.formulaire.action="video-proj-affichage.php";
	}
}
</script>
</head>
<body>
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once('librairie_php/db_triade.php');
$cnx=cnx();
validerequete("7");
$valeur=aff_Trimestre();
if (countTriade($valeur)) { $disabled=""; $alert=""; }
else { $disabled="disabled=disabled"; $alert=LANGMESS10."<br>".LANGMESS11."<br>".LANGMESS12; }

$datap=config_param_visu("validenoteviescolaire");
$validenoteviescolaire=$datap[0][0];
$validenoteviescolaireOUI=($validenoteviescolaire=="oui")?"checked='checked'":"";
$validenoteviescolaireNON=($validenoteviescolaire!="oui")?"checked='checked'":"";

$datap=config_param_visu("affNotePartielVatel");
$affNotePartielVatel=$datap[0][0];
$affNotePartielVatelOUI=($affNotePartielVatel=="oui")?"checked='checked'":"";
$affNotePartielVatelNON=($affNotePartielVatel!="oui")?"checked='checked'":"";
?>

<div class="vp-card">
  <div class="vp-card-header">
    <i class="bi bi-projector-fill"></i>
    <div>
      <h1>Vidéo-Projecteur</h1>
      <p>Sélection de la classe et de la période</p>
    </div>
  </div>
  <div class="vp-card-body">
  <form method=post action="video-proj-affichage.php" onsubmit="return valide_choix_projo()" name="formulaire">

    <div class="vp-field">
      <label><i class="bi bi-calendar3"></i> <?php print LANGBULL3 ?></label>
      <?php $anneeScolaire=$_COOKIE["anneeScolaire"]; ?>
      <select name='anneeScolaire'><?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?></select>
    </div>

    <div class="vp-field">
      <label><i class="bi bi-mortarboard"></i> <?php print LANGPROJ1 ?></label>
      <select name="saisie_classe">
        <option><?php print LANGCHOIX ?></option>
        <?php
        if ($_SESSION["membre"] == "menuprof") {
          $ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
          $mySession=hashSessionVar($ident); unset($ident);
          $donne=$_SESSION["id_suppleant"];
          $sql="SELECT idprof,idclasse FROM {$prefixe}prof_p WHERE idprof='$donne'";
          $curs=execSql($sql); $data=chargeMat($curs);
          for($i=0;$i<countTriade($data);$i++){
            $nomclasse=chercheClasse($data[$i][1]); $nomclasse=$nomclasse[0][1];
            print "<option value='".$data[$i][1]."'>$nomclasse</option>\n";
          }
          freeResult($curs);
        }else{
          select_classe();
        }
        ?>
      </select>
    </div>

    <div class="vp-field">
      <label><i class="bi bi-calendar-range"></i> <?php print LANGPROJ2 ?></label>
      <select name="saisie_trimestre">
        <option value=0><?php print LANGCHOIX ?></option>
        <option value="trimestre1"><?php print LANGPROJ3." ".LANGOU." ".LANGPROJ19 ?></option>
        <option value="trimestre2"><?php print LANGPROJ4." ".LANGOU." ".LANGPROJ20 ?></option>
        <option value="trimestre3"><?php print LANGPROJ5 ?></option>
        <option value="annee"><?php print LANGMESS430 ?></option>
      </select>
    </div>

    <div class="vp-field">
      <label><i class="bi bi-file-text"></i> <?php print LANGMESS432 ?></label>
      <select name="type_bulletin" onchange="modifForm();">
        <?php if (isset($_COOKIE["type_bulletin"])) { print "<option value='".$_COOKIE["type_bulletin"]."'>Bulletin ".strtoupper($_COOKIE["type_bulletin"])."</option>"; } ?>
        <option value='default'>Standard</option>
        <option value='UE'>Avec unité enseignement</option>
        <optgroup label="Montessori">
          <option value='montessori'>Bulletin Montessori standard</option>
          <option value='montessori_spec'>Bulletin Montessori Spécif.</option>
        <optgroup label="Seminaire">
          <option value='seminaire'>Bulletin Seminaire standard</option>
        <optgroup label="LEAP">
          <option value='leap'>Bulletin LEAP standard</option>
        <optgroup label="ISMAPP">
          <option value='ismapp'>Bulletin ISMAPP standard</option>
      </select>
    </div>

    <hr class="vp-divider">

    <?php if (MODNAMUR0 == "oui"): ?>
    <div class="vp-field">
      <label><?php print LANGTMESS487 ?></label>
      <div class="vp-radio-row">
        <label><input type='radio' name='validenoteviescolaire' value='oui' <?php print $validenoteviescolaireOUI ?>> <?php print LANGOUI ?></label>
        <label><input type='radio' name='validenoteviescolaire' value='non' <?php print $validenoteviescolaireNON ?>> <?php print LANGNON ?></label>
      </div>
    </div>
    <?php endif; ?>

    <div class="vp-field">
      <label><?php print LANGMESS431 ?></label>
      <div class="vp-radio-row">
        <label><input type='radio' name='afficheNotePartielVatel' value='oui' <?php print $affNotePartielVatelOUI ?>> <?php print LANGOUI ?></label>
        <label><input type='radio' name='afficheNotePartielVatel' value='non' <?php print $affNotePartielVatelNON ?>> <?php print LANGNON ?></label>
      </div>
    </div>

  </div><!-- .vp-card-body -->

  <div class="vp-card-footer">
    <script language=JavaScript>buttonMagicSubmitAtt("<?php print LANGBT31?>","supp","<?php print $disabled?>");</script>
    <?php if (isset($_GET["info"])): ?>
    <span class="vp-alert"><i class="bi bi-exclamation-triangle"></i> <?php print LANGPROJ6 ?></span>
    <?php endif; ?>
  </div>

  <?php if ($alert): ?>
  <div class="vp-alert" style="padding:10px 24px 16px"><i class="bi bi-exclamation-triangle-fill"></i> <?php print $alert ?></div>
  <?php endif; ?>

  </form>
</div>

<?php Pgclose(); ?>
</body>
</html>

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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_circulaire.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once("./librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
$fichier="";
if (isset($_GET["idcirculaire"])) {
	$idcirculaire=$_GET["idcirculaire"];
	$data=chercheCirculaire($idcirculaire);
	$id_circulaire=$data[0][0];
	$sujet=htmlentities($data[0][1]);
	$reference=htmlentities($data[0][2]);
	$fichier=$data[0][3];
	$enseignant=$data[0][5];
	$classe=$data[0][6];
	$idprofp=$data[0][7];
	$comptepersonnel=$data[0][8];
	$compteviescolaire=$data[0][9];
	$comptedirection=$data[0][10];
	$comptetuteurdestage=$data[0][11];
	$categorie=$data[0][12];
}

if (file_exists("common/config8.inc.php")) { include_once("common/config8.inc.php"); }
else { define("AGENTWEBPRENOM","Lise"); }

if (UPLOADIMG == "oui") { $taille="8Mo"; } else { $taille="2Mo"; }
$mess=LANGCIRCU11." (Taille max : $taille) ";

$checkedProf           = ($enseignant==1)          ? "checked='checked'" : "";
$checkedpersonnel      = ($comptepersonnel==1)      ? "checked='checked'" : "";
$checkedviescolaire    = ($compteviescolaire==1)    ? "checked='checked'" : "";
$checkedtuteurdestage  = ($comptetuteurdestage==1)  ? "checked='checked'" : "";
$checkeddirection      = ($comptedirection==1)      ? "checked='checked'" : "";

$data_class=affclasse();
$liste_classe=preg_replace('/[{}]/','',$classe);
$dataClasse=explode(",",$liste_classe);
?>

<script>
nbcase="<?php print countTriade($data_class) ?>"; nbcase+=4;
function tout() { for(var i=10;i<=nbcase;i++) document.formulaire.elements[i].checked=true; }
</script>

<form method="post" action="./circulaire_ajout2.php" name="formulaire" enctype="multipart/form-data">
<?php if ($id_circulaire != "") { ?>
<input type="hidden" name="id_circulaire" value="<?php print $id_circulaire ?>">
<?php } ?>

<div class="na-card">

  <div class="na-row">
    <span class="na-lbl"><?php print LANGCIRCU6 ?> :</span>
    <input type="text" name="saisie_titre" size="30" maxlength="28" value="<?php print $sujet ?>" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGCIRCU7 ?> :</span>
    <input type="text" name="saisie_ref" size="30" maxlength="28" value="<?php print $reference ?>" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl">Catégorie :</span>
    <input type="text" name="saisie_cat" size="30" maxlength="200" value="<?php print $categorie ?>" class="cc-select">
  </div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGCIRCU8 ?> :</span>
    <?php if ($fichier != "") { ?>
      <span style="font-size:12px;color:#333;"><?php print $fichier ?></span>
    <?php } else { ?>
      <div>
        <input type="file" name="fichier" size="30">
        <a href="#" onMouseOver="AffBulle3('Attention','./image/commun/warning.jpg','<?php print $mess ?>'); window.status=''; return true;" onMouseOut="HideBulle()">
          <img src="./image/help.gif" width="15" height="15" border="0" style="vertical-align:middle;margin-left:4px;">
        </a>
      </div>
    <?php } ?>
  </div>

  <div class="na-row">
    <span class="na-lbl">Avertir par messagerie :</span>
    <label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;">
      <input type="checkbox" name="envoimessage" value="oui"> oui
    </label>
  </div>

</div>

<div class="na-card">
  <div style="font-size:11px;font-weight:700;color:#080A66;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #eef0f8;">Destinataires</div>

  <div class="na-row">
    <span class="na-lbl"><?php print LANGCIRCU9 ?> :</span>
    <label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;">
      <input type="checkbox" name="saisie_envoi_prof" value="1" <?php print $checkedProf ?>>
      <a href="#" onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<?php print LANGCIRCU12 ?>'); window.status=''; return true;" onMouseOut="HideBulle()"><img src="./image/help.gif" width="15" height="15" border="0" style="vertical-align:middle;"></a>
    </label>
  </div>
  <div class="na-row">
    <span class="na-lbl">Pour le personnel :</span>
    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;"><input type="checkbox" name="saisie_envoi_pers" value="1" <?php print $checkedpersonnel ?>></label>
  </div>
  <div class="na-row">
    <span class="na-lbl">Pour la vie scolaire :</span>
    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;"><input type="checkbox" name="saisie_envoi_mvs" value="1" <?php print $checkedviescolaire ?>></label>
  </div>
  <div class="na-row">
    <span class="na-lbl">Pour tuteurs de stage :</span>
    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;"><input type="checkbox" name="saisie_envoi_tut" value="1" <?php print $checkedtuteurdestage ?>></label>
  </div>
  <div class="na-row">
    <span class="na-lbl">Pour la direction :</span>
    <label style="display:flex;align-items:center;gap:5px;cursor:pointer;"><input type="checkbox" name="saisie_envoi_dir" value="1" <?php print $checkeddirection ?>></label>
  </div>

  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl" style="padding-top:4px;">La ou les classe(s) :</span>
    <div>
      <?php
      $j=0;
      for ($i=0; $i<countTriade($data_class); $i++) {
          if ($j==2) { $j=0; print "<br>"; }
          $checked="";
          foreach ($dataClasse as $val) {
              if ($val==$data_class[$i][0]) { $checked="checked='checked'"; break; }
          }
          print "<label style='display:inline-flex;align-items:center;gap:4px;margin-right:10px;font-size:12px;cursor:pointer;'>";
          print "<input type='checkbox' name='saisie_classe[]' value='".$data_class[$i][0]."' $checked>";
          print trim($data_class[$i][1])."</label>";
          $j++;
      }
      ?>
      <br><div style="text-align:right;margin-top:4px;"><a href="#" onclick="tout(); return false;" style="font-size:11px;color:#080A66;"><?php print LANGCIRCU13 ?></a></div>
    </div>
  </div>
</div>

<div class="na-foot">
  <script language=JavaScript>buttonMagic("<?php print LANGCIRCU14 ?>","Javascript:history.go(-1)","_parent","","");</script>
  <?php if ($idcirculaire != "") { ?>
    <script language=JavaScript>buttonMagicSubmit3("Modifier","modif","onclick='attente();'");</script>
  <?php } else { ?>
    <script language=JavaScript>buttonMagicSubmit3("<?php print LANGCIRCU15 ?>","rien","onclick='attente();'");</script>
  <?php } ?>
</div>
</form>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>

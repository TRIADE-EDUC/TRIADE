<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
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
<title>Triade - Evaluation jury</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
$data=visu_param();
for($i=0;$i<countTriade($data);$i++) {
    $nom_etablissement=trim($data[$i][0]);
    $adresse=trim($data[$i][1]);
    $postal=trim($data[$i][2]);
    $ville=trim($data[$i][3]);
    $tel=trim($data[$i][4]);
    $mail=trim($data[$i][5]);
    $directeur_etablissement=trim($data[$i][6]);
    $urlsite=trim($data[$i][7]);
    $accademie=trim($data[$i][8]);
    $pays=trim($data[$i][9]);
    $departement=trim($data[$i][10]);
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-clipboard2-check-fill" style="margin-right:6px"></i>Evaluation et notation du jury</font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:16px">

<div class="card" style="max-width:680px;margin:0 auto">
<form method="post" name="formulaire" action="gestion_examen_jury2.php" enctype="multipart/form-data">
<div class="card-body" style="padding:0">

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right">Année</label>
    <input type="text" name="annee" id="annee" value="<?php print $anneeScolaire ?>" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;width:200px">
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right">Titre</label>
    <input type="text" name="titre" id="titre" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1">
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right">Directeur</label>
    <select name="directeur" id="directeur" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1">
      <option id='select0' value=''><?php print LANGCHOIX ?></option>
      <?php print select_personne_2('ENS','30'); ?>
    </select>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:flex-start;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right;padding-top:4px">Composition du jury</label>
    <div style="flex:1">
      <input type="text" name="jury" id="jury" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;width:100%;box-sizing:border-box">
      <span style="font-size:10px;color:#888;font-style:italic">Séparer par une virgule</span>
    </div>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right">Sujet</label>
    <input type="text" name="sujet" id="sujet" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1">
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right">Auteur(s)</label>
    <input type="text" name="auteur" id="auteur" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;flex:1">
  </div>

  <div style="padding:10px 16px;border-bottom:1px solid #e8eaf6">
    <label style="font-size:12px;font-weight:600;color:#555;display:block;margin-bottom:6px">
      <i class="bi bi-pencil-square" style="color:#080A66;margin-right:4px"></i>Appréciation et Evaluation du jury
    </label>
    <textarea name="commentaire" id="commentaire" rows="20" style="width:100%;box-sizing:border-box;font-size:12px;padding:6px 8px;border:1px solid #c5cae9;border-radius:4px;resize:vertical;font-family:inherit"></textarea>
  </div>

  <div style="padding:8px 16px;border-bottom:1px solid #e8eaf6;display:flex;align-items:center;gap:12px">
    <label style="font-size:12px;font-weight:600;color:#555;min-width:150px;text-align:right">Notation</label>
    <div style="display:flex;align-items:center;gap:6px">
      <input type="text" name="note" id="note" size="3" style="font-size:12px;padding:4px 8px;border:1px solid #c5cae9;border-radius:4px;width:50px;text-align:center">
      <span style="font-size:12px;color:#555">/20</span>
    </div>
  </div>

  <div style="padding:12px 16px;display:flex;gap:8px;justify-content:center">
    <script language=JavaScript>buttonMagic("<?php print LANGCIRCU14 ?>","Javascript:history.go(-2)","_parent","","");</script>
    <script language=JavaScript>buttonMagicSubmit3("<?php print LANGENR ?>","rien","");</script>
  </div>

</div>
</form>
</div>
<br><br>
<?php
Pgclose();
include_once("./librairie_php/lib_conexpersistant.php");
connexpersistance();
?>
</td></tr>
</table>
</div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY>
</HTML>

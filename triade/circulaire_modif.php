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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGTMESS486 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<!-- ── Filtre catégorie ── -->
<?php $data=listeCatCirculaire(); ?>
<form method="get" action="circulaire_modif.php">
<div class="na-card" style="margin-bottom:10px;">
  <div class="na-row">
    <span class="na-lbl">Catégorie :</span>
    <select name="filtre" class="cc-select" onchange="this.form.submit()">
      <option value=""><?php print LANGCHOIX ?></option>
      <?php
      for ($i=0; $i<countTriade($data); $i++) {
          $selected = ($_GET["filtre"] == $data[$i][0]) ? "selected='selected'" : '';
          print "<option $selected value=\"".$data[$i][0]."\">".$data[$i][0]."</option>";
      }
      ?>
    </select>
    <a href="circulaire_admin.php" class="btn-retour" style="text-decoration:none;">&larr; Administration</a>
  </div>
</div>
</form>

<!-- ── Tableau ── -->
<?php
$filtre=$_GET["filtre"];
$tri = isset($_GET["tri"]) ? $_GET["tri"] : "date";
$imgDate = ($tri=="date")    ? "<img src='image/commun/za2.png'>" : "";
$imgRef  = ($tri=="refence") ? "<img src='image/commun/za2.png'>" : "";
$imgObj  = ($tri=="sujet")   ? "<img src='image/commun/za2.png'>" : "";
?>

<table class="cc-data-table">
<thead>
<tr class="cc-thead-row">
  <th class="cc-th"><a href="circulaire_modif.php?tri=date&filtre=<?php print urlencode($filtre) ?>" style="color:#080A66;text-decoration:none">Date <?php print $imgDate ?></a></th>
  <th class="cc-th">Catégorie</th>
  <th class="cc-th"><a href="circulaire_modif.php?tri=refence&filtre=<?php print urlencode($filtre) ?>" style="color:#080A66;text-decoration:none">Référence <?php print $imgRef ?></a></th>
  <th class="cc-th"><a href="circulaire_modif.php?tri=sujet&filtre=<?php print urlencode($filtre) ?>" style="color:#080A66;text-decoration:none">Objet <?php print $imgObj ?></a></th>
  <th class="cc-th cc-th-center">Modifier</th>
</tr>
</thead>
<tbody>
<?php
if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) {
	$data = ($_SESSION["membre"] == "menuscolaire")
		? circulaireAffVieScolaire($tri,$filtre)
		: circulaireAffAdmin($tri,$filtre);

	for ($i=0; $i<countTriade($data); $i++) { ?>
<tr class="cc-tr-data">
  <td class="cc-td"><?php print dateForm($data[$i][4]) ?></td>
  <td class="cc-td"><?php print htmlspecialchars($data[$i][7]) ?></td>
  <td class="cc-td"><?php print htmlspecialchars($data[$i][2]) ?></td>
  <td class="cc-td"><?php print htmlspecialchars($data[$i][1]) ?></td>
  <td class="cc-td cc-td-center">
    <a href="circulaire_ajout.php?idcirculaire=<?php print $data[$i][0] ?>" class="btn-dest" style="text-decoration:none;display:inline-block;">Modifier</a>
  </td>
</tr>
<tr>
  <td colspan="5" class="cc-td" style="font-size:11px;color:#666;font-style:italic;padding-top:0;">
    <?php
    if ($data[$i][5] == 1) print LANGPER6." — ";
    $ligne = substr($data[$i][6],1,-1);
    $vals = explode(',', $ligne);
    $noms = [];
    foreach ($vals as $val) {
        $val = trim($val);
        if ($val !== "") { $n = chercheClasse_nom($val); if ($n) $noms[] = htmlspecialchars(ucwords(strtolower($n))); }
    }
    print implode(' — ', $noms);
    ?>
  </td>
</tr>
<?php }
} ?>
</tbody>
</table>
<br>

</td></tr></table>

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

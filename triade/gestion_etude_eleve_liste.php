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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGETUDE28 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">
<?php
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
error($cnx);
$data=liste_etude();
//id,jour_semaine,heure,salle,pion,nom_etude,duree
?>

<div style="padding:8px 0;">
<?php
for ($i=0; $i<countTriade($data); $i++) {
	$pion  = (($data[$i][4] == "-1") || ($data[$i][4] == NULL)) ? "???" : $data[$i][4];
	$duree = ($data[$i][6] == 0) ? "???" : $data[$i][6];
	$liste = preg_replace('/[\{\}]/','',$data[$i][1]);
	$tab   = explode(",", $liste);
	$jours = implode(", ", array_map('jourdesemaine', $tab));
?>
<div class="vdi-card">
  <div class="vdi-card-head">
    <span class="vdi-card-date"><?php print LANGETUDE13 ?> : <b><?php print $data[$i][5] ?></b></span>
    <span class="vdi-card-time"><?php print LANGETUDE14 ?> : <?php print $data[$i][3] ?></span>
  </div>
  <div class="vdi-card-body">
    <div class="vdi-field">
      <span class="vdi-lbl"><?php print LANGETUDE12 ?> :</span>
      <span class="vdi-val"><?php print $pion ?></span>
    </div>
    <div class="vdi-field">
      <span class="vdi-lbl"><?php print LANGETUDE16 ?> :</span>
      <span class="vdi-val"><?php print $jours ?></span>
    </div>
    <div class="vdi-field">
      <span class="vdi-lbl"><?php print LANGETUDE17 ?></span>
      <span class="vdi-val"><?php print timeForm($data[$i][2]) ?></span>
      <span class="vdi-lbl" style="margin-left:8px;"><?php print LANGETUDE18 ?></span>
      <span class="vdi-val"><?php print $duree ?></span>
    </div>
    <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
      <button type="button" class="btn-dest"
        onclick="open('gestion_etude_liste_eleve2.php?id=<?php print $data[$i][0]?>','listeE','width=490,height=500,scrollbars=yes')">
        <?php print LANGETUDE31 ?>
      </button>
      <button type="button" class="btn-dest"
        onclick="open('planclasseetude.php?id=<?php print $data[$i][0]?>','_blank','width=800,height=600,resizable=yes,scrollbars=yes')">
        Plan étude
      </button>
    </div>
  </div>
</div>
<?php } ?>
</div>

<div class="na-foot">
  <button type="button" class="btn-retour" onclick="open('gestion_etude.php','_self','')">Retour</button>
</div>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>

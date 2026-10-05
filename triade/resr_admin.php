<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0)) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");
}
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
<script type="text/javascript" src="./librairie_js/jquery.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
$demresaSalle = consult_resa2('2');
$demresaEquip = consult_resa2('1');
Pgclose();
?>

<div class="dest-wrap">

<!-- ── Section Équipements ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGRESA1 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="resr_equip_liste.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA3 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT288 ?></button>
    </div>
  </form>

  <form action="resr_equip_ajout.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA5 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE3 ?></button>
    </div>
  </form>

  <form action="resr_equip_supp.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA7 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
    </div>
  </form>

  <form action="calendrier_reser_equi.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA44 ?></span>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        <button type="submit" class="btn-dest"><?php print LANGRESA57 ?></button>
        <button type="button" class="btn-dest" onclick="open('listing_resa.php?id=equip','_self','')">Listing</button>
        <button type="button" class="btn-dest" onclick="open('edt_visu.php?equip','_blank','')">E.D.T.</button>
      </div>
    </div>
  </form>

  <form action="resr_equip_confirmer.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <?php print LANGRESA56 ?>
        <?php if (countTriade($demresaSalle) > 0) { print " <img src='image/commun/important.png' id='imp1' />"; } ?>
      </span>
      <button type="submit" class="btn-dest"><?php print LANGRESA58 ?></button>
    </div>
  </form>

</div>

<!-- ── Section Salles ── -->
<a name="salle"></a>
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGRESA2 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="resr_salle_visu.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA4 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT288 ?></button>
    </div>
  </form>

  <form action="resr_salle_ajout.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA8 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE3 ?></button>
    </div>
  </form>

  <form action="resr_salle_supp.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA10 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
    </div>
  </form>

  <form action="calendrier_reser_salle.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA51 ?></span>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        <button type="submit" class="btn-dest"><?php print LANGRESA57 ?></button>
        <button type="button" class="btn-dest" onclick="open('listing_resa.php?id=salle','_self','')">Listing</button>
        <button type="button" class="btn-dest" onclick="open('edt_visu.php?equip','_blank','')">E.D.T.</button>
      </div>
    </div>
  </form>

  <form action="resr_salle_confirmer.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">
        <?php print LANGRESA56 ?>
        <?php if (countTriade($demresaEquip) > 0) { print " <img src='image/commun/important.png' id='imp2' />"; } ?>
      </span>
      <button type="submit" class="btn-dest"><?php print LANGRESA58 ?></button>
    </div>
  </form>

</div>

<!-- ── Section Réservations ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGRESA11 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="resr_equip.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA12 ?></span>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        <button type="submit" class="btn-dest"><?php print LANGRESA14 ?></button>
        <button type="button" class="btn-dest" onclick="open('edt_visu.php?equip','_blank','')">Réserver via E.D.T.</button>
      </div>
    </div>
  </form>

  <form action="resr_salle.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGRESA13 ?></span>
      <div style="display:flex;gap:6px;flex-wrap:wrap;">
        <button type="submit" class="btn-dest"><?php print LANGRESA14 ?></button>
        <button type="button" class="btn-dest" onclick="open('edt_visu.php?equip','_blank','')"><?php print LANGMESS244 ?></button>
      </div>
    </div>
  </form>

</div>

</div><!-- /.dest-wrap -->

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<script>
$(document).ready(function() { repeat(); });
function repeat() {
<?php if (countTriade($demresaSalle) > 0) { ?>
	$('img#imp1').hide("slow");
	$('img#imp1').show("slow");
<?php } ?>
<?php if (countTriade($demresaEquip) > 0) { ?>
	$('img#imp2').hide("slow");
	$('img#imp2').show("slow");
<?php } ?>
	setTimeout("repeat()","3000");
}
</script>

</BODY></HTML>

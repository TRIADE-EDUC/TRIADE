<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx = cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0)) {
    PgClose();
    header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
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
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TYPE="text/css" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] " ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>

<?php
$data = list_salle();
// id, libelle, info, type
?>

<form method="post" onsubmit="return valide_supp_choix('saisie_classe_supp','<?php print LANGRESA31?>')" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
  <i class="bi bi-door-open" style="margin-right:6px"></i><?php print LANGRESA4 ?>
</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="max-width:700px;margin:12px auto;display:flex;flex-direction:column;gap:12px">

<div class="card">
  <div class="card-header card-header-primary">
    <i class="bi bi-list-ul"></i> <?php print LANGRESA4 ?>
    <span class="badge" style="margin-left:auto"><?php print countTriade($data) ?></span>
  </div>
  <?php if (countTriade($data) > 0): ?>
  <div style="overflow-x:auto">
  <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:Electrolize,'Trebuchet MS',Arial">
    <thead><tr>
      <th class="cc-th"><?php print LANGRESA59 ?></th>
      <th class="cc-th"><?php print LANGRESA60 ?></th>
      <th class="cc-th" style="width:1%"></th>
    </tr></thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++): ?>
    <tr class="cc-tr-data">
      <td style="padding:8px 12px;font-weight:600"><?php print htmlspecialchars($data[$i][1]) ?></td>
      <td style="padding:8px 12px;color:#555"><?php print htmlspecialchars($data[$i][2]) ?></td>
      <td style="padding:6px 10px;white-space:nowrap">
        <a href="resr_salle_ajout.php?id=<?php print $data[$i][0] ?>"
           class="btn-enr" style="font-size:11px;padding:4px 10px;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
          <i class="bi bi-pencil"></i> Modifier
        </a>
      </td>
    </tr>
    <?php endfor; ?>
    </tbody>
  </table>
  </div>
  <?php else: ?>
  <div class="card-body" style="color:#888;font-size:13px;font-style:italic">Aucune salle configurée.</div>
  <?php endif; ?>
</div>

<div class="toolbar">
  <script language="JavaScript">buttonMagicRetour("resr_admin.php","_parent")</script>
</div>

</div>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
</BODY></HTML>

<?php
session_start();
include_once("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
<title>Triade — Réinitialisation compte MAILING</title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des emails — Réinitialisation du compte</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<div style="max-width:540px;margin:16px auto">

  <!-- Avertissement -->
  <div style="background:#fdecea;border:1px solid #ef9a9a;border-radius:8px;padding:18px 20px;margin-bottom:18px">
    <div style="display:flex;align-items:flex-start;gap:12px">
      <i class="bi bi-exclamation-triangle-fill" style="font-size:24px;color:#c62828;flex-shrink:0;margin-top:2px"></i>
      <div>
        <div style="font-size:13px;font-weight:bold;color:#b71c1c;font-family:Electrolize,'Trebuchet MS',Arial;margin-bottom:8px">
          ATTENTION — Action irréversible
        </div>
        <div style="font-size:12px;color:#7f0000;font-family:Electrolize,'Trebuchet MS',Arial;line-height:1.7">
          Vous êtes sur le point de <strong>supprimer votre compte TRIADE-MAILING</strong>.<br>
          L'accès à votre interface de gestion des emails sera <strong>immédiatement coupé</strong>.<br>
          Cette opération est <strong>définitive et ne peut pas être annulée</strong>.
        </div>
      </div>
    </div>
  </div>

  <!-- Conséquences -->
  <div style="background:#fff8e1;border:1px solid #f5c842;border-radius:8px;padding:14px 18px;margin-bottom:20px">
    <div style="font-size:11px;color:#5d4037;font-family:Electrolize,'Trebuchet MS',Arial;line-height:1.8">
      <div style="font-weight:bold;margin-bottom:6px;color:#6d4c00"><i class="bi bi-info-circle-fill"></i> Conséquences de la réinitialisation</div>
      <div><i class="bi bi-x-circle" style="color:#c62828"></i> Suppression de la configuration locale TRIADE-MAILING</div>
      <div><i class="bi bi-x-circle" style="color:#c62828"></i> Perte de l'accès à votre compte depuis cet espace</div>
      <div><i class="bi bi-x-circle" style="color:#c62828"></i> Désinscription du service (envois d'emails désactivés)</div>
      <div style="margin-top:8px"><i class="bi bi-check-circle" style="color:#2e7d32"></i> Possibilité de créer un nouveau compte après la suppression</div>
    </div>
  </div>

  <!-- Boutons -->
  <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
    <table border=0><tr><td>
    <script language=JavaScript>buttonMagicSubmit3('Confirmer la suppression du compte','mailing-init2.php','_self','','','');</script>
    </td></tr></table>
    <table border=0><tr><td>
    <script language=JavaScript>buttonMagicRetour('Annuler — Retour','mailing.php');</script>
    </td></tr></table>
  </div>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</HTML>

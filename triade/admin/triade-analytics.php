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
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
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
<script language="JavaScript" src="librairie_js/clickdroit2.js"></script>
<title>Triade — Analytics</title>
<style>
.analytics-intro { display:flex; align-items:flex-start; gap:16px; margin:12px 8px 14px; }
.analytics-intro img { border-radius:10px; box-shadow:1px 1px 12px #555; flex-shrink:0; }
.analytics-text { font-size:12px; font-family:Electrolize,Trebuchet MS,Arial; color:#333; line-height:1.6; }
.analytics-sep { border:none; border-top:1px solid #c5cae9; margin:14px 8px; }
.analytics-form-card { background:#f5f7ff; border:1px solid #c5cae9; border-radius:8px; padding:14px 18px; margin:0 8px 14px; }
.analytics-form-label { font-size:12px; font-weight:700; color:#080A66; font-family:Electrolize,Trebuchet MS,Arial; margin-bottom:10px; }
.analytics-form-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>TRIADE-ANALYTICS</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php
if (LAN == "oui") {
    include_once("./librairie_php/lib_licence.php");
    include_once("./librairie_php/db_triade_admin.php");
    $cnx = cnx();
    $IDGOOGLE = recupcomptegoogleanalytic();
?>

<!-- Présentation -->
<div class="analytics-intro">
    <div class="analytics-text">
        TRIADE-ANALYTICS vous permet d'avoir un retour des différents modules utilisés.
        Il analyse le trafic de votre TRIADE et fournit des rapports clairs et précis
        au sujet de vos visiteurs, vous informant de leur provenance et de leur fréquentation du site.<br><br>
        Nous utilisons la plateforme <strong>Matomo</strong> — une solution entièrement conforme au RGPD.
        En tant que leader dans le traitement éthique des données, Matomo propose un outil d'analytique
        Web qui protège les internautes et leur vie privée, tout en étant similaire à GA3 dans son utilisation.
    </div>
    <img src="image/intro_small_new.jpg" alt="Triade Analytics">
</div>

<!-- Accès ou inscription -->
<div style="margin:0 8px 14px;text-align:center;">
<?php if (verifcomptegoogleanalytic()) { ?>
    <a href="https://analytics.triade-educ.net" target="_blank" class="btn-nav" style="text-decoration:none;">
        Accès à TRIADE-ANALYTICS
    </a>
<?php } else { ?>
    <a href="https://www.triade-educ.org/fr/triade-analytics.php" target="_blank" class="btn-retour" style="text-decoration:none;">
        Inscrivez-vous dès maintenant — c'est facile et gratuit !
    </a>
<?php } ?>
</div>

<hr class="analytics-sep">

<!-- Formulaire ID -->
<div class="analytics-form-card">
    <div class="analytics-form-label">Activation de votre compte</div>
    <div class="analytics-text">
        Une fois votre compte créé, consultez vos emails et suivez les instructions.
        Un numéro de code vous sera retourné.
    </div>
    <form method="post" action="triade-analytics2.php">
        <div class="analytics-form-row">
            <label style="font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#333;">
                ID-ANALYTICS reçu par mail :
            </label>
            <input type="text" size="8" name="idref" value="<?php echo htmlspecialchars($IDGOOGLE); ?>"
                   style="font-size:12px;border:1px solid #c5cae9;border-radius:4px;padding:3px 8px;">
            <input type="submit" value="Valider" class="btn-enr" name="creategoogle">
        </div>
    </form>
</div>

<?php
    PgClose();
} else {
    echo '<div style="margin:14px 8px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;text-align:center;">'
       . '&#9888; ' . ERREUR1 . '<br><br><i>' . ERREUR2 . '</i></div>';
}
?>

</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>

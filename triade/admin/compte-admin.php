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
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css-v4-2.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade — Compte direction</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>

<!-- ═══════════════════════════════════════════════════════
     Section 1 : Création d'un compte
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Création d'un compte membre de la direction</font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<form method="post" name="formulaire">
<div class="na-card" style="margin-top:10px;">

    <div class="na-row">
        <span class="na-lbl">Civilité :</span>
        <span>
            <label><input type="radio" name="saisie_intitule" value="0" class="btradio1" checked="checked"> M.</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="saisie_intitule" value="1" class="btradio1"> Mme</label>
            &nbsp;&nbsp;
            <label><input type="radio" name="saisie_intitule" value="2" class="btradio1"> Mlle</label>
        </span>
    </div>

    <div class="na-row">
        <span class="na-lbl">Nom :</span>
        <input type="text" name="saisie_creat_nom" size="30" maxlength="30" class="cc-select">
    </div>

    <div class="na-row">
        <span class="na-lbl">Prénom :</span>
        <input type="text" name="saisie_creat_prenom" size="30" maxlength="30" class="cc-select">
    </div>

    <div class="na-row">
        <span class="na-lbl">Mot de passe :</span>
        <input type="text" name="saisie_creat_password" size="30" maxlength="20" class="cc-select">
    </div>

</div>
<br>
<div class="na-foot">
    <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit("Enregistrer","create"); //text,nomInput</script></span>
</div>
<br>
</form>

<?php
include_once("../common/config.inc.php");
include_once("../librairie_php/db_triade.php");
$cnx = cnx();

if (isset($_POST["create"])) {
    $cr = create_personnel_via_admin(
        $_POST["saisie_creat_nom"],
        $_POST["saisie_creat_prenom"],
        $_POST["saisie_creat_password"],
        'ADM',
        $_POST["saisie_intitule"]
    );
    if ($cr > 0) {
        if (!file_exists("../data/install_log/access.log")) touch("../data/install_log/access.log");
        history_cmd("admin-triade", "CREATION", "administration");
        $reponse = "<div style='margin:8px;padding:10px 14px;background:#d4edda;border:1px solid #b8dac4;border-radius:6px;color:#155724;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;'>Compte créé avec succès.</div>";
    } elseif ($cr == '-1') {
        alertJs("Ce compte existe déjà \\n\\n L'Equipe Triade.");
    } else {
        include_once("../librairie_php/langue-text-fr.php");
        $affiche = affichageMessageSecurite2();
        alertJs($affiche);
    }
}
?>

</td></tr></table>
<br>

<!-- ═══════════════════════════════════════════════════════
     Section 2 : Liste des membres de la direction
     ═══════════════════════════════════════════════════════ -->
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Liste des membres de la direction</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php if (!empty($reponse)) print $reponse; ?>

<?php
include_once('../librairie_php/langue.php');
$data = affPers('ADM');
if (countTriade($data) > 0):
?>
<table class="cc-data-table" style="margin:8px 0;">
<thead>
<tr class="cc-thead-row">
    <th class="cc-th">Civilité</th>
    <th class="cc-th">Nom</th>
    <th class="cc-th">Prénom</th>
    <th class="cc-th"></th>
</tr>
</thead>
<tbody>
<?php for ($i = 0; $i < countTriade($data); $i++): ?>
<tr class="cc-tr-data">
    <td class="cc-td" style="width:60px;">
        <?php if ($data[$i][5] == 1): ?>
            <img src='../image/commun/img_ssl_mini.png' alt='Inactif' title='Compte inactif'>
        <?php endif; ?>
        <?php echo civ($data[$i][1]); ?>
    </td>
    <td class="cc-td"><b><?php echo strtoupper($data[$i][2]); ?></b></td>
    <td class="cc-td"><?php echo ucfirst($data[$i][3]); ?></td>
    <td class="cc-td cc-td-center">
        <button type="button" class="btn-retour"
                onclick="open('modif_admin.php?saisie_id=<?php echo $data[$i][0]; ?>','_parent','')">
            Visualiser / Modifier
        </button>
    </td>
</tr>
<?php endfor; ?>
</tbody>
</table>
<?php else: ?>
<div style="padding:16px;color:#999;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;">
    Aucun membre de la direction enregistré.
</div>
<?php endif; ?>

</td></tr></table>

<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>

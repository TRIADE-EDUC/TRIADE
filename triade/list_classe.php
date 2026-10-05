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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGGRP24; ?></font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php
include_once('librairie_php/db_triade.php');
$cnx = cnx();
$data = affClasseSansOffline();
// code_class, libelle, desclong, offline, idsite, niveau, connexion_impossible
?>

<div style="margin:10px 8px;">
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th" style="text-align:left;">Classe</th>
            <th class="cc-th" style="text-align:left;">Site</th>
            <th class="cc-th cc-th-center" style="width:80px;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++):
        $idClasse       = $data[$i][0];
        $libelle        = stripslashes($data[$i][1]);
        $descLong       = stripslashes(trim($data[$i][2]));
        $offline        = $data[$i][3];
        $idsite         = $data[$i][4];
        $niveau         = $data[$i][5];
        $noConnexion    = ($data[$i][6] == "1");
        $disabled       = ($idClasse == 0);
        $site           = ($idsite > 0) ? recupSite($idsite) : '';
        if ($descLong != '') $descLong = " ($descLong)";
    ?>
        <tr class="cc-tr-data">
            <td class="cc-td">
                <?php if ($offline == 1): ?>
                    &#128164;
                <?php endif; ?>
                <strong><?php echo htmlspecialchars($libelle); ?></strong>
                <?php if ($niveau != ''): ?>
                    <span style="color:#2e7d32;font-size:11px;margin-left:4px;">(<?php echo htmlspecialchars($niveau); ?>)</span>
                <?php endif; ?>
                <?php if ($descLong != ''): ?>
                    <em style="color:#555;font-size:11px;"><?php echo htmlspecialchars($descLong); ?></em>
                <?php endif; ?>
                <?php if ($noConnexion): ?>
                    <span style="color:#c0392b;font-size:10px;margin-left:6px;font-style:italic;">Connexion étudiants impossible</span>
                <?php endif; ?>
            </td>
            <td class="cc-td" style="font-size:11px;color:#555;"><?php echo htmlspecialchars($site); ?></td>
            <td class="cc-td cc-td-center">
                <?php if (!$disabled): ?>
                    <a href="#" onclick="open('modif_classe.php?id=<?php echo $idClasse; ?>','_parent',''); return false;" class="cc-btn-consult">
                        <?php echo LANGPER30; ?>
                    </a>
                <?php else: ?>
                    <span style="font-size:11px;color:#aaa;"><?php echo LANGPER30; ?></span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>
</div>

<?php Pgclose(); ?>

</td></tr></table>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>
</body></html>

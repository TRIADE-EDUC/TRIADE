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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGGRP25; ?></font></b></td></tr>
<tr id='cadreCentral0'><td valign="top">

<?php
include_once('librairie_php/db_triade.php');
$cnx = cnx();
$data = affToutesLesMatieres();
// code_mat, libelle, sous_matiere, offline, couleur, libelle_long, code_matiere, ?, libelle_sansaccent
?>

<div style="margin:10px 8px;">
<table class="cc-data-table">
    <thead>
        <tr class="cc-thead-row">
            <th class="cc-th" style="text-align:left;">Matière</th>
            <th class="cc-th cc-th-center" style="width:80px;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 0; $i < countTriade($data); $i++):
        if ($data[$i][1] == "") continue;

        $code_matiere       = $data[$i][6];
        $libelle_sansaccent = $data[$i][8];
        $libelleLong        = trim(stripslashes($data[$i][5]));
        $sousMat            = trim($data[$i][2]);
        $offline            = $data[$i][3];
        $idMat              = $data[$i][0];

        if (trim($libelle_sansaccent) == "") updateLibelleSansAccent($idMat);
    ?>
        <tr class="cc-tr-data">
            <td class="cc-td">
                <?php if ($offline == 1): ?>
                    &#128164;
                <?php endif; ?>
                <?php if ($code_matiere != ''): ?>
                    <strong><?php echo htmlspecialchars($code_matiere); ?></strong>&nbsp;:&nbsp;
                <?php endif; ?>
                <?php echo htmlspecialchars(stripslashes($data[$i][1])); ?>
                <?php if ($sousMat != '' && $sousMat != '0'): ?>
                    &nbsp;<?php echo htmlspecialchars(stripslashes($sousMat)); ?>
                    <em style="font-size:10px;color:#666;">(sous-matière)</em>
                <?php endif; ?>
                <?php if ($libelleLong != ''): ?>
                    &nbsp;<em style="font-size:11px;color:#555;">(<?php echo htmlspecialchars($libelleLong); ?>)</em>
                <?php endif; ?>
            </td>
            <td class="cc-td cc-td-center">
                <a href="#" onclick="open('modif_matiere.php?id=<?php echo $idMat; ?>','_parent',''); return false;" class="cc-btn-consult">
                    <?php echo LANGPER30; ?>
                </a>
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

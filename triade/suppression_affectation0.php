<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
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
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg) { alertify.error(msg); };</script>
<title>Triade - Compte de <?php echo $_SESSION['nom'].' '.$_SESSION['prenom']; ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
    print "<script>location.href='./base_de_donne_key.php'</script>";
    exit;
}
include_once('librairie_php/db_triade.php');
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<form method="post" onsubmit="return affectation_classe()" name="formulaire" action="suppression_affectation2.php">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php echo LANGTITRE21; ?></font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="margin:12px 8px;">
    <div style="background:#f5f7ff;border:1px solid #c5cae9;border-radius:8px;padding:14px 18px;">

        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
                <?php echo LANGPER33; ?> :
            </span>
            <?php
            $sql = "SELECT DISTINCT c.code_class, trim(c.libelle)
                    FROM {$prefixe}classes c, {$prefixe}affectations a
                    WHERE c.code_class = a.code_classe
                    ORDER BY 2";
            $curs = execSql($sql);
            $data = chargeMat($curs);
            freeResult($curs);
            unset($curs);
            echo selectHtml('saisie_classe_envoi', 1, false, $data);
            unset($data);
            ?>
        </div>

        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
            <span style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial;">
                <?php echo LANGBULL3; ?> :
            </span>
            <select name="anneeScolaire" class="cc-select">
                <?php filtreAnneeScolaireSelectAnterieur('', 10); ?>
            </select>
        </div>

        <input type="submit" name="rien" value="<?php echo LANGBT22; ?>" class="btn-enr">

    </div>

    <div style="margin-top:14px;padding:10px 14px;background:#fff8e1;border:1px solid #f5c842;border-radius:6px;font-size:12px;font-family:Electrolize,Trebuchet MS,Arial;color:#856404;">
        <?php echo LANGPER34; ?>
    </div>
</div>

</td></tr></table>
</form>

<SCRIPT language="JavaScript" <?php echo "src='./librairie_js/".$_SESSION['membre']."2.js'"; ?>></SCRIPT>
<?php Pgclose(); ?>
</body></html>
